<?php

namespace NovaElements\Widgets\Media;

defined( 'ABSPATH' ) || exit;

final class VideoSource {
	public static function external( mixed $value ): ?array {
		if ( ! is_string( $value ) || ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return null;
		}
		$url = wp_parse_url( $value );
		if ( 'https' !== ( $url['scheme'] ?? '' ) || isset( $url['user'] ) || isset( $url['port'] ) ) {
			return null;
		}
		$host = strtolower( $url['host'] ?? '' );
		$path = trim( $url['path'] ?? '', '/' );
		if ( in_array( $host, array( 'youtube.com', 'www.youtube.com', 'youtu.be', 'www.youtube-nocookie.com' ), true ) ) {
			parse_str( $url['query'] ?? '', $query );
			$id = 'youtu.be' === $host ? $path : ( 'watch' === $path ? ( $query['v'] ?? '' ) : preg_replace( '#^(?:embed|shorts)/#', '', $path ) );
			if ( is_string( $id ) && preg_match( '/^[a-zA-Z0-9_-]{11}$/D', $id ) ) {
				return array( 'url' => 'https://www.youtube-nocookie.com/embed/' . $id . '?autoplay=1', 'link' => 'https://www.youtube.com/watch?v=' . $id, 'type' => 'embed' );
			}
		}
		if ( in_array( $host, array( 'vimeo.com', 'www.vimeo.com', 'player.vimeo.com' ), true ) && preg_match( '#^(?:video/)?([0-9]{1,12})$#D', $path, $match ) ) {
			return array( 'url' => 'https://player.vimeo.com/video/' . $match[1] . '?dnt=1&autoplay=1', 'link' => 'https://vimeo.com/' . $match[1], 'type' => 'embed' );
		}
		return null;
	}

	public static function local( mixed $media ): ?array {
		$id = is_array( $media ) && is_numeric( $media['id'] ?? null ) ? (int) $media['id'] : 0;
		if ( $id < 1 || ! wp_attachment_is( 'video', $id ) ) {
			return null;
		}
		$url = wp_get_attachment_url( $id );
		return $url ? array( 'url' => $url, 'link' => $url, 'type' => 'video' ) : null;
	}

	public static function caption_track( mixed $value ): string {
		if ( ! is_string( $value ) || ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return '';
		}
		$url = wp_parse_url( $value );
		$site = wp_parse_url( home_url() );
		foreach ( array( 'scheme', 'host', 'port' ) as $part ) {
			if ( ( $url[ $part ] ?? null ) !== ( $site[ $part ] ?? null ) ) {
				return '';
			}
		}
		return ! isset( $url['user'] ) && preg_match( '/\.vtt$/iD', $url['path'] ?? '' ) ? esc_url_raw( $value ) : '';
	}
}
