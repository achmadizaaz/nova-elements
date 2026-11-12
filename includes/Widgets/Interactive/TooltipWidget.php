<?php

namespace NovaElements\Widgets\Interactive;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

final class TooltipWidget extends InteractiveWidget {
	public function get_name(): string {
		return 'ne-tooltip';
	}

	public function get_title(): string {
		return __( 'Tooltip', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-info-circle';
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => $this->get_title() ) );
		$this->add_control( 'trigger_label', array( 'label' => __( 'Trigger label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'What is a milestone?', 'nova-elements' ), 'dynamic' => array( 'active' => true ) ) );
		$this->add_control( 'tooltip_text', array( 'label' => __( 'Tooltip text', 'nova-elements' ), 'type' => Controls_Manager::TEXTAREA, 'default' => __( 'A milestone is a checkpoint that marks an important step in a project.', 'nova-elements' ), 'dynamic' => array( 'active' => true ) ) );
		$this->end_controls_section();
		$this->register_visual_presets();
		$this->start_controls_section( 'tooltip_style', array( 'label' => __( 'Tooltip', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'trigger', __( 'Trigger', 'nova-elements' ), '.nova-tooltip__trigger' );
		$this->add_text_style( 'body', __( 'Tooltip text', 'nova-elements' ), '.nova-tooltip__bubble' );
		$this->add_panel_style( '.nova-tooltip__bubble' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$text = $this->text_value( $settings['tooltip_text'] ?? null );
		if ( '' === $text ) {
			return;
		}
		$label = $this->text_value( $settings['trigger_label'] ?? null );
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		?>
		<nova-tooltip class="nova-widget nova-note nova-note--<?php echo esc_attr( $preset ); ?>">
			<button type="button" class="nova-tooltip__trigger" hidden><?php echo esc_html( '' !== $label ? $label : __( 'More information', 'nova-elements' ) ); ?></button>
			<p class="nova-tooltip__bubble"><?php echo esc_html( $text ); ?></p>
		</nova-tooltip>
		<?php
	}
}
