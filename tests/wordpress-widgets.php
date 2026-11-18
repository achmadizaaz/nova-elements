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
		if ( in_array( $id, array( 'ne-blog-list', 'ne-blog-masonry', 'ne-blog-carousel' ), true ) ) {
			nova_check( is_array( $widget->get_controls( 'query_source' ) ) && is_array( $widget->get_controls( 'post_title_color' ) ), "$id provides shared query and style controls" );
			nova_check( wp_style_is( 'nova-elements-blog-collection', 'registered' ) && array( 'nova-elements-blog-collection' ) === $widget->get_style_depends(), "$id declares its registered conditional stylesheet" );
			if ( 'ne-blog-carousel' === $id ) {
				$columns = $widget->get_controls( 'columns' );
				nova_check( ( ! empty( $columns['is_responsive'] ) || is_array( $widget->get_controls( 'columns_mobile' ) ) )
					&& is_array( $widget->get_controls( 'carousel_autoplay' ) ), 'Carousel has responsive slide and rotation controls' );
				nova_check( wp_script_is( 'nova-elements-blog-carousel', 'registered' ) && array( 'nova-elements-blog-carousel' ) === $widget->get_script_depends(), 'Carousel declares its registered conditional script' );
			} else {
				nova_check( ! $widget->get_script_depends(), "$id needs no frontend script" );
			}
		}
		$editorial_options = array( 'ne-featured-posts' => 'featured_support_excerpt', 'ne-magazine-layout' => 'magazine_support_images', 'ne-post-timeline' => 'timeline_date_format' );
		if ( isset( $editorial_options[ $id ] ) ) {
			nova_check( is_array( $widget->get_controls( 'query_source' ) ) && is_array( $widget->get_controls( $editorial_options[ $id ] ) ), "$id has query and layout controls" );
			nova_check( wp_style_is( 'nova-elements-blog-editorial', 'registered' ) && array( 'nova-elements-blog-editorial' ) === $widget->get_style_depends()
				&& ! $widget->get_script_depends(), "$id declares its conditional CSS and needs no script" );
			if ( 'ne-post-timeline' === $id ) {
				nova_check( is_array( $widget->get_controls( 'order' ) ) && ! $widget->get_controls( 'orderby' ), 'Timeline offers direction with publication date fixed as the sort field' );
			}
		}
		$context_options = array( 'ne-related-posts' => 'related_context_id', 'ne-post-categories' => 'terms_taxonomy', 'ne-author-profile' => 'profile_source' );
		if ( isset( $context_options[ $id ] ) ) {
			nova_check( is_array( $widget->get_controls( $context_options[ $id ] ) ) && ! $widget->get_script_depends()
				&& array( 'nova-elements-blog-context' ) === $widget->get_style_depends() && wp_style_is( 'nova-elements-blog-context', 'registered' ), "$id provides its context control and conditional CSS" );
			if ( 'ne-related-posts' === $id ) {
				nova_check( is_array( $widget->get_controls( 'related_taxonomy' ) ) && ! $widget->get_controls( 'query_source' ), 'Related Posts fixes its source and exposes taxonomy selection' );
			}
		}
		if ( 'ne-advanced-post-meta' === $id ) {
			nova_check( 'repeater' === $widget->get_controls( 'meta_items' )['type'] && is_array( $widget->get_controls( 'meta_reference_id' ) ), 'Advanced Post Meta exposes ordered fields and a public reference control' );
			nova_check( array( 'nova-elements-post-meta' ) === $widget->get_style_depends() && wp_style_is( 'nova-elements-post-meta', 'registered' )
				&& ! $widget->get_script_depends(), 'Advanced Post Meta declares its conditional stylesheet and no frontend script' );
		}
		$card_options = array(
			'ne-info-card' => 'card_note',
			'ne-service-card' => 'service_points',
			'ne-profile-card' => 'profile_source',
			'ne-team-member-card' => 'card_note',
			'ne-pricing-card' => 'price_text',
			'ne-image-box' => 'image_caption',
			'ne-icon-box' => 'feature_icon',
			'ne-flip-card' => 'back_description',
			'ne-hover-card' => 'hover_effect',
			'ne-feature-showcase' => 'showcase_features',
		);
		if ( isset( $card_options[ $id ] ) ) {
			nova_check( is_array( $widget->get_controls( $card_options[ $id ] ) ) && is_array( $widget->get_controls( 'media_position' ) )
				&& array( 'nova-elements-content-cards' ) === $widget->get_style_depends()
				&& wp_style_is( 'nova-elements-content-cards', 'registered' ) && ! $widget->get_script_depends(), "$id provides its controls and conditional stylesheet" );
		}
		$interactive_options = array(
			'ne-advanced-accordion' => 'allow_multiple', 'ne-advanced-tabs' => 'tabs_label', 'ne-modal-popup' => 'trigger_label',
			'ne-tooltip' => 'tooltip_text', 'ne-content-toggle' => 'panel_items', 'ne-interactive-hotspot' => 'markers',
			'ne-image-comparison' => 'initial_position', 'ne-off-canvas-content' => 'drawer_side',
			'ne-advanced-button-group' => 'group_items', 'ne-advanced-faq' => 'panel_items',
		);
		if ( isset( $interactive_options[ $id ] ) ) {
			$handle = 'nova-elements-interactive-panels';
			nova_check( is_array( $widget->get_controls( $interactive_options[ $id ] ) )
				&& array( $handle ) === $widget->get_style_depends()
				&& ( 'ne-advanced-button-group' === $id ? array() : array( $handle ) ) === $widget->get_script_depends()
				&& wp_style_is( $handle, 'registered' ) && wp_script_is( $handle, 'registered' ), "$id registers its controls and conditional assets" );
		}
		if ( 'media' === $definition['category'] ) {
			$comparison = 'ne-before-after-slider' === $id;
			$handle = $comparison ? 'nova-elements-interactive-panels' : 'nova-elements-media-gallery';
			nova_check( is_array( $widget->get_controls( $comparison ? 'before_image' : 'image_ratio' ) )
				&& array( $handle ) === $widget->get_style_depends() && array( $handle ) === $widget->get_script_depends()
				&& wp_style_is( $handle, 'registered' ) && wp_script_is( $handle, 'registered' ), "$id registers the expected media controls and conditional assets" );
		}
		foreach ( NovaElements\Core\StylePresetRegistry::for_widget( $id ) as $preset ) {
			$dimensions = getimagesize( dirname( __DIR__ ) . '/' . $preset['thumbnail'] );
			nova_check( 320 === $dimensions[0] && 200 === $dimensions[1] && 'image/webp' === $dimensions['mime'], "$id has a local 320 by 200 WebP layout preview" );
		}
	}
}

if ( 'media' === ( $argv[2] ?? '' ) ) {
	$photo = array( 'feature_image' => array( 'url' => '/fixture.webp' ), 'media_title' => 'Sample', 'media_caption' => '<b>Caption</b>', 'media_category' => 'Collection' );
	$video = array( 'video_url' => 'https://youtu.be/M7lc1UVf-VE' ) + $photo;
	$settings = array(
		'ne-image-gallery' => array( 'gallery_items' => array( $photo ) ),
		'ne-image-carousel' => array( 'gallery_items' => array( $photo, $photo ) ),
		'ne-video-gallery' => array( 'gallery_items' => array( $video ) ),
		'ne-video-popup' => $video,
		'ne-before-after-slider' => array( 'before_image' => array( 'url' => '/before.webp' ), 'after_image' => array( 'url' => '/after.webp' ) ),
		'ne-image-hover-effects' => array( 'gallery_items' => array( $photo ) ),
		'ne-logo-carousel' => array( 'gallery_items' => array( $photo, $photo ) ),
		'ne-filterable-gallery' => array( 'gallery_items' => array( $photo ) ),
		'ne-image-accordion' => array( 'gallery_items' => array( $photo ) ),
		'ne-media-showcase' => array( 'gallery_items' => array( $photo, $video ) ),
	);
	foreach ( $settings as $widget => $values ) {
		foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
			nova_check( str_contains( nova_render_widget( $widget, array( 'style_preset' => $preset ) + $values ), '--' . $preset ), "$widget renders $preset" );
		}
	}
	$html = nova_render_widget( 'ne-video-popup', $video );
	nova_check( ! str_contains( $html, '<iframe' ) && ! str_contains( $html, '<video' )
		&& str_contains( $html, 'youtube-nocookie.com/embed/M7lc1UVf-VE' ), 'Video Popup renders the local poster and defers the privacy-enhanced player' );
	$vimeo = NovaElements\Widgets\Media\VideoSource::external( 'https://vimeo.com/76979871' );
	nova_check( 'https://player.vimeo.com/video/76979871?dnt=1&autoplay=1' === $vimeo['url'], 'Vimeo playback requests the DNT parameter' );
	nova_check( null === NovaElements\Widgets\Media\VideoSource::external( 'https://www.youtube.com.invalid/watch?v=M7lc1UVf-VE' )
		&& null === NovaElements\Widgets\Media\VideoSource::external( 'http://youtu.be/M7lc1UVf-VE' ), 'External providers require an exact HTTPS host' );
	nova_check( '' === NovaElements\Widgets\Media\VideoSource::caption_track( 'https://external.example/captions.vtt' )
		&& home_url( '/captions.vtt' ) === NovaElements\Widgets\Media\VideoSource::caption_track( home_url( '/captions.vtt' ) ), 'Local captions accept only a same-site WebVTT URL' );
	$html = nova_render_widget( 'ne-image-gallery', array( 'lightbox' => '', 'gallery_items' => array_fill( 0, 13, $photo ) ) );
	nova_check( 12 === substr_count( $html, '<figure ' ) && ! str_contains( $html, '<a ' )
		&& str_contains( $html, '&lt;b&gt;Caption&lt;/b&gt;' ), 'Gallery escapes captions, limits its items and can disable lightbox' );
}

if ( 'interactive' === ( $argv[2] ?? '' ) ) {
	foreach ( array( 'ne-advanced-accordion', 'ne-advanced-tabs', 'ne-modal-popup' ) as $widget ) {
		foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
			$prefix = 'ne-modal-popup' === $widget ? 'nova-modal--' : 'nova-panels--';
			nova_check( str_contains( nova_render_widget( $widget, array( 'style_preset' => $preset ) ), $prefix . $preset ), "$widget renders $preset" );
		}
	}
	$rows = array(
		array( 'panel_label' => ' ', 'panel_content' => 'Omit this row' ),
		array( 'panel_label' => '<b>Overview</b>', 'panel_content' => '<strong>Keep this</strong><a href="javascript:alert(1)" onclick="alert(1)">Link</a>' ),
		array( 'panel_label' => 'Support', 'panel_content' => 'Find help' ),
	);
	$accordion = nova_render_widget( 'ne-advanced-accordion', array( 'panel_items' => $rows, 'initial_panel' => 0, 'allow_multiple' => 'yes' ) );
	nova_check( 2 === substr_count( $accordion, '<details ' ) && 2 === substr_count( $accordion, '<summary ' )
		&& ! str_contains( $accordion, ' open>' ) && str_contains( $accordion, 'data-multiple' ), 'Accordion supports an initially closed multiple-panel configuration' );
	nova_check( str_contains( $accordion, '&lt;b&gt;Overview&lt;/b&gt;' ) && str_contains( $accordion, '<strong>Keep this</strong>' )
		&& ! str_contains( $accordion, 'onclick=' ) && ! str_contains( $accordion, 'javascript:' ), 'Panel headings escape markup and rich content strips unsafe attributes' );
	$tabs = nova_render_widget( 'ne-advanced-tabs', array( 'panel_items' => $rows, 'initial_panel' => 8, 'tabs_label' => 'Project information' ) );
	nova_check( str_contains( $tabs, 'data-initial="2"' ) && str_contains( $tabs, 'aria-label="Project information" hidden' )
		&& 2 === substr_count( $tabs, '<section ' ) && ! str_contains( $tabs, 'Omit this row' ), 'Tabs clamp the initial index and expose content before enhancement' );
	$modal = nova_render_widget( 'ne-modal-popup', array(
		'title' => 'Project <launch>', 'link' => array( 'url' => '/project', 'is_external' => 'on' ),
	) );
	nova_check( str_contains( $modal, '<dialog ' ) && str_contains( $modal, 'aria-label="Project &lt;launch&gt;"' )
		&& str_contains( $modal, 'aria-haspopup="dialog" hidden' ) && str_contains( $modal, 'noopener noreferrer' )
		&& 1 === substr_count( $modal, '<a ' ), 'Modal has an accessible name, a native dialog, and a separate safe CTA' );
	$unsafe = nova_render_widget( 'ne-modal-popup', array( 'link' => array( 'url' => 'javascript:alert(1)' ) ) );
	nova_check( ! str_contains( $unsafe, '<a ' ), 'Modal drops an unsafe action URL' );

	$settings = array(
		'ne-tooltip' => array( 'tooltip_text' => '<b>Explanation</b>' ),
		'ne-content-toggle' => array( 'initial_panel' => 2 ),
		'ne-interactive-hotspot' => array( 'markers' => array( array( 'marker_label' => 'Entrance', 'marker_x' => -5, 'marker_y' => 108 ) ) ),
		'ne-image-comparison' => array( 'before_image' => array( 'url' => '/before.webp' ), 'after_image' => array( 'url' => '/after.webp' ), 'initial_position' => 108 ),
		'ne-off-canvas-content' => array( 'drawer_side' => 'start' ),
		'ne-advanced-button-group' => array( 'group_items' => array(
			array( 'button_label' => 'Open <brief>', 'button_link' => array( 'url' => '/brief', 'is_external' => 'on' ) ),
			array( 'button_label' => 'Unsafe', 'button_link' => array( 'url' => 'javascript:alert(1)' ) ),
		) ),
		'ne-advanced-faq' => array( 'initial_panel' => 0 ),
	);
	foreach ( $settings as $widget => $values ) {
		foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
			nova_check( str_contains( nova_render_widget( $widget, array( 'style_preset' => $preset ) + $values ), '--' . $preset ), "$widget renders $preset" );
		}
	}
	$tooltip = nova_render_widget( 'ne-tooltip', $settings['ne-tooltip'] );
	nova_check( str_contains( $tooltip, '&lt;b&gt;Explanation&lt;/b&gt;' ) && str_contains( $tooltip, 'nova-tooltip__trigger" hidden' ), 'Tooltip exposes escaped text before enhancement' );
	$toggle = nova_render_widget( 'ne-content-toggle', array( 'panel_items' => array(
		array( 'panel_label' => 'One' ), array( 'panel_label' => 'Two' ), array( 'panel_label' => 'Three' ),
	) ) + $settings['ne-content-toggle'] );
	nova_check( 2 === substr_count( $toggle, '<section ' ) && str_contains( $toggle, 'data-initial="2"' ), 'Content Toggle limits rows and keeps the initial state' );
	$hotspot = nova_render_widget( 'ne-interactive-hotspot', $settings['ne-interactive-hotspot'] );
	nova_check( str_contains( $hotspot, '--ne-x:0%;--ne-y:100%' ) && str_contains( $hotspot, 'aria-label="Entrance"' ), 'Hotspot clamps coordinates and names its marker' );
	$compare = nova_render_widget( 'ne-image-comparison', $settings['ne-image-comparison'] );
	nova_check( 2 === substr_count( $compare, '<figure ' ) && str_contains( $compare, 'value="100"' ), 'Comparison renders both figures and clamps the initial position' );
	nova_check( ! str_contains( nova_render_widget( 'ne-image-comparison', array( 'before_image' => array( 'url' => '/before.webp' ) ) ), 'type="range"' ), 'A single comparison image remains visible without a slider' );
	$drawer = nova_render_widget( 'ne-off-canvas-content', $settings['ne-off-canvas-content'] );
	nova_check( str_contains( $drawer, 'nova-drawer--start' ) && str_contains( $drawer, '<dialog ' ), 'Drawer keeps its edge and reuses the accessible dialog' );
	$group = nova_render_widget( 'ne-advanced-button-group', $settings['ne-advanced-button-group'] );
	nova_check( 1 === substr_count( $group, '<a ' ) && str_contains( $group, 'Open &lt;brief&gt;' ) && str_contains( $group, 'noopener noreferrer' ), 'Button group preserves independent safe links' );
	$faq = nova_render_widget( 'ne-advanced-faq', $settings['ne-advanced-faq'] );
	nova_check( 3 === substr_count( $faq, '<details ' ) && ! str_contains( $faq, ' open>' ) && str_contains( $faq, 'Who can access the workspace?' ), 'FAQ uses its own questions and honors the closed start setting' );
}

if ( 'blog' === ( $argv[2] ?? '' ) ) {
	$category = wp_insert_term( 'Nova QA ' . wp_generate_password( 8, false ), 'category' );
	if ( is_wp_error( $category ) ) {
		throw new RuntimeException( $category->get_error_message() );
	}
	$ids = array();
	$extra_terms = array();
	$authors = get_users( array( 'number' => 1, 'fields' => 'ID' ) );
	nova_check( ! empty( $authors ), 'Disposable site has a fixture author' );
	try {
		foreach ( array( 'Alpha story' => 'publish', 'Beta story' => 'publish', 'Private draft' => 'draft', 'Protected story' => 'publish' ) as $title => $status ) {
			$id = wp_insert_post(
				array(
					'post_title' => $title,
					'post_author' => (int) $authors[0],
					'post_status' => $status,
					'post_date' => sprintf( '2024-04-18 1%d:00:00', count( $ids ) ),
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
		unset( $_GET['ne_page_a701001'] );
		$numbered = nova_render_widget( 'ne-blog-grid', array( 'pagination_mode' => 'numbers' ) + $settings );
		nova_check( str_contains( $numbered, 'aria-current="page"' ) && str_contains( $numbered, 'another_filter=1' ), 'Numbered links mark the current page and preserve unrelated URL arguments' );
		foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
			$html = nova_render_widget( 'ne-blog-grid', array( 'style_preset' => $preset ) + $settings );
			nova_check( str_contains( $html, "nova-blog--$preset" ) && str_contains( $html, 'Alpha story' ), "$preset preset renders the legacy query" );
		}

		$engine = new NovaElements\Query\PostQuery();
		$visible = array_slice( $ids, 0, 2 );
		$base = array( 'include_ids' => implode( ',', $ids ), 'orderby' => 'title', 'order' => 'ASC', 'pagination' => 'yes' );
		$result = $engine->run( $base );
		nova_check( null === $result->error && $visible === wp_list_pluck( $result->posts, 'ID' ) && 2 === $result->total_posts, 'Structured result excludes drafts and password-protected posts' );
		$result = $engine->run( array( 'post_type' => 'revision' ) );
		nova_check( is_wp_error( $result->error ) && ! $result->posts, 'Internal post types fail closed' );
		$result = $engine->run( array( 'query_source' => 'manual' ) );
		nova_check( ! $result->posts, 'An empty manual selection returns no posts' );
		$result = $engine->run( array( 'query_source' => 'current' ) + $base, 2, get_post( $ids[0] ) );
		nova_check( array( $ids[0] ) === wp_list_pluck( $result->posts, 'ID' ) && 1 === $result->page, 'Current source selects only the supplied public context' );
		$result = $engine->run( array( 'query_source' => 'related' ) + $base, 1, get_post( $ids[0] ) );
		nova_check( array( $ids[1] ) === wp_list_pluck( $result->posts, 'ID' ), 'Related source uses shared terms and omits its context post' );
		$result = $engine->run( array( 'query_source' => 'archive' ) + $base, 1, null, get_term( $category['term_id'], 'category' ) );
		nova_check( $visible === wp_list_pluck( $result->posts, 'ID' ), 'Archive source uses the supplied public term' );
		$negative = array( 'filter_taxonomy' => 'category', 'filter_terms' => (string) $category['term_id'], 'filter_operator' => 'NOT IN' );
		$result = $engine->run( array( 'taxonomy_filters' => array( $negative ), 'taxonomy_relation' => 'OR' ) + $base );
		nova_check( null === $result->error && ! $result->posts, 'Taxonomy exclusion remains mandatory when inclusions use OR' );
		$parent = wp_insert_term( 'Nova QA ' . wp_generate_password( 8, false ), 'category' );
		nova_check( ! is_wp_error( $parent ), 'Created a second taxonomy branch' );
		$extra_terms[] = (int) $parent['term_id'];
		$child = wp_insert_term( 'Nova QA ' . wp_generate_password( 8, false ), 'category', array( 'parent' => $extra_terms[0] ) );
		nova_check( ! is_wp_error( $child ), 'Created a child term for the second branch' );
		$extra_terms[] = (int) $child['term_id'];
		wp_set_object_terms( $ids[1], $extra_terms[1], 'category', true );
		$branches = array( 'filter_taxonomy' => 'category', 'filter_terms' => $category['term_id'] . ',' . $extra_terms[0], 'filter_operator' => 'AND', 'filter_children' => 'yes' );
		$result = $engine->run( array( 'taxonomy_filters' => array( $branches ) ) + $base );
		nova_check( array( $ids[1] ) === wp_list_pluck( $result->posts, 'ID' ), 'Hierarchical AND matches a post in both selected branches' );
		$result = $engine->run( array( 'include_authors' => (string) get_post( $ids[0] )->post_author ) + $base );
		nova_check( $visible === wp_list_pluck( $result->posts, 'ID' ), 'Author include uses the fixture author' );
		$result = $engine->run( array( 'exclude_authors' => (string) get_post( $ids[0] )->post_author ) + $base );
		nova_check( ! $result->posts, 'Author exclusion uses the fixture author' );
		$today = get_the_date( 'Y-m-d', $ids[0] );
		$result = $engine->run( array( 'date_period' => 'custom', 'date_start' => $today, 'date_end' => $today ) + $base );
		nova_check( $visible === wp_list_pluck( $result->posts, 'ID' ), 'Custom date range includes both boundary days' );
		$result = $engine->run( array( 'date_period' => 'custom', 'date_start' => '2025-02-30' ) + $base );
		nova_check( is_wp_error( $result->error ), 'Invalid calendar dates fail closed' );
		$args = $engine->arguments( array( 'orderby' => 'rand' ) + $base, 2 );
		nova_check( isset( $args['orderby']['date'], $args['orderby']['ID'] ) && 2 === $args['paged'], 'Random order falls back to a deterministic paginated sort' );

		register_post_meta( 'post', 'nova_qa_score', array( 'type' => 'number', 'single' => true ) );
		register_post_meta( 'post', '_nova_qa_private', array( 'type' => 'string', 'single' => true ) );
		update_post_meta( $ids[0], 'nova_qa_score', 5.5 );
		update_post_meta( $ids[1], 'nova_qa_score', 5.1 );
		$allow = static fn(): array => array( 'nova_qa_score', '_nova_qa_private' );
		add_filter( 'nova_elements_queryable_meta', $allow );
		try {
			$meta = array( 'query_meta_key' => 'nova_qa_score', 'query_meta_operator' => '>', 'query_meta_format' => 'number', 'query_meta_value' => '5.2' );
			$result = $engine->run( $meta + $base );
			nova_check( array( $ids[0] ) === wp_list_pluck( $result->posts, 'ID' ), 'Numeric meta comparison retains decimal precision' );
			$result = $engine->run( array( 'orderby' => 'meta_value_num', 'query_sort_key' => 'nova_qa_score' ) + $base );
			nova_check( array_reverse( $visible ) === wp_list_pluck( $result->posts, 'ID' ), 'Numeric meta sorting retains decimal precision' );
			update_post_meta( $ids[1], 'nova_qa_score', 9 );
			$result = $engine->run( $meta + $base );
			nova_check( $visible === wp_list_pluck( $result->posts, 'ID' ), 'A direct meta edit is reflected in the next query' );
			$result = $engine->run( array( 'query_meta_key' => '_nova_qa_private' ) + $meta + $base );
			nova_check( is_wp_error( $result->error ) && ! $result->posts, 'Protected metadata cannot be queried even if supplied by the allowlist' );
		} finally {
			remove_filter( 'nova_elements_queryable_meta', $allow );
			unregister_post_meta( 'post', 'nova_qa_score' );
			unregister_post_meta( 'post', '_nova_qa_private' );
		}
		// The public title contains literal markup in storage to exercise output escaping.
		wp_update_post( array( 'ID' => $ids[0], 'post_title' => 'Alpha <img src=x onerror=alert(1)>' ) );
		$collections = array(
			'list' => 'ne-blog-list',
			'masonry' => 'ne-blog-masonry',
			'carousel' => 'ne-blog-carousel',
			'featured' => 'ne-featured-posts',
			'magazine' => 'ne-magazine-layout',
			'timeline' => 'ne-post-timeline',
		);
		foreach ( $collections as $layout => $name ) {
			$html = nova_render_widget( $name, array() );
			nova_check( str_contains( $html, 'nova-blog--' . $layout ), "$name renders with default settings" );
			foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
				$show = array( 'posts_per_page' => 6, 'style_preset' => $preset, 'carousel_label' => 'Stories & more' ) + $settings;
				$html = nova_render_widget( $name, $show );
				nova_check( str_contains( $html, 'nova-blog--' . $preset ) && str_contains( $html, 'nova-blog--' . $layout )
					&& str_contains( $html, 'Alpha' ) && str_contains( $html, 'Beta story' )
					&& ! str_contains( $html, 'Private draft' ) && ! str_contains( $html, 'Protected story' ) && ! str_contains( $html, '<img src=x' ), "$name $preset queries only public posts and escapes its title" );
				if ( 'carousel' === $layout ) {
					nova_check( str_contains( $html, '<nova-post-carousel' ) && str_contains( $html, 'aria-label="Stories &amp; more"' ), 'Carousel markup has an escaped accessible name' );
				}
			}
			$_GET['ne_page_a701001'] = '2';
			$other = nova_render_widget( $name, $settings, 'a701002' );
			$paged = nova_render_widget( $name, $settings );
			nova_check( str_contains( $other, 'Alpha' ) && str_contains( $paged, 'Beta story' )
				&& ! str_contains( $paged, '<nova-post-carousel' ), "$name retains independent server pagination and renders single results without a carousel" );
			unset( $_GET['ne_page_a701001'] );
		}
		$editorial = array( 'posts_per_page' => 6, 'pagination' => '' ) + $settings;
		$featured = nova_render_widget( 'ne-featured-posts', $editorial );
		nova_check( 1 === substr_count( $featured, 'nova-blog__card--lead' ) && 1 === substr_count( $featured, 'class="nova-blog__excerpt"' )
			&& str_contains( $featured, 'nova-blog__support' ), 'Featured Posts separates the lead and suppresses supporting excerpts by default' );
		nova_check( 2 === substr_count( nova_render_widget( 'ne-featured-posts', array( 'featured_support_excerpt' => 'yes' ) + $editorial ), 'class="nova-blog__excerpt"' ), 'Supporting excerpts can be enabled' );
		stick_post( $ids[1] );
		try {
			$sticky = nova_render_widget( 'ne-featured-posts', array( 'sticky_posts' => 'only' ) + $editorial );
			nova_check( str_contains( $sticky, 'Beta story' ) && ! str_contains( $sticky, 'Alpha' ) && ! str_contains( $sticky, 'nova-blog__support' ), 'Featured Posts reuses sticky filtering and handles a single lead' );
		} finally {
			unstick_post( $ids[1] );
		}
		$magazine = nova_render_widget( 'ne-magazine-layout', $editorial );
		nova_check( 1 === substr_count( $magazine, 'nova-blog__card--lead' ) && str_contains( $magazine, 'nova-blog__support' ), 'Magazine marks one lead and groups the supporting stories' );
		$timeline = nova_render_widget( 'ne-post-timeline', array( 'order' => 'DESC', 'timeline_date_format' => 'year' ) + $editorial );
		nova_check( str_contains( $timeline, '<ol class="nova-blog__grid" role="list">' ) && 2 === substr_count( $timeline, '<li class="nova-blog__entry">' )
			&& 2 === substr_count( $timeline, '>2024</time>' ) && strpos( $timeline, 'Beta story' ) < strpos( $timeline, 'Alpha' ), 'Timeline uses semantic ordered markup, date formatting and chronological direction' );
		nova_check( ! str_contains( nova_render_widget( 'ne-post-timeline', array( 'date' => '' ) + $editorial ), '<time' ), 'Timeline can hide dates without changing chronological order' );
		nova_check( ( $GLOBALS['post'] ?? null ) === $original_post, 'Query preserves the global post context' );
	} finally {
		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}
		foreach ( array_reverse( $extra_terms ) as $term_id ) {
			wp_delete_term( $term_id, 'category' );
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

if ( 'context' === ( $argv[2] ?? '' ) ) {
	require_once ABSPATH . 'wp-admin/includes/user.php';
	$original_query = $GLOBALS['wp_query'];
	$users = $posts = $terms = array();
	$taxonomy = 'nova_context_qa';
	register_taxonomy( $taxonomy, 'post', array( 'public' => true ) );
	try {
		foreach ( array( 'Public Contributor', 'Hidden Contributor' ) as $name ) {
			$login = 'nova-qa-' . strtolower( wp_generate_password( 10, false ) );
			$id = wp_insert_user( array( 'user_login' => $login, 'user_pass' => wp_generate_password(),
				'user_email' => $login . '@example.test', 'display_name' => $name, 'description' => 'A public biography with useful information.', 'role' => 'subscriber' ) );
			nova_check( ! is_wp_error( $id ), 'Created a disposable contributor' );
			$users[] = $id;
		}
		foreach ( array( 'Related topic', 'Other topic', 'Unused topic' ) as $name ) {
			$term = wp_insert_term( $name, $taxonomy, array( 'description' => 'A useful topic description.' ) );
			nova_check( ! is_wp_error( $term ), 'Created a disposable term' );
			$terms[] = (int) $term['term_id'];
		}
		foreach ( array( 'Context article', 'Related article', 'Unrelated article', 'Password article', 'Second recommendation' ) as $index => $title ) {
			$id = wp_insert_post( array( 'post_status' => 'publish', 'post_title' => $title,
				'post_author' => $users[ 3 === $index ? 1 : 0 ], 'post_password' => 3 === $index ? 'fixture' : '' ), true );
			nova_check( ! is_wp_error( $id ), 'Created a disposable context article' );
			$posts[] = $id;
			wp_set_object_terms( $id, $terms[ 2 === $index ? 1 : 0 ], $taxonomy );
		}
		$GLOBALS['wp_query'] = new WP_Query( array( 'p' => $posts[0] ) );
		$related_settings = array( 'related_taxonomy' => $taxonomy, 'pagination' => '' );
		$term_settings = array( 'terms_taxonomy' => $taxonomy, 'taxonomy_source' => 'current', 'show_description' => 'yes' );
		foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
			$style = array( 'style_preset' => $preset );
			$html = nova_render_widget( 'ne-related-posts', $style + $related_settings );
			nova_check( str_contains( $html, "nova-blog--$preset" ) && str_contains( $html, 'Related article' )
				&& ! str_contains( $html, 'Context article' ) && ! str_contains( $html, 'Unrelated article' )
				&& ! str_contains( $html, 'Password article' ), "Related Posts $preset matches terms and excludes the context and nonpublic articles" );
			$html = nova_render_widget( 'ne-post-categories', $style + $term_settings );
			nova_check( str_contains( $html, "nova-terms--$preset" ) && str_contains( $html, 'Related topic' )
				&& str_contains( $html, 'A useful topic description.' ) && ! str_contains( $html, 'Other topic' ), "Post Categories $preset uses the current article terms" );
			$html = nova_render_widget( 'ne-author-profile', $style );
			nova_check( str_contains( $html, "nova-author--$preset" ) && str_contains( $html, 'Public Contributor' )
				&& str_contains( $html, 'A public biography' ) && ! str_contains( $html, '@example.test' )
				&& ! str_contains( $html, '<img' ), "Author Profile $preset exposes public fields and defaults to initials" );
		}

		$GLOBALS['wp_query'] = new WP_Query( array( 'taxonomy' => $taxonomy, 'term' => 'related-topic' ) );
		$html = nova_render_widget( 'ne-post-categories', array( 'terms_taxonomy' => $taxonomy, 'terms_order' => 'count' ) );
		nova_check( str_contains( $html, 'aria-current="page"' ) && ! str_contains( $html, 'Unused topic' )
			&& strpos( $html, 'Related topic' ) < strpos( $html, 'Other topic' ), 'Taxonomy links mark the active archive, hide empty terms and order by count' );
		$html = nova_render_widget( 'ne-post-categories', array( 'terms_taxonomy' => $taxonomy, 'hide_empty' => '', 'show_count' => '' ) );
		nova_check( str_contains( $html, 'Unused topic' ) && ! str_contains( $html, 'nova-terms__count' ), 'Empty terms and counts can be controlled independently' );
		register_taxonomy( 'nova_private_qa', 'post', array( 'public' => false ) );
		try {
			$hidden_term = wp_insert_term( 'Internal topic', 'nova_private_qa' );
			nova_check( ! is_wp_error( $hidden_term ), 'Created a private taxonomy term' );
			$html = nova_render_widget( 'ne-post-categories', array( 'terms_taxonomy' => 'nova_private_qa', 'hide_empty' => '' ) );
			nova_check( str_contains( $html, 'No categories match' ) && ! str_contains( $html, 'Internal topic' ), 'Internal taxonomies are omitted' );
		} finally {
			if ( ! is_wp_error( $hidden_term ) ) {
				wp_delete_term( $hidden_term['term_id'], 'nova_private_qa' );
			}
			unregister_taxonomy( 'nova_private_qa' );
		}

		$reference = array( 'related_context_id' => $posts[0] ) + $related_settings;
		$paginate = array( 'pagination' => 'yes', 'posts_per_page' => 1, 'orderby' => 'title', 'order' => 'ASC' ) + $reference;
		$_GET['ne_page_a701001'] = '2';
		try {
			$html = nova_render_widget( 'ne-related-posts', $paginate );
			$other = nova_render_widget( 'ne-related-posts', $paginate, 'a701002' );
			nova_check( str_contains( $html, 'Second recommendation' ) && str_contains( $html, 'Page 2 of 2' )
				&& str_contains( $other, 'Related article' ), 'Related Posts pagination remains independent across instances' );
		} finally {
			unset( $_GET['ne_page_a701001'] );
		}
		nova_check( str_contains( nova_render_widget( 'ne-related-posts', $reference ), 'Related article' ), 'Explicit public reference works outside singular context' );
		nova_check( str_contains( nova_render_widget( 'ne-related-posts', array( 'related_context_id' => $posts[3] ) + $related_settings ), 'No posts match' ), 'Password-protected references fail closed' );
		$manual = array( 'profile_source' => 'manual', 'profile_author_id' => $users[0], 'show_bio' => '', 'show_archive' => '' );
		$html = nova_render_widget( 'ne-author-profile', $manual );
		nova_check( str_contains( $html, 'Public Contributor' ) && ! str_contains( $html, 'A public biography' )
			&& ! str_contains( $html, 'View articles' ), 'Manual author supports visibility toggles' );
		nova_check( str_contains( nova_render_widget( 'ne-author-profile', array( 'profile_author_id' => $users[1] ) + $manual ), 'No public author profile' ), 'Manual profiles require a publicly readable article' );

		$GLOBALS['wp_query'] = new WP_Query( array( 'author' => $users[0] ) );
		nova_check( str_contains( nova_render_widget( 'ne-author-profile', array() ), 'Public Contributor' ), 'Author archive uses its public contributor' );
	} finally {
		$GLOBALS['wp_query'] = $original_query;
		foreach ( $posts as $id ) {
			wp_delete_post( $id, true );
		}
		foreach ( $terms as $id ) {
			wp_delete_term( $id, $taxonomy );
		}
		foreach ( $users as $id ) {
			wp_delete_user( $id );
		}
		unregister_taxonomy( $taxonomy );
	}
}

if ( 'meta' === ( $argv[2] ?? '' ) ) {
	$original_query = $GLOBALS['wp_query'];
	$posts = array();
	$comment = 0;
	$taxonomy = 'nova_meta_private';
	$keys = array( 'nova_meta_edition' => 'string', 'nova_meta_zero' => 'integer', 'nova_meta_query_only' => 'string', '_nova_meta_private' => 'string' );
	$allow_display = static fn(): array => array( 'nova_meta_edition', 'nova_meta_zero', '_nova_meta_private' );
	$allow_query = static fn(): array => array( 'nova_meta_query_only' );
	add_filter( 'nova_elements_displayable_meta', $allow_display );
	add_filter( 'nova_elements_queryable_meta', $allow_query );
	register_taxonomy( $taxonomy, 'post', array( 'public' => false ) );
	foreach ( $keys as $key => $type ) {
		register_post_meta( 'post', $key, array( 'type' => $type, 'single' => true ) );
	}
	try {
		$authors = get_users( array( 'number' => 1, 'fields' => 'ID' ) );
		nova_check( ! empty( $authors ), 'Disposable site has a public author fixture' );
		$term = wp_insert_term( 'Internal tag', $taxonomy );
		nova_check( ! is_wp_error( $term ), 'Created a hidden taxonomy term' );
		foreach ( array( 'Public article' => 'publish', 'Password article' => 'publish', 'Draft article' => 'draft' ) as $title => $status ) {
			$id = wp_insert_post( array(
				'post_title' => $title, 'post_status' => $status, 'post_author' => (int) $authors[0],
				'post_date' => '2024-04-18 11:00:00', 'post_content' => '<p>' . str_repeat( 'Nova ', 221 ) . '</p>',
				'post_password' => 'Password article' === $title ? 'fixture' : '', 'comment_status' => 'open',
			), true );
			nova_check( ! is_wp_error( $id ), 'Created a disposable metadata article' );
			$posts[] = $id;
		}
		$comment = wp_insert_comment( array( 'comment_post_ID' => $posts[0], 'comment_content' => 'Public test comment.', 'comment_approved' => 1 ) );
		nova_check( $comment > 0, 'Created an approved comment' );
		wp_set_object_terms( $posts[0], (int) $term['term_id'], $taxonomy );
		$tag = wp_insert_term( 'Meta QA ' . wp_generate_password( 8, false ), 'post_tag' );
		nova_check( ! is_wp_error( $tag ), 'Created a public tag' );
		wp_set_object_terms( $posts[0], (int) $tag['term_id'], 'post_tag' );
		update_post_meta( $posts[0], 'nova_meta_edition', '<b>Edition three</b><script>alert(1)</script>' );
		update_post_meta( $posts[0], 'nova_meta_zero', 0 );
		update_post_meta( $posts[0], 'nova_meta_query_only', 'Query-only value' );
		update_post_meta( $posts[0], '_nova_meta_private', 'Protected value' );
		$GLOBALS['wp_query'] = new WP_Query( array( 'p' => $posts[0] ) );

		$settings = array( 'meta_date_format' => 'iso', 'meta_items' => array(
			array( 'meta_field' => 'published' ),
			array( 'meta_field' => 'modified' ),
			array( 'meta_field' => 'author', 'meta_link' => 'yes' ),
			array( 'meta_field' => 'terms', 'meta_taxonomy' => 'category', 'meta_link' => 'yes' ),
			array( 'meta_field' => 'comments', 'meta_link' => 'yes' ),
			array( 'meta_field' => 'reading' ),
			array( 'meta_field' => 'custom', 'meta_key' => 'nova_meta_edition', 'meta_label' => '<img src=x onerror=alert(1)>Edition' ),
			array( 'meta_field' => 'custom', 'meta_key' => 'nova_meta_zero', 'meta_label' => 'Volume' ),
		) );
		foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
			$html = nova_render_widget( 'ne-advanced-post-meta', array( 'style_preset' => $preset ) + $settings );
			nova_check( str_contains( $html, "nova-post-meta--$preset" ) && str_contains( $html, '<dl' )
				&& 8 === substr_count( $html, '<dt' ) && 2 === substr_count( $html, '<time' ) && str_contains( $html, '>2024-04-18</time>' ), "$preset has semantic metadata and machine-readable dates" );
			nova_check( str_contains( $html, '2 minutes' ) && str_contains( $html, '>1</a>' ) && str_contains( $html, 'Edition three' )
				&& str_contains( $html, '>0</dd>' ) && ! str_contains( $html, '<img' ) && ! str_contains( $html, '<script>' ), "$preset renders bounded public values and escapes labels" );
		}
		$html = nova_render_widget( 'ne-advanced-post-meta', array( 'meta_items' => array( array( 'meta_field' => 'terms', 'meta_taxonomy' => 'post_tag' ) ) ) );
		nova_check( str_contains( $html, '>Tags</dt>' ) && str_contains( $html, 'Meta QA' ), 'Taxonomy rows use the selected taxonomy label' );

		$blocked = array( 'meta_items' => array(
			array( 'meta_field' => 'custom', 'meta_key' => 'nova_meta_query_only' ),
			array( 'meta_field' => 'custom', 'meta_key' => '_nova_meta_private' ),
			array( 'meta_field' => 'terms', 'meta_taxonomy' => $taxonomy ),
			array( 'meta_field' => 'bogus' ),
		) );
		nova_check( '' === trim( nova_render_widget( 'ne-advanced-post-meta', $blocked ) ), 'Private taxonomies, protected metadata and query-only fields are omitted' );
		nova_check( '' === trim( nova_render_widget( 'ne-advanced-post-meta', array( 'meta_items' => array() ) ) ), 'An explicitly empty field list remains empty' );

		$GLOBALS['wp_query'] = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 1 ) );
		nova_check( '' === trim( nova_render_widget( 'ne-advanced-post-meta', array() ) ), 'Archives have no implicit article metadata' );
		$html = nova_render_widget( 'ne-advanced-post-meta', array( 'meta_reference_id' => $posts[0] ) );
		nova_check( 4 === substr_count( $html, '<dt' ) && str_contains( $html, '2 minutes' ), 'Explicit public reference works outside the article and uses the default fields' );
		foreach ( array( $posts[1], $posts[2], 'invalid' ) as $id ) {
			nova_check( '' === trim( nova_render_widget( 'ne-advanced-post-meta', array( 'meta_reference_id' => $id ) ) ), 'Invalid and nonpublic references produce no public metadata' );
		}
	} finally {
		$GLOBALS['wp_query'] = $original_query;
		if ( $comment ) {
			wp_delete_comment( $comment, true );
		}
		foreach ( $posts as $id ) {
			wp_delete_post( $id, true );
		}
		if ( isset( $term ) && is_array( $term ) ) {
			wp_delete_term( $term['term_id'], $taxonomy );
		}
		if ( isset( $tag ) && is_array( $tag ) ) {
			wp_delete_term( $tag['term_id'], 'post_tag' );
		}
		unregister_taxonomy( $taxonomy );
		foreach ( $keys as $key => $type ) {
			unregister_post_meta( 'post', $key );
		}
		remove_filter( 'nova_elements_displayable_meta', $allow_display );
		remove_filter( 'nova_elements_queryable_meta', $allow_query );
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

if ( 'cards' === ( $argv[2] ?? '' ) ) {
	foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
		$html = nova_render_widget( 'ne-feature-showcase', array( 'style_preset' => $preset ) );
		nova_check( str_contains( $html, 'nova-feature--' . $preset )
			&& 3 === substr_count( $html, 'class="nova-feature__item"' ), "Feature Showcase renders the three default features in $preset" );
	}
	$features = array( array( 'feature_title' => '' ), array( 'feature_title' => '<em>First</em>' ) );
	foreach ( range( 2, 7 ) as $number ) {
		$features[] = array( 'feature_description' => "Feature $number" );
	}
	$showcase = nova_render_widget( 'ne-feature-showcase', array(
		'eyebrow' => '', 'title' => '', 'description' => '', 'media_type' => 'none',
		'link' => array( 'url' => 'javascript:alert(1)' ), 'showcase_features' => $features,
	) );
	nova_check( 6 === substr_count( $showcase, 'class="nova-feature__item"' )
		&& str_contains( $showcase, '&lt;em&gt;First&lt;/em&gt;' )
		&& strpos( $showcase, 'First' ) < strpos( $showcase, 'Feature 2' )
		&& ! str_contains( $showcase, 'Feature 7' ) && ! str_contains( $showcase, '<a ' ), 'Feature Showcase preserves order, skips blanks, caps rows and escapes feature text' );
	nova_check( ! str_contains( nova_render_widget( 'ne-feature-showcase', array( 'showcase_features' => array() ) ), '<ul ' ), 'Feature Showcase omits an empty feature list' );
	foreach ( array( 'ne-icon-box', 'ne-flip-card', 'ne-hover-card' ) as $widget ) {
		foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
			nova_check( str_contains( nova_render_widget( $widget, array( 'style_preset' => $preset ) ), 'nova-feature--' . $preset ), "$widget renders $preset" );
		}
	}
	$icon_box = nova_render_widget( 'ne-icon-box', array( 'media_position' => 'start', 'link' => array( 'url' => '/guide' ) ) );
	nova_check( str_contains( $icon_box, 'nova-feature--media-start' ) && str_contains( $icon_box, 'class="nova-feature__icon" aria-hidden="true"' )
		&& 1 === substr_count( $icon_box, '<a ' ), 'Icon Box uses decorative media and a separate action' );
	$flip = nova_render_widget( 'ne-flip-card', array(
		'flip_label' => '<script>Reveal</script>',
		'back_title' => 'Reverse heading',
		'back_description' => '<b>Private label</b>',
		'link' => array( 'url' => '/details' ),
	) );
	nova_check( str_contains( $flip, '<details class="nova-feature__flip">' ) && str_contains( $flip, '<summary ' )
		&& str_contains( $flip, '&lt;script&gt;Reveal&lt;/script&gt;' ) && str_contains( $flip, '&lt;b&gt;Private label&lt;/b&gt;' )
		&& strpos( $flip, '</details>' ) < strpos( $flip, '<a ' ), 'Flip Card renders an escaped native disclosure with a separate link' );
	nova_check( ! str_contains( nova_render_widget( 'ne-flip-card', array( 'back_title' => '', 'back_description' => '' ) ), '<details' ), 'Flip Card omits an empty reverse face' );
	$hover = nova_render_widget( 'ne-hover-card', array( 'hover_effect' => 'outline', 'description' => 'Visible on touch' ) );
	nova_check( str_contains( $hover, 'nova-feature--effect-outline' ) && str_contains( $hover, '>Visible on touch</p>' ), 'Hover Card exposes its content with an optional visual accent' );
	nova_check( ! str_contains( nova_render_widget( 'ne-hover-card', array( 'hover_effect' => 'injected extra-class' ) ), 'extra-class' ), 'Hover Card rejects an invalid effect class' );
	foreach ( array( 'ne-team-member-card', 'ne-pricing-card', 'ne-image-box' ) as $widget ) {
		foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
			nova_check( str_contains( nova_render_widget( $widget, array( 'style_preset' => $preset ) ), 'nova-feature--' . $preset ), "$widget renders $preset" );
		}
	}
	$team = nova_render_widget( 'ne-team-member-card', array(
		'title' => 'Sam Rivera',
		'card_note' => '<b>Design</b>',
		'link' => array( 'url' => 'mailto:sam@example.test' ),
	) );
	nova_check( str_contains( $team, 'aria-hidden="true">SR' ) && str_contains( $team, '&lt;b&gt;Design&lt;/b&gt;' )
		&& 1 === substr_count( $team, '<a ' ) && str_contains( $team, 'mailto:sam@example.test' ), 'Team Card renders initials, escaped department and a single contact link' );
	$pricing = nova_render_widget( 'ne-pricing-card', array(
		'price_text' => '0',
		'price_previous' => '<b>20</b>',
		'price_period' => 'per year',
		'service_points' => "One\nTwo\n\nThree\nFour\nFive\nSix\nSeven",
		'link' => array( 'url' => 'javascript:alert(1)' ),
	) );
	nova_check( str_contains( $pricing, '>0</span>' ) && str_contains( $pricing, 'Previous price' )
		&& str_contains( $pricing, '&lt;b&gt;20&lt;/b&gt;' ) && str_contains( $pricing, 'per year' ), 'Pricing preserves zero, announces the previous price and escapes content' );
	nova_check( 6 === substr_count( $pricing, '<li>' ) && ! str_contains( $pricing, '<a ' ), 'Pricing caps features and rejects an unsafe action URL' );
	$box = nova_render_widget( 'ne-image-box', array(
		'media_type' => 'image',
		'feature_image' => array( 'url' => 'https://example.test/photo.webp' ),
		'image_caption' => '<b>Photo credit</b>',
		'media_position' => 'end',
		'link' => array( 'url' => '/projects', 'is_external' => 'on' ),
	) );
	nova_check( str_contains( $box, '<figure ' ) && str_contains( $box, '&lt;b&gt;Photo credit&lt;/b&gt;</figcaption>' )
		&& str_contains( $box, 'nova-feature--media-end' ) && 1 === substr_count( $box, '<a ' )
		&& str_contains( $box, 'noopener noreferrer' ), 'Image Box uses a figure and caption with a separate safe action' );
	nova_check( ! str_contains( nova_render_widget( 'ne-image-box', array( 'media_type' => 'none', 'image_caption' => 'Unused' ) ), '<figcaption' ), 'Image Box omits the caption without an image' );
	foreach ( array( 'info', 'service', 'profile' ) as $kind ) {
		$id = 'ne-' . $kind . '-card';
		$default = nova_render_widget( $id, array() );
		nova_check( str_contains( $default, 'nova-feature--' . $kind ) && str_contains( $default, '<h3' ), "$id renders default content" );
		if ( 'profile' === $kind ) {
			nova_check( str_contains( $default, 'nova-feature__image--initials' ) && str_contains( $default, '>AM</div>' ), 'Profile uses local initials when the portrait is unavailable' );
		}
		foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
			$html = nova_render_widget( $id, array(
				'style_preset' => $preset,
				'title' => '<img src=x onerror=alert(1)>',
				'description' => 'Public description',
				'heading_tag' => 'script',
				'media_type' => 'none',
				'cta_label' => 'Read more',
				'link' => array( 'url' => 'https://example.test/card', 'is_external' => 'on', 'nofollow' => 'on', 'custom_attributes' => 'onclick|alert(1)' ),
			) );
			nova_check( str_contains( $html, 'nova-feature--' . $preset ) && str_contains( $html, '<h3' ) && ! str_contains( $html, '<img' )
				&& 1 === substr_count( $html, '<a ' ) && str_contains( $html, 'rel="noopener noreferrer nofollow"' )
				&& ! str_contains( $html, 'onclick=' ), "$id $preset renders an escaped card with one safe action" );
		}
		$html = nova_render_widget( $id, array( 'media_type' => 'none', 'cta_label' => 'Broken', 'link' => array( 'url' => 'javascript:alert(1)' ) ) );
		nova_check( ! str_contains( $html, '<a ' ), "$id omits unsafe actions" );
	}
	$info = nova_render_widget( 'ne-info-card', array( 'card_note' => '<script>unsafe</script>', 'media_type' => 'none' ) );
	nova_check( str_contains( $info, 'nova-feature__note' ) && ! str_contains( $info, '<script>' ), 'Info Card escapes its note' );
	$service = nova_render_widget( 'ne-service-card', array( 'service_points' => "Plan
0
<img src=x>
Build
Test
Launch
Extra", 'media_type' => 'none' ) );
	nova_check( 6 === substr_count( $service, '<li>' ) && str_contains( $service, '<li>0</li>' ) && ! str_contains( $service, '<img' )
		&& ! str_contains( $service, 'Extra' ), 'Service benefits keep source order, escape markup and render at most six entries' );
	$profile = nova_render_widget( 'ne-profile-card', array( 'media_type' => 'image', 'media_position' => 'end', 'title' => 'Mara Tan' ) );
	nova_check( str_contains( $profile, 'nova-feature--media-end' ) && str_contains( $profile, '>MT</div>' ), 'Profile initials honor the media position setting' );
	$posts = array();
	try {
		foreach ( array( 'Public staff profile' => '', 'Protected staff profile' => 'fixture' ) as $title => $password ) {
			$post = wp_insert_post( array( 'post_title' => $title, 'post_status' => 'publish', 'post_excerpt' => 'The public role description.', 'post_password' => $password ), true );
			nova_check( ! is_wp_error( $post ), 'Created a disposable profile reference' );
			$posts[] = $post;
		}
		$settings = array( 'profile_source' => 'post', 'cta_label' => 'View profile', 'title' => 'Manual name', 'description' => 'Manual description' );
		$html = nova_render_widget( 'ne-profile-card', array( 'profile_post_id' => $posts[0] ) + $settings );
		nova_check( str_contains( $html, 'Public staff profile' ) && str_contains( $html, 'The public role description.' )
			&& str_contains( $html, esc_url( get_permalink( $posts[0] ) ) ) && ! str_contains( $html, 'Manual name' ), 'Profile maps public post title, excerpt and permalink' );
		nova_check( '' === trim( nova_render_widget( 'ne-profile-card', array( 'profile_post_id' => $posts[1] ) + $settings ) ), 'Profile does not display password-protected post content' );
	} finally {
		foreach ( $posts as $post ) {
			wp_delete_post( $post, true );
		}
	}
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

if ( 'team' === ( $argv[2] ?? '' ) ) {
	$items = array(
		array( 'title' => '<img src=x onerror=alert(1)>', 'role' => 'Board', 'footer_start' => 'Since 2021', 'image' => array( 'url' => 'javascript:alert(2)' ) ),
		array( 'title' => 'Second member', 'role' => 'Staff <b>', 'footer_end' => 'Programs' ),
		array( 'title' => 'Third member', 'role' => 'board' ),
		array( 'title' => '', 'role' => 'Empty item' ),
	);
	foreach ( array( 'editorial', 'corporate', 'gradient', 'creative', 'dark' ) as $preset ) {
		$html = nova_render_widget( 'ne-team', array( 'style_preset' => $preset, 'items' => $items, 'show_filter' => 'yes', 'show_badge' => 'yes', 'heading_tag' => 'h4' ) );
		nova_check( str_contains( $html, 'nova-team--' . $preset ) && substr_count( $html, '<h4 class="nova-team__title"' ) === 3, "$preset preset and empty items skipped" );
		nova_check( substr_count( $html, 'data-nova-team-filter=' ) === 3 && str_contains( $html, 'data-nova-team-filter="board"' ) && str_contains( $html, 'aria-pressed="true"' ), 'Roles deduplicate into an All button and one button per role' );
		nova_check( substr_count( $html, 'class="nova-team__badge"' ) === 3 && str_contains( $html, 'Staff &lt;b&gt;' ) && str_contains( $html, '&lt;img' ) && ! str_contains( $html, '<img' ) && ! str_contains( $html, 'Empty item' ), 'Escaped badges and titles, unsafe image omitted' );
		nova_check( substr_count( $html, 'class="nova-team__footer"' ) === 2, 'Footer renders only when one of its columns has text' );
	}
	$html = nova_render_widget( 'ne-team', array( 'items' => array( $items[0], $items[2] ), 'show_badge' => '' ) );
	nova_check( substr_count( $html, 'data-nova-team-filter=' ) === 2 && ! str_contains( $html, 'nova-team__badge' ), 'A single shared role keeps the filter and the badge toggle is honoured' );
	$html = nova_render_widget( 'ne-team', array( 'items' => array( array( 'title' => 'No role' ) ) ) );
	nova_check( ! str_contains( $html, 'nova-team__filters' ), 'Members without a role hide the filter' );
}
