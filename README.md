# Debug Log Inspector for WordPress

![WordPress Plugin Version](https://img.shields.io/badge/version-1.0.0-blue)
![WordPress Compatibility](https://img.shields.io/badge/wordpress-5.0%2B-brightgreen)
![PHP Version](https://img.shields.io/badge/php-7.0%2B-purple)
![License](https://img.shields.io/badge/license-GPL--2.0%2B-orange)

A powerful WordPress plugin for monitoring debug logs and tracking plugin errors in real-time. Monitor any WordPress plugin through an intuitive admin interface - no coding required!

![Debug Log Inspector Banner](https://via.placeholder.com/1200x300/2271b1/ffffff?text=Debug+Log+Inspector)

---

## 🚀 Features

- **🔍 Universal Plugin Monitoring** - Monitor ANY WordPress plugin, not just specific ones
- **⚙️ No Code Required** - Manage everything through a beautiful admin interface
- **🎨 Visual Status Indicators** - Color-coded admin bar (🟢 Green = OK, 🔴 Red = Errors, ⚪ Gray = Disabled)
- **⚡ Real-time Scanning** - Automatically detects errors as they occur
- **🎯 Smart Filtering** - Only monitor active plugins (optional)
- **🔄 Enable/Disable Toggle** - Turn monitoring on/off without deleting entries
- **🚫 Duplicate Prevention** - Smart validation prevents adding the same plugin twice
- **📊 Last Error Display** - Quick access to the most recent error
- **💨 Lightweight** - Minimal performance impact
- **🏗️ Clean Code** - Modular, well-documented, follows WordPress standards

---

## 📸 Screenshots

### Settings Page
![Settings Page](https://via.placeholder.com/800x600/f0f0f1/333333?text=Settings+Page+Screenshot)

### Admin Bar Indicator
![Admin Bar](https://via.placeholder.com/800x200/23282d/ffffff?text=Admin+Bar+Indicator)

### Plugin List Management
![Plugin List](https://via.placeholder.com/800x400/ffffff/333333?text=Plugin+List+Management)

---

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- Debug logging enabled in WordPress

---

## 🔧 Installation

### Via WordPress Admin (Recommended)

1. Download the latest release from the [Releases](https://github.com/yourusername/debug-log-inspector/releases) page
2. Go to **Plugins > Add New > Upload Plugin**
3. Upload the zip file
4. Click **Install Now** and then **Activate**

### Manual Installation

```bash
cd wp-content/plugins
git clone https://github.com/yourusername/debug-log-inspector.git
```

Then activate through WordPress admin.

### Enable Debug Logging

Add these lines to your `wp-config.php` file:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

---

## 🎯 Quick Start

1. **Navigate to Settings > Log Inspector**
2. **Add a plugin to monitor:**
   - Plugin Name: `WooCommerce`
   - Plugin File Path: `woocommerce/woocommerce.php`
   - Search Terms: `woocommerce, wc-`
3. **Save and check your admin bar** for the status indicator

---

## 📖 Usage Guide

### Adding a Plugin to Monitor

1. Go to **Settings > Log Inspector**
2. Fill in the "Add New Plugin to Monitor" form:
   - **Plugin Name**: Display name (e.g., "WooCommerce")
   - **Plugin File Path**: Main plugin file (e.g., "woocommerce/woocommerce.php")
   - **Search Terms**: Keywords to look for in logs (e.g., "woocommerce, wc-")
3. Click **Add Plugin**

### Finding Plugin File Paths

The plugin file path format is: `folder-name/main-file.php`

**Examples:**
- WooCommerce: `woocommerce/woocommerce.php`
- Yoast SEO: `wordpress-seo/wp-seo.php`
- Contact Form 7: `contact-form-7/wp-contact-form-7.php`

You can find this under **Plugins > Installed Plugins** (shown below each plugin name).

### Understanding Search Terms

Search terms are keywords the plugin looks for in your `debug.log` file. Use unique identifiers from the plugin:

- **WooCommerce**: `woocommerce, wc-`
- **Elementor**: `elementor, elementor-pro`
- **Yoast SEO**: `yoast, wpseo`

**Pro Tip:** Check your debug.log to see what keywords appear in error messages from the plugin.

---

## 🧪 Testing the Plugin

### Method 1: Test Plugin (Recommended)

Create `wp-content/plugins/test-error/test-error.php`:

```php
<?php
/**
 * Plugin Name: Test Error Generator
 */
if ( isset( $_GET['trigger_test_error'] ) ) {
    trigger_error( 'Test error from WooCommerce', E_USER_WARNING );
}
```

1. Activate the test plugin
2. Visit: `yoursite.com/wp-admin/?trigger_test_error=1`
3. Check admin bar - should turn RED
4. Click "LOG INSPECTOR" to see the error

### Method 2: Quick Test

Temporarily add this to any active plugin:

```php
trigger_error( 'woocommerce test error', E_USER_WARNING );
```

Reload admin, check the admin bar, then remove the code.

---

## 🏗️ File Structure

```
debug-log-inspector/
├── debug-log-inspector.php       # Main plugin file
├── readme.txt                    # WordPress.org readme
├── README.md                     # This file
├── includes/
│   ├── class-log-inspector.php   # Core scanning logic
│   ├── class-settings.php        # Settings page handler
│   └── class-admin-bar.php       # Admin bar display
├── assets/
│   ├── css/
│   │   └── admin-style.css       # Admin interface styles
│   └── js/
│       └── admin-script.js       # Admin interface scripts
└── templates/
    └── settings-page.php          # Settings page template
```

---

## 🎨 Admin Bar Colors

| Color | Status | Description |
|-------|--------|-------------|
| 🟢 **Green** | All Clear | No errors detected in monitored plugins |
| 🔴 **Red** | Errors Found | At least one plugin has errors |
| ⚪ **Gray** | Disabled | Debug logging is not enabled |

---

## ⚙️ Settings

### General Settings

- **Log Scan Size**: Maximum bytes to scan (default: 300KB)
- **Auto Enable**: Only monitor active plugins
- **Show Last Error**: Display most recent error in admin bar

---

## 🛠️ Development

### Setting Up Development Environment

```bash
# Clone the repository
git clone https://github.com/yourusername/debug-log-inspector.git

# Navigate to the plugin directory
cd debug-log-inspector

# Create a symlink in your WordPress plugins directory
ln -s $(pwd) /path/to/wordpress/wp-content/plugins/debug-log-inspector
```

### Coding Standards

This plugin follows [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/).

Run PHP CodeSniffer:

```bash
phpcs --standard=WordPress debug-log-inspector/
```

### Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📝 Changelog

### Version 2.0.0 (Current)

#### 🎉 Major Rewrite
- ✨ Complete plugin rewrite with modular architecture
- ✨ Universal plugin monitoring - works with ANY WordPress plugin
- ✨ Settings page for easy management (no code editing)
- ✨ Add/Edit/Delete plugins through admin interface
- ✨ Enable/Disable toggle for individual plugins
- ✨ Duplicate prevention using file paths and names
- ✨ Improved security with proper nonce verification
- ✨ Better internationalization support
- ✨ Full WordPress Coding Standards compliance
- 🐛 Fixed multiple security warnings
- 🎨 Modern admin UI with better UX

### Version 1.0.0

- 🎉 Initial release
- ⚡ Basic monitoring for predefined plugins
- 🎨 Color-coded admin bar indicator
- 📊 Last error message display

[View Full Changelog](CHANGELOG.md)

---

## 🐛 Known Issues

- None currently reported

[Report a Bug](https://github.com/yourusername/debug-log-inspector/issues)

---

## 🗺️ Roadmap

### Planned Features

- [ ] Email notifications when errors detected
- [ ] Export error logs to CSV
- [ ] Error statistics dashboard
- [ ] Multi-site network admin support
- [ ] Integration with error tracking services (Sentry, Rollbar)
- [ ] Custom alert thresholds
- [ ] Error categorization (warnings vs fatal)
- [ ] Automated testing suite
- [ ] WP-CLI commands

[View Full Roadmap](https://github.com/yourusername/debug-log-inspector/projects)

---

## ❓ FAQ

### How do I enable debug logging?

Add to your `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

### Will this slow down my site?

No. The plugin only runs in the WordPress admin area and uses efficient file reading.

### Can I monitor custom plugins?

Yes! As long as the plugin writes to the debug log, you can monitor it.

### Does it work with Multisite?

Yes, the plugin works on WordPress Multisite installations.

[More FAQs](https://github.com/yourusername/debug-log-inspector/wiki/FAQ)

---

## 🔒 Security

Debug Log Inspector takes security seriously. We follow WordPress security best practices:

- ✅ Proper nonce verification
- ✅ Data sanitization and validation
- ✅ Capability checks
- ✅ Escaping output
- ✅ No external API calls
- ✅ Local file reading only

**Found a security issue?** Please report it privately to security@paluhost.co.ke

---

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2025 Paluhost Web Services

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

[Full License Text](LICENSE)

---

## 👥 Credits

### Developed By

**Paluhost Web Services**
- Website: [https://paluhost.co.ke](https://paluhost.co.ke)
- GitHub: [@paluhost](https://github.com/paluhost)

### Contributors

Thanks to these wonderful people who have contributed to this project:

<!-- Add contributor images here -->
- [Your Name](https://github.com/yourusername)

Want to contribute? See [CONTRIBUTING.md](CONTRIBUTING.md)

---

## 💬 Support

### Documentation

- [Installation Guide](https://github.com/yourusername/debug-log-inspector/wiki/Installation)
- [User Guide](https://github.com/yourusername/debug-log-inspector/wiki/User-Guide)
- [Developer Documentation](https://github.com/yourusername/debug-log-inspector/wiki/Developer-Docs)

### Get Help

- [GitHub Issues](https://github.com/yourusername/debug-log-inspector/issues)
- [Support Forum](https://wordpress.org/support/plugin/debug-log-inspector/)
- Email: support@paluhost.co.ke

### Community

- [Discussions](https://github.com/yourusername/debug-log-inspector/discussions)
- [Twitter](https://twitter.com/paluhost)

---

## 🌟 Show Your Support

If you find this plugin helpful, please:

- ⭐ Star this repository
- 🐛 Report bugs and issues
- 💡 Suggest new features
- 🔀 Submit pull requests
- 📢 Share with the WordPress community
- ☕ [Buy us a coffee](https://www.buymeacoffee.com/paluhost)

---

## 📊 Stats

![GitHub stars](https://img.shields.io/github/stars/yourusername/debug-log-inspector?style=social)
![GitHub forks](https://img.shields.io/github/forks/yourusername/debug-log-inspector?style=social)
![GitHub issues](https://img.shields.io/github/issues/yourusername/debug-log-inspector)
![GitHub pull requests](https://img.shields.io/github/issues-pr/yourusername/debug-log-inspector)
![GitHub last commit](https://img.shields.io/github/last-commit/yourusername/debug-log-inspector)

---

**Made with ❤️ by [Paluhost Web Services](https://paluhost.co.ke)**

[⬆ Back to Top](#debug-log-inspector-for-wordpress)
