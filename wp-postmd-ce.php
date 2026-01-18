<?php
/**
 * Plugin Name: WP Post to Markdown - Classic Editor
 * Plugin URI: https://github.com/dlukt/wp-postmd-ce
 * Description: Converts classic editor posts into markdown documents with alternate link meta tags for AI-friendly content consumption.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Darko Luketic
 * Author URI: https://github.com/dlukt
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: wp-postmd-ce
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add markdown alternate link meta tag to post head
 */
function wppostmd_add_markdown_link_tag() {
    // Only add to single posts
    if (!is_single()) {
        return;
    }
    
    global $post;
    
    // Only process published posts
    if ($post->post_status !== 'publish') {
        return;
    }
    
    // Construct markdown URL
    $markdown_url = trailingslashit(get_site_url()) . $post->post_name . '.md';
    
    // Output the link tag
    echo '<link rel="alternate" type="text/markdown" title="Markdown Version" href="' . esc_url($markdown_url) . '">' . "\n";
}
add_action('wp_head', 'wppostmd_add_markdown_link_tag');

/**
 * Handle markdown URL requests
 */
function wppostmd_handle_markdown_request() {
    // Check if the request is for a .md file
    $request_uri = sanitize_text_field($_SERVER['REQUEST_URI']);
    
    if (!preg_match('/\/([^\/]+)\.md$/', $request_uri, $matches)) {
        return;
    }
    
    $slug = sanitize_text_field($matches[1]);
    
    // Find post by slug
    $post = get_page_by_path($slug, OBJECT, 'post');
    
    if (!$post || $post->post_status !== 'publish') {
        return;
    }
    
    // Convert post to markdown
    $markdown = wppostmd_convert_to_markdown($post);
    
    // Set headers
    header('Content-Type: text/markdown; charset=utf-8');
    header('Content-Disposition: inline; filename="' . sanitize_file_name($post->post_name) . '.md"');
    
    // Output markdown
    echo $markdown;
    exit;
}
add_action('init', 'wppostmd_handle_markdown_request', 1);

/**
 * Convert WordPress post to markdown format
 *
 * @param WP_Post $post The post object
 * @return string Markdown formatted content
 */
function wppostmd_convert_to_markdown($post) {
    $markdown = '';
    
    // Add title
    $markdown .= "# " . $post->post_title . "\n\n";
    
    // Add metadata
    $markdown .= "**Author:** " . get_the_author_meta('display_name', $post->post_author) . "\n";
    $markdown .= "**Published:** " . get_the_date('Y-m-d H:i:s', $post) . "\n";
    $markdown .= "**URL:** " . get_permalink($post->ID) . "\n\n";
    
    // Add categories if any
    $categories = get_the_category($post->ID);
    if (!empty($categories)) {
        $cat_names = array_map(function($cat) {
            return $cat->name;
        }, $categories);
        $markdown .= "**Categories:** " . implode(', ', $cat_names) . "\n";
    }
    
    // Add tags if any
    $tags = get_the_tags($post->ID);
    if (!empty($tags)) {
        $tag_names = array_map(function($tag) {
            return $tag->name;
        }, $tags);
        $markdown .= "**Tags:** " . implode(', ', $tag_names) . "\n";
    }
    
    if (!empty($categories) || !empty($tags)) {
        $markdown .= "\n";
    }
    
    $markdown .= "---\n\n";
    
    // Get post content
    $content = $post->post_content;
    
    // Apply WordPress content filters
    $content = apply_filters('the_content', $content);
    
    // Convert HTML to markdown
    $content = wppostmd_html_to_markdown($content);
    
    $markdown .= $content;
    
    return $markdown;
}

/**
 * Convert HTML content to markdown
 *
 * @param string $html HTML content
 * @return string Markdown formatted content
 */
function wppostmd_html_to_markdown($html) {
    // Remove script and style tags
    $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
    $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
    
    // Convert code blocks first (must be before inline code) - allow whitespace between pre and code tags
    $html = preg_replace('/<pre[^>]*>\s*<code[^>]*>(.*?)<\/code>\s*<\/pre>/is', "\n```\n$1\n```\n", $html);
    
    // Convert headings
    $html = preg_replace('/<h1[^>]*>(.*?)<\/h1>/is', "\n# $1\n", $html);
    $html = preg_replace('/<h2[^>]*>(.*?)<\/h2>/is', "\n## $1\n", $html);
    $html = preg_replace('/<h3[^>]*>(.*?)<\/h3>/is', "\n### $1\n", $html);
    $html = preg_replace('/<h4[^>]*>(.*?)<\/h4>/is', "\n#### $1\n", $html);
    $html = preg_replace('/<h5[^>]*>(.*?)<\/h5>/is', "\n##### $1\n", $html);
    $html = preg_replace('/<h6[^>]*>(.*?)<\/h6>/is', "\n###### $1\n", $html);
    
    // Convert bold and strong (handle both separately to avoid mismatched tags)
    $html = preg_replace('/<strong[^>]*>(.*?)<\/strong>/is', "**$1**", $html);
    $html = preg_replace('/<b[^>]*>(.*?)<\/b>/is', "**$1**", $html);
    
    // Convert italic and em (handle both separately to avoid mismatched tags)
    $html = preg_replace('/<em[^>]*>(.*?)<\/em>/is', "*$1*", $html);
    $html = preg_replace('/<i[^>]*>(.*?)<\/i>/is', "*$1*", $html);
    
    // Convert links
    $html = preg_replace('/<a[^>]+href="([^"]*)"[^>]*>(.*?)<\/a>/is', '[$2]($1)', $html);
    
    // Convert images
    $html = preg_replace('/<img[^>]+src="([^"]*)"[^>]*alt="([^"]*)"[^>]*\/?>/is', '![$2]($1)', $html);
    $html = preg_replace('/<img[^>]+src="([^"]*)"[^>]*\/?>/is', '![]($1)', $html);
    
    // Convert inline code (after processing links to avoid conflicts)
    $html = preg_replace('/<code[^>]*>(.*?)<\/code>/is', "`$1`", $html);
    
    // Convert ordered lists with numbered items (process after inline elements)
    $html = preg_replace_callback('/<ol[^>]*>(.*?)<\/ol>/is', function($matches) {
        $content = $matches[1];
        preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $content, $items);
        $result = "\n";
        foreach ($items[1] as $index => $item) {
            // Strip remaining HTML tags but keep the converted markdown
            $item = preg_replace('/<[^>]+>/', '', $item);
            $result .= ($index + 1) . ". " . trim($item) . "\n";
        }
        return $result;
    }, $html);
    
    // Convert unordered lists (process after inline elements)
    $html = preg_replace_callback('/<ul[^>]*>(.*?)<\/ul>/is', function($matches) {
        $content = $matches[1];
        preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $content, $items);
        $result = "\n";
        foreach ($items[1] as $item) {
            // Strip remaining HTML tags but keep the converted markdown
            $item = preg_replace('/<[^>]+>/', '', $item);
            $result .= "* " . trim($item) . "\n";
        }
        return $result;
    }, $html);
    
    // Convert blockquotes (process after inline elements)
    $html = preg_replace_callback('/<blockquote[^>]*>(.*?)<\/blockquote>/is', function($matches) {
        // Strip remaining HTML tags but keep the converted markdown
        $content = preg_replace('/<[^>]+>/', '', $matches[1]);
        $lines = explode("\n", trim($content));
        $quoted = array_map(function($line) {
            return '> ' . trim($line);
        }, array_filter($lines, function($line) {
            return trim($line) !== '';
        }));
        return "\n" . implode("\n", $quoted) . "\n";
    }, $html);
    
    // Convert line breaks
    $html = preg_replace('/<br\s*\/?>/is', "\n", $html);
    
    // Convert paragraphs
    $html = preg_replace('/<p[^>]*>(.*?)<\/p>/is', "$1\n\n", $html);
    
    // Convert horizontal rules
    $html = preg_replace('/<hr[^>]*\/?>/is', "\n---\n", $html);
    
    // Remove remaining HTML tags
    $html = strip_tags($html);
    
    // Decode HTML entities
    $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Clean up multiple newlines
    $html = preg_replace("/\n{3,}/", "\n\n", $html);
    
    // Trim whitespace
    $html = trim($html);
    
    return $html;
}
