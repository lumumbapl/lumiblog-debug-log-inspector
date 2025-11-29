<?php
/**
 * Core Log Inspector Class
 * Handles log file reading and error detection
 *
 * @package Debug_Log_Inspector
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Debug_Log_Inspector {

    /**
     * Get monitored plugins from options
     *
     * @return array List of plugins to monitor
     */
    public static function get_monitored_plugins() {
        $plugins = get_option( 'debug_log_inspector_plugins', array() );
        return is_array( $plugins ) ? $plugins : array();
    }

    /**
     * Get plugin settings
     *
     * @return array Plugin settings
     */
    public static function get_settings() {
        $defaults = array(
            'log_max_bytes' => 307200, // 300KB
            'auto_enable' => true,
            'show_last_error' => true,
        );
        $settings = get_option( 'debug_log_inspector_settings', $defaults );
        return wp_parse_args( $settings, $defaults );
    }

    /**
     * Check if a plugin is active
     *
     * @param string $file_path Plugin file path
     * @return bool True if active
     */
    public static function is_plugin_active( $file_path ) {
        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return is_plugin_active( $file_path );
    }

    /**
     * Search for plugin text in a log line
     *
     * @param string $line Log line to search
     * @param mixed $search_terms String or array of search terms
     * @return bool True if found
     */
    public static function search_in_line( $line, $search_terms ) {
        if ( empty( $search_terms ) ) {
            return false;
        }

        // Convert comma-separated string to array
        if ( is_string( $search_terms ) ) {
            $search_terms = array_map( 'trim', explode( ',', $search_terms ) );
        }

        if ( is_array( $search_terms ) ) {
            foreach ( $search_terms as $term ) {
                if ( stripos( $line, trim( $term ) ) !== false ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Scan debug log for errors
     *
     * @return array Array with error status for each plugin
     */
    public static function scan_debug_log() {
        $settings = self::get_settings();
        $plugins = self::get_monitored_plugins();
        $results = array();

        // Get debug log path
        $debug_log_path = self::get_debug_log_path();
        
        if ( ! $debug_log_path || ! file_exists( $debug_log_path ) ) {
            return array(
                'error' => true,
                'message' => 'Debug log file not found',
                'plugins' => array(),
            );
        }

        // Initialize results
        $error_found = false;
        $last_error = '';
        $plugin_errors = array();

        foreach ( $plugins as $index => $plugin ) {
            $plugin_errors[$index] = false;
        }

        // Read log file
        $debug_log = fopen( $debug_log_path, 'r' );
        if ( ! $debug_log ) {
            return array(
                'error' => true,
                'message' => 'Cannot open debug log file',
                'plugins' => array(),
            );
        }

        fseek( $debug_log, -$settings['log_max_bytes'], SEEK_END );

        while ( ( $log_line = fgets( $debug_log, 4096 ) ) !== false ) {
            foreach ( $plugins as $index => $plugin ) {
                // Skip if plugin is disabled
                if ( isset( $plugin['enabled'] ) && ! $plugin['enabled'] ) {
                    continue;
                }

                // Skip if auto_enable is on and plugin is not active
                if ( $settings['auto_enable'] && ! empty( $plugin['file_path'] ) ) {
                    if ( ! self::is_plugin_active( $plugin['file_path'] ) ) {
                        continue;
                    }
                }

                // Search for plugin errors
                if ( self::search_in_line( $log_line, $plugin['search_terms'] ) ) {
                    $error_found = true;
                    $plugin_errors[$index] = true;
                    $last_error = $log_line;
                }
            }
        }

        fclose( $debug_log );

        return array(
            'error' => false,
            'message' => '',
            'error_found' => $error_found,
            'last_error' => $last_error,
            'plugins' => $plugin_errors,
        );
    }

    /**
     * Get debug log file path
     *
     * @return string|bool Path to debug log or false
     */
    public static function get_debug_log_path() {
        if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
            if ( is_string( WP_DEBUG_LOG ) ) {
                return WP_DEBUG_LOG;
            }
            return ABSPATH . 'wp-content/debug.log';
        }
        return false;
    }

    /**
     * Check if debug logging is enabled
     *
     * @return bool True if enabled
     */
    public static function is_debug_enabled() {
        return defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG;
    }
}
