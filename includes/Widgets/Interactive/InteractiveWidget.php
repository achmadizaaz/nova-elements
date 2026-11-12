<?php

namespace NovaElements\Widgets\Interactive;

use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

abstract class InteractiveWidget extends BaseWidget {
	public function get_style_depends(): array {
		return array( 'nova-elements-interactive-panels' );
	}

	public function get_script_depends(): array {
		return array( 'nova-elements-interactive-panels' );
	}
}
