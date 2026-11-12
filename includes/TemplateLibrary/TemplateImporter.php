<?php

namespace NovaElements\TemplateLibrary;

use Elementor\Plugin as Elementor;
use NovaElements\Core\Compatibility;
use NovaElements\Core\WidgetManager;

defined( 'ABSPATH' ) || exit;

final class TemplateImporter {
	public const POST_TYPE = 'elementor_library';

	public function __construct( private TemplateRegistry $registry, private WidgetManager $widgets ) {}

	public static function can_import(): bool {
		$type = get_post_type_object( self::POST_TYPE );
		return $type && current_user_can( $type->cap->create_posts ) && current_user_can( $type->cap->publish_posts );
	}

	public function compatibility( array $manifest ): ?\WP_Error {
		if ( ! Compatibility::elementor_ready() || version_compare( ELEMENTOR_VERSION, $manifest['elementor_min'], '<' ) ) {
			return new \WP_Error( 'elementor', __( 'This template requires a compatible version of Elementor.', 'nova-elements' ) );
		}
		$missing = array_diff( $manifest['required_widgets'], array_keys( $this->widgets->active_widgets() ) );
		if ( $missing ) {
			return new \WP_Error( 'widgets', __( 'Enable the required Nova Elements widgets before importing.', 'nova-elements' ) );
		}
		if ( ! empty( $manifest['assets'] ) && ! current_user_can( 'upload_files' ) ) {
			return new \WP_Error( 'uploads', __( 'Your account needs permission to upload the bundled media.', 'nova-elements' ) );
		}
		return null;
	}

	public function import( string $id ): int|\WP_Error {
		if ( ! self::can_import() ) {
			return new \WP_Error( 'permission', __( 'Your account cannot create published Elementor templates.', 'nova-elements' ) );
		}
		$manifest = $this->registry->find( $id );
		if ( ! $manifest ) {
			return new \WP_Error( 'unknown', __( 'The requested template is unavailable.', 'nova-elements' ) );
		}
		$problem = $this->compatibility( $manifest );
		if ( $problem ) {
			return $problem;
		}
		$path = $this->registry->file( $id, 'template.json' );
		$data = $path ? TemplateRegistry::read_json( $path, 262144 ) : null;
		if ( ! $data || ! TemplateContent::valid( $data, $manifest ) ) {
			return new \WP_Error( 'invalid', __( 'The bundled template failed validation.', 'nova-elements' ) );
		}
		$media = array();
		foreach ( $manifest['assets'] ?? array() as $asset ) {
			$file = $this->registry->file( $id, 'media/' . $asset['file'] );
			if ( ! $file || filesize( $file ) > 2097152 || 'image/webp' !== wp_get_image_mime( $file ) ) {
				return new \WP_Error( 'media', __( 'A bundled image is missing or incompatible.', 'nova-elements' ) );
			}
			$media[] = array( $asset['element'], $asset['control'] ?? 'feature_image', $file, $asset['alt'] );
		}

		$local = Elementor::instance()->templates_manager->get_source( 'local' );
		if ( ! is_callable( array( $local, 'save_item' ) ) ) {
			return new \WP_Error( 'elementor', __( 'Elementor Site Templates are unavailable.', 'nova-elements' ) );
		}
		$created = array();
		$template = 0;
		$complete = false;
		try {
			$bindings = array();
			$images = new AssetImporter();
			foreach ( $media as list( $element, $control, $file, $alt ) ) {
				$image = $images->import( $file, $alt, $created );
				if ( is_wp_error( $image ) ) {
					return new \WP_Error( 'media', __( 'WordPress could not import the bundled image.', 'nova-elements' ) );
				}
				$url = wp_get_attachment_url( $image );
				if ( ! $url ) {
					return new \WP_Error( 'media', __( 'WordPress could not locate the imported image.', 'nova-elements' ) );
				}
				$bindings[ $element ][ $control ] = array( 'id' => $image, 'url' => $url );
			}
			$expected = TemplateContent::widget_counts( $data['content'] );
			TemplateContent::bind_media( $data['content'], $bindings );
			$result = $local->save_item( $data );
			if ( is_wp_error( $result ) || ! is_numeric( $result ) || (int) $result < 1 ) {
				return new \WP_Error( 'save', __( 'Elementor could not save this template.', 'nova-elements' ) );
			}
			$template = (int) $result;
			$document = Elementor::instance()->documents->get( $template );
			if ( get_post_type( $template ) !== self::POST_TYPE || 'publish' !== get_post_status( $template )
				|| ! $document || $expected !== TemplateContent::widget_counts( $document->get_elements_data() ) ) {
				return new \WP_Error( 'save', __( 'Elementor did not persist the expected template content.', 'nova-elements' ) );
			}
			$complete = true;
			// Only expose completed uploads for reuse by another import.
			foreach ( $created as $attachment => $hash ) {
				update_post_meta( $attachment, '_nova_template_image', $hash );
			}
			return $template;
		} catch ( \Throwable $exception ) {
			return $complete ? $template : new \WP_Error( 'save', __( 'The template import was interrupted.', 'nova-elements' ) );
		} finally {
			if ( ! $complete ) {
				if ( $template && self::POST_TYPE === get_post_type( $template ) && (int) get_post_field( 'post_author', $template ) === get_current_user_id() ) {
					wp_delete_post( $template, true );
				}
				foreach ( array_keys( $created ) as $attachment ) {
					wp_delete_attachment( $attachment, true );
				}
			}
		}
	}
}
