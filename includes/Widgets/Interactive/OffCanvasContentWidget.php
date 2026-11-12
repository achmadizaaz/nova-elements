<?php

namespace NovaElements\Widgets\Interactive;

defined( 'ABSPATH' ) || exit;

final class OffCanvasContentWidget extends ModalPopupWidget {
	protected const DRAWER = true;

	public function get_name(): string {
		return 'ne-off-canvas-content';
	}

	public function get_title(): string {
		return __( 'Off-Canvas Content', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-sidebar';
	}

	protected function dialog_defaults(): array {
		return array(
			'trigger' => __( 'Open project guide', 'nova-elements' ),
			'title' => __( 'Project guide', 'nova-elements' ),
			'description' => __( 'Read the project brief, find useful resources, and see how to get in touch with the team.', 'nova-elements' ),
			'action' => __( 'Read the full guide', 'nova-elements' ),
		);
	}
}
