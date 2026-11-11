# MASTER PROMPT: PREMIUM ELEMENTOR ADDONS PLUGIN

## ROLE & EXPERTISE

Act as a **Principal WordPress Plugin Engineer, Senior PHP Architect, Elementor Addons Specialist, UI/UX Design Lead, and Frontend Performance Engineer** with 15+ years of professional experience.

Your task is to architect, design, develop, and deliver a production-ready WordPress plugin that extends Elementor with a comprehensive collection of professional widgets.

The plugin must demonstrate the engineering quality, architecture, security, performance, and visual excellence expected from a premium commercial WordPress product.

**Do not generate a basic tutorial plugin, prototype, or simplistic widget collection.**

The final product must be maintainable, extensible, secure, responsive, visually sophisticated, and ready for real-world deployment.

---

# 1. PROJECT OVERVIEW

**Plugin Name:** Nova Elements  
**Plugin Slug:** nova-elements  
**Namespace:** NovaElements  
**Text Domain:** nova-elements  
**Architecture:** Modular, OOP, PSR-4 Autoloading  
**Integration:** Elementor  
**License:** GPL-compatible

### Main Objective

Build a premium Elementor addon containing a large collection of customizable widgets for modern websites, including corporate websites, universities, portfolios, agencies, news portals, landing pages, and online stores.

Each applicable visual widget must provide **at least 5 genuinely distinct, professionally designed styles**.

Each style must differ meaningfully in composition, typography, spacing, hierarchy, borders, backgrounds, image treatment, or interaction.

Simply changing colors, border radius, or shadows is NOT considered a different style.

The visual quality should be comparable to polished premium website templates and leading commercial Elementor addons, without copying their proprietary assets or source code.

---

# 2. TECHNOLOGY STACK

Use modern, stable, officially supported technology.

### Backend
- PHP 8.2+ with compatibility verified against the selected WordPress and Elementor versions.
- WordPress Plugin API.
- Elementor public developer APIs.
- PSR-4 autoloading with Composer.
- Object-oriented architecture.
- WordPress Settings API.
- WordPress REST API where necessary.
- Secure AJAX handlers where appropriate.
- WordPress Transients API for caching.

### Frontend
- Modern semantic HTML5.
- CSS3 with custom properties.
- SCSS for organized styling.
- Vanilla JavaScript and lightweight utilities.
- CSS Grid and Flexbox.
- CSS transitions and transforms.
- Accessible, keyboard-friendly interactions.
- Optional Swiper only if compatible with Elementor's supported integration.

Avoid unnecessarily importing large JavaScript frameworks.

Do not rely on undocumented Elementor APIs or internal implementation details.

---

# 3. MODULAR PLUGIN ARCHITECTURE

Design the plugin with clear separation of responsibilities.

Suggested structure:

```text
nova-elements/
├── nova-elements.php
├── uninstall.php
├── composer.json
├── readme.txt
├── includes/
│   ├── Core/
│   │   ├── Plugin.php
│   │   ├── Bootstrap.php
│   │   ├── Compatibility.php
│   │   ├── Assets.php
│   │   ├── Widget_Manager.php
│   │   ├── Widget_Registry.php
│   │   ├── Settings.php
│   │   └── Cache_Manager.php
│   ├── Admin/
│   │   ├── Dashboard.php
│   │   ├── Settings_Page.php
│   │   └── Widget_Manager_Page.php
│   ├── Widgets/
│   │   ├── Base/
│   │   │   ├── Base_Widget.php
│   │   │   └── Style_Manager.php
│   │   ├── Heading/
│   │   ├── Blog/
│   │   ├── Cards/
│   │   ├── Buttons/
│   │   └── ...
│   ├── Controls/
│   ├── Helpers/
│   ├── Integrations/
│   └── Traits/
├── assets/
│   ├── css/
│   ├── js/
│   ├── scss/
│   ├── images/
│   └── icons/
├── templates/
│   └── widgets/
├── languages/
├── tests/
└── build/
```

Architecture requirements:

1. Every widget must be independent.
2. Widgets must share reusable control builders and utilities.
3. Each widget must support multiple visual templates.
4. A widget's assets should load only when necessary.
5. Disabled widgets must not register their frontend assets unnecessarily.
6. Register widgets through the supported Elementor widget registration hooks.
7. Avoid global namespace pollution.
8. Keep business logic outside presentation templates.
9. Provide an extensible registry for future widgets.
10. Implement compatibility checks and graceful error handling.

Use a central **Widget Registry** that automatically discovers or explicitly registers available widgets and their metadata.

---

# 4. COMPLETE WIDGET COLLECTION

Implement the following widget categories.

Each widget must provide at least five distinct design presets unless its functionality makes visual presets inappropriate.

## A. Content & Typography

1. Advanced Heading
2. Animated Heading
3. Gradient Heading
4. Dual Color Heading
5. Text Highlight
6. Text Marquee
7. Advanced Text Block
8. Drop Cap
9. Blockquote
10. Icon List

## B. Blog & Posts

11. Blog Grid
12. Blog List
13. Blog Masonry
14. Blog Carousel
15. Featured Posts
16. Post Timeline
17. Magazine Layout
18. Post Categories
19. Author Profile
20. Related Posts

Support WordPress posts, custom post types, taxonomy filtering, pagination, and responsive layouts where appropriate.

## C. Cards & Content Blocks

21. Info Card
22. Feature Card
23. Service Card
24. Profile Card
25. Team Member Card
26. Pricing Card
27. Testimonial Card
28. Image Box
29. Icon Box
30. Flip Card

## D. Interactive Elements

31. Advanced Button
32. Button Group
33. Accordion
34. Tabs
35. Modal Popup
36. Tooltip
37. Content Toggle
38. Interactive Hotspot
39. Image Comparison
40. Off-Canvas Content

## E. Media & Gallery

41. Image Gallery
42. Image Carousel
43. Video Gallery
44. Video Popup
45. Before/After Slider
46. Image Hover Effects
47. Logo Carousel
48. Filterable Gallery
49. Image Accordion
50. Media Showcase

## F. Business & Marketing

51. Call to Action
52. Pricing Table
53. Pricing Comparison
54. Countdown Timer
55. Progress Bar
56. Circular Progress
57. Counter
58. Skill Bar
59. Statistics Grid
60. Client Logo Grid

## G. Layout & Navigation

61. Advanced Divider
62. Breadcrumbs
63. Content Timeline
64. Vertical Timeline
65. Steps
66. Advanced Navigation Menu
67. Search Box
68. Table of Contents
69. Content Slider
70. Advanced Container Elements

## H. Education & Institutional Widgets

71. Academic Program Card
72. Lecturer / Faculty Profile
73. Campus News Grid
74. Event Listing
75. Event Timeline
76. Announcement Box
77. Download / Document Card
78. Achievement Showcase
79. Partner Logo Showcase
80. Admission CTA

## I. WooCommerce Integration

81. Product Grid
82. Product Carousel
83. Product Category Grid
84. Featured Products
85. Product Badge
86. Product Quick View
87. Product Tabs
88. Add to Cart
89. Product Comparison
90. Promotional Product Banner

WooCommerce widgets must load only when WooCommerce is active and the required integration is available.

---

# 5. MANDATORY FIVE-STYLE DESIGN SYSTEM

Every visual widget must have a Style Preset selector under Elementor's Content tab.

Implement these five foundational design directions:

### Style 01 — Minimal Editorial
- Clean white surfaces.
- Elegant typography.
- Generous whitespace.
- Fine borders.
- Restrained visual hierarchy.
- Suitable for professional and editorial websites.

### Style 02 — Modern Corporate
- Structured layouts.
- Balanced typography.
- Blue and indigo accents.
- Professional content hierarchy.
- Refined shadows.
- Suitable for universities, companies, and institutions.

### Style 03 — Premium Gradient
- Sophisticated gradient accents.
- Carefully controlled depth.
- Soft lighting and layered backgrounds.
- Contemporary visual treatments.
- Suitable for SaaS products and landing pages.

### Style 04 — Bold Creative
- Expressive typographic scale.
- Strong compositions.
- Distinctive image placement.
- Asymmetrical layouts where appropriate.
- High-impact interactive effects.
- Suitable for agencies and portfolios.

### Style 05 — Elegant Dark
- Charcoal and deep navy surfaces.
- Carefully balanced contrast.
- Subtle borders and highlights.
- Refined depth.
- Premium visual hierarchy.
- Suitable for technology and luxury websites.

### CRITICAL DESIGN RULE

Do not apply these five styles as simple theme color variations.

For example, Blog Grid must support five different layout compositions, such as:

1. Minimal editorial grid with bottom-aligned metadata.
2. Corporate card grid with visible content panels.
3. Featured magazine layout with asymmetric post sizes.
4. Image-overlay editorial layout.
5. Premium dark layout with distinctive content arrangement.

Every widget should define its own meaningful five-preset implementation.

---

# 6. ADVANCED ELEMENTOR CONTROLS

Every widget must provide appropriate controls.

### Content Tab
- Content source.
- Layout selection.
- Style preset selection.
- Image and icon selection.
- Title, subtitle, description.
- Link settings.
- Repeater fields where appropriate.
- Query options where applicable.
- Display toggles.
- Alignment and ordering.

### Style Tab
- Typography.
- Text colors.
- Background colors.
- Gradient backgrounds.
- Spacing.
- Padding.
- Margin.
- Borders.
- Border radius.
- Box shadows.
- Hover states.
- Image aspect ratio.
- Image overlay.
- Icon properties.
- Animation settings.
- Responsive styling.

### Advanced Behavior
- Responsive device controls.
- Elementor Global Colors.
- Elementor Global Fonts.
- Dynamic Tags where compatible.
- RTL layouts.
- Accessibility settings.
- Conditional controls.
- Elementor editor live preview.

Avoid implementing redundant controls that conflict with Elementor's built-in Advanced tab.

Use Elementor's official responsive controls and style selectors wherever possible.

---

# 7. PROFESSIONAL UI/UX REQUIREMENTS

Visual design is a primary acceptance criterion.

Every widget must demonstrate:

- High-quality visual hierarchy.
- Modern typography.
- Balanced spacing.
- Consistent alignment.
- Premium image treatments.
- Elegant interactions.
- Clean card compositions.
- Responsive layouts.
- Professional empty states.
- Consistent control organization.

### Design Tokens

Create a central CSS variable system:

```css
:root {
  --ne-primary: #4f46e5;
  --ne-secondary: #0f172a;
  --ne-accent: #06b6d4;
  --ne-surface: #ffffff;
  --ne-surface-muted: #f8fafc;
  --ne-text: #0f172a;
  --ne-text-muted: #64748b;
  --ne-border: #e2e8f0;
  --ne-radius-sm: 8px;
  --ne-radius-md: 14px;
  --ne-radius-lg: 20px;
  --ne-radius-xl: 28px;
}
```

Tokens must be configurable and should integrate with Elementor Site Settings when appropriate.

Do not globally override WordPress theme styles.

Scope frontend styles to the plugin's widget classes.

---

# 8. RESPONSIVE DESIGN

All widgets must work on:

- Desktop.
- Tablet.
- Mobile.

Use appropriate responsive controls for:

- Grid columns.
- Gaps.
- Typography.
- Padding.
- Image aspect ratios.
- Content alignment.
- Visibility.
- Layout direction.

Use CSS Grid and Flexbox instead of rigid fixed dimensions.

Avoid horizontal overflow on small screens.

Carousel widgets must provide device-specific slide counts.

Ensure responsive behavior is verified in Elementor's editor and on the real frontend.

---

# 9. ANIMATION AND INTERACTIONS

Provide subtle, performance-conscious animations.

Examples:

- Hover lift.
- Border transitions.
- Image zoom.
- Gradient transitions.
- Text reveal.
- Fade and slide.
- Icon motion.
- Accordion expansion.
- Tab transitions.
- Carousel interactions.

Respect `prefers-reduced-motion`.

Avoid unnecessary continuous animations, excessive DOM manipulation, layout thrashing, and heavy JavaScript.

Make animations optional.

---

# 10. BLOG AND QUERY ENGINE

Create a reusable Query Builder for post-based widgets.

Supported features:

- Post type selection.
- Category and taxonomy filtering.
- Include/exclude post IDs.
- Order by date, title, modified date, or supported query fields.
- Ascending/descending order.
- Post count.
- Offset where compatible.
- Featured image settings.
- Author information.
- Publication date.
- Excerpt length.
- Reading time estimation.
- Pagination.
- Load More.
- Optional AJAX filtering.
- Taxonomy badges.
- Custom post type support.

Use `WP_Query` safely and efficiently.

Ensure every generated query resets WordPress post data correctly.

Avoid expensive unnecessary database queries.

Cache suitable query results when beneficial, with an appropriate invalidation strategy.

---

# 11. PERFORMANCE OPTIMIZATION

Implement performance best practices:

1. Conditional asset loading.
2. Minified production CSS and JavaScript.
3. Lightweight frontend markup.
4. Avoid duplicated assets.
5. Lazy loading for suitable images.
6. Optimized image sizes through WordPress.
7. No unnecessary jQuery dependency.
8. No third-party CDN dependency by default.
9. Efficient WordPress queries.
10. Avoid rendering disabled widgets.

Performance targets must be measured rather than claimed.

Pay special attention to pages containing multiple instances of the same widget.

Do not load all 90 widget scripts on every page.

---

# 12. SECURITY STANDARDS

Follow WordPress security best practices.

Requirements:

- Sanitize all appropriate user input.
- Escape values at output according to HTML, URL, attribute, and JavaScript contexts.
- Use nonces for state-changing requests.
- Check user capabilities on privileged operations.
- Use prepared SQL queries when direct SQL is necessary.
- Avoid unsafe file operations.
- Prevent stored and reflected XSS.
- Prevent unauthorized AJAX and REST actions.
- Prevent path traversal.
- Validate widget query parameters.
- Never trust values from Elementor settings without appropriate validation and escaping.

Use WordPress coding standards and relevant WordPress security APIs.

---

# 13. ADMIN DASHBOARD

Create a premium WordPress administration interface.

Menu:

**Nova Elements**
- Dashboard
- Widget Manager
- Global Styles
- Integrations
- Settings
- System Status

### Dashboard
Include:
- Number of available widgets.
- Number of active widgets.
- Plugin version.
- Elementor compatibility status.
- WooCommerce integration status.
- Quick configuration links.

### Widget Manager
Provide:
- Search widgets.
- Filter by category.
- Enable/disable individual widgets.
- Enable all.
- Disable all.
- Save configuration.
- Display widget description and category.

Keep the dashboard clean, modern, accessible, and consistent with WordPress administration conventions.

Avoid unnecessary decorative animations inside the admin area.

---

# 14. EDITOR EXPERIENCE

Create an excellent Elementor editing experience.

Requirements:

- Custom Elementor widget category: "Nova Elements".
- Appropriate widget icons.
- Clear widget names.
- Descriptive control labels.
- Useful default content.
- Professional default styles.
- Logical content/style panel sections.
- Preview updates in the editor.
- No broken controls.
- No unnecessary editor reloads.
- Proper support for multiple widget instances.

Create realistic placeholder content and images using approved local assets or WordPress media when needed.

Every widget must look polished immediately after insertion without requiring extensive manual configuration.

---

# 15. WIDGET DEVELOPMENT CONTRACT

For every widget, provide:

1. Widget PHP class.
2. Widget metadata.
3. Elementor control registration.
4. Five distinct preset designs.
5. Frontend rendering template(s).
6. Scoped CSS/SCSS.
7. JavaScript only if required.
8. Responsive behavior.
9. Accessibility handling.
10. Security and output escaping.
11. Automated tests where practical.
12. Documentation.

Example class responsibilities:

```text
AdvancedHeadingWidget
├── get_name()
├── get_title()
├── get_icon()
├── get_categories()
├── get_style_depends()
├── get_script_depends()
├── register_controls()
└── render()
```

Use currently supported Elementor method signatures and registration mechanisms.

Implement safe extensibility using reusable classes, traits, or services where they genuinely reduce duplication.

Avoid excessive abstraction that makes widgets harder to maintain.

---

# 16. QUALITY ASSURANCE

Implement a realistic testing strategy.

### PHP
- WordPress coding standards.
- PHP syntax validation.
- PHPStan or suitable static analysis.
- PHPUnit tests for testable services.
- WordPress integration tests for plugin lifecycle and querying.

### Frontend
- ESLint.
- Stylelint.
- Accessibility checks.
- Responsive tests.
- Playwright end-to-end testing where feasible.

### Compatibility
Verify on the declared supported versions of:
- WordPress.
- Elementor.
- PHP.
- WooCommerce for integration widgets.

### Functional Acceptance Criteria

- Plugin activates without PHP fatal errors.
- Plugin deactivates cleanly.
- Missing Elementor dependency is handled gracefully.
- All enabled widgets appear in Elementor.
- Disabled widgets are not registered.
- Widget controls work.
- Style presets produce meaningfully different visuals.
- Frontend renders correctly.
- Editor and frontend styling remain consistent.
- Responsive layouts work.
- Multiple instances do not interfere.
- Assets are conditionally loaded.
- Accessibility checks pass for applicable functionality.

---

# 17. DEVELOPMENT WORKFLOW

Do not attempt to implement all 90 widgets in one unreviewed generation.

Follow a structured engineering process.

### Phase 1 — Architecture
Deliver:
- Technical architecture.
- Dependency compatibility matrix.
- Full folder structure.
- Design system.
- Widget registry specification.
- Asset loading strategy.
- Security strategy.
- Testing strategy.

### Phase 2 — Plugin Foundation
Implement:
- Plugin bootstrap.
- Autoloader.
- Elementor compatibility.
- Widget manager.
- Base widget.
- Asset manager.
- Admin dashboard.
- Global design tokens.

### Phase 3 — Five Reference Widgets
Fully implement:
1. Advanced Heading.
2. Blog Grid.
3. Feature Card.
4. Advanced Button.
5. Testimonials.

Each must include five truly distinct designs and comprehensive controls.

Use these widgets to validate architecture, performance, editing experience, and visual quality.

### Phase 4 — Widget Expansion
Develop the remaining widgets by category.

Reuse proven architecture without sacrificing distinctive design.

### Phase 5 — Testing and Optimization
Perform:
- Code review.
- Security review.
- Accessibility review.
- Visual regression testing.
- Responsive testing.
- Performance profiling.
- Compatibility testing.

### Phase 6 — Packaging
Generate:
- Installable WordPress plugin ZIP.
- Source code.
- Build commands.
- Installation guide.
- Developer documentation.
- Changelog.
- Test report.

Do not declare a phase complete if required files are missing or tests have not been performed.

---

# 18. CODE QUALITY REQUIREMENTS

All implementation must follow these rules:

- Production-quality code.
- Clear naming conventions.
- SOLID principles where useful.
- DRY without over-engineering.
- Reusable components.
- Strict responsibility separation.
- No PHP notices or warnings.
- No JavaScript console errors.
- No CSS leakage.
- No hidden hardcoded dependencies.
- No placeholder implementation disguised as complete functionality.
- No TODO-only widget features.
- No pseudo-code in files presented as complete.
- Meaningful error handling.
- Internationalization-ready strings.
- Maintainable and documented APIs.

Never invent Elementor APIs.

When compatibility is uncertain, inspect the official documentation and installed package sources before selecting an implementation.

---

# 19. OUTPUT FORMAT

For each development phase, provide:

1. Architecture or implementation summary.
2. Files created or modified.
3. Complete working code for the relevant files.
4. Explanation of important design decisions.
5. Testing commands.
6. Verified results and known limitations.
7. Next implementation milestone.

If operating inside a coding workspace, write the actual files into the project, not merely provide snippets.

Do not claim that a ZIP, test, or working feature exists unless it has actually been generated or verified.

---

# 20. FINAL EXPECTATIONS

The result must feel like a polished premium Elementor addon, developed by an experienced WordPress engineering team.

Prioritize the following, in order:

1. Correctness and compatibility.
2. Security and maintainability.
3. Visual design quality.
4. User experience inside Elementor.
5. Performance.
6. Extensibility.
7. Widget quantity.

A smaller group of fully working, beautifully designed widgets is preferable to a large collection of incomplete widgets.

**START NOW WITH PHASE 1: ARCHITECTURE AND DESIGN SYSTEM.**

Present the proposed architecture, compatibility strategy, design system, widget specifications, and development plan before implementing the plugin foundation.

## Repository conventions

- Use PHP 8.2+, the `NovaElements\` PSR-4 namespace, and WordPress PHP coding conventions. Guard direct PHP entry points and keep translation, sanitization and output escaping at their respective boundaries.
- Use public Elementor hooks and controls. Keep widget IDs and persisted control keys stable. Register widget dependencies through the registry and manager. Use Elementor selectors for instance-specific CSS and keep shared styles scoped to Nova.
- Use the Settings API for site settings. Validate against known widget IDs, preset IDs, and token formats. Protect admin writes with capabilities and nonces.
- Edit SCSS and readable JavaScript sources, then regenerate the production assets. Avoid new runtime dependencies for features the platform already supports.
- Run the checks in `README.md` and record real WordPress, Elementor editor, and frontend verification separately. Follow the milestone in the current task and use the reference widgets to validate the architecture before catalogue expansion.
