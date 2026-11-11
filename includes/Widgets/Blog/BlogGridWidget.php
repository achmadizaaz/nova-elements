<?php

namespace NovaElements\Widgets\Blog;

use Elementor\Controls_Manager;
use NovaElements\Query\PostQuery;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

final class BlogGridWidget extends BaseWidget {
	public function get_name(): string {
		return 'ne-blog-grid';
	}

	public function get_title(): string {
		return __( 'Blog Grid', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-posts-grid';
	}

	public function get_style_depends(): array {
		return array( 'nova-elements-blog-grid' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'query', array( 'label' => __( 'Post query', 'nova-elements' ) ) );
		$this->add_control( 'post_type', array( 'label' => __( 'Post type', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'options' => PostQuery::post_types(), 'default' => 'post' ) );
		$this->add_control( 'taxonomy', array( 'label' => __( 'Filter taxonomy', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'options' => array( '' => __( 'Any', 'nova-elements' ) ) + PostQuery::taxonomies(), 'default' => '' ) );
		$this->add_control(
			'term_ids',
			array(
				'label' => __( 'Term IDs', 'nova-elements' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Enter comma-separated term IDs from the selected taxonomy, for example 12, 34.', 'nova-elements' ),
				'condition' => array( 'taxonomy!' => '' ),
			)
		);
		$this->add_control( 'posts_per_page', array( 'label' => __( 'Posts per page', 'nova-elements' ), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 24, 'default' => 6 ) );
		$this->add_control( 'orderby', array( 'label' => __( 'Order by', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'options' => array( 'date' => __( 'Publication date', 'nova-elements' ), 'modified' => __( 'Last modified', 'nova-elements' ), 'title' => __( 'Title', 'nova-elements' ) ), 'default' => 'date' ) );
		$this->add_control( 'order', array( 'label' => __( 'Sort direction', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'options' => array( 'DESC' => __( 'Descending', 'nova-elements' ), 'ASC' => __( 'Ascending', 'nova-elements' ) ), 'default' => 'DESC' ) );
		$this->add_control( 'include_ids', array( 'label' => __( 'Include post IDs', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'description' => __( 'Enter comma-separated IDs or leave blank to include all matching posts.', 'nova-elements' ) ) );
		$this->add_control( 'exclude_ids', array( 'label' => __( 'Exclude post IDs', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'description' => __( 'Enter comma-separated IDs to omit.', 'nova-elements' ) ) );
		$this->add_control( 'offset', array( 'label' => __( 'Skip first posts', 'nova-elements' ), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'max' => 1000, 'default' => 0, 'condition' => array( 'pagination!' => 'yes' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'display', array( 'label' => __( 'Post display', 'nova-elements' ) ) );
		$this->add_heading_tag_control();
		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Columns', 'nova-elements' ),
				'type' => Controls_Manager::SELECT,
				'options' => array( '' => __( 'Preset default', 'nova-elements' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
				'selectors' => array( '{{WRAPPER}} .nova-blog' => '--ne-blog-columns: {{VALUE}};' ),
			)
		);
		foreach ( array(
			'image' => __( 'Featured image', 'nova-elements' ),
			'excerpt' => __( 'Excerpt', 'nova-elements' ),
			'date' => __( 'Date', 'nova-elements' ),
			'author' => __( 'Author', 'nova-elements' ),
			'term' => __( 'Taxonomy label', 'nova-elements' ),
			'pagination' => __( 'Pagination', 'nova-elements' ),
		) as $id => $label ) {
			$this->add_control( $id, array( 'label' => $label, 'type' => Controls_Manager::SWITCHER, 'default' => 'author' === $id ? '' : 'yes' ) );
		}
		$this->add_control( 'image_size', array( 'label' => __( 'Image size', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'default' => 'large', 'options' => array( 'medium' => __( 'Medium', 'nova-elements' ), 'medium_large' => __( 'Medium large', 'nova-elements' ), 'large' => __( 'Large', 'nova-elements' ) ), 'condition' => array( 'image' => 'yes' ) ) );
		$this->add_control( 'excerpt_words', array( 'label' => __( 'Excerpt length (words)', 'nova-elements' ), 'type' => Controls_Manager::NUMBER, 'min' => 5, 'max' => 60, 'default' => 22, 'condition' => array( 'excerpt' => 'yes' ) ) );
		$this->add_control( 'pagination_label', array( 'label' => __( 'Pagination accessible label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Posts pagination', 'nova-elements' ), 'condition' => array( 'pagination' => 'yes' ) ) );
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'text_style', array( 'label' => __( 'Text', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'post_title', __( 'Title', 'nova-elements' ), '.nova-blog__title a' );
		$this->add_text_style( 'post_excerpt', __( 'Excerpt', 'nova-elements' ), '.nova-blog__excerpt' );
		$this->add_text_style( 'post_meta', __( 'Metadata', 'nova-elements' ), '.nova-blog__meta, {{WRAPPER}} .nova-blog__term' );
		$this->end_controls_section();

		$this->start_controls_section( 'card_style', array( 'label' => __( 'Cards and spacing', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_size( 'grid_gap', __( 'Grid gap', 'nova-elements' ), '.nova-blog', '--ne-blog-gap' );
		$this->add_size( 'content_gap', __( 'Content gap', 'nova-elements' ), '.nova-blog', '--ne-blog-content-gap' );
		$this->add_panel_style( '.nova-blog__card' );
		$this->add_control( 'image_ratio', array( 'label' => __( 'Image aspect ratio', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'options' => array( '' => __( 'Preset default', 'nova-elements' ), '1' => '1:1', '1.3333' => '4:3', '1.7778' => '16:9' ), 'selectors' => array( '{{WRAPPER}} .nova-blog__media' => 'aspect-ratio: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'pagination_style', array( 'label' => __( 'Pagination', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'pagination' => 'yes' ) ) );
		$this->add_text_style( 'navigation', __( 'Navigation', 'nova-elements' ), '.nova-blog__pagination, {{WRAPPER}} .nova-blog__pagination a' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$tag = $this->heading_tag( $settings['heading_tag'] ?? null );
		$key = 'ne_page_' . sanitize_key( $this->get_id() );
		// Public pagination is a bounded read-only query parameter.
		$page = PostQuery::page_number( $_GET[ $key ] ?? null ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput -- Bounded decimal validation.
		$args = ( new PostQuery() )->arguments( $settings, $page );
		$query = new \WP_Query( $args );
		$page = $args['paged'];
		$total = min( PostQuery::MAX_PAGE, (int) $query->max_num_pages );
		$label = is_string( $settings['pagination_label'] ?? null ) && trim( $settings['pagination_label'] ) !== ''
			? $settings['pagination_label'] : __( 'Posts pagination', 'nova-elements' );
		$posts = $this->prepare_posts( $query->posts, $settings );
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
		foreach ( $posts as $post ) {
			$id = $post->ID;
			$terms = 'yes' === ( $settings['term'] ?? '' ) ? get_the_terms( $id, $taxonomy ) : false;
			$cards[] = array(
				'title' => get_the_title( $post ) ?: __( 'Untitled post', 'nova-elements' ),
				'url' => get_permalink( $post ),
				'image' => 'yes' === ( $settings['image'] ?? '' ) ? get_the_post_thumbnail( $post, $size, array( 'decoding' => 'async' ) ) : '',
				'excerpt' => 'yes' === ( $settings['excerpt'] ?? '' ) ? wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post ) ), $length, '…' ) : '',
				'date' => 'yes' === ( $settings['date'] ?? '' ) ? get_the_date( '', $post ) : '',
				'datetime' => get_the_date( DATE_W3C, $post ),
				'author' => 'yes' === ( $settings['author'] ?? '' ) ? get_the_author_meta( 'display_name', (int) $post->post_author ) : '',
				'term' => is_array( $terms ) && $terms ? reset( $terms )->name : '',
			);
		}
		return $cards;
	}
}
