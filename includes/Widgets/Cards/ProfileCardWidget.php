<?php

namespace NovaElements\Widgets\Cards;

use Elementor\Controls_Manager;
use NovaElements\Widgets\Blog\PostContext;

defined( 'ABSPATH' ) || exit;

final class ProfileCardWidget extends CardWidget {
	protected const KIND = 'profile';

	public function get_name(): string {
		return 'ne-profile-card';
	}

	public function get_title(): string {
		return __( 'Profile Card', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-person';
	}

	public function get_keywords(): array {
		return array( 'profile', 'team', 'bio' );
	}

	protected function content_defaults(): array {
		return array(
			'section' => __( 'Profile', 'nova-elements' ),
			'eyebrow_label' => __( 'Role', 'nova-elements' ),
			'eyebrow' => '',
			'title' => __( 'Alex Morgan', 'nova-elements' ),
			'description' => __( 'Alex designs clear, thoughtful digital experiences for teams doing meaningful work.', 'nova-elements' ),
			'cta_label' => __( 'View profile', 'nova-elements' ),
			'media' => 'image',
			'icon' => 'fas fa-user',
		);
	}

	protected function register_card_details(): void {
		$this->add_control( 'card_note', array( 'label' => __( 'Profile detail', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'default' => '', 'dynamic' => array( 'active' => true ) ) );
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function register_card_source(): void {
		$this->add_control( 'profile_source', array(
			'label' => __( 'Profile source', 'nova-elements' ),
			'type' => Controls_Manager::SELECT,
			'options' => array( 'manual' => __( 'Manual content', 'nova-elements' ), 'post' => __( 'Public post', 'nova-elements' ) ),
			'default' => 'manual',
		) );
		$this->add_control( 'profile_post_id', array(
			'label' => __( 'Reference post ID', 'nova-elements' ),
			'type' => Controls_Manager::NUMBER,
			'min' => 1,
			'description' => __( 'Leave blank to use the current public post. Its title, excerpt, featured image and permalink supply the profile.', 'nova-elements' ),
			'condition' => array( 'profile_source' => 'post' ),
		) );
	}

	protected function manual_content_condition(): array {
		return array( 'profile_source!' => 'post' );
	}

	protected function resolve_card_settings( array $settings ): array {
		if ( 'post' !== ( $settings['profile_source'] ?? '' ) ) {
			return $settings;
		}
		$post = PostContext::public_post( $settings['profile_post_id'] ?? '' );
		if ( ! $post ) {
			return array();
		}
		$settings['title'] = get_the_title( $post );
		$settings['description'] = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post ) ), 40 );
		$settings['feature_image'] = array( 'id' => get_post_thumbnail_id( $post ) );
		$settings['link'] = array( 'url' => get_permalink( $post ) );
		return $settings;
	}
}
