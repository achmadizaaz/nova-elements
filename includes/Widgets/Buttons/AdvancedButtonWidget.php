<?php

namespace NovaElements\Widgets\Buttons;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

final class AdvancedButtonWidget extends BaseWidget {
	public function get_name(): string {
		return 'ne-advanced-button';
	}

	public function get_title(): string {
		return __( 'Advanced Button', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-button';
	}

	public function get_style_depends(): array {
		return array( 'nova-elements-advanced-button' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Button', 'nova-elements' ) ) );
		$this->add_control( 'label', array( 'label' => __( 'Label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Explore the collection', 'nova-elements' ), 'dynamic' => array( 'active' => true ) ) );
		$this->add_control( 'detail', array( 'label' => __( 'Supporting text', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$this->add_control( 'link', array( 'label' => __( 'Link', 'nova-elements' ), 'type' => Controls_Manager::URL, 'options' => array( 'url', 'is_external', 'nofollow' ), 'dynamic' => array( 'active' => true ), 'description' => __( 'Set a destination to make this button clickable.', 'nova-elements' ) ) );
		$this->add_control( 'icon_type', array( 'label' => __( 'Icon', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'default' => 'arrow', 'options' => array( 'arrow' => __( 'Arrow', 'nova-elements' ), 'custom' => __( 'Choose an icon', 'nova-elements' ), 'none' => __( 'None', 'nova-elements' ) ) ) );
		$this->add_control( 'button_icon', array( 'label' => __( 'Custom icon', 'nova-elements' ), 'type' => Controls_Manager::ICONS, 'condition' => array( 'icon_type' => 'custom' ) ) );
		$this->add_control( 'icon_position', array( 'label' => __( 'Icon position', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'default' => 'preset', 'options' => array( 'preset' => __( 'Preset default', 'nova-elements' ), 'start' => __( 'Start', 'nova-elements' ), 'end' => __( 'End', 'nova-elements' ) ), 'condition' => array( 'icon_type!' => 'none' ) ) );
		$this->add_responsive_control(
			'alignment',
			array(
				'label' => __( 'Alignment', 'nova-elements' ),
				'type' => Controls_Manager::SELECT,
				'options' => array( '' => __( 'Preset default', 'nova-elements' ), 'start' => __( 'Start', 'nova-elements' ), 'center' => __( 'Center', 'nova-elements' ), 'end' => __( 'End', 'nova-elements' ), 'stretch' => __( 'Full width', 'nova-elements' ) ),
				'selectors' => array( '{{WRAPPER}} .nova-button' => 'justify-items: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'text_style', array( 'label' => __( 'Typography', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'button_label', __( 'Label', 'nova-elements' ), '.nova-button__label' );
		$this->add_text_style( 'button_detail', __( 'Supporting text', 'nova-elements' ), '.nova-button__detail' );
		$this->end_controls_section();

		$this->start_controls_section( 'surface_style', array( 'label' => __( 'Button surface', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$selector = '{{WRAPPER}} .nova-button__surface';
		$this->start_controls_tabs( 'surface_states' );
		$this->start_controls_tab( 'normal', array( 'label' => __( 'Normal', 'nova-elements' ) ) );
		$this->add_background_style( 'button_background', $selector );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'button_border', 'selector' => $selector ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'button_shadow', 'selector' => $selector ) );
		$this->end_controls_tab();
		$this->start_controls_tab( 'hover', array( 'label' => __( 'Hover and focus', 'nova-elements' ) ) );
		$state = '{{WRAPPER}} a.nova-button__surface:is(:hover, :focus-visible)';
		$this->add_background_style( 'hover_background', $state );
		$this->add_control(
			'hover_text',
			array(
				'label' => __( 'Text and icon color', 'nova-elements' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array( "$state :is(.nova-button__label, .nova-button__detail, .nova-button__icon)" => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control( 'hover_border', array( 'label' => __( 'Border color', 'nova-elements' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( $state => 'border-color: {{VALUE}};' ) ) );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_dimensions( 'button_padding', __( 'Padding', 'nova-elements' ), $selector, 'padding' );
		$this->add_dimensions( 'button_radius', __( 'Border radius', 'nova-elements' ), $selector, 'border-radius' );
		$this->add_size( 'icon_gap', __( 'Icon gap', 'nova-elements' ), '.nova-button__surface', 'gap' );
		$this->add_size( 'icon_size', __( 'Icon size', 'nova-elements' ), '.nova-button__icon', 'font-size' );
		$this->add_control( 'icon_color', array( 'label' => __( 'Icon color', 'nova-elements' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .nova-button__icon' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$label = $this->text_value( $settings['label'] ?? null );
		if ( '' === $label ) {
			return;
		}

		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$detail = $this->text_value( $settings['detail'] ?? null );
		$linked = $this->add_safe_link( 'button_link', $settings['link'] ?? null );
		$tag = $linked ? 'a' : 'span';
		$type = $settings['icon_type'] ?? 'arrow';
		$position = $settings['icon_position'] ?? 'preset';
		$position = in_array( $position, array( 'start', 'end' ), true ) ? $position : 'preset';
		$icon = match ( $type ) {
			'arrow' => '<span class="nova-button__arrow">&rarr;</span>',
			'custom' => $this->icon_markup( $settings['button_icon'] ?? null ),
			default => '',
		};
		require dirname( __DIR__, 3 ) . '/templates/widgets/advanced-button.php';
	}
}
