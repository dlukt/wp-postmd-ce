# WP Post to Markdown - Classic Editor

WordPress plugin that converts classic editor posts into markdown documents with alternate link meta tags for AI-friendly content consumption.

## Features

- ✅ Automatically adds `<link rel="alternate" type="text/markdown">` meta tags to published posts
- ✅ Serves markdown versions of posts at URLs ending with `.md`
- ✅ Converts HTML content to clean, readable markdown
- ✅ Includes post metadata (author, date, categories, tags)
- ✅ Preserves formatting (headings, bold, italic, links, images, lists, code blocks)
- ✅ Compatible with WordPress 6.9 and later
- ✅ Lightweight with no external dependencies

## How It Works

1. When you publish a post, the plugin adds a link tag to the post's HTML head pointing to the markdown version
2. Visitors (including AI crawlers) can access the markdown version by appending `.md` to the post slug
3. The markdown version includes full post metadata and properly formatted content

## Example

If your post URL is: `https://yourwebsite.com/my-awesome-post/`

The markdown version will be available at: `https://yourwebsite.com/my-awesome-post.md`

And the HTML head will include:
```html
<link rel="alternate" type="text/markdown" title="Markdown Version" href="https://yourwebsite.com/my-awesome-post.md">
```

## Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/wp-postmd-ce` directory
3. Activate the plugin through the WordPress admin panel
4. That's it! No configuration needed

## Requirements

- WordPress 6.0 or higher (tested up to 6.9)
- PHP 7.4 or higher

## License

MIT License - see [LICENSE](LICENSE) file for details

## Author

Darko Luketic - [GitHub](https://github.com/dlukt)
