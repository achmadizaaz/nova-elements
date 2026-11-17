<?php

namespace NovaElements\Widgets\Media;

defined( 'ABSPATH' ) || exit;

final class ImageAccordionWidget extends MediaCollectionWidget {
	protected const KIND = 'accordion';

	public function get_name(): string {
		return 'ne-image-accordion';
	}

	public function get_title(): string {
		return __( 'Image Accordion', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-accordion';
	}
}
