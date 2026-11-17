<?php

namespace NovaElements\Widgets\Media;

defined( 'ABSPATH' ) || exit;

final class FilterableGalleryWidget extends MediaCollectionWidget {
	protected const KIND = 'filter';

	public function get_name(): string {
		return 'ne-filterable-gallery';
	}

	public function get_title(): string {
		return __( 'Filterable Gallery', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-gallery-grid';
	}
}
