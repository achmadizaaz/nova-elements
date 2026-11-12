<?php

namespace NovaElements\Widgets\Blog;

use Elementor\Controls_Manager;
use NovaElements\Query\PostQuery;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

final class PostCategoriesWidget extends BaseWidget {
	public function get_name(): string {
		return 'ne-post-categories';
	}

	public function get_title(): string {
		return __( 'Post Categories', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-tags';
	}

	public function get_keywords(): array {
		return array( 'category', 'tag', 'taxonomy', 'archive' );
	}

	public function get_style_depends(): array {
		return array( 'nova-elements-blog-context' );
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'terms', array( 'label' => __( 'Terms', 'nova-elements' ) ) );
		$this->add_control( 'taxonomy_source', array(
			'label' => __( 'Source', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'options' => array( 'all' => __( 'All terms', 'nova-elements' ), 'current' => __( 'Current article terms', 'nova-elements' ) ),
			'default' => 'all',
		) );
		$this->add_control( 'terms_taxonomy', array( 'label' => __( 'Taxonomy', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'options' => self::viewable_taxonomies(), 'default' => 'category' ) );
		$this->add_control( 'terms_limit', array( 'label' => __( 'Maximum terms', 'nova-elements' ), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 24, 'default' => 8 ) );
		$this->add_control( 'terms_order', array(
			'label' => __( 'Sort by', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'options' => array( 'name' => __( 'Name', 'nova-elements' ), 'count' => __( 'Most posts', 'nova-elements' ) ),
			'default' => 'name',
		) );
		foreach ( array(
			'hide_empty' => __( 'Hide empty terms', 'nova-elements' ),
			'show_count' => __( 'Show post counts', 'nova-elements' ),
			'show_description' => __( 'Show descriptions', 'nova-elements' ),
		) as $key => $label ) {
			$this->add_control( $key, array( 'label' => $label, 'type' => Controls_Manager::SWITCHER, 'default' => 'show_description' === $key ? '' : 'yes' ) );
		}
		$this->add_control( 'terms_label', array( 'label' => __( 'Accessible navigation name', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Post categories', 'nova-elements' ) ) );
		$this->add_responsive_control( 'columns', array(
			'label' => __( 'Columns', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'options' => array( '' => __( 'Preset default', 'nova-elements' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
			'selectors' => array( '{{WRAPPER}} .nova-terms' => '--ne-term-columns: {{VALUE}};' ),
		) );
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'term_style', array( 'label' => __( 'Term cards', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'term_name', __( 'Name', 'nova-elements' ), '.nova-terms__name' );
		$this->add_text_style( 'term_count', __( 'Count', 'nova-elements' ), '.nova-terms__count' );
		$this->add_text_style( 'term_description', __( 'Description', 'nova-elements' ), '.nova-terms__description' );
		$this->add_panel_style( '.nova-terms .nova-terms__link' );
		$this->add_size( 'terms_gap', __( 'Gap', 'nova-elements' ), '.nova-terms', '--ne-term-gap' );
		$this->end_controls_section();
	}

	private static function viewable_taxonomies(): array {
		return array_filter( PostQuery::taxonomies(), 'is_taxonomy_viewable', ARRAY_FILTER_USE_KEY );
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$label = $this->text_value( $settings['terms_label'] ?? null ) ?: __( 'Post categories', 'nova-elements' );
		$terms = $this->terms( $settings );
		$context = get_queried_object();
		$current = $context instanceof \WP_Term ? $context : null;
		$show_count = 'yes' === ( $settings['show_count'] ?? 'yes' );
		$show_description = 'yes' === ( $settings['show_description'] ?? '' );
		require dirname( __DIR__, 3 ) . '/templates/widgets/post-categories.php';
	}

	private function terms( array $settings ): array {
		$taxonomy = $settings['terms_taxonomy'] ?? 'category';
		if ( ! is_string( $taxonomy ) || ! isset( self::viewable_taxonomies()[ $taxonomy ] ) ) {
			return array();
		}
		$limit = $settings['terms_limit'] ?? 8;
		$orderby = 'count' === ( $settings['terms_order'] ?? '' ) ? 'count' : 'name';
		$args = array(
			'taxonomy' => $taxonomy,
			'number' => is_numeric( $limit ) ? max( 1, min( 24, (int) $limit ) ) : 8,
			'orderby' => $orderby,
			'order' => 'count' === $orderby ? 'DESC' : 'ASC',
			'hide_empty' => 'yes' === ( $settings['hide_empty'] ?? 'yes' ),
			'update_term_meta_cache' => false,
		);
		if ( 'current' === ( $settings['taxonomy_source'] ?? '' ) ) {
			$post = get_queried_object();
			if ( ! is_singular() || ! $post instanceof \WP_Post || 'publish' !== $post->post_status || '' !== $post->post_password
				|| ! is_post_publicly_viewable( $post ) || ! is_object_in_taxonomy( $post->post_type, $taxonomy ) ) {
				return array();
			}
			$args['object_ids'] = array( $post->ID );
		}

		$terms = get_terms( $args );
		$links = array();
		foreach ( is_array( $terms ) ? $terms : array() as $term ) {
			$url = get_term_link( $term );
			if ( ! is_wp_error( $url ) && esc_url( $url ) ) {
				$links[] = array( 'term' => $term, 'url' => $url );
			}
		}
		return $links;
	}
}
