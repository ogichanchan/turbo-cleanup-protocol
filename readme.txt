=== Turbo Cleanup Protocol ===
Contributors: ogichanchan
Tags: wordpress, plugin, tool, admin, performance, cleanup, optimization, security, bloat
Requires at least: 6.2
Tested up to: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Turbo Cleanup Protocol is a lightweight, PHP-only WordPress plugin designed to optimize your site by removing unnecessary clutter and features. Acting as a protocol, it focuses on simplicity and efficiency, applying a comprehensive set of cleanup actions immediately upon activation, without the need for a separate settings page.

This plugin enhances performance and security by:
*   **Cleaning up the Head:** Removing RSD, WLW manifest, shortlinks, WordPress version, and various feed links.
*   **Disabling REST API & oEmbed Discovery:** Eliminating unnecessary links and headers related to the WordPress REST API and oEmbed functionality.
*   **Removing Emojis:** Completely disabling emoji scripts, styles, and related functionality across the site and admin.
*   **Disabling Embeds:** Preventing oEmbed script loading, HTML generation, and filtering Gutenberg embed blocks.
*   **Disabling XML-RPC:** Fully deactivating XML-RPC and removing the X-Pingback header for improved security.
*   **Deregistering Unwanted Scripts & Styles:** Removing 'comment-reply', 'wp-embed', and 'wp-embed-template-css' to reduce page load.

Upon activation, an admin notice will confirm that your WordPress site is now operating under turbo cleanup protocols, optimizing performance and security by removing unnecessary clutter and features.

This plugin is open source. Report bugs at: https://github.com/ogichanchan/turbo-cleanup-protocol

== Installation ==
1. Upload to /wp-content/plugins/
2. Activate

== Changelog ==
= 1.0.0 =
* Initial release.