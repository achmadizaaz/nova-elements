<?php

namespace NovaElements\Widgets\Media;

defined( 'ABSPATH' ) || exit;

final class ImageGalleryWidget extends MediaCollectionWidget {
	protected const KIND = 'gallery';

	public function get_name(): string {
		return 'ne-image-gallery';
	}

	public function get_title(): string {
		return __( 'Image Gallery', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}
}
