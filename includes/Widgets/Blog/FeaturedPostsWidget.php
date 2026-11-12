<?php

namespace NovaElements\Widgets\Blog;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class FeaturedPostsWidget extends PostCollectionWidget {
	protected const LAYOUT = 'featured';
	protected const STYLE = 'blog-editorial';

	public function get_name(): string {
		return 'ne-featured-posts';
	}

	public function get_title(): string {
		return __( 'Featured Posts', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-posts-grid';
	}

	protected function register_collection_controls(): void {
		$this->start_controls_section( 'featured_options', array( 'label' => __( 'Featured Posts', 'nova-elements' ) ) );
		$this->add_control( 'featured_support_excerpt', array(
			'label' => __( 'Excerpts on supporting posts', 'nova-elements' ),
			'type' => Controls_Manager::SWITCHER,
			'condition' => array( 'excerpt' => 'yes' ),
		) );
		$this->end_controls_section();
	}
}
