<?php

namespace NovaElements\Widgets\Blog;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class BlogCarouselWidget extends PostCollectionWidget {
	protected const LAYOUT = 'carousel';

	public function get_name(): string {
		return 'ne-blog-carousel';
	}

	public function get_title(): string {
		return __( 'Blog Carousel', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-posts-carousel';
	}

	public function get_script_depends(): array {
		return array( 'nova-elements-blog-carousel' );
	}

	protected function register_collection_controls(): void {
		$this->start_controls_section( 'carousel', array( 'label' => __( 'Carousel', 'nova-elements' ) ) );
		$this->add_control( 'carousel_label', array(
			'label' => __( 'Accessible name', 'nova-elements' ),
			'type' => Controls_Manager::TEXT,
			'default' => __( 'Latest articles', 'nova-elements' ),
		) );
		foreach ( array(
			'carousel_arrows' => __( 'Previous and next buttons', 'nova-elements' ),
			'carousel_dots' => __( 'Slide page buttons', 'nova-elements' ),
			'carousel_autoplay' => __( 'Autoplay', 'nova-elements' ),
		) as $key => $label ) {
			$this->add_control( $key, array(
				'label' => $label,
				'type' => Controls_Manager::SWITCHER,
				'default' => 'carousel_autoplay' === $key ? '' : 'yes',
			) );
		}
		$this->add_control( 'carousel_interval', array(
			'label' => __( 'Seconds between advances', 'nova-elements' ),
			'type' => Controls_Manager::NUMBER,
			'default' => 6,
			'min' => 4,
			'max' => 20,
			'condition' => array( 'carousel_autoplay' => 'yes' ),
		) );
		$this->end_controls_section();
	}
}
