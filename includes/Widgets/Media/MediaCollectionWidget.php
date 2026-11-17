<?php

namespace NovaElements\Widgets\Media;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

abstract class MediaCollectionWidget extends BaseWidget {
	protected const KIND = 'gallery';

	public function get_style_depends(): array {
		return array( 'nova-elements-media-gallery' );
	}

	public function get_script_depends(): array {
		return array( 'nova-elements-media-gallery' );
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => $this->get_title() ) );
		$this->add_control( 'gallery_label', array( 'label' => __( 'Accessible collection label', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => $this->get_title() ) );
		if ( 'popup' === static::KIND ) {
			$this->register_media_fields( $this );
		} else {
			$repeater = new Repeater();
			$this->register_media_fields( $repeater );
			$this->add_control( 'gallery_items', array(
				'label' => __( 'Media items', 'nova-elements' ),
				'description' => __( 'Displays up to twelve configured items in the order shown.', 'nova-elements' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{ media_title }}',
			) );
		}
		if ( $this->allows_images() && 'logos' !== static::KIND ) {
			$this->add_control( 'lightbox', array( 'label' => __( 'Open images in a lightbox', 'nova-elements' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
		}
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'media_style', array( 'label' => __( 'Media and captions', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'image_ratio', array(
			'label' => __( 'Image aspect ratio', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'default' => '',
			'options' => array( '' => __( 'Preset default', 'nova-elements' ), '1' => '1:1', '1.3333' => '4:3', '1.7778' => '16:9', '2.3333' => '21:9' ),
			'selectors' => array( '{{WRAPPER}} .nova-media' => '--ne-media-ratio: {{VALUE}}; --ne-media-ratio-override: {{VALUE}};' ),
		) );
		$this->add_size( 'media_gap', __( 'Gap', 'nova-elements' ), '.nova-media', '--ne-media-gap' );
		$this->add_text_style( 'caption', __( 'Captions', 'nova-elements' ), '.nova-media__caption' );
		$this->add_panel_style( '.nova-media__item' );
		$this->end_controls_section();
	}

	private function register_media_fields( Repeater|self $controls ): void {
		$controls->add_control( 'media_title', array( 'label' => __( 'Title', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$controls->add_control( 'media_caption', array( 'label' => __( 'Caption', 'nova-elements' ), 'type' => Controls_Manager::TEXTAREA, 'dynamic' => array( 'active' => true ) ) );
		$controls->add_control( 'feature_image', array( 'label' => $this->allows_videos() ? __( 'Image or video poster', 'nova-elements' ) : __( 'Image', 'nova-elements' ), 'type' => Controls_Manager::MEDIA, 'dynamic' => array( 'active' => true ) ) );
		if ( $this->allows_videos() ) {
			$controls->add_control( 'video_file', array( 'label' => __( 'Local video', 'nova-elements' ), 'type' => Controls_Manager::MEDIA, 'media_types' => array( 'video' ) ) );
			$controls->add_control( 'video_url', array( 'label' => __( 'YouTube or Vimeo URL', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'description' => __( 'A local video takes precedence. External players load after activation.', 'nova-elements' ) ) );
			$controls->add_control( 'video_captions', array( 'label' => __( 'Local video WebVTT URL', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'description' => __( 'Optional captions for the local video. Use an absolute .vtt URL on this site.', 'nova-elements' ) ) );
			$controls->add_control( 'caption_language', array( 'label' => __( 'Caption language code', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => 'en' ) );
		}
		if ( 'filter' === static::KIND ) {
			$controls->add_control( 'media_category', array( 'label' => __( 'Category', 'nova-elements' ), 'type' => Controls_Manager::TEXT ) );
		}
	}

	private function allows_videos(): bool {
		return in_array( static::KIND, array( 'videos', 'popup', 'showcase' ), true );
	}

	private function allows_images(): bool {
		return ! in_array( static::KIND, array( 'videos', 'popup' ), true );
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$kind = static::KIND;
		$rows = 'popup' === $kind ? array( $settings ) : ( $settings['gallery_items'] ?? array() );
		$items = array();
		$categories = array();
		foreach ( is_array( $rows ) ? $rows : array() as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$media = $row['feature_image'] ?? null;
			$id = is_array( $media ) && is_numeric( $media['id'] ?? null ) ? (int) $media['id'] : 0;
			$attributes = $items && wp_lazy_loading_enabled( 'img', 'wp_get_attachment_image' ) ? array( 'loading' => 'lazy' ) : array();
			$image = $id > 0 ? wp_get_attachment_image( $id, 'large', false, $attributes ) : '';
			if ( '' === $image ) {
				$image = $this->media_image( $media, 'large' );
			}
			$video = $this->allows_videos() ? VideoSource::local( $row['video_file'] ?? null ) ?? VideoSource::external( $row['video_url'] ?? null ) : null;
			if ( ! $video && ( '' === $image || ! $this->allows_images() ) ) {
				continue;
			}
			$title = $this->text_value( $row['media_title'] ?? null );
			$alt = $id > 0 ? $this->text_value( get_post_meta( $id, '_wp_attachment_image_alt', true ) ) : '';
			$caption = $this->text_value( $row['media_caption'] ?? null );
			if ( '' === $caption && $id > 0 ) {
				$caption = $this->text_value( wp_get_attachment_caption( $id ) );
			}
			$category = 'filter' === $kind ? $this->text_value( $row['media_category'] ?? null ) : '';
			if ( '' !== $category && ! in_array( $category, $categories, true ) ) {
				$categories[] = $category;
			}
			$full = $id > 0 ? wp_get_attachment_image_url( $id, 'full' ) : '';
			$language = $this->text_value( $row['caption_language'] ?? null );
			$items[] = array(
				'title' => $title, 'alt' => $alt, 'caption' => $caption, 'category' => $category, 'image' => $image, 'video' => $video,
				'full' => $full ?: esc_url_raw( $this->text_value( is_array( $media ) ? ( $media['url'] ?? null ) : null ) ),
				'track' => 'video' === ( $video['type'] ?? '' ) ? VideoSource::caption_track( $row['video_captions'] ?? null ) : '',
				'language' => preg_match( '/^[a-z]{2,3}(?:-[a-z0-9]{2,8}){0,3}$/iD', $language ) ? $language : 'en',
			);
			if ( 12 === count( $items ) ) {
				break;
			}
		}
		if ( ! $items ) {
			if ( is_user_logged_in() && current_user_can( 'edit_posts' ) && \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Add media to display this widget.', 'nova-elements' ) . '</p>';
			}
			return;
		}
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$label = $this->text_value( $settings['gallery_label'] ?? null ) ?: $this->get_title();
		$lightbox = 'yes' === ( $settings['lightbox'] ?? '' ) && 'logos' !== $kind;
		require dirname( __DIR__, 3 ) . '/templates/widgets/media-collection.php';
	}
}
