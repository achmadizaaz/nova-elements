<?php

namespace NovaElements\Widgets\Blog;

defined( 'ABSPATH' ) || exit;

final class BlogGridWidget extends PostCollectionWidget {
	public function get_name(): string {
		return 'ne-blog-grid';
	}

	public function get_title(): string {
		return __( 'Blog Grid', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-posts-grid';
	}
}
