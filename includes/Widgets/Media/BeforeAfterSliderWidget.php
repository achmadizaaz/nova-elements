<?php

namespace NovaElements\Widgets\Media;

use NovaElements\Widgets\Interactive\ImageComparisonWidget;

defined( 'ABSPATH' ) || exit;

final class BeforeAfterSliderWidget extends ImageComparisonWidget {
	public function get_name(): string {
		return 'ne-before-after-slider';
	}

	public function get_title(): string {
		return __( 'Before/After Slider', 'nova-elements' );
	}
}
