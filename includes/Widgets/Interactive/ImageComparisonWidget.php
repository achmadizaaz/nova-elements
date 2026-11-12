<?php

namespace NovaElements\Widgets\Interactive;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class ImageComparisonWidget extends InteractiveWidget {
	public function get_name(): string {
		return 'ne-image-comparison';
	}

	public function get_title(): string {
		return __( 'Image Comparison', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-image-before-after';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => $this->get_title() ) );
		foreach ( array( 'before' => __( 'Before', 'nova-elements' ), 'after' => __( 'After', 'nova-elements' ) ) as $key => $label ) {
			/* translators: %s: the side of an image comparison. */
			$this->add_control( $key . '_image', array( 'label' => sprintf( __( '%s image', 'nova-elements' ), $label ), 'type' => Controls_Manager::MEDIA, 'dynamic' => array( 'active' => true ) ) );
			/* translators: %s: the side of an image comparison. */
			$this->add_control( $key . '_label', array( 'label' => sprintf( __( '%s label', 'nova-elements' ), $label ), 'type' => Controls_Manager::TEXT, 'default' => $label ) );
		}
		$this->add_control( 'range_label', array( 'label' => __( 'Slider accessible label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Percent of the after image shown', 'nova-elements' ) ) );
		$this->add_control( 'initial_position', array( 'label' => __( 'Initial position (%)', 'nova-elements' ), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'max' => 100, 'default' => 50 ) );
		$this->end_controls_section();
		$this->register_visual_presets();
		$this->start_controls_section( 'comparison_style', array( 'label' => __( 'Comparison', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'caption', __( 'Labels', 'nova-elements' ), '.nova-comparison__labels' );
		$this->add_panel_style( '.nova-comparison__stage' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$before = $this->media_image( $settings['before_image'] ?? null, 'large' );
		$after = $this->media_image( $settings['after_image'] ?? null, 'large' );
		if ( '' === $before && '' === $after ) {
			if ( is_user_logged_in() && current_user_can( 'edit_posts' ) && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Select the before and after images.', 'nova-elements' ) . '</p>';
			}
			return;
		}
		$before_label = $this->text_value( $settings['before_label'] ?? null );
		$after_label = $this->text_value( $settings['after_label'] ?? null );
		$range_label = $this->text_value( $settings['range_label'] ?? null );
		$position = $settings['initial_position'] ?? 50;
		$position = is_numeric( $position ) ? max( 0, min( 100, (int) $position ) ) : 50;
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		require dirname( __DIR__, 3 ) . '/templates/widgets/image-comparison.php';
	}
}
