<?php

namespace NovaElements\Widgets\Interactive;

defined( 'ABSPATH' ) || exit;

final class ContentToggleWidget extends PanelWidget {
	protected const TABS = true;
	protected const MAX_PANELS = 2;

	public function get_name(): string {
		return 'ne-content-toggle';
	}

	public function get_title(): string {
		return __( 'Content Toggle', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-toggle';
	}

	protected function default_panels(): array {
		return array(
			array( 'panel_label' => __( 'Monthly', 'nova-elements' ), 'panel_content' => __( 'Pay month to month and adjust your plan as your team changes.', 'nova-elements' ) ),
			array( 'panel_label' => __( 'Yearly', 'nova-elements' ), 'panel_content' => __( 'Pay once a year and keep the same plan for twelve months.', 'nova-elements' ) ),
		);
	}
}
