<?php

namespace NovaElements\Widgets\Interactive;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

final class AdvancedButtonGroupWidget extends InteractiveWidget {
	public function get_name(): string {
		return 'ne-advanced-button-group';
	}

	public function get_title(): string {
		return __( 'Advanced Button Group', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-button';
	}

	public function get_script_depends(): array {
		return array();
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => $this->get_title() ) );
		$repeater = new Repeater();
		$repeater->add_control( 'button_label', array( 'label' => __( 'Link text', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'button_link', array( 'label' => __( 'Link', 'nova-elements' ), 'type' => Controls_Manager::URL, 'options' => array( 'url', 'is_external', 'nofollow' ), 'dynamic' => array( 'active' => true ) ) );
		$this->add_control( 'group_items', array(
			'label' => __( 'Actions', 'nova-elements' ),
			'description' => __( 'Displays up to six actions with a valid link.', 'nova-elements' ),
			'type' => Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
			'title_field' => '{{ button_label }}',
			'default' => array(
				array( 'button_label' => __( 'View the project', 'nova-elements' ) ),
				array( 'button_label' => __( 'Read the brief', 'nova-elements' ) ),
			),
		) );
		$this->add_control( 'group_label', array( 'label' => __( 'Accessible group label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Project actions', 'nova-elements' ) ) );
		$this->end_controls_section();
		$this->register_visual_presets();
		$this->start_controls_section( 'group_style', array( 'label' => __( 'Actions', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'button', __( 'Links', 'nova-elements' ), '.nova-button-group a' );
		$this->add_panel_style( '.nova-button-group' );
		$this->add_size( 'group_gap', __( 'Gap between links', 'nova-elements' ), '.nova-button-group', '--ne-group-gap' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$rows = $settings['group_items'] ?? null;
		$links = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			if ( ! is_array( $row ) ) { continue; }
			$text = $this->text_value( $row['button_label'] ?? null );
			$key = 'group_link_' . count( $links );
			if ( '' !== $text && $this->add_safe_link( $key, $row['button_link'] ?? null ) ) {
				$links[ $key ] = $text;
			}
			if ( 6 === count( $links ) ) { break; }
		}
		if ( ! $links ) {
			if ( is_user_logged_in() && current_user_can( 'edit_posts' ) && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Add links to display the button group.', 'nova-elements' ) . '</p>';
			}
			return;
		}
		$label = $this->text_value( $settings['group_label'] ?? null );
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		?>
		<ul class="nova-widget nova-note nova-button-group nova-note--<?php echo esc_attr( $preset ); ?>" role="list" aria-label="<?php echo esc_attr( '' !== $label ? $label : __( 'Actions', 'nova-elements' ) ); ?>">
			<?php foreach ( $links as $key => $text ) : ?>
				<li><a <?php $this->print_render_attribute_string( $key ); ?>><?php echo esc_html( $text ); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}
