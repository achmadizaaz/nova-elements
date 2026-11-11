<?php

namespace NovaElements\Widgets\Heading;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use NovaElements\Core\StylePresetRegistry;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

final class AdvancedHeadingWidget extends BaseWidget {
	private const TAGS = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div' );

	public function get_name(): string {
		return 'ne-advanced-heading';
	}

	public function get_title(): string {
		return esc_html__( 'Advanced Heading', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-heading';
	}

	public function get_keywords(): array {
		return array( 'heading', 'title', 'subtitle', 'typography', 'gradient', 'nova' );
	}

	public function get_style_depends(): array {
		return array( 'nova-elements-heading' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Heading', 'nova-elements' ) ) );
		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'nova-elements' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Design for what comes next.', 'nova-elements' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'subtitle',
			array(
				'label'   => esc_html__( 'Subtitle', 'nova-elements' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Build a digital experience with clarity, character, and purpose.', 'nova-elements' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'heading_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'nova-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array_combine( self::TAGS, self::TAGS ),
			)
		);
		$this->add_responsive_control(
			'alignment',
			array(
				'label'     => esc_html__( 'Text Alignment', 'nova-elements' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''       => esc_html__( 'Preset default', 'nova-elements' ),
					'start'  => esc_html__( 'Start', 'nova-elements' ),
					'center' => esc_html__( 'Center', 'nova-elements' ),
					'end'    => esc_html__( 'End', 'nova-elements' ),
				),
				'selectors' => array( '{{WRAPPER}} .nova-heading' => 'text-align: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'title_style', array( 'label' => esc_html__( 'Title', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .nova-heading__title' ) );
		$this->add_control(
			'color_mode',
			array(
				'label'   => esc_html__( 'Text Fill', 'nova-elements' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => array( 'solid' => esc_html__( 'Solid', 'nova-elements' ), 'gradient' => esc_html__( 'Gradient', 'nova-elements' ) ),
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Text Color', 'nova-elements' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array( 'color_mode' => 'solid' ),
				'selectors' => array( '{{WRAPPER}} .nova-heading__title' => 'color: {{VALUE}};' ),
			)
		);
		foreach ( array( 'start' => esc_html__( 'Gradient Start', 'nova-elements' ), 'end' => esc_html__( 'Gradient End', 'nova-elements' ) ) as $stop => $label ) {
			$this->add_control(
				'gradient_' . $stop,
				array(
					'label'     => $label,
					'type'      => Controls_Manager::COLOR,
					'condition' => array( 'color_mode' => 'gradient' ),
					'selectors' => array( '{{WRAPPER}} .nova-heading__title' => '--ne-gradient-' . $stop . ': {{VALUE}};' ),
				)
			);
		}
		$this->end_controls_section();

		$this->start_controls_section( 'subtitle_style', array( 'label' => esc_html__( 'Subtitle', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_group_control( Group_Control_Typography::get_type(), array( 'name' => 'subtitle_typography', 'selector' => '{{WRAPPER}} .nova-heading__subtitle' ) );
		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'Text Color', 'nova-elements' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .nova-heading__subtitle' => 'color: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'layout_style', array( 'label' => esc_html__( 'Layout', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control(
			'gap',
			array(
				'label'      => esc_html__( 'Content Gap', 'nova-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ), 'rem' => array( 'min' => 0, 'max' => 6, 'step' => 0.1 ) ),
				'selectors'  => array( '{{WRAPPER}} .nova-heading' => '--ne-heading-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'      => esc_html__( 'Panel Padding', 'nova-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem', '%' ),
				'selectors'  => array( '{{WRAPPER}} .nova-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$title    = is_string( $settings['title'] ?? null ) ? trim( $settings['title'] ) : '';
		$subtitle = is_string( $settings['subtitle'] ?? null ) ? trim( $settings['subtitle'] ) : '';
		$tag      = in_array( $settings['heading_tag'] ?? null, self::TAGS, true ) ? $settings['heading_tag'] : 'h2';
		$preset   = $this->resolve_preset( $settings['style_preset'] ?? null );
		$gradient = 'gradient' === ( $settings['color_mode'] ?? null );

		if ( '' !== $title || '' !== $subtitle ) {
			require dirname( __DIR__, 3 ) . '/templates/widgets/heading.php';
		}
	}

	protected function content_template(): void {
		$tags    = self::TAGS;
		$presets = array_keys( StylePresetRegistry::for_widget( $this->get_name() ) );
		$default = $this->resolve_preset( 'site' );
		require dirname( __DIR__, 3 ) . '/templates/widgets/heading-preview.php';
	}
}
