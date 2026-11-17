<?php

namespace NovaElements\Widgets\Media;

defined( 'ABSPATH' ) || exit;

final class ImageCarouselWidget extends MediaCollectionWidget {
	protected const KIND = 'carousel';

	public function get_name(): string {
		return 'ne-image-carousel';
	}

	public function get_title(): string {
		return __( 'Image Carousel', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}
}
