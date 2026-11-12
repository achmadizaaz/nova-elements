<?php
/**
 * Run on a disposable local database:
 * php tests/wordpress-query-benchmark.php /path/to/wp-load.php --disposable
 */

if ( 'cli' !== PHP_SAPI || '--disposable' !== ( $argv[2] ?? '' ) || ! is_file( $argv[1] ?? '' ) ) {
	exit( 1 );
}
require $argv[1];

use NovaElements\Query\PostQuery;

if ( 'local' !== wp_get_environment_type() || ! class_exists( PostQuery::class ) ) {
	throw new RuntimeException( 'Use a disposable local WordPress site with Nova Elements active.' );
}
$type = 'ne_bench_' . strtolower( wp_generate_password( 8, false ) );
register_post_type( $type, array( 'public' => true ) );
$ids = array();
$engine = new PostQuery();
$settings = array( 'post_type' => $type, 'posts_per_page' => 12, 'pagination' => 'yes' );

try {
	foreach ( array( 100, 1000, 10000 ) as $size ) {
		while ( count( $ids ) < $size ) {
			$id = wp_insert_post( array(
				'post_title' => 'Benchmark post ' . count( $ids ),
				'post_type' => $type,
				'post_status' => 'publish',
				'post_date' => '2024-01-01 12:00:00',
			), true, false );
			if ( is_wp_error( $id ) || ! $id ) {
				throw new RuntimeException( 'Could not seed the benchmark dataset.' );
			}
			$ids[] = $id;
		}
		wp_cache_flush();
		foreach ( array( 'cold', 'warm' ) as $cache ) {
			$before = $wpdb->num_queries;
			$memory = memory_get_usage();
			memory_reset_peak_usage();
			$start = hrtime( true );
			$result = $engine->run( $settings, 2 );
			$elapsed = ( hrtime( true ) - $start ) / 1e6;
			$peak = memory_get_peak_usage() - $memory;
			if ( $size !== $result->total_posts || 12 !== count( $result->posts ) ) {
				throw new RuntimeException( 'Unexpected benchmark results.' );
			}
			printf( "%d posts, %s: %.2f ms, %d SQL queries, %.1f KiB peak PHP memory increase\n", $size, $cache, $elapsed, $wpdb->num_queries - $before, $peak / 1024 );
			unset( $result );
		}
	}
} finally {
	foreach ( $ids as $id ) {
		wp_delete_post( $id, true );
	}
	unregister_post_type( $type );
}
