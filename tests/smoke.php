<?php
/**
 * Standalone boundary checks. Real WordPress and Elementor integration is tested separately.
 */

namespace Elementor {
	class Widget_Base {
		public array $settings = array();

		public function get_settings_for_display(): array {
			return $this->settings;
		}
	}
}

namespace {
	use NovaElements\Core\Assets;
	use NovaElements\Core\DesignSystem;
	use NovaElements\Core\Settings;
	use NovaElements\Core\StylePresetRegistry;
	use NovaElements\Core\WidgetManager;
	use NovaElements\Core\WidgetRegistry;
	use NovaElements\TemplateLibrary\TemplateContent;
	use NovaElements\TemplateLibrary\TemplateRegistry;
	use NovaElements\Widgets\Heading\AdvancedHeadingWidget;

	define( 'ABSPATH', __DIR__ );
	define( 'ELEMENTOR_VERSION', '3.20.0' );
	require dirname( __DIR__ ) . '/vendor/autoload.php';

	function __( string $text ): string {
		return $text;
	}

	function esc_html( string $text ): string {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}

	function esc_attr( string $text ): string {
		return esc_html( $text );
	}

	function did_action( string $hook ): int {
		return 'elementor/loaded' === $hook ? (int) $GLOBALS['elementor_loaded'] : 0;
	}

	function get_option( string $key, mixed $default ): mixed {
		return $GLOBALS['options'][ $key ] ?? $default;
	}

	function sanitize_hex_color( string $value ): ?string {
		return preg_match( '/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/iD', $value ) ? $value : null;
	}

	function check( bool $passed, string $label ): void {
		if ( ! $passed ) {
			throw new \RuntimeException( $label );
		}
		echo "PASS: $label\n";
	}

	$options          = array();
	$elementor_loaded = false;
	$registry         = new WidgetRegistry();
	$settings         = new Settings( $registry );
	$manager          = new WidgetManager( $registry, $settings, new Assets( 'nova-elements.php', $settings ) );
	$widget           = new AdvancedHeadingWidget();
	$id               = $widget->get_name();

	check( isset( $manager->available_widgets()[ $id ] ), 'The widget name matches its registry key.' );
	check( array() === $manager->active_widgets(), 'An unavailable Elementor dependency blocks active widgets.' );
	$elementor_loaded = true;
	check( isset( $manager->active_widgets()[ $id ] ), 'A registered widget is enabled by default.' );
	$options[ Settings::WIDGETS ] = $settings->sanitize_widgets( array( $id => '0', 'unknown' => '1' ) );
	check( array( $id => '0' ) === $options[ Settings::WIDGETS ] && ! isset( $manager->active_widgets()[ $id ] ), 'An allowlisted disabled widget is inactive and unknown IDs are discarded.' );
	check( array( 'primary' => '#abc' ) === $settings->sanitize_palette( array( 'primary' => '#abc', 'accent' => '#fff;display:none', 'text' => array(), 'other' => '#fff' ) ), 'Palette rejects CSS injection and unknown or malformed values.' );
	check( array( 'preset' => 'editorial' ) === Settings::sanitize_preferences( array( 'preset' => array() ) ), 'Malformed preferences fall back to Editorial.' );
	$global = Settings::sanitize_global_style(
		array( 'theme' => 'dark', 'radius' => array(), 'heading_font' => 'serif', 'duration' => 'slow', 'motion' => 'reduce', 'unknown' => 'value', 'scale' => 'url(evil)' )
	);
	check( array( 'theme' => 'dark', 'heading_font' => 'serif', 'duration' => 'slow', 'motion' => 'reduce' ) === $global, 'Global settings validate profile, font, motion, and known fields.' );
	$vars = DesignSystem::declarations( $global, array( 'primary' => '#123456' ) );
	check( '#123456' === $vars['primary'] && '#172237' === $vars['surface'] && 'Georgia,serif' === $vars['font-heading'] && '0ms' === $vars['motion-duration'], 'Global theme, explicit palette, typography, and reduced-motion precedence.' );
	check( array() === DesignSystem::declarations( array(), array() ), 'An unchanged installation emits no new inline overrides.' );
	$inherited = DesignSystem::declarations( array( 'elementor_colors' => 'inherit', 'elementor_fonts' => 'inherit' ), array( 'accent' => '#123456' ) );
	check( str_starts_with( $inherited['primary'], 'var(--e-global-color-primary,' ) && '#123456' === $inherited['accent'] && str_starts_with( $inherited['font-body'], 'var(--e-global-typography-text-font-family,' ), 'Elementor inheritance respects existing Nova palette overrides.' );
	foreach ( $registry->all() as $widget_id => $definition ) {
		check( $definition['presets'] === array_keys( StylePresetRegistry::for_widget( $widget_id ) ), "$widget_id retains the existing five layout keys." );
	}

	$render = new \ReflectionMethod( $widget, 'render' );
	$options[ Settings::PREFERENCES ] = array( 'preset' => 'dark' );
	$widget->settings = array(
		'title'        => '<script>alert(1)</script>',
		'subtitle'     => '<img onerror="alert(1)">',
		'heading_tag'  => 'h2 onclick="alert(1)"',
		'style_preset' => 'site',
		'color_mode'   => 'gradient',
	);
	ob_start();
	$render->invoke( $widget );
	$html = ob_get_clean();
	check( str_contains( $html, 'nova-heading--dark nova-heading--gradient-text' ), 'Site default and gradient resolve into widget classes.' );
	check( str_contains( $html, '<h2 class=' ) && str_contains( $html, '&lt;script&gt;' ) && ! str_contains( $html, '<img' ), 'Rendering allowlists the tag and escapes title and subtitle.' );
	$widget->settings = array( 'title' => 'Hello', 'heading_tag' => 'h3', 'style_preset' => 'creative' );
	ob_start();
	$render->invoke( $widget );
	$html = ob_get_clean();
	check( str_contains( $html, 'nova-heading--creative' ) && str_contains( $html, '<h3 ' ) && ! str_contains( $html, 'nova-heading__subtitle' ), 'An explicit preset overrides site defaults and empty subtitles are omitted.' );

	$library = new TemplateRegistry( dirname( __DIR__ ) . '/library', $registry );
	check( count( $library->all() ) >= 3, 'The three reference templates are discoverable.' );
	check( array( 'premium-blog-grid' ) === array_keys( $library->filter( 'PREMIUM', 'section', 'blog' ) ), 'Catalog search combines case-insensitive text, type and category.' );
	check( ! $library->file( '../corporate-hero', 'manifest.json' ) && ! $library->file( 'corporate-hero', '../template.json' ), 'Catalog rejects path traversal.' );
	foreach ( $library->all() as $key => $manifest ) {
		$file = $library->file( $key, 'template.json' );
		$document = $file ? TemplateRegistry::read_json( $file, 262144 ) : null;
		check( is_array( $document ) && TemplateContent::valid( $document, $manifest ), "$key passes native document validation." );
		$desktop = $library->file( $key, 'preview.webp' );
		$tablet = $library->file( $key, 'preview-tablet.webp' );
		$mobile = $library->file( $key, 'preview-mobile.webp' );
		check( $desktop && $tablet && $mobile && IMAGETYPE_WEBP === ( getimagesize( $desktop )[2] ?? null )
			&& IMAGETYPE_WEBP === ( getimagesize( $tablet )[2] ?? null )
			&& IMAGETYPE_WEBP === ( getimagesize( $mobile )[2] ?? null ), "$key bundles desktop, tablet and mobile WebP previews." );
	}
	$blog = $library->find( 'premium-blog-grid' );
	$export = TemplateRegistry::read_json( $library->file( 'premium-blog-grid', 'template.json' ), 262144 );
	$query_settings = &$export['content'][0]['elements'][1]['settings'];
	$query_settings['pagination_mode'] = 'numbers';
	$query_settings['taxonomy_filters'] = array( array( '_id' => 'a123456', 'filter_taxonomy' => 'category', 'filter_terms' => '12', 'filter_operator' => 'IN' ) );
	$query_settings['query_meta_operator'] = '>';
	check( TemplateContent::valid( $export, $blog ), 'Template validation preserves advanced Blog Grid query controls.' );
	$query_settings['taxonomy_filters'][0]['unexpected'] = 'value';
	check( ! TemplateContent::valid( $export, $blog ), 'Template validation rejects unlisted taxonomy repeater fields.' );
	unset( $query_settings );
	foreach ( array( 'ne-blog-list', 'ne-blog-masonry', 'ne-blog-carousel', 'ne-featured-posts', 'ne-magazine-layout', 'ne-post-timeline', 'ne-related-posts' ) as $name ) {
		$variant = TemplateRegistry::read_json( $library->file( 'premium-blog-grid', 'template.json' ), 262144 );
		$variant['content'][0]['elements'][1]['widgetType'] = $name;
		$variant['content'][0]['elements'][1]['settings']['query_source'] = 'related';
		$manifest = array( 'required_widgets' => array( 'ne-advanced-heading', $name ) ) + $blog;
		if ( 'ne-blog-carousel' === $name ) {
			$variant['content'][0]['elements'][1]['settings']['carousel_dots'] = 'yes';
		}
		check( TemplateContent::valid( $variant, $manifest ), "$name is allowed in native templates with saved query settings." );
	}

	$meta_template = TemplateRegistry::read_json( $library->file( 'premium-blog-grid', 'template.json' ), 262144 );
	$meta_widget = &$meta_template['content'][0]['elements'][1];
	$meta_widget['widgetType'] = 'ne-advanced-post-meta';
	$meta_widget['settings'] = array( 'style_preset' => 'editorial', 'meta_items' => array( array( 'meta_field' => 'author', 'meta_link' => 'yes' ) ) );
	$meta_manifest = array( 'required_widgets' => array( 'ne-advanced-heading', 'ne-advanced-post-meta' ) ) + $blog;
	check( TemplateContent::valid( $meta_template, $meta_manifest ), 'Native templates accept the Advanced Post Meta field repeater.' );
	$meta_widget['settings']['meta_items'][0]['url'] = '/unexpected';
	check( ! TemplateContent::valid( $meta_template, $meta_manifest ), 'Advanced Post Meta template rows reject unknown settings.' );
	unset( $meta_widget );

	$cards = TemplateRegistry::read_json( $library->file( 'premium-blog-grid', 'template.json' ), 262144 );
	$cards['content'][0]['elements'][1]['widgetType'] = 'ne-profile-card';
	$cards['content'][0]['elements'][1]['settings'] = array( 'style_preset' => 'dark', 'media_type' => 'image', 'profile_source' => 'post', 'profile_post_id' => 123 );
	$cards_manifest = array( 'required_widgets' => array( 'ne-advanced-heading', 'ne-profile-card' ) ) + $blog;
	check( TemplateContent::valid( $cards, $cards_manifest ), 'Public-post profiles import without a bundled image.' );
	$cards_manifest['assets'] = array( array( 'element' => $cards['content'][0]['elements'][1]['id'] ) );
	check( ! TemplateContent::valid( $cards, $cards_manifest ), 'Public-post profiles reject an unused bundled image.' );

	$hero = $library->find( 'corporate-hero' );
	$payload = TemplateRegistry::read_json( $library->file( 'corporate-hero', 'template.json' ), 262144 );
	$unsafe = $payload;
	$unsafe['content'][0]['settings']['background_color'] = '#fff;display:none';
	check( ! TemplateContent::valid( $unsafe, $hero ), 'Template validation rejects CSS injection.' );
	$unsafe = $payload;
	$unsafe['content'][0]['elements'][0]['elements'][1]['settings']['link']['url'] = '//external.example/';
	check( ! TemplateContent::valid( $unsafe, $hero ), 'Template validation rejects protocol-relative links.' );
	$unsafe = $payload;
	$unsafe['content'][0]['elements'][0]['elements'][1]['widgetType'] = 'html';
	check( ! TemplateContent::valid( $unsafe, $hero ), 'Template validation rejects unlisted Elementor widgets.' );
}
