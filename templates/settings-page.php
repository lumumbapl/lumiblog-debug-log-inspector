<?php
/**
 * Settings Page Template
 *
 * @package Debug_Log_Inspector
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>

<div class="wrap dli-wrap">
    <h1><?php esc_html_e( 'Debug Log Inspector Settings', 'lumiblog-debug-log-inspector' ); ?></h1>

    <?php if ( ! $debug_enabled ) : ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php esc_html_e( 'Warning:', 'lumiblog-debug-log-inspector' ); ?></strong>
                <?php esc_html_e( 'WordPress debug logging is not enabled. Please add the following to your wp-config.php file:', 'lumiblog-debug-log-inspector' ); ?>
            </p>
            <pre>define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );</pre>
        </div>
    <?php endif; ?>

    <div class="dli-settings-container">
        <!-- Add/Edit Plugin Form -->
        <div class="dli-card">
            <h2><?php echo $edit_plugin ? esc_html__( 'Edit Plugin', 'lumiblog-debug-log-inspector' ) : esc_html__( 'Add New Plugin to Monitor', 'lumiblog-debug-log-inspector' ); ?></h2>
            
            <form method="post" action="">
                <?php wp_nonce_field( 'debug_log_inspector_action', 'debug_log_inspector_nonce' ); ?>
                <input type="hidden" name="debug_log_inspector_action" value="<?php echo $edit_plugin ? 'edit_plugin' : 'add_plugin'; ?>">
                <?php if ( $edit_plugin ) : ?>
                    <input type="hidden" name="plugin_index" value="<?php echo esc_attr( $edit_mode ); ?>">
                <?php endif; ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="plugin_name"><?php esc_html_e( 'Plugin Name', 'lumiblog-debug-log-inspector' ); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="text" name="plugin_name" id="plugin_name" class="regular-text" 
                                   value="<?php echo $edit_plugin ? esc_attr( $edit_plugin['name'] ) : ''; ?>" required>
                            <p class="description"><?php esc_html_e( 'Display name for the plugin (e.g., WooCommerce)', 'lumiblog-debug-log-inspector' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="plugin_file_path"><?php esc_html_e( 'Plugin File Path', 'lumiblog-debug-log-inspector' ); ?></label>
                        </th>
                        <td>
                            <input type="text" name="plugin_file_path" id="plugin_file_path" class="regular-text" 
                                   value="<?php echo $edit_plugin ? esc_attr( $edit_plugin['file_path'] ) : ''; ?>">
                            <p class="description"><?php esc_html_e( 'Main plugin file path (e.g., woocommerce/woocommerce.php). Used for auto-detection.', 'lumiblog-debug-log-inspector' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="plugin_search_terms"><?php esc_html_e( 'Search Terms', 'lumiblog-debug-log-inspector' ); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="text" name="plugin_search_terms" id="plugin_search_terms" class="regular-text" 
                                   value="<?php echo $edit_plugin ? esc_attr( $edit_plugin['search_terms'] ) : ''; ?>" required>
                            <p class="description"><?php esc_html_e( 'Comma-separated keywords to search for in debug.log (e.g., woocommerce, wc-)', 'lumiblog-debug-log-inspector' ); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php echo $edit_plugin ? esc_html__( 'Update Plugin', 'lumiblog-debug-log-inspector' ) : esc_html__( 'Add Plugin', 'lumiblog-debug-log-inspector' ); ?>
                    </button>
                    <?php if ( $edit_plugin ) : ?>
                        <a href="<?php echo esc_url( admin_url( 'options-general.php?page=debug-log-inspector' ) ); ?>" class="button">
                            <?php esc_html_e( 'Cancel', 'lumiblog-debug-log-inspector' ); ?>
                        </a>
                    <?php endif; ?>
                </p>
            </form>
        </div>

        <!-- Monitored Plugins List -->
        <div class="dli-card">
            <h2><?php esc_html_e( 'Monitored Plugins', 'lumiblog-debug-log-inspector' ); ?></h2>
            
            <?php if ( empty( $plugins ) ) : ?>
                <p><?php esc_html_e( 'No plugins are currently being monitored. Add one using the form above.', 'lumiblog-debug-log-inspector' ); ?></p>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Status', 'lumiblog-debug-log-inspector' ); ?></th>
                            <th><?php esc_html_e( 'Plugin Name', 'lumiblog-debug-log-inspector' ); ?></th>
                            <th><?php esc_html_e( 'File Path', 'lumiblog-debug-log-inspector' ); ?></th>
                            <th><?php esc_html_e( 'Search Terms', 'lumiblog-debug-log-inspector' ); ?></th>
                            <th><?php esc_html_e( 'Active', 'lumiblog-debug-log-inspector' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'lumiblog-debug-log-inspector' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                        foreach ( $plugins as $dli_plugin_index => $plugin ) :
                            $dli_plugin_is_enabled = isset( $plugin['enabled'] ) ? $plugin['enabled'] : true;
                            $dli_plugin_is_active  = ! empty( $plugin['file_path'] ) ? Debug_Log_Inspector::is_plugin_active( $plugin['file_path'] ) : false;

                            // When "Only monitor active plugins" is on and the WP plugin is inactive,
                            // monitoring is effectively disabled regardless of the plugin's own enabled flag.
                            $dli_auto_enable_overrides = $settings['auto_enable'] && ! empty( $plugin['file_path'] ) && ! $dli_plugin_is_active;
                            $dli_effectively_enabled   = $dli_plugin_is_enabled && ! $dli_auto_enable_overrides;
                            ?>
                            <tr class="<?php echo $dli_effectively_enabled ? '' : 'dli-disabled-row'; ?>">
                                <td>
                                    <form method="post" action="" style="display: inline;">
                                        <?php wp_nonce_field( 'debug_log_inspector_action', 'debug_log_inspector_nonce' ); ?>
                                        <input type="hidden" name="debug_log_inspector_action" value="toggle_plugin">
                                        <input type="hidden" name="plugin_index" value="<?php echo esc_attr( $dli_plugin_index ); ?>">
                                        <button type="submit" class="button-link dli-toggle-btn"
                                            <?php if ( $dli_auto_enable_overrides ) : ?>
                                                disabled
                                                title="<?php esc_attr_e( 'Inactive – enable the plugin in WordPress or turn off "Only monitor active plugins"', 'lumiblog-debug-log-inspector' ); ?>"
                                            <?php else : ?>
                                                title="<?php echo $dli_plugin_is_enabled ? esc_attr__( 'Disable', 'lumiblog-debug-log-inspector' ) : esc_attr__( 'Enable', 'lumiblog-debug-log-inspector' ); ?>"
                                            <?php endif; ?>
                                        >
                                            <span class="dashicons dashicons-<?php echo $dli_effectively_enabled ? 'yes-alt' : 'dismiss'; ?>" style="color: <?php echo $dli_effectively_enabled ? '#46b450' : '#dc3232'; ?>;"></span>
                                        </button>
                                    </form>
                                </td>
                                <td><strong><?php echo esc_html( $plugin['name'] ); ?></strong></td>
                                <td><code><?php echo esc_html( $plugin['file_path'] ); ?></code></td>
                                <td><?php echo esc_html( $plugin['search_terms'] ); ?></td>
                                <td>
                                    <?php if ( $dli_plugin_is_active ) : ?>
                                        <span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
                                    <?php else : ?>
                                        <span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url( admin_url( 'options-general.php?page=debug-log-inspector&edit=' . $dli_plugin_index ) ); ?>" class="button button-small">
                                        <?php esc_html_e( 'Edit', 'lumiblog-debug-log-inspector' ); ?>
                                    </a>
                                    <form method="post" action="" style="display: inline;" onsubmit="return confirm('<?php echo esc_js( __( 'Are you sure you want to delete this plugin?', 'lumiblog-debug-log-inspector' ) ); ?>');">
                                        <?php wp_nonce_field( 'debug_log_inspector_action', 'debug_log_inspector_nonce' ); ?>
                                        <input type="hidden" name="debug_log_inspector_action" value="delete_plugin">
                                        <input type="hidden" name="plugin_index" value="<?php echo esc_attr( $dli_plugin_index ); ?>">
                                        <button type="submit" class="button button-small button-link-delete">
                                            <?php esc_html_e( 'Delete', 'lumiblog-debug-log-inspector' ); ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                        // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- General Settings -->
        <div class="dli-card">
            <h2><?php esc_html_e( 'General Settings', 'lumiblog-debug-log-inspector' ); ?></h2>
            
            <form method="post" action="">
                <?php wp_nonce_field( 'debug_log_inspector_action', 'debug_log_inspector_nonce' ); ?>
                <input type="hidden" name="debug_log_inspector_action" value="update_settings">

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="log_max_bytes"><?php esc_html_e( 'Log Scan Size (bytes)', 'lumiblog-debug-log-inspector' ); ?></label>
                        </th>
                        <td>
                            <input type="number" name="log_max_bytes" id="log_max_bytes" class="small-text" 
                                   value="<?php echo esc_attr( $settings['log_max_bytes'] ); ?>" min="10240" step="10240">
                            <p class="description"><?php esc_html_e( 'Maximum bytes to scan from the end of debug.log (default: 307200 = 300KB)', 'lumiblog-debug-log-inspector' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Auto Enable', 'lumiblog-debug-log-inspector' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="auto_enable" value="1" <?php checked( $settings['auto_enable'], true ); ?>>
                                <?php esc_html_e( 'Only monitor plugins that are currently active', 'lumiblog-debug-log-inspector' ); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Show Last Error', 'lumiblog-debug-log-inspector' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="show_last_error" value="1" <?php checked( $settings['show_last_error'], true ); ?>>
                                <?php esc_html_e( 'Display the last error message in admin bar menu', 'lumiblog-debug-log-inspector' ); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary"><?php esc_html_e( 'Save Settings', 'lumiblog-debug-log-inspector' ); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>
