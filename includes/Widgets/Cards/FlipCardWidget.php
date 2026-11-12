<?php

namespace NovaElements\Widgets\Cards;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class FlipCardWidget extends CardWidget {
	protected const KIND = 'flip';

	public function get_name(): string {
		return 'ne-flip-card';
	}

	public function get_title(): string {
		return __( 'Flip Card', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-flip-box';
	}

	public function get_keywords(): array {
		return array( 'flip', 'card', 'icon' );
	}

	protected function content_defaults(): array {
		return array(
			'section' => __( 'Flip Card', 'nova-elements' ),
			'eyebrow_label' => __( 'Label', 'nova-elements' ),
			'eyebrow' => __( 'Behind the scenes', 'nova-elements' ),
			'title' => __( 'How we work', 'nova-elements' ),
			'description' => __( 'See how we turn a rough idea into a plan the whole team can use.', 'nova-elements' ),
			'cta_label' => __( 'Read about our process', 'nova-elements' ),
			'media' => 'icon',
			'icon' => 'fas fa-layer-group',
		);
	}

	protected function register_card_details(): void {
		$this->add_control( 'flip_label', array(
			'label' => __( 'Reveal control label', 'nova-elements' ),
			'type' => Controls_Manager::TEXT,
			'default' => __( 'View details', 'nova-elements' ),
		) );
		$this->add_control( 'back_title', array(
			'label' => __( 'Reverse heading', 'nova-elements' ),
			'type' => Controls_Manager::TEXT,
			'default' => __( 'From idea to plan', 'nova-elements' ),
			'dynamic' => array( 'active' => true ),
		) );
		$this->add_control( 'back_description', array(
			'label' => __( 'Reverse description', 'nova-elements' ),
			'type' => Controls_Manager::TEXTAREA,
			'default' => __( 'We start with a short workshop and agree on what success looks like before designing anything.', 'nova-elements' ),
			'dynamic' => array( 'active' => true ),
		) );
	}
}
