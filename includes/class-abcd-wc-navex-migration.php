<?php
/**
 * Handles the one-time data migration for cleaning up old options.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Abcdo_Wc_Navex_Migration
 */
class Abcdo_Wc_Navex_Migration {

    /**
     * The option key to track if migration is complete.
     */
    private const MIGRATION_FLAG = 'abcdo_wc_navex_migration_complete_v1_1_0';

    /**
     * Initialize hooks.
     */
    public function init() {
        add_action( 'admin_init', array( $this, 'run_migration_check' ) );
    }

    /**
     * Check if the migration needs to be run.
     */
    public function run_migration_check() {
        if ( ! get_option( self::MIGRATION_FLAG ) ) {
            $this->perform_migration();
        }
    }

    /**
     * Perform the migration.
     */
    private function perform_migration() {
        // Delete old options
        delete_option( 'abcdo_wc_navex_api_token_add' );
        delete_option( 'abcdo_wc_navex_api_token_get' );
        delete_option( 'abcdo_wc_navex_api_token_delete' );

        // Set flag to prevent this from running again
        update_option( self::MIGRATION_FLAG, true );

        // Add an admin notice to inform the user
        add_action( 'admin_notices', array( $this, 'show_migration_notice' ) );
    }

    /**
     * Show an admin notice about the successful migration.
     */
    public function show_migration_notice() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong><?php esc_html_e( 'Navex Integration Plugin Update:', 'abcdo-wc-navex' ); ?></strong>
                <?php esc_html_e( 'The API key storage has been updated for better security. Please re-enter your Navex API key in the settings.', 'abcdo-wc-navex' ); ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=abcdo-wc-navex-settings' ) ); ?>"><?php esc_html_e( 'Go to settings', 'abcdo-wc-navex' ); ?></a>
            </p>
        </div>
        <?php
    }
}
