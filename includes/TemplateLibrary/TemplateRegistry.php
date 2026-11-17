<?php

namespace NovaElements\TemplateLibrary;

use NovaElements\Core\DesignSystem;
use NovaElements\Core\WidgetRegistry;

defined( 'ABSPATH' ) || exit;

final class TemplateRegistry {
	private const SCHEMA = 1;
	private array $templates;

	public function __construct( private string $root, private WidgetRegistry $widgets ) {}

	public static function types(): array {
		return array( 'section' => __( 'Section', 'nova-elements' ), 'page' => __( 'Full page', 'nova-elements' ), 'widget' => __( 'Widget preset', 'nova-elements' ) );
	}

	public static function categories(): array {
		return array(
			'hero' => __( 'Hero', 'nova-elements' ), 'about' => __( 'About', 'nova-elements' ),
			'features' => __( 'Features', 'nova-elements' ), 'services' => __( 'Services', 'nova-elements' ),
			'blog' => __( 'Blog & News', 'nova-elements' ), 'testimonials' => __( 'Testimonials', 'nova-elements' ),
			'team' => __( 'Team', 'nova-elements' ), 'pricing' => __( 'Pricing', 'nova-elements' ),
			'faq' => __( 'FAQ', 'nova-elements' ), 'contact' => __( 'Contact', 'nova-elements' ),
			'cta' => __( 'Call to Action', 'nova-elements' ), 'education' => __( 'Education', 'nova-elements' ),
			'woocommerce' => __( 'WooCommerce', 'nova-elements' ), 'pages' => __( 'Full Pages', 'nova-elements' ),
		);
	}

	public function all(): array {
		if ( isset( $this->templates ) ) {
			return $this->templates;
		}
		$this->templates = array();
		foreach ( glob( $this->root . '/*/manifest.json' ) ?: array() as $candidate ) {
			$id = basename( dirname( $candidate ) );
			$file = $this->file( $id, 'manifest.json' );
			$manifest = $file ? self::read_json( $file, 8192 ) : null;
			if ( $manifest && $this->valid_manifest( $id, $manifest ) ) {
				$this->templates[ $id ] = $manifest;
			}
		}
		uasort( $this->templates, static fn ( array $a, array $b ): int => strcasecmp( $a['name'], $b['name'] ) );
		return $this->templates;
	}

	public function find( string $id ): ?array {
		return $this->all()[ $id ] ?? null;
	}

	public function filter( string $search, string $type, string $category ): array {
		return array_filter(
			$this->all(),
			static function ( array $item ) use ( $search, $type, $category ): bool {
				$styles = DesignSystem::presets();
				$categories = self::categories();
				$haystack = implode( ' ', array( $item['name'], $item['description'], $styles[ $item['style'] ], $categories[ $item['category'] ] ) );
				return ( '' === $type || $type === $item['type'] )
					&& ( '' === $category || $category === $item['category'] )
					&& ( '' === $search || stripos( $haystack, $search ) !== false );
			}
		);
	}

	public function file( string $id, string $relative ): ?string {
		if ( ! preg_match( '/^[a-z0-9][a-z0-9-]{0,63}$/D', $id )
			|| ! preg_match( '#^(?:manifest\.json|template\.json|preview(?:-tablet|-mobile)?\.webp|media/[a-z0-9-]+\.webp)$#D', $relative ) ) {
			return null;
		}
		$root = realpath( $this->root );
		$directory = realpath( $this->root . '/' . $id );
		$path = realpath( $this->root . '/' . $id . '/' . $relative );
		if ( ! $root || ! $directory || ! $path || ! str_starts_with( $directory . DIRECTORY_SEPARATOR, $root . DIRECTORY_SEPARATOR )
			|| ! str_starts_with( $path, $directory . DIRECTORY_SEPARATOR ) || ! is_file( $path ) ) {
			return null;
		}
		return $path;
	}

	public static function read_json( string $path, int $limit ): ?array {
		$contents = @file_get_contents( $path, false, null, 0, $limit + 1 );
		if ( ! is_string( $contents ) || strlen( $contents ) > $limit ) {
			return null;
		}
		$result = json_decode( $contents, true, 32 );
		return is_array( $result ) ? $result : null;
	}

	private function valid_manifest( string $id, array $manifest ): bool {
		$required = $manifest['required_widgets'] ?? null;
		$assets = $manifest['assets'] ?? array();
		if ( ( $manifest['schema_version'] ?? null ) !== self::SCHEMA || ( $manifest['id'] ?? null ) !== $id
			|| ! $this->label( $manifest['name'] ?? null, 100 ) || ! $this->label( $manifest['description'] ?? null, 350 )
			|| ! is_string( $manifest['version'] ?? null ) || ! preg_match( '/^[0-9]+\.[0-9]+\.[0-9]+$/D', $manifest['version'] )
			|| ! is_string( $manifest['elementor_min'] ?? null ) || ! preg_match( '/^[0-9]+\.[0-9]+\.[0-9]+$/D', $manifest['elementor_min'] )
			|| ! is_string( $manifest['type'] ?? null ) || ! isset( self::types()[ $manifest['type'] ] )
			|| ! is_string( $manifest['category'] ?? null ) || ! isset( self::categories()[ $manifest['category'] ] )
			|| ! is_string( $manifest['style'] ?? null ) || ! isset( DesignSystem::presets()[ $manifest['style'] ] )
			|| ! is_array( $required ) || ! array_is_list( $required ) || ! $required || count( $required ) > 10
			|| ! is_array( $assets ) || ! array_is_list( $assets ) || count( $assets ) > 8 ) {
			return false;
		}
		$widgets = $this->widgets->all();
		foreach ( $required as $widget ) {
			if ( ! is_string( $widget ) || ! isset( $widgets[ $widget ] ) ) {
				return false;
			}
		}
		if ( count( array_unique( $required ) ) !== count( $required ) ) {
			return false;
		}
		foreach ( $assets as $asset ) {
			if ( ! is_array( $asset ) || ! is_string( $asset['element'] ?? null ) || ! preg_match( '/^[a-f0-9]{7,8}$/D', $asset['element'] )
				|| ! is_string( $asset['file'] ?? null ) || ! preg_match( '/^[a-z0-9-]+\.webp$/D', $asset['file'] )
				|| ! $this->label( $asset['alt'] ?? null, 180 )
				|| ! is_string( $asset['control'] ?? 'feature_image' )
				|| ! preg_match( '/^(?:feature_image|before_image|after_image|gallery_image_(?:[1-9]|1[0-2]))$/D', $asset['control'] ?? 'feature_image' ) ) {
				return false;
			}
		}
		return true;
	}

	private function label( mixed $value, int $limit ): bool {
		return is_string( $value ) && '' !== trim( $value ) && strlen( $value ) <= $limit;
	}
}
