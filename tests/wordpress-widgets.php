<?php
/**
 * Run with: php tests/wordpress-widgets.php /path/to/disposable/wordpress/wp-load.php blog
 */

if ( 'cli' !== PHP_SAPI || ! is_file( $argv[1] ?? '' ) ) {
	exit( 1 );
}
if ( 'presets' === ( $argv[2] ?? '' ) ) {
	// Elementor omits panel metadata when booted for frontend rendering.
	define( 'WP_ADMIN', true );
}
require $argv[1];

function nova_check( bool $condition, string $message ): void {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
	echo "PASS: $message\n";
}

function nova_render_widget( string $name, array $settings, string $id = 'a701001' ): string {
	$widget = Elementor\Plugin::instance()->elements_manager->create_element_instance(
		array( 'id' => $id, 'elType' => 'widget', 'widgetType' => $name, 'settings' => $settings, 'elements' => array() )
	);
	nova_check( $widget instanceof Elementor\Widget_Base, "$name registered" );
	ob_start();
	try {
		( new ReflectionMethod( $widget, 'render' ) )->invoke( $widget );
		return ob_get_contents();
	} finally {
		ob_end_clean();
	}
}

if ( 'presets' === ( $argv[2] ?? '' ) ) {
	$registry = new NovaElements\Core\WidgetRegistry();
	$manager = Elementor\Plugin::instance()->widgets_manager;
	foreach ( $registry->all() as $id => $definition ) {
		$widget = $manager->get_widget_types( $id );
		nova_check( $widget instanceof Elementor\Widget_Base, "$id registered" );
		$control = $widget->get_controls( 'style_preset' );
		nova_check( 'nova_visual_style' === $control['type'] && 'site' === $control['default'] && 'style' === $control['tab'], "$id uses the visual control and retains the saved setting key" );
		nova_check( array_merge( array( 'site' ), $definition['presets'] ) === array_keys( $control['options'] ), "$id exposes Site default and the five existing layout IDs" );
		foreach ( NovaElements\Core\StylePresetRegistry::for_widget( $id ) as $preset ) {
			$dimensions = getimagesize( dirname( __DIR__ ) . '/' . $preset['thumbnail'] );
			nova_check( 320 === $dimensions[0] && 200 === $dimensions[1] && 'image/webp' === $dimensions['mime'], "$id has a local 320 by 200 WebP layout preview" );
		}
	}
}

if ( 'blog' === ( $argv[2] ?? '' ) ) {
	$category = wp_insert_term( 'Nova QA ' . wp_generate_password( 8, false ), 'category' );
	if ( is_wp_error( $category ) ) {
		throw new RuntimeException( $category->get_error_message() );
	}
	$ids = array();
	try {
		foreach ( array( 'Alpha story' => 'publish', 'Beta story' => 'publish', 'Private draft' => 'draft', 'Protected story' => 'publish' ) as $title => $status ) {
			$id = wp_insert_post(
				array(
					'post_title' => $title,
					'post_status' => $status,
					'post_excerpt' => 'A real excerpt for the article, prepared from the published WordPress fixture.',
					'post_password' => 'Protected story' === $title ? 'fixture-only' : '',
					'post_category' => array( $category['term_id'] ),
				),
				true
			);
			if ( is_wp_error( $id ) || ! $id ) {
				throw new RuntimeException( 'Unable to create a disposable test post.' );
			}
			$ids[] = $id;
		}
		$settings = array(
			'taxonomy' => 'category',
			'term_ids' => (string) $category['term_id'],
			'posts_per_page' => 1,
			'orderby' => 'title',
			'order' => 'ASC',
			'pagination' => 'yes',
			'excerpt' => 'yes',
			'date' => 'yes',
			'style_preset' => 'editorial',
		);
		$_SERVER['REQUEST_URI'] = '/?another_filter=1';
		$original_post = $GLOBALS['post'] ?? null;
		$first = nova_render_widget( 'ne-blog-grid', $settings );
		nova_check( str_contains( $first, 'Alpha story' ) && ! str_contains( $first, 'Beta story' ) && str_contains( $first, 'Page 1 of 2' ), 'Taxonomy, sorting and published-only query' );
		nova_check( str_contains( $first, 'A real excerpt' ) && str_contains( $first, '<time' ) && str_contains( $first, 'another_filter=1' ), 'Excerpt, metadata and preserved URL arguments' );
		$_GET['ne_page_a701001'] = '2';
		$second = nova_render_widget( 'ne-blog-grid', $settings );
		$other = nova_render_widget( 'ne-blog-grid', $settings, 'a701002' );
		nova_check( str_contains( $second, 'Beta story' ) && str_contains( $second, 'Page 2 of 2' ) && str_contains( $other, 'Alpha story' ), 'Independent pagination across two instances' );
		$_GET['ne_page_a701001'] = array( '2' );
		nova_check( str_contains( nova_render_widget( 'ne-blog-grid', $settings ), 'Alpha story' ), 'Malformed pagination input falls back to page one' );
		nova_check( str_contains( nova_render_widget( 'ne-blog-grid', array( 'taxonomy' => 'invalid' ) + $settings ), 'No posts match' ), 'Invalid taxonomies fail closed' );
		$selection = array( 'include_ids' => implode( ',', $ids ), 'exclude_ids' => (string) $ids[0], 'pagination' => '', 'offset' => 0 ) + $settings;
		nova_check( str_contains( nova_render_widget( 'ne-blog-grid', $selection ), 'Beta story' ), 'Explicit exclusions take precedence over includes' );
		$offset = array( 'pagination' => '', 'offset' => 1 ) + $settings;
		nova_check( str_contains( nova_render_widget( 'ne-blog-grid', $offset ), 'Beta story' ), 'Offset applies only to an unpaginated query' );
		nova_check( ( $GLOBALS['post'] ?? null ) === $original_post, 'Query preserves the global post context' );
	} finally {
		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}
		wp_delete_term( $category['term_id'], 'category' );
	}

	register_post_type( 'nova_qa_article', array( 'public' => true ) );
	register_taxonomy( 'nova_qa_topic', 'nova_qa_article', array( 'public' => true ) );
	$id = 0;
	$term = wp_insert_term( 'Nova custom topic', 'nova_qa_topic' );
	try {
		nova_check( ! is_wp_error( $term ), 'Created a custom taxonomy fixture' );
		$id = wp_insert_post( array( 'post_type' => 'nova_qa_article', 'post_status' => 'publish', 'post_title' => 'Custom article' ), true );
		nova_check( is_int( $id ) && $id > 0, 'Created a custom post type fixture' );
		wp_set_object_terms( $id, (int) $term['term_id'], 'nova_qa_topic' );
		$html = nova_render_widget( 'ne-blog-grid', array( 'post_type' => 'nova_qa_article', 'taxonomy' => 'nova_qa_topic', 'term_ids' => (string) $term['term_id'], 'term' => 'yes' ) );
		nova_check( str_contains( $html, 'Custom article' ) && str_contains( $html, 'Nova custom topic' ), 'Custom post type and taxonomy badge render' );
	} finally {
		if ( is_int( $id ) && $id > 0 ) {
			wp_delete_post( $id, true );
		}
		if ( ! is_wp_error( $term ) ) {
			wp_delete_term( $term['term_id'], 'nova_qa_topic' );
		}
		unregister_taxonomy( 'nova_qa_topic' );
		unregister_post_type( 'nova_qa_article' );
	}
}

if ( 'feature' === ( $argv[2] ?? '' ) ) {
	foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
		$html = nova_render_widget(
			'ne-feature-card',
			array(
				'style_preset' => $preset,
				'title' => '<img src=x onerror=alert(1)>',
				'heading_tag' => 'script',
				'media_type' => 'none',
				'cta_label' => 'Read more',
				'link' => array( 'url' => 'https://example.org/approach', 'is_external' => 'on', 'nofollow' => 'on', 'custom_attributes' => 'onclick|alert(1)' ),
			)
		);
		nova_check( str_contains( $html, 'nova-feature--' . $preset ) && str_contains( $html, '<h3' ) && ! str_contains( $html, '<img' ), "$preset preset and escaped content" );
		nova_check( str_contains( $html, 'rel="noopener noreferrer nofollow"' ) && ! str_contains( $html, 'onclick=' ), 'Only safe link attributes rendered' );
	}
	$html = nova_render_widget( 'ne-feature-card', array( 'link' => array( 'url' => 'javascript:alert(1)' ), 'media_type' => 'image', 'feature_image' => array( 'url' => 'javascript:alert(2)' ) ) );
	nova_check( ! str_contains( $html, '<a ' ) && ! str_contains( $html, '<img' ), 'Unsafe links and media URLs are omitted' );
}

if ( 'button' === ( $argv[2] ?? '' ) ) {
	foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
		$html = nova_render_widget(
			'ne-advanced-button',
			array(
				'style_preset' => $preset,
				'label' => '<b>Open the collection</b>',
				'detail' => 'Supporting label',
				'icon_position' => 'start',
				'link' => array( 'url' => '#collection', 'is_external' => '', 'nofollow' => 'on' ),
			)
		);
		nova_check( str_contains( $html, 'nova-button--' . $preset ) && str_contains( $html, 'nova-button--icon-start' ), "$preset preset and explicit icon position" );
		nova_check( substr_count( $html, '<a ' ) === 1 && str_contains( $html, 'href="#collection"' ) && str_contains( $html, 'rel="nofollow"' ), 'Single usable link with safe attributes' );
		nova_check( str_contains( $html, '&lt;b&gt;Open' ) && ! str_contains( $html, '<b>' ) && str_contains( $html, 'aria-hidden="true"' ), 'Escaped label and decorative icon' );
	}
	$html = nova_render_widget( 'ne-advanced-button', array( 'link' => array( 'url' => 'javascript:alert(1)' ), 'icon_type' => 'none' ) );
	nova_check( ! str_contains( $html, '<a ' ) && str_contains( $html, '<span class="nova-button__surface"' ) && ! str_contains( $html, 'nova-button__icon' ), 'Unavailable link becomes plain content with no focus target' );
}

if ( 'testimonials' === ( $argv[2] ?? '' ) ) {
	$items = array(
		array( 'quote' => '<img src=x onerror=alert(1)>', 'name' => 'First author', 'rating' => '900' ),
		array( 'quote' => 'Second review', 'name' => 'Second author', 'rating' => '0', 'portrait' => array( 'url' => 'javascript:alert(2)' ) ),
		array( 'quote' => '', 'name' => 'Empty item' ),
	);
	foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
		$html = nova_render_widget( 'ne-testimonials', array( 'style_preset' => $preset, 'items' => $items, 'layout' => 'carousel', 'carousel_label' => 'Reviews from clients' ) );
		nova_check( str_contains( $html, 'nova-testimonials--' . $preset ) && substr_count( $html, '<blockquote' ) === 2, "$preset preset and empty items skipped" );
		nova_check( str_contains( $html, 'tabindex="0" role="region" aria-label="Reviews from clients"' ) && str_contains( $html, 'aria-label="5 out of 5 stars"' ), 'Named keyboard viewport and bounded accessible rating' );
		nova_check( str_contains( $html, '&lt;img' ) && ! str_contains( $html, '<img' ) && ! str_contains( $html, 'Empty item' ), 'Quote escaped and unsafe portrait omitted' );
	}
	$html = nova_render_widget( 'ne-testimonials', array( 'layout' => 'carousel', 'items' => array( $items[0] ) ) );
	nova_check( str_contains( $html, 'nova-testimonials--grid' ) && ! str_contains( $html, 'tabindex=' ), 'A single quote does not create a redundant carousel' );
}
