<?php

namespace NovaElements\Widgets\Media;

defined( 'ABSPATH' ) || exit;

final class MediaShowcaseWidget extends MediaCollectionWidget {
	protected const KIND = 'showcase';

	public function get_name(): string {
		return 'ne-media-showcase';
	}

	public function get_title(): string {
		return __( 'Media Showcase', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}
}
