<?php

namespace NovaElements\Widgets\Testimonials;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

final class TestimonialsWidget extends BaseWidget {
	public function get_name(): string {
		return 'ne-testimonials';
	}

	public function get_title(): string {
		return __( 'Testimonials', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-testimonial';
	}

	public function get_style_depends(): array {
		return array( 'nova-elements-testimonials' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Testimonials', 'nova-elements' ) ) );
		$repeater = new Repeater();
		$repeater->add_control( 'quote', array( 'label' => __( 'Quote', 'nova-elements' ), 'type' => Controls_Manager::TEXTAREA, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'name', array( 'label' => __( 'Name', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'role', array( 'label' => __( 'Role or organization', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'portrait', array( 'label' => __( 'Portrait', 'nova-elements' ), 'type' => Controls_Manager::MEDIA, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'rating', array( 'label' => __( 'Rating (0 hides stars)', 'nova-elements' ), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'max' => 5, 'step' => 1, 'default' => 0 ) );
		$this->add_control(
			'items',
			array(
				'label' => __( 'Quotes', 'nova-elements' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array( 'quote' => __( 'We finally had a clear way to organize our work and keep everyone moving in the same direction.', 'nova-elements' ), 'name' => __( 'Avery Morgan', 'nova-elements' ), 'role' => __( 'Project lead', 'nova-elements' ) ),
					array( 'quote' => __( 'The new approach made it easier to explain our ideas and turn feedback into concrete next steps.', 'nova-elements' ), 'name' => __( 'Sofia Patel', 'nova-elements' ), 'role' => __( 'Design manager', 'nova-elements' ) ),
					array( 'quote' => __( 'Our team can now spend less time on handoffs and more time on the details our clients care about.', 'nova-elements' ), 'name' => __( 'Jordan Lee', 'nova-elements' ), 'role' => __( 'Studio director', 'nova-elements' ) ),
				),
				'title_field' => '{{ name }}',
			)
		);
		$this->add_control( 'layout', array( 'label' => __( 'Layout', 'nova-elements' ), 'type' => Controls_Manager::SELECT, 'default' => 'grid', 'options' => array( 'grid' => __( 'Grid', 'nova-elements' ), 'carousel' => __( 'Carousel', 'nova-elements' ) ) ) );
		$this->add_control( 'carousel_label', array( 'label' => __( 'Carousel accessible label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'Testimonials', 'nova-elements' ), 'condition' => array( 'layout' => 'carousel' ) ) );
		$counts = array( '' => __( 'Preset default', 'nova-elements' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' );
		foreach ( array( 'grid' => __( 'Grid columns', 'nova-elements' ), 'carousel' => __( 'Slides per view', 'nova-elements' ) ) as $layout => $label ) {
			$this->add_responsive_control(
				$layout . '_count',
				array(
					'label' => $label,
					'type' => Controls_Manager::SELECT,
					'options' => $counts,
					'condition' => array( 'layout' => $layout ),
					'selectors' => array( '{{WRAPPER}} .nova-testimonials--' . $layout => '--ne-testimonials-count: {{VALUE}};' ),
				)
			);
		}
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'text_style', array( 'label' => __( 'Text', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'quote', __( 'Quote', 'nova-elements' ), '.nova-testimonials__quote' );
		$this->add_text_style( 'author', __( 'Name', 'nova-elements' ), '.nova-testimonials__name' );
		$this->add_text_style( 'role', __( 'Role', 'nova-elements' ), '.nova-testimonials__role' );
		$this->add_control( 'rating_color', array( 'label' => __( 'Rating color', 'nova-elements' ), 'type' => Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .nova-testimonials__rating' => 'color: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'card_style', array( 'label' => __( 'Cards and spacing', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_panel_style( '.nova-testimonials__card' );
		$this->add_size( 'card_gap', __( 'Gap between cards', 'nova-elements' ), '.nova-testimonials', '--ne-testimonials-gap' );
		$this->add_size( 'content_gap', __( 'Content gap', 'nova-elements' ), '.nova-testimonials__card', '--ne-testimonials-content-gap' );
		$this->add_size( 'portrait_size', __( 'Portrait size', 'nova-elements' ), '.nova-testimonials__portrait', '--ne-testimonials-portrait', 160 );
		$this->add_dimensions( 'portrait_radius', __( 'Portrait radius', 'nova-elements' ), '{{WRAPPER}} .nova-testimonials__portrait img', 'border-radius' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$rows = $settings['items'] ?? array();
		$items = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$quote = $this->text_value( $row['quote'] ?? null );
			if ( '' === $quote ) {
				continue;
			}
			$rating = $row['rating'] ?? 0;
			$items[] = array(
				'quote' => $quote,
				'name' => $this->text_value( $row['name'] ?? null ),
				'role' => $this->text_value( $row['role'] ?? null ),
				'portrait' => $this->media_image( $row['portrait'] ?? null, 'thumbnail', true ),
				'rating' => is_numeric( $rating ) ? min( 5, max( 0, (int) $rating ) ) : 0,
			);
		}
		$layout = 'carousel' === ( $settings['layout'] ?? '' ) && count( $items ) > 1 ? 'carousel' : 'grid';
		$label = $this->text_value( $settings['carousel_label'] ?? null ) ?: __( 'Testimonials', 'nova-elements' );
		require dirname( __DIR__, 3 ) . '/templates/widgets/testimonials.php';
	}
}
