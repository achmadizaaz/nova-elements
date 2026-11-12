<?php

namespace NovaElements\Widgets\Interactive;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

class ModalPopupWidget extends InteractiveWidget {
	protected const DRAWER = false;
	public function get_name(): string {
		return 'ne-modal-popup';
	}

	public function get_title(): string {
		return __( 'Modal Popup', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-popup';
	}

	public function get_keywords(): array {
		return array( 'modal', 'dialog', 'popup', 'overlay' );
	}

	protected function dialog_defaults(): array {
		return array(
			'trigger' => __( 'View project details', 'nova-elements' ),
			'title' => __( 'A closer look at the project', 'nova-elements' ),
			'description' => __( 'Learn how the team planned the work, what changed along the way, and where to find the finished project.', 'nova-elements' ),
			'action' => __( 'Visit the project', 'nova-elements' ),
		);
	}

	protected function register_controls(): void {
		$defaults = $this->dialog_defaults();
		$this->start_controls_section( 'content', array( 'label' => $this->get_title() ) );
		foreach ( array(
			'trigger_label' => array( __( 'Open button label', 'nova-elements' ), $defaults['trigger'] ),
			'title' => array( __( 'Dialog title', 'nova-elements' ), $defaults['title'] ),
			'cta_label' => array( __( 'Action link text', 'nova-elements' ), $defaults['action'] ),
		) as $key => list( $label, $default ) ) {
			$this->add_control( $key, array( 'label' => $label, 'type' => Controls_Manager::TEXT, 'default' => $default, 'dynamic' => array( 'active' => true ) ) );
		}
		$this->add_heading_tag_control();
		$this->add_control( 'description', array(
			'label' => __( 'Description', 'nova-elements' ),
			'type' => Controls_Manager::TEXTAREA,
			'default' => $defaults['description'],
			'dynamic' => array( 'active' => true ),
		) );
		$this->add_control( 'link', array(
			'label' => __( 'Action link', 'nova-elements' ),
			'type' => Controls_Manager::URL,
			'options' => array( 'url', 'is_external', 'nofollow' ),
			'condition' => array( 'cta_label!' => '' ),
			'dynamic' => array( 'active' => true ),
		) );
		if ( static::DRAWER ) {
			$this->add_control( 'drawer_side', array(
				'label' => __( 'Open from', 'nova-elements' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'end',
				'options' => array( 'start' => __( 'Leading edge', 'nova-elements' ), 'end' => __( 'Trailing edge', 'nova-elements' ) ),
			) );
		}
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'dialog_style', array( 'label' => __( 'Dialog', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_panel_style( '.nova-modal__shell' );
		$this->add_text_style( 'title', __( 'Title', 'nova-elements' ), '.nova-modal__title' );
		$this->add_text_style( 'body', __( 'Description', 'nova-elements' ), '.nova-modal__description' );
		$this->add_text_style( 'trigger', __( 'Open button', 'nova-elements' ), '.nova-modal__open' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$title = $this->text_value( $settings['title'] ?? null );
		$description = $this->text_value( $settings['description'] ?? null );
		$trigger = $this->text_value( $settings['trigger_label'] ?? null );
		$cta = $this->text_value( $settings['cta_label'] ?? null );
		$linked = '' !== $cta && $this->add_safe_link( 'modal_link', $settings['link'] ?? null );
		$tag = $this->heading_tag( $settings['heading_tag'] ?? null );
		$drawer = static::DRAWER ? ( 'start' === ( $settings['drawer_side'] ?? '' ) ? 'start' : 'end' ) : '';
		if ( '' === $title && '' === $description && ! $linked ) {
			return;
		}
		require dirname( __DIR__, 3 ) . '/templates/widgets/modal-popup.php';
	}
}
