=== WP Post to Markdown - Classic Editor ===
Contributors: dlukt
Tags: markdown, classic editor, AI, content, export
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Converts classic editor posts into markdown documents with alternate link meta tags for AI-friendly content consumption.

== Description ==

WP Post to Markdown - Classic Editor is a lightweight WordPress plugin that automatically converts your classic editor posts into markdown format, making them easily accessible for AI systems and markdown readers.

**Key Features:**

* Automatically adds `<link rel="alternate" type="text/markdown">` meta tags to published posts
* Serves markdown versions of posts at URLs ending with `.md`
* Converts HTML content to clean, readable markdown
* Includes post metadata (author, date, categories, tags)
* Preserves formatting (headings, bold, italic, links, images, lists, code blocks)
* Compatible with WordPress 6.9 and later
* Lightweight with no external dependencies

**How It Works:**

1. When you publish a post, the plugin adds a link tag to the post's HTML head pointing to the markdown version
2. Visitors (including AI crawlers) can access the markdown version by appending `.md` to the post slug
3. The markdown version includes full post metadata and properly formatted content

**Example:**

If your post URL is: `https://yourwebsite.com/my-awesome-post/`

The markdown version will be available at: `https://yourwebsite.com/my-awesome-post.md`

And the HTML head will include:
```
<link rel="alternate" type="text/markdown" title="Markdown Version" href="https://yourwebsite.com/my-awesome-post.md">
```

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-postmd-ce` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. That's it! The plugin works automatically with no configuration needed.

== Frequently Asked Questions ==

= Does this work with the block editor (Gutenberg)? =

This plugin is designed for classic editor posts. Block editor support may be added in a future version.

= Will this affect my site's performance? =

No. The markdown conversion only happens when the `.md` URL is requested. Regular page views are not affected.

= Can I customize the markdown output? =

Currently, the plugin provides a sensible default markdown format. Custom formatting options may be added in future versions.

= Does this work with custom post types? =

Currently, the plugin only works with standard WordPress posts. Custom post type support may be added in a future version.

== Changelog ==

= 1.0.0 =
* Initial release
* Automatic markdown link tag generation
* HTML to markdown conversion
* WordPress 6.9 compatibility
