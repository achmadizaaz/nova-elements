<?php

namespace NovaElements\Core;

use NovaElements\Widgets\Heading\AdvancedHeadingWidget;
use NovaElements\Widgets\Blog\BlogGridWidget;
use NovaElements\Widgets\Blog\BlogListWidget;
use NovaElements\Widgets\Blog\BlogMasonryWidget;
use NovaElements\Widgets\Blog\BlogCarouselWidget;
use NovaElements\Widgets\Blog\FeaturedPostsWidget;
use NovaElements\Widgets\Blog\MagazineLayoutWidget;
use NovaElements\Widgets\Blog\PostTimelineWidget;
use NovaElements\Widgets\Blog\RelatedPostsWidget;
use NovaElements\Widgets\Blog\PostCategoriesWidget;
use NovaElements\Widgets\Blog\AuthorProfileWidget;
use NovaElements\Widgets\Blog\AdvancedPostMetaWidget;
use NovaElements\Widgets\Cards\FeatureCardWidget;
use NovaElements\Widgets\Media\ImageGalleryWidget;
use NovaElements\Widgets\Media\ImageCarouselWidget;
use NovaElements\Widgets\Media\VideoGalleryWidget;
use NovaElements\Widgets\Media\VideoPopupWidget;
use NovaElements\Widgets\Media\BeforeAfterSliderWidget;
use NovaElements\Widgets\Media\ImageHoverEffectsWidget;
use NovaElements\Widgets\Media\LogoCarouselWidget;
use NovaElements\Widgets\Media\FilterableGalleryWidget;
use NovaElements\Widgets\Media\ImageAccordionWidget;
use NovaElements\Widgets\Media\MediaShowcaseWidget;
use NovaElements\Widgets\Cards\FeatureShowcaseWidget;
use NovaElements\Widgets\Interactive\AdvancedAccordionWidget;
use NovaElements\Widgets\Interactive\AdvancedTabsWidget;
use NovaElements\Widgets\Interactive\ModalPopupWidget;
use NovaElements\Widgets\Interactive\AdvancedFaqWidget;
use NovaElements\Widgets\Interactive\ImageComparisonWidget;
use NovaElements\Widgets\Interactive\OffCanvasContentWidget;
use NovaElements\Widgets\Interactive\AdvancedButtonGroupWidget;
use NovaElements\Widgets\Interactive\TooltipWidget;
use NovaElements\Widgets\Interactive\ContentToggleWidget;
use NovaElements\Widgets\Interactive\InteractiveHotspotWidget;
use NovaElements\Widgets\Cards\InfoCardWidget;
use NovaElements\Widgets\Cards\ServiceCardWidget;
use NovaElements\Widgets\Cards\ProfileCardWidget;
use NovaElements\Widgets\Cards\ImageBoxWidget;
use NovaElements\Widgets\Cards\PricingCardWidget;
use NovaElements\Widgets\Cards\TeamMemberCardWidget;
use NovaElements\Widgets\Cards\IconBoxWidget;
use NovaElements\Widgets\Cards\FlipCardWidget;
use NovaElements\Widgets\Cards\HoverCardWidget;
use NovaElements\Widgets\Buttons\AdvancedButtonWidget;
use NovaElements\Widgets\Testimonials\TestimonialsWidget;
use NovaElements\Widgets\Team\TeamWidget;

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
			'ne-blog-list' => array(
				'class'       => BlogListWidget::class,
				'title'       => __( 'Blog List', 'nova-elements' ),
				'description' => __( 'Display a sequential reading list with responsive media and text placement.', 'nova-elements' ),
				'category'    => 'posts',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-blog-list' ) ),
				'style'       => 'blog-collection',
				'base_style'  => 'blog-grid',
				'woocommerce' => false,
			),
			'ne-blog-masonry' => array(
				'class'       => BlogMasonryWidget::class,
				'title'       => __( 'Blog Masonry', 'nova-elements' ),
				'description' => __( 'Arrange variable-height posts in responsive masonry columns.', 'nova-elements' ),
				'category'    => 'posts',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-blog-masonry' ) ),
				'style'       => 'blog-collection',
				'base_style'  => 'blog-grid',
				'woocommerce' => false,
			),
			'ne-blog-carousel' => array(
				'class'       => BlogCarouselWidget::class,
				'title'       => __( 'Blog Carousel', 'nova-elements' ),
				'description' => __( 'Browse posts in a scrollable carousel with responsive slides and optional autoplay.', 'nova-elements' ),
				'category'    => 'posts',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-blog-carousel' ) ),
				'style'       => 'blog-collection',
				'base_style'  => 'blog-grid',
				'script'      => 'blog-carousel',
				'woocommerce' => false,
			),
			'ne-featured-posts' => array(
				'class'       => FeaturedPostsWidget::class,
				'title'       => __( 'Featured Posts', 'nova-elements' ),
				'description' => __( 'Show a lead article with supporting stories from a curated or sticky-post query.', 'nova-elements' ),
				'category'    => 'posts',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-featured-posts' ) ),
				'style'       => 'blog-editorial',
				'base_style'  => 'blog-grid',
				'woocommerce' => false,
			),
			'ne-magazine-layout' => array(
				'class'       => MagazineLayoutWidget::class,
				'title'       => __( 'Magazine Layout', 'nova-elements' ),
				'description' => __( 'Pair a lead article with a responsive group of supporting stories.', 'nova-elements' ),
				'category'    => 'posts',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-magazine-layout' ) ),
				'style'       => 'blog-editorial',
				'base_style'  => 'blog-grid',
				'woocommerce' => false,
			),
			'ne-post-timeline' => array(
				'class'       => PostTimelineWidget::class,
				'title'       => __( 'Post Timeline', 'nova-elements' ),
				'description' => __( 'Browse public posts in an ordered timeline by publication date.', 'nova-elements' ),
				'category'    => 'posts',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-post-timeline' ) ),
				'style'       => 'blog-editorial',
				'base_style'  => 'blog-grid',
				'woocommerce' => false,
			),
			'ne-related-posts' => array(
				'class'       => RelatedPostsWidget::class,
				'title'       => __( 'Related Posts', 'nova-elements' ),
				'description' => __( 'Display related articles based on terms shared with a public reference post.', 'nova-elements' ),
				'category'    => 'posts',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-related-posts' ) ),
				'style'       => 'blog-context',
				'base_style'  => 'blog-grid',
				'woocommerce' => false,
			),
			'ne-post-categories' => array(
				'class'       => PostCategoriesWidget::class,
				'title'       => __( 'Post Categories', 'nova-elements' ),
				'description' => __( 'Display linked categories or public taxonomy terms with optional counts and descriptions.', 'nova-elements' ),
				'category'    => 'posts',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-post-categories' ) ),
				'style'       => 'blog-context',
				'base_style'  => 'blog-grid',
				'woocommerce' => false,
			),
			'ne-author-profile' => array(
				'class'       => AuthorProfileWidget::class,
				'title'       => __( 'Author Profile', 'nova-elements' ),
				'description' => __( 'Display the public profile of an article author or selected contributor.', 'nova-elements' ),
				'category'    => 'posts',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-author-profile' ) ),
				'style'       => 'blog-context',
				'base_style'  => 'blog-grid',
				'woocommerce' => false,
			),
			'ne-advanced-post-meta' => array(
				'class'       => AdvancedPostMetaWidget::class,
				'title'       => __( 'Advanced Post Meta', 'nova-elements' ),
				'description' => __( 'Show a configurable byline, dates, categories, reading time and public metadata for an article.', 'nova-elements' ),
				'category'    => 'posts',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-advanced-post-meta' ) ),
				'style'       => 'post-meta',
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
			'ne-info-card' => array(
				'class'       => InfoCardWidget::class,
				'title'       => __( 'Info Card', 'nova-elements' ),
				'description' => __( 'Present key information with an optional note and link.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-info-card' ) ),
				'style'       => 'content-cards',
				'base_style'  => 'feature-card',
				'woocommerce' => false,
			),
			'ne-service-card' => array(
				'class'       => ServiceCardWidget::class,
				'title'       => __( 'Service Card', 'nova-elements' ),
				'description' => __( 'Describe a service with an image or icon, benefits and a clear action.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-service-card' ) ),
				'style'       => 'content-cards',
				'base_style'  => 'feature-card',
				'woocommerce' => false,
			),
			'ne-profile-card' => array(
				'class'       => ProfileCardWidget::class,
				'title'       => __( 'Profile Card', 'nova-elements' ),
				'description' => __( 'Introduce a person with a portrait, role and biography using manual or public post content.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-profile-card' ) ),
				'style'       => 'content-cards',
				'base_style'  => 'feature-card',
				'woocommerce' => false,
			),
			'ne-team-member-card' => array(
				'class'       => TeamMemberCardWidget::class,
				'title'       => __( 'Team Member Card', 'nova-elements' ),
				'description' => __( 'Introduce a team member with a portrait, role, department and contact link.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-team-member-card' ) ),
				'style'       => 'content-cards',
				'base_style'  => 'feature-card',
				'woocommerce' => false,
			),
			'ne-team' => array(
				'class'       => TeamWidget::class,
				'title'       => __( 'Team', 'nova-elements' ),
				'description' => __( 'Show team or board members with a role badge on each image and a role filter.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-team' ) ),
				'style'       => 'team',
				'script'      => 'team',
				'woocommerce' => false,
			),
			'ne-pricing-card' => array(
				'class'       => PricingCardWidget::class,
				'title'       => __( 'Pricing Card', 'nova-elements' ),
				'description' => __( 'Show a plan, a current or previous price, and included features.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-pricing-card' ) ),
				'style'       => 'content-cards',
				'base_style'  => 'feature-card',
				'woocommerce' => false,
			),
			'ne-image-box' => array(
				'class'       => ImageBoxWidget::class,
				'title'       => __( 'Image Box', 'nova-elements' ),
				'description' => __( 'Pair a responsive image and optional caption with a heading and a link.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-image-box' ) ),
				'style'       => 'content-cards',
				'base_style'  => 'feature-card',
				'woocommerce' => false,
			),
			'ne-icon-box' => array(
				'class'       => IconBoxWidget::class,
				'title'       => __( 'Icon Box', 'nova-elements' ),
				'description' => __( 'Present an icon or image with a heading, description and action.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-icon-box' ) ),
				'style'       => 'content-cards',
				'base_style'  => 'feature-card',
				'woocommerce' => false,
			),
			'ne-flip-card' => array(
				'class'       => FlipCardWidget::class,
				'title'       => __( 'Flip Card', 'nova-elements' ),
				'description' => __( 'Reveal a second face with a native keyboard and touch disclosure.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-flip-card' ) ),
				'style'       => 'content-cards',
				'base_style'  => 'feature-card',
				'woocommerce' => false,
			),
			'ne-hover-card' => array(
				'class'       => HoverCardWidget::class,
				'title'       => __( 'Hover Card', 'nova-elements' ),
				'description' => __( 'Highlight an image or icon on hover and keyboard focus with the content always visible.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-hover-card' ) ),
				'style'       => 'content-cards',
				'base_style'  => 'feature-card',
				'woocommerce' => false,
			),
			'ne-feature-showcase' => array(
				'class'       => FeatureShowcaseWidget::class,
				'title'       => __( 'Feature Showcase', 'nova-elements' ),
				'description' => __( 'Present a lead feature with media, an action, and an ordered collection of supporting benefits.', 'nova-elements' ),
				'category'    => 'cards',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-feature-showcase' ) ),
				'style'       => 'content-cards',
				'base_style'  => 'feature-card',
				'woocommerce' => false,
			),
			'ne-advanced-accordion' => array(
				'class'       => AdvancedAccordionWidget::class,
				'title'       => __( 'Advanced Accordion', 'nova-elements' ),
				'description' => __( 'Organize rich content into keyboard-friendly disclosure panels.', 'nova-elements' ),
				'category'    => 'interactive',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-advanced-accordion' ) ),
				'style'       => 'interactive-panels',
				'script'      => 'interactive-panels',
				'woocommerce' => false,
			),
			'ne-advanced-tabs' => array(
				'class'       => AdvancedTabsWidget::class,
				'title'       => __( 'Advanced Tabs', 'nova-elements' ),
				'description' => __( 'Present related content in tabs with responsive keyboard navigation.', 'nova-elements' ),
				'category'    => 'interactive',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-advanced-tabs' ) ),
				'style'       => 'interactive-panels',
				'script'      => 'interactive-panels',
				'woocommerce' => false,
			),
			'ne-modal-popup' => array(
				'class'       => ModalPopupWidget::class,
				'title'       => __( 'Modal Popup', 'nova-elements' ),
				'description' => __( 'Open an accessible dialog with a heading, description and optional action.', 'nova-elements' ),
				'category'    => 'interactive',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-modal-popup' ) ),
				'style'       => 'interactive-panels',
				'script'      => 'interactive-panels',
				'woocommerce' => false,
			),
			'ne-tooltip' => array(
				'class'       => TooltipWidget::class,
				'title'       => __( 'Tooltip', 'nova-elements' ),
				'description' => __( 'Show a short explanation on hover, focus, or tap.', 'nova-elements' ),
				'category'    => 'interactive',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-tooltip' ) ),
				'style'       => 'interactive-panels',
				'script'      => 'interactive-panels',
				'woocommerce' => false,
			),
			'ne-content-toggle' => array(
				'class'       => ContentToggleWidget::class,
				'title'       => __( 'Content Toggle', 'nova-elements' ),
				'description' => __( 'Switch between two labeled versions of rich content.', 'nova-elements' ),
				'category'    => 'interactive',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-content-toggle' ) ),
				'style'       => 'interactive-panels',
				'script'      => 'interactive-panels',
				'woocommerce' => false,
			),
			'ne-interactive-hotspot' => array(
				'class'       => InteractiveHotspotWidget::class,
				'title'       => __( 'Interactive Hotspot', 'nova-elements' ),
				'description' => __( 'Add interactive numbered markers and explanations to an image.', 'nova-elements' ),
				'category'    => 'interactive',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-interactive-hotspot' ) ),
				'style'       => 'interactive-panels',
				'script'      => 'interactive-panels',
				'woocommerce' => false,
			),
			'ne-image-comparison' => array(
				'class'       => ImageComparisonWidget::class,
				'title'       => __( 'Image Comparison', 'nova-elements' ),
				'description' => __( 'Compare two images with a native accessible range input.', 'nova-elements' ),
				'category'    => 'interactive',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-image-comparison' ) ),
				'style'       => 'interactive-panels',
				'script'      => 'interactive-panels',
				'woocommerce' => false,
			),
			'ne-off-canvas-content' => array(
				'class'       => OffCanvasContentWidget::class,
				'title'       => __( 'Off-Canvas Content', 'nova-elements' ),
				'description' => __( 'Open a keyboard-accessible content drawer from either edge.', 'nova-elements' ),
				'category'    => 'interactive',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-off-canvas-content' ) ),
				'style'       => 'interactive-panels',
				'script'      => 'interactive-panels',
				'woocommerce' => false,
			),
			'ne-advanced-button-group' => array(
				'class'       => AdvancedButtonGroupWidget::class,
				'title'       => __( 'Advanced Button Group', 'nova-elements' ),
				'description' => __( 'Present a responsive group of independent action links.', 'nova-elements' ),
				'category'    => 'interactive',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-advanced-button-group' ) ),
				'style'       => 'interactive-panels',
				'woocommerce' => false,
			),
			'ne-advanced-faq' => array(
				'class'       => AdvancedFaqWidget::class,
				'title'       => __( 'Advanced FAQ', 'nova-elements' ),
				'description' => __( 'Present reorderable questions and rich-text answers as accessible disclosures.', 'nova-elements' ),
				'category'    => 'interactive',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-advanced-faq' ) ),
				'style'       => 'interactive-panels',
				'script'      => 'interactive-panels',
				'woocommerce' => false,
			),
			'ne-image-gallery' => array(
				'class'       => ImageGalleryWidget::class,
				'title'       => __( 'Image Gallery', 'nova-elements' ),
				'description' => __( 'Image Gallery with responsive media and five visual presets.', 'nova-elements' ),
				'category'    => 'media',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-image-gallery' ) ),
				'style'       => 'media-gallery',
				'script'      => 'media-gallery',
				'woocommerce' => false,
			),
			'ne-image-carousel' => array(
				'class'       => ImageCarouselWidget::class,
				'title'       => __( 'Image Carousel', 'nova-elements' ),
				'description' => __( 'Image Carousel with responsive media and five visual presets.', 'nova-elements' ),
				'category'    => 'media',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-image-carousel' ) ),
				'style'       => 'media-gallery',
				'script'      => 'media-gallery',
				'woocommerce' => false,
			),
			'ne-video-gallery' => array(
				'class'       => VideoGalleryWidget::class,
				'title'       => __( 'Video Gallery', 'nova-elements' ),
				'description' => __( 'Video Gallery with responsive media and five visual presets.', 'nova-elements' ),
				'category'    => 'media',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-video-gallery' ) ),
				'style'       => 'media-gallery',
				'script'      => 'media-gallery',
				'woocommerce' => false,
			),
			'ne-video-popup' => array(
				'class'       => VideoPopupWidget::class,
				'title'       => __( 'Video Popup', 'nova-elements' ),
				'description' => __( 'Video Popup with responsive media and five visual presets.', 'nova-elements' ),
				'category'    => 'media',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-video-popup' ) ),
				'style'       => 'media-gallery',
				'script'      => 'media-gallery',
				'woocommerce' => false,
			),
			'ne-before-after-slider' => array(
				'class'       => BeforeAfterSliderWidget::class,
				'title'       => __( 'Before/After Slider', 'nova-elements' ),
				'description' => __( 'Before/After Slider with responsive media and five visual presets.', 'nova-elements' ),
				'category'    => 'media',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-before-after-slider' ) ),
				'style'       => 'interactive-panels',
				'script'      => 'interactive-panels',
				'woocommerce' => false,
			),
			'ne-image-hover-effects' => array(
				'class'       => ImageHoverEffectsWidget::class,
				'title'       => __( 'Image Hover Effects', 'nova-elements' ),
				'description' => __( 'Image Hover Effects with responsive media and five visual presets.', 'nova-elements' ),
				'category'    => 'media',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-image-hover-effects' ) ),
				'style'       => 'media-gallery',
				'script'      => 'media-gallery',
				'woocommerce' => false,
			),
			'ne-logo-carousel' => array(
				'class'       => LogoCarouselWidget::class,
				'title'       => __( 'Logo Carousel', 'nova-elements' ),
				'description' => __( 'Logo Carousel with responsive media and five visual presets.', 'nova-elements' ),
				'category'    => 'media',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-logo-carousel' ) ),
				'style'       => 'media-gallery',
				'script'      => 'media-gallery',
				'woocommerce' => false,
			),
			'ne-filterable-gallery' => array(
				'class'       => FilterableGalleryWidget::class,
				'title'       => __( 'Filterable Gallery', 'nova-elements' ),
				'description' => __( 'Filterable Gallery with responsive media and five visual presets.', 'nova-elements' ),
				'category'    => 'media',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-filterable-gallery' ) ),
				'style'       => 'media-gallery',
				'script'      => 'media-gallery',
				'woocommerce' => false,
			),
			'ne-image-accordion' => array(
				'class'       => ImageAccordionWidget::class,
				'title'       => __( 'Image Accordion', 'nova-elements' ),
				'description' => __( 'Image Accordion with responsive media and five visual presets.', 'nova-elements' ),
				'category'    => 'media',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-image-accordion' ) ),
				'style'       => 'media-gallery',
				'script'      => 'media-gallery',
				'woocommerce' => false,
			),
			'ne-media-showcase' => array(
				'class'       => MediaShowcaseWidget::class,
				'title'       => __( 'Media Showcase', 'nova-elements' ),
				'description' => __( 'Media Showcase with responsive media and five visual presets.', 'nova-elements' ),
				'category'    => 'media',
				'presets'     => array_keys( StylePresetRegistry::for_widget( 'ne-media-showcase' ) ),
				'style'       => 'media-gallery',
				'script'      => 'media-gallery',
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
			'media' => __( 'Media & Gallery', 'nova-elements' ),
		);
	}
}
