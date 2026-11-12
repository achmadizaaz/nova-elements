<?php

namespace NovaElements\Widgets\Blog;

use Elementor\Controls_Manager;
use NovaElements\Query\PostQuery;
use NovaElements\Widgets\Base\BaseWidget;

defined( 'ABSPATH' ) || exit;

final class AuthorProfileWidget extends BaseWidget {
	public function get_name(): string {
		return 'ne-author-profile';
	}

	public function get_title(): string {
		return __( 'Author Profile', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-person';
	}

	public function get_keywords(): array {
		return array( 'author', 'profile', 'byline', 'biography' );
	}

	public function get_style_depends(): array {
		return array( 'nova-elements-blog-context' );
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function register_controls(): void {
		$this->start_controls_section( 'profile', array( 'label' => __( 'Author', 'nova-elements' ) ) );
		$this->add_control( 'profile_source', array(
			'label' => __( 'Source', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'options' => array( 'current' => __( 'Current article or author archive', 'nova-elements' ), 'manual' => __( 'Author ID', 'nova-elements' ) ),
			'default' => 'current',
		) );
		$this->add_control( 'profile_author_id', array( 'label' => __( 'Author ID', 'nova-elements' ), 'type' => Controls_Manager::NUMBER, 'min' => 1, 'condition' => array( 'profile_source' => 'manual' ) ) );
		$this->add_control( 'profile_post_type', array(
			'label' => __( 'Author post type', 'nova-elements' ),
			'description' => __( 'A manually selected author must have a public article of this type.', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'options' => PostQuery::post_types(),
			'default' => 'post',
			'condition' => array( 'profile_source' => 'manual' ),
		) );
		$this->add_heading_tag_control();
		$this->add_control( 'profile_label', array( 'label' => __( 'Eyebrow', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => __( 'About the author', 'nova-elements' ) ) );
		foreach ( array(
			'show_bio' => __( 'Show public biography', 'nova-elements' ),
			'show_archive' => __( 'Link to author archive', 'nova-elements' ),
			'show_website' => __( 'Link to profile website', 'nova-elements' ),
			'show_avatar' => __( 'Show WordPress avatar', 'nova-elements' ),
		) as $key => $label ) {
			$args = array( 'label' => $label, 'type' => Controls_Manager::SWITCHER, 'default' => in_array( $key, array( 'show_bio', 'show_archive' ), true ) ? 'yes' : '' );
			if ( 'show_avatar' === $key ) {
				$args['description'] = __( 'May load an image from the site avatar provider. Initials are shown when disabled or unavailable.', 'nova-elements' );
			}
			$this->add_control( $key, $args );
		}
		$this->end_controls_section();

		$this->register_visual_presets();
		$this->start_controls_section( 'profile_style', array( 'label' => __( 'Profile card', 'nova-elements' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_text_style( 'author_name', __( 'Name', 'nova-elements' ), '.nova-author__name' );
		$this->add_text_style( 'author_bio', __( 'Biography', 'nova-elements' ), '.nova-author__bio' );
		$this->add_text_style( 'author_action', __( 'Links', 'nova-elements' ), '.nova-author__actions a' );
		$this->add_panel_style( '.nova-author .nova-author__panel' );
		$this->add_size( 'avatar_size', __( 'Portrait size', 'nova-elements' ), '.nova-author', '--ne-author-portrait', 240 );
		$this->add_size( 'profile_gap', __( 'Gap', 'nova-elements' ), '.nova-author', '--ne-author-gap' );
		$this->end_controls_section();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$preset = $this->resolve_preset( $settings['style_preset'] ?? null );
		$tag = $this->heading_tag( $settings['heading_tag'] ?? null );
		$user = $this->public_author( $settings );
		$profile = array();
		if ( $user ) {
			$name = $this->text_value( $user->display_name ) ?: __( 'Author', 'nova-elements' );
			preg_match_all( '/(?:^|\s)(\p{L}|\p{N})/u', $name, $letters );
			$profile = array(
				'name' => $name,
				'initials' => implode( '', array_slice( $letters[1] ?? array(), 0, 2 ) ) ?: '·',
				'eyebrow' => $this->text_value( $settings['profile_label'] ?? __( 'About the author', 'nova-elements' ) ),
				'bio' => 'yes' === ( $settings['show_bio'] ?? 'yes' ) ? wp_trim_words( wp_strip_all_tags( get_the_author_meta( 'description', $user->ID ) ), 100 ) : '',
				'avatar' => 'yes' === ( $settings['show_avatar'] ?? '' ) && get_option( 'show_avatars' ) ? esc_url( get_avatar_url( $user->ID, array( 'size' => 256 ) ) ) : '',
				'archive' => 'yes' === ( $settings['show_archive'] ?? 'yes' ) ? esc_url( get_author_posts_url( $user->ID ) ) : '',
				'website' => 'yes' === ( $settings['show_website'] ?? '' ) ? esc_url( $user->user_url, array( 'http', 'https' ) ) : '',
			);
		}
		require dirname( __DIR__, 3 ) . '/templates/widgets/author-profile.php';
	}

	private function public_author( array $settings ): ?\WP_User {
		$context = get_queried_object();
		$type = 'post';
		if ( 'manual' === ( $settings['profile_source'] ?? '' ) ) {
			$value = $settings['profile_author_id'] ?? '';
			$id = is_bool( $value ) ? false : filter_var( $value, FILTER_VALIDATE_INT, array( 'options' => array( 'min_range' => 1 ) ) );
			$type = $settings['profile_post_type'] ?? 'post';
		} elseif ( is_singular() && $context instanceof \WP_Post ) {
			if ( 'publish' !== $context->post_status || '' !== $context->post_password || ! is_post_publicly_viewable( $context ) ) {
				return null;
			}
			return get_userdata( (int) $context->post_author ) ?: null;
		} else {
			$id = is_author() && $context instanceof \WP_User ? $context->ID : 0;
		}

		if ( ! $id || ! is_string( $type ) || ! isset( PostQuery::post_types()[ $type ] ) ) {
			return null;
		}
		$result = ( new PostQuery() )->run( array( 'post_type' => $type, 'include_authors' => (string) $id, 'posts_per_page' => 1 ) );
		return $result->posts ? ( get_userdata( $id ) ?: null ) : null;
	}
}
