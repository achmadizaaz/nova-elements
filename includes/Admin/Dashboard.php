<?php

namespace NovaElements\Admin;

use NovaElements\Core\Assets;
use NovaElements\Core\Compatibility;
use NovaElements\Core\DesignSystem;
use NovaElements\Core\Plugin;
use NovaElements\Core\Settings;
use NovaElements\Core\WidgetManager;
use NovaElements\Core\WidgetRegistry;

defined( 'ABSPATH' ) || exit;

final class Dashboard {
	private array $screens = array();

	public function __construct(
		private WidgetRegistry $registry,
		private Settings $settings,
		private WidgetManager $widgets,
		private Assets $assets
	) {}

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_menus' ) );
		add_action( 'admin_init', array( $this->settings, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_notices', array( $this, 'dependency_notice' ) );
	}

	private function pages(): array {
		return array(
			'nova-elements'              => __( 'Dashboard', 'nova-elements' ),
			'nova-elements-widgets'      => __( 'Widget Manager', 'nova-elements' ),
			'nova-elements-styles'       => __( 'Global Styles', 'nova-elements' ),
			'nova-elements-integrations' => __( 'Integrations', 'nova-elements' ),
			'nova-elements-settings'     => __( 'Settings', 'nova-elements' ),
			'nova-elements-status'       => __( 'System Status', 'nova-elements' ),
		);
	}

	public function register_menus(): void {
		$dashboard = add_menu_page(
			__( 'Nova Elements', 'nova-elements' ),
			__( 'Nova Elements', 'nova-elements' ),
			'manage_options',
			'nova-elements',
			array( $this, 'dashboard' ),
			'dashicons-layout'
		);
		$this->screens[ $dashboard ] = 'nova-elements';

		foreach ( $this->pages() as $slug => $label ) {
			$screen = add_submenu_page(
				'nova-elements',
				$label,
				$label,
				'manage_options',
				$slug,
				'nova-elements' === $slug ? '' : fn () => $this->render( $slug )
			);
			if ( $screen ) {
				$this->screens[ $screen ] = $slug;
			}
		}
	}

	public function dashboard(): void {
		$this->render( 'nova-elements' );
	}

	public function enqueue_assets( string $screen ): void {
		if ( isset( $this->screens[ $screen ] ) ) {
			$page = $this->screens[ $screen ];
			$this->assets->enqueue_admin(
				in_array( $page, array( 'nova-elements', 'nova-elements-styles' ), true ),
				in_array( $page, array( 'nova-elements', 'nova-elements-widgets' ), true )
			);
		}
	}

	public function dependency_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) || Compatibility::elementor_ready() ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html( $this->elementor_status() )
		);
	}

	private function elementor_status(): string {
		if ( ! Compatibility::elementor_ready() ) {
			/* translators: %s: minimum Elementor version. */
			return sprintf( __( 'Nova Elements widgets require Elementor %s or later to be active.', 'nova-elements' ), Compatibility::MIN_ELEMENTOR );
		}
		/* translators: %s: installed Elementor version. */
		return sprintf( __( 'Elementor %s is active and meets the minimum requirement.', 'nova-elements' ), ELEMENTOR_VERSION );
	}

	private function render( string $page ): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage Nova Elements.', 'nova-elements' ) );
		}

		$pages           = $this->pages();
		$version         = Plugin::VERSION;
		$available       = $this->widgets->available_widgets();
		$enabled         = $this->settings->enabled_widgets();
		$active          = $this->widgets->active_widgets();
		$categories      = $this->registry->categories();
		$category_counts = array_count_values( array_column( $available, 'category' ) );
		$preset_count    = array_sum( array_map( static fn ( array $widget ): int => count( $widget['presets'] ), $available ) );
		$presets         = DesignSystem::presets();
		$default_preset  = Settings::default_preset();
		$elementor_ready = Compatibility::elementor_ready();
		$elementor_info  = $this->elementor_status();
		$woo_active      = Compatibility::woocommerce_active();
		$palette         = $this->settings->palette();
		$tokens          = DesignSystem::palette();
		$global_style    = $this->settings->global_style();
		$template_path   = dirname( __DIR__, 2 ) . '/templates/admin/';
		require $template_path . 'dashboard.php';
	}
}
