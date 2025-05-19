# Custom Portfolio Plugin

Adds a Portfolio custom post type with Advanced Custom Fields (ACF) integration and a shortcode to display portfolio items.

## Features
- Registers a Portfolio custom post type.
- Adds custom fields using ACF for client name, project URL, completion date, gallery, and technologies used.
- Provides a `[portfolio_items]` shortcode to display portfolio items on any page or post.
- Adds admin columns for easy management of portfolio details.

## Requirements
- WordPress 5.0+
- [Advanced Custom Fields (ACF) plugin](https://wordpress.org/plugins/advanced-custom-fields/) (must be installed and activated)

## Installation
1. Upload the plugin ZIP folder via WordPress **Plugins > Add New > Upload Plugin**.
2. Activate the plugin.
3. Make sure ACF plugin is activated.
4. Add portfolio items via the new "Portfolio" menu.
5. Use the shortcode `[portfolio_items]` to display portfolio on the front end.

## Notes
- The plugin depends on ACF; without it, the custom fields will not work.
