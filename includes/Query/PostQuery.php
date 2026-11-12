<?php

namespace NovaElements\Query;

defined( 'ABSPATH' ) || exit;

final class PostQuery {
	public const MAX_PAGE = 10000;
	public const ORDERS = array( 'date', 'modified', 'title', 'menu_order', 'comment_count', 'ID', 'rand', 'meta_value_num' );
	public const META_OPERATORS = array( '=', '!=', 'LIKE', '>', '<', 'BETWEEN', 'EXISTS', 'NOT EXISTS' );

	public static function post_types(): array {
		$types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $types['attachment'] );
		return wp_list_pluck( array_filter( $types, 'is_post_type_viewable' ), 'label' );
	}

	public static function taxonomies(): array {
		return wp_list_pluck( get_taxonomies( array( 'public' => true ), 'objects' ), 'label' );
	}

	public static function page_number( mixed $value ): int {
		return is_scalar( $value ) && preg_match( '/^[0-9]{1,5}$/D', (string) $value )
			? max( 1, min( self::MAX_PAGE, (int) $value ) )
			: 1;
	}

	/** Explicitly opted-in public keys. A registered schema alone does not imply public visibility. */
	public static function meta_fields( string $type ): array {
		$registered = get_registered_meta_keys( 'post', $type ) + get_registered_meta_keys( 'post' );
		$allowed = (array) apply_filters( 'nova_elements_queryable_meta', array(), $type );
		return array_filter(
			array_intersect_key( $registered, array_fill_keys( array_filter( $allowed, 'is_string' ), true ) ),
			static fn( array $schema, string $key ): bool => ! is_protected_meta( $key, 'post' )
				&& $schema['single'] && in_array( $schema['type'], array( 'string', 'number', 'integer' ), true ),
			ARRAY_FILTER_USE_BOTH
		);
	}

	public function run( array $settings, int $page = 1, ?\WP_Post $context = null, ?\WP_Term $archive = null ): QueryResult {
		$args = $this->arguments( $settings, $page, $context, $archive );
		if ( is_wp_error( $args ) ) {
			return new QueryResult( array(), 1, 0, 0, $args );
		}
		$query = new \WP_Query( $args );
		$posts = array_values( array_filter(
			$query->posts,
			static fn( $post ): bool => $post instanceof \WP_Post && $args['post_type'] === $post->post_type
				&& 'publish' === $post->post_status && '' === $post->post_password && is_post_publicly_viewable( $post )
		) );
		return new QueryResult(
			$posts,
			$args['paged'],
			min( self::MAX_PAGE, (int) $query->max_num_pages ),
			$args['no_found_rows'] || ( ! $posts && $args['paged'] > 1 ) ? null : (int) $query->found_posts
		);
	}

	/** Compile only supported settings. Context objects are supplied by the calling widget. */
	public function arguments( array $settings, int $page, ?\WP_Post $context = null, ?\WP_Term $archive = null ): array|\WP_Error {
		$type = $settings['post_type'] ?? 'post';
		$source = $settings['query_source'] ?? 'type';
		if ( ! in_array( $source, array( 'type', 'manual', 'current', 'related', 'archive' ), true ) ) {
			return self::error( 'source', __( 'Choose a supported content source.', 'nova-elements' ) );
		}
		if ( in_array( $source, array( 'current', 'related' ), true ) ) {
			$type = $context?->post_type;
			if ( ! $context || 'publish' !== $context->post_status || '' !== $context->post_password || ! is_post_publicly_viewable( $context ) ) {
				return self::error( 'context', __( 'This query needs a published, publicly viewable post as its context.', 'nova-elements' ) );
			}
		}
		if ( ! is_string( $type ) || ! isset( self::post_types()[ $type ] ) ) {
			return self::error( 'post_type', __( 'The selected post type is unavailable or is not public.', 'nova-elements' ) );
		}

		$paginate = 'yes' === ( $settings['pagination'] ?? '' ) && 'current' !== $source;
		$count = $settings['posts_per_page'] ?? 6;
		$order = 'ASC' === ( $settings['order'] ?? '' ) ? 'ASC' : 'DESC';
		$orderby = self::choice( $settings['orderby'] ?? null, self::ORDERS, 'date' );
		if ( $paginate && 'rand' === $orderby ) {
			$orderby = 'date';
		}
		$args = array(
			'post_type' => $type,
			'post_status' => 'publish',
			'has_password' => false,
			'posts_per_page' => is_numeric( $count ) ? max( 1, min( 24, (int) $count ) ) : 6,
			'orderby' => array( $orderby => $order ) + array( 'ID' => 'DESC' ),
			'ignore_sticky_posts' => true,
			'no_found_rows' => ! $paginate,
			'paged' => $paginate ? self::page_number( $page ) : 1,
		);
		if ( 'current' === $source ) {
			return array( 'post__in' => array( $context->ID ), 'posts_per_page' => 1, 'orderby' => 'ID' ) + $args;
		}
		if ( ! $paginate && is_numeric( $settings['offset'] ?? null ) ) {
			$args['offset'] = max( 0, min( 1000, (int) $settings['offset'] ) );
		}
		if ( 'related' !== $source && is_post_type_hierarchical( $type ) && '' !== ( $settings['parent_id'] ?? '' ) ) {
			$parent = $settings['parent_id'];
			if ( ( ! is_string( $parent ) && ! is_int( $parent ) ) || ! ctype_digit( (string) $parent ) ) {
				return self::error( 'parent', __( 'Enter a valid parent post ID or zero for top-level posts.', 'nova-elements' ) );
			}
			$args['post_parent'] = (int) $parent;
		}

		$include = $settings['include_ids'] ?? '';
		$included = self::ids( $include );
		$excluded = self::ids( $settings['exclude_ids'] ?? '' );
		if ( $context && ( 'related' === $source || 'yes' === ( $settings['exclude_current'] ?? '' ) ) ) {
			$excluded[] = $context->ID;
		}
		$restrict = 'manual' === $source || ( is_string( $include ) && '' !== trim( $include ) );
		$sticky = $settings['sticky_posts'] ?? '';
		if ( 'related' !== $source && 'post' === $type && in_array( $sticky, array( 'only', 'exclude' ), true ) ) {
			$ids = wp_parse_id_list( (array) get_option( 'sticky_posts', array() ) );
			if ( 'only' === $sticky ) {
				$included = $restrict ? array_intersect( $included, $ids ) : $ids;
				$restrict = true;
			} else {
				$excluded = array_merge( $excluded, $ids );
			}
		}
		if ( $restrict ) {
			$args['post__in'] = array_values( array_diff( $included, $excluded ) ) ?: array( 0 );
		} elseif ( $excluded ) {
			$args['post__not_in'] = array_values( array_unique( $excluded ) );
		}
		$authors = self::ids( $settings['include_authors'] ?? '' );
		$excluded_authors = self::ids( $settings['exclude_authors'] ?? '' );
		if ( $authors ) {
			$args['author__in'] = array_values( array_diff( $authors, $excluded_authors ) );
			if ( ! $args['author__in'] ) {
				$args['post__in'] = array( 0 );
			}
		} elseif ( $excluded_authors ) {
			$args['author__not_in'] = $excluded_authors;
		}

		$tax = $this->taxonomy_query( $settings, $type, $source, $context, $archive );
		$date = $this->date_query( $settings );
		$meta = $this->meta_query( $settings, $type, $orderby );
		foreach ( array( 'tax_query' => $tax, 'date_query' => $date, 'meta_query' => $meta ) as $key => $value ) {
			if ( is_wp_error( $value ) ) {
				return $value;
			}
			if ( $value ) {
				$args[ $key ] = $value;
			}
		}
		if ( 'meta_value_num' === $orderby ) {
			$args['orderby'] = array( 'nova_numeric_order' => $order, 'ID' => 'DESC' );
		}
		// Core caches deterministic post queries. Avoid stale results from direct metadata writes and cached random order.
		if ( $meta || 'rand' === $orderby ) {
			$args['cache_results'] = false;
		}
		return $args;
	}

	private function taxonomy_query( array $settings, string $type, string $source, ?\WP_Post $context, ?\WP_Term $archive ): array|\WP_Error {
		$group = array( 'relation' => 'OR' === ( $settings['taxonomy_relation'] ?? '' ) ? 'OR' : 'AND' );
		$clauses = $settings['taxonomy_filters'] ?? array();
		$error = self::error( 'taxonomy', __( 'Check that each taxonomy and term is available for the selected post type.', 'nova-elements' ) );
		if ( ! is_array( $clauses ) || count( $clauses ) > 5 ) {
			return $error;
		}
		if ( '' !== ( $settings['taxonomy'] ?? '' ) ) {
			array_unshift( $clauses, array( 'filter_taxonomy' => $settings['taxonomy'], 'filter_terms' => $settings['term_ids'] ?? '', 'filter_children' => 'yes' ) );
		}
		$required = array();
		if ( 'related' === $source || 'archive' === $source ) {
			$taxonomy = 'related' === $source ? ( $settings['related_taxonomy'] ?? 'category' ) : $archive?->taxonomy;
			if ( ! is_string( $taxonomy ) || ! isset( self::taxonomies()[ $taxonomy ] ) || ! is_object_in_taxonomy( $type, $taxonomy ) ) {
				return $error;
			}
			$ids = 'archive' === $source ? array( $archive->term_id ) : wp_get_object_terms( $context->ID, $taxonomy, array( 'fields' => 'ids', 'number' => 50 ) );
			if ( is_wp_error( $ids ) || ! $ids ) {
				return $error;
			}
			$required[] = array( 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => $ids, 'include_children' => 'archive' === $source );
		}
		$branch_count = 0;
		foreach ( $clauses as $clause ) {
			$taxonomy = $clause['filter_taxonomy'] ?? '';
			if ( ! is_array( $clause ) || ! is_string( $taxonomy ) || ! isset( self::taxonomies()[ $taxonomy ] ) || ! is_object_in_taxonomy( $type, $taxonomy ) ) {
				return $error;
			}
			$ids = self::ids( $clause['filter_terms'] ?? '' );
			if ( 'yes' === ( $clause['filter_current_term'] ?? '' ) ) {
				$ids = $archive && $archive->taxonomy === $taxonomy ? array( $archive->term_id ) : array();
			}
			$terms = $ids ? get_terms( array( 'taxonomy' => $taxonomy, 'include' => $ids, 'hide_empty' => false, 'fields' => 'ids', 'update_term_meta_cache' => false ) ) : array();
			if ( is_wp_error( $terms ) || ! $terms || array_diff( $ids, $terms ) ) {
				return $error;
			}
			$operator = self::choice( $clause['filter_operator'] ?? null, array( 'IN', 'NOT IN', 'AND' ), 'IN' );
			$children = 'yes' === ( $clause['filter_children'] ?? '' ) && is_taxonomy_hierarchical( $taxonomy );
			$filter = array( 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => $ids, 'operator' => $operator, 'include_children' => $children );
			if ( 'NOT IN' === $operator ) {
				$required[] = $filter;
			} elseif ( 'AND' === $operator && $children ) {
				$branch_count += count( $ids );
				if ( $branch_count > 12 ) {
					return self::error( 'taxonomy', __( 'Use up to 12 hierarchical term branches in filters that require all selected terms.', 'nova-elements' ) );
				}
				// Require one match in each selected branch, rather than every descendant.
				$group[] = array( 'relation' => 'AND' ) + array_map(
					static fn( int $id ): array => array( 'terms' => array( $id ), 'operator' => 'IN' ) + $filter,
					$ids
				);
			} else {
				$group[] = $filter;
			}
		}
		if ( count( $group ) > 1 ) {
			$required[] = $group;
		}
		return $required ? array( 'relation' => 'AND' ) + $required : array();
	}

	private function date_query( array $settings ): array|\WP_Error {
		$period = $settings['date_period'] ?? '';
		if ( '' === $period ) {
			return array();
		}
		if ( ! in_array( $period, array( 'today', 'week', 'month_rolling', 'month', 'year', 'custom' ), true ) ) {
			return self::error( 'date', __( 'Choose a supported publication date range.', 'nova-elements' ) );
		}
		$today = current_datetime();
		$start = match ( $period ) {
			'today' => $today->format( 'Y-m-d' ),
			'week' => $today->modify( '-6 days' )->format( 'Y-m-d' ),
			'month_rolling' => $today->modify( '-29 days' )->format( 'Y-m-d' ),
			'month' => $today->format( 'Y-m-01' ),
			'year' => $today->format( 'Y-01-01' ),
			'custom' => $settings['date_start'] ?? '',
			default => '',
		};
		$end = 'custom' === $period ? ( $settings['date_end'] ?? '' ) : ( '' !== $start ? $today->format( 'Y-m-d' ) : '' );
		if ( ( '' !== $start && ! self::valid_date( $start ) ) || ( '' !== $end && ! self::valid_date( $end ) )
			|| ( '' !== $start && '' !== $end && $start > $end ) ) {
			return self::error( 'date', __( 'Enter a valid date range in YYYY-MM-DD format.', 'nova-elements' ) );
		}
		$range = array_filter( array( 'after' => $start, 'before' => $end ), static fn( $date ): bool => '' !== $date );
		return $range ? array( $range + array( 'inclusive' => true, 'column' => 'post_date' ) ) : array();
	}

	private function meta_query( array $settings, string $type, string $orderby ): array|\WP_Error {
		$filter = $settings['query_meta_key'] ?? '';
		$sort = 'meta_value_num' === $orderby ? ( $settings['query_sort_key'] ?? '' ) : null;
		if ( '' === $filter && null === $sort ) {
			return array();
		}
		$fields = self::meta_fields( $type );
		$error = self::error( 'meta', __( 'Check the selected public custom field and its comparison values.', 'nova-elements' ) );
		$clauses = array();
		if ( null !== $sort ) {
			if ( ! is_string( $sort ) || ! in_array( $fields[ $sort ]['type'] ?? '', array( 'number', 'integer' ), true ) ) {
				return $error;
			}
			$clauses['nova_numeric_order'] = array( 'key' => $sort, 'compare' => 'EXISTS', 'type' => 'DECIMAL(20,6)' );
		}
		if ( '' === $filter ) {
			return $clauses;
		}
		if ( ! is_string( $filter ) || ! isset( $fields[ $filter ] ) ) {
			return $error;
		}
		$operator = $settings['query_meta_operator'] ?? '=';
		$format = $settings['query_meta_format'] ?? 'text';
		if ( ! in_array( $operator, self::META_OPERATORS, true ) || ! in_array( $format, array( 'text', 'number', 'date' ), true ) ) {
			return $error;
		}
		$clause = array( 'key' => $filter, 'compare' => $operator );
		if ( ! in_array( $operator, array( 'EXISTS', 'NOT EXISTS' ), true ) ) {
			if ( 'LIKE' === $operator && 'text' !== $format ) {
				return $error;
			}
			$values = array( $settings['query_meta_value'] ?? '' );
			if ( 'BETWEEN' === $operator ) {
				$values[] = $settings['query_meta_end'] ?? '';
			}
			foreach ( $values as $value ) {
				if ( ! is_string( $value ) || strlen( $value ) > 160
					|| ( 'date' === $format && ! self::valid_date( $value ) )
					|| ( 'number' === $format && ! preg_match( '/^-?[0-9]{1,14}(?:\.[0-9]{1,6})?$/D', $value ) ) ) {
					return $error;
				}
			}
			if ( 'BETWEEN' === $operator && ( 'number' === $format ? (float) $values[0] > (float) $values[1] : strcmp( $values[0], $values[1] ) > 0 ) ) {
				return $error;
			}
			$clause['value'] = 'BETWEEN' === $operator ? $values : $values[0];
			$clause['type'] = array( 'text' => 'CHAR', 'number' => 'DECIMAL(20,6)', 'date' => 'DATE' )[ $format ];
		}
		$clauses[] = $clause;
		return $clauses;
	}

	private static function valid_date( mixed $value ): bool {
		return is_string( $value ) && preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/D', $value, $parts )
			&& checkdate( (int) $parts[2], (int) $parts[3], (int) $parts[1] );
	}

	private static function ids( mixed $value ): array {
		if ( ! is_string( $value ) ) {
			return array();
		}
		$parts = preg_split( '/[\s,]+/', trim( substr( $value, 0, 1000 ) ) ) ?: array();
		$ids = array_map( 'intval', array_filter( $parts, 'ctype_digit' ) );
		return array_slice( array_values( array_unique( array_filter( $ids ) ) ), 0, 50 );
	}

	private static function choice( mixed $value, array $allowed, string $default ): string {
		return in_array( $value, $allowed, true ) ? $value : $default;
	}

	private static function error( string $code, string $message ): \WP_Error {
		return new \WP_Error( 'nova_elements_query_' . $code, $message );
	}
}
