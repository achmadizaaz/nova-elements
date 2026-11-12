<?php

namespace NovaElements\Widgets\Interactive;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

final class InteractiveHotspotWidget extends InteractiveWidget {
	public function get_name(): string {
		return 'ne-interactive-hotspot';
	}

	public function get_title(): string {
		return __( 'Interactive Hotspot', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-hotspot';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => $this->get_title() ) );
		$this->add_control( 'feature_image', array( 'label' => __( 'Image', 'nova-elements' ), 'type' => Controls_Manager::MEDIA, 'dynamic' => array( 'active' => true ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'marker_label', array( 'label' => __( 'Marker label', 'nova-elements' ), 'type' => Controls_Manager::TEXT ) );
		$repeater->add_control( 'marker_text', array( 'label' => __( 'Marker description', 'nova-elements' ), 'type' => Controls_Manager::TEXTAREA ) );
		foreach ( array( 'marker_x' => __( 'Horizontal position (%)', 'nova-elements' ), 'marker_y' => __( 'Vertical position (%)', 'nova-elements' ) ) as $key => $label ) {
			$repeater->add_control( $key, array( 'label' => $label, 'type' => Controls_Manager::NUMBER, 'min' => 0, 'max' => 100, 'default' => 50 ) );
		}
		$this->add_control( 'markers', array(
			'label' => __( 'Markers', 'nova-elements' ),
			'type' => Controls_Manager::REPEATER,
			'description' => __( 'Displays up to six markers. Horizontal position is measured from the left edge of the image.', 'nova-elements' ),
			'fields' => $repeater->get_controls(),
			'title_field' => '{{ marker_label }}',
			'default' => array(
				array( 'marker_label' => __( 'Entrance', 'nova-elements' ), 'marker_text' => __( 'The main entrance is open during visiting hours.', 'nova-elements' ), 'marker_x' => 28, 'marker_y' => 66 ),
				array( 'marker_label' => __( 'Upper level', 'nova-elements' ), 'marker_text' => __( 'Follow the stairs to the upper level.', 'nova-elements' ), 'marker_x' => 70, 'marker_y' => 35 ),
			),
		) );
		$this->end_controls_section();
		$this->register_visual_presets();
		$this->start_controls_section( 'marker_style', array( 'label' => __( 'Markers and description', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'marker', __( 'Marker', 'nova-elements' ), '.nova-hotspot__marker' );
		$this->add_text_style( 'body', __( 'Description', 'nova-elements' ), '.nova-hotspot__panel' );
		$this->add_panel_style( '.nova-hotspot__panel' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$image = $this->media_image( $settings['feature_image'] ?? null, 'large' );
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$markers = array();
		$rows = $settings['markers'] ?? null;
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			if ( ! is_array( $row ) || '' === $this->text_value( $row['marker_label'] ?? null ) ) {
				continue;
			}
			$markers[] = array(
				'label' => $this->text_value( $row['marker_label'] ),
				'text' => $this->text_value( $row['marker_text'] ?? null ),
				'x' => $this->position( $row['marker_x'] ?? null ),
				'y' => $this->position( $row['marker_y'] ?? null ),
			);
			if ( 6 === count( $markers ) ) {
				break;
			}
		}
		if ( '' === $image && ! $markers ) {
			return;
		}
		require dirname( __DIR__, 3 ) . '/templates/widgets/interactive-hotspot.php';
	}

	private function position( mixed $value ): int {
		return is_numeric( $value ) ? max( 0, min( 100, (int) $value ) ) : 50;
	}
}
