1. Plugin Name: Turbo Cleanup Protocol
2. Short Description: A unique PHP-only WordPress utility. A turbo style cleanup plugin acting as a protocol. Focused on simplicity and efficiency.
3. Detailed Description: Turbo Cleanup Protocol is a lightweight, PHP-only WordPress plugin designed to optimize your site by immediately and efficiently removing unnecessary clutter and features. Acting as a "turbo style" cleanup utility, it applies a comprehensive set of protocols without requiring a separate settings page, focusing on simplicity and performance.

Upon activation, the plugin removes various elements from the WordPress head, including RSD links, Windows Live Writer manifest links, shortlinks, WordPress version meta tags, and all feed links. It also cleans up REST API and oEmbed discovery links from both the head and HTTP headers.

A core focus is the complete disabling of emojis and embeds. This includes removing emoji detection scripts and styles, emoji-related filters, the emoji TinyMCE plugin, and the emoji SVG URL. For embeds, it removes the oEmbed JavaScript, filters, and prevents oEmbed HTML generation, including filtering Gutenberg embed blocks to remove their output.

Furthermore, the plugin enhances security and performance by completely disabling XML-RPC functionality and removing the X-Pingback header. It also intelligently deregisters unwanted scripts like 'comment-reply' (when not needed) and 'wp-embed', along with associated styles like 'wp-embed-template-css'.

An activation notice is displayed once to inform the user that the turbo cleanup protocols are active, ensuring a smooth and transparent experience. This plugin is ideal for users seeking a no-frills, highly efficient solution to streamline their WordPress installation.

4. GitHub URL: https://github.com/ogichanchan/turbo-cleanup-protocol