<?php

namespace NovaElements\Widgets\Blog;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class MagazineLayoutWidget extends PostCollectionWidget {
	protected const LAYOUT = 'magazine';
	protected const STYLE = 'blog-editorial';

	public function get_name(): string {
		return 'ne-magazine-layout';
	}

	public function get_title(): string {
		return __( 'Magazine Layout', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-posts-masonry';
	}

	protected function register_collection_controls(): void {
		$this->start_controls_section( 'magazine_options', array( 'label' => __( 'Magazine Layout', 'nova-elements' ) ) );
		$this->add_control( 'magazine_support_images', array(
			'label' => __( 'Images on supporting posts', 'nova-elements' ),
			'type' => Controls_Manager::SWITCHER,
			'default' => 'yes',
			'condition' => array( 'image' => 'yes' ),
		) );
		$this->end_controls_section();
	}
}
