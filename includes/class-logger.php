<?php
/**
 * A wrapper class for the WooCommerce logging system.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Abcdo_Wc_Navex_Logger
 */
class Abcdo_Wc_Navex_Logger {

    /**
     * The logger instance.
     *
     * @var WC_Logger
     */
    private static $logger;

    /**
     * Get the logger instance.
     *
     * @return WC_Logger
     */
    private static function get_logger() {
        if ( ! isset( self::$logger ) ) {
            self::$logger = wc_get_logger();
        }
        return self::$logger;
    }

    /**
     * Log a message.
     *
     * @param string $message The message to log.
     * @param string $level The log level (e.g., 'info', 'notice', 'warning', 'error').
     */
    public static function log( $message, $level = 'info' ) {
        $context = array( 'source' => 'abcdo-wc-navex-sync' );
        self::get_logger()->log( $level, $message, $context );
    }
}
