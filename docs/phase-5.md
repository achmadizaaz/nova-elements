# Phase 5 design system and visual picker

## Audit before implementation

- `WidgetRegistry` lists Advanced Heading, Blog Grid, Feature Card, Advanced Button, and Testimonials. The five composition keys are `editorial`, `corporate`, `gradient`, `creative`, and `dark`. Each widget already has distinct scoped CSS for these compositions.
- All five widgets use `BaseWidget` for the `style_preset` dropdown and resolver. Elementor documents store the scalar key. The special value `site` uses the widget layout default stored in `nova_elements_preferences`.
- `Assets` registers each widget stylesheet conditionally, with one shared token stylesheet. The CSS palette and PHP color defaults overlap. Typography, spacing, neutral colors, shadows, and motion are still declared in individual stylesheets.
- The Global Styles page writes `nova_elements_palette` via the Settings API. Existing per-widget Elementor color and typography controls already support Site Settings globals. The dashboard's current miniature heading preview uses the real heading template.
- Composer smoke checks, disposable WordPress integration tests, and Chromium fixtures are available. Advanced Heading has a client preview template. The remaining widgets use Elementor's PHP rendering path in the editor.
- Elementor 3.20 provides the documented custom data control API. Its native Visual Choice control is absent. Elementor 4.4 includes Visual Choice, introduced in 3.28.

## Design decisions

Use the existing token handle and add a validated global theme option. Keep the palette option and the widget layout default independent. The default theme selection preserves the original palette. New global choices update CSS variables without rewriting Elementor documents or Elementor's active Kit.

Keep the stored `style_preset` key and its six existing values. Add widget-specific metadata and local WebP captures of the existing layouts. Register a custom data control with native radio inputs and Elementor's documented template binding. The same implementation can run on the minimum version. The picker belongs on the widget's Style tab.

Keep token definitions scoped to Nova. Use Elementor's generated instance selectors for per-widget colors, fonts and responsive overrides. Use shared fluid spacing for global density and leave the editor's breakpoint definitions with Elementor.

## Global style behavior

Choose a design theme at **Nova Elements → Global Styles**. The original palette remains the initial selection. The five named themes bundle a palette with typography scale, spacing, radius, and optional shadow defaults. Colors, fonts, component treatment, density, and motion can then be overridden individually. The site's layout default remains under **Nova Elements → Settings**.

1. Elementor's generated instance selectors take precedence, including device controls and the widget's Elementor Global Color or Typography references.
2. The selected layout class owns composition and preset-specific treatments such as image overlays and dark text surfaces. Eligible properties use shared Nova tokens with the layout's original values as defaults.
3. Explicit Nova settings override the selected global theme. The compiled overrides are attached once to the token stylesheet. An untouched installation emits no new inline style.
4. With color inheritance enabled, Elementor's Kit supplies colors for roles without a theme color or custom value. Font inheritance fills unassigned families with Elementor Global Fonts. All inheritance uses CSS variable fallbacks and preserves the Kit.
5. Unassigned properties use the original Nova tokens, preset defaults, or inherited theme fonts.

The component controls style button radius and shadow and add an outline or raised treatment to the three card widgets. Fluid spacing uses viewport-relative CSS. Per-instance responsive controls can override the resulting gaps. Motion uses the operating system preference. Instant feedback and always-reduced motion additionally set Nova's hover duration to zero.

## Layout assets and compatibility

`StylePresetRegistry` supplies the label, description, stable key, and local thumbnail for each composition. The Style tab includes five layout cards and **Site default**. That sixth choice reuses the current site layout's thumbnail. A bundled SVG covers missing WebP files. Images are requested only when the editor renders the picker.

The 25 WebP thumbnails were captured from the actual five widgets on disposable Elementor pages. Blog Grid and Feature Card previews use the Twenty Twenty-Four theme's sample photography. Capture an actual widget rendered with the Original Nova palette when adding or updating a thumbnail. Resize its screenshot to fit within 304 × 184 px on a 320 × 200 px canvas, then encode to WebP. Store it at `assets/presets/<widget-slug>/<layout-key>.webp`.

The picker uses Elementor's documented custom data control and native radio input binding. There is no picker script or editor asset on public pages. Elementor keeps the `style_preset` key, its stored scalar values, and the existing instance controls. The additional global settings are stored independently in `nova_elements_global_style`. There is no database migration.

## Files

- Design system and settings in `includes/Core/DesignSystem.php`, `Settings.php`, and `Assets.php`.
- Preset registration in `includes/Core/StylePresetRegistry.php`, `WidgetRegistry.php`, and `Plugin.php`.
- Elementor control in `includes/Controls/VisualStylePicker.php`, with its integration in the five widget classes and shared `BaseWidget.php`.
- Global Styles form in `templates/admin/global-styles.php`, styled by `assets/scss/admin.scss`.
- Editor and widget styling in `assets/scss` and generated `assets/css`. Thumbnails and the fallback are in `assets/presets`.
- Test additions in `tests/smoke.php` and `tests/wordpress-widgets.php`.

## Checks

```powershell
composer validate --strict
composer test
npm.cmd run build
npm.cmd run check
$novaWordPress = 'D:/path/to/disposable/wordpress/wp-load.php'
foreach ($case in @('presets', 'blog', 'feature', 'button', 'testimonials')) {
    php tests/wordpress-widgets.php $novaWordPress $case
    if ($LASTEXITCODE) { throw "Widget test failed for $case" }
}
```

To repeat the editor checks, open a page saved with the previous release. Select a widget and open **Style → Visual Style Picker**. Check the selected card, change it with the mouse and arrow keys, undo the change in Elementor history, then save and reopen the page. Check the public markup and the previously customized widget styles. In Global Styles, save a theme and a custom primary color. Inspect a widget with an explicit layout and a per-widget Global Color reference. Repeat on a narrow viewport and check the keyboard focus indicator. Use an account without `manage_options` to verify that it cannot open or save the settings.

## Verification record

| Area | Observed result |
| --- | --- |
| Source checks | Composer validation, strict PSR-4 generation, PHP lint, 18 smoke assertions, Sass build, and JavaScript syntax checks passed. |
| WordPress integration | The five suites passed on WordPress 6.5 with Elementor 3.20.0 and WordPress 7.1.4 with Elementor 4.4.0, both using PHP 8.3.17. Each suite run recorded 120 assertions. |
| Elementor 3.20 editor | All five pickers restored the previously saved choice, loaded only five distinct thumbnails for the active widget, and updated the preview by mouse and keyboard without changing the displayed content. Advanced Heading also saved through the editor and restored the selected card on a fresh editor load. |
| Elementor 4.4 editor | Advanced Heading opened with its existing selection and switched layouts by mouse and keyboard. Correct gzip decoding in the disposable Chromium harness allowed the editor to finish loading. |
| Global Styles | A saved theme changed the frontend tokens while the widget layout stayed independent. Chromium confirmed Elementor color and font inheritance and preservation of instance styles, including an existing Elementor Global Color and Global Typography reference. |
| Responsive and accessibility | The Global Styles form fit 375 px and passed its scoped axe check. Blog Grid, Feature Card, Advanced Button, and Testimonials passed the 1440, 768, and 375 px frontend checks and scoped axe tests with Elegant Dark enabled. |
| Security and assets | A settings POST without a nonce was rejected. A signed-in subscriber could neither open the settings page nor submit it. Public Heading pages requested no picker stylesheet or thumbnail. The editor picker CSS is 2,160 bytes before compression, and all 25 local WebPs total 121,152 bytes. |

The additional browser run for Elementor Undo, the wide three-column picker, picker-specific axe, and Widget Manager disable and re-enable did not start because automatic approval review reached its usage limit. The standalone disabled-registration check passed. PHP 8.2, other browser engines, and manual screen-reader testing remain unverified.

## Platform references

- [Elementor custom control registration](https://developers.elementor.com/docs/controls/add-new-control/)
- [Elementor control templates](https://developers.elementor.com/docs/controls/control-template/)
- [Elementor global style controls](https://developers.elementor.com/docs/editor-controls/global-style/)
- [Elementor instance selectors](https://developers.elementor.com/docs/editor-controls/selectors/)
