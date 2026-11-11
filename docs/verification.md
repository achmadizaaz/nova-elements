# First milestone verification

## Results recorded on 2026-11-11

| Environment | Results |
| --- | --- |
| PHP 8.3.17 and Node.js | PHP lint, strict Composer validation and PSR-4 generation, nine standalone checks, SCSS/JS production build, and JavaScript syntax checks passed. |
| WordPress 7.1.4, Elementor 4.4.0, PHP 8.3.17 | WordPress refused normal activation before Elementor. Activation succeeded with Elementor, and Elementor registered Advanced Heading and its controls. A forced missing dependency left WordPress usable. Deactivation, WordPress's uninstall callback, and reactivation preserved saved settings. |
| WordPress 6.5, Elementor 3.20.0, PHP 8.3.17 | Activation, reactivation, registration, control discovery, and rendering six heading instances with the widget stylesheet passed. In the live editor, editing the title and preset updated the preview and HTML-like input was rendered as text. Elementor's document save method persisted the changed title and preset, which rendered on a fresh request. The browser publish click timed out in this harness. |
| Chromium on the WordPress 7.1.4 site | Dashboard counts, search, category filtering, bulk actions, saved enablement, widget removal and restoration, preset and palette saves, and all five layouts passed. WordPress rejected a save without a nonce and kept the existing setting. At 375px, the dashboard and frontend had no horizontal overflow and Creative stacked its columns. Scoped axe WCAG 2.0/2.1 A/AA checks returned zero violations for the tested dashboard and heading views. |
| Assets | A page with six heading instances loaded one heading stylesheet and one token stylesheet, totaling 3,923 bytes before transfer compression. The test observed no Nova frontend script and no Nova assets on an unrelated page or wp-admin screen. |

The runtime checks used disposable WordPress sites with SQLite. Chromium received live PHP CGI responses through Playwright request routing because direct loopback connections timed out. WordPress update requests and external Elementor resources were restricted during the tests. The browser logged blocked external Elementor requests and an Elementor 3.20 style-guide script exception. The PHP log contained no Nova Elements file errors during the recorded checks.

The Elementor 4.4 preview iframe rendered all six headings. The editor panel stayed on its loading screen in this setup. The same loading screen persisted on a blank page with Nova Elements deactivated. Interactive editing was verified on Elementor 3.20. PHP 8.2, WooCommerce, production profiling, other browsers, and manual assistive-technology checks remain outside this record.

Phase 5 identified gzip decoding in the disposable Chromium harness as the cause of the earlier loading issue. See [the Phase 5 results](phase-5.md#verification-record) for the interactive Elementor 4.4 check.

## Test procedure

Run the source checks in [README](../README.md). On a disposable site, enable `WP_DEBUG` and `WP_DEBUG_LOG` and record the WordPress, Elementor, and PHP versions.

1. Activate Elementor, then Nova Elements. Open the dashboard. Confirm one available widget, one active widget, five presets, and one category. Verify the reported Elementor version in System Status.
2. Use the Widget Manager search and category selector, including an empty search result. Use Disable All, save, and confirm the toggle persists and the active count falls to zero. Re-enable with Enable All and save again.
3. Create an Elementor page. Insert Advanced Heading and change the heading tag, title, optional subtitle, preset, and text fill. Set responsive alignment and inspect the editor preview. Duplicate the widget and give the two copies different presets and colors. Publish and check the public page.
4. Inspect each preset in desktop and mobile views. Check logical reading order, text contrast, long-text wrapping, and keyboard focus in the dashboard. At a narrow viewport, confirm the Creative columns stack and the dashboard remains usable.
5. Open a page without any Nova widget. Check the network or rendered source for the absence of Nova frontend styles and scripts. A page with multiple headings should include each Nova style handle once and load no Nova JavaScript. An unrelated wp-admin screen should load no Nova assets.
6. Set a valid palette override and change the site preset. Confirm a heading set to Site default updates while an explicitly selected preset remains selected. Confirm a non-admin cannot open or submit the settings and that a save without a valid nonce is rejected.
7. Disable the widget and check that its editor entry and published markup disappear. Re-enable it and confirm the published settings reappear. Deactivate Nova Elements and reactivate it to verify the saved settings persist. Test missing Elementor on a disposable copy. Core's dependency screen may prevent deactivation while Nova Elements is active.
8. Delete Nova Elements on the disposable site. Reinstall and verify that its site preferences persist.

Repeat on the minimum and current supported combinations before treating the compatibility matrix as fully verified.
