<?php
/**
 * GitHub update manager file.
 *
 * @package Abcdo_Wc_Navex
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class to handle plugin updates from GitHub.
 */
class ABCD_WC_Navex_Updater {

    private $file;
    private $plugin;
    private $basename;
    private $active;
    private $github_repo;
    private $github_response;

    /**
     * Constructor.
     */
    public function __construct( $file ) {
        $this->file = $file;
        $this->github_repo = 'ABCDO-TN/abcdo-wc-navex'; // Format: user/repo

        // Load properties immediately to avoid race conditions.
        $this->set_plugin_properties();
    }

    public function set_plugin_properties() {
        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        $this->plugin   = get_plugin_data( $this->file );
        $this->basename = plugin_basename( $this->file );
        $this->active   = is_plugin_active( $this->basename );
    }

    public function init() {
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'modify_transient' ), 10, 1 );
        add_filter( 'plugins_api', array( $this, 'plugin_popup' ), 10, 3 );
        add_filter( 'upgrader_source_selection', array( $this, 'upgrader_source_selection' ), 10, 4 );
    }

    private function get_repository_info() {
        if ( is_null( $this->github_response ) ) {
            $request_uri = sprintf( 'https://api.github.com/repos/%s/releases/latest', $this->github_repo );
            $response = wp_remote_get( $request_uri );

            if ( is_wp_error( $response ) ) {
                return false;
            }

            $body = wp_remote_retrieve_body( $response );
            if ( ! empty( $body ) ) {
                $this->github_response = json_decode( $body );
            }
        }
        return $this->github_response;
    }

    public function modify_transient( $transient ) {
        if ( ! is_object( $transient ) || ! isset( $transient->checked ) ) {
            return $transient;
        }

        // Ensure plugin data is loaded.
        if ( empty( $this->plugin ) || empty( $this->plugin['Version'] ) ) {
            return $transient;
        }

        $this->get_repository_info();

        if ( $this->github_response && ! empty( $this->github_response->tag_name ) && version_compare( $this->plugin['Version'], $this->github_response->tag_name, '<' ) ) {
            
            if ( ! empty( $this->github_response->assets ) && ! empty( $this->github_response->assets[0]->browser_download_url ) ) {
                $obj = new stdClass();
                $obj->slug = $this->basename;
                $obj->new_version = $this->github_response->tag_name;
                $obj->url = $this->plugin['PluginURI'];
                $obj->package = $this->github_response->assets[0]->browser_download_url;
                
                $transient->response[ $this->basename ] = $obj;
            }
        }
        
        return $transient;
    }

    public function plugin_popup( $result, $action, $args ) {
        if ( ! empty( $args->slug ) && $args->slug == $this->basename ) {
            $this->get_repository_info();

            if ( $this->github_response ) {
                $obj = new stdClass();
                $obj->name = $this->plugin['Name'];
                $obj->slug = $this->basename;
                $obj->version = $this->github_response->tag_name;
                $obj->author = $this->plugin['Author'];
                $obj->homepage = $this->plugin['PluginURI'];
                $obj->sections = array(
                    'description' => $this->plugin['Description'],
                    'changelog' => ! empty( $this->github_response->body ) ? $this->github_response->body : '',
                );

                if ( ! empty( $this->github_response->assets ) && ! empty( $this->github_response->assets[0]->browser_download_url ) ) {
                    $obj->download_link = $this->github_response->assets[0]->browser_download_url;
                }
                
                return $obj;
            }
        }
        return $result;
    }

    public function upgrader_source_selection( $source, $remote_source, $upgrader, $hook_extra = null ) {
        if ( isset( $hook_extra['plugin'] ) && $hook_extra['plugin'] === $this->basename ) {
            // More robust folder renaming logic
            global $wp_filesystem;
            $new_source = trailingslashit( $remote_source ) . $wp_filesystem->find_folder( $remote_source );
            
            if ( is_dir( $new_source ) ) {
                return $new_source;
            }
        }
        return $source;
    }
}
