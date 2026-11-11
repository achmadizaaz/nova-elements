<?php

namespace NovaElements\Widgets\Base;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use NovaElements\Controls\VisualStylePicker;
use NovaElements\Core\StylePresetRegistry;

defined( 'ABSPATH' ) || exit;

abstract class BaseWidget extends Widget_Base {
	public function get_categories(): array {
		return array( 'nova-elements' );
	}

	protected function resolve_preset( mixed $value ): string {
		return StylePresetRegistry::resolve( $this->get_name(), $value );
	}

	protected function register_visual_presets(): void {
		$this->start_controls_section(
			'nova_visual_presets',
			array( 'label' => __( 'Visual Style Picker', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE )
		);
		$this->add_control(
			'style_preset',
			array(
				'label' => __( 'Widget layout', 'nova-elements' ),
				'type' => VisualStylePicker::TYPE,
				'default' => 'site',
				'options' => StylePresetRegistry::choices( $this->get_name() ),
				'render_type' => 'template',
			)
		);
		$this->end_controls_section();
	}

	protected function add_text_style( string $name, string $label, string $selector ): void {
		$selector = '{{WRAPPER}} ' . $selector;
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => $name . '_typography', 'label' => $label, 'selector' => $selector )
		);
		$this->add_control(
			$name . '_color',
			array(
				/* translators: %s: the element being styled. */
				'label' => sprintf( __( '%s color', 'nova-elements' ), $label ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array( $selector => 'color: {{VALUE}};' ),
			)
		);
	}

	protected function add_panel_style( string $selector ): void {
		$selector = '{{WRAPPER}} ' . $selector;
		$this->add_background_style( 'panel_background', $selector );
		$this->add_group_control( Group_Control_Border::get_type(), array( 'name' => 'panel_border', 'selector' => $selector ) );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array( 'name' => 'panel_shadow', 'selector' => $selector ) );
		$this->add_dimensions( 'panel_radius', __( 'Border radius', 'nova-elements' ), $selector, 'border-radius' );
		$this->add_dimensions( 'panel_padding', __( 'Padding', 'nova-elements' ), $selector, 'padding' );
	}

	protected function add_background_style( string $name, string $selector ): void {
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => $name,
				'types' => array( 'classic', 'gradient' ),
				'selector' => $selector,
				// A classic fill replaces a preset gradient. Elementor's gradient and image fields emit later.
				'fields_options' => array(
					'color' => array( 'selectors' => array( '{{SELECTOR}}' => 'background-color: {{VALUE}}; background-image: none;' ) ),
				),
			)
		);
	}

	protected function add_dimensions( string $name, string $label, string $selector, string $property ): void {
		$this->add_responsive_control(
			$name,
			array(
				'label' => $label,
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem', '%' ),
				'selectors' => array( $selector => $property . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
	}

	protected function add_size( string $name, string $label, string $selector, string $property, int $max = 100, array $condition = array() ): void {
		$this->add_responsive_control(
			$name,
			array(
				'label' => $label,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array( 'px' => array( 'min' => 0, 'max' => $max ) ),
				'condition' => $condition,
				'selectors' => array( '{{WRAPPER}} ' . $selector => $property . ': {{SIZE}}{{UNIT}};' ),
			)
		);
	}

	protected function add_heading_tag_control(): void {
		$tags = array( 'h2', 'h3', 'h4', 'h5', 'h6' );
		$this->add_control(
			'heading_tag',
			array(
				'label' => __( 'Title HTML tag', 'nova-elements' ),
				'type' => Controls_Manager::SELECT,
				'options' => array_combine( $tags, $tags ),
				'default' => 'h3',
			)
		);
	}

	protected function heading_tag( mixed $tag ): string {
		return in_array( $tag, array( 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ? $tag : 'h3';
	}

	protected function text_value( mixed $value ): string {
		return is_string( $value ) ? trim( $value ) : '';
	}

	protected function add_safe_link( string $name, mixed $setting ): bool {
		if ( ! is_array( $setting ) ) {
			return false;
		}
		$url = esc_url_raw( $this->text_value( $setting['url'] ?? null ) );
		if ( '' === $url ) {
			return false;
		}

		$attributes = array( 'href' => $url );
		$rel = array();
		if ( in_array( $setting['is_external'] ?? null, array( 'on', 'yes', true, '1' ), true ) ) {
			$attributes['target'] = '_blank';
			$rel[] = 'noopener';
			$rel[] = 'noreferrer';
		}
		if ( in_array( $setting['nofollow'] ?? null, array( 'on', 'yes', true, '1' ), true ) ) {
			$rel[] = 'nofollow';
		}
		if ( $rel ) {
			$attributes['rel'] = implode( ' ', $rel );
		}
		$this->add_render_attribute( $name, $attributes, null, true );
		return true;
	}

	protected function media_image( mixed $media, string $size, bool $decorative = false ): string {
		if ( ! is_array( $media ) ) {
			return '';
		}
		$id = $media['id'] ?? 0;
		if ( is_numeric( $id ) && (int) $id > 0 ) {
			$attributes = $decorative ? array( 'alt' => '' ) : array();
			$image = wp_get_attachment_image( (int) $id, $size, false, $attributes );
			if ( '' !== $image ) {
				return $image;
			}
		}
		$url = esc_url( $this->text_value( $media['url'] ?? null ) );
		return '' === $url ? '' : '<img src="' . $url . '" alt="" loading="lazy" decoding="async">';
	}

	protected function icon_markup( mixed $icon ): string {
		if ( ! is_array( $icon ) || ! is_string( $icon['library'] ?? null ) || empty( $icon['value'] ) ) {
			return '';
		}
		$value = $icon['value'];
		if ( 'svg' === $icon['library'] ) {
			if ( ! is_array( $value ) || ! is_numeric( $value['id'] ?? null ) || (int) $value['id'] < 1 ) {
				return '';
			}
			$icon['value'] = array( 'id' => (int) $value['id'] );
		} elseif ( ! is_string( $value ) || ! preg_match( '/^[A-Za-z0-9_ -]+$/D', $value ) ) {
			return '';
		}
		ob_start();
		try {
			Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
			return (string) ob_get_contents();
		} finally {
			ob_end_clean();
		}
	}
}
