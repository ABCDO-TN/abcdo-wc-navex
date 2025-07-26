<?php
/**
 * Manages the admin settings page for the Navex API key.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Abcdo_Wc_Navex_Settings
 */
class Abcdo_Wc_Navex_Settings {

    /**
     * @var Abcdo_Wc_Navex_Token_Manager
     */
    private $token_manager;

    /**
     * The main option group for the settings page.
     */
    private const OPTION_GROUP = 'abcdo_wc_navex_options';

    /**
     * Constructor.
     */
    public function __construct() {
        $this->token_manager = new Abcdo_Wc_Navex_Token_Manager();
    }

    /**
     * Initialize hooks.
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Add the settings submenu page.
     */
    public function add_submenu_page() {
        add_submenu_page(
            'abcdo-wc-navex', // Parent slug
            __( 'Settings', 'abcdo-wc-navex' ),
            __( 'Settings', 'abcdo-wc-navex' ),
            'manage_options',
            'abcdo-wc-navex-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Register settings and fields.
     */
    public function register_settings() {
        register_setting(
            self::OPTION_GROUP,
            'abcdo_wc_navex_api_key',
            array(
                'sanitize_callback' => array( $this, 'sanitize_and_save_token' ),
            )
        );

        add_settings_section(
            'abcdo_wc_navex_api_section',
            __( 'API Credentials', 'abcdo-wc-navex' ),
            null,
            'abcdo-wc-navex-settings'
        );

        add_settings_field(
            'abcdo_wc_navex_api_key_field',
            __( 'Navex API Key', 'abcdo-wc-navex' ),
            array( $this, 'render_api_key_field' ),
            'abcdo-wc-navex-settings',
            'abcdo_wc_navex_api_section'
        );
    }

    /**
     * Render the API key field with secure logic.
     */
    public function render_api_key_field() {
        if ( $this->token_manager->is_token_saved() ) {
            echo '<p>' . esc_html__( 'An API key is currently saved.', 'abcdo-wc-navex' ) . '</p>';
            echo '<p>' . esc_html__( 'To replace it, enter a new key below.', 'abcdo-wc-navex' ) . '</p>';
        } else {
            echo '<p>' . esc_html__( 'Enter your Navex API key below.', 'abcdo-wc-navex' ) . '</p>';
        }
        ?>
        <input type="password" name="abcdo_wc_navex_api_key" value="" class="regular-text" placeholder="<?php esc_attr_e( 'Enter new API key', 'abcdo-wc-navex' ); ?>" />
        <p class="description">
            <?php esc_html_e( 'Your API key is never displayed. It is stored securely.', 'abcdo-wc-navex' ); ?>
        </p>
        <?php
        if ( $this->token_manager->is_token_saved() ) {
            ?>
            <p style="margin-top: 15px;">
                <label for="abcdo_wc_navex_delete_token">
                    <input type="checkbox" id="abcdo_wc_navex_delete_token" name="abcdo_wc_navex_delete_token" value="1" />
                    <?php esc_html_e( 'Delete the saved API key.', 'abcdo-wc-navex' ); ?>
                </label>
            </p>
            <?php
        }
    }

    /**
     * Sanitize and save the token.
     *
     * @param mixed $input The input from the settings field.
     * @return string Empty string because we handle saving manually.
     */
    public function sanitize_and_save_token( $input ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return '';
        }

        // Check if the delete checkbox is checked
        if ( isset( $_POST['abcdo_wc_navex_delete_token'] ) && '1' === $_POST['abcdo_wc_navex_delete_token'] ) {
            $this->token_manager->delete_token();
            add_settings_error( 'abcdo_wc_navex_settings', 'token_deleted', __( 'API key deleted successfully.', 'abcdo-wc-navex' ), 'updated' );
            return '';
        }

        // Sanitize and save the new token if provided
        $new_token = sanitize_text_field( $input );
        if ( ! empty( $new_token ) ) {
            $result = $this->token_manager->save_token( $new_token );
            if ( $result ) {
                add_settings_error( 'abcdo_wc_navex_settings', 'token_saved', __( 'API key saved successfully.', 'abcdo-wc-navex' ), 'updated' );
            } else {
                add_settings_error( 'abcdo_wc_navex_settings', 'token_error', __( 'Failed to save API key.', 'abcdo-wc-navex' ), 'error' );
            }
        }

        // This option does not need to be saved in the options table.
        return '';
    }

    /**
     * Render the main settings page.
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( self::OPTION_GROUP );
                do_settings_sections( 'abcdo-wc-navex-settings' );
                submit_button( __( 'Save Settings', 'abcdo-wc-navex' ) );
                ?>
            </form>
        </div>
        <?php
    }
}
