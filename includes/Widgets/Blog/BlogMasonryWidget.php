<?php

namespace NovaElements\Widgets\Blog;

defined( 'ABSPATH' ) || exit;

final class BlogMasonryWidget extends PostCollectionWidget {
	protected const LAYOUT = 'masonry';

	public function get_name(): string {
		return 'ne-blog-masonry';
	}

	public function get_title(): string {
		return __( 'Blog Masonry', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-gallery-masonry';
	}
}
