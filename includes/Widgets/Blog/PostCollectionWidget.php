<?php

namespace NovaElements\Widgets\Blog;

use Elementor\Controls_Manager;
use Elementor\Plugin as Elementor;
use NovaElements\Controls\QueryControls;
use NovaElements\Query\PostQuery;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

abstract class PostCollectionWidget extends BaseWidget {
	protected const LAYOUT = 'grid';
	protected const STYLE = 'blog-collection';

	public function get_style_depends(): array {
		return 'grid' === static::LAYOUT ? array( 'nova-elements-blog-grid' ) : array( 'nova-elements-' . static::STYLE );
	}

	public function get_keywords(): array {
		return array( 'posts', 'blog', 'query', static::LAYOUT );
	}

	protected function register_controls(): void {
		QueryControls::register( $this, 'timeline' === static::LAYOUT, 'related' === static::LAYOUT );

		$this->start_controls_section( 'display', array( 'label' => __( 'Post display', 'nova-elements' ) ) );
		$this->add_heading_tag_control();
		if ( ! in_array( static::LAYOUT, array( 'list', 'timeline' ), true ) ) {
			$this->add_responsive_control(
				'columns',
				array(
					'label' => match ( static::LAYOUT ) {
						'carousel' => __( 'Visible slides', 'nova-elements' ),
						'featured', 'magazine' => __( 'Supporting columns', 'nova-elements' ),
						default => __( 'Columns', 'nova-elements' ),
					},
					'type' => Controls_Manager::SELECT,
					'options' => array( '' => __( 'Preset default', 'nova-elements' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
					'selectors' => array( '{{WRAPPER}} .nova-blog' => '--ne-blog-columns: {{VALUE}};' ),
				)
			);
		}
		foreach ( array(
			'image' => __( 'Featured image', 'nova-elements' ),
			'excerpt' => __( 'Excerpt', 'nova-elements' ),
			'date' => __( 'Date', 'nova-elements' ),
			'author' => __( 'Author', 'nova-elements' ),
			'term' => __( 'Taxonomy label', 'nova-elements' ),
		) as $id => $label ) {
			$this->add_control( $id, array( 'label' => $label, 'type' => Controls_Manager::SWITCHER, 'default' => 'author' === $id ? '' : 'yes' ) );
		}
		$this->add_control( 'image_size', array( 'label' => __( 'Image size', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'default' => 'large', 'options' => array( 'medium' => __( 'Medium', 'nova-elements' ), 'medium_large' => __( 'Medium large', 'nova-elements' ), 'large' => __( 'Large', 'nova-elements' ) ), 'condition' => array( 'image' => 'yes' ) ) );
		$this->add_control( 'excerpt_words', array( 'label' => __( 'Excerpt length (words)', 'nova-elements' ), 'type' => Controls_Manager::NUMBER, 'min' => 5, 'max' => 60, 'default' => 22, 'condition' => array( 'excerpt' => 'yes' ) ) );
		$this->end_controls_section();

		$this->register_collection_controls();
		$this->register_visual_presets();
		$this->start_controls_section( 'text_style', array( 'label' => __( 'Text', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'post_title', __( 'Title', 'nova-elements' ), '.nova-blog__title a' );
		$this->add_text_style( 'post_excerpt', __( 'Excerpt', 'nova-elements' ), '.nova-blog__excerpt' );
		$meta_selector = '.nova-blog__meta, {{WRAPPER}} .nova-blog__term';
		if ( 'timeline' === static::LAYOUT ) {
			$meta_selector .= ', {{WRAPPER}} .nova-blog__date';
		}
		$this->add_text_style( 'post_meta', __( 'Metadata', 'nova-elements' ), $meta_selector );
		$this->end_controls_section();

		$this->start_controls_section( 'card_style', array( 'label' => __( 'Cards and spacing', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_size( 'grid_gap', __( 'Grid gap', 'nova-elements' ), '.nova-blog', '--ne-blog-gap' );
		$this->add_size( 'content_gap', __( 'Content gap', 'nova-elements' ), '.nova-blog', '--ne-blog-content-gap' );
		$this->add_panel_style( 'grid' === static::LAYOUT ? '.nova-blog__card' : '.nova-blog .nova-blog__card' );
		// The collection presets set ratios per card. Keep instance settings more specific.
		$media = 'grid' === static::LAYOUT
			? '{{WRAPPER}} .nova-blog__media'
			: '{{WRAPPER}} .nova-blog .nova-blog__grid .nova-blog__card.nova-blog__card--has-image .nova-blog__media';
		$this->add_control( 'image_ratio', array( 'label' => __( 'Image aspect ratio', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'options' => array( '' => __( 'Preset default', 'nova-elements' ), '1' => '1:1', '1.3333' => '4:3', '1.7778' => '16:9' ), 'selectors' => array( $media => 'aspect-ratio: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pagination_style', array( 'label' => __( 'Pagination', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'pagination' => 'yes' ) ) );
		$this->add_text_style( 'navigation', __( 'Navigation', 'nova-elements' ), '.nova-blog__pagination, {{WRAPPER}} .nova-blog__pagination a' );
		$this->end_controls_section();
	}

	protected function register_collection_controls(): void {}

	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$layout = static::LAYOUT;
		if ( 'timeline' === $layout ) {
			$settings['orderby'] = 'date';
		}
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$tag = $this->heading_tag( $settings['heading_tag'] ?? null );
		$key = 'ne_page_' . sanitize_key( $this->get_id() );
		// Public pagination is a bounded read-only query parameter.
		$page = PostQuery::page_number( $_GET[ $key ] ?? null ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput -- Bounded decimal validation.
		$context = get_queried_object();
		$archive = $context instanceof \WP_Term ? $context : null;
		if ( 'related' === $layout ) {
			$settings['query_source'] = 'related';
			$context = PostContext::public_post( $settings['related_context_id'] ?? '' );
		}
		$result = ( new PostQuery() )->run( $settings, $page, $context instanceof \WP_Post ? $context : null, $archive );
		$page = $result->page;
		$total = $result->total_pages;
		$numbered = 'numbers' === ( $settings['pagination_mode'] ?? '' );
		$error = null;
		if ( $result->error && is_user_logged_in() ) {
			$elementor = Elementor::instance();
			if ( $elementor->editor->is_edit_mode() || $elementor->preview->is_preview_mode() ) {
				$error = $result->error;
			}
		}
		$label = is_string( $settings['pagination_label'] ?? null ) && trim( $settings['pagination_label'] ) !== ''
			? $settings['pagination_label'] : __( 'Posts pagination', 'nova-elements' );
		$posts = $this->prepare_posts( $result->posts, $settings );
		require dirname( __DIR__, 3 ) . '/templates/widgets/blog-grid.php';
	}

	private function prepare_posts( array $posts, array $settings ): array {
		$cards = array();
		$size = $settings['image_size'] ?? 'large';
		$size = in_array( $size, array( 'medium', 'medium_large', 'large' ), true ) ? $size : 'large';
		$length = $settings['excerpt_words'] ?? 22;
		$length = is_numeric( $length ) ? max( 5, min( 60, (int) $length ) ) : 22;
		$taxonomy = $settings['taxonomy'] ?? '';
		$taxonomy = is_string( $taxonomy ) && isset( PostQuery::taxonomies()[ $taxonomy ] ) ? $taxonomy : 'category';
		$date_format = 'timeline' === static::LAYOUT ? match ( $settings['timeline_date_format'] ?? '' ) {
			'month' => _x( 'F Y', 'timeline date format', 'nova-elements' ),
			'year' => 'Y',
			default => '',
		} : '';
		foreach ( $posts as $index => $post ) {
			$show_image = 'yes' === ( $settings['image'] ?? '' )
				&& ( 'magazine' !== static::LAYOUT || 0 === $index || 'yes' === ( $settings['magazine_support_images'] ?? 'yes' ) );
			$show_excerpt = 'yes' === ( $settings['excerpt'] ?? '' )
				&& ( 'featured' !== static::LAYOUT || 0 === $index || 'yes' === ( $settings['featured_support_excerpt'] ?? '' ) );
			$id = $post->ID;
			$terms = 'yes' === ( $settings['term'] ?? '' ) ? get_the_terms( $id, $taxonomy ) : false;
			$cards[] = array(
				'title' => get_the_title( $post ) ?: __( 'Untitled post', 'nova-elements' ),
				'url' => get_permalink( $post ),
				'image' => $show_image ? get_the_post_thumbnail( $post, $size, array( 'decoding' => 'async' ) ) : '',
				'excerpt' => $show_excerpt ? wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post ) ), $length, '…' ) : '',
				'date' => 'yes' === ( $settings['date'] ?? '' ) ? get_the_date( $date_format, $post ) : '',
				'datetime' => get_the_date( DATE_W3C, $post ),
				'author' => 'yes' === ( $settings['author'] ?? '' ) ? get_the_author_meta( 'display_name', (int) $post->post_author ) : '',
				'term' => is_array( $terms ) && $terms ? reset( $terms )->name : '',
			);
		}
		return $cards;
	}
}
