<?php

namespace NovaElements\Admin;

use NovaElements\Core\DesignSystem;
use NovaElements\Core\Plugin;
use NovaElements\Core\WidgetRegistry;
use NovaElements\TemplateLibrary\TemplateImporter;
use NovaElements\TemplateLibrary\TemplateRegistry;

defined( 'ABSPATH' ) || exit;

final class TemplateLibraryPage {
	private const SLUG = 'nova-elements-library';
	private const ACTION = 'nova_import_template';

	public function __construct( private string $plugin_file, private TemplateRegistry $registry, private TemplateImporter $importer ) {}

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'menu' ), 20 );
		add_action( 'admin_post_' . self::ACTION, array( $this, 'import' ) );
	}

	public static function url(): string {
		return admin_url( 'admin.php?page=' . self::SLUG );
	}

	public static function finder( array $categories ): array {
		if ( current_user_can( 'edit_posts' ) ) {
			$categories['create']['items']['nova-elements-library'] = array(
				'title' => __( 'Nova Elements Template Library', 'nova-elements' ),
				'icon' => 'folder-o',
				'url' => self::url(),
				'keywords' => array( 'nova', 'template', 'library', 'section' ),
			);
		}
		return $categories;
	}

	public function menu(): void {
		$screen = add_submenu_page( 'nova-elements', __( 'Template Library', 'nova-elements' ), __( 'Template Library', 'nova-elements' ), 'edit_posts', self::SLUG, array( $this, 'render' ) );
		if ( $screen ) {
			add_action( 'load-' . $screen, array( $this, 'assets' ) );
		}
	}

	public function assets(): void {
		wp_enqueue_style( 'nova-elements-library', plugins_url( 'assets/css/library.min.css', $this->plugin_file ), array(), Plugin::VERSION );
		wp_enqueue_script( 'nova-elements-library', plugins_url( 'assets/js/library.min.js', $this->plugin_file ), array(), Plugin::VERSION, true );
	}

	public function render(): void {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You cannot access the Nova Elements library.', 'nova-elements' ), '', array( 'response' => 403 ) );
		}

		// Public catalog filters are read-only. They do not change site data.
		$search = self::query( 'q' );
		$type = self::query( 'type' );
		$category = self::query( 'category' );
		$types = TemplateRegistry::types();
		$categories = TemplateRegistry::categories();
		$styles = DesignSystem::presets();
		$widgets = ( new WidgetRegistry() )->all();
		$matches = $this->registry->filter( $search, $type, $category );
		$page_count = max( 1, (int) ceil( count( $matches ) / 12 ) );
		$page = min( $page_count, max( 1, (int) self::query( 'library_page' ) ) );
		$cards = array_slice( $matches, ( $page - 1 ) * 12, 12, true );
		$native = post_type_exists( TemplateImporter::POST_TYPE ) ? admin_url( 'edit.php?post_type=' . TemplateImporter::POST_TYPE ) : '';
		$status = self::query( 'nova_result' );
		$notice = match ( $status ) {
			'imported' => __( 'Template saved to Elementor Site Templates. Return to your page, open the template folder, and choose Insert under Site Templates or My Templates.', 'nova-elements' ),
			'elementor' => __( 'This template needs a compatible Elementor installation.', 'nova-elements' ),
			'widgets' => __( 'Enable the required Nova Elements widgets and try again.', 'nova-elements' ),
			'uploads' => __( 'Your account cannot upload the bundled media.', 'nova-elements' ),
			'media' => __( 'The bundled media could not be imported.', 'nova-elements' ),
			'invalid', 'unknown' => __( 'The selected bundle is unavailable or failed validation.', 'nova-elements' ),
			'save' => __( 'Elementor could not save the template.', 'nova-elements' ),
			default => '',
		};
		$can_import = TemplateImporter::can_import();
		require dirname( __DIR__, 2 ) . '/templates/admin/template-library.php';
	}

	public function import(): void {
		if ( ! TemplateImporter::can_import() ) {
			wp_die( esc_html__( 'You cannot import Elementor templates.', 'nova-elements' ), '', array( 'response' => 403 ) );
		}
		$id = isset( $_POST['nova_template'] ) && is_string( $_POST['nova_template'] ) ? wp_unslash( $_POST['nova_template'] ) : '';
		if ( ! preg_match( '/^[a-z0-9][a-z0-9-]{0,63}$/D', $id ) ) {
			wp_die( esc_html__( 'Invalid template ID.', 'nova-elements' ), '', array( 'response' => 400 ) );
		}
		check_admin_referer( self::ACTION . '_' . $id );
		$result = $this->importer->import( $id );
		wp_safe_redirect( add_query_arg( 'nova_result', is_wp_error( $result ) ? $result->get_error_code() : 'imported', self::url() ), 303 );
		exit;
	}

	private static function query( string $key ): string {
		return isset( $_GET[ $key ] ) && is_string( $_GET[ $key ] ) ? substr( sanitize_text_field( wp_unslash( $_GET[ $key ] ) ), 0, 100 ) : '';
	}
}
