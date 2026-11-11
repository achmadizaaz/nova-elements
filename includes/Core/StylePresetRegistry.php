<?php

namespace NovaElements\Core;

defined( 'ABSPATH' ) || exit;

final class StylePresetRegistry {
	public static function for_widget( string $widget ): array {
		$descriptions = match ( $widget ) {
			'ne-advanced-heading' => array(
				__( 'Serif headline with a fine rule and a lead paragraph.', 'nova-elements' ),
				__( 'Compact headline and copy beside an accent rail.', 'nova-elements' ),
				__( 'Centered display text on a softly lit panel.', 'nova-elements' ),
				__( 'Oversized title with a separate subtitle column.', 'nova-elements' ),
				__( 'Framed headline with a ruled subtitle row.', 'nova-elements' ),
			),
			'ne-blog-grid' => array(
				__( 'Editorial images, serif titles, and a fine metadata rule.', 'nova-elements' ),
				__( 'Inset article cards with a visible taxonomy label.', 'nova-elements' ),
				__( 'A wide lead story above smaller supporting cards.', 'nova-elements' ),
				__( 'Full-bleed images with readable text overlays.', 'nova-elements' ),
				__( 'Side-by-side images and text with ruled metadata.', 'nova-elements' ),
			),
			'ne-feature-card' => array(
				__( 'Wide media above a serif heading and fine rules.', 'nova-elements' ),
				__( 'Compact media alongside an accented copy panel.', 'nova-elements' ),
				__( 'Raised media above centered copy and a pill link.', 'nova-elements' ),
				__( 'Large headline and asymmetric media placement.', 'nova-elements' ),
				__( 'Framed copy followed by a wide media strip.', 'nova-elements' ),
			),
			'ne-advanced-button' => array(
				__( 'Serif text, a fine underline, and an end arrow.', 'nova-elements' ),
				__( 'Structured button with a divided icon section.', 'nova-elements' ),
				__( 'Gradient pill with a circular inset icon.', 'nova-elements' ),
				__( 'Oversized text and an offset edge with a separate icon tile.', 'nova-elements' ),
				__( 'Inset dark frame with a leading icon.', 'nova-elements' ),
			),
			'ne-testimonials' => array(
				__( 'Serif quotes with fine rules and attribution below.', 'nova-elements' ),
				__( 'Author-first cards with a separator above each quote.', 'nova-elements' ),
				__( 'Centered quotes with a medallion and stacked attribution.', 'nova-elements' ),
				__( 'Oversized quotes and an offset quotation mark.', 'nova-elements' ),
				__( 'Framed dark cards with a separate attribution band.', 'nova-elements' ),
			),
			default => array(),
		};
		$result = array();
		foreach ( DesignSystem::presets() as $id => $label ) {
			if ( ! $descriptions ) {
				break;
			}
			$result[ $id ] = array(
				'label' => $label,
				'description' => array_shift( $descriptions ),
				'thumbnail' => 'assets/presets/' . substr( $widget, 3 ) . '/' . $id . '.webp',
			);
		}
		return $result;
	}

	public static function resolve( string $widget, mixed $value ): string {
		$presets = self::for_widget( $widget );
		if ( is_string( $value ) && isset( $presets[ $value ] ) ) {
			return $value;
		}
		$default = Settings::default_preset();
		return isset( $presets[ $default ] ) ? $default : ( array_key_first( $presets ) ?? DesignSystem::DEFAULT_PRESET );
	}

	public static function choices( string $widget ): array {
		$presets = self::for_widget( $widget );
		$default = $presets[ self::resolve( $widget, 'site' ) ] ?? null;
		if ( $default ) {
			/* translators: %s: current layout label. */
			$default['description'] = sprintf( __( 'Uses the site layout default, currently %s.', 'nova-elements' ), $default['label'] );
			$default['label'] = __( 'Site default', 'nova-elements' );
			$presets = array( 'site' => $default ) + $presets;
		}
		$root = dirname( __DIR__, 2 );
		foreach ( $presets as &$preset ) {
			$path = $preset['thumbnail'];
			if ( ! is_readable( $root . '/' . $path ) ) {
				$path = 'assets/presets/fallback.svg';
			}
			$preset['thumbnail'] = plugins_url( $path, $root . '/nova-elements.php' );
		}
		unset( $preset );
		return $presets;
	}
}
