<?php

namespace NovaElements\Widgets\Cards;

defined( 'ABSPATH' ) || exit;

final class IconBoxWidget extends CardWidget {
	protected const KIND = 'icon-box';

	public function get_name(): string {
		return 'ne-icon-box';
	}

	public function get_title(): string {
		return __( 'Icon Box', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-icon-box';
	}

	public function get_keywords(): array {
		return array( 'icon', 'card', 'icon' );
	}

	protected function content_defaults(): array {
		return array(
			'section' => __( 'Icon Box', 'nova-elements' ),
			'eyebrow_label' => __( 'Label', 'nova-elements' ),
			'eyebrow' => __( 'Field guide', 'nova-elements' ),
			'title' => __( 'Find your next step', 'nova-elements' ),
			'description' => __( 'Give readers a clear way to find the resources they need.', 'nova-elements' ),
			'cta_label' => __( 'Explore the guide', 'nova-elements' ),
			'media' => 'icon',
			'icon' => 'fas fa-compass',
		);
	}
}
