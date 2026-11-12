<?php

namespace NovaElements\Widgets\Interactive;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

abstract class PanelWidget extends InteractiveWidget {
	protected const TABS = false;
	protected const MAX_PANELS = 8;

	protected function default_panels(): array {
		return array(
			array( 'panel_label' => __( 'Overview', 'nova-elements' ), 'panel_content' => __( 'Get the project brief, meet the team, and agree on the first milestone.', 'nova-elements' ) ),
			array( 'panel_label' => __( 'Getting started', 'nova-elements' ), 'panel_content' => __( 'Invite collaborators and assign an owner to the first task.', 'nova-elements' ) ),
			array( 'panel_label' => __( 'Support', 'nova-elements' ), 'panel_content' => __( 'Find answers and get help when the team needs it.', 'nova-elements' ) ),
		);
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => $this->get_title() ) );
		$repeater = new Repeater();
		$repeater->add_control( 'panel_label', array( 'label' => __( 'Label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'panel_content', array( 'label' => __( 'Content', 'nova-elements' ), 'type' => Controls_Manager::WYSIWYG, 'dynamic' => array( 'active' => true ) ) );
		$this->add_control(
			'panel_items',
			array(
				'label' => __( 'Panels', 'nova-elements' ),
				/* translators: %d: maximum number of displayed panels. */
				'description' => sprintf( __( 'Displays up to %d panels in the order shown.', 'nova-elements' ), static::MAX_PANELS ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{ panel_label }}',
				'default' => $this->default_panels(),
			)
		);
		$this->add_control( 'initial_panel', array(
			'label' => __( 'Initial panel', 'nova-elements' ),
			'type' => Controls_Manager::NUMBER,
			'min' => static::TABS ? 1 : 0,
			'max' => static::MAX_PANELS,
			'step' => 1,
			'default' => 1,
			'description' => static::TABS ? __( 'Enter the panel number, starting at 1.', 'nova-elements' ) : __( 'Enter the panel number, or 0 to start with all panels closed.', 'nova-elements' ),
		) );
		if ( static::TABS ) {
			$this->add_control( 'tabs_label', array( 'label' => __( 'Tab list accessible label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Information', 'nova-elements' ) ) );
		} else {
			$this->add_control( 'allow_multiple', array( 'label' => __( 'Allow multiple open panels', 'nova-elements' ), 'type' => Controls_Manager::SWITCHER ) );
		}
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'panel_style', array( 'label' => __( 'Panels', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'label', __( 'Label', 'nova-elements' ), '.nova-panel__label' );
		$this->add_text_style( 'body', __( 'Content', 'nova-elements' ), '.nova-panel__content' );
		$this->add_panel_style( '.nova-panel__surface' );
		$this->add_size( 'panel_gap', __( 'Panel gap', 'nova-elements' ), '.nova-panels', '--ne-panel-gap' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$rows = $settings['panel_items'] ?? null;
		$panels = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$label = $this->text_value( $row['panel_label'] ?? null );
			if ( '' === $label ) {
				continue;
			}
			$panels[] = array( 'label' => $label, 'content' => $this->text_value( $row['panel_content'] ?? null ) );
			if ( static::MAX_PANELS === count( $panels ) ) {
				break;
			}
		}
		if ( ! $panels ) {
			return;
		}

		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$initial = $settings['initial_panel'] ?? 1;
		$initial = is_numeric( $initial ) ? max( static::TABS ? 1 : 0, min( count( $panels ), (int) $initial ) ) : 1;
		$tabs = static::TABS;
		$tabs_label = $this->text_value( $settings['tabs_label'] ?? null );
		$multiple = 'yes' === ( $settings['allow_multiple'] ?? '' );
		require dirname( __DIR__, 3 ) . '/templates/widgets/interactive-panels.php';
	}
}
