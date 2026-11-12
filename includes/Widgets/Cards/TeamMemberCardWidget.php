<?php

namespace NovaElements\Widgets\Cards;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class TeamMemberCardWidget extends CardWidget {
	protected const KIND = 'team';

	public function get_name(): string {
		return 'ne-team-member-card';
	}

	public function get_title(): string {
		return __( 'Team Member Card', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-person';
	}

	public function get_keywords(): array {
		return array( 'team', 'staff', 'member', 'portrait' );
	}

	protected function content_defaults(): array {
		return array(
			'section' => __( 'Team member', 'nova-elements' ),
			'eyebrow_label' => __( 'Role', 'nova-elements' ),
			'eyebrow' => __( 'Design lead', 'nova-elements' ),
			'title' => __( 'Sam Rivera', 'nova-elements' ),
			'description' => __( 'Sam leads a team that makes digital services easier to use.', 'nova-elements' ),
			'cta_label' => __( 'Contact Sam', 'nova-elements' ),
			'media' => 'image',
			'icon' => 'fas fa-user',
		);
	}

	protected function register_card_details(): void {
		$this->add_control( 'card_note', array(
			'label' => __( 'Department', 'nova-elements' ),
			'type' => Controls_Manager::TEXT,
			'default' => __( 'Design and Research', 'nova-elements' ),
			'dynamic' => array( 'active' => true ),
		) );
	}
}
