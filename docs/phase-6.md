# Phase 6 template library

## Audit and architecture proposal

Nova 0.3.0 registers five widgets with stable IDs and stores each composition in `style_preset`. Elementor's ordinary widget settings provide instance overrides, and Nova's token stylesheet supplies site-wide defaults. The project has no template storage, importer, or editor library integration.

Elementor 3.20 and 4.4 expose the documented Finder category filter and the native Site Templates workflow. The installed packages expose the local template source's public `save_item()` method. This saves a native Elementor library document and assigns new element IDs. The documented integration uses Finder to open Nova's catalog, then Elementor's native Insert action to place the imported template at the selected location. My Templates links to Elementor's existing save, organization, edit, preview, export, and trash workflow.

Each bundle has a versioned manifest, an Elementor-format JSON document, optional local raster assets, and preview images captured from the rendered design. Listing reads metadata only. Import uses a POST with a template-specific nonce, checks native capabilities and enabled widgets, validates the bundle, resolves media by content hash, and verifies the resulting native document. Existing pages and site design settings are outside the import write path.

The first verification batch is Corporate Hero, Premium Blog Grid, and Modern Features. Each uses the existing Nova widget types and preset keys. The Blog Grid uses posts already present on the destination site. The expansion toward 30 designs follows validation of this batch.

- [Elementor Finder extension](https://developers.elementor.com/docs/finder/add-items-to-existing-category)
- [Elementor document format](https://developers.elementor.com/docs/data-structure/general-structure/)
- [Native template insertion and export](https://elementor.com/help/template-library/)

## Reference catalog

| Template | Composition | Destination site data |
| --- | --- | --- |
| Corporate Hero | Corporate headline and button beside a dark strategy card. The columns stack on mobile. | Customize the headline and CTA URL. Choose the heading level that fits the destination page. |
| Premium Blog Grid | Editorial introduction above a magazine grid with a lead story, category, date, excerpt, and pagination. | Uses published posts, categories, and featured images from the destination site. Screenshots show sample posts. |
| Modern Features | A photo-led research card beside stacked planning and delivery cards. | Imports one local WebP into the Media Library with alt text. |

The Modern Features photograph and the photos in the blog previews are CC0 images from WordPress's Twenty Twenty-Four theme. [Template image credits](../library/CREDITS.md)

## Use the library

1. Open **Nova Elements → Template Library**. In Elementor, use Finder (Ctrl or Cmd + E), search for **Nova Elements Template Library**, and open the result in a separate tab to keep the current editor open.
2. Filter by name, type, or category. Open **Preview** and switch between desktop, tablet, and mobile screenshots.
3. Select **Import to Site Templates**. WordPress must grant your account permission to create and publish Elementor templates. Modern Features also requires permission to upload files.
4. In the active page editor, open the template folder and select **Site Templates** or **My Templates**, depending on the Elementor version. Select **Insert** on the imported entry. If the folder was already open when you imported, reload the editor after saving in-progress edits so Elementor refreshes its local template list.
5. Edit the imported containers and widgets normally. Save a reusable variation using Elementor's **Save as Template** action. The **My Templates** link in Nova opens Elementor's own management screen for browsing, editing, organizing, exporting, and trashing saved templates.

Every successful import creates a separately editable Site Template. A subsequent import can reuse the local image when the existing file still matches the bundled image hash. The import uses the same local source as Elementor's UI. No remote media download or Nova-specific storage for user templates is involved.

On recoverable failure, Nova removes the incomplete template and new attachments when their IDs are known. If PHP stops during a WordPress or Elementor write before the platform returns an ID, review recent entries in Site Templates and Media Library before retrying.

On the clean offline Elementor 3.20 QA install, Elementor's own template-folder dialog needed its catalog metadata before it could show the local tab. The Nova catalog and its import action worked offline. If the native folder is unavailable, open the imported template through **My Templates → Edit with Elementor** in a second tab on the same site. Right-click the template's outer container handle and choose **Copy**, then right-click the destination page's empty add-content area and choose **Paste**. This workflow was verified with Premium Blog Grid. [Elementor context menus](https://elementor.com/help/right-click/)

## Add or review a bundle

Create a directory under `library/` named after the stable lowercase manifest ID. Place `manifest.json` (schema version 1), `template.json` (Elementor document format 0.4), and `preview.webp`, `preview-tablet.webp`, and `preview-mobile.webp` in that directory. The registry supports section, full-page, and widget-preset manifest types. This batch contains section templates. For a widget preset, wrap the widget in an Elementor container. Use the existing widget IDs, preset keys, and documented Elementor controls.

Declare each bundled image under `assets` in the manifest with the target element ID, filename, and alt text. Keep the image in the bundle's `media/` directory and use an empty image ID/URL in the JSON. Validate new settings against the importer allowlist before adding a bundle. Capture previews from the design as rendered on a disposable WordPress site. Install the resulting bundle only after a clean import, native insertion, and responsive review.

## Verification

```powershell
composer validate --strict
composer test
npm run build
npm run check
php tests/wordpress-templates.php "D:/path/to/disposable-wordpress/wp-load.php"
```

The WordPress integration test checks authorization, missing widget dependencies, native persistence, element ID remapping, insertion payloads, and WebP reuse. It creates temporary Site Templates and removes them after the run. Use a disposable installation with at least one administrator and the required plugins activated.

The importer passed on WordPress 6.5 with Elementor 3.20.0 and WordPress 7.1.4 with Elementor 4.4.0, both on PHP 8.3.17. The five existing widgets passed their PHP regression cases on the latter installation.

Chrome checks on Elementor 3.20 verified Finder, search, combined filters, the empty state, all three preview sizes, Escape and restored keyboard focus, and invalid-nonce rejection. Axe reported no WCAG A/AA violations in its scoped check of the catalog with the preview open. All three templates rendered at 1120, 768, and 375 pixels without horizontal overflow.

With Elementor's catalog metadata seeded in offline QA, its native **Insert** placed each template alongside existing content. Edited titles and Visual Style Picker changes persisted after saving and rendered on the public page. A temporary Nova global palette override reached the editor iframe and public page for Corporate Hero and Modern Features. The catalog's CSS and JavaScript were absent from all three public pages.

For manual QA, import and insert each template into an existing page. Confirm that the existing content remains in place, select an inserted widget, change its title and Visual Style Picker setting, and save. Inspect desktop, tablet, and mobile in the editor and public page. Change a Nova global color in a disposable test site and verify the inserted widgets follow it where no instance override is set. For the blog template, try a site with zero posts, then add at least five published posts with featured images and verify the page navigation.

The expansion milestone adds independently designed and verified full-page and widget-preset templates, with a target of 30 total.
