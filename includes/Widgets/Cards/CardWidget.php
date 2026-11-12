<?php

namespace NovaElements\Widgets\Cards;

use Elementor\Controls_Manager;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

abstract class CardWidget extends BaseWidget {
	protected const KIND = 'feature';

	public function get_style_depends(): array {
		return array( 'nova-elements-' . ( 'feature' === static::KIND ? 'feature-card' : 'content-cards' ) );
	}

	abstract protected function content_defaults(): array;

	protected function register_card_source(): void {}

	protected function register_card_details(): void {}

	protected function manual_content_condition(): array {
		return array();
	}

	protected function resolve_card_settings( array $settings ): array {
		return $settings;
	}

	protected function card_items( array $settings ): array {
		return array();
	}

	protected function register_controls(): void {
		$defaults = $this->content_defaults();
		$this->start_controls_section( 'content', array( 'label' => $defaults['section'] ) );
		$this->register_card_source();
		$manual = $this->manual_content_condition();
		$this->add_control( 'eyebrow', array( 'label' => $defaults['eyebrow_label'], 'type' => Controls_Manager::TEXT, 'default' => $defaults['eyebrow'], 'dynamic' => array( 'active' => true ) ) );
		$this->add_control( 'title', array( 'label' => __( 'Title', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => $defaults['title'], 'dynamic' => array( 'active' => true ), 'condition' => $manual ) );
		$this->add_heading_tag_control();
		$this->add_control( 'description', array( 'label' => __( 'Description', 'nova-elements' ), 'type' => Controls_Manager::TEXTAREA, 'default' => $defaults['description'], 'dynamic' => array( 'active' => true ), 'condition' => $manual ) );
		$this->add_control( 'media_type', array( 'label' => __( 'Media', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'default' => $defaults['media'], 'options' => array( 'none' => __( 'None', 'nova-elements' ), 'icon' => __( 'Icon', 'nova-elements' ), 'image' => __( 'Image', 'nova-elements' ) ) ) );
		$this->add_control( 'feature_icon', array( 'label' => __( 'Icon', 'nova-elements' ), 'type' => Controls_Manager::ICONS, 'default' => array( 'value' => $defaults['icon'], 'library' => 'fa-solid' ), 'condition' => array( 'media_type' => 'icon' ) ) );
		$this->add_control( 'feature_image', array( 'label' => __( 'Image', 'nova-elements' ), 'type' => Controls_Manager::MEDIA, 'dynamic' => array( 'active' => true ), 'condition' => array( 'media_type' => 'image' ) + $manual ) );
		$this->add_control( 'cta_label', array( 'label' => __( 'Link text', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => $defaults['cta_label'] ) );
		$this->add_control(
			'link',
			array(
				'label' => __( 'Link', 'nova-elements' ),
				'type' => Controls_Manager::URL,
				'options' => array( 'url', 'is_external', 'nofollow' ),
				'dynamic' => array( 'active' => true ),
				'condition' => array( 'cta_label!' => '' ) + $manual,
			)
		);
		if ( 'feature' !== static::KIND ) {
			$this->add_control( 'media_position', array(
				'label' => __( 'Media position', 'nova-elements' ),
				'type' => Controls_Manager::SELECT,
				'options' => array( '' => __( 'Preset default', 'nova-elements' ), 'top' => __( 'Above', 'nova-elements' ), 'start' => __( 'Leading side', 'nova-elements' ), 'end' => __( 'Trailing side', 'nova-elements' ) ),
				'default' => '',
				'condition' => array( 'media_type!' => 'none' ),
			) );
			$this->register_card_details();
		}
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'text_style', array( 'label' => __( 'Text', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'label', __( 'Label', 'nova-elements' ), '.nova-feature__eyebrow' );
		$this->add_text_style( 'title', __( 'Title', 'nova-elements' ), '.nova-feature__title' );
		$this->add_text_style( 'body', __( 'Description', 'nova-elements' ), '.nova-feature__description' );
		if ( 'feature' !== static::KIND ) {
			$this->add_text_style( 'detail', __( 'Supporting details', 'nova-elements' ), '.nova-feature__detail' );
		}
		if ( 'pricing' === static::KIND ) {
			$this->add_text_style( 'price', __( 'Price', 'nova-elements' ), '.nova-feature__price-current' );
		}
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
		$feature = 'feature' === static::KIND;
		$this->add_size( 'icon_size', __( 'Icon size', 'nova-elements' ), $feature ? '.nova-feature__icon' : '.nova-feature', $feature ? 'font-size' : '--ne-feature-icon-size', 100, array( 'media_type' => 'icon' ) );
		$this->add_control( 'icon_color', array( 'label' => __( 'Icon color', 'nova-elements' ), 'type' => Controls_Manager::COLOR, 'condition' => array( 'media_type' => 'icon' ), 'selectors' => array( '{{WRAPPER}} .nova-feature__icon' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'image_ratio', array( 'label' => __( 'Image aspect ratio', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'options' => array( '' => __( 'Preset default', 'nova-elements' ), '1' => '1:1', '1.3333' => '4:3', '1.7778' => '16:9' ), 'condition' => array( 'media_type' => 'image' ), 'selectors' => $feature
			? array( '{{WRAPPER}} .nova-feature__image' => 'aspect-ratio: {{VALUE}};' )
			: array( '{{WRAPPER}} .nova-feature' => '--ne-feature-media-ratio: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->resolve_card_settings( $this->get_settings_for_display() );
		if ( ! $settings && 'feature' !== static::KIND ) {
			return;
		}
		$kind = static::KIND;
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
		$position = 'feature' === $kind ? '' : ( $settings['media_position'] ?? '' );
		$position = in_array( $position, array( 'top', 'start', 'end' ), true ) ? $position : '';
		$note = in_array( $kind, array( 'info', 'profile', 'team' ), true ) ? $this->text_value( $settings['card_note'] ?? null ) : '';
		$caption = 'image-box' === $kind ? $this->text_value( $settings['image_caption'] ?? null ) : '';
		$price = 'pricing' === $kind ? $this->text_value( $settings['price_text'] ?? null ) : '';
		$back_title = 'flip' === $kind ? $this->text_value( $settings['back_title'] ?? null ) : '';
		$back_description = 'flip' === $kind ? $this->text_value( $settings['back_description'] ?? null ) : '';
		$effect = $settings['hover_effect'] ?? 'lift';
		$effect = 'hover' === $kind && in_array( $effect, HoverCardWidget::EFFECTS, true ) ? $effect : '';
		$points = array();
		if ( in_array( $kind, array( 'service', 'pricing' ), true ) ) {
			$lines = preg_split( '/\R/u', $this->text_value( $settings['service_points'] ?? null ) ) ?: array();
			$points = array_slice( array_filter( array_map( 'trim', $lines ), static fn( string $line ): bool => '' !== $line ), 0, 6 );
		}
		$initials = '';
		if ( in_array( $kind, array( 'profile', 'team' ), true ) && 'image' === $media && '' === $image && '' !== $title ) {
			preg_match_all( '/(?:^|\s)(\p{L}|\p{N})/u', $title, $letters );
			$initials = implode( '', array_slice( $letters[1] ?? array(), 0, 2 ) ) ?: '·';
		}
		$items = $this->card_items( $settings );
		if ( 'feature' !== $kind && ! $points && ! $items && ! $linked && '' === $title . $eyebrow . $description . $image . $icon . $note . $price . $back_title . $back_description ) {
			return;
		}
		require dirname( __DIR__, 3 ) . '/templates/widgets/feature-card.php';
	}
}
