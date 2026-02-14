=== Lumiblog Debug Log Inspector ===
Contributors: lumiblog
Donate link: https://lumumbas-blog.co.ke/support-wp-plugins
Tags: debug, log, monitor, testing, error tracking
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Monitor debug logs for any WordPress plugin errors and display real-time status in the WordPress admin bar.

== Description ==

Lumiblog Debug Log Inspector is a powerful quality assurance and debugging tool that monitors your WordPress debug.log file for plugin-specific errors. Unlike other debug tools, it allows you to **monitor ANY WordPress plugin** through an easy-to-use settings interface - no coding required!

= Key Features =

* **Universal Plugin Monitoring**: Add any WordPress plugin to monitor through a simple settings page
* **No Code Editing**: Add/edit/delete monitored plugins through the WordPress admin interface
* **Real-time Monitoring**: Automatically scans your debug.log file for errors
* **Visual Status Indicators**: Color-coded admin bar display (Green = OK, Red = Errors Found, Gray = Debug Logging Disabled)
* **Auto-Detection**: Option to only monitor plugins that are currently active
* **Plugin-Specific Tracking**: Individual status for each monitored plugin
* **Last Error Display**: Shows the most recent error message for quick diagnosis
* **Duplicate Prevention**: Smart validation prevents adding the same plugin twice
* **Enable/Disable Plugins**: Toggle monitoring for specific plugins without deleting them
* **Lightweight**: Minimal performance impact with efficient log reading
* **Well-Organized Code**: Modular file structure for easy maintenance and customization

= Perfect For =

* QA Teams testing multiple plugins
* Plugin Developers debugging their own plugins
* WordPress Developers monitoring client sites
* Agency Teams managing multiple WordPress installations
* Anyone who wants to keep track of plugin errors

= How It Works =

1. Install and activate the plugin
2. Go to Settings > Log Inspector
3. Add any plugin you want to monitor by providing:
   - Plugin Name (e.g., "WooCommerce")
   - Plugin File Path (e.g., "woocommerce/woocommerce.php")
   - Search Terms (e.g., "woocommerce, wc-")
4. Check the admin bar for real-time error status

= Requirements =

To use this plugin effectively, you need to enable WordPress debug logging by adding these constants to your `wp-config.php` file:

`
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
`

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "Lumiblog Debug Log Inspector"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Upload the `debug-log-inspector` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Log Inspector to configure

= After Installation =

1. Ensure WP_DEBUG_LOG is enabled in your `wp-config.php` file
2. Navigate to Settings > Log Inspector
3. Add plugins you want to monitor
4. Look for "LOG INSPECTOR" in your WordPress admin bar

== Frequently Asked Questions ==

= How do I enable debug logging? =

Add these lines to your `wp-config.php` file (before the "That's all, stop editing!" line):

`
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
`

= What do the colors in the admin bar mean? =

* **Green**: All monitored plugins are error-free
* **Red**: At least one plugin has errors in the debug log
* **Gray**: Debug logging is not enabled

= Can I monitor any WordPress plugin? =

Yes! You can add any WordPress plugin to monitor. Just provide the plugin name, file path, and search terms through the settings page.

= What are "Search Terms"? =

Search terms are keywords that the plugin looks for in your debug.log file. For example, if you're monitoring WooCommerce, you might use "woocommerce, wc-" as search terms. These should be unique identifiers that appear in error messages from that plugin.

= How do I find the plugin file path? =

The plugin file path is usually in the format: `folder-name/main-file.php`

For example:
- WooCommerce: `woocommerce/woocommerce.php`
- Contact Form 7: `contact-form-7/wp-contact-form-7.php`
- Yoast SEO: `wordpress-seo/wp-seo.php`

You can find this in your WordPress admin under Plugins > Installed Plugins (it's shown below each plugin name).

= How much of the debug.log is scanned? =

By default, the plugin scans the last 300KB of your debug.log file. You can customize this in Settings > Log Inspector > General Settings.

= Will this slow down my site? =

No. The plugin only runs in the WordPress admin area and uses efficient file reading techniques to minimize performance impact.

= Can I temporarily disable monitoring for a plugin? =

Yes! In the settings page, you can toggle any plugin on/off without deleting it from your list.

= Does this work with Multisite? =

Yes, the plugin works on WordPress Multisite installations.

= Can I monitor custom/proprietary plugins? =

Absolutely! As long as the plugin generates errors in the debug.log, you can monitor it.

= How do I test if the plugin is working? =

See the "Testing the Plugin" section below for detailed instructions on how to verify everything is working correctly.

== Testing the Plugin ==

To verify Lumiblog Debug Log Inspector is working correctly, follow these steps:

**Step 1: Enable Debug Logging**

Add to your `wp-config.php`:
`
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
`

**Step 2: Add a Plugin to Monitor**

1. Go to Settings > Log Inspector
2. Click "Add New Plugin to Monitor"
3. Fill in the form with a plugin you have installed (e.g., WooCommerce)
   - Plugin Name: `WooCommerce`
   - Plugin File Path: `woocommerce/woocommerce.php`
   - Search Terms: `woocommerce, wc-`
4. Click "Add Plugin"

**Step 3: Generate a Test Error**

Note: Ensure that the test site is running on PHP 8.4+

1. Add this code snippet inside of any active plugin's main file:

`
/**
 * DEBUG LOG INSPECTOR DEMO - PHP 8.4 Warning Trigger
 * REMOVE AFTER DEMO/TESTING!
 */
add_action( 'load-index.php', function() {
    if ( version_compare( PHP_VERSION, '8.4.0', '>=' ) ) {
        $test_array = array( 'name' => 'Debug Log Inspector' );
        $undefined_value = $test_array['email']; // Triggers warning
        error_log( '[DLI-DEMO-PHP84] PHP 8.4 warning triggered!' );
    }
});
`

2. Reload the site's admin dashboard to trigger the error
3. Check your admin bar - it should turn RED

**Step 4: View the Debug Log (Optional)**

Navigate to `wp-content/debug.log` to see the actual error entries that were logged.

**Clean Up:**

1. Remove the test code you added
2. Either delete `wp-content/debug.log` or clear its contents
3. The admin bar should return to GREEN

== Additional Information ==

= Debug Constants =

For enhanced debugging, you can also add these optional constants to your `wp-config.php`:

`
define( 'SCRIPT_DEBUG', true );
define( 'SAVEQUERIES', true );
define( 'WP_DEBUG_DISPLAY', false ); // Set to false on production sites
`

= For Developers =

The plugin is designed to be easily extendable. All classes are well-documented and follow WordPress coding standards. Feel free to fork and customize for your specific needs.

= Support =

For support, feature requests, or bug reports, please visit the WordPress.org support forums for this plugin.

= Contributing =

Contributions are welcome! Please visit the plugin's [Github Repo](https://github.com/lumumbapl/lumiblog-debug-log-inspector) for more information.

== Screenshots ==

1. Settings page - Add new plugins to monitor
2. Monitored plugins list with enable/disable toggles
3. Admin bar showing green status (no errors)
4. Admin bar showing red status with error details
5. General settings configuration
6. Add plugin form with field descriptions
7. Edit plugin interface

== Changelog ==

= 1.1.0 [February 15, 2026] =
* Fixed: Minor issues across the plugin [[Issue #1]](https://github.com/lumumbapl/lumiblog-debug-log-inspector/issues/1)
* Enhanced: Compatibility with PHP 8.5 [[Issue #2]](https://github.com/lumumbapl/lumiblog-debug-log-inspector/issues/2)
* Fixed: Inactive plugins incorrectly shown as enabled when "Only monitor active plugins" is turned on [[Issue #3]](https://github.com/lumumbapl/lumiblog-debug-log-inspector/issues/3)
* Fixed: Edit button for monitored plugins not working due to incorrect page slug [[Issue #4]](https://github.com/lumumbapl/lumiblog-debug-log-inspector/issues/4)
* Fixed: Edit form not reset to "Add New Plugin" after a successful update [[Issue #5]](https://github.com/lumumbapl/lumiblog-debug-log-inspector/issues/5)
* Fixed: Cancel button on the Edit Plugin page not working due to incorrect page slug [[Issue #6]](https://github.com/lumumbapl/lumiblog-debug-log-inspector/issues/6)

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.1.0 =
This update fixes several bugs including inactive plugins showing as enabled, broken Edit and Cancel buttons on the settings page, and improves compatibility with PHP 8.5.


