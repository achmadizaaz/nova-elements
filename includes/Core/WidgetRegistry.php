<?php

namespace NovaElements\Core;

use NovaElements\Widgets\Heading\AdvancedHeadingWidget;
use NovaElements\Widgets\Blog\BlogGridWidget;
use NovaElements\Widgets\Cards\FeatureCardWidget;
use NovaElements\Widgets\Buttons\AdvancedButtonWidget;
use NovaElements\Widgets\Testimonials\TestimonialsWidget;

defined( 'ABSPATH' ) || exit;

final class WidgetRegistry {
	public function all(): array {
		return array(
			'ne-advanced-heading' => array(
				'class'       => AdvancedHeadingWidget::class,
				'title'       => __( 'Advanced Heading', 'nova-elements' ),
				'description' => __( 'Compose a semantic heading and optional subtitle with responsive typography and five layouts.', 'nova-elements' ),
				'category'    => 'typography',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-advanced-heading' ) ),
				'style'       => 'heading',
				'woocommerce' => false,
			),
			'ne-blog-grid' => array(
				'class'       => BlogGridWidget::class,
				'title'       => __( 'Blog Grid', 'nova-elements' ),
				'description' => __( 'Display published posts with taxonomy filters, metadata and independent pagination.', 'nova-elements' ),
				'category'    => 'posts',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-blog-grid' ) ),
				'style'       => 'blog-grid',
				'woocommerce' => false,
			),
			'ne-feature-card' => array(
				'class'       => FeatureCardWidget::class,
				'title'       => __( 'Feature Card', 'nova-elements' ),
				'description' => __( 'Present a feature with an image or icon, descriptive copy and a link.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-feature-card' ) ),
				'style'       => 'feature-card',
				'woocommerce' => false,
			),
			'ne-advanced-button' => array(
				'class'       => AdvancedButtonWidget::class,
				'title'       => __( 'Advanced Button', 'nova-elements' ),
				'description' => __( 'Create a responsive link with icon placement and independently styled interaction states.', 'nova-elements' ),
				'category'    => 'interactive',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-advanced-button' ) ),
				'style'       => 'advanced-button',
				'woocommerce' => false,
			),
			'ne-testimonials' => array(
				'class'       => TestimonialsWidget::class,
				'title'       => __( 'Testimonials', 'nova-elements' ),
				'description' => __( 'Display repeatable quotes and ratings in a grid or accessible scrolling carousel.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-testimonials' ) ),
				'style'       => 'testimonials',
				'woocommerce' => false,
			),
		);
	}

	public function categories(): array {
		return array(
			'typography' => __( 'Content & Typography', 'nova-elements' ),
			'posts' => __( 'Blog & Posts', 'nova-elements' ),
			'cards' => __( 'Cards & Content Blocks', 'nova-elements' ),
			'interactive' => __( 'Interactive Elements', 'nova-elements' ),
		);
	}
}
