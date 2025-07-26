<?php
/**
 * Manages the WP-Cron job for status synchronization.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Abcdo_Wc_Navex_Cron_Manager
 */
class Abcdo_Wc_Navex_Cron_Manager {

    /**
     * The hook for the cron event.
     * @var string
     */
    const CRON_HOOK = 'abcdo_wc_navex_status_sync_hook';

    /**
     * Initialize hooks.
     */
    public function init() {
        add_action( self::CRON_HOOK, array( $this, 'run_order_sync' ) );
    }

    /**
     * Schedule the cron event.
     */
    public static function schedule_event() {
        if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
            wp_schedule_event( time(), 'hourly', self::CRON_HOOK );
        }
    }

    /**
     * Unschedule the cron event.
     */
    public static function unschedule_event() {
        wp_clear_scheduled_hook( self::CRON_HOOK );
    }

    /**
     * Run the order synchronization process.
     */
    public function run_order_sync() {
        $sync_enabled = get_option( 'abcdo_wc_navex_sync_enabled', 'no' );

        if ( 'yes' !== $sync_enabled ) {
            return;
        }

        Abcdo_Wc_Navex_Logger::log( 'Starting order status synchronization job.' );

        $order_sync = new Abcdo_Wc_Navex_Order_Sync();
        $order_sync->execute();

        Abcdo_Wc_Navex_Logger::log( 'Order status synchronization job finished.' );
    }
}
