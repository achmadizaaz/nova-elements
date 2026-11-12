# Phase 7 — Shared query engine and Blog Grid

## Milestone 1 — Repository audit

The owning checkout is based on `4b4b625adbac1fb68cfd6bbbcaadbcdf16e9cd36` and already contains local Phase 6 Template Library work. The registry exposes five widgets. Blog Grid is the only widget that queries posts. It used `PostQuery` for argument construction and ran `WP_Query` directly in its renderer. The Visual Style Picker, global design tokens, and five Blog Grid presets already existed. The library validates its bundled native Elementor documents against a per-widget setting allowlist.

This audit determined the integration points without introducing a migration or changing the widget registry.

## Milestone 2 — Shared engine

Modified `includes/Query/PostQuery.php` and added `includes/Query/QueryResult.php`. `PostQuery::run()` compiles saved settings, runs `WP_Query`, and returns posts, page data, and an optional validation error. WordPress owns query caching and object/meta/term priming. The engine skips total-row counting for unpaginated requests and bypasses result caching for random ordering and metadata queries. Direct metadata updates therefore take effect immediately in those queries.

The engine serves published, password-free posts of registered, viewable public types for every visitor. It supports post type, manual IDs, current post, related posts and current taxonomy archive. The caller supplies the context objects. Rules cover explicit post and author IDs, current-post exclusion, sticky posts, hierarchical parents, taxonomy clauses and site-local publication dates. Exclusions and source context remain mandatory when inclusion rules use OR.

Filtering and sorting by custom fields require a public scalar field registered through WordPress and explicitly enabled through `nova_elements_queryable_meta`. The callback receives the list of allowed field keys and the post type. Return only keys whose values may safely influence a public listing. Protected keys are rejected. Numeric fields use a fixed precision decimal cast. Date comparison expects values stored as `YYYY-MM-DD`. A site can return a different list for each post type.

## Milestone 3 — Elementor controls

Added `includes/Controls/QueryControls.php` for any compatible Elementor widget. The panel groups controls into Content Source, Include / Exclude, Taxonomy Filters, Custom Fields, Sorting, Pagination and Advanced Query. Source, comparison, date and navigation controls use Elementor's built-in conditions. Taxonomy and field choices reflect registrations on the site, and the engine validates that each choice belongs to the effective post type. Manual selection accepts post IDs. Searchable post and term selectors remain open.

## Milestone 4 — Blog Grid

Modified `includes/Widgets/Blog/BlogGridWidget.php`, `templates/widgets/blog-grid.php` and `includes/TemplateLibrary/TemplateContent.php`. The widget uses the shared controls and engine. It keeps the `ne-blog-grid` ID, saved control keys, visual presets and existing Previous / Next navigation default. Numbered links are opt-in. Pagination preserves unrelated URL arguments and uses the widget ID to scope page state. The engine uses an ID tie-breaker, falls back to publication-date sorting for random paginated queries, and applies the existing skip control only when pagination is off.

An unavailable saved post type now produces an empty result and a configuration message in the editor. Term and post IDs are local to the site. Template imports retain the supported setting keys, including taxonomy repeater entries. Imports to a different site may require updating those IDs and selecting fields available there.

Pagination uses offsets with a 24-result page-size cap and a 10,000-page navigation cap. Counts are computed for ordinary paginated results. WordPress can omit a count for an empty out-of-range page, so navigation provides a link back to page one. Concurrent edits to the sorted dataset can move results between requests. Large directories with deep pagination should use cursor-based traversal on an immutable or consistently ordered indexed field if that becomes a supported workflow.

## Verification

The product source was checked against a disposable WordPress 7.1.4 site with Elementor Free 4.4.0, Twenty Twenty-Four, PHP 8.3.17 and MySQL 8.4.3. No other Nova product was active. Execute the mutating test commands only with disposable WordPress data.

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
php tests/wordpress-query-benchmark.php $wpLoad --disposable
```

Added the benchmark script and expanded `tests/wordpress-widgets.php` and `tests/smoke.php`. PHP lint, Composer validation and smoke tests, the asset build and JavaScript syntax check passed. The 11 regenerated assets matched their originals. WordPress checks covered legacy rendering, instance-specific and numbered navigation, the five presets and their control metadata, public post visibility, custom post types, taxonomy, author and date rules, decimal metadata filtering/sorting, direct meta edits, and protected meta rejection. The three bundled templates imported as native Elementor documents. The standalone smoke test also exercised advanced query keys in the bundled template validator.

Interactive Elementor editor use, browser layout, assistive technology and the minimum supported platform versions remain unverified. The first disposable run logged Elementor CLI warnings and a fixture configuration warning. The fixture configuration was corrected for the repeat run.

## Query benchmark

| Posts | Cold ms | Warm ms | Cold / warm SQL queries | Cold / warm peak memory increase |
| ---: | ---: | ---: | ---: | ---: |
| 100 | 6.79 | 0.85 | 5 / 0 | 182.4 / 31.8 KiB |
| 1,000 | 8.68 | 0.95 | 5 / 0 | 181.1 / 31.8 KiB |
| 10,000 | 42.33 | 0.95 | 5 / 0 | 181.1 / 31.8 KiB |

The benchmark queries page 2 of a public custom post type with 12 posts per page, total counting and date/ID ordering. Cold clears the WordPress object cache. Warm repeats the query in the same PHP process using WordPress's built-in cache. Memory is the incremental PHP peak above the pre-query baseline. Each cell represents one sample on the local test stack. These measurements cover query execution only. AJAX timing requires the later AJAX milestone.

## Remaining Phase 7 work

Milestones 5–10 cover native dynamic field mapping and provider adapters, AJAX filtering and load more, Events and Team widgets, saved presets, further widget and template integration, and end-to-end QA.
