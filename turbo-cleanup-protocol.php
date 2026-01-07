<?php
/**
 * Plugin Name: Turbo Cleanup Protocol
 * Plugin URI: https://github.com/ogichanchan/turbo-cleanup-protocol
 * Description: A unique PHP-only WordPress utility. A turbo style cleanup plugin acting as a protocol. Focused on simplicity and efficiency.
 * Version: 1.0.0
 * Author: ogichanchan
 * Author URI: https://github.com/ogichanchan
 * License: GPLv2 or later
 * Text Domain: turbo-cleanup-protocol
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Turbo_Cleanup_Protocol Class.
 *
 * This class encapsulates all the cleanup functionalities for the plugin.
 * It applies various optimizations and cleanups to WordPress by removing
 * unnecessary elements from the head, disabling features, and deregistering scripts/styles.
 *
 * All operations are immediate upon activation without a separate settings page,
 * adhering to a "turbo style" cleanup focused on simplicity and efficiency.
 */
class Turbo_Cleanup_Protocol {

    /**
     * Constructor.
     * Registers all necessary WordPress hooks (actions and filters)
     * to apply cleanup protocols.
     */
    public function __construct() {
        // Register the core cleanup actions to run early in WordPress lifecycle.
        add_action( 'init', array( $this, 'apply_core_cleanup_filters' ) );

        // Deregister specific scripts and styles on 'wp_enqueue_scripts' hook.
        // Priority 999 ensures most other plugins have already enqueued their scripts.
        add_action( 'wp_enqueue_scripts', array( $this, 'deregister_unwanted_scripts' ), 999 );
        add_action( 'wp_print_styles', array( $this, 'deregister_unwanted_styles' ), 999 );

        // Add an admin notice upon plugin activation to inform the user.
        add_action( 'admin_notices', array( $this, 'display_activation_notice' ) );
    }

    /**
     * Applies a set of core cleanup filters and actions.
     * This method is hooked to 'init' and removes various links,
     * disables emojis, embeds, and XML-RPC.
     */
    public function apply_core_cleanup_filters() {
        // --- Head Cleanup ---
        // Remove RSD (Really Simple Discovery) link.
        remove_action( 'wp_head', 'rsd_link' );
        // Remove Windows Live Writer manifest link.
        remove_action( 'wp_head', 'wlwmanifest_link' );
        // Remove shortlink if it's not explicitly used.
        remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
        // Remove WordPress version generator meta tag.
        remove_action( 'wp_head', 'wp_generator' );
        // Remove feed links (post and comment feeds).
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'feed_links', 2 );

        // --- REST API / WP-JSON Cleanup ---
        // Remove REST API discovery link.
        remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
        // Remove oEmbed discovery links for the REST API.
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
        // Remove REST API link from HTTP headers.
        remove_action( 'template_redirect', 'rest_output_link_header', 11 );

        // --- Emojis Cleanup ---
        // Remove the emoji detection script.
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        // Remove the emoji styles.
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        // Remove emoji-related filters.
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        // Disable emoji TinyMCE plugin.
        add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojis_tinymce_plugin' ) );
        // Remove emoji SVG URL and DNS prefetching.
        add_filter( 'emoji_svg_url', '__return_false' );

        // --- Embeds Cleanup ---
        // Remove the oEmbed JavaScript for hosts.
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        // Remove filters that register oEmbed handlers.
        remove_filter( 'the_content_feed', 'wp_embed_register_handler' );
        remove_filter( 'the_content_remove_wpautop', 'wp_embed_register_handler' );
        remove_filter( 'widget_text_content', 'wp_embed_register_handler' );
        // Completely disable oEmbed HTML generation.
        add_filter( 'embed_oembed_html', '__return_false', 9999 );
        // Filter Gutenberg embed blocks to remove their output.
        add_filter( 'render_block', array( $this, 'filter_gutenberg_embed_blocks' ), 10, 2 );

        // --- XML-RPC Cleanup ---
        // Completely disable XML-RPC functionality.
        add_filter( 'xmlrpc_enabled', '__return_false' );
        // Remove X-Pingback header from HTTP responses.
        add_filter( 'wp_headers', array( $this, 'remove_x_pingback_header_filter' ) );
        // Explicitly remove X-Pingback using header_remove if available.
        add_action( 'template_redirect', array( $this, 'remove_x_pingback_header_action' ) );
    }

    /**
     * Filters TinyMCE plugins to disable the 'wpemoji' plugin.
     *
     * @param array $plugins An array of TinyMCE plugins.
     * @return array The filtered array of TinyMCE plugins.
     */
    public function disable_emojis_tinymce_plugin( $plugins ) {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, array( 'wpemoji' ) );
        }
        return $plugins;
    }

    /**
     * Deregisters specific scripts like 'comment-reply' and 'wp-embed'.
     * This method is hooked to 'wp_enqueue_scripts'.
     */
    public function deregister_unwanted_scripts() {
        // Deregister 'comment-reply' script if not on a singular page with comments open.
        if ( ! is_singular() || ( ! comments_open() && get_option( 'thread_comments' ) ) ) {
            wp_deregister_script( 'comment-reply' );
        }
        // Deregister 'wp-embed' script.
        wp_deregister_script( 'wp-embed' );
    }

    /**
     * Deregisters specific styles like 'wp-embed-template-css'.
     * This method is hooked to 'wp_print_styles'.
     */
    public function deregister_unwanted_styles() {
        // Deregister 'wp-embed-template-css'.
        wp_deregister_style( 'wp-embed-template-css' );
    }

    /**
     * Filters Gutenberg embed blocks to prevent their rendering.
     *
     * @param string $block_content The HTML content of the block.
     * @param array  $block         The full block array.
     * @return string Modified block content (empty for embed blocks).
     */
    public function filter_gutenberg_embed_blocks( $block_content, $block ) {
        if ( 'core/embed' === $block['blockName'] ) {
            return ''; // Return an empty string to remove the embed block content.
        }
        return $block_content;
    }

    /**
     * Filters HTTP headers to remove the 'X-Pingback' header.
     *
     * @param array $headers The array of HTTP headers.
     * @return array The modified array of HTTP headers.
     */
    public function remove_x_pingback_header_filter( $headers ) {
        unset( $headers['X-Pingback'] );
        return $headers;
    }

    /**
     * Removes the 'X-Pingback' header using `header_remove()` if the function exists.
     * This is an alternative/redundant measure to ensure the header is removed.
     */
    public function remove_x_pingback_header_action() {
        if ( function_exists( 'header_remove' ) ) {
            header_remove( 'X-Pingback' );
        }
    }

    /**
     * Displays an admin notice upon plugin activation.
     * This notice is transient and only shown once after activation.
     */
    public function display_activation_notice() {
        // Only show notice to users who can manage options.
        if ( current_user_can( 'manage_options' ) ) {
            // Check if the transient for this notice exists.
            if ( get_transient( 'turbo_cleanup_protocol_activation_notice' ) ) {
                ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <strong><?php esc_html_e( 'Turbo Cleanup Protocol Activated!', 'turbo-cleanup-protocol' ); ?></strong>
                        <?php esc_html_e( 'Your WordPress site is now operating under turbo cleanup protocols, optimizing performance and security by removing unnecessary clutter and features.', 'turbo-cleanup-protocol' ); ?>
                    </p>
                </div>
                <?php
                // Delete the transient so the notice doesn't show again.
                delete_transient( 'turbo_cleanup_protocol_activation_notice' );
            }
        }
    }

    /**
     * Static method for activation hook callback.
     * Sets a transient to display an admin notice upon plugin activation.
     */
    public static function activate() {
        // Set a transient that expires quickly, making the notice show once.
        set_transient( 'turbo_cleanup_protocol_activation_notice', true, 5 );
    }

    /**
     * Static method for deactivation hook callback.
     * Cleans up any transients left by the plugin.
     */
    public static function deactivate() {
        // Clean up the activation notice transient upon deactivation.
        delete_transient( 'turbo_cleanup_protocol_activation_notice' );
    }
}

// Instantiate the plugin class to start its functionality.
// This ensures the constructor is called and all hooks are registered.
$turbo_cleanup_protocol = new Turbo_Cleanup_Protocol();

// Register activation and deactivation hooks.
// These methods must be static within the class.
register_activation_hook( __FILE__, array( 'Turbo_Cleanup_Protocol', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Turbo_Cleanup_Protocol', 'deactivate' ) );