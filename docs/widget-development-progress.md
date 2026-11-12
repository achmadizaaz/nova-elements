# Nova Elements widget development progress

## Repository audit

Phase 8A starts from product checkout `4b4b625adbac1fb68cfd6bbbcaadbcdf16e9cd36` plus the existing local Phase 6 and Phase 7 changes. The Widget Registry previously contained Advanced Heading, Blog Grid, Feature Card, Advanced Button, and Testimonials. All five use the shared Visual Style Picker and Global Design System. Blog Grid was the only post-query widget. The Template Library imports native Elementor documents. Phase 7 provides public post querying and reusable query controls. Field mapping, provider adapters, AJAX filtering and saved query presets remain open.

| Batch 8A widget | Status at start | Dependencies | Presets | Query integration | Verification |
| --- | --- | --- | --- | --- | --- |
| Blog List | Missing | Elementor Free | Five | Shared `PostQuery` and `QueryControls` | WordPress, native import, desktop/mobile Chrome |
| Blog Masonry | Missing | Elementor Free | Five | Shared `PostQuery` and `QueryControls` | WordPress, native import, desktop/mobile Chrome |
| Blog Carousel | Missing | Elementor Free | Five | Shared `PostQuery` and `QueryControls` | WordPress, native import, desktop/mobile Chrome, keyboard and rotation |
| Featured Posts | Missing | Elementor Free | Five in iteration 2 | Shared `PostQuery` and `QueryControls` | See iteration 2 |
| Magazine Layout | Missing | Elementor Free | Five in iteration 2 | Shared `PostQuery` and `QueryControls` | See iteration 2 |
| Post Timeline | Missing | Elementor Free | Five in iteration 2 | Shared `PostQuery` and `QueryControls` | See iteration 2 |
| Related Posts | Missing | Elementor Free | Five in iteration 3 | Shared contextual post query | See iteration 3 |
| Post Categories | Missing | Elementor Free | Five in iteration 3 | WordPress public taxonomy API | See iteration 3 |
| Author Profile | Missing | Elementor Free | Five in iteration 3 | WordPress author context and shared public post query | See iteration 3 |
| Advanced Post Meta | Missing | Elementor Free | Five in iteration 4 | Public WordPress post context | See iteration 4 |

## Iteration 1 — Blog List, Blog Masonry, and Blog Carousel

The widgets share query settings, card preparation, WordPress pagination, and Elementor text, panel, spacing and image controls with Blog Grid. List adapts the image and copy at a container breakpoint. Masonry uses CSS columns and reads down each column in source order. Masonry and Carousel expose responsive column and slide counts. The original Blog Grid widget ID and saved control keys still work.

Carousel uses a native horizontal scroll area with scroll snapping. Optional buttons and page selectors act on their own instance. Autoplay defaults off. When enabled, it has a 4–20 second interval, pauses on hover, stops on focus or direct interaction, and honors reduced motion. One-result collections render the article by itself. The conditional script is a custom element that removes its observers, events and timer when detached. Post query pagination remains separate from slide navigation.

| Preset | Blog List | Blog Masonry | Blog Carousel |
| --- | --- | --- | --- |
| Minimal Editorial | Wide leading media, serif titles and divided rows | Portrait and landscape photos with fine rules | Two photo-led stories with ruled bylines |
| Modern Corporate | Inset rows with trailing thumbnail and category chip | Inset alternating-height photo cards with a separate copy panel | Compact three-up cards with controls above |
| Premium Gradient | Full-width lead photo and compact supporting rows | Mixed-height photography with overlapping copy panels | One feature with side-by-side image and copy |
| Bold Creative | Alternating media placement and a prominent accent rule | Full-bleed collage with alternating overlay positions | Two full-bleed covers with overlaid titles |
| Elegant Dark | Framed compact thumbnails with divided metadata | Framed portraits and a dark metadata band | Wide split slides with framed media |

All 15 picker thumbnails are 320 × 200 WebP captures of the actual rendered widgets. Preview posts use the sample photography bundled with Twenty Twenty-Four, following the existing Phase 5 capture process. [Twenty Twenty-Four image credits](https://themes.trac.wordpress.org/browser/twentytwentyfour/1.0/readme.txt)

### Files

- New widget classes in `includes/Widgets/Blog/` and shared `PostCollectionWidget.php`. The existing `BlogGridWidget.php` now delegates to the shared implementation.
- Widget, preset, asset and dependency changes in `includes/Core/WidgetRegistry.php`, `StylePresetRegistry.php`, `Assets.php` and `WidgetManager.php`.
- Shared rendering in `templates/widgets/blog-grid.php` and carousel controls in `templates/widgets/blog-carousel-controls.php`.
- Scoped stylesheet in `assets/scss/blog-collection.scss` and `assets/css/blog-collection.min.css`, carousel source and compiled script in `assets/js/blog-carousel*.js`, 15 WebP files under `assets/presets/blog-{list,masonry,carousel}/`, and build/check updates in `package.json`.
- Native import settings in `includes/TemplateLibrary/TemplateContent.php`. Tests in `tests/smoke.php`, `tests/wordpress-widgets.php`, `tests/wordpress-templates.php`, and `tests/browser-blog-carousel.mjs`.

### Verification

The disposable stack used WordPress 7.1.4, Elementor Free 4.4.0, PHP 8.3.17, MySQL 8.4.3, Twenty Twenty-Four, and Chrome 157. Other Nova products and WooCommerce were absent.

```powershell
composer validate --strict
composer test
npm run build
npm run check
php tests/wordpress-widgets.php $wpLoad blog
php tests/wordpress-widgets.php $wpLoad presets
php tests/wordpress-widgets.php $wpLoad feature
php tests/wordpress-widgets.php $wpLoad button
php tests/wordpress-widgets.php $wpLoad testimonials
php tests/wordpress-templates.php $wpLoad
```

Composer validation and smoke tests, PHP lint, the production asset build and JavaScript syntax checks passed. All 11 existing compiled assets matched their originals. The WordPress tests passed for the three new widgets, legacy Blog Grid, preset registration and thumbnails, conditional dependencies, the other reference widgets, and native template imports. The importer round-trip preserved each new widget's preset, query settings, navigation and responsive values.

Chrome rendered all 15 compositions on the frontend. At 390 px, the showcase fit the viewport, list rows stacked, masonry collapsed to one column, and each carousel showed one slide. The carousel regression test passed autoplay, focus stop, Space and Enter navigation, reduced-motion behavior, two-instance isolation and conditional script loading.

To repeat the browser test, create two disposable Elementor pages. On the first, insert two carousels with seven posts each. Enable autoplay with a four-second interval on the first and set two desktop slides. Keep arrows and page buttons enabled. Leave autoplay off on the second carousel. Put a Blog List on the other page. Start a separate Chrome profile with loopback remote debugging enabled, then run with Node 22 or later.

```powershell
node tests/browser-blog-carousel.mjs $twoCarouselPage $listOnlyPage http://127.0.0.1:9228
```

Chrome confirmed that a saved Elementor image ratio overrides the Blog List preset. Interactive editor verification remains open. Elementor 4.4 returned HTTP 200 for the preview iframe but stayed on its loading screen on both the carousel fixture and a blank page in this disposable stack.

Manual screen-reader testing, other browsers and the minimum supported platform versions remain unverified for this iteration.

### Local performance sample

| Fixture | Widgets | DOM ready | Page load | Nova asset requests |
| --- | ---: | ---: | ---: | ---: |
| Showcase | 15 | 2,438 ms | 2,461 ms | 4 |
| Two carousels | 2 | 2,168 ms | 2,209 ms | 4 |
| List only | 1 | 2,226 ms | 2,293 ms | 3 |

These are single Chrome Resource Timing samples on the disposable loopback server with browser cache disabled. Every page requested the shared tokens, base post stylesheet and one collection stylesheet. Carousel pages also requested one script. The collection stylesheet is 9,879 bytes and the carousel script is 4,647 bytes before compression. The 15 previews total 128,672 bytes and load in the editor picker.

## Iteration 2 — Featured Posts, Magazine Layout, and Post Timeline

All three build on the existing post collection widget. The shared query and its filters, supported custom post types, and per-instance server pagination serve the new layouts. The editorial stylesheet is registered only as a dependency of the three new widgets. Each inherits Nova design tokens and has five native Visual Style Picker options.

- **Featured Posts** promotes the first result on each page to the lead. The shared query can select sticky WordPress posts or use explicit post IDs. Supporting excerpts are optional and default off.
- **Magazine Layout** puts the lead beside or above the supporting group according to the preset. Editors can control supporting columns by breakpoint and hide supporting images independently.
- **Post Timeline** presents a semantic ordered list sorted by publication date with ascending or descending direction. Its date label follows the site setting or can display month and year or year only. Labels can be hidden while retaining chronology.

| Preset | Featured Posts | Magazine Layout | Post Timeline |
| --- | --- | --- | --- |
| Minimal Editorial | Wide image above a centered lead, with supporting cards below | Serif lead beside a divided digest | Date column beside the spine and horizontal entries |
| Modern Corporate | Split feature with compact thumbnail rows | Inset lead beside a supporting grid | Inset thumbnail panels with a framed date rail |
| Premium Gradient | Split spotlight with its image trailing the copy | Full-width horizontal lead above tiled stories | Rounded image panels with separate date chips |
| Bold Creative | Full-bleed headline cover above smaller covers | Tall lead cover with staggered supporting overlays | Alternating story covers along a center spine |
| Elegant Dark | Framed lead beside a compact support stack | Balanced split with framed supporting headlines | Dark compact entries with a separate date band |

### Files

- New `FeaturedPostsWidget.php`, `MagazineLayoutWidget.php`, and `PostTimelineWidget.php` in `includes/Widgets/Blog/`.
- Shared controls and rendering in `includes/Controls/QueryControls.php`, `includes/Widgets/Blog/PostCollectionWidget.php`, and `templates/widgets/blog-grid.php`.
- Widget and preset definitions in `includes/Core/WidgetRegistry.php` and `includes/Core/StylePresetRegistry.php`. Native import rules in `includes/TemplateLibrary/TemplateContent.php`.
- Styles in `assets/scss/blog-editorial.scss` and `assets/css/blog-editorial.min.css`, 15 WebP files in `assets/presets/`, and the build entry in `package.json`. The thumbnails use WordPress Twenty Twenty-Four sample photography. [Image credits](https://themes.trac.wordpress.org/browser/twentytwentyfour/1.0/readme.txt)
- Regression coverage in `tests/smoke.php`, `tests/wordpress-widgets.php`, and `tests/wordpress-templates.php`.

### Verification

Test stack: WordPress 7.1.4, Elementor Free 4.4.0, PHP 8.3.17, MySQL 8.4.3, Twenty Twenty-Four, and Chrome 157. The tests used disposable data. No other Nova product or WooCommerce was active.

```powershell
composer validate --strict
composer test
npm run build
npm run check
php tests/wordpress-widgets.php $wpLoad blog
php tests/wordpress-widgets.php $wpLoad presets
php tests/wordpress-widgets.php $wpLoad feature
php tests/wordpress-widgets.php $wpLoad button
php tests/wordpress-widgets.php $wpLoad testimonials
php tests/wordpress-templates.php $wpLoad
```

Composer validation, 44 smoke assertions, PHP lint for 49 files, Sass and JavaScript build, and JavaScript syntax checks passed. The Blog suite passed 158 assertions covering public visibility, escaped titles, independent pagination, Featured sticky selection, timeline chronology, and the 15 preset renders. The other reference widget suites passed. The template suite passed 26 assertions and confirmed all six post-collection variants retain their query, layout and presentation settings after native import.

Chrome captured and visually checked all 15 layouts at 1,240 px. At 390 px the page fit the viewport, the lead and supporting groups stacked, and the timeline used a single full-width column. The public showcase requested the shared tokens, base post stylesheet, and 13,573-byte editorial stylesheet with no Nova JavaScript. The cold loopback page loaded in 7,115 ms in one sample taken while the integration tests were running.

The Elementor metadata suite passed 105 assertions, including widget registration, the timeline direction control, conditional dependencies, and thumbnail dimensions. Chrome also confirmed saved support excerpts in Featured Posts, independent support-image visibility in Magazine Layout, and saved Timeline date formatting and direction. Interactive editor preview remains open. Elementor stayed on its loading screen for both the three-widget fixture and an empty fixture. The 15 thumbnails total 131,846 bytes.

Other browser engines, assistive technology, and the minimum supported platform versions remain unverified for this iteration.

## Iteration 3 — Related Posts, Post Categories, and Author Profile

- **Related Posts** finds public articles in a selected taxonomy shared by the current public article. The existing query excludes the reference and applies the existing filters, order, and per-widget pagination. An explicit public reference post ID supports pages without singular article context.
- **Post Categories** lists public, viewable taxonomy terms from the whole taxonomy or the current public article. Links use WordPress term archives and mark the active archive. Controls cover ordering, a 24-term limit, responsive columns, descriptions, counts, and empty terms. Counts are WordPress taxonomy counts.
- **Author Profile** uses a public article, author archive, or explicit author ID. The explicit ID must have a public, password-free post of the selected type. The card displays the public display name and optional biography, archive and website links. Initials are the default portrait. Avatar provider requests require enabling the widget option and WordPress avatars.

| Preset | Related Posts | Post Categories | Author Profile |
| --- | --- | --- | --- |
| Minimal Editorial | Photo-led serif cards with fine rules | Ruled directory and trailing counts | Compact portrait beside serif biography |
| Modern Corporate | Inset horizontal thumbnails | Inset cards with count badges | Full-height portrait panel and structured copy |
| Premium Gradient | Split lead above supporting cards | Centered tiles with stacked counts | Centered biography and portrait medallion |
| Bold Creative | Full-bleed recommendation covers | Oversized lead category and compact support | Oversized portrait tile with offset biography |
| Elegant Dark | Dark image-and-copy rows | Framed links with a statistics column | Inset dark portrait and separate action band |

### Files

- New classes in `includes/Widgets/Blog/{RelatedPosts,PostCategories,AuthorProfile}Widget.php`. Related Posts reuses `PostCollectionWidget.php` and the query controls in `includes/Controls/QueryControls.php`.
- New views in `templates/widgets/{post-categories,author-profile}.php`. Widget and picker registration in `includes/Core/WidgetRegistry.php` and `StylePresetRegistry.php`.
- Context styles in `assets/scss/blog-context.scss`, `assets/css/blog-context.min.css`, and `package.json`. All 15 local thumbnails are under `assets/presets/`. Related Posts thumbnails use WordPress Twenty Twenty-Four sample photography. [Image credits](https://themes.trac.wordpress.org/browser/twentytwentyfour/1.0/readme.txt)
- Template allowlists in `includes/TemplateLibrary/TemplateContent.php`. Tests in `tests/smoke.php`, `tests/wordpress-widgets.php`, and `tests/wordpress-templates.php`. Explicit reference post and author IDs are site-specific.

### Verification

The disposable stack used WordPress 7.1.4, Elementor Free 4.4.0, PHP 8.3.17, MySQL 8.4.3, Twenty Twenty-Four, and Chrome 157. Other Nova products and WooCommerce were absent.

```powershell
composer validate --strict
composer test
npm run build
npm run check
php tests/wordpress-widgets.php $wpLoad context
php tests/wordpress-widgets.php $wpLoad presets
php tests/wordpress-widgets.php $wpLoad blog
php tests/wordpress-widgets.php $wpLoad feature
php tests/wordpress-widgets.php $wpLoad button
php tests/wordpress-widgets.php $wpLoad testimonials
php tests/wordpress-templates.php $wpLoad
```

Composer, 48 smoke assertions, PHP lint across 54 files, the asset build, and JavaScript syntax checks passed. The context suite passed 60 assertions covering shared terms, reference exclusion, independent pagination, active category archives, internal taxonomies, and public author visibility. The preset suite passed 133 assertions. Existing widget regressions passed. The importer suite passed 29 assertions and confirmed that settings for all nine collection and context variants survive native import.

Chrome verified the 15 frontend renders and single-column layouts at 390 px. A 15-widget public fixture requested three Nova CSS assets and no Nova script. Its new context stylesheet is 10,562 bytes before compression. One local cold navigation completed in 882 ms with Chrome caching disabled. The 15 thumbnails total 94,880 bytes.

The Elementor editor reached the preview frame and stayed on its loading screen on the three-widget fixture, as in earlier iterations. Interactive editor verification, manual screen-reader checks, other browser engines, and the minimum supported platform versions remain open.

## Iteration 4 — Advanced Post Meta

Advanced Post Meta displays an ordered list of up to eight metadata fields from the current public article or a public reference post. Editors can choose the author, publication and update dates, public taxonomy terms, comment count, reading time, or an explicitly permitted custom field. Each row can have its own label. Author, taxonomy, and comment rows can link to their WordPress destinations. Dates use the site format or YYYY-MM-DD, with machine-readable timestamps in the markup. Reading time estimates stored article text using the WordPress locale's counting mode. It uses 220 words or 500 letters and digits per minute and does not execute dynamic content. [WordPress locale counting](https://developer.wordpress.org/reference/functions/wp_get_word_count_type/)

The custom field picker lists registered, single-valued strings and numbers whose owning code explicitly enables public display. To enable a field, attach a callback to `nova_elements_displayable_meta` with two accepted arguments. Return the permitted keys for the supplied post type. The separate query allowlist does not authorize rendering. The widget omits protected keys, nonpublic terms, and values from unpublished or password-protected reference posts. [WordPress registered metadata API](https://developer.wordpress.org/reference/functions/get_registered_meta_keys/)

| Preset | Composition |
| --- | --- |
| Minimal Editorial | Compact serif byline with fine divisions |
| Modern Corporate | Inset grid of metadata tiles with accent rails |
| Premium Gradient | Centered label badges and values on a soft gradient |
| Bold Creative | Oversized lead field above a supporting grid |
| Elegant Dark | Framed panel with paired labels and values |

### Files

- `includes/Widgets/Blog/AdvancedPostMetaWidget.php` and `templates/widgets/advanced-post-meta.php` implement the widget.
- `includes/Widgets/Blog/PostContext.php` provides the public reference check shared with Related Posts through `PostCollectionWidget.php`.
- `includes/Core/WidgetRegistry.php`, `includes/Core/StylePresetRegistry.php`, and `includes/TemplateLibrary/TemplateContent.php` register the widget, presets and native template settings.
- `assets/scss/post-meta.scss`, `assets/css/post-meta.min.css`, and `package.json` provide the conditional stylesheet. Five captured WebP thumbnails are in `assets/presets/advanced-post-meta/`.
- `tests/wordpress-widgets.php`, `tests/wordpress-templates.php`, and `tests/smoke.php` verify the metadata and existing collection behavior.

### Verification

The disposable stack used WordPress 7.1.4, Elementor Free 4.4.0, PHP 8.3.17, MySQL 8.4.3, Twenty Twenty-Four, and Chrome 157. Other Nova products and WooCommerce were absent.

```powershell
composer validate --strict
composer test
npm run build
npm run check
php tests/wordpress-widgets.php $wpLoad meta
php tests/wordpress-widgets.php $wpLoad presets
php tests/wordpress-widgets.php $wpLoad context
php tests/wordpress-widgets.php $wpLoad blog
php tests/wordpress-widgets.php $wpLoad feature
php tests/wordpress-widgets.php $wpLoad button
php tests/wordpress-widgets.php $wpLoad testimonials
php tests/wordpress-templates.php $wpLoad
```

Composer validation, 51 smoke assertions, PHP lint across 57 files, the production build, and JavaScript syntax checks passed. The 15 existing generated assets matched their originals. The metadata suite passed 38 assertions, including site metadata, ISO dates, reading time, custom label escaping, permission boundaries, zero values, and public reference handling. The preset suite passed 143 assertions. Context and collection regressions passed. The importer suite passed 30 assertions and confirmed that settings survive native import for all ten planned Batch 8A widgets.

Chrome rendered all five presets with no horizontal overflow at 390 px and confirmed the dark preset's foreground color. The five-widget public fixture requested two Nova stylesheets and no Nova script. The new stylesheet is 4,790 bytes before compression. A single cold loopback navigation during concurrent tests measured 3,393 ms. The five thumbnails total 10,352 bytes.

The Elementor editor reached the preview frame and stayed on its loading screen. Interactive editing, manual screen-reader checks, other browsers, and the minimum supported platform versions remain unverified for this iteration.

### Batch 8A editor acceptance — 12 November 2026

The fresh fixture used WordPress 7.1.4, Elementor Free 4.4.0, PHP 8.3.17, MySQL 8.4.3, Twenty Twenty-Four, and Chrome 157. WordPress and Elementor ran on loopback with a disposable database. Elementor was activated through WordPress, and the first admin visit consumed its onboarding redirect. The preview then opened with all ten Nova widgets and the local editor REST and AJAX calls succeeded. The loading failure from the earlier fixtures did not recur on the Nova page in this run. The comparison page containing the built-in Heading widget also opened with Nova Elements deactivated in the disposable site. No runtime changes to Nova Elements were needed.

- Opened a Content control and the Visual Style Picker for each of the ten widgets. Each picker showed Site default and all five preset choices.
- Changed the live preset for all ten widgets. Advanced Post Meta switched presets with a native arrow-key action.
- Changed Blog List's post count and Advanced Post Meta's date format from the Content panel and observed both changes in the live preview.
- Published the disposable page, reopened it, and verified all ten saved presets in the editor and on the anonymous frontend. The changed post count and ISO date format also persisted.

To repeat the editor check, use a disposable page containing the ten widgets and public content that satisfies each query. Set an explicit public reference for Related Posts and Advanced Post Meta if the page lacks suitable context. Set Author Profile to a public contributor. Open the editor, select each widget, inspect its Content controls, then change its preset from the Style tab. Publish the fixture, reopen it in Elementor, and check its public page in a signed-out browser.

Other browser engines, manual screen-reader use, and the minimum supported platform versions remain unverified.

### Batch 8A status

All ten planned widgets are implemented. Public rendering, interactive editing, saved template data, and regression tests have coverage on the verified stack. Batch 8B begins below.

## Batch 8B iteration 1 — Info Card, Service Card, and Profile Card

| Widget | Status |
| --- | --- |
| Info Card | Implemented |
| Service Card | Implemented |
| Profile Card | Implemented |
| Team Member Card | Implemented |
| Pricing Card | Implemented |
| Image Box | Implemented |
| Icon Box | Implemented |
| Flip Card | Implemented |
| Hover Card | Implemented |
| Feature Showcase | Implemented |

These cards share the existing Feature Card content and style controls, including optional icon or image media, heading level, description, and CTA. Set the media position to Preset default, Above, Leading, or Trailing. Side-by-side layouts stack on narrow cards. Info Card adds a note. Service Card accepts up to six benefits, one per line. Profile Card supports manually entered information and a public WordPress post as its source. In post mode it reads the title, excerpt, featured image, and permalink. It skips unpublished and password-protected references. Select a suitable reference ID on the destination site when moving a post-sourced profile between sites. Manual profiles with an empty image show decorative initials.

| Preset | Info Card | Service Card | Profile Card |
| --- | --- | --- | --- |
| Minimal Editorial | Serif title and ruled note | Ruled benefits list | Compact circular portrait |
| Modern Corporate | Media column and bordered note | Media beside stacked benefits | Full-height portrait column |
| Premium Gradient | Centered media and inset note | Centered badge and benefit tiles | Centered portrait and compact action |
| Bold Creative | Offset media with large typography | Offset media and asymmetric benefits | Portrait tile beside oversized name |
| Elegant Dark | Dark frame with a divided note | Dark frame and inset benefit panel | Dark frame with a divided action |

Each widget registers five captured WebP thumbnails. Template imports accept the card controls and bundled images for manual cards. The new stylesheet loads only on pages that use a new card, alongside the shared Feature Card stylesheet. This iteration adds no frontend script.

### Files

- The shared and specialized widgets are in `includes/Widgets/Cards/`. The renderer extends `templates/widgets/feature-card.php`.
- `WidgetRegistry.php`, `StylePresetRegistry.php`, and `TemplateContent.php` register the widgets, their presets, and their importable controls.
- `assets/scss/content-cards.scss`, `assets/css/content-cards.min.css`, and `package.json` provide the conditional stylesheet. The 15 WebP files are in `assets/presets/info-card/`, `assets/presets/service-card/`, and `assets/presets/profile-card/`. The Service Card thumbnails show the CC0 `angular-roof.webp` photo from Twenty Twenty-Four. [Twenty Twenty-Four asset credits](https://themes.trac.wordpress.org/browser/twentytwentyfour/1.0/readme.txt)
- `tests/wordpress-widgets.php`, `tests/wordpress-templates.php`, and `tests/smoke.php` cover control registration, rendering, import, escaping, and post visibility.

### Verification

The disposable stack used WordPress 7.1.4, Elementor Free 4.4.0, PHP 8.3.17, MySQL 8.4.3, Twenty Twenty-Four, and Chrome 157. Composer validation and smoke tests, PHP lint across 71 files, the production asset build, and JavaScript syntax checks passed. All 16 existing generated assets matched their originals.

The cards suite passed 55 assertions, the preset suite passed 170, and the importer passed 34. Feature Card, blog, post context, post metadata, button, and testimonials regression suites passed.

Chrome rendered all 15 presets, checked the stacked layout at 390 px, and verified a post-sourced portrait and trailing media. A Feature Card-only page skipped the new stylesheet. The 15-card page used three Nova stylesheets and no Nova scripts. The new stylesheet is 10,049 bytes before compression. The 15 thumbnails total 51,632 bytes. One loopback page load during concurrent tests took 2,737 ms.

In Elementor, changed the Info Card note, the Service Card benefits, and the Profile Card name. Switched all three presets in the live preview, using the keyboard for Profile Card. After publishing, reopened the editor and confirmed that the changed content and presets persisted. Confirmed the same values on the anonymous public page.

Manual screen-reader checks, other browser engines, and the minimum supported WordPress, Elementor, and PHP versions remain unverified for this iteration.

## Batch 8B iteration 2 — Team Member Card, Pricing Card, and Image Box

The three widgets reuse the Feature Card renderer and controls for headings, responsive spacing, typography, colors, image or icon media, safe action links, and media position. Text, images, and links support Elementor Dynamic Tags where enabled by the installed Elementor edition.

- **Team Member Card** adds a role and department. A missing portrait falls back to decorative initials. The contact action can use an email or page link.
- **Pricing Card** accepts a current price, optional previous price, billing period, and up to six included features. Prices are display text to support local formats and labels such as Free. The previous and current prices have distinct accessible labels when both appear.
- **Image Box** pairs a responsive WordPress image with an optional caption in a figure. It preserves the attachment's alternative text and keeps the action link separate from the image.

| Preset | Team Member Card | Pricing Card | Image Box |
| --- | --- | --- | --- |
| Minimal Editorial | Compact portrait and ruled department | Serif rate between horizontal rules | Wide editorial photo and ruled copy |
| Modern Corporate | Portrait column and boxed department | Boxed rate and full-width action | Side-by-side photo and copy |
| Premium Gradient | Centered portrait and role badge | Centered rate and feature tiles | Framed central photo and caption |
| Bold Creative | Offset portrait and oversized name | Oversized rate and two-column features | Trailing photo and bold copy |
| Elegant Dark | Framed portrait and full-width contact | Dark inset rate and outlined features | Inset photo and divided action |

### Files

- `includes/Widgets/Cards/` adds the three widget classes and extends the shared controls. `templates/widgets/feature-card.php` adds the figure and price markup.
- `WidgetRegistry.php`, `StylePresetRegistry.php`, and `TemplateContent.php` register the widgets, five presets each, and supported native template fields. The importer binds local bundled images using the existing asset pipeline.
- `assets/scss/content-cards.scss` and `assets/css/content-cards.min.css` provide the scoped layouts and reuse the existing conditional stylesheet. Fifteen captured previews are in `assets/presets/`. Image Box thumbnails use the CC0 `building-exterior.webp` photo from Twenty Twenty-Four. [Twenty Twenty-Four asset credits](https://themes.trac.wordpress.org/browser/twentytwentyfour/1.0/readme.txt)
- `tests/wordpress-widgets.php` and `tests/wordpress-templates.php` check render, registered controls, unsafe links, escaped text, zero prices, and imported media.

### Verification

Composer validation, 59 smoke assertions, PHP lint across 74 files, the production asset build, and JavaScript syntax checks passed. The 16 unrelated generated assets matched their originals. On WordPress 7.1.4, Elementor Free 4.4.0, PHP 8.3.17, MySQL 8.4.3, and Twenty Twenty-Four, the cards suite passed 94 assertions and native template import passed 35. Feature Card, blog, post context, post metadata, button, and testimonials regressions passed.

The preset suite passed 197 assertions. Chrome 157 rendered all 15 presets at desktop width and checked the stacked layouts and absence of horizontal overflow at 390 px. It verified the Image Box caption and attachment alternative text, the Team Member Card initials, and the dark Pricing Card text. The fixture loaded three Nova stylesheets and no Nova scripts. The shared card stylesheet is 19,130 bytes before compression. A legacy Feature Card-only page omitted that stylesheet. The 15 thumbnails total 61,392 bytes. The final loopback navigation loaded in 894 ms.

In the Elementor editor, changed a team department, a plan price, and an image caption. Switched each preset in the live preview, using the keyboard for Image Box. Published and verified all six changes in anonymous page output and after reopening the editor.

Manual screen-reader checks, other browsers, Elementor Pro Dynamic Tags, and the minimum supported WordPress, Elementor, and PHP versions remain unverified.

## Batch 8B iteration 3 — Icon Box, Flip Card, and Hover Card

The widgets reuse the shared card controls for responsive layout, media position, typography, colors, and safe action links. Elementor Dynamic Tags are available on applicable text, image, and link controls when provided by the installed edition.

- **Icon Box** uses Elementor's icon picker, supports an image alternative, and offers icon sizing and color controls. The default icon is decorative. The visible heading and action convey the card's purpose.
- **Flip Card** has a configurable front, reveal label, and reverse title and description. A native disclosure replaces the front description with the reverse copy when opened. The card's heading and separate action stay available in both states. Opening the reverse uses a short fold animation when motion is allowed. Enter, Space, pointer, and touch use the same control.
- **Hover Card** offers lift, media zoom, accent outline, or no additional effect. Pointer hover and focus within the card can trigger the effect. Content and links are always visible on touch screens. Reduced-motion preference disables the lift and zoom transforms.

| Preset | Icon Box | Flip Card | Hover Card |
| --- | --- | --- | --- |
| Minimal Editorial | Outlined leading icon and serif copy | Ruled front and a compact reverse panel | Panoramic photo and ruled introduction |
| Modern Corporate | Solid leading icon and divided copy | Leading media and a structured reverse panel | Photo and copy in adjacent columns |
| Premium Gradient | Centered round icon and inset action | Centered toggle and raised reverse panel | Framed photo and centered copy |
| Bold Creative | Oversized trailing icon and bold copy | Offset front and an accent reverse panel | Trailing photo and asymmetric copy |
| Elegant Dark | Framed icon and divided action | Dark frame with a full-width toggle and inset reverse | Stacked photo and full-width action |

### Files

- `includes/Widgets/Cards/` adds the three widgets and extends the shared controls and renderer. `templates/widgets/feature-card.php` adds the disclosure markup.
- `WidgetRegistry.php`, `StylePresetRegistry.php`, and `TemplateContent.php` register the widgets, five presets each, and supported native template fields.
- `assets/scss/content-cards.scss` and `assets/css/content-cards.min.css` provide the scoped layouts and interaction styles. The fifteen 320 × 200 WebP files in `assets/presets/` were captured from the rendered widgets. Flip Card thumbnails show the open face. Hover Card thumbnails show the CC0 `art-gallery.webp` photo from Twenty Twenty-Four. [Twenty Twenty-Four asset credits](https://themes.trac.wordpress.org/browser/twentytwentyfour/1.0/readme.txt)
- `tests/wordpress-widgets.php` and `tests/wordpress-templates.php` verify registered presets and controls, escaping, disclosure markup, effect validation, and image binding.

### Verification

Composer validation, 62 smoke assertions, PHP lint across 77 files, the production asset build, and JavaScript syntax checks passed. All 16 unrelated generated assets matched their originals. On WordPress 7.1.4, Elementor Free 4.4.0, PHP 8.3.17, MySQL 8.4.3, and Twenty Twenty-Four, the cards suite passed 134 assertions, the preset suite passed 224, and native template import passed 36. Feature Card, blog, post context, post metadata, button, and testimonials regressions passed.

Chrome 157 rendered all fifteen presets on desktop and checked single-column layouts without horizontal overflow at 390 px. Enter opened the Flip Card, Space closed it, and a touch tap reopened it. The reveal control retained focus. The Hover Card zoomed on pointer hover and keyboard focus. Reduced-motion emulation disabled both the fold animation and zoom while preserving the card content. The showcase used three Nova stylesheets and no Nova frontend scripts. The shared card CSS is 27,039 bytes before compression. A legacy Feature Card page did not load it. The final loopback showcase navigation measured 1,358 ms and the thumbnails total 60692 bytes.

In Elementor, changed the Icon Box heading, Flip Card reverse description, and Hover Card description. Switched all three presets in the live preview, using an arrow key for Hover Card. Published and verified the content and preset changes on the anonymous frontend and after reopening the editor.

Manual screen-reader checks, other browser engines, physical touch devices, Elementor Pro Dynamic Tags, and the minimum supported WordPress, Elementor, and PHP versions remain unverified.

## Batch 8B iteration 4 — Feature Showcase

Feature Showcase presents a lead feature with a configurable heading, description, image or icon, and action. Its Supporting features repeater displays up to six nonempty rows in the editor's order. Each row has a title and description. The widget uses a semantic list and keeps the CTA as a separate link. The shared card controls provide responsive alignment, spacing, typography, focus and hover styling, and media positions above, leading, and trailing. Elementor Dynamic Tags are available on applicable text, image, and link controls when the installed edition provides them.

| Preset | Composition |
| --- | --- |
| Minimal Editorial | Panoramic lead image above serif copy and a ruled list |
| Modern Corporate | Leading media and compact copy above a tile grid |
| Premium Gradient | Centered hero and raised feature tiles |
| Bold Creative | Oversized trailing media above a full-width lead tile and a two-column list |
| Elegant Dark | Inset media and copy beside a framed feature panel |

### Files

- `FeatureShowcaseWidget.php` defines the repeater and defaults. `CardWidget.php` and `templates/widgets/feature-card.php` add the optional list to the shared renderer.
- `WidgetRegistry.php`, `StylePresetRegistry.php`, and `TemplateContent.php` register the widget, preset choices, and template allowlist. Native imports preserve repeater order and bind a bundled lead image using the existing attachment importer.
- `assets/scss/content-cards.scss` and its compiled stylesheet provide the five compositions. Five captured 320 × 200 WebP previews are in `assets/presets/feature-showcase/`. They show the CC0 `green-staircase.webp` photo from Twenty Twenty-Four. [Twenty Twenty-Four asset credits](https://themes.trac.wordpress.org/browser/twentytwentyfour/1.0/readme.txt)
- `tests/wordpress-widgets.php` and `tests/wordpress-templates.php` cover defaults, escaping, order, row limits, preset registration, and image binding.

### Verification

```powershell
composer validate --strict
composer test
npm run build
npm run check
php tests/wordpress-widgets.php $wpLoad cards
php tests/wordpress-widgets.php $wpLoad presets
php tests/wordpress-templates.php $wpLoad
```

Composer validation, 63 smoke assertions, PHP lint across 78 files, the production asset build, and JavaScript syntax checks passed. All 16 unrelated generated assets matched their originals. On WordPress 7.1.4, Elementor Free 4.4.0, PHP 8.3.17, MySQL 8.4.3, and Twenty Twenty-Four, the cards suite passed 148 assertions, the preset suite passed 233, and the native template suite passed 38. Feature Card, blog, post context, post metadata, button, and testimonials regressions passed.

Chrome 157 rendered all five presets and checked layout at 768 px and 390 px. It verified the semantic feature list, image alternative text, keyboard focus indicator, and trailing media override. The fixture loaded three Nova stylesheets and no Nova frontend scripts. The shared card CSS is 32,543 bytes before compression. The five thumbnails total 32,604 bytes. A legacy Feature Card-only page omitted the shared content card stylesheet. One loopback showcase navigation during concurrent tests measured 9,736 ms.

In Elementor, opened the first repeater row, changed its title, and observed it update in the live list. Changed the preset from Editorial to Corporate with an arrow key. Published the fixture and verified that both values persisted in anonymous page output and after reopening the editor.

All ten Batch 8B widgets are implemented and covered by native template import. The iteration reports record editor and frontend verification for each widget on the tested stack. Manual screen-reader checks, other browser engines, physical touch devices, Elementor Pro Dynamic Tags, and the minimum supported WordPress, Elementor, and PHP versions remain unverified.

## Batch 8C iteration 1 — Advanced Accordion, Advanced Tabs, and Modal Popup

| Widget | Status |
| --- | --- |
| Advanced Accordion | Implemented |
| Advanced Tabs | Implemented |
| Modal Popup | Implemented |
| Tooltip | Implemented |
| Content Toggle | Implemented |
| Interactive Hotspot | Implemented |
| Image Comparison | Implemented |
| Off-Canvas Content | Implemented |
| Advanced Button Group | Implemented |
| Advanced FAQ | Implemented |

- **Advanced Accordion** displays up to eight reorderable rich-text panels. Editors choose the initially open panel or start with all panels closed, and can allow several panels to remain open. Native summaries support activation with Enter, Space, and touch. Up, Down, Home, and End move between headers. [HTML disclosure elements](https://html.spec.whatwg.org/multipage/interactive-elements.html#the-details-element)
- **Advanced Tabs** uses the same panel controls and adds an accessible tab-list label and starting tab. Focus moves between tabs using arrow keys, Home, and End, with automatic activation. The Corporate preset changes from a vertical rail to a horizontal list when space is limited. Tab names, selection, and panel relationships update per instance. [ARIA tabs pattern](https://www.w3.org/WAI/ARIA/apg/patterns/tabs/)
- **Modal Popup** opens on request from a configurable button. The dialog has a title, description, close button, and optional action link. Opening focuses Close. Tab and Shift+Tab cycle inside the dialog. Escape or Close dismisses it and restores focus to its trigger. Following the action dismisses the dialog and leaves focus to the link destination. [ARIA modal dialog pattern](https://www.w3.org/WAI/ARIA/apg/patterns/dialog-modal/)

The three widgets use scoped, conditionally loaded CSS and a shared custom-element script. Without that script, the accordion uses native disclosures, all tab sections remain readable, and modal content appears inline. Reduced-motion preferences suppress the reveal animations. Applicable content controls support Elementor Dynamic Tags when the installed edition provides them. Native Nova templates accept plain-text panel fields and the standard internal action-link format.

| Preset | Accordion | Tabs | Modal |
| --- | --- | --- | --- |
| Minimal Editorial | Ruled disclosures and serif labels | Serif tabs and ruled content | Serif title and ruled description |
| Modern Corporate | Accent rail and separate header band | Leading vertical rail on wide layouts | Inset title band and accent rail |
| Premium Gradient | Centered floating panels | Centered pills and raised content | Narrow centered dialog and pill action |
| Bold Creative | Oversized labels and offset content | Full-width tab band and accent edge | Bold title tile beside description |
| Elegant Dark | Framed disclosures and inset body | Framed navigation and inset panel | Inset dark frame and divided action |

### Files

- `includes/Widgets/Interactive/`, `templates/widgets/interactive-panels.php`, and `templates/widgets/modal-popup.php` provide the controls and sanitized markup.
- `WidgetRegistry.php`, `StylePresetRegistry.php`, and `TemplateContent.php` register the widgets and accepted template fields.
- `assets/scss/interactive-panels.scss`, `assets/js/interactive-panels.js`, their production builds, and `package.json` provide the interaction and styling assets. The 15 captured WebP files are in `assets/presets/`.
- `tests/wordpress-widgets.php` and `tests/wordpress-templates.php` verify rendering, sanitization, registration, and template import.

### Verification

```powershell
composer validate --strict
composer test
npm run build
npm run check
php tests/wordpress-widgets.php $wpLoad interactive
php tests/wordpress-widgets.php $wpLoad presets
php tests/wordpress-templates.php $wpLoad
```

Composer validation, 66 smoke assertions, lint across 84 PHP files, the production asset build, and JavaScript syntax checks passed. The 17 existing generated assets matched their originals.

Chrome 157 rendered all 15 presets. It tested accordion Enter, Space, arrows, Home and End, single and multiple expansion, and isolation between instances. It verified tab and panel ARIA relationships, unique IDs, arrow selection, roving focus, and the Corporate orientation change at 390 px. The modal trapped focus in both directions, closed with Escape, and restored focus. Its action dismissed the dialog and focused the destination of a same-page link. Simulated touch worked for all three widgets. Browser reduced-motion emulation suppressed reveal animation. The three content fallbacks worked when the shared script was blocked.

The showcase fetched the shared stylesheet and script once each. They measure 12,768 and 3,578 bytes before compression. The 15 thumbnails total 46,262 bytes. The legacy Feature Card page requested neither interactive asset. The final loopback showcase navigation measured 3,289 ms.

On WordPress 7.1.4, Elementor Free 4.4.0, PHP 8.3.17, MySQL 8.4.3, and Twenty Twenty-Four, the interactive suite passed 39 assertions, the preset suite passed 260, and the native template suite passed 44. The cards, Feature Card, blog, post context, metadata, button, and testimonials regression suites also passed.

In Elementor, changed the initial accordion panel, the tab-list label, and the modal trigger label. Switched all three presets in the live preview, including a keyboard selection for Tabs. Opened and closed the modal in the preview. Published and verified all six changes in anonymous page output and after reopening the editor.

Manual screen-reader checks, physical touch devices, other browser engines, Elementor Pro Dynamic Tags, and the minimum supported WordPress, Elementor, and PHP versions remain unverified.

## Batch 8C iteration 2 — Tooltip, Content Toggle, and Interactive Hotspot

- **Tooltip** reveals a short explanation on pointer hover or focus. A click or tap pins it, a second activation dismisses it, and Escape dismisses it from the keyboard. The trigger references the text with `aria-describedby`. The text remains inline if the script is unavailable. [WAI-ARIA tooltip guidance](https://www.w3.org/WAI/ARIA/apg/patterns/tooltip/)
- **Content Toggle** presents two editable rich-text states. It shares tab navigation and orientation with Advanced Tabs and lets editors choose the initial state. Both states are readable without the script.
- **Interactive Hotspot** overlays up to six named markers on a responsive image. Each button controls a description and reports whether it is expanded. Marker coordinates are measured from the physical left and top edges. Descriptions remain in the document without the script. Bundled images use the existing feature-image binding.

| Preset | Tooltip | Content Toggle | Interactive Hotspot |
| --- | --- | --- | --- |
| Minimal Editorial | Underlined serif trigger and ruled note | Inline choices and ruled content | Ruled caption below the image |
| Modern Corporate | Leading trigger and inset note | Side rail and structured panel | Framed stage and accent caption |
| Premium Gradient | Centered pill and raised note | Segmented pills and centered content | Rounded stage and centered caption |
| Bold Creative | Oversized trigger and offset note | Expanded switch and accent edge | Image alongside an offset caption |
| Elegant Dark | Framed dark trigger and note | Dark switch and inset content | Dark image frame and separate caption |

## Batch 8C iteration 3 — Image Comparison, Off-Canvas Content, and Advanced Button Group

- **Image Comparison** has two image controls, configurable labels, and a starting percentage. A native range reveals the after image from the left. Both figures remain available when the script is unavailable. A single selected image renders by itself. [HTML range input](https://html.spec.whatwg.org/multipage/input.html#range-state-(type=range))
- **Off-Canvas Content** opens a dialog at the leading or trailing edge and shares the modal's focus containment, Escape, and focus restoration. It accepts an optional action link and shows its content inline when dialog enhancement is unavailable.
- **Advanced Button Group** displays up to six safe, independent links in the editor's order. It has a configurable accessible group label and needs only CSS.

| Preset | Image Comparison | Off-Canvas Content | Advanced Button Group |
| --- | --- | --- | --- |
| Minimal Editorial | Wide stage with serif labels below | Ruled drawer and serif heading | Inline serif links with individual rules |
| Modern Corporate | Framed stage beside the slider on wide screens | Accent rail and inset heading | Close-set links with divided borders |
| Premium Gradient | Rounded stage and centered slider | Centered copy on a soft gradient | Centered pill links on a tinted tray |
| Bold Creative | Panoramic crop and accent edge | Bold title tile and accent edge | Expanded lead action and supporting links |
| Elegant Dark | High-contrast labels above a framed stage | Dark inset drawer and divided action | Framed action grid |

## Batch 8C iteration 4 — Advanced FAQ

Advanced FAQ uses the shared native disclosures with question and answer defaults, up to eight rows, and a configurable initial question. The five presets offer ruled editorial questions, structured accent bands, raised centered panels, oversized questions with offset answers, and framed dark disclosures. Keyboard and touch interactions match Advanced Accordion.

### Files and compatibility

- `includes/Widgets/Interactive/` and `templates/widgets/` contain the seven widgets and shared renderers. Existing Accordion, Tabs, and Modal control keys are preserved.
- `WidgetRegistry.php` and `StylePresetRegistry.php` register the seven widgets and their preset metadata. The 35 captured WebP previews are in `assets/presets/`.
- `TemplateContent.php`, `TemplateRegistry.php`, and `TemplateImporter.php` accept the new controls. A bundled comparison image declares `before_image` or `after_image` as its control. Existing manifests still default to `feature_image`. Duplicate slots, wrong slots, and unsafe local-link shapes fail validation.
- `assets/scss/interactive-panels.scss` and `assets/js/interactive-panels.js` extend the conditional asset pair and are compiled into production assets. `tests/wordpress-widgets.php` and `tests/wordpress-templates.php` cover renderer behavior, sanitization, preset registration, and imports.
- The Hotspot and Image Comparison previews use the CC0 `art-gallery.webp` and `green-staircase.webp` images from Twenty Twenty-Four. [Twenty Twenty-Four asset credits](https://themes.trac.wordpress.org/browser/twentytwentyfour/1.0/readme.txt)

### Verification

```powershell
composer validate --strict
composer test
npm run build
npm run check
php tests/wordpress-widgets.php $wpLoad interactive
php tests/wordpress-widgets.php $wpLoad presets
php tests/wordpress-templates.php $wpLoad
```

Composer validation, 73 smoke assertions, lint across 94 PHP files, the production build, and JavaScript syntax checks passed. On WordPress 7.1.4, Elementor Free 4.4.0, PHP 8.3.17, MySQL 8.4.3, and Twenty Twenty-Four, the interactive suite passed 125 assertions, preset registration passed 323, and native template import passed 54. Regression suites for the cards, Feature Card, blog, post context, metadata, button, and testimonials also passed.

Chrome 157 rendered all 35 new presets, checked horizontal overflow at 390 px, and verified conditional assets and readable fallbacks. The interaction runs covered Tooltip hover, focus, Escape and tap, Content Toggle selection, Hotspot marker keyboard and tap controls, Image Comparison range keys, bounds, and simulated touch, drawer focus, Escape, and reduced motion, button-group link order, and FAQ keyboard and touch behavior.

In Elementor, changed a tooltip description, the toggle initial state, a marker label, the comparison position, the drawer side, the button-group label, and the initially open FAQ. Switched all seven presets in the live preview, including a keyboard selection for Content Toggle. Published and verified all seven content changes and preset selections on the anonymous frontend and after reopening the editor.

The shared CSS and JavaScript measure 22,616 and 5,510 bytes before compression. The 35 new thumbnails total 125712 bytes. All 17 unrelated generated assets matched their originals.

All ten Batch 8C widgets are implemented. The native importer covers all ten in one fixture. The iteration reports document frontend and editor verification for all ten on the tested stack. Manual screen-reader checks, physical touch devices, other browser engines, Elementor Pro Dynamic Tags, and the minimum supported WordPress, Elementor, and PHP versions remain unverified.
