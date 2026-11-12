<?php

namespace NovaElements\Widgets\Blog;

use Elementor\Controls_Manager;
use Elementor\Plugin as Elementor;
use Elementor\Repeater;
use NovaElements\Query\PostQuery;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

final class AdvancedPostMetaWidget extends BaseWidget {
	public function get_name(): string {
		return 'ne-advanced-post-meta';
	}

	public function get_title(): string {
		return __( 'Advanced Post Meta', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-post-info';
	}

	public function get_keywords(): array {
		return array( 'byline', 'post meta', 'reading time', 'published', 'taxonomy' );
	}

	public function get_style_depends(): array {
		return array( 'nova-elements-post-meta' );
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	private static function labels(): array {
		return array(
			'author' => __( 'Author', 'nova-elements' ),
			'published' => __( 'Published', 'nova-elements' ),
			'modified' => __( 'Updated', 'nova-elements' ),
			'terms' => __( 'Taxonomy terms', 'nova-elements' ),
			'comments' => __( 'Comments', 'nova-elements' ),
			'reading' => __( 'Reading time', 'nova-elements' ),
			'custom' => __( 'Public custom field', 'nova-elements' ),
		);
	}

	private static function default_items(): array {
		return array(
			array( 'meta_field' => 'author', 'meta_link' => 'yes' ),
			array( 'meta_field' => 'published' ),
			array( 'meta_field' => 'terms', 'meta_taxonomy' => 'category', 'meta_link' => 'yes' ),
			array( 'meta_field' => 'reading' ),
		);
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'post_metadata', array( 'label' => __( 'Post metadata', 'nova-elements' ) ) );
		$this->add_control( 'meta_reference_id', array(
			'label' => __( 'Reference post ID', 'nova-elements' ),
			'type' => Controls_Manager::NUMBER,
			'min' => 1,
			'description' => __( 'Leave blank to use the current article. Use a published post ID to show its details on another page.', 'nova-elements' ),
		) );
		$this->add_control( 'meta_date_format', array(
			'label' => __( 'Date format', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'options' => array( 'site' => __( 'Site date format', 'nova-elements' ), 'iso' => __( 'YYYY-MM-DD', 'nova-elements' ) ),
			'default' => 'site',
		) );

		$row = new Repeater();
		$labels = self::labels();
		$keys = array();
		foreach ( array_keys( PostQuery::post_types() ) as $type ) {
			foreach ( self::displayable_fields( $type ) as $key => $schema ) {
				$keys[ $key ] = ! empty( $schema['label'] ) && is_string( $schema['label'] ) ? $schema['label'] : $key;
			}
		}
		if ( ! $keys ) {
			unset( $labels['custom'] );
		}
		$row->add_control( 'meta_field', array( 'label' => __( 'Field', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'options' => $labels, 'default' => 'author' ) );
		$row->add_control( 'meta_label', array( 'label' => __( 'Custom label', 'nova-elements' ), 'type' => Controls_Manager::TEXT ) );
		$row->add_control( 'meta_taxonomy', array(
			'label' => __( 'Taxonomy', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'options' => array_filter( PostQuery::taxonomies(), 'is_taxonomy_viewable', ARRAY_FILTER_USE_KEY ),
			'default' => 'category',
			'condition' => array( 'meta_field' => 'terms' ),
		) );
		$row->add_control( 'meta_key', array(
			'label' => __( 'Custom field', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'options' => $keys,
			'condition' => array( 'meta_field' => 'custom' ),
		) );
		$row->add_control( 'meta_link', array(
			'label' => __( 'Link to archive or comments', 'nova-elements' ),
			'type' => Controls_Manager::SWITCHER,
			'condition' => array( 'meta_field' => array( 'author', 'terms', 'comments' ) ),
		) );
		$this->add_control( 'meta_items', array(
			'label' => __( 'Fields', 'nova-elements' ),
			'type' => Controls_Manager::REPEATER,
			'fields' => $row->get_controls(),
			'default' => self::default_items(),
			'title_field' => '{{ meta_label || meta_field }}',
			'description' => __( 'Reorder up to eight fields. Fields without a public value are omitted.', 'nova-elements' ),
		) );
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'meta_style', array( 'label' => __( 'Metadata', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'meta_term', __( 'Labels', 'nova-elements' ), '.nova-post-meta__label' );
		$this->add_text_style( 'meta_text', __( 'Values', 'nova-elements' ), '.nova-post-meta__value' );
		$this->add_panel_style( '.nova-post-meta .nova-post-meta__list' );
		$this->add_size( 'meta_gap', __( 'Field gap', 'nova-elements' ), '.nova-post-meta', '--ne-meta-gap' );
		$this->end_controls_section();
	}

	/** A registered field needs separate permission to display its value publicly. */
	private static function displayable_fields( string $type ): array {
		$registered = get_registered_meta_keys( 'post', $type ) + get_registered_meta_keys( 'post' );
		$keys = array_filter( (array) apply_filters( 'nova_elements_displayable_meta', array(), $type ), 'is_string' );
		return array_filter(
			array_intersect_key( $registered, array_fill_keys( $keys, true ) ),
			static fn ( array $schema, string $key ): bool => ! is_protected_meta( $key, 'post' )
				&& $schema['single'] && in_array( $schema['type'], array( 'string', 'number', 'integer' ), true ),
			ARRAY_FILTER_USE_BOTH
		);
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$post = PostContext::public_post( $settings['meta_reference_id'] ?? '' );
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$items = array();
		$rows = $settings['meta_items'] ?? self::default_items();
		if ( $post && is_array( $rows ) ) {
			$labels = self::labels();
			$format = 'iso' === ( $settings['meta_date_format'] ?? '' ) ? 'Y-m-d' : (string) get_option( 'date_format', 'F j, Y' );
			foreach ( array_slice( $rows, 0, 8 ) as $row ) {
				$field = $row['meta_field'] ?? '';
				if ( ! is_array( $row ) || ! is_string( $field ) || ! isset( $labels[ $field ] ) ) {
					continue;
				}
				$values = $this->values( $post, $field, $row, $format );
				if ( $values ) {
					$label = $this->text_value( $row['meta_label'] ?? null );
					if ( '' === $label && 'terms' === $field ) {
						$label = get_taxonomy( $row['meta_taxonomy'] ?? 'category' )->labels->name ?? '';
					}
					$items[] = array( 'label' => '' === $label ? $labels[ $field ] : $label, 'values' => $values );
				}
			}
		}
		$empty_message = '';
		if ( ! $items && is_user_logged_in() ) {
			$elementor = Elementor::instance();
			if ( $elementor->editor->is_edit_mode() || $elementor->preview->is_preview_mode() ) {
				$empty_message = $post
					? __( 'No public values match the selected fields.', 'nova-elements' )
					: __( 'Select a public reference article to preview its metadata.', 'nova-elements' );
			}
		}
		require dirname( __DIR__, 3 ) . '/templates/widgets/advanced-post-meta.php';
	}

	private function values( \WP_Post $post, string $field, array $row, string $format ): array {
		$link = 'yes' === ( $row['meta_link'] ?? '' );
		switch ( $field ) {
			case 'published':
			case 'modified':
				$date = get_post_datetime( $post, 'modified' === $field ? 'modified' : 'date' );
				return $date ? array( array( 'text' => wp_date( $format, $date->getTimestamp() ), 'datetime' => $date->format( DATE_W3C ) ) ) : array();
			case 'author':
				$name = get_the_author_meta( 'display_name', (int) $post->post_author );
				return '' !== $name ? array( array( 'text' => $name, 'url' => $link ? get_author_posts_url( (int) $post->post_author ) : '' ) ) : array();
			case 'comments':
				$count = (int) get_comments_number( $post );
				return array( array( 'text' => number_format_i18n( $count ), 'url' => $link && ( $count || comments_open( $post ) ) ? get_comments_link( $post ) : '' ) );
			case 'terms':
				return $this->term_values( $post, $row['meta_taxonomy'] ?? 'category', $link );
			case 'reading':
				$content = strip_shortcodes( $post->post_content );
				$text = html_entity_decode( wp_strip_all_tags( str_replace( '><', '> <', $content ), true ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
				$characters = 'words' !== wp_get_word_count_type();
				$count = preg_match_all( $characters ? '/[\p{L}\p{N}]/u' : "/[\p{L}\p{N}][\p{L}\p{N}\p{M}’'-]*/u", $text );
				if ( ! $count ) {
					return array();
				}
				$minutes = (int) ceil( $count / ( $characters ? 500 : 220 ) );
				/* translators: %s: estimated reading time in minutes. */
				return array( array( 'text' => sprintf( _n( '%s minute', '%s minutes', $minutes, 'nova-elements' ), number_format_i18n( $minutes ) ) ) );
			case 'custom':
				$key = $row['meta_key'] ?? null;
				$fields = self::displayable_fields( $post->post_type );
				if ( ! is_string( $key ) || ! isset( $fields[ $key ] ) || ! metadata_exists( 'post', $post->ID, $key ) ) {
					return array();
				}
				$value = get_post_meta( $post->ID, $key, true );
				if ( ! is_string( $value ) && ! is_int( $value ) && ! is_float( $value ) ) {
					return array();
				}
				$value = trim( wp_strip_all_tags( (string) $value ) );
				if ( '' === $value || ( 'string' !== $fields[ $key ]['type'] && ! is_numeric( $value ) ) ) {
					return array();
				}
				return array( array( 'text' => wp_html_excerpt( $value, 240, '…' ) ) );
		}
		return array();
	}

	private function term_values( \WP_Post $post, mixed $taxonomy, bool $link ): array {
		if ( ! is_string( $taxonomy ) || ! isset( PostQuery::taxonomies()[ $taxonomy ] ) || ! is_taxonomy_viewable( $taxonomy )
			|| ! is_object_in_taxonomy( $post->post_type, $taxonomy ) ) {
			return array();
		}
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'object_ids' => array( $post->ID ), 'number' => 8, 'hide_empty' => false, 'update_term_meta_cache' => false ) );
		$values = array();
		foreach ( is_array( $terms ) ? $terms : array() as $term ) {
			$url = $link ? get_term_link( $term ) : '';
			$values[] = array( 'text' => $term->name, 'url' => is_wp_error( $url ) ? '' : $url );
		}
		return $values;
	}
}
