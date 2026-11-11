<?php

namespace NovaElements\Widgets\Cards;

use Elementor\Controls_Manager;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

final class FeatureCardWidget extends BaseWidget {
	public function get_name(): string {
		return 'ne-feature-card';
	}

	public function get_title(): string {
		return __( 'Feature Card', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-info-box';
	}

	public function get_style_depends(): array {
		return array( 'nova-elements-feature-card' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Feature', 'nova-elements' ) ) );
		$this->add_control( 'eyebrow', array( 'label' => __( 'Label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Design systems', 'nova-elements' ), 'dynamic' => array( 'active' => true ) ) );
		$this->add_control( 'title', array( 'label' => __( 'Title', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'A clearer way to build', 'nova-elements' ), 'dynamic' => array( 'active' => true ) ) );
		$this->add_heading_tag_control();
		$this->add_control( 'description', array( 'label' => __( 'Description', 'nova-elements' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'Give your team reusable building blocks for pages that feel consistent and are easier to maintain.', 'nova-elements' ), 'dynamic' => array( 'active' => true ) ) );
		$this->add_control( 'media_type', array( 'label' => __( 'Media', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'default' => 'icon', 'options' => array( 'none' => __( 'None', 'nova-elements' ), 'icon' => __( 'Icon', 'nova-elements' ), 'image' => __( 'Image', 'nova-elements' ) ) ) );
		$this->add_control( 'feature_icon', array( 'label' => __( 'Icon', 'nova-elements' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => 'fas fa-layer-group', 'library' => 'fa-solid' ), 'condition' => array( 'media_type' => 'icon' ) ) );
		$this->add_control( 'feature_image', array( 'label' => __( 'Image', 'nova-elements' ), 'type' => Controls_Manager::MEDIA, 'dynamic' => array( 'active' => true ), 'condition' => array( 'media_type' => 'image' ) ) );
		$this->add_control( 'cta_label', array( 'label' => __( 'Link text', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Explore the approach', 'nova-elements' ) ) );
		$this->add_control(
			'link',
			array(
				'label' => __( 'Link', 'nova-elements' ),
				'type' => Controls_Manager::URL,
				'options' => array( 'url', 'is_external', 'nofollow' ),
				'dynamic' => array( 'active' => true ),
				'condition' => array( 'cta_label!' => '' ),
			)
		);
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'text_style', array( 'label' => __( 'Text', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'label', __( 'Label', 'nova-elements' ), '.nova-feature__eyebrow' );
		$this->add_text_style( 'title', __( 'Title', 'nova-elements' ), '.nova-feature__title' );
		$this->add_text_style( 'body', __( 'Description', 'nova-elements' ), '.nova-feature__description' );
		$this->add_text_style( 'action', __( 'Link', 'nova-elements' ), '.nova-feature__link' );
		$this->add_control( 'link_hover_color', array( 'label' => __( 'Link hover and focus color', 'nova-elements' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .nova-feature__link:is(:hover, :focus-visible)' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'card_style', array( 'label' => __( 'Card', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_panel_style( '.nova-feature' );
		$this->add_size( 'content_gap', __( 'Content gap', 'nova-elements' ), '.nova-feature', '--ne-feature-gap' );
		$this->add_responsive_control(
			'alignment',
			array(
				'label' => __( 'Content alignment', 'nova-elements' ),
				'type' => Controls_Manager::SELECT,
				'options' => array( '' => __( 'Preset default', 'nova-elements' ), 'start' => __( 'Start', 'nova-elements' ), 'center' => __( 'Center', 'nova-elements' ), 'end' => __( 'End', 'nova-elements' ) ),
				'selectors' => array( '{{WRAPPER}} .nova-feature' => '--ne-feature-align: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'media_style', array( 'label' => __( 'Media', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE, 'condition' => array( 'media_type!' => 'none' ) ) );
		$this->add_size( 'icon_size', __( 'Icon size', 'nova-elements' ), '.nova-feature__icon', 'font-size', 100, array( 'media_type' => 'icon' ) );
		$this->add_control( 'icon_color', array( 'label' => __( 'Icon color', 'nova-elements' ), 'type' => Controls_Manager::COLOR, 'condition' => array( 'media_type' => 'icon' ), 'selectors' => array( '{{WRAPPER}} .nova-feature__icon' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'image_ratio', array( 'label' => __( 'Image aspect ratio', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'options' => array( '' => __( 'Preset default', 'nova-elements' ), '1' => '1:1', '1.3333' => '4:3', '1.7778' => '16:9' ), 'condition' => array( 'media_type' => 'image' ), 'selectors' => array( '{{WRAPPER}} .nova-feature__image' => 'aspect-ratio: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$tag = $this->heading_tag( $settings['heading_tag'] ?? null );
		$eyebrow = $this->text_value( $settings['eyebrow'] ?? null );
		$title = $this->text_value( $settings['title'] ?? null );
		$description = $this->text_value( $settings['description'] ?? null );
		$cta = $this->text_value( $settings['cta_label'] ?? null );
		$linked = '' !== $cta && $this->add_safe_link( 'feature_link', $settings['link'] ?? null );
		$media = $settings['media_type'] ?? 'icon';
		$image = 'image' === $media ? $this->media_image( $settings['feature_image'] ?? null, 'large' ) : '';
		$icon = 'icon' === $media ? $this->icon_markup( $settings['feature_icon'] ?? null ) : '';
		require dirname( __DIR__, 3 ) . '/templates/widgets/feature-card.php';
	}
}
