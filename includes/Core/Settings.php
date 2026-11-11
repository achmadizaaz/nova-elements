<?php

namespace NovaElements\Core;

defined( 'ABSPATH' ) || exit;

final class Settings {
	public const WIDGETS = 'nova_elements_widgets';
	public const PALETTE = 'nova_elements_palette';
	public const GLOBAL_STYLE = 'nova_elements_global_style';
	public const PREFERENCES = 'nova_elements_preferences';

	public function __construct( private WidgetRegistry $registry ) {}

	public function register(): void {
		register_setting( self::WIDGETS, self::WIDGETS, array( 'type' => 'array', 'sanitize_callback' => array( $this, 'sanitize_widgets' ) ) );
		register_setting( self::PALETTE, self::PALETTE, array( 'type' => 'array', 'sanitize_callback' => array( $this, 'sanitize_palette' ) ) );
		register_setting( self::PALETTE, self::GLOBAL_STYLE, array( 'type' => 'array', 'sanitize_callback' => array( self::class, 'sanitize_global_style' ) ) );
		register_setting( self::PREFERENCES, self::PREFERENCES, array( 'type' => 'array', 'sanitize_callback' => array( self::class, 'sanitize_preferences' ) ) );
	}

	public static function sanitize_global_style( mixed $input ): array {
		if ( ! is_array( $input ) ) {
			return array();
		}
		$allowed = array(
			'theme' => array_keys( DesignSystem::themes() ),
			'elementor_colors' => array( 'inherit' ),
		);
		foreach ( DesignSystem::fields() as $panel ) {
			foreach ( $panel as $key => $field ) {
				$allowed[ $key ] = array_keys( $field[1] );
			}
		}
		$result = array();
		foreach ( $allowed as $key => $values ) {
			$value = $input[ $key ] ?? null;
			if ( is_string( $value ) && '' !== $value && in_array( $value, $values, true ) ) {
				$result[ $key ] = $value;
			}
		}
		return $result;
	}

	public function global_style(): array {
		return self::sanitize_global_style( get_option( self::GLOBAL_STYLE, array() ) );
	}

	public function sanitize_widgets( mixed $input ): array {
		$result = array();
		foreach ( $this->registry->all() as $id => $widget ) {
			$value = is_array( $input ) ? ( $input[ $id ] ?? null ) : null;
			if ( in_array( $value, array( '0', '1' ), true ) ) {
				$result[ $id ] = $value;
			}
		}
		return $result;
	}

	public function sanitize_palette( mixed $input ): array {
		$result = array();
		foreach ( DesignSystem::palette() as $key => $token ) {
			$value = is_array( $input ) ? ( $input[ $key ] ?? null ) : null;
			$color = is_string( $value ) ? sanitize_hex_color( $value ) : null;
			if ( $color ) {
				$result[ $key ] = $color;
			}
		}
		return $result;
	}

	public static function sanitize_preferences( mixed $input ): array {
		$preset = is_array( $input ) ? ( $input['preset'] ?? null ) : null;
		return array( 'preset' => is_string( $preset ) && isset( DesignSystem::presets()[ $preset ] ) ? $preset : DesignSystem::DEFAULT_PRESET );
	}

	public function enabled_widgets(): array {
		$saved = $this->sanitize_widgets( get_option( self::WIDGETS, array() ) );
		return array_filter(
			$this->registry->all(),
			static fn ( string $id ): bool => '0' !== ( $saved[ $id ] ?? '1' ),
			ARRAY_FILTER_USE_KEY
		);
	}

	public function palette(): array {
		return $this->sanitize_palette( get_option( self::PALETTE, array() ) );
	}

	public static function default_preset(): string {
		return self::sanitize_preferences( get_option( self::PREFERENCES, array() ) )['preset'];
	}
}
