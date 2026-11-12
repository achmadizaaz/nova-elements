<?php

namespace NovaElements\Widgets\Cards;

defined( 'ABSPATH' ) || exit;

final class FeatureCardWidget extends CardWidget {
	public function get_name(): string {
		return 'ne-feature-card';
	}

	public function get_title(): string {
		return __( 'Feature Card', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-info-box';
	}

	protected function content_defaults(): array {
		return array(
			'section' => __( 'Feature', 'nova-elements' ),
			'eyebrow_label' => __( 'Label', 'nova-elements' ),
			'eyebrow' => __( 'Design systems', 'nova-elements' ),
			'title' => __( 'A clearer way to build', 'nova-elements' ),
			'description' => __( 'Give your team reusable building blocks for pages that feel consistent and are easier to maintain.', 'nova-elements' ),
			'cta_label' => __( 'Explore the approach', 'nova-elements' ),
			'media' => 'icon',
			'icon' => 'fas fa-layer-group',
		);
	}
}
