<?php

namespace NovaElements\Widgets\Cards;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class InfoCardWidget extends CardWidget {
	protected const KIND = 'info';

	public function get_name(): string {
		return 'ne-info-card';
	}

	public function get_title(): string {
		return __( 'Info Card', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-alert';
	}

	public function get_keywords(): array {
		return array( 'info', 'callout', 'note' );
	}

	protected function content_defaults(): array {
		return array(
			'section' => __( 'Information', 'nova-elements' ),
			'eyebrow_label' => __( 'Label', 'nova-elements' ),
			'eyebrow' => __( 'Project guide', 'nova-elements' ),
			'title' => __( 'Keep everyone in the loop', 'nova-elements' ),
			'description' => __( 'Give your team a single place to find the decisions and documents they need.', 'nova-elements' ),
			'cta_label' => __( 'Read the guide', 'nova-elements' ),
			'media' => 'icon',
			'icon' => 'fas fa-info',
		);
	}

	protected function register_card_details(): void {
		$this->add_control( 'card_note', array( 'label' => __( 'Note', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Keep this page handy for project handoffs.', 'nova-elements' ), 'dynamic' => array( 'active' => true ) ) );
	}
}
