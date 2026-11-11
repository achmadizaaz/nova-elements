# Nova Elements

Nova Elements is a WordPress plugin for Elementor. It ships Advanced Heading, Blog Grid, Feature Card, Advanced Button, and Testimonials with five visual layouts each, a widget manager, and global design themes. See [architecture](docs/architecture.md), the [reference widget guide](docs/reference-widgets.md), and the [design system and picker guide](docs/phase-5.md).

## Setup

1. Use WordPress 6.5+, PHP 8.2+, and Elementor 3.20+. These are the plugin's minimum gates. See the verification record for tested versions.
2. Place this directory at `wp-content/plugins/nova-elements` in a disposable WordPress installation for development.
3. From the plugin directory run `composer install --no-dev --optimize-autoloader`. The minified assets are in the source tree. To rebuild them, run `npm ci` followed by `npm run build`.
4. Activate Elementor, then Nova Elements. Open the Nova Elements dashboard and insert Advanced Heading into an Elementor page.

For a distribution, include `nova-elements.php`, `uninstall.php`, `readme.txt`, `LICENSE`, `includes/`, `templates/`, Composer's generated `vendor/`, and `assets/` with the sources and compiled files. Package them under one `nova-elements/` directory.

## Structure

| Directory | Responsibility |
| --- | --- |
| `includes/Core` | Bootstrap, compatibility gates, settings, design tokens, registry, widget and asset registration |
| `includes/Admin`, `templates/admin` | Admin hooks, settings and dashboard views |
| `includes/Widgets`, `templates/widgets` | Elementor controls and escaped frontend rendering |
| `assets/scss`, `assets/js`, `assets/css` | Scoped styles, admin behavior and production assets |
| `tests` | Standalone checks and widget integration tests for a disposable WordPress installation |
| `docs` | Design reference, architecture and verification |

Add a real widget definition in `WidgetRegistry` with a stable ID matching Elementor's `get_name()`. Declare asset handles in the widget and register them through the manager. Keep production assets synchronized with their sources. Changes to existing IDs or control keys require migration planning because Elementor stores them in documents.

The **Visual Style Picker** is on the Style tab. **Site default** follows the layout selected in Nova Elements → Settings. The theme in Nova Elements → Global Styles supplies colors, typography, spacing, component styling, and motion. Widgets are enabled on first installation. Disabling one stops registration and suppresses its rendering on existing pages. Nova clears Elementor's generated files and data on widget enablement changes. Purge any external full-page cache after changing widget availability or styles. Deactivation and uninstall preserve site settings.

## Checks

```powershell
composer validate --strict
composer test
Get-ChildItem -Recurse -Filter *.php -File | Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\' } | ForEach-Object { php -l $_.FullName; if ($LASTEXITCODE) { throw "PHP lint failed" } }
npm run build
npm run check
```

Follow the [foundation checklist](docs/verification.md) and the [reference widget checklist](docs/reference-widgets.md) for WordPress and browser checks.
