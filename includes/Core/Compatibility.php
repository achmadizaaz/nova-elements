<?php

namespace NovaElements\Core;

defined( 'ABSPATH' ) || exit;

final class Compatibility {
	public const MIN_ELEMENTOR = '3.20.0';

	public static function elementor_ready(): bool {
		return did_action( 'elementor/loaded' ) && defined( 'ELEMENTOR_VERSION' )
			&& version_compare( ELEMENTOR_VERSION, self::MIN_ELEMENTOR, '>=' );
	}

	public static function woocommerce_active(): bool {
		return class_exists( 'WooCommerce', false );
	}

	public static function clear_elementor_cache(): void {
		if ( ! self::elementor_ready() ) {
			return;
		}

		$files = \Elementor\Plugin::instance()->files_manager;
		if ( is_callable( array( $files, 'clear_cache' ) ) ) {
			// Match Elementor's flush-css operation so previously absent widgets can render again.
			$files->clear_cache();
		}
	}
}
