<?php

namespace NovaElements\Widgets\Interactive;

defined( 'ABSPATH' ) || exit;

final class AdvancedAccordionWidget extends PanelWidget {
	public function get_name(): string {
		return 'ne-advanced-accordion';
	}

	public function get_title(): string {
		return __( 'Advanced Accordion', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-accordion';
	}

	public function get_keywords(): array {
		return array( 'accordion', 'disclosure', 'panels', 'expand' );
	}
}
