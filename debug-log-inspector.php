<?php
/**
 * Plugin Name:       Lumiblog Debug Log Inspector
 * Plugin URI:        https://wordpress.org/plugins/lumiblog-debug-log-inspector/
 * Description:       Monitor debug logs for any WordPress plugin errors and display real-time status in the admin bar
 * Version:           1.1.0
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Author:            Patrick Lumumba
 * Author URI:        https://lumumbas-blog.co.ke
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lumiblog-debug-log-inspector
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define plugin constants
define( 'DEBUG_LOG_INSPECTOR_VERSION', '1.1.0' );
define( 'DEBUG_LOG_INSPECTOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'DEBUG_LOG_INSPECTOR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin classes
 */
require_once DEBUG_LOG_INSPECTOR_PATH . 'includes/class-log-inspector.php';
require_once DEBUG_LOG_INSPECTOR_PATH . 'includes/class-settings.php';
require_once DEBUG_LOG_INSPECTOR_PATH . 'includes/class-admin-bar.php';

/**
 * Initialize the plugin
 */
function debug_log_inspector_init() {
    // Initialize settings page
    new Debug_Log_Inspector_Settings();
    
    // Initialize admin bar display
    new Debug_Log_Inspector_Admin_Bar();
}
add_action( 'plugins_loaded', 'debug_log_inspector_init' );

/**
 * Activation hook - set default options
 */
function debug_log_inspector_activate() {
    // Set default options on activation if they don't exist
    if ( ! get_option( 'debug_log_inspector_plugins' ) ) {
        // Start with empty plugin list - users add their own
        $default_plugins = array();
        update_option( 'debug_log_inspector_plugins', $default_plugins );
    }
    
    // Set default settings
    if ( ! get_option( 'debug_log_inspector_settings' ) ) {
        $default_settings = array(
            'log_max_bytes' => 307200, // 300KB
            'auto_enable' => true,
            'show_last_error' => true,
        );
        update_option( 'debug_log_inspector_settings', $default_settings );
    }
}
register_activation_hook( __FILE__, 'debug_log_inspector_activate' );

/**
 * Deactivation hook
 */
function debug_log_inspector_deactivate() {
    // Clean up if needed
}
register_deactivation_hook( __FILE__, 'debug_log_inspector_deactivate' );
