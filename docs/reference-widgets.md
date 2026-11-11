# Reference widgets

## Preset compositions

| Preset | Blog Grid | Feature Card | Advanced Button | Testimonials |
| --- | --- | --- | --- | --- |
| Minimal Editorial | Serif titles under an image, with a bottom-aligned metadata row | Wide media over a serif title and a ruled card | Serif text with a hairline and an end arrow | Serif quotation with a fine vertical rule and attribution below |
| Modern Corporate | Bordered image cards with inset content and a taxonomy pill | Compact lead media with a left accent rail | Solid rectangular control with a divided icon section | Author-first card with a separator above the quote |
| Premium Gradient | A wide first story followed by smaller supporting cards | Centered content and raised media inside a gradient panel | Gradient pill with a circular inset icon | Centered quote with a medallion and stacked attribution |
| Bold Creative | Full-bleed image cards with bottom-aligned content overlays | Oversized type and media on the opposite side at wider widths | Oversized label with a separate icon tile and offset edge | Oversized quote and an offset quotation mark over an accent surface |
| Elegant Dark | Side-by-side image and copy, with ruled metadata | Framed text with a separate media strip below | Inset dark control with leading icon and a serif label | Framed quote and separate attribution band |

## Content and style

- **Blog Grid.** Select a public post type, optional public taxonomy and term IDs, sort order, include and exclude lists, and page size. Term IDs are visible in a term's edit URL as `tag_ID`. Offset applies with pagination off. The query uses published, unprotected posts and bounds page size to 24. Each instance uses its own `ne_page_<element-id>` parameter. Style controls cover responsive columns and gaps, card surface, image ratio, typography, and pagination.
- **Feature Card.** Configure the title and HTML tag, description, optional label, media, and CTA. The image uses WordPress media alt text when available. Icons are decorative. The CTA appears once both link text and a valid URL are supplied. Style controls cover type, surface, alignment, spacing, icon, and image ratio.
- **Advanced Button.** Configure label, supporting text, URL, optional icon, icon placement, and responsive alignment. A valid URL generates a single anchor. A button without a destination displays as non-interactive text. Style controls cover typography, normal and hover/focus surfaces, spacing, icon, border, radius, and shadow.
- **Testimonials.** Add repeater items with a quote, name, role, portrait, and optional rating. The carousel is a native scroll-snap region with keyboard and touch scrolling. Grid columns and slides per view are responsive. The widget adds no JavaScript. Style controls cover quote, attribution, rating, portrait, card, and spacing.

Color and typography controls can select Elementor Site Settings globals. Site default follows the Nova preset selected in Settings. The four widgets share their PHP renderers between the editor and public page.

## Automated checks

Activate Nova Elements and Elementor on a disposable site. Run the following from the plugin source directory, adjusting the path to that site's WordPress loader.

```powershell
composer test
npm.cmd run build
npm.cmd run check
$novaWordPress = 'D:/path/to/disposable/wordpress/wp-load.php'
foreach ($widget in @('blog', 'feature', 'button', 'testimonials')) {
    php tests/wordpress-widgets.php $novaWordPress $widget
    if ($LASTEXITCODE) { throw "Widget test failed for $widget" }
}
```

The Blog Grid test creates disposable posts and terms and removes its fixtures at the end. The other cases render actual registered Elementor widget instances against malformed settings and all five presets.

## Manual checks

1. Open the Nova dashboard and confirm the clean-install totals are five available and active widgets, 25 presets, and four categories. Disable one widget, save, and verify that the other four remain registered.
2. On an Elementor page with at least four published posts with featured images, insert two Blog Grid instances. Filter one by term, use different column counts at each breakpoint, toggle author and excerpt, and open page two. Confirm the other grid remains on page one. Try a filter with no matches.
3. Duplicate Feature Card five times and select each preset. Switch media between icon and a WordPress attachment with alt text. Add a CTA URL, test its focus style and new-tab behavior, and verify that removing the URL removes the anchor.
4. Insert Advanced Button, set an actual destination, and test keyboard navigation and activation. Try custom icons on both sides, a classic fill on Premium Gradient, and custom hover/focus colors. Verify full-width mobile alignment and a long wrapped label.
5. Add and reorder testimonial repeater items. Try a portrait, a zero rating, and a five-star rating. Switch between grid and carousel. On a narrow viewport swipe or scroll the track. With keyboard focus on the named region, use the arrow keys. Verify that a second carousel scrolls independently.
6. Compare all five presets for each widget in the editor and on the public page at desktop, tablet, and mobile widths. Verify a custom global color and typography setting, RTL, reduced motion, and focus visibility. On a page containing only one reference widget, confirm the browser loads that widget's stylesheet and shared tokens once.

## Verification record

The 2026-11-11 checks used disposable WordPress sites and PHP 8.3.17. The foundation recheck covered heading rendering, dashboard state, and rejection of a settings save without a nonce.

| Widget | Automated integration checks | Chromium checks |
| --- | --- | --- |
| Blog Grid | Public and custom post types, valid and invalid taxonomy selections, excludes, offsets, excerpt and metadata, and independent pagination | Featured images with responsive sources, all five compositions, and the Next link changing only its grid |
| Feature Card | Five presets, escaped content, allowed tags, safe new-tab attributes, and rejection of invalid image and link URLs | Image and icon modes, all five media arrangements, and compact spacing inside a narrow Elementor flex container |
| Advanced Button | Five presets, one semantic link, escaped labels, icon positions, and plain-text fallback | Different shapes and icon positions, plus Elementor Global Color, Typography, classic fill, and mobile full-width alignment |
| Testimonials | Five presets, skipped empty quotes, safe portraits, bounded ratings, and a named keyboard viewport | Five grid treatments and a carousel that scrolls by keyboard and fits one slide at mobile width |

All four PHP cases passed on WordPress 6.5 with Elementor 3.20.0 and WordPress 7.1.4 with Elementor 4.4.0. Chromium verified 1440, 768, and 375 px layouts on the minimum-version site. Scoped axe checks reported zero violations for the tested frontend views. Each widget page loaded its stylesheet and shared tokens once, with no Nova frontend JavaScript. All four Elementor 3.20 preview iframes loaded the expected styles, and a Content control was opened for each widget.

Interactive editor testing covered Elementor 3.20. Publishing through the browser, PHP 8.2, other browsers, and manual screen-reader testing were outside this run. See the [foundation verification](verification.md) for the Elementor 4.4 editor loading behavior in the same test harness.
