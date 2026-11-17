<?php

namespace NovaElements\Widgets\Media;

defined( 'ABSPATH' ) || exit;

final class ImageHoverEffectsWidget extends MediaCollectionWidget {
	protected const KIND = 'hover';

	public function get_name(): string {
		return 'ne-image-hover-effects';
	}

	public function get_title(): string {
		return __( 'Image Hover Effects', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}
}
