<?php
/**
 * Plugin Name: Nova Elements
 * Description: Design-focused widgets and site-wide styles for Elementor.
 * Version: 0.3.0
 * Requires at least: 6.5
 * Requires PHP: 8.2
 * Requires Plugins: elementor
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: nova-elements
 */

defined( 'ABSPATH' ) || exit;

if ( version_compare( PHP_VERSION, '8.2', '<' ) || version_compare( get_bloginfo( 'version' ), '6.5', '<' ) ) {
	add_action( 'admin_notices', static function () {
		if ( current_user_can( 'activate_plugins' ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'Nova Elements requires WordPress 6.5 and PHP 8.2 or later.', 'nova-elements' ) . '</p></div>';
		}
	} );
	return;
}

$nova_elements_autoload = __DIR__ . '/vendor/autoload.php';
if ( ! is_readable( $nova_elements_autoload ) ) {
	add_action( 'admin_notices', static function () {
		if ( current_user_can( 'activate_plugins' ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'Nova Elements is missing its Composer autoloader. Reinstall the built plugin or run Composer install in the plugin directory.', 'nova-elements' ) . '</p></div>';
		}
	} );
	unset( $nova_elements_autoload );
	return;
}

require_once $nova_elements_autoload;
unset( $nova_elements_autoload );

NovaElements\Core\Plugin::register( __FILE__ );
