<?php
/**
 * Contains the logic for the order synchronization process.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Abcdo_Wc_Navex_Order_Sync
 */
class Abcdo_Wc_Navex_Order_Sync {

    /**
     * @var Abcdo_Wc_Navex_Api_Client
     */
    private $api_client;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->api_client = new Abcdo_Wc_Navex_Api_Client();
    }

    /**
     * Execute the main sync process.
     */
    public function execute() {
        if ( ! $this->api_client->has_token( 'get' ) ) {
            Abcdo_Wc_Navex_Logger::log( 'Order sync failed: Get Token is not set.', 'warning' );
            return;
        }

        $orders = $this->get_eligible_orders();

        if ( empty( $orders ) ) {
            Abcdo_Wc_Navex_Logger::log( 'No eligible orders found for synchronization.' );
            return;
        }

        Abcdo_Wc_Navex_Logger::log( 'Found ' . count( $orders ) . ' orders to process.' );

        $this->process_orders( $orders );
    }

    /**
     * Get orders that need to be synced.
     *
     * @return WC_Order[]
     */
    private function get_eligible_orders() {
        $args = array(
            'status'     => array( 'wc-processing', 'wc-on-hold' ), // Example statuses, can be customized
            'limit'      => -1,
            'meta_query' => array(
                array(
                    'key'     => '_navex_tracking_id',
                    'compare' => 'EXISTS',
                ),
            ),
        );
        return wc_get_orders( $args );
    }

    /**
     * Process a batch of orders.
     *
     * @param WC_Order[] $orders
     */
    private function process_orders( $orders ) {
        foreach ( $orders as $order ) {
            $tracking_id = $order->get_meta( '_navex_tracking_id' );
            if ( empty( $tracking_id ) ) {
                continue;
            }

            $response = $this->api_client->get_shipment_status( $tracking_id );

            if ( is_wp_error( $response ) ) {
                $error_message = sprintf(
                    'API Error for Order #%d: %s',
                    $order->get_id(),
                    $response->get_error_message()
                );
                Abcdo_Wc_Navex_Logger::log( $error_message, 'error' );
                continue;
            }

            if ( isset( $response['status'] ) ) {
                $this->update_order_status( $order, $response['status'] );
            }
        }
    }

    /**
     * Update the order status based on the Navex status.
     *
     * @param WC_Order $order
     * @param string   $navex_status
     */
    private function update_order_status( $order, $navex_status ) {
        $status_map = array(
            // This map needs to be defined based on actual Navex API statuses
            'Livré'         => 'completed',
            'Retour'        => 'refunded',
            'Annulé'        => 'cancelled',
            'En cours'      => 'processing',
        );

        $wc_status = isset( $status_map[ $navex_status ] ) ? $status_map[ $navex_status ] : null;

        if ( $wc_status && 'wc-' . $wc_status !== $order->get_status() ) {
            $order->update_status( $wc_status, sprintf( __( 'Order status automatically updated by Navex Sync. New status: %s', 'abcdo-wc-navex' ), $navex_status ) );
            Abcdo_Wc_Navex_Logger::log( sprintf( 'Order #%d status updated to %s.', $order->get_id(), $wc_status ) );
        }
    }
}
