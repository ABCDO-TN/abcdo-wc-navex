<?php
/**
 * Manages the admin settings page for Navex Integration.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Abcdo_Wc_Navex_Admin_Settings
 */
class Abcdo_Wc_Navex_Admin_Settings {

    /**
     * Option group.
     * @var string
     */
    private const OPTION_GROUP = 'abcdo_wc_navex_options';

    /**
     * Option names for the tokens.
     * @var array
     */
    private const TOKEN_OPTIONS = [
        'add'    => 'abcdo_wc_navex_add_token',
        'get'    => 'abcdo_wc_navex_get_token',
        'delete' => 'abcdo_wc_navex_delete_token',
    ];

    /**
     * Initialize hooks.
     */
    public function init() {
        add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );
        add_action( 'woocommerce_settings_tabs_navex_integration', array( $this, 'settings_tab_content' ) );
        add_action( 'woocommerce_update_options_navex_integration', array( $this, 'update_settings' ) );
        add_action( 'wp_ajax_abcdo_wc_navex_delete_token', array( $this, 'handle_ajax_delete_token' ) );

        // Add custom field type for tokens
        add_action( 'woocommerce_admin_field_navex_token', array( $this, 'render_token_field' ) );
    }

    /**
     * Add a new settings tab to the WooCommerce settings pages.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs.
     * @return array
     */
    public function add_settings_tab( $settings_tabs ) {
        $settings_tabs['navex_integration'] = __( 'Navex Integration', 'abcdo-wc-navex' );
        return $settings_tabs;
    }

    /**
     * Get settings array.
     *
     * @return array
     */
    public function get_settings() {
        $settings = array(
            'section_title'           => array(
                'name' => __( 'Navex API Credentials', 'abcdo-wc-navex' ),
                'type' => 'title',
                'desc' => __( 'Enter your API tokens from your Navex dashboard.', 'abcdo-wc-navex' ),
                'id'   => 'wc_navex_api_credentials_section_title',
            ),
            'add_token'               => array(
                'name' => __( 'Add Token', 'abcdo-wc-navex' ),
                'type' => 'navex_token',
                'desc' => __( 'Token for creating shipments.', 'abcdo-wc-navex' ),
                'id'   => self::TOKEN_OPTIONS['add'],
                'token_key' => 'add',
            ),
            'get_token'               => array(
                'name' => __( 'Get Token', 'abcdo-wc-navex' ),
                'type' => 'navex_token',
                'desc' => __( 'Token for retrieving shipment status.', 'abcdo-wc-navex' ),
                'id'   => self::TOKEN_OPTIONS['get'],
                'token_key' => 'get',
            ),
            'delete_token'            => array(
                'name' => __( 'Delete Token', 'abcdo-wc-navex' ),
                'type' => 'navex_token',
                'desc' => __( 'Token for deleting shipments.', 'abcdo-wc-navex' ),
                'id'   => self::TOKEN_OPTIONS['delete'],
                'token_key' => 'delete',
            ),
            'api_section_end'         => array(
                'type' => 'sectionend',
                'id'   => 'wc_navex_api_credentials_section_end',
            ),
            'sync_section_title'      => array(
                'name' => __( 'Order Synchronization', 'abcdo-wc-navex' ),
                'type' => 'title',
                'id'   => 'wc_navex_sync_section_title',
            ),
            'sync_enabled'            => array(
                'name' => __( 'Enable Auto-Sync', 'abcdo-wc-navex' ),
                'type' => 'checkbox',
                'desc' => __( 'Enable hourly synchronization of order statuses from Navex.', 'abcdo-wc-navex' ),
                'id'   => 'abcdo_wc_navex_sync_enabled',
            ),
            'sync_section_end'        => array(
                'type' => 'sectionend',
                'id'   => 'wc_navex_sync_section_end',
            ),
        );

        return apply_filters( 'wc_navex_integration_settings', $settings );
    }

    /**
     * Render the settings tab content.
     */
    public function settings_tab_content() {
        woocommerce_admin_fields( $this->get_settings() );
    }

    /**
     * Save settings.
     */
    public function update_settings() {
        woocommerce_update_options( $this->get_settings() );

        foreach ( self::TOKEN_OPTIONS as $key => $option_name ) {
            if ( ! empty( $_POST[ $option_name ] ) ) {
                $raw_token = sanitize_text_field( $_POST[ $option_name ] );
                $encrypted_token = Abcdo_Wc_Navex_Encryption_Service::encrypt( $raw_token );
                update_option( $option_name, $encrypted_token );
            }
        }
    }

    /**
     * Handle AJAX request to delete a token.
     */
    public function handle_ajax_delete_token() {
        check_ajax_referer( 'abcdo_wc_navex_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'abcdo-wc-navex' ) ) );
        }

        $token_key = isset( $_POST['token_key'] ) ? sanitize_key( $_POST['token_key'] ) : '';

        if ( array_key_exists( $token_key, self::TOKEN_OPTIONS ) ) {
            delete_option( self::TOKEN_OPTIONS[ $token_key ] );
            wp_send_json_success( array( 'message' => __( 'Token deleted.', 'abcdo-wc-navex' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Invalid token key.', 'abcdo-wc-navex' ) ) );
        }
    }

    /**
     * Render the custom navex_token field.
     *
     * @param array $value
     */
    public function render_token_field( $value ) {
        $option_value = get_option( $value['id'] );
        $is_saved = ! empty( $option_value );
        $description = $value['desc'];
        $tooltip_html = wc_help_tip( $description );
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                <?php echo $tooltip_html; ?>
            </th>
            <td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
                <input
                    name="<?php echo esc_attr( $value['id'] ); ?>"
                    id="<?php echo esc_attr( $value['id'] ); ?>"
                    type="password"
                    style="<?php echo esc_attr( $value['css'] ); ?>"
                    class="<?php echo esc_attr( $value['class'] ); ?>"
                    placeholder="<?php esc_attr_e( 'Enter new token to update', 'abcdo-wc-navex' ); ?>"
                    />
                <span id="<?php echo esc_attr( $value['id'] ); ?>-status" class="navex-token-status">
                    <?php if ( $is_saved ) : ?>
                        <span style="color: green;"><?php esc_html_e( 'Status: Saved', 'abcdo-wc-navex' ); ?></span>
                        <button type="button" class="button button-secondary navex-delete-token-btn" data-token-key="<?php echo esc_attr( $value['token_key'] ); ?>">
                            <?php esc_html_e( 'Delete', 'abcdo-wc-navex' ); ?>
                        </button>
                    <?php else : ?>
                        <span style="color: red;"><?php esc_html_e( 'Status: Not Set', 'abcdo-wc-navex' ); ?></span>
                    <?php endif; ?>
                </span>
            </td>
        </tr>
        <?php
    }
}
