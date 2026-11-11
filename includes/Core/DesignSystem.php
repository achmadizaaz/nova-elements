<?php

namespace NovaElements\Core;

defined( 'ABSPATH' ) || exit;

final class DesignSystem {
	public const DEFAULT_PRESET = 'editorial';

	public static function presets(): array {
		return array(
			'editorial' => __( 'Minimal Editorial', 'nova-elements' ),
			'corporate' => __( 'Modern Corporate', 'nova-elements' ),
			'gradient'  => __( 'Premium Gradient', 'nova-elements' ),
			'creative'  => __( 'Bold Creative', 'nova-elements' ),
			'dark'      => __( 'Elegant Dark', 'nova-elements' ),
		);
	}

	public static function palette(): array {
		return array(
			'primary'       => array( __( 'Primary', 'nova-elements' ), '#4f46e5' ),
			'secondary'     => array( __( 'Secondary', 'nova-elements' ), '#0f172a' ),
			'accent'        => array( __( 'Accent', 'nova-elements' ), '#06b6d4' ),
			'surface'       => array( __( 'Surface', 'nova-elements' ), '#ffffff' ),
			'surface-muted' => array( __( 'Muted surface', 'nova-elements' ), '#f8fafc' ),
			'text'          => array( __( 'Text', 'nova-elements' ), '#0f172a' ),
			'text-muted'    => array( __( 'Muted text', 'nova-elements' ), '#64748b' ),
			'border'        => array( __( 'Border', 'nova-elements' ), '#e2e8f0' ),
			'success'       => array( __( 'Success', 'nova-elements' ), '#15803d' ),
			'warning'       => array( __( 'Warning', 'nova-elements' ), '#92400e' ),
			'danger'        => array( __( 'Danger', 'nova-elements' ), '#b91c1c' ),
		);
	}

	public static function themes(): array {
		return array(
			'editorial' => array(
				'colors' => array( 'primary' => '#364567', 'secondary' => '#182134', 'accent' => '#17665c', 'surface' => '#fffdf8', 'surface-muted' => '#f6f1e8', 'text' => '#23232a', 'text-muted' => '#535461', 'border' => '#ded8cd' ),
				'defaults' => array( 'scale' => 'standard', 'spacing' => 'generous', 'radius' => 'small' ),
			),
			'corporate' => array(
				'colors' => array( 'primary' => '#2447a5', 'secondary' => '#101c3a', 'accent' => '#12766c', 'surface' => '#ffffff', 'surface-muted' => '#eef3fa', 'text' => '#172746', 'text-muted' => '#41516e', 'border' => '#cfdbed' ),
				'defaults' => array( 'scale' => 'standard', 'spacing' => 'balanced', 'radius' => 'standard', 'shadow' => 'soft' ),
			),
			'gradient' => array(
				'colors' => array( 'primary' => '#6334af', 'secondary' => '#241a44', 'accent' => '#197386', 'surface' => '#fcfaff', 'surface-muted' => '#f1ebfd', 'text' => '#251d40', 'text-muted' => '#55486c', 'border' => '#ddd0f3' ),
				'defaults' => array( 'scale' => 'standard', 'spacing' => 'generous', 'radius' => 'round', 'shadow' => 'soft' ),
			),
			'creative' => array(
				'colors' => array( 'primary' => '#5d249c', 'secondary' => '#231c32', 'accent' => '#12646b', 'surface' => '#fffdf6', 'surface-muted' => '#faefd9', 'text' => '#211b29', 'text-muted' => '#514456', 'border' => '#e7d7b8' ),
				'defaults' => array( 'scale' => 'large', 'spacing' => 'balanced', 'radius' => 'small' ),
			),
			'dark' => array(
				'colors' => array( 'primary' => '#c4b5fd', 'secondary' => '#111827', 'accent' => '#a5f3fc', 'surface' => '#172237', 'surface-muted' => '#1e2c44', 'text' => '#f8fafc', 'text-muted' => '#c9d5e7', 'border' => '#475874' ),
				'defaults' => array( 'scale' => 'standard', 'spacing' => 'balanced', 'radius' => 'standard' ),
			),
		);
	}

	/** Labels and allowlists shared by the settings sanitizer and form. */
	public static function fields(): array {
		$preserve = __( 'Use the theme default', 'nova-elements' );
		$fonts = array(
			'' => __( 'Use the inherited font', 'nova-elements' ),
			'sans' => __( 'System sans', 'nova-elements' ),
			'serif' => __( 'Georgia serif', 'nova-elements' ),
			'mono' => __( 'System monospace', 'nova-elements' ),
		);
		return array(
			'typography' => array(
				'primary_font' => array( __( 'Primary font family', 'nova-elements' ), $fonts ),
				'heading_font' => array( __( 'Heading font family', 'nova-elements' ), $fonts ),
				'body_font' => array( __( 'Body font family', 'nova-elements' ), $fonts ),
				'scale' => array( __( 'Type scale', 'nova-elements' ), array( '' => $preserve, 'small' => __( 'Compact', 'nova-elements' ), 'standard' => __( 'Standard', 'nova-elements' ), 'large' => __( 'Large', 'nova-elements' ) ) ),
				'elementor_fonts' => array( __( 'Font inheritance', 'nova-elements' ), array( '' => __( 'Theme and widget defaults', 'nova-elements' ), 'inherit' => __( 'Use Elementor Global Fonts for inherited families', 'nova-elements' ) ) ),
			),
			'components' => array(
				'radius' => array( __( 'Border radius', 'nova-elements' ), array( '' => $preserve, 'small' => __( 'Tight', 'nova-elements' ), 'standard' => __( 'Standard', 'nova-elements' ), 'round' => __( 'Rounded', 'nova-elements' ) ) ),
				'shadow' => array( __( 'Shadow intensity', 'nova-elements' ), array( '' => $preserve, 'none' => __( 'None', 'nova-elements' ), 'soft' => __( 'Soft', 'nova-elements' ), 'deep' => __( 'Deep', 'nova-elements' ) ) ),
				'button' => array( __( 'Button shape', 'nova-elements' ), array( '' => __( 'Use the widget layout', 'nova-elements' ), 'square' => __( 'Square', 'nova-elements' ), 'round' => __( 'Rounded', 'nova-elements' ), 'pill' => __( 'Pill', 'nova-elements' ) ) ),
				'card' => array( __( 'Card treatment', 'nova-elements' ), array( '' => __( 'Use the widget layout', 'nova-elements' ), 'outline' => __( 'Outline', 'nova-elements' ), 'raised' => __( 'Raised', 'nova-elements' ) ) ),
			),
			'responsive' => array(
				'spacing' => array( __( 'Fluid layout spacing', 'nova-elements' ), array( '' => $preserve, 'compact' => __( 'Compact', 'nova-elements' ), 'balanced' => __( 'Balanced', 'nova-elements' ), 'generous' => __( 'Generous', 'nova-elements' ) ) ),
			),
			'motion' => array(
				'duration' => array( __( 'Transition duration', 'nova-elements' ), array( '' => $preserve, 'fast' => __( 'Fast', 'nova-elements' ), 'normal' => __( 'Normal', 'nova-elements' ), 'slow' => __( 'Slow', 'nova-elements' ) ) ),
				'hover' => array( __( 'Hover transitions', 'nova-elements' ), array( '' => __( 'Enabled', 'nova-elements' ), 'off' => __( 'Instant feedback', 'nova-elements' ) ) ),
				'motion' => array( __( 'Reduced motion', 'nova-elements' ), array( '' => __( 'Follow the operating system', 'nova-elements' ), 'reduce' => __( 'Always reduce motion', 'nova-elements' ) ) ),
			),
		);
	}

	/** Arguments are sanitized by Settings before any declarations are compiled. */
	public static function declarations( array $settings, array $palette ): array {
		$theme = self::themes()[ $settings['theme'] ?? '' ] ?? array();
		$colors = $palette + ( $theme['colors'] ?? array() );
		if ( 'inherit' === ( $settings['elementor_colors'] ?? '' ) ) {
			foreach ( array( 'primary', 'secondary', 'accent', 'text' ) as $role ) {
				if ( ! isset( $colors[ $role ] ) ) {
					$colors[ $role ] = 'var(--e-global-color-' . $role . ',' . self::palette()[ $role ][1] . ')';
				}
			}
		}
		$values = $colors;
		if ( isset( $colors['text-muted'] ) ) {
			$values['content-muted'] = 'var(--ne-text-muted)';
		}
		if ( isset( $colors['primary'] ) ) {
			$values['content-link'] = 'var(--ne-primary)';
			$primary = $colors['primary'];
			if ( str_starts_with( $primary, '#' ) ) {
				$ink = self::filled_ink( $primary );
				$hover = '#fff' === $ink ? '#000' : '#fff';
				$values['filled-surface'] = 'var(--ne-primary)';
				$values['filled-ink'] = $ink;
				$values['filled-hover'] = 'color-mix(in srgb,var(--ne-primary) 87%,' . $hover . ')';
			}
		}
		if ( isset( $colors['surface'] ) || isset( $colors['surface-muted'] ) ) {
			$values += array(
				'soft-accent' => 'color-mix(in srgb,var(--ne-primary) 12%,var(--ne-surface))',
				'soft-surface' => 'var(--ne-surface-muted)',
				'badge-surface' => 'var(--ne-surface)',
				'pager-surface' => 'var(--ne-surface)',
			);
		}
		if ( 'dark' === ( $settings['theme'] ?? '' ) ) {
			$values['rating-ink'] = '#fde68a';
		}
		$options = $settings + ( $theme['defaults'] ?? array() );
		$fonts = array( 'sans' => 'system-ui,sans-serif', 'serif' => 'Georgia,serif', 'mono' => 'ui-monospace,monospace' );
		foreach ( array( 'primary' => 'primary', 'heading' => 'primary', 'body' => 'text' ) as $role => $kit_role ) {
			$font = $fonts[ $options[ $role . '_font' ] ?? '' ] ?? '';
			if ( '' !== $font ) {
				$values[ 'font-' . $role ] = $font;
			} elseif ( 'inherit' === ( $options['elementor_fonts'] ?? '' ) ) {
				$values[ 'font-' . $role ] = 'var(--e-global-typography-' . $kit_role . '-font-family,var(--ne-font-primary,inherit))';
				if ( 'primary' === $role ) {
					$values['font-primary'] = 'var(--e-global-typography-primary-font-family,inherit)';
				}
			}
		}
		$presets = array(
			'scale' => array( 'small' => array( 'type-scale' => '.92' ), 'standard' => array( 'type-scale' => '1' ), 'large' => array( 'type-scale' => '1.12' ) ),
			'spacing' => array(
				'compact' => array( 'layout-gap' => 'clamp(.625rem,1.5vw,1.125rem)' ),
				'balanced' => array( 'layout-gap' => 'clamp(.875rem,2.25vw,1.75rem)' ),
				'generous' => array( 'layout-gap' => 'clamp(1.125rem,3vw,2.75rem)' ),
			),
			'radius' => array(
				'small' => array( 'radius-sm' => '4px', 'radius-md' => '8px', 'radius-lg' => '12px', 'radius-xl' => '16px' ),
				'standard' => array( 'radius-sm' => '8px', 'radius-md' => '14px', 'radius-lg' => '20px', 'radius-xl' => '28px' ),
				'round' => array( 'radius-sm' => '12px', 'radius-md' => '20px', 'radius-lg' => '28px', 'radius-xl' => '36px' ),
			),
			'shadow' => array(
				'none' => array( 'card-shadow' => '0 0 transparent', 'button-shadow' => 'none' ),
				'soft' => array( 'card-shadow' => 'var(--ne-shadow-sm)', 'button-shadow' => 'var(--ne-shadow-sm)' ),
				'deep' => array( 'card-shadow' => 'var(--ne-shadow-lg)', 'button-shadow' => 'var(--ne-shadow-md)' ),
			),
			'button' => array( 'square' => array( 'button-radius' => '0px' ), 'round' => array( 'button-radius' => 'var(--ne-radius-md)' ), 'pill' => array( 'button-radius' => 'var(--ne-radius-full)' ) ),
			'card' => array( 'outline' => array( 'card-edge' => 'inset 0 0 0 1px var(--ne-border)' ), 'raised' => array( 'card-edge' => 'var(--ne-shadow-md)' ) ),
			'duration' => array(
				'fast' => array( 'motion-duration' => 'var(--ne-transition-fast)' ),
				'normal' => array( 'motion-duration' => 'var(--ne-transition-normal)' ),
				'slow' => array( 'motion-duration' => 'var(--ne-transition-slow)' ),
			),
		);
		foreach ( $presets as $key => $choices ) {
			$values += $choices[ $options[ $key ] ?? '' ] ?? array();
		}
		if ( 'off' === ( $options['hover'] ?? '' ) || 'reduce' === ( $options['motion'] ?? '' ) ) {
			$values['motion-duration'] = '0ms';
		}
		return $values;
	}

	private static function filled_ink( string $color ): string {
		$hex = substr( $color, 1 );
		if ( 3 === strlen( $hex ) ) {
			$hex = preg_replace( '/(.)/', '$1$1', $hex );
		}
		$channels = array_map(
			static function ( string $part ): float {
				$value = hexdec( $part ) / 255;
				return $value <= .04045 ? $value / 12.92 : ( ( $value + .055 ) / 1.055 ) ** 2.4;
			},
			str_split( $hex, 2 )
		);
		$luminance = .2126 * $channels[0] + .7152 * $channels[1] + .0722 * $channels[2];
		return $luminance > .179 ? '#000' : '#fff';
	}
}
