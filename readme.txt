=== Debug Log Inspector ===
Contributors: paluhost, lumiblog
Donate link: https://lumumbas.blog/support-wp-plugins
Tags: debug, log, monitor, testing, error tracking
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 0.0.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Monitor debug logs for Paluhost plugin errors and display real-time status in the WordPress admin bar.

== Description ==

Paluhost Log Inspector is a quality assurance and debugging tool designed specifically for monitoring Paluhost Web Services plugins. It scans your WordPress debug.log file for plugin-specific errors and displays an easy-to-read status indicator in the admin bar.

= Key Features =

* **Real-time Monitoring**: Automatically scans the last 300KB of your debug.log file
* **Visual Status Indicators**: Color-coded admin bar display (Green = OK, Red = Errors Found, Gray = Debug Logging Disabled)
* **Auto-Detection**: Automatically detects which Paluhost plugins are active and monitors them
* **Plugin-Specific Tracking**: Individual status for each monitored plugin
* **Last Error Display**: Shows the most recent error message for quick diagnosis
* **Lightweight**: Minimal performance impact with efficient log reading

= Monitored Plugins =

Currently supports monitoring for the following Paluhost plugins:

* Page Categorizer
* Footnotes Made Easy
* Quick Event Calendar
* Search Engines Blocked in Header
* Bulk Variations for WooCommerce

= Requirements =

To use this plugin effectively, you need to enable WordPress debug logging by adding these constants to your `wp-config.php` file:

`
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
`

== Installation ==

1. Upload the `paluhost-log-inspector` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure WP_DEBUG_LOG is enabled in your `wp-config.php` file
4. Look for "PALUHOST LOG INSPECTOR" in your WordPress admin bar

== Frequently Asked Questions ==

= How do I enable debug logging? =

Add these lines to your `wp-config.php` file (before the "That's all, stop editing!" line):

`
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
`

= What do the colors mean? =

* **Green**: All monitored plugins are error-free
* **Red**: At least one plugin has errors in the debug log
* **Gray**: Debug logging is not enabled

= How much of the debug.log is scanned? =

The plugin scans the last 300KB of your debug.log file by default. This can be customized in the plugin code if needed.

= Will this slow down my site? =

No. The plugin only runs in the WordPress admin area and uses efficient file reading techniques to minimize performance impact.

= Can I add more plugins to monitor? =

Yes! The plugin is designed to be easily customizable. You can add additional plugins by modifying the class properties in the main plugin file.

= Does this work with Multisite? =

Yes, the plugin works on WordPress Multisite installations.

== Screenshots ==

1. Admin bar showing green status (no errors found)
2. Admin bar showing red status with plugin-specific error indicators
3. Dropdown menu displaying individual plugin statuses
4. Last error message display in the submenu

== Changelog ==

= 1.0 =
* Initial release
* Auto-detection for 5 Paluhost plugins
* Color-coded status indicators
* Last error message display
* Efficient log file scanning

== Upgrade Notice ==

= 1.0 =
Initial release of Paluhost Log Inspector.

== Additional Information ==

= Debug Constants =

For enhanced debugging, you can also add these optional constants to your `wp-config.php`:

`
define( 'SCRIPT_DEBUG', true );
define( 'SAVEQUERIES', true );
define( 'WP_DEBUG_DISPLAY', true ); // Set to false on production sites
`

= Support =

For support, feature requests, or bug reports, please contact Paluhost Web Services.

= Developer Notes =

The plugin reads from the WordPress debug.log file location, which is typically `wp-content/debug.log`. If you've defined a custom WP_DEBUG_LOG path, the plugin will use that location instead.

== Privacy Policy ==

Paluhost Log Inspector does not collect, store, or transmit any user data. All monitoring happens locally on your server by reading the debug.log file.
