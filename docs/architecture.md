# Nova Elements architecture

## First milestone

The existing dashboard reference defines the visual direction. The live registry defines widget, category, and preset totals. The registry contains Advanced Heading and four additional reference widgets. The target catalogue in `AGENTS.md` remains the expansion roadmap.

| Area | Design |
| --- | --- |
| Bootstrap | WordPress loads the entry file, which checks PHP and WordPress, then loads Composer. Admin remains available when Elementor is unavailable. |
| Compatibility | Proposed minimums are PHP 8.2, WordPress 6.5, and Elementor 3.20. Validate the minimum combination and a current combination before expanding the catalogue. WooCommerce status is diagnostic for this milestone. |
| Registry | Explicit, keyed definitions supply titles, categories, preset IDs, requirements, asset handles, and widget classes. Definitions can be read without loading Elementor widget classes. |
| Editor | Register the Nova Elements category and enabled widgets via public Elementor hooks. Advanced Heading uses a live preview template. The other four reference widgets use the same PHP templates for preview and frontend. Style controls use Elementor selectors scoped per instance. |
| Assets | Register enabled widget handles when Elementor registers widgets. Elementor enqueues dependencies for instances it renders. Share one token stylesheet. Admin assets load on Nova screens only. |
| Cache | On activation and widget availability writes, clear generated Elementor files and data through the public files manager operation used by Elementor's documented `flush-css` command. This prevents cached documents from retaining missing widgets. Nova Elements adds no cache of its own for headings. |
| Admin | Dashboard and Widget Manager share the registry and save enablement with the Settings API. Global Styles owns token overrides. Integrations and System Status report observed dependencies. |
| Security | Use capability checks and Settings API nonces, validate registry IDs and colors, allowlist tags and presets, and escape at the template boundary. No external requests are required during plugin use. |
| Verification | Lint and build, then exercise a disposable WordPress installation. Check activation, missing dependencies, option writes, disabled registration, frontend assets, editor preview, accessibility, and mobile layouts. |

## Advanced Heading presets

| Preset | Composition |
| --- | --- |
| Minimal Editorial | Serif headline above a lead paragraph, with a fine rule and generous vertical spacing. |
| Modern Corporate | Compact heading and supporting text grouped beside a full-height accent rail. |
| Premium Gradient | Centered display headline and narrow lead on a layered gradient surface. |
| Bold Creative | Large offset headline and a separate subtitle column, stacked on narrow screens. |
| Elegant Dark | Inset bordered frame with a prominent headline and a separate, ruled subtitle row. |

The heading supports an optional subtitle, semantic tag, responsive alignment and spacing, individual typography, and solid or gradient title color. The base CSS applies token defaults only within Nova widgets. Global Styles can override the plugin palette, and Elementor color and typography controls can use Site Settings globals.

## Platform references

- [WordPress plugin headers and dependencies](https://developer.wordpress.org/plugins/plugin-basics/header-requirements/)
- [WordPress post queries](https://developer.wordpress.org/reference/classes/wp_query/)
- [Elementor widget registration](https://developers.elementor.com/docs/widgets/add-new-widget/)
- [Elementor widget dependencies](https://developers.elementor.com/docs/widgets/widget-dependencies/)
- [Elementor global styles in controls](https://developers.elementor.com/docs/editor-controls/global-style/)
- [Elementor PHP and editor rendering](https://developers.elementor.com/docs/widgets/widget-rendering/)
- [Elementor repeater controls](https://developers.elementor.com/docs/editor-controls/control-repeater/)
- [Elementor cache flushing](https://developers.elementor.com/docs/cli/flush-css) and [the official implementation](https://github.com/elementor/elementor/blob/main/modules/wp-cli/command.php)

## Expansion

Blog Grid establishes the shared `WP_Query` contract. It works with explicit post objects to preserve the page's global post context and namespaces pagination by Elementor element ID. Feature Card, Advanced Button, and Testimonials share the base widget's typography, background, spacing, link, and media helpers where applicable. The reference widgets are the basis for the next category expansion after visual and editor verification.
