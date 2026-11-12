<?php

namespace NovaElements\Widgets\Blog;

defined( 'ABSPATH' ) || exit;

final class PostContext {
	public static function public_post( mixed $reference = '' ): ?\WP_Post {
		if ( '' === $reference ) {
			$post = is_singular() ? get_queried_object() : null;
		} else {
			$id = is_bool( $reference ) ? false : filter_var( $reference, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
			$post = $id ? get_post( $id ) : null;
		}

		return $post instanceof \WP_Post && 'publish' === $post->post_status && '' === $post->post_password
			&& is_post_publicly_viewable( $post ) ? $post : null;
	}
}
