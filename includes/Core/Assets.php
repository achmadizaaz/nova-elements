<?php

namespace NovaElements\Core;

defined( 'ABSPATH' ) || exit;

final class Assets {
	public function __construct( private string $plugin_file, private Settings $settings ) {}

	public function register_widget_style( string $name ): void {
		if ( ! wp_style_is( 'nova-elements-tokens', 'registered' ) ) {
			$this->register_style( 'tokens' );
			$declarations = '';
			foreach ( DesignSystem::declarations( $this->settings->global_style(), $this->settings->palette() ) as $token => $value ) {
				$declarations .= '--ne-' . $token . ':' . $value . ';';
			}
			if ( '' !== $declarations ) {
				wp_add_inline_style( 'nova-elements-tokens', '.nova-widget{' . $declarations . '}' );
			}
		}

		$this->register_style( $name, array( 'nova-elements-tokens' ) );
	}

	public function enqueue_admin( bool $previews, bool $manager ): void {
		if ( $previews ) {
			$this->register_widget_style( 'heading' );
		}
		$this->register_style( 'admin', $previews ? array( 'nova-elements-heading' ) : array() );
		wp_enqueue_style( 'nova-elements-admin' );
		if ( $manager ) {
			wp_enqueue_script( 'nova-elements-admin', plugins_url( 'assets/js/admin.min.js', $this->plugin_file ), array(), Plugin::VERSION, true );
		}
	}

	private function register_style( string $name, array $dependencies = array() ): void {
		wp_register_style(
			'nova-elements-' . $name,
			plugins_url( 'assets/css/' . $name . '.min.css', $this->plugin_file ),
			$dependencies,
			Plugin::VERSION
		);
	}
}
