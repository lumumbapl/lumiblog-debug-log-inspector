<?php
/**
 * Settings Page Class
 * Handles admin settings page and form processing
 *
 * @package Debug_Log_Inspector
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Debug_Log_Inspector_Settings {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'handle_form_submission' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'current_screen', array( $this, 'register_footer_hooks' ) );
    }

    /**
     * Register admin footer hooks only on the plugin's settings page
     */
    public function register_footer_hooks() {
        $screen = get_current_screen();
        if ( ! $screen || 'settings_page_lumiblog-debug-log-inspector' !== $screen->id ) {
            return;
        }
        add_filter( 'admin_footer_text', array( $this, 'custom_footer_text' ) );
        add_filter( 'update_footer', array( $this, 'custom_footer_version' ), 11 );
    }

    /**
     * Custom footer left text — rating prompt
     *
     * @return string
     */
    public function custom_footer_text() {
        return sprintf(
            /* translators: %s: WordPress.org review URL */
            __( 'If you like <strong>Lumiblog Debug Log Inspector</strong>, please leave us a <a href="%s" target="_blank" rel="noopener noreferrer">★★★★★</a> rating. Thank you so much in advance!', 'lumiblog-debug-log-inspector' ),
            'https://wordpress.org/support/plugin/lumiblog-debug-log-inspector/reviews/#new-post'
        );
    }

    /**
     * Custom footer right text — plugin version
     *
     * @return string
     */
    public function custom_footer_version() {
        return sprintf(
            /* translators: %s: plugin version number */
            __( 'Version %s', 'lumiblog-debug-log-inspector' ),
            DEBUG_LOG_INSPECTOR_VERSION
        );
    }

    /**
     * Add settings page to WordPress admin
     */
    public function add_settings_page() {
        add_options_page(
            __( 'Debug Log Inspector', 'lumiblog-debug-log-inspector' ),
            __( 'Log Inspector', 'lumiblog-debug-log-inspector' ),
            'manage_options',
            'lumiblog-debug-log-inspector',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Enqueue admin CSS and JS
     *
     * @param string $hook Current admin page hook
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load on our settings page
        if ( 'settings_page_debug-log-inspector' !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'debug-log-inspector-admin',
            DEBUG_LOG_INSPECTOR_URL . 'assets/css/admin-style.css',
            array(),
            DEBUG_LOG_INSPECTOR_VERSION
        );

        wp_enqueue_script(
            'debug-log-inspector-admin',
            DEBUG_LOG_INSPECTOR_URL . 'assets/js/admin-script.js',
            array( 'jquery' ),
            DEBUG_LOG_INSPECTOR_VERSION,
            true
        );
    }

    /**
     * Handle form submissions
     */
    public function handle_form_submission() {
        // Check if form was submitted
        if ( ! isset( $_POST['debug_log_inspector_action'] ) ) {
            return;
        }

        // Verify nonce
        if ( ! isset( $_POST['debug_log_inspector_nonce'] ) || 
             ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['debug_log_inspector_nonce'] ) ), 'debug_log_inspector_action' ) ) {
            return;
        }

        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $action = isset( $_POST['debug_log_inspector_action'] ) ? sanitize_text_field( wp_unslash( $_POST['debug_log_inspector_action'] ) ) : '';

        switch ( $action ) {
            case 'add_plugin':
                $this->add_plugin();
                break;
            case 'edit_plugin':
                $this->edit_plugin();
                break;
            case 'delete_plugin':
                $this->delete_plugin();
                break;
            case 'toggle_plugin':
                $this->toggle_plugin();
                break;
            case 'update_settings':
                $this->update_settings();
                break;
        }
    }

    /**
     * Add a new plugin to monitor
     */
    private function add_plugin() {
        // Nonce is already verified in handle_form_submission()
        $plugins = Debug_Log_Inspector::get_monitored_plugins();

        // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in handle_form_submission()
        $new_plugin = array(
            'name' => isset( $_POST['plugin_name'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_name'] ) ) : '',
            'file_path' => isset( $_POST['plugin_file_path'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_file_path'] ) ) : '',
            'search_terms' => isset( $_POST['plugin_search_terms'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_search_terms'] ) ) : '',
            'enabled' => true,
        );
        // phpcs:enable WordPress.Security.NonceVerification.Missing

        // Check for duplicates using file_path
        if ( ! empty( $new_plugin['file_path'] ) ) {
            foreach ( $plugins as $plugin ) {
                if ( ! empty( $plugin['file_path'] ) && $plugin['file_path'] === $new_plugin['file_path'] ) {
                    $this->add_admin_notice( 'error', __( 'This plugin is already being monitored!', 'lumiblog-debug-log-inspector' ) );
                    return;
                }
            }
        }

        // Check for duplicate names as a fallback
        foreach ( $plugins as $plugin ) {
            if ( strtolower( $plugin['name'] ) === strtolower( $new_plugin['name'] ) ) {
                $this->add_admin_notice( 'error', __( 'A plugin with this name already exists!', 'lumiblog-debug-log-inspector' ) );
                return;
            }
        }

        $plugins[] = $new_plugin;
        update_option( 'debug_log_inspector_plugins', $plugins );

        $this->add_admin_notice( 'success', __( 'Plugin added successfully!', 'lumiblog-debug-log-inspector' ) );
    }

    /**
     * Edit an existing plugin
     */
    private function edit_plugin() {
        // Nonce is already verified in handle_form_submission()
        $plugins = Debug_Log_Inspector::get_monitored_plugins();
        
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in handle_form_submission()
        $index = isset( $_POST['plugin_index'] ) ? intval( $_POST['plugin_index'] ) : -1;

        if ( isset( $plugins[$index] ) ) {
            $updated_plugin = array(
                'name' => isset( $_POST['plugin_name'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_name'] ) ) : '',
                'file_path' => isset( $_POST['plugin_file_path'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_file_path'] ) ) : '',
                'search_terms' => isset( $_POST['plugin_search_terms'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin_search_terms'] ) ) : '',
            );
        // phpcs:enable WordPress.Security.NonceVerification.Missing

            // Check for duplicates (excluding current plugin)
            if ( ! empty( $updated_plugin['file_path'] ) ) {
                foreach ( $plugins as $i => $plugin ) {
                    if ( $i !== $index && ! empty( $plugin['file_path'] ) && $plugin['file_path'] === $updated_plugin['file_path'] ) {
                        $this->add_admin_notice( 'error', __( 'Another plugin with this file path already exists!', 'lumiblog-debug-log-inspector' ) );
                        return;
                    }
                }
            }

            // Check for duplicate names (excluding current plugin)
            foreach ( $plugins as $i => $plugin ) {
                if ( $i !== $index && strtolower( $plugin['name'] ) === strtolower( $updated_plugin['name'] ) ) {
                    $this->add_admin_notice( 'error', __( 'Another plugin with this name already exists!', 'lumiblog-debug-log-inspector' ) );
                    return;
                }
            }

            $plugins[$index]['name'] = $updated_plugin['name'];
            $plugins[$index]['file_path'] = $updated_plugin['file_path'];
            $plugins[$index]['search_terms'] = $updated_plugin['search_terms'];

            update_option( 'debug_log_inspector_plugins', $plugins );
            $this->add_admin_notice( 'success', __( 'Plugin updated successfully!', 'lumiblog-debug-log-inspector' ) );
            wp_safe_redirect( admin_url( 'options-general.php?page=lumiblog-debug-log-inspector' ) );
            exit;
        }
    }

    /**
     * Delete a plugin from monitoring
     */
    private function delete_plugin() {
        // Nonce is already verified in handle_form_submission()
        $plugins = Debug_Log_Inspector::get_monitored_plugins();
        
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in handle_form_submission()
        $index = isset( $_POST['plugin_index'] ) ? intval( $_POST['plugin_index'] ) : -1;

        if ( isset( $plugins[$index] ) ) {
            unset( $plugins[$index] );
            $plugins = array_values( $plugins ); // Re-index array
            update_option( 'debug_log_inspector_plugins', $plugins );
            $this->add_admin_notice( 'success', __( 'Plugin deleted successfully!', 'lumiblog-debug-log-inspector' ) );
        }
    }

    /**
     * Toggle plugin enabled/disabled status
     */
    private function toggle_plugin() {
        // Nonce is already verified in handle_form_submission()
        $plugins = Debug_Log_Inspector::get_monitored_plugins();
        
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in handle_form_submission()
        $index = isset( $_POST['plugin_index'] ) ? intval( $_POST['plugin_index'] ) : -1;

        if ( isset( $plugins[$index] ) ) {
            $plugins[$index]['enabled'] = ! ( isset( $plugins[$index]['enabled'] ) && $plugins[$index]['enabled'] );
            update_option( 'debug_log_inspector_plugins', $plugins );
            
            $status = $plugins[$index]['enabled'] ? __( 'enabled', 'lumiblog-debug-log-inspector' ) : __( 'disabled', 'lumiblog-debug-log-inspector' );
            /* translators: %s: the status (enabled or disabled) */
            $this->add_admin_notice( 'success', sprintf( __( 'Plugin %s!', 'lumiblog-debug-log-inspector' ), $status ) );
        }
    }

    /**
     * Update general settings
     */
    private function update_settings() {
        // Nonce is already verified in handle_form_submission()
        
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in handle_form_submission()
        $settings = array(
            'log_max_bytes' => isset( $_POST['log_max_bytes'] ) ? intval( $_POST['log_max_bytes'] ) : 307200,
            'auto_enable' => isset( $_POST['auto_enable'] ),
            'show_last_error' => isset( $_POST['show_last_error'] ),
        );
        // phpcs:enable WordPress.Security.NonceVerification.Missing

        update_option( 'debug_log_inspector_settings', $settings );
        $this->add_admin_notice( 'success', __( 'Settings updated successfully!', 'lumiblog-debug-log-inspector' ) );
    }

    /**
     * Add admin notice
     *
     * @param string $type Notice type (success, error, warning, info)
     * @param string $message Notice message
     */
    private function add_admin_notice( $type, $message ) {
        set_transient( 'debug_log_inspector_notice', array(
            'type' => $type,
            'message' => $message,
        ), 30 );
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'lumiblog-debug-log-inspector' ) );
        }

        // Get data
        $plugins = Debug_Log_Inspector::get_monitored_plugins();
        $settings = Debug_Log_Inspector::get_settings();
        $debug_enabled = Debug_Log_Inspector::is_debug_enabled();

        // Get edit mode if set
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only operation for display purposes
        $edit_mode = isset( $_GET['edit'] ) ? intval( $_GET['edit'] ) : -1;
        $edit_plugin = ( $edit_mode >= 0 && isset( $plugins[$edit_mode] ) ) ? $plugins[$edit_mode] : null;

        // Display admin notice if exists
        $notice = get_transient( 'debug_log_inspector_notice' );
        if ( $notice ) {
            delete_transient( 'debug_log_inspector_notice' );
            printf(
                '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
                esc_attr( $notice['type'] ),
                esc_html( $notice['message'] )
            );
        }

        // Include template
        include DEBUG_LOG_INSPECTOR_PATH . 'templates/settings-page.php';
    }
}
