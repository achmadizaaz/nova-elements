<?php

namespace NovaElements\TemplateLibrary;

defined( 'ABSPATH' ) || exit;

final class AssetImporter {
	public function import( string $file, string $alt, array &$created ): int|\WP_Error {
		$hash = hash_file( 'sha256', $file );
		if ( ! $hash ) {
			return new \WP_Error( 'media', __( 'Could not read the bundled image.', 'nova-elements' ) );
		}
		$ids = get_posts(
			array(
				'post_type' => 'attachment', 'post_status' => 'inherit', 'fields' => 'ids',
				'posts_per_page' => 5, 'meta_key' => '_nova_template_image', 'meta_value' => $hash,
			)
		);
		foreach ( $ids as $id ) {
			$existing = get_attached_file( $id );
			if ( $existing && is_readable( $existing ) && hash_file( 'sha256', $existing ) === $hash && wp_get_attachment_url( $id ) ) {
				return (int) $id;
			}
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$name = sanitize_file_name( 'nova-' . basename( $file ) );
		$temp = wp_tempnam( $name );
		if ( ! $temp ) {
			return new \WP_Error( 'media', __( 'Could not prepare the bundled image.', 'nova-elements' ) );
		}
		try {
			if ( ! copy( $file, $temp ) ) {
				return new \WP_Error( 'media', __( 'Could not prepare the bundled image.', 'nova-elements' ) );
			}
			$id = media_handle_sideload( array( 'name' => $name, 'tmp_name' => $temp ), 0 );
			if ( is_wp_error( $id ) ) {
				return $id;
			}
			$created[ $id ] = $hash;
			update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
			return $id;
		} finally {
			if ( is_file( $temp ) ) {
				wp_delete_file( $temp );
			}
		}
	}
}
