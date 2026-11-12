<?php

namespace NovaElements\Widgets\Cards;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class ServiceCardWidget extends CardWidget {
	protected const KIND = 'service';

	public function get_name(): string {
		return 'ne-service-card';
	}

	public function get_title(): string {
		return __( 'Service Card', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-tools';
	}

	public function get_keywords(): array {
		return array( 'service', 'offer', 'benefits' );
	}

	protected function content_defaults(): array {
		return array(
			'section' => __( 'Service', 'nova-elements' ),
			'eyebrow_label' => __( 'Category', 'nova-elements' ),
			'eyebrow' => __( 'Digital services', 'nova-elements' ),
			'title' => __( 'Brand and web design', 'nova-elements' ),
			'description' => __( 'Build a clear identity and a website that makes it easy to understand what you do.', 'nova-elements' ),
			'cta_label' => __( 'Explore the service', 'nova-elements' ),
			'media' => 'icon',
			'icon' => 'fas fa-pen-nib',
		);
	}

	protected function register_card_details(): void {
		$this->add_control( 'service_points', array(
			'label' => __( 'Service benefits', 'nova-elements' ),
			'type' => Controls_Manager::TEXTAREA,
			'description' => __( 'Enter up to six benefits, one per line.', 'nova-elements' ),
			'default' => implode( "\n", array( __( 'Visual identity', 'nova-elements' ), __( 'Responsive website', 'nova-elements' ), __( 'Launch handoff', 'nova-elements' ) ) ),
		) );
	}
}
