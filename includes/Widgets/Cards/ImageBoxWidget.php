<?php

namespace NovaElements\Widgets\Cards;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class ImageBoxWidget extends CardWidget {
	protected const KIND = 'image-box';

	public function get_name(): string {
		return 'ne-image-box';
	}

	public function get_title(): string {
		return __( 'Image Box', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-image-box';
	}

	public function get_keywords(): array {
		return array( 'image', 'photo', 'caption', 'portfolio' );
	}

	protected function content_defaults(): array {
		return array(
			'section' => __( 'Image box', 'nova-elements' ),
			'eyebrow_label' => __( 'Label', 'nova-elements' ),
			'eyebrow' => __( 'Selected work', 'nova-elements' ),
			'title' => __( 'Spaces for everyday life', 'nova-elements' ),
			'description' => __( 'Show a project or place and give readers a way to explore it.', 'nova-elements' ),
			'cta_label' => __( 'Explore the project', 'nova-elements' ),
			'media' => 'image',
			'icon' => 'fas fa-image',
		);
	}

	protected function register_card_details(): void {
		$this->add_control( 'image_caption', array(
			'label' => __( 'Image caption', 'nova-elements' ),
			'type' => Controls_Manager::TEXT,
			'dynamic' => array( 'active' => true ),
			'condition' => array( 'media_type' => 'image' ),
		) );
	}
}
