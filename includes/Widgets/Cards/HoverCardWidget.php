<?php

namespace NovaElements\Widgets\Cards;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class HoverCardWidget extends CardWidget {
	protected const KIND = 'hover';

	public function get_name(): string {
		return 'ne-hover-card';
	}

	public function get_title(): string {
		return __( 'Hover Card', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-image-rollover';
	}

	public function get_keywords(): array {
		return array( 'hover', 'card', 'image' );
	}

	protected function content_defaults(): array {
		return array(
			'section' => __( 'Hover Card', 'nova-elements' ),
			'eyebrow_label' => __( 'Label', 'nova-elements' ),
			'eyebrow' => __( 'On the road', 'nova-elements' ),
			'title' => __( 'A closer look', 'nova-elements' ),
			'description' => __( 'Share a project, destination or story with a visual accent on interaction.', 'nova-elements' ),
			'cta_label' => __( 'See the story', 'nova-elements' ),
			'media' => 'image',
			'icon' => 'fas fa-mountain',
		);
	}

	public const EFFECTS = array( 'lift', 'zoom', 'outline', 'none' );

	protected function register_card_details(): void {
		$this->add_control( 'hover_effect', array(
			'label' => __( 'Interaction effect', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'lift' => __( 'Lift card', 'nova-elements' ),
				'zoom' => __( 'Zoom media', 'nova-elements' ),
				'outline' => __( 'Accent outline', 'nova-elements' ),
				'none' => __( 'None', 'nova-elements' ),
			),
			'default' => 'lift',
		) );
	}
}
