<?php

namespace NovaElements\Widgets\Media;

defined( 'ABSPATH' ) || exit;

final class VideoGalleryWidget extends MediaCollectionWidget {
	protected const KIND = 'videos';

	public function get_name(): string {
		return 'ne-video-gallery';
	}

	public function get_title(): string {
		return __( 'Video Gallery', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-video-camera';
	}
}
