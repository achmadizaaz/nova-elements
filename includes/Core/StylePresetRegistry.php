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
			'ne-blog-list' => array(
				__( 'Horizontal editorial stories with an image and a fine dividing rule.', 'nova-elements' ),
				__( 'Inset reading rows with trailing thumbnail and category chip.', 'nova-elements' ),
				__( 'A full-width lead photograph above compact supporting rows.', 'nova-elements' ),
				__( 'Alternating magazine panels with offset media and a heavy accent rule.', 'nova-elements' ),
				__( 'A dark framed reading list with an inset thumbnail and ruled byline.', 'nova-elements' ),
			),
			'ne-blog-masonry' => array(
				__( 'Tall editorial photographs with staggered heights and fine rules.', 'nova-elements' ),
				__( 'Inset structured cards with alternating landscape and portrait media.', 'nova-elements' ),
				__( 'Layered copy panels that overlap mixed-height photographs.', 'nova-elements' ),
				__( 'An expressive collage with overlaid titles and uneven image heights.', 'nova-elements' ),
				__( 'Dark tiles with framed portraits and a separate metadata band.', 'nova-elements' ),
			),
			'ne-blog-carousel' => array(
				__( 'An editorial strip of two photo-led stories with fine dividers.', 'nova-elements' ),
				__( 'Compact inset cards with visible taxonomy and structured controls.', 'nova-elements' ),
				__( 'One large feature at a time, split between image and copy.', 'nova-elements' ),
				__( 'Full-bleed story covers with text overlays and a heavy accent rule.', 'nova-elements' ),
				__( 'Wide dark slides with framed thumbnails and ruled metadata.', 'nova-elements' ),
			),
			'ne-featured-posts' => array(
				__( 'Wide editorial image above a centered lead and ruled supporting stories.', 'nova-elements' ),
				__( 'Split feature with inset copy and a row of compact thumbnail cards.', 'nova-elements' ),
				__( 'Rounded spotlight with a soft radial fill and stacked support tiles.', 'nova-elements' ),
				__( 'A full-bleed headline cover above smaller supporting covers.', 'nova-elements' ),
				__( 'A framed dark feature beside a compact stack of supporting stories.', 'nova-elements' ),
			),
			'ne-magazine-layout' => array(
				__( 'A serif lead column beside a ruled compact digest.', 'nova-elements' ),
				__( 'An inset lead with a two-column supporting grid and visible categories.', 'nova-elements' ),
				__( 'A horizontal lead spans the page above a tiled supporting section.', 'nova-elements' ),
				__( 'A bold lead cover beside staggered supporting cards.', 'nova-elements' ),
				__( 'A dark two-column spread with the lead on the leading edge and framed supporting headlines.', 'nova-elements' ),
			),
			'ne-post-timeline' => array(
				__( 'A date column and fine spine beside each editorial story.', 'nova-elements' ),
				__( 'Inset thumbnail rows with a framed date rail.', 'nova-elements' ),
				__( 'Rounded horizontal story panels with a date chip above the copy.', 'nova-elements' ),
				__( 'Alternating story covers around a central spine.', 'nova-elements' ),
				__( 'Dark compact entries with a full-width date band.', 'nova-elements' ),
			),
			'ne-related-posts' => array(
				__( 'Photo-led cards with a serif reading hierarchy.', 'nova-elements' ),
				__( 'Compact thumbnail rails with inset article details.', 'nova-elements' ),
				__( 'A split lead recommendation above supporting cards.', 'nova-elements' ),
				__( 'Full-bleed recommendation covers with overlay copy.', 'nova-elements' ),
				__( 'Framed dark rows with paired image and metadata.', 'nova-elements' ),
			),
			'ne-post-categories' => array(
				__( 'A ruled directory with counts aligned at the trailing edge.', 'nova-elements' ),
				__( 'Inset taxonomy cards with a separate count badge.', 'nova-elements' ),
				__( 'Centered category tiles with stacked counts and descriptions.', 'nova-elements' ),
				__( 'An oversized lead category above compact supporting panels.', 'nova-elements' ),
				__( 'Dark framed links with a separate statistics column.', 'nova-elements' ),
			),
			'ne-author-profile' => array(
				__( 'A compact portrait beside serif biography copy.', 'nova-elements' ),
				__( 'A full-height portrait panel paired with a structured biography.', 'nova-elements' ),
				__( 'A centered profile with a portrait medallion.', 'nova-elements' ),
				__( 'An oversized portrait tile with asymmetric biography placement.', 'nova-elements' ),
				__( 'An inset dark profile with an independent archive action.', 'nova-elements' ),
			),
			'ne-advanced-post-meta' => array(
				__( 'A serif byline with compact labels and fine dividers.', 'nova-elements' ),
				__( 'An inset grid of metadata tiles with accent rails.', 'nova-elements' ),
				__( 'Centered metadata groups on a soft gradient panel.', 'nova-elements' ),
				__( 'An oversized lead field above a compact supporting grid.', 'nova-elements' ),
				__( 'A framed dark panel with paired labels and values.', 'nova-elements' ),
			),
			'ne-feature-card' => array(
				__( 'Wide media above a serif heading and fine rules.', 'nova-elements' ),
				__( 'Compact media alongside an accented copy panel.', 'nova-elements' ),
				__( 'Raised media above centered copy and a pill link.', 'nova-elements' ),
				__( 'Large headline and asymmetric media placement.', 'nova-elements' ),
				__( 'Framed copy followed by a wide media strip.', 'nova-elements' ),
			),
			'ne-info-card' => array(
				__( 'Compact information block with a serif heading and a ruled note.', 'nova-elements' ),
				__( 'Leading media panel and a separate bordered note.', 'nova-elements' ),
				__( 'Centered icon or image above a soft, inset note.', 'nova-elements' ),
				__( 'Wide graphic tile paired with oversized, asymmetric copy.', 'nova-elements' ),
				__( 'Dark framed information with a divided note and action.', 'nova-elements' ),
			),
			'ne-service-card' => array(
				__( 'Service introduction above a ruled list of benefits.', 'nova-elements' ),
				__( 'Leading service media and a structured column of benefits.', 'nova-elements' ),
				__( 'A centered service badge above compact benefit tiles.', 'nova-elements' ),
				__( 'Offset service media and an asymmetric list of benefits.', 'nova-elements' ),
				__( 'Dark service card with a separate benefits panel.', 'nova-elements' ),
			),
			'ne-profile-card' => array(
				__( 'A compact circular portrait beside a serif introduction.', 'nova-elements' ),
				__( 'A full-height portrait column beside the role and biography.', 'nova-elements' ),
				__( 'A centered circular portrait and a compact profile action.', 'nova-elements' ),
				__( 'A large portrait tile beside an oversized name.', 'nova-elements' ),
				__( 'A dark framed portrait and biography with a divided action.', 'nova-elements' ),
			),
			'ne-team-member-card' => array(
				__( 'Compact staff byline with a circular portrait and ruled department.', 'nova-elements' ),
				__( 'Staff dossier with a left portrait and a dedicated department field.', 'nova-elements' ),
				__( 'Centered portrait with a role badge and a contact pill.', 'nova-elements' ),
				__( 'Offset portrait with an oversized name and accent band.', 'nova-elements' ),
				__( 'Dark staff panel with a framed portrait and a full-width contact action.', 'nova-elements' ),
			),
			'ne-pricing-card' => array(
				__( 'Type-led price with horizontal rules and a concise feature list.', 'nova-elements' ),
				__( 'Structured quote with a boxed rate and a full-width action.', 'nova-elements' ),
				__( 'Centered rate with a raised price panel and compact feature tiles.', 'nova-elements' ),
				__( 'Oversized rate with offset feature columns and a strong action.', 'nova-elements' ),
				__( 'Dark price panel with an outlined feature list and action.', 'nova-elements' ),
			),
			'ne-image-box' => array(
				__( 'Wide editorial photo with a small caption and a ruled text area.', 'nova-elements' ),
				__( 'Side-by-side photo and copy with a compact project label.', 'nova-elements' ),
				__( 'Framed central photo and caption with a centered action.', 'nova-elements' ),
				__( 'Oversized trailing photo paired with bold copy.', 'nova-elements' ),
				__( 'Dark inset photo and caption with a divided action.', 'nova-elements' ),
			),
			'ne-icon-box' => array(
				__( 'Outlined icon beside an editorial introduction.', 'nova-elements' ),
				__( 'Solid leading icon on a structured information panel.', 'nova-elements' ),
				__( 'Centered round icon with an inset action.', 'nova-elements' ),
				__( 'Oversized icon tile beside asymmetric copy.', 'nova-elements' ),
				__( 'Dark panel with a framed icon badge and a divided action.', 'nova-elements' ),
			),
			'ne-flip-card' => array(
				__( 'Ruled front with a compact reverse panel.', 'nova-elements' ),
				__( 'Leading media beside a split front and reverse.', 'nova-elements' ),
				__( 'Centered front with a raised reverse panel.', 'nova-elements' ),
				__( 'Bold offset front with a full-width reverse panel.', 'nova-elements' ),
				__( 'Dark frame with a contrasting inset reverse panel.', 'nova-elements' ),
			),
			'ne-hover-card' => array(
				__( 'Wide photo and a ruled editorial introduction.', 'nova-elements' ),
				__( 'Leading media alongside compact copy and action.', 'nova-elements' ),
				__( 'Framed image with centered copy and action.', 'nova-elements' ),
				__( 'Oversized trailing image paired with bold copy.', 'nova-elements' ),
				__( 'Dark stacked frame with a divided, full-width action.', 'nova-elements' ),
			),
			'ne-feature-showcase' => array(
				__( 'Panoramic media above editorial copy and a ruled feature list.', 'nova-elements' ),
				__( 'Leading media and a compact introduction above a grid of feature tiles.', 'nova-elements' ),
				__( 'Centered media and copy above a group of raised feature tiles.', 'nova-elements' ),
				__( 'Offset image and bold copy with a wide lead feature tile.', 'nova-elements' ),
				__( 'Inset media and copy beside a dark panel of supporting features.', 'nova-elements' ),
			),
			'ne-advanced-accordion' => array(
				__( 'Ruled editorial disclosures with serif labels and readable text measure.', 'nova-elements' ),
				__( 'Inset header bands and an accent rail on each panel.', 'nova-elements' ),
				__( 'Centered, raised panels on a soft gradient.', 'nova-elements' ),
				__( 'Oversized headings with offset expanded content.', 'nova-elements' ),
				__( 'Dark frame with divided headers and inset content.', 'nova-elements' ),
			),
			'ne-advanced-tabs' => array(
				__( 'Serif navigation with an underline and a ruled panel.', 'nova-elements' ),
				__( 'Leading vertical tab rail on wide layouts and a separate content pane.', 'nova-elements' ),
				__( 'Centered pills above a raised content pane.', 'nova-elements' ),
				__( 'Full-width tab band with an accented panel edge.', 'nova-elements' ),
				__( 'Dark framed tab bar and inset content pane.', 'nova-elements' ),
			),
			'ne-modal-popup' => array(
				__( 'Serif title and ruled description in a centered dialog.', 'nova-elements' ),
				__( 'Inset title band with a structured accent rail.', 'nova-elements' ),
				__( 'Narrow centered dialog with a gradient and a pill action.', 'nova-elements' ),
				__( 'Bold title tile alongside the description on wide screens.', 'nova-elements' ),
				__( 'Dark inset frame and a divided full-width action.', 'nova-elements' ),
			),
			'ne-tooltip' => array(
				__( 'Underlined serif trigger with a ruled note.', 'nova-elements' ),
				__( 'Framed trigger above an inset note.', 'nova-elements' ),
				__( 'Centered pill and elevated bubble.', 'nova-elements' ),
				__( 'Bold trigger and offset accent note.', 'nova-elements' ),
				__( 'Dark trigger above a framed explanation.', 'nova-elements' ),
			),
			'ne-content-toggle' => array(
				__( 'Inline choices above a ruled panel.', 'nova-elements' ),
				__( 'Side rail with a structured content pane.', 'nova-elements' ),
				__( 'Segmented pills above a centered card.', 'nova-elements' ),
				__( 'Wide two-column switch with a bold content edge.', 'nova-elements' ),
				__( 'Dark inset switch and content area.', 'nova-elements' ),
			),
			'ne-interactive-hotspot' => array(
				__( 'Ruled caption underneath the image.', 'nova-elements' ),
				__( 'Framed stage with an accent caption.', 'nova-elements' ),
				__( 'Rounded stage and centered raised caption.', 'nova-elements' ),
				__( 'Oversized stage beside an offset caption.', 'nova-elements' ),
				__( 'Dark image frame with a distinct caption panel.', 'nova-elements' ),
			),
			'ne-image-comparison' => array(
				__( 'Wide comparison with serif labels below the image.', 'nova-elements' ),
				__( 'Framed comparison with a structured label strip.', 'nova-elements' ),
				__( 'Rounded comparison over a gradient surface.', 'nova-elements' ),
				__( 'Panoramic comparison with an accented baseline.', 'nova-elements' ),
				__( 'Dark framed comparison with high-contrast labels.', 'nova-elements' ),
			),
			'ne-off-canvas-content' => array(
				__( 'Ruled editorial drawer with a serif title.', 'nova-elements' ),
				__( 'Structured drawer with an inset heading band.', 'nova-elements' ),
				__( 'Centered narrow drawer on a gradient surface.', 'nova-elements' ),
				__( 'Drawer with a bold title block and accent edge.', 'nova-elements' ),
				__( 'Dark inset drawer with a divided action.', 'nova-elements' ),
			),
			'ne-advanced-button-group' => array(
				__( 'Inline serif links with individual rules.', 'nova-elements' ),
				__( 'Close-set links with divided borders.', 'nova-elements' ),
				__( 'Centered pill links on a tinted tray.', 'nova-elements' ),
				__( 'Lead action with an accent edge and supporting links.', 'nova-elements' ),
				__( 'Framed dark link group with distinct action tiles.', 'nova-elements' ),
			),
			'ne-advanced-faq' => array(
				__( 'Ruled questions with serif typography and a readable answer width.', 'nova-elements' ),
				__( 'Structured question bands with an accent rail.', 'nova-elements' ),
				__( 'Centered, elevated question panels.', 'nova-elements' ),
				__( 'Oversized questions with offset answers.', 'nova-elements' ),
				__( 'Dark question panels with separated answers.', 'nova-elements' ),
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
