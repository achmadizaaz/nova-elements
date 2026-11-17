<?php

namespace NovaElements\Widgets\Media;

defined( 'ABSPATH' ) || exit;

final class VideoPopupWidget extends MediaCollectionWidget {
	protected const KIND = 'popup';

	public function get_name(): string {
		return 'ne-video-popup';
	}

	public function get_title(): string {
		return __( 'Video Popup', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-video-camera';
	}
}
