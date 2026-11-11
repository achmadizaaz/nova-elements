<?php

namespace NovaElements\Query;

defined( 'ABSPATH' ) || exit;

final class PostQuery {
	public const MAX_PAGE = 10000;

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

	public function arguments( array $settings, int $page ): array {
		$type = self::choice( $settings['post_type'] ?? null, array_keys( self::post_types() ), 'post' );
		$paginate = 'yes' === ( $settings['pagination'] ?? '' );
		$count = $settings['posts_per_page'] ?? 6;
		$args = array(
			'post_type' => $type,
			'post_status' => 'publish',
			'has_password' => false,
			'posts_per_page' => is_numeric( $count ) ? max( 1, min( 24, (int) $count ) ) : 6,
			'orderby' => array(
				self::choice( $settings['orderby'] ?? null, array( 'date', 'modified', 'title' ), 'date' ) => 'ASC' === ( $settings['order'] ?? '' ) ? 'ASC' : 'DESC',
				'ID' => 'DESC',
			),
			'ignore_sticky_posts' => true,
			'no_found_rows' => ! $paginate,
			'paged' => $paginate ? self::page_number( $page ) : 1,
		);
		if ( ! $paginate && is_numeric( $settings['offset'] ?? null ) ) {
			$args['offset'] = max( 0, min( 1000, (int) $settings['offset'] ) );
		}
		$include = $settings['include_ids'] ?? '';
		$excluded = self::ids( $settings['exclude_ids'] ?? '' );
		if ( is_string( $include ) && '' !== trim( $include ) ) {
			$args['post__in'] = array_values( array_diff( self::ids( $include ), $excluded ) ) ?: array( 0 );
		} elseif ( $excluded ) {
			$args['post__not_in'] = $excluded;
		}

		$taxonomy = $settings['taxonomy'] ?? '';
		if ( '' === $taxonomy ) {
			return $args;
		}

		$ids = self::ids( $settings['term_ids'] ?? '' );
		if ( ! is_string( $taxonomy ) || ! isset( self::taxonomies()[ $taxonomy ] ) || ! is_object_in_taxonomy( $type, $taxonomy ) || ! $ids ) {
			$args['post__in'] = array( 0 );
			return $args;
		}

		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- User-selected indexed taxonomy relationships.
			array( 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => $ids, 'include_children' => true ),
		);
		return $args;
	}

	private static function ids( mixed $value ): array {
		if ( ! is_string( $value ) ) {
			return array();
		}
		$parts = preg_split( '/[\s,]+/', trim( substr( $value, 0, 1000 ) ) ) ?: array();
		$ids = array_map( 'intval', array_filter( $parts, 'ctype_digit' ) );
		return array_slice( array_unique( array_filter( $ids ) ), 0, 50 );
	}

	private static function choice( mixed $value, array $allowed, string $default ): string {
		return in_array( $value, $allowed, true ) ? $value : $default;
	}
}
