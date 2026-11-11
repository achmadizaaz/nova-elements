=== Nova Elements ===
Requires at least: 6.5
Requires PHP: 8.2
Stable tag: 0.3.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Design-focused Elementor widgets with scoped styles and a widget manager.

== Description ==

Nova Elements includes Advanced Heading, Blog Grid, Feature Card, Advanced Button, and Testimonials. Each widget offers five layouts in a visual Elementor picker. Global Styles manages site-wide themes and design tokens. Elementor 3.20 or later is required. Deleting the plugin preserves Nova Elements settings.

== Installation ==

1. From source, run composer install --no-dev --optimize-autoloader. A built distribution includes the resulting vendor directory and production assets.
2. Place the plugin folder in wp-content/plugins/nova-elements.
3. Install and activate Elementor, then activate Nova Elements.
4. Open Nova Elements in wp-admin. In the Elementor widget panel, look for Advanced Heading under Nova Elements.

== Changelog ==

= 0.3.0 =
* Add a keyboard-accessible Visual Style Picker with thumbnails of the five widget layouts.
* Add independent global themes and reusable colors, typography, spacing, component, and motion settings.
* Preserve existing Elementor layout selections and Nova palette overrides.

= 0.2.0 =
* Add Blog Grid with taxonomy filtering and pagination.
* Add Feature Card, Advanced Button, and Testimonials with a native scrolling carousel.

= 0.1.0 =
* Introduce the plugin foundation, registry, dashboard, scoped palette and Advanced Heading.
