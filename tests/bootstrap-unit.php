<?php
/**
 * Standalone unit-test stubs for WordPress helpers used by parser classes.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WordPress core constant stub for standalone unit tests.
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	/**
	 * @param mixed $data Data.
	 * @return string|false
	 */
	function wp_json_encode( $data ) {
		return json_encode( $data ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- test stub only.
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	/**
	 * @param string $str Raw.
	 * @return string
	 */
	function sanitize_text_field( $str ) {
		return trim( wp_strip_all_tags( (string) $str ) );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	/**
	 * @param string $str Raw.
	 * @return string
	 */
	function wp_strip_all_tags( $str ) {
		$str = preg_replace( '/<[^>]*>/', '', (string) $str );
		return trim( html_entity_decode( $str, ENT_QUOTES, 'UTF-8' ) );
	}
}

if ( ! function_exists( 'absint' ) ) {
	/**
	 * @param mixed $maybeint Value.
	 * @return int
	 */
	function absint( $maybeint ) {
		return abs( (int) $maybeint );
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	/**
	 * @param string $key Raw key.
	 * @return string
	 */
	function sanitize_key( $key ) {
		$key = strtolower( (string) $key );
		return preg_replace( '/[^a-z0-9_\-]/', '', $key );
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	/**
	 * @param string $data HTML.
	 * @return string
	 */
	function wp_kses_post( $data ) {
		return preg_replace( '/<(?!\/?(p|a|strong|em|b|i|br)\b)[^>]*>/i', '', (string) $data );
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	/**
	 * @param string $url URL.
	 * @return string
	 */
	function esc_url_raw( $url ) {
		return filter_var( (string) $url, FILTER_SANITIZE_URL );
	}
}
