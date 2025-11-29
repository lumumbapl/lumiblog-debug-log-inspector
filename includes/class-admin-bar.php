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
        add_action( 'admin_head', array( $this, 'add_css_style' ), 1 );
        add_action( 'admin_bar_menu', array( $this, 'add_to_admin_bar' ), 10000 );
    }

    /**
     * Add CSS styles for admin bar
     */
    public function add_css_style() {
        ?>
        <style id="debug-log-inspector-css">
            .dli-admin-text-red > a {
                color: #ff3939 !important;
            }
            .dli-admin-text-green > a {
                color: #00f70b !important;
            }
            .dli-admin-text-gray > a {
                color: #808080 !important;
            }
            .dli-admin-text-last-error {
                width: 500px !important;
                border-top: 1px solid #808080;
            }
            .dli-admin-text-last-error > a {
                height: auto !important;
                width: 500px !important;
                white-space: normal !important;
                word-wrap: break-word !important;
            }
            .dli-admin-settings-link {
                border-top: 1px solid #808080;
                margin-top: 5px !important;
                padding-top: 5px !important;
            }
        </style>
        <?php
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
        $menu_id = 'debug-log-inspector';
        $wp_admin_bar->add_menu( array(
            'id'    => $menu_id,
            'title' => __( 'LOG INSPECTOR', 'debug-log-inspector' ),
            'href'  => admin_url( 'options-general.php?page=debug-log-inspector' ),
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
                'title'  => '<strong>' . __( 'Last Error:', 'debug-log-inspector' ) . '</strong> ' . esc_html( $scan_results['last_error'] ),
                'id'     => 'dli-last-error',
                'href'   => '#',
                'meta'   => array( 'class' => 'dli-admin-text-last-error' ),
            ) );
        }

        // Add settings link
        $wp_admin_bar->add_menu( array(
            'parent' => $menu_id,
            'title'  => __( '⚙️ Settings', 'debug-log-inspector' ),
            'id'     => 'dli-settings-link',
            'href'   => admin_url( 'options-general.php?page=debug-log-inspector' ),
            'meta'   => array( 'class' => 'dli-admin-settings-link' ),
        ) );
    }
}
