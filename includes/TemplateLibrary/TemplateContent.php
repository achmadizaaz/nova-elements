<?php

namespace NovaElements\TemplateLibrary;

use NovaElements\Core\StylePresetRegistry;
use NovaElements\Query\PostQuery;
use NovaElements\Widgets\Media\VideoSource;

defined( 'ABSPATH' ) || exit;

final class TemplateContent {
	private const BLOG_SETTINGS = 'style_preset heading_tag post_type posts_per_page orderby order columns columns_tablet columns_mobile image image_size image_ratio excerpt excerpt_words date author term pagination pagination_label query_source related_taxonomy parent_id include_ids exclude_ids include_authors exclude_authors exclude_current sticky_posts taxonomy term_ids taxonomy_filters taxonomy_relation query_meta_key query_meta_operator query_meta_format query_meta_value query_meta_end query_sort_key pagination_mode date_period date_start date_end offset';

	private const CARD_SETTINGS = 'style_preset eyebrow title heading_tag description media_type feature_image feature_icon cta_label link alignment panel_background_background panel_background_color';
	private const GALLERY_SETTINGS = 'style_preset gallery_label gallery_items lightbox image_ratio media_gap media_gap_tablet media_gap_mobile';

	private const SETTINGS = array(
		'container' => 'content_width boxed_width flex_direction flex_direction_tablet flex_direction_mobile flex_wrap flex_justify_content flex_align_items flex_gap padding padding_tablet padding_mobile background_background background_color border_border border_width border_color border_radius width width_tablet width_mobile',
		'ne-advanced-heading' => 'style_preset title subtitle heading_tag alignment alignment_mobile color_mode title_color subtitle_color title_typography_typography title_typography_font_size title_typography_font_size_mobile title_typography_font_weight panel_padding panel_padding_mobile',
		'ne-advanced-button' => 'style_preset label detail link icon_type icon_position alignment alignment_mobile button_background_background button_background_color button_label_color',
		'ne-feature-card' => self::CARD_SETTINGS,
		'ne-info-card' => self::CARD_SETTINGS . ' media_position card_note',
		'ne-service-card' => self::CARD_SETTINGS . ' media_position service_points',
		'ne-profile-card' => self::CARD_SETTINGS . ' media_position card_note profile_source profile_post_id',
		'ne-team-member-card' => self::CARD_SETTINGS . ' media_position card_note',
		'ne-pricing-card' => self::CARD_SETTINGS . ' media_position price_text price_previous price_period service_points',
		'ne-image-box' => self::CARD_SETTINGS . ' media_position image_caption',
		'ne-icon-box' => self::CARD_SETTINGS . ' media_position',
		'ne-flip-card' => self::CARD_SETTINGS . ' media_position flip_label back_title back_description',
		'ne-hover-card' => self::CARD_SETTINGS . ' media_position hover_effect',
		'ne-feature-showcase' => self::CARD_SETTINGS . ' media_position showcase_features',
		'ne-advanced-accordion' => 'style_preset panel_items initial_panel allow_multiple panel_gap panel_gap_tablet panel_gap_mobile',
		'ne-advanced-tabs' => 'style_preset panel_items initial_panel tabs_label panel_gap panel_gap_tablet panel_gap_mobile',
		'ne-modal-popup' => 'style_preset trigger_label title heading_tag description cta_label link panel_background_background panel_background_color',
		'ne-tooltip' => 'style_preset trigger_label tooltip_text',
		'ne-content-toggle' => 'style_preset panel_items initial_panel tabs_label panel_gap',
		'ne-interactive-hotspot' => 'style_preset feature_image markers',
		'ne-image-comparison' => 'style_preset before_image after_image before_label after_label range_label initial_position',
		'ne-off-canvas-content' => 'style_preset trigger_label title heading_tag description cta_label link drawer_side panel_background_background panel_background_color',
		'ne-advanced-button-group' => 'style_preset group_label group_items group_gap group_gap_tablet group_gap_mobile',
		'ne-advanced-faq' => 'style_preset panel_items initial_panel allow_multiple panel_gap panel_gap_tablet panel_gap_mobile',
		'ne-image-gallery' => self::GALLERY_SETTINGS,
		'ne-image-carousel' => self::GALLERY_SETTINGS,
		'ne-video-gallery' => self::GALLERY_SETTINGS,
		'ne-video-popup' => 'style_preset gallery_label feature_image media_title media_caption video_url image_ratio',
		'ne-before-after-slider' => 'style_preset before_image after_image before_label after_label range_label initial_position',
		'ne-image-hover-effects' => self::GALLERY_SETTINGS,
		'ne-logo-carousel' => self::GALLERY_SETTINGS,
		'ne-filterable-gallery' => self::GALLERY_SETTINGS,
		'ne-image-accordion' => self::GALLERY_SETTINGS,
		'ne-media-showcase' => self::GALLERY_SETTINGS,
		'ne-blog-grid' => self::BLOG_SETTINGS,
		'ne-blog-list' => self::BLOG_SETTINGS,
		'ne-blog-masonry' => self::BLOG_SETTINGS,
		'ne-blog-carousel' => self::BLOG_SETTINGS . ' carousel_label carousel_arrows carousel_dots carousel_autoplay carousel_interval',
		'ne-featured-posts' => self::BLOG_SETTINGS . ' featured_support_excerpt',
		'ne-magazine-layout' => self::BLOG_SETTINGS . ' magazine_support_images',
		'ne-post-timeline' => self::BLOG_SETTINGS . ' timeline_date_format',
		'ne-related-posts' => self::BLOG_SETTINGS . ' related_context_id',
		'ne-post-categories' => 'style_preset taxonomy_source terms_taxonomy terms_limit terms_order hide_empty show_count show_description terms_label columns columns_tablet columns_mobile terms_gap terms_gap_tablet terms_gap_mobile',
		'ne-author-profile' => 'style_preset heading_tag profile_source profile_author_id profile_post_type show_bio show_archive show_avatar show_website profile_label avatar_size avatar_size_tablet avatar_size_mobile profile_gap profile_gap_tablet profile_gap_mobile',
		'ne-advanced-post-meta' => 'style_preset meta_reference_id meta_date_format meta_items meta_gap meta_gap_tablet meta_gap_mobile meta_term_color meta_text_color panel_background_background panel_background_color',
		'ne-testimonials' => 'style_preset items layout carousel_label grid_count grid_count_tablet grid_count_mobile carousel_count carousel_count_tablet carousel_count_mobile',
	);

	/** Validate an Elementor export before processing any assets or saving a document. */
	public static function valid( array $document, array $manifest ): bool {
		$type = 'page' === $manifest['type'] ? 'page' : 'container';
		$elements = $document['content'] ?? null;
		if ( ( $document['version'] ?? null ) !== '0.4' || ( $document['type'] ?? null ) !== $type
			|| ( $document['title'] ?? null ) !== $manifest['name'] || array() !== ( $document['page_settings'] ?? null )
			|| array_diff( array_keys( $document ), array( 'version', 'type', 'title', 'page_settings', 'content' ) )
			|| ! is_array( $elements ) || ! $elements || ! array_is_list( $elements ) ) {
			return false;
		}

		$seen = array();
		$assets = array();
		foreach ( $manifest['assets'] ?? array() as $asset ) {
			$control = $asset['control'] ?? 'feature_image';
			if ( ! is_string( $control ) || isset( $assets[ $asset['element'] ][ $control ] ) ) {
				return false;
			}
			$assets[ $asset['element'] ][ $control ] = true;
		}
		if ( ! self::valid_elements( $elements, $seen, $assets, 0 ) || $assets ) {
			return false;
		}
		$used = array_keys( self::widget_counts( $elements ) );
		return $used && ! array_diff( $used, $manifest['required_widgets'] ) && ! array_diff( $manifest['required_widgets'], $used );
	}

	public static function widget_counts( array $elements ): array {
		$widgets = array();
		foreach ( $elements as $element ) {
			if ( 'widget' === $element['elType'] ) {
				$name = $element['widgetType'];
				$widgets[ $name ] = ( $widgets[ $name ] ?? 0 ) + 1;
			}
			foreach ( self::widget_counts( $element['elements'] ?? array() ) as $name => $count ) {
				$widgets[ $name ] = ( $widgets[ $name ] ?? 0 ) + $count;
			}
		}
		ksort( $widgets );
		return $widgets;
	}

	public static function bind_media( array &$elements, array $bindings ): void {
		foreach ( $elements as &$element ) {
			foreach ( $bindings[ $element['id'] ] ?? array() as $field => $media ) {
				if ( preg_match( '/^gallery_image_([1-9]|1[0-2])$/D', $field, $match ) ) {
					$element['settings']['gallery_items'][ (int) $match[1] - 1 ]['feature_image'] = $media;
				} else {
					$element['settings'][ $field ] = $media;
				}
			}
			self::bind_media( $element['elements'], $bindings );
		}
	}

	private static function valid_elements( array $elements, array &$seen, array &$assets, int $depth ): bool {
		if ( $depth > 8 || ! array_is_list( $elements ) ) {
			return false;
		}
		foreach ( $elements as $element ) {
			$id = $element['id'] ?? null;
			$type = $element['elType'] ?? null;
			$children = $element['elements'] ?? null;
			$settings = $element['settings'] ?? null;
			if ( ! is_array( $element ) || ! is_string( $id ) || ! preg_match( '/^[a-f0-9]{7,8}$/D', $id )
				|| isset( $seen[ $id ] ) || count( $seen ) >= 150 || ! is_array( $children ) || ! is_array( $settings )
				|| ! in_array( $type, array( 'container', 'widget' ), true ) || ( 0 === $depth && 'container' !== $type )
				|| array_diff( array_keys( $element ), array( 'id', 'elType', 'widgetType', 'settings', 'elements', 'isInner' ) )
				|| ( isset( $element['isInner'] ) && ! is_bool( $element['isInner'] ) ) ) {
				return false;
			}
			$seen[ $id ] = true;
			$name = 'container' === $type ? 'container' : ( $element['widgetType'] ?? null );
			if ( ! is_string( $name ) || ! isset( self::SETTINGS[ $name ] ) || ( 'widget' === $type && ( $children || 'container' === $name ) )
				|| ! self::valid_settings( $settings, $name, array_keys( $assets[ $id ] ?? array() ) ) ) {
				return false;
			}
			unset( $assets[ $id ] );
			if ( ! self::valid_elements( $children, $seen, $assets, $depth + 1 ) ) {
				return false;
			}
		}
		return true;
	}

	private static function valid_settings( array $settings, string $widget, array $asset_fields ): bool {
		$allowed = explode( ' ', self::SETTINGS[ $widget ] );
		$is_card = in_array( 'feature_image', $allowed, true );
		$profile_post = 'ne-profile-card' === $widget && 'post' === ( $settings['profile_source'] ?? '' );
		$card_image = $is_card && 'image' === ( $settings['media_type'] ?? '' ) && ! $profile_post;
		$allowed_assets = match ( $widget ) {
			'ne-image-comparison', 'ne-before-after-slider' => array( 'before_image', 'after_image' ),
			'ne-interactive-hotspot', 'ne-video-popup' => array( 'feature_image' ),
			default => $card_image ? array( 'feature_image' ) : array(),
		};
		if ( in_array( 'gallery_items', $allowed, true ) && is_array( $settings['gallery_items'] ?? null ) ) {
			for ( $index = 1; $index <= min( 12, count( $settings['gallery_items'] ) ); ++$index ) {
				$allowed_assets[] = 'gallery_image_' . $index;
			}
		}
		$optional_image = ! in_array( $widget, array( 'ne-feature-card', 'ne-info-card', 'ne-service-card' ), true );
		if ( array_diff( $asset_fields, $allowed_assets ) || ( ! $asset_fields && $card_image && ! $optional_image ) ) {
			return false;
		}
		foreach ( $settings as $key => $value ) {
			if ( '__globals__' === $key ) {
				if ( ! is_array( $value ) ) {
					return false;
				}
				foreach ( $value as $control => $reference ) {
					if ( ! in_array( $control, $allowed, true ) || ! is_string( $reference )
						|| ! preg_match( '#^globals/(?:colors|typography)\?id=(?:primary|secondary|text|accent)$#D', $reference ) ) {
						return false;
					}
				}
			} elseif ( ! in_array( $key, $allowed, true ) ) {
				return false;
			} elseif ( 'style_preset' === $key ) {
				if ( ! is_string( $value ) || ( 'site' !== $value && ! isset( StylePresetRegistry::for_widget( $widget )[ $value ] ) ) ) {
					return false;
				}
			} elseif ( 'taxonomy_filters' === $key ) {
				if ( ! self::valid_rows( $value, array( '_id', 'filter_taxonomy', 'filter_operator', 'filter_current_term', 'filter_terms', 'filter_children' ), 5 ) ) {
					return false;
				}
			} elseif ( 'meta_items' === $key ) {
				if ( ! self::valid_rows( $value, array( '_id', 'meta_field', 'meta_label', 'meta_taxonomy', 'meta_key', 'meta_link' ), 8 ) ) {
					return false;
				}
			} elseif ( 'showcase_features' === $key ) {
				if ( ! self::valid_rows( $value, array( '_id', 'feature_title', 'feature_description' ), 6 ) ) {
					return false;
				}
			} elseif ( 'panel_items' === $key ) {
				if ( ! self::valid_rows( $value, array( '_id', 'panel_label', 'panel_content' ), 'ne-content-toggle' === $widget ? 2 : 8 ) ) {
					return false;
				}
			} elseif ( 'markers' === $key ) {
				if ( ! self::valid_rows( $value, array( '_id', 'marker_label', 'marker_text', 'marker_x', 'marker_y' ), 6, array( 'marker_x', 'marker_y' ) ) ) {
					return false;
				}
			} elseif ( 'group_items' === $key ) {
				if ( ! self::valid_rows( $value, array( '_id', 'button_label', 'button_link' ), 6, array(), 'button_link' ) ) {
					return false;
				}
			} elseif ( 'gallery_items' === $key ) {
				if ( ! self::valid_gallery_rows( $value, $widget ) ) {
					return false;
				}
			} elseif ( 'video_url' === $key ) {
				if ( '' !== $value && ! VideoSource::external( $value ) ) {
					return false;
				}
			} elseif ( 'query_meta_operator' === $key ) {
				if ( ! in_array( $value, PostQuery::META_OPERATORS, true ) ) {
					return false;
				}
			} elseif ( 'link' === $key ) {
				if ( ! self::valid_local_link( $value ) ) {
					return false;
				}
			} elseif ( in_array( $key, array( 'feature_image', 'before_image', 'after_image' ), true ) ) {
				if ( ! self::empty_image( $value ) ) {
					return false;
				}
			} elseif ( ! self::safe_value( $value, 0 ) ) {
				return false;
			}
		}
		return true;
	}

	private static function empty_image( mixed $value ): bool {
		return is_array( $value ) && 2 === count( $value ) && in_array( $value['id'] ?? null, array( 0, '' ), true ) && '' === ( $value['url'] ?? null );
	}

	private static function valid_gallery_rows( mixed $rows, string $widget ): bool {
		if ( ! is_array( $rows ) || ! array_is_list( $rows ) || count( $rows ) > 12 ) {
			return false;
		}
		$keys = array( '_id', 'media_title', 'media_caption', 'feature_image' );
		if ( in_array( $widget, array( 'ne-video-gallery', 'ne-media-showcase' ), true ) ) {
			$keys[] = 'video_url';
		}
		if ( 'ne-filterable-gallery' === $widget ) {
			$keys[] = 'media_category';
		}
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || array_diff( array_keys( $row ), $keys ) ) {
				return false;
			}
			foreach ( $row as $key => $value ) {
				$valid = match ( $key ) {
					'feature_image' => self::empty_image( $value ),
					'video_url' => '' === $value || null !== VideoSource::external( $value ),
					default => is_string( $value ) && self::safe_value( $value, 0 ),
				};
				if ( ! $valid ) {
					return false;
				}
			}
		}
		return true;
	}

	private static function valid_local_link( mixed $value ): bool {
		$url = is_array( $value ) ? ( $value['url'] ?? null ) : null;
		return is_string( $url ) && array_keys( $value ) === array( 'url' )
			&& preg_match( '#^(?:/[a-zA-Z0-9_/-]*|\#[a-zA-Z0-9_-]+|)$#D', $url ) && ! str_contains( $url, '//' );
	}

	private static function valid_rows( mixed $rows, array $keys, int $limit, array $percentages = array(), string $link_key = '' ): bool {
		if ( ! is_array( $rows ) || ! array_is_list( $rows ) || count( $rows ) > $limit ) {
			return false;
		}
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || array_diff( array_keys( $row ), $keys ) ) {
				return false;
			}
			foreach ( $row as $key => $field ) {
				if ( $link_key === $key ) {
					if ( ! self::valid_local_link( $field ) ) {
						return false;
					}
					continue;
				}
				if ( in_array( $key, $percentages, true ) ) {
					if ( ! is_numeric( $field ) || $field < 0 || $field > 100 ) {
						return false;
					}
					continue;
				}
				if ( ! is_string( $field ) || ! self::safe_value( $field, 0 ) ) {
					return false;
				}
			}
		}
		return true;
	}

	private static function safe_value( mixed $value, int $depth ): bool {
		if ( is_array( $value ) ) {
			if ( $depth >= 4 || count( $value ) > 30 ) {
				return false;
			}
			foreach ( $value as $key => $item ) {
				if ( is_string( $key ) && ! preg_match( '/^(?:unit|size|sizes|top|right|bottom|left|row|column|isLinked|value|library|_id|quote|name|role|rating)$/D', $key ) ) {
					return false;
				}
				if ( ! self::safe_value( $item, $depth + 1 ) ) {
					return false;
				}
			}
			return true;
		}
		return is_int( $value ) || is_float( $value ) || is_bool( $value )
			|| ( is_string( $value ) && strlen( $value ) <= 1200 && ! str_contains( $value, chr( 92 ) )
				&& ! preg_match( '#[<>;{}]|(?:javascript|data|https?|file):|//|url\s*\(#i', $value ) );
	}
}
