<?php

namespace NovaElements\Widgets\Team;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

final class TeamWidget extends BaseWidget {
	public function get_name(): string {
		return 'ne-team';
	}

	public function get_title(): string {
		return __( 'Team', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-person';
	}

	public function get_style_depends(): array {
		return array( 'nova-elements-team' );
	}

	public function get_script_depends(): array {
		return array( 'nova-elements-team' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Members', 'nova-elements' ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'image', array( 'label' => __( 'Image', 'nova-elements' ), 'type' => Controls_Manager::MEDIA, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'title', array( 'label' => __( 'Title', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'subtitle', array( 'label' => __( 'Subtitle', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'description', array( 'label' => __( 'Description', 'nova-elements' ), 'type' => Controls_Manager::TEXTAREA, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'role', array( 'label' => __( 'Role', 'nova-elements' ), 'description' => __( 'Shown as the image badge and used for filtering.', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'footer_start', array( 'label' => __( 'Footer left', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'footer_end', array( 'label' => __( 'Footer right', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$this->add_control(
			'items',
			array(
				'label' => __( 'Members', 'nova-elements' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'title' => __( 'Avery Morgan', 'nova-elements' ), 'subtitle' => __( 'Chair', 'nova-elements' ), 'description' => __( 'Leads the board and sets the direction for every program.', 'nova-elements' ), 'role' => __( 'Board', 'nova-elements' ), 'footer_start' => __( 'Since 2021', 'nova-elements' ), 'footer_end' => __( 'Governance', 'nova-elements' ) ),
					array( 'title' => __( 'Sofia Patel', 'nova-elements' ), 'subtitle' => __( 'Secretary', 'nova-elements' ), 'description' => __( 'Keeps records, minutes, and member communication in order.', 'nova-elements' ), 'role' => __( 'Board', 'nova-elements' ), 'footer_start' => __( 'Since 2022', 'nova-elements' ), 'footer_end' => __( 'Administration', 'nova-elements' ) ),
					array( 'title' => __( 'Jordan Lee', 'nova-elements' ), 'subtitle' => __( 'Program coordinator', 'nova-elements' ), 'description' => __( 'Plans events and connects volunteers with each initiative.', 'nova-elements' ), 'role' => __( 'Staff', 'nova-elements' ), 'footer_start' => __( 'Since 2023', 'nova-elements' ), 'footer_end' => __( 'Programs', 'nova-elements' ) ),
				),
				'title_field' => '{{ title }}',
			)
		);
		$this->add_heading_tag_control();
		$this->add_control( 'show_filter', array( 'label' => __( 'Role filter', 'nova-elements' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_control( 'all_label', array( 'label' => __( 'All roles label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'All', 'nova-elements' ), 'condition' => array( 'show_filter' => 'yes' ) ) );
		$this->add_control( 'filter_label', array( 'label' => __( 'Filter accessible label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Filter members by role', 'nova-elements' ), 'condition' => array( 'show_filter' => 'yes' ) ) );
		$this->add_control( 'show_badge', array( 'label' => __( 'Role badge on image', 'nova-elements' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Columns', 'nova-elements' ),
				'type' => Controls_Manager::SELECT,
				'options' => array( '' => __( 'Preset default', 'nova-elements' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
				'selectors' => array( '{{WRAPPER}} .nova-team__grid' => '--ne-team-count: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'text_style', array( 'label' => __( 'Text', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'title', __( 'Title', 'nova-elements' ), '.nova-team__title' );
		$this->add_text_style( 'subtitle', __( 'Subtitle', 'nova-elements' ), '.nova-team__subtitle' );
		$this->add_text_style( 'description', __( 'Description', 'nova-elements' ), '.nova-team__description' );
		$this->add_text_style( 'footer', __( 'Footer', 'nova-elements' ), '.nova-team__footer' );
		$this->end_controls_section();

		$this->start_controls_section( 'badge_style', array( 'label' => __( 'Role badge and filter', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'badge', __( 'Badge', 'nova-elements' ), '.nova-team__badge' );
		$this->add_control( 'badge_background', array( 'label' => __( 'Badge background', 'nova-elements' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .nova-team__badge' => 'background: {{VALUE}};' ) ) );
		$this->add_dimensions( 'badge_radius', __( 'Badge radius', 'nova-elements' ), '{{WRAPPER}} .nova-team__badge', 'border-radius' );
		$this->add_text_style( 'filter', __( 'Filter button', 'nova-elements' ), '.nova-team__filter' );
		$this->add_control( 'filter_active', array( 'label' => __( 'Active filter background', 'nova-elements' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .nova-team__filter[aria-pressed="true"]' => 'background: {{VALUE}}; border-color: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'card_style', array( 'label' => __( 'Cards and spacing', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_panel_style( '.nova-team__card' );
		$this->add_size( 'card_gap', __( 'Gap between cards', 'nova-elements' ), '.nova-team', '--ne-team-gap' );
		$this->add_dimensions( 'image_radius', __( 'Image radius', 'nova-elements' ), '{{WRAPPER}} .nova-team__media', 'border-radius' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$rows = $settings['items'] ?? array();
		$items = array();
		$roles = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$title = $this->text_value( $row['title'] ?? null );
			if ( '' === $title ) {
				continue;
			}
			$role = $this->text_value( $row['role'] ?? null );
			// Keys are derived from the label, so renaming a role keeps filter buttons and cards in sync.
			$key = '' === $role ? '' : sanitize_title( $role );
			if ( '' !== $key ) {
				$roles[ $key ] ??= $role;
			}
			$items[] = array(
				'title' => $title,
				'subtitle' => $this->text_value( $row['subtitle'] ?? null ),
				'description' => $this->text_value( $row['description'] ?? null ),
				'role' => $role,
				'role_key' => $key,
				'image' => $this->media_image( $row['image'] ?? null, 'medium_large' ),
				'footer' => array( $this->text_value( $row['footer_start'] ?? null ), $this->text_value( $row['footer_end'] ?? null ) ),
			);
		}
		$tag = $this->heading_tag( $settings['heading_tag'] ?? null );
		$show_badge = 'yes' === ( $settings['show_badge'] ?? 'yes' );
		$show_filter = 'yes' === ( $settings['show_filter'] ?? 'yes' ) && count( $roles ) > 1;
		$all_label = $this->text_value( $settings['all_label'] ?? null ) ?: __( 'All', 'nova-elements' );
		$filter_label = $this->text_value( $settings['filter_label'] ?? null ) ?: __( 'Filter members by role', 'nova-elements' );
		require dirname( __DIR__, 3 ) . '/templates/widgets/team.php';
	}
}
