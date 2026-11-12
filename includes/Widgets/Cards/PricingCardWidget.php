<?php

namespace NovaElements\Widgets\Cards;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class PricingCardWidget extends CardWidget {
	protected const KIND = 'pricing';

	public function get_name(): string {
		return 'ne-pricing-card';
	}

	public function get_title(): string {
		return __( 'Pricing Card', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-price-table';
	}

	public function get_keywords(): array {
		return array( 'price', 'plan', 'subscription', 'offer' );
	}

	protected function content_defaults(): array {
		return array(
			'section' => __( 'Plan', 'nova-elements' ),
			'eyebrow_label' => __( 'Plan badge', 'nova-elements' ),
			'eyebrow' => __( 'For growing teams', 'nova-elements' ),
			'title' => __( 'Studio', 'nova-elements' ),
			'description' => __( 'A flexible plan for small teams working on several projects.', 'nova-elements' ),
			'cta_label' => __( 'Choose Studio', 'nova-elements' ),
			'media' => 'none',
			'icon' => 'fas fa-star',
		);
	}

	protected function register_card_details(): void {
		foreach ( array(
			'price_text' => array( __( 'Price', 'nova-elements' ), __( '$29', 'nova-elements' ) ),
			'price_previous' => array( __( 'Previous price', 'nova-elements' ), '' ),
			'price_period' => array( __( 'Billing period', 'nova-elements' ), __( 'per month', 'nova-elements' ) ),
		) as $name => list( $label, $default ) ) {
			$this->add_control( $name, array( 'label' => $label, 'type' => Controls_Manager::TEXT, 'default' => $default, 'dynamic' => array( 'active' => true ) ) );
		}
		$this->add_control( 'service_points', array(
			'label' => __( 'Included features', 'nova-elements' ),
			'type' => Controls_Manager::TEXTAREA,
			'description' => __( 'Enter up to six features, one per line.', 'nova-elements' ),
			'default' => __( "Unlimited projects\nFive team seats\nPriority support", 'nova-elements' ),
		) );
	}
}
