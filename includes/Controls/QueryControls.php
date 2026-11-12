<?php

namespace NovaElements\Controls;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;
use NovaElements\Query\PostQuery;

defined( 'ABSPATH' ) || exit;

final class QueryControls {
	public static function register( Widget_Base $widget, bool $chronological = false, bool $related = false ): void {
		$types = PostQuery::post_types();
		$taxonomies = PostQuery::taxonomies();
		$meta = array();
		$numeric = array();
		foreach ( array_keys( $types ) as $type ) {
			foreach ( PostQuery::meta_fields( $type ) as $key => $schema ) {
				$meta[ $key ] = $key;
				if ( in_array( $schema['type'], array( 'number', 'integer' ), true ) ) {
					$numeric[ $key ] = $key;
				}
			}
		}
		$selected_type = array( 'query_source' => array( 'type', 'manual', 'archive' ) );
		$collection = $related ? array() : array( 'query_source!' => 'current' );
		$widget->start_controls_section( 'query', array( 'label' => __( 'Content Source', 'nova-elements' ) ) );
		if ( ! $related ) {
			self::select( $widget, 'query_source', __( 'Source', 'nova-elements' ), array(
				'type' => __( 'Post type', 'nova-elements' ),
				'manual' => __( 'Manual post IDs', 'nova-elements' ),
				'current' => __( 'Current post', 'nova-elements' ),
				'related' => __( 'Related posts', 'nova-elements' ),
				'archive' => __( 'Current taxonomy archive', 'nova-elements' ),
			), 'type' );
			self::select( $widget, 'post_type', __( 'Post type', 'nova-elements' ), $types, 'post', $selected_type );
		}
		self::select( $widget, 'related_taxonomy', __( 'Match terms in', 'nova-elements' ), $taxonomies, 'category', $related ? array() : array( 'query_source' => 'related' ) );
		$widget->add_control( 'posts_per_page', array( 'label' => __( 'Posts per page', 'nova-elements' ), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'max' => 24, 'default' => 6, 'condition' => $collection ) );
		if ( $related ) {
			$widget->add_control( 'related_context_id', array(
				'label' => __( 'Reference post ID', 'nova-elements' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'description' => __( 'Leave blank to use the current article. Set a published post ID to use this widget on a landing page or preview it with a reference article.', 'nova-elements' ),
			) );
		} else {
			$widget->add_control( 'parent_id', array( 'label' => __( 'Parent post ID', 'nova-elements' ), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'description' => __( 'Use zero for top-level posts, or leave blank for any parent.', 'nova-elements' ), 'condition' => $selected_type + array( 'post_type' => array_values( array_filter( array_keys( $types ), 'is_post_type_hierarchical' ) ) ) ) );
		}
		$widget->end_controls_section();

		$widget->start_controls_section( 'query_inclusion', array( 'label' => __( 'Include / Exclude', 'nova-elements' ), 'condition' => $collection ) );
		foreach ( array(
			'include_ids' => __( 'Include post IDs', 'nova-elements' ),
			'exclude_ids' => __( 'Exclude post IDs', 'nova-elements' ),
			'include_authors' => __( 'Include author IDs', 'nova-elements' ),
			'exclude_authors' => __( 'Exclude author IDs', 'nova-elements' ),
		) as $key => $label ) {
			$widget->add_control( $key, array( 'label' => $label, 'type' => Controls_Manager::TEXT, 'description' => __( 'Separate up to 50 IDs with commas.', 'nova-elements' ), 'condition' => $collection ) );
		}
		if ( ! $related ) {
			$widget->add_control( 'exclude_current', array( 'label' => __( 'Exclude current post', 'nova-elements' ), 'type' => Controls_Manager::SWITCHER, 'condition' => array( 'query_source' => array( 'type', 'manual', 'archive' ) ) ) );
			self::select( $widget, 'sticky_posts', __( 'Sticky posts', 'nova-elements' ), array(
				'' => __( 'Use normal sort order', 'nova-elements' ),
				'only' => __( 'Only sticky posts', 'nova-elements' ),
				'exclude' => __( 'Exclude sticky posts', 'nova-elements' ),
			), '', $selected_type + array( 'post_type' => 'post' ) );
		}
		$widget->end_controls_section();

		$widget->start_controls_section( 'query_taxonomies', array( 'label' => __( 'Taxonomy Filters', 'nova-elements' ), 'condition' => $collection ) );
		$choices = array( '' => __( 'Any', 'nova-elements' ) ) + $taxonomies;
		// Keep the original control keys and their role as the card's taxonomy label.
		self::select( $widget, 'taxonomy', __( 'Filter taxonomy', 'nova-elements' ), $choices, '' );
		$widget->add_control( 'term_ids', array( 'label' => __( 'Term IDs', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'description' => __( 'Enter comma-separated term IDs from the selected taxonomy.', 'nova-elements' ), 'condition' => array( 'taxonomy!' => '' ) ) );
		$filter = new Repeater();
		self::select( $filter, 'filter_taxonomy', __( 'Taxonomy', 'nova-elements' ), $choices, '' );
		self::select( $filter, 'filter_operator', __( 'Match', 'nova-elements' ), array(
			'IN' => __( 'Any selected term', 'nova-elements' ),
			'AND' => __( 'All selected terms', 'nova-elements' ),
			'NOT IN' => __( 'Exclude selected terms', 'nova-elements' ),
		), 'IN' );
		$filter->add_control( 'filter_current_term', array( 'label' => __( 'Use the current archive term', 'nova-elements' ), 'type' => Controls_Manager::SWITCHER ) );
		$filter->add_control( 'filter_terms', array( 'label' => __( 'Term IDs', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'condition' => array( 'filter_current_term!' => 'yes' ) ) );
		$filter->add_control( 'filter_children', array( 'label' => __( 'Include child terms', 'nova-elements' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => array( 'filter_taxonomy' => array_values( array_filter( array_keys( $taxonomies ), 'is_taxonomy_hierarchical' ) ) ) ) );
		$widget->add_control( 'taxonomy_filters', array( 'label' => __( 'Additional filters', 'nova-elements' ), 'type' => Controls_Manager::REPEATER, 'fields' => $filter->get_controls(), 'title_field' => '{{ filter_taxonomy }}', 'description' => __( 'Add up to five filters using taxonomies registered for the selected post type.', 'nova-elements' ) ) );
		self::select( $widget, 'taxonomy_relation', __( 'Combine included filters', 'nova-elements' ), array( 'AND' => __( 'Match all filters', 'nova-elements' ), 'OR' => __( 'Match any filter', 'nova-elements' ) ), 'AND' );
		$widget->end_controls_section();

		$widget->start_controls_section( 'query_fields', array( 'label' => __( 'Custom Fields', 'nova-elements' ), 'condition' => $collection ) );
		if ( $meta ) {
			self::select( $widget, 'query_meta_key', __( 'Public custom field', 'nova-elements' ), array( '' => __( 'None', 'nova-elements' ) ) + $meta, '' );
			$selected = array( 'query_meta_key!' => '' );
			self::select( $widget, 'query_meta_operator', __( 'Comparison', 'nova-elements' ), array(
				'=' => __( 'Equals', 'nova-elements' ),
				'!=' => __( 'Does not equal', 'nova-elements' ),
				'LIKE' => __( 'Contains', 'nova-elements' ),
				'>' => __( 'Greater than', 'nova-elements' ),
				'<' => __( 'Less than', 'nova-elements' ),
				'BETWEEN' => __( 'Between', 'nova-elements' ),
				'EXISTS' => __( 'Exists', 'nova-elements' ),
				'NOT EXISTS' => __( 'Does not exist', 'nova-elements' ),
			), '=', $selected );
			$compare = $selected + array( 'query_meta_operator!' => array( 'EXISTS', 'NOT EXISTS' ) );
			self::select( $widget, 'query_meta_format', __( 'Compare as', 'nova-elements' ), array(
				'text' => __( 'Text', 'nova-elements' ),
				'number' => __( 'Number', 'nova-elements' ),
				'date' => __( 'Date (YYYY-MM-DD)', 'nova-elements' ),
			), 'text', $compare );
			$widget->add_control( 'query_meta_value', array( 'label' => __( 'Value', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'condition' => $compare ) );
			$widget->add_control( 'query_meta_end', array( 'label' => __( 'End value', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'condition' => $selected + array( 'query_meta_operator' => 'BETWEEN' ) ) );
		} else {
			$widget->add_control( 'query_meta_help', array( 'type' => Controls_Manager::RAW_HTML, 'raw' => esc_html__( 'This site has no public custom fields enabled for querying.', 'nova-elements' ) ) );
		}
		$widget->end_controls_section();

		$widget->start_controls_section( 'query_sorting', array( 'label' => __( 'Sorting', 'nova-elements' ), 'condition' => $collection ) );
		if ( ! $chronological ) {
			$orders = array(
				'date' => __( 'Publication date', 'nova-elements' ),
				'modified' => __( 'Last modified', 'nova-elements' ),
				'title' => __( 'Title', 'nova-elements' ),
				'menu_order' => __( 'Menu order', 'nova-elements' ),
				'comment_count' => __( 'Comment count', 'nova-elements' ),
				'ID' => __( 'Post ID', 'nova-elements' ),
				'rand' => __( 'Random (without pagination)', 'nova-elements' ),
			);
			if ( $numeric ) {
				$orders['meta_value_num'] = __( 'Numeric custom field', 'nova-elements' );
			}
			self::select( $widget, 'orderby', __( 'Order by', 'nova-elements' ), $orders, 'date' );
		}
		self::select( $widget, 'order', __( 'Sort direction', 'nova-elements' ), array( 'DESC' => __( 'Descending', 'nova-elements' ), 'ASC' => __( 'Ascending', 'nova-elements' ) ), 'DESC', $chronological ? array() : array( 'orderby!' => 'rand' ) );
		if ( ! $chronological ) {
			self::select( $widget, 'query_sort_key', __( 'Numeric field', 'nova-elements' ), $numeric, '', array( 'orderby' => 'meta_value_num' ) );
		}
		$widget->end_controls_section();

		$widget->start_controls_section( 'query_pagination', array( 'label' => __( 'Pagination', 'nova-elements' ), 'condition' => $collection ) );
		$widget->add_control( 'pagination', array( 'label' => __( 'Enable pagination', 'nova-elements' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		self::select( $widget, 'pagination_mode', __( 'Navigation', 'nova-elements' ), array( 'arrows' => __( 'Previous / Next', 'nova-elements' ), 'numbers' => __( 'Page numbers', 'nova-elements' ) ), 'arrows', array( 'pagination' => 'yes' ) );
		$widget->add_control( 'pagination_label', array( 'label' => __( 'Pagination accessible label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Posts pagination', 'nova-elements' ), 'condition' => array( 'pagination' => 'yes' ) ) );
		$widget->end_controls_section();

		$widget->start_controls_section( 'query_advanced', array( 'label' => __( 'Advanced Query', 'nova-elements' ), 'condition' => $collection ) );
		self::select( $widget, 'date_period', __( 'Publication date', 'nova-elements' ), array(
			'' => __( 'All dates', 'nova-elements' ),
			'today' => __( 'Today', 'nova-elements' ),
			'week' => __( 'Last 7 days', 'nova-elements' ),
			'month_rolling' => __( 'Last 30 days', 'nova-elements' ),
			'month' => __( 'Current month', 'nova-elements' ),
			'year' => __( 'Current year', 'nova-elements' ),
			'custom' => __( 'Custom range', 'nova-elements' ),
		), '' );
		foreach ( array( 'date_start' => __( 'Start date', 'nova-elements' ), 'date_end' => __( 'End date', 'nova-elements' ) ) as $key => $label ) {
			$widget->add_control( $key, array( 'label' => $label, 'type' => Controls_Manager::TEXT, 'placeholder' => 'YYYY-MM-DD', 'condition' => array( 'date_period' => 'custom' ) ) );
		}
		$widget->add_control( 'offset', array( 'label' => __( 'Skip first posts', 'nova-elements' ), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'max' => 1000, 'default' => 0, 'condition' => $collection + array( 'pagination!' => 'yes' ) ) );
		$widget->end_controls_section();
	}

	private static function select( Widget_Base|Repeater $controls, string $key, string $label, array $options, string $default, array $condition = array() ): void {
		$controls->add_control( $key, array( 'label' => $label, 'type' => Controls_Manager::SELECT, 'options' => $options, 'default' => $default, 'condition' => $condition ) );
	}
}
