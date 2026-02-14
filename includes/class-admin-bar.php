<?php
/**
 * Admin Bar Display Class
 * Handles the admin bar menu display
 *
 * @package Debug_Log_Inspector
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Debug_Log_Inspector_Admin_Bar {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_bar_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_admin_bar_styles' ) );
        add_action( 'admin_bar_menu', array( $this, 'add_to_admin_bar' ), 10000 );
    }

    /**
     * Enqueue admin bar styles
     */
    public function enqueue_admin_bar_styles() {
        // Only load if admin bar is showing
        if ( ! is_admin_bar_showing() ) {
            return;
        }

        wp_enqueue_style(
            'debug-log-inspector-admin-bar',
            DEBUG_LOG_INSPECTOR_URL . 'assets/css/admin-bar.css',
            array(),
            DEBUG_LOG_INSPECTOR_VERSION
        );
    }

    /**
     * Add menu to admin bar
     *
     * @param WP_Admin_Bar $wp_admin_bar Admin bar instance
     */
    public function add_to_admin_bar( $wp_admin_bar ) {
        // Get scan results
        $scan_results = Debug_Log_Inspector::scan_debug_log();
        $plugins = Debug_Log_Inspector::get_monitored_plugins();
        $settings = Debug_Log_Inspector::get_settings();

        // Determine main menu color
        if ( ! Debug_Log_Inspector::is_debug_enabled() ) {
            $css_class = 'dli-admin-text-gray';
        } elseif ( $scan_results['error_found'] ) {
            $css_class = 'dli-admin-text-red';
        } else {
            $css_class = 'dli-admin-text-green';
        }

        // Add main menu
        $menu_id = 'lumiblog-debug-log-inspector';
        $wp_admin_bar->add_menu( array(
            'id'    => $menu_id,
            'title' => __( 'LOG INSPECTOR', 'lumiblog-debug-log-inspector' ),
            'href'  => admin_url( 'options-general.php?page=lumiblog-debug-log-inspector' ),
            'meta'  => array( 'class' => $css_class ),
        ) );

        // Add plugin status items
        foreach ( $plugins as $index => $plugin ) {
            // Skip if disabled
            if ( isset( $plugin['enabled'] ) && ! $plugin['enabled'] ) {
                continue;
            }

            // Skip if auto_enable is on and plugin is not active
            if ( $settings['auto_enable'] && ! empty( $plugin['file_path'] ) ) {
                if ( ! Debug_Log_Inspector::is_plugin_active( $plugin['file_path'] ) ) {
                    continue;
                }
            }

            $has_error = isset( $scan_results['plugins'][$index] ) && $scan_results['plugins'][$index];
            $status = $has_error ? 'ERROR!' : 'OK';
            $css_class = $has_error ? 'dli-admin-text-red' : '';

            $wp_admin_bar->add_menu( array(
                'parent' => $menu_id,
                'title'  => esc_html( $plugin['name'] ) . ': ' . $status,
                'id'     => 'dli-plugin-' . $index,
                'href'   => '#',
                'meta'   => array( 'class' => $css_class ),
            ) );
        }

        // Add last error if enabled and error exists
        if ( $settings['show_last_error'] && ! empty( $scan_results['last_error'] ) ) {
            $wp_admin_bar->add_menu( array(
                'parent' => $menu_id,
                'title'  => '<strong>' . __( 'Last Error:', 'lumiblog-debug-log-inspector' ) . '</strong> ' . esc_html( $scan_results['last_error'] ),
                'id'     => 'dli-last-error',
                'href'   => '#',
                'meta'   => array( 'class' => 'dli-admin-text-last-error' ),
            ) );
        }

        // Add settings link
        $wp_admin_bar->add_menu( array(
            'parent' => $menu_id,
            'title'  => __( '⚙️ Settings', 'lumiblog-debug-log-inspector' ),
            'id'     => 'dli-settings-link',
            'href'   => admin_url( 'options-general.php?page=lumiblog-debug-log-inspector' ),
            'meta'   => array( 'class' => 'dli-admin-settings-link' ),
        ) );
    }
}
