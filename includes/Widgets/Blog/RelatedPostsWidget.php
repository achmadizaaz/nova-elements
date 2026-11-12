<?php

namespace NovaElements\Widgets\Blog;

defined( 'ABSPATH' ) || exit;

final class RelatedPostsWidget extends PostCollectionWidget {
	protected const LAYOUT = 'related';
	protected const STYLE = 'blog-context';

	public function get_name(): string {
		return 'ne-related-posts';
	}

	public function get_title(): string {
		return __( 'Related Posts', 'nova-elements' );
	}

	public function get_icon(): string {
		return 'eicon-posts-grid';
	}
}
