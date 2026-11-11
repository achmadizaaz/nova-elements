<?php

namespace NovaElements\Core;

defined( 'ABSPATH' ) || exit;

final class WidgetManager {
	public function __construct(
		private WidgetRegistry $registry,
		private Settings $settings,
		private Assets $assets
	) {}

	public function available_widgets(): array {
		return array_filter(
			$this->registry->all(),
			static fn ( array $widget ): bool => ! $widget['woocommerce'] || Compatibility::woocommerce_active()
		);
	}

	public function active_widgets(): array {
		if ( ! Compatibility::elementor_ready() ) {
			return array();
		}
		return array_intersect_key( $this->available_widgets(), $this->settings->enabled_widgets() );
	}

	public function register_category( \Elementor\Elements_Manager $manager ): void {
		if ( $this->active_widgets() ) {
			$manager->add_category( 'nova-elements', array( 'title' => __( 'Nova Elements', 'nova-elements' ) ) );
		}
	}

	public function register_widgets( \Elementor\Widgets_Manager $manager ): void {
		foreach ( $this->active_widgets() as $definition ) {
			$this->assets->register_widget_style( $definition['style'] );
			$manager->register( new $definition['class']() );
		}
	}
}
