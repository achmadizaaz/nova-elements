<?php

namespace NovaElements\Core;

use NovaElements\Admin\Dashboard;
use NovaElements\Controls\VisualStylePicker;

defined( 'ABSPATH' ) || exit;

final class Plugin {
	public const VERSION = '0.3.0';

	public static function register( string $file ): void {
		$registry = new WidgetRegistry();
		$settings = new Settings( $registry );
		$assets   = new Assets( $file, $settings );
		$widgets  = new WidgetManager( $registry, $settings, $assets );

		if ( is_admin() ) {
			( new Dashboard( $registry, $settings, $widgets, $assets ) )->register();
		}

		// Install public integration hooks early, independent of plugin load order.
		add_action( 'elementor/elements/categories_registered', array( $widgets, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $widgets, 'register_widgets' ) );
		add_action(
			'elementor/controls/register',
			static function ( \Elementor\Controls_Manager $controls ): void {
				$controls->register( new VisualStylePicker() );
			}
		);

		$clear_cache = array( Compatibility::class, 'clear_elementor_cache' );
		register_activation_hook( $file, $clear_cache );
		add_action( 'add_option_' . Settings::WIDGETS, $clear_cache, 10, 0 );
		add_action( 'update_option_' . Settings::WIDGETS, $clear_cache, 10, 0 );
	}
}
