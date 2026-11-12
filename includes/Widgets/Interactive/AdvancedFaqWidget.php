<?php

namespace NovaElements\Widgets\Interactive;

defined( 'ABSPATH' ) || exit;

final class AdvancedFaqWidget extends PanelWidget {
	public function get_name(): string {
		return 'ne-advanced-faq';
	}

	public function get_title(): string {
		return __( 'Advanced FAQ', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-help-o';
	}

	public function get_keywords(): array {
		return array( 'faq', 'questions', 'answers', 'disclosure' );
	}

	protected function default_panels(): array {
		return array(
			array( 'panel_label' => __( 'Who can access the workspace?', 'nova-elements' ), 'panel_content' => __( 'People invited by the workspace owner can sign in and participate.', 'nova-elements' ) ),
			array( 'panel_label' => __( 'Can I invite my team later?', 'nova-elements' ), 'panel_content' => __( 'Yes. Invite additional collaborators when you are ready to share the project.', 'nova-elements' ) ),
			array( 'panel_label' => __( 'Where can I get help?', 'nova-elements' ), 'panel_content' => __( 'Contact the project team for help with access, settings, or your next steps.', 'nova-elements' ) ),
		);
	}
}
