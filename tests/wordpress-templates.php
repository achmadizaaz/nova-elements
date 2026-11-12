<?php
/**
 * Run against a disposable WordPress installation with Nova and Elementor active.
 */

use Elementor\Plugin as Elementor;
use NovaElements\Core\Assets;
use NovaElements\Core\Settings;
use NovaElements\Core\WidgetManager;
use NovaElements\Core\WidgetRegistry;
use NovaElements\TemplateLibrary\TemplateContent;
use NovaElements\TemplateLibrary\TemplateImporter;
use NovaElements\TemplateLibrary\TemplateRegistry;

if ( 'cli' !== PHP_SAPI || empty( $argv[1] ) || ! is_file( $argv[1] ) ) {
	exit( "Usage: php tests/wordpress-templates.php /path/to/wp-load.php\n" );
}
require $argv[1];

function nova_template_check( bool $pass, string $label ): void {
	if ( ! $pass ) {
		throw new RuntimeException( $label );
	}
	echo "PASS: $label\n";
}

$widgets   = new WidgetRegistry();
$settings  = new Settings( $widgets );
$manager   = new WidgetManager( $widgets, $settings, new Assets( dirname( __DIR__ ) . '/nova-elements.php', $settings ) );
$registry  = new TemplateRegistry( dirname( __DIR__ ) . '/library', $widgets );
$importer  = new TemplateImporter( $registry, $manager );
$templates = array();
$media     = array();
$previous  = get_option( Settings::WIDGETS, false );
$admin     = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
if ( ! $admin ) {
	throw new RuntimeException( 'The disposable site must have an administrator.' );
}

wp_set_current_user( 0 );
nova_template_check( 'permission' === $importer->import( 'corporate-hero' )->get_error_code(), 'Unauthenticated imports are denied.' );
wp_set_current_user( (int) $admin[0] );
$source = Elementor::instance()->templates_manager->get_source( 'local' );

try {
	update_option( Settings::WIDGETS, array( 'ne-advanced-heading' => '0' ) );
	nova_template_check( 'widgets' === $importer->import( 'corporate-hero' )->get_error_code(), 'Disabled required widgets block import.' );
	update_option( Settings::WIDGETS, array() );
	foreach ( array( 'corporate-hero', 'premium-blog-grid', 'modern-features', 'modern-features' ) as $id ) {
		$manifest = $registry->find( $id );
		$original = TemplateRegistry::read_json( $registry->file( $id, 'template.json' ), 262144 );
		$result = $importer->import( $id );
		nova_template_check( ! is_wp_error( $result ), $id . ' imports' . ( is_wp_error( $result ) ? ' (' . $result->get_error_message() . ')' : '.' ) );
		$templates[] = $result;
		$document = Elementor::instance()->documents->get( $result );
		$saved = $document->get_elements_data();
		nova_template_check( 'publish' === get_post_status( $result ) && $original['content'][0]['id'] !== $saved[0]['id']
			&& TemplateContent::widget_counts( $original['content'] ) === TemplateContent::widget_counts( $saved ), "$id stores the native document with remapped IDs." );
		$data = $source->get_data( array( 'template_id' => $result ) );
		nova_template_check( ! empty( $data['content'] ) && TemplateContent::widget_counts( $saved ) === TemplateContent::widget_counts( $data['content'] ), "$id provides native insertion data." );
		if ( 'modern-features' === $id ) {
			$image = $saved[0]['elements'][1]['elements'][0]['elements'][0]['settings']['feature_image'];
			$media[] = (int) $image['id'];
			nova_template_check( 'image/webp' === get_post_mime_type( $image['id'] ) && is_file( get_attached_file( $image['id'] ) )
				&& ! empty( get_post_meta( $image['id'], '_wp_attachment_image_alt', true ) ), 'Bundled media resolves to a local WordPress attachment with alternative text.' );
		}
	}
	nova_template_check( $media[0] === $media[1], 'Repeating the import reuses the bundled attachment.' );
	$test_root = trailingslashit( get_temp_dir() ) . 'nova-template-qa-' . wp_generate_uuid4();
	$bundle = $test_root . '/post-collections';
	nova_template_check( wp_mkdir_p( $bundle ), 'Created a disposable template bundle.' );
	try {
		$manifest = $registry->find( 'premium-blog-grid' );
		$manifest['id'] = 'post-collections';
		$manifest['required_widgets'] = array( 'ne-blog-list', 'ne-blog-masonry', 'ne-blog-carousel', 'ne-featured-posts', 'ne-magazine-layout', 'ne-post-timeline', 'ne-related-posts', 'ne-post-categories', 'ne-author-profile', 'ne-advanced-post-meta' );
		$document = TemplateRegistry::read_json( $registry->file( 'premium-blog-grid', 'template.json' ), 262144 );
		$container = &$document['content'][0];
		$grid = $container['elements'][1];
		$container['elements'] = array();
		foreach ( $manifest['required_widgets'] as $index => $widget_name ) {
			$widget = $grid;
			$widget['id'] = sprintf( '8a0000%d', $index );
			$widget['widgetType'] = $widget_name;
			$widget['settings']['style_preset'] = array( 'editorial', 'creative', 'dark' )[ $index % 3 ];
			$widget['settings']['date_period'] = 'week';
			$widget['settings']['pagination_mode'] = 'numbers';
			if ( 'ne-blog-carousel' === $widget_name ) {
				$widget['settings']['carousel_dots'] = 'yes';
				$widget['settings']['columns_mobile'] = 1;
			}
			$widget['settings'] += match ( $widget_name ) {
				'ne-featured-posts' => array( 'featured_support_excerpt' => 'yes' ),
				'ne-magazine-layout' => array( 'magazine_support_images' => '' ),
				'ne-post-timeline' => array( 'timeline_date_format' => 'month' ),
				'ne-related-posts' => array( 'related_context_id' => 123 ),
				default => array(),
			};
			$widget['settings'] = match ( $widget_name ) {
				'ne-post-categories' => array( 'style_preset' => 'corporate', 'terms_taxonomy' => 'category', 'show_count' => 'yes' ),
				'ne-author-profile' => array( 'style_preset' => 'dark', 'profile_source' => 'manual', 'profile_author_id' => 123, 'show_archive' => '' ),
				'ne-advanced-post-meta' => array( 'style_preset' => 'gradient', 'meta_reference_id' => 123, 'meta_date_format' => 'iso',
					'meta_items' => array( array( '_id' => 'b080001', 'meta_field' => 'published', 'meta_label' => 'First published' ), array( '_id' => 'b080002', 'meta_field' => 'author', 'meta_link' => 'yes' ) ) ),
				default => $widget['settings'],
			};
			$container['elements'][] = $widget;
		}
		unset( $container );
		nova_template_check( (bool) file_put_contents( $bundle . '/manifest.json', wp_json_encode( $manifest ) )
			&& (bool) file_put_contents( $bundle . '/template.json', wp_json_encode( $document ) ), 'Wrote the native collection fixture.' );
		$test_registry = new TemplateRegistry( $test_root, $widgets );
		$result = ( new TemplateImporter( $test_registry, $manager ) )->import( 'post-collections' );
		nova_template_check( ! is_wp_error( $result ), 'All ten collection and context variants import as a native Nova template.' );
		$templates[] = $result;
		$saved = Elementor::instance()->documents->get( $result )->get_elements_data();
		foreach ( $document['content'][0]['elements'] as $index => $expected ) {
			$actual = $saved[0]['elements'][ $index ];
			nova_template_check( $expected['id'] !== $actual['id'] && $expected['widgetType'] === $actual['widgetType']
				&& $expected['settings'] == $actual['settings'], $actual['widgetType'] . ' retains its preset, query, responsive and navigation settings.' );
		}
		$manifest['required_widgets'] = array( 'ne-info-card', 'ne-service-card', 'ne-profile-card', 'ne-team-member-card', 'ne-pricing-card', 'ne-image-box', 'ne-icon-box', 'ne-flip-card', 'ne-hover-card', 'ne-feature-showcase' );
		$manifest['assets'] = array(
			array( 'element' => '8b00003', 'file' => 'architecture.webp', 'alt' => 'A modern roof viewed from below.' ),
			array( 'element' => '8b00007', 'file' => 'architecture.webp', 'alt' => 'A modern roof viewed from below.' ),
			array( 'element' => '8b00010', 'file' => 'architecture.webp', 'alt' => 'A modern roof viewed from below.' ),
			array( 'element' => '8b00011', 'file' => 'architecture.webp', 'alt' => 'A modern roof viewed from below.' ),
		);
		$document['content'][0]['elements'] = array();
		$cards = array(
			array( 'ne-info-card', array( 'style_preset' => 'editorial', 'card_note' => 'Keep for reference', 'media_position' => 'start' ) ),
			array( 'ne-service-card', array( 'style_preset' => 'creative', 'service_points' => "Research
Design
Launch" ) ),
			array( 'ne-profile-card', array( 'style_preset' => 'dark', 'profile_source' => 'manual', 'media_type' => 'image', 'feature_image' => array( 'id' => 0, 'url' => '' ) ) ),
			array( 'ne-profile-card', array( 'style_preset' => 'corporate', 'profile_source' => 'post', 'profile_post_id' => 123, 'media_type' => 'image' ) ),
			array( 'ne-team-member-card', array( 'style_preset' => 'corporate', 'media_type' => 'image', 'card_note' => 'Research' ) ),
			array( 'ne-pricing-card', array( 'style_preset' => 'dark', 'price_text' => '0', 'price_previous' => '19', 'price_period' => 'per month', 'service_points' => 'Email support' ) ),
			array( 'ne-image-box', array( 'style_preset' => 'editorial', 'media_type' => 'image', 'image_caption' => 'Exterior detail' ) ),
			array( 'ne-icon-box', array( 'style_preset' => 'corporate', 'media_type' => 'icon', 'feature_icon' => array( 'value' => 'fas fa-compass', 'library' => 'fa-solid' ) ) ),
			array( 'ne-flip-card', array( 'style_preset' => 'dark', 'flip_label' => 'Open panel', 'back_title' => 'Reverse title', 'back_description' => 'Reverse detail' ) ),
			array( 'ne-hover-card', array( 'style_preset' => 'gradient', 'media_type' => 'image', 'hover_effect' => 'zoom' ) ),
			array( 'ne-feature-showcase', array( 'style_preset' => 'dark', 'media_type' => 'image', 'showcase_features' => array(
				array( '_id' => 'abc1234', 'feature_title' => 'Plan', 'feature_description' => 'Share the roadmap' ),
				array( '_id' => 'abc1235', 'feature_title' => 'Review', 'feature_description' => 'Gather feedback' ),
			) ) ),
		);
		foreach ( $cards as $index => list( $name, $settings ) ) {
			$document['content'][0]['elements'][] = array( 'id' => sprintf( '8b000%02d', $index + 1 ), 'elType' => 'widget', 'widgetType' => $name, 'settings' => $settings, 'elements' => array() );
		}
		$invalid = $document;
		$invalid['content'][0]['elements'][10]['settings']['showcase_features'][0]['unknown'] = 'Disallowed';
		nova_template_check( ! NovaElements\TemplateLibrary\TemplateContent::valid( $invalid, $manifest ), 'Feature Showcase rejects unknown imported row fields.' );
		$image = $registry->file( 'modern-features', 'media/architecture.webp' );
		nova_template_check( $image && wp_mkdir_p( $bundle . '/media' ) && copy( $image, $bundle . '/media/architecture.webp' )
			&& file_put_contents( $bundle . '/manifest.json', wp_json_encode( $manifest ) )
			&& file_put_contents( $bundle . '/template.json', wp_json_encode( $document ) ), 'Wrote the card template fixture and local media.' );
		$cards_registry = new TemplateRegistry( $test_root, $widgets );
		$result = ( new TemplateImporter( $cards_registry, $manager ) )->import( 'post-collections' );
		nova_template_check( ! is_wp_error( $result ), 'All ten Batch 8B cards import as native Elementor widgets.' );
		$templates[] = $result;
		$saved = Elementor::instance()->documents->get( $result )->get_elements_data()[0]['elements'];
		nova_template_check( 'Keep for reference' === $saved[0]['settings']['card_note']
			&& "Research
Design
Launch" === $saved[1]['settings']['service_points']
			&& 'post' === $saved[3]['settings']['profile_source'], 'Card detail fields and dynamic profile source survive import.' );
		nova_template_check( (int) $saved[2]['settings']['feature_image']['id'] === $media[0], 'Manual Profile Card binds the bundled media to a local attachment.' );
		nova_template_check( 'Research' === $saved[4]['settings']['card_note'] && '0' === $saved[5]['settings']['price_text']
			&& '19' === $saved[5]['settings']['price_previous'] && 'Exterior detail' === $saved[6]['settings']['image_caption']
			&& (int) $saved[6]['settings']['feature_image']['id'] === $media[0], 'Team, pricing and image settings survive native import with local media.' );
		nova_template_check( 'fas fa-compass' === $saved[7]['settings']['feature_icon']['value'] && 'Open panel' === $saved[8]['settings']['flip_label']
			&& 'Reverse detail' === $saved[8]['settings']['back_description'] && 'zoom' === $saved[9]['settings']['hover_effect']
			&& (int) $saved[9]['settings']['feature_image']['id'] === $media[0], 'Icon, reverse face and hover settings survive native import with local media.' );
		nova_template_check( $cards[10][1]['showcase_features'] === $saved[10]['settings']['showcase_features']
			&& (int) $saved[10]['settings']['feature_image']['id'] === $media[0], 'Feature Showcase preserves repeater order and binds the bundled image.' );

		$manifest['assets'] = array(
			array( 'element' => '8c00006', 'file' => 'architecture.webp', 'alt' => 'A modern roof viewed from below.' ),
			array( 'element' => '8c00007', 'control' => 'before_image', 'file' => 'architecture.webp', 'alt' => 'A modern roof viewed from below.' ),
			array( 'element' => '8c00007', 'control' => 'after_image', 'file' => 'comparison.webp', 'alt' => 'A preview of the features section.' ),
		);
		$panel_rows = array( array( '_id' => 'abc1245', 'panel_label' => 'Overview', 'panel_content' => 'Read the brief' ) );
		$interactive = array(
			array( 'ne-advanced-accordion', array( 'style_preset' => 'dark', 'panel_items' => $panel_rows, 'initial_panel' => 0, 'allow_multiple' => 'yes' ) ),
			array( 'ne-advanced-tabs', array( 'style_preset' => 'corporate', 'panel_items' => $panel_rows, 'tabs_label' => 'Project information' ) ),
			array( 'ne-modal-popup', array( 'style_preset' => 'creative', 'title' => 'Project brief', 'trigger_label' => 'Open brief', 'link' => array( 'url' => '/project' ) ) ),
			array( 'ne-tooltip', array( 'style_preset' => 'editorial', 'trigger_label' => 'Term', 'tooltip_text' => 'A definition' ) ),
			array( 'ne-content-toggle', array( 'style_preset' => 'gradient', 'initial_panel' => 2, 'panel_items' => $panel_rows ) ),
			array( 'ne-interactive-hotspot', array( 'style_preset' => 'dark', 'markers' => array( array( 'marker_label' => 'Entrance', 'marker_x' => 40, 'marker_y' => '55' ) ) ) ),
			array( 'ne-image-comparison', array( 'style_preset' => 'creative', 'initial_position' => 45 ) ),
			array( 'ne-off-canvas-content', array( 'style_preset' => 'dark', 'drawer_side' => 'start', 'link' => array( 'url' => '/project' ) ) ),
			array( 'ne-advanced-button-group', array( 'style_preset' => 'corporate', 'group_items' => array( array( 'button_label' => 'Project', 'button_link' => array( 'url' => '/project' ) ) ) ) ),
			array( 'ne-advanced-faq', array( 'style_preset' => 'editorial', 'initial_panel' => 0, 'panel_items' => $panel_rows ) ),
		);
		$manifest['required_widgets'] = array_column( $interactive, 0 );
		$document['content'][0]['elements'] = array();
		foreach ( $interactive as $index => list( $name, $settings ) ) {
			$document['content'][0]['elements'][] = array( 'id' => sprintf( '8c000%02d', $index + 1 ), 'elType' => 'widget', 'widgetType' => $name, 'settings' => $settings, 'elements' => array() );
		}
		$invalid = $document;
		$invalid['content'][0]['elements'][0]['settings']['panel_items'][0]['unknown'] = 'Disallowed';
		nova_template_check( ! NovaElements\TemplateLibrary\TemplateContent::valid( $invalid, $manifest ), 'Interactive templates reject unknown panel fields.' );
		$invalid = $document;
		$invalid['content'][0]['elements'][8]['settings']['group_items'][0]['button_link']['url'] = '//unsafe.example';
		nova_template_check( ! NovaElements\TemplateLibrary\TemplateContent::valid( $invalid, $manifest ), 'Group actions reject protocol-relative URLs.' );
		$invalid_manifest = $manifest;
		$invalid_manifest['assets'][2]['control'] = 'before_image';
		nova_template_check( ! NovaElements\TemplateLibrary\TemplateContent::valid( $document, $invalid_manifest ), 'Comparison rejects duplicate image slots.' );
		$preview = $registry->file( 'modern-features', 'preview.webp' );
		nova_template_check( $preview && copy( $preview, $bundle . '/media/comparison.webp' )
			&& file_put_contents( $bundle . '/manifest.json', wp_json_encode( $manifest ) )
			&& file_put_contents( $bundle . '/template.json', wp_json_encode( $document ) ), 'Wrote the interactive template fixture.' );
		$result = ( new TemplateImporter( new TemplateRegistry( $test_root, $widgets ), $manager ) )->import( 'post-collections' );
		nova_template_check( ! is_wp_error( $result ), 'All ten Batch 8C widgets import as native Elementor widgets.' );
		$templates[] = $result;
		$saved = Elementor::instance()->documents->get( $result )->get_elements_data()[0]['elements'];
		nova_template_check( (int) $saved[5]['settings']['feature_image']['id'] === $media[0]
			&& (int) $saved[6]['settings']['before_image']['id'] === $media[0]
			&& (int) $saved[6]['settings']['after_image']['id'] > 0
			&& (int) $saved[6]['settings']['after_image']['id'] !== $media[0], 'Hotspot and comparison bind the correct media controls.' );
		unset( $saved[5]['settings']['feature_image'], $saved[6]['settings']['before_image'], $saved[6]['settings']['after_image'] );
		foreach ( $interactive as $index => list( $name, $settings ) ) {
			nova_template_check( $name === $saved[ $index ]['widgetType'] && $settings == $saved[ $index ]['settings'], "$name retains its content, controls and preset." );
		}
	} finally {
		wp_delete_file( $bundle . '/manifest.json' );
		wp_delete_file( $bundle . '/template.json' );
		if ( is_dir( $bundle . '/media' ) ) {
			wp_delete_file( $bundle . '/media/architecture.webp' );
			wp_delete_file( $bundle . '/media/comparison.webp' );
			rmdir( $bundle . '/media' );
		}
		rmdir( $bundle );
		rmdir( $test_root );
	}
	echo 'WordPress ', get_bloginfo( 'version' ), ' · Elementor ', ELEMENTOR_VERSION, ' · PHP ', PHP_VERSION, PHP_EOL;
} finally {
	foreach ( $templates as $id ) {
		wp_delete_post( $id, true );
	}
	if ( false === $previous ) {
		delete_option( Settings::WIDGETS );
	} else {
		update_option( Settings::WIDGETS, $previous );
	}
}
