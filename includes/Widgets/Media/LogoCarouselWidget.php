<?php

namespace NovaElements\Widgets\Media;

defined( 'ABSPATH' ) || exit;

final class LogoCarouselWidget extends MediaCollectionWidget {
	protected const KIND = 'logos';

	public function get_name(): string {
		return 'ne-logo-carousel';
	}

	public function get_title(): string {
		return __( 'Logo Carousel', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}
}
