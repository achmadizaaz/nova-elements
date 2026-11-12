<?php

namespace NovaElements\Widgets\Blog;

defined( 'ABSPATH' ) || exit;

final class BlogListWidget extends PostCollectionWidget {
	protected const LAYOUT = 'list';

	public function get_name(): string {
		return 'ne-blog-list';
	}

	public function get_title(): string {
		return __( 'Blog List', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-post-list';
	}
}
