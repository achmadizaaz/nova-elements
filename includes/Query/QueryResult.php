<?php

namespace NovaElements\Query;

defined( 'ABSPATH' ) || exit;

final class QueryResult {
	/** @param \WP_Post[] $posts */
	public function __construct(
		public readonly array $posts,
		public readonly int $page,
		public readonly int $total_pages,
		public readonly ?int $total_posts,
		public readonly ?\WP_Error $error = null,
	) {}
}
