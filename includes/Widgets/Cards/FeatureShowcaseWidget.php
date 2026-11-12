<?php

namespace NovaElements\Widgets\Cards;

use Elementor\Controls_Manager;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

final class FeatureShowcaseWidget extends CardWidget {
	protected const KIND = 'showcase';
	public const MAX_FEATURES = 6;

	public function get_name(): string {
		return 'ne-feature-showcase';
	}

	public function get_title(): string {
		return __( 'Feature Showcase', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-featured-image';
	}

	public function get_keywords(): array {
		return array( 'showcase', 'features', 'benefits', 'product' );
	}

	protected function content_defaults(): array {
		return array(
			'section' => __( 'Feature showcase', 'nova-elements' ),
			'eyebrow_label' => __( 'Label', 'nova-elements' ),
			'eyebrow' => __( 'Project workspace', 'nova-elements' ),
			'title' => __( 'Make room for good work', 'nova-elements' ),
			'description' => __( 'Give your team one place to plan projects, share feedback, and hand off the finished work.', 'nova-elements' ),
			'cta_label' => __( 'Explore the workspace', 'nova-elements' ),
			'media' => 'icon',
			'icon' => 'fas fa-th-large',
		);
	}

	protected function register_card_details(): void {
		$repeater = new Repeater();
		$repeater->add_control( 'feature_title', array( 'label' => __( 'Feature title', 'nova-elements' ), 'type' => Controls_Manager::TEXT, 'dynamic' => array( 'active' => true ) ) );
		$repeater->add_control( 'feature_description', array( 'label' => __( 'Feature description', 'nova-elements' ), 'type' => Controls_Manager::TEXTAREA, 'dynamic' => array( 'active' => true ) ) );
		$this->add_control(
			'showcase_features',
			array(
				'label' => __( 'Supporting features', 'nova-elements' ),
				/* translators: %d: maximum number of features displayed. */
				'description' => sprintf( __( 'Displays up to %d features in the order shown.', 'nova-elements' ), self::MAX_FEATURES ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{ feature_title }}',
				'default' => array(
					array( 'feature_title' => __( 'Plan together', 'nova-elements' ), 'feature_description' => __( 'Assign owners and keep the next milestone in view.', 'nova-elements' ) ),
					array( 'feature_title' => __( 'Gather feedback', 'nova-elements' ), 'feature_description' => __( 'Keep decisions and comments beside the work.', 'nova-elements' ) ),
					array( 'feature_title' => __( 'Share the handoff', 'nova-elements' ), 'feature_description' => __( 'Give the next team the context and files they need.', 'nova-elements' ) ),
				),
			)
		);
	}

	protected function card_items( array $settings ): array {
		$rows = $settings['showcase_features'] ?? null;
		if ( ! is_array( $rows ) ) {
			return array();
		}

		$features = array();
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$title = $this->text_value( $row['feature_title'] ?? null );
			$description = $this->text_value( $row['feature_description'] ?? null );
			if ( '' === $title && '' === $description ) {
				continue;
			}
			$features[] = array( 'title' => $title, 'description' => $description );
			if ( self::MAX_FEATURES === count( $features ) ) {
				break;
			}
		}
		return $features;
	}
}
