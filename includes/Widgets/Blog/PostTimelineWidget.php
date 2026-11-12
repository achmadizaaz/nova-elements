<?php

namespace NovaElements\Widgets\Blog;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class PostTimelineWidget extends PostCollectionWidget {
	protected const LAYOUT = 'timeline';
	protected const STYLE = 'blog-editorial';

	public function get_name(): string {
		return 'ne-post-timeline';
	}

	public function get_title(): string {
		return __( 'Post Timeline', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-time-line';
	}

	protected function register_collection_controls(): void {
		$this->start_controls_section( 'timeline_options', array( 'label' => __( 'Post Timeline', 'nova-elements' ) ) );
		$this->add_control( 'timeline_date_format', array(
			'label' => __( 'Timeline date format', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'options' => array( 'site' => __( 'Site date format', 'nova-elements' ), 'month' => __( 'Month and year', 'nova-elements' ), 'year' => __( 'Year only', 'nova-elements' ) ),
			'default' => 'site',
			'condition' => array( 'date' => 'yes' ),
		) );
		$this->end_controls_section();
	}
}
