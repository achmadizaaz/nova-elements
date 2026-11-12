<?php

namespace NovaElements\Widgets\Interactive;

defined( 'ABSPATH' ) || exit;

final class AdvancedTabsWidget extends PanelWidget {
	protected const TABS = true;

	public function get_name(): string {
		return 'ne-advanced-tabs';
	}

	public function get_title(): string {
		return __( 'Advanced Tabs', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-tabs';
	}

	public function get_keywords(): array {
		return array( 'tabs', 'panels', 'navigation', 'sections' );
	}
}
