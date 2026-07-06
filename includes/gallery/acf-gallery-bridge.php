<?php
/**
 * Normalize ACF Gallery fields into Elementor gallery datasets for LandTech Gallery widgets.
 *
 * Requires Advanced Custom Fields (`get_field()`). Fully functional on WordPress.org when ACF is active.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Gallery;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.2.54
 */
final class ACF_Gallery_Bridge {

	/**
	 * Whether the bridge may run on this site.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return function_exists( 'get_field' );
	}

	/**
	 * @param string $field_name ACF field name (letters, numbers, underscore, hyphen).
	 * @param int    $post_id    Post ID hosting the field (attachment fields unsupported here).
	 * @return array<int, array<string, int|string>> Elementor-compatible gallery rows (`id`, `url`).
	 */
	public static function get_items( $field_name, $post_id ) {

		$field = self::sanitize_field_name( $field_name );
		if ( '' === $field ) {
			return array();
		}

		$pid = absint( $post_id );
		if ( $pid < 1 ) {
			$pid = get_the_ID();
		}
		if ( $pid < 1 ) {
			return array();
		}

		$value = get_field( $field, $pid, false );

		if ( empty( $value ) || ! is_array( $value ) ) {
			return array();
		}

		$out = array();

		foreach ( $value as $row ) {
			$attachment_id = self::row_to_attachment_id( $row );
			if ( $attachment_id < 1 ) {
				continue;
			}

			if ( function_exists( 'wp_attachment_is_image' ) && ! wp_attachment_is_image( $attachment_id ) ) {
				continue;
			}

			$url = wp_get_attachment_image_url( $attachment_id, 'full' );
			if ( ! is_string( $url ) || '' === $url ) {
				$url = '';
			}

			$out[] = array(
				'id'  => $attachment_id,
				'url' => $url,
			);
		}

		return $out;
	}

	/**
	 * @param mixed $row ACF gallery row (ID, array, or object).
	 * @return int
	 */
	protected static function row_to_attachment_id( $row ) {

		if ( is_numeric( $row ) ) {
			return absint( $row );
		}

		if ( is_array( $row ) ) {
			if ( isset( $row['ID'] ) ) {
				return absint( $row['ID'] );
			}
			if ( isset( $row['id'] ) ) {
				return absint( $row['id'] );
			}
		}

		if ( is_object( $row ) ) {
			if ( isset( $row->ID ) ) {
				return absint( $row->ID );
			}
			if ( isset( $row->id ) ) {
				return absint( $row->id );
			}
		}

		return 0;
	}

	/**
	 * @param string $field_name Raw field name.
	 * @return string
	 */
	protected static function sanitize_field_name( $field_name ) {

		$name = strtolower( (string) $field_name );
		$name = preg_replace( '/[^a-z0-9_-]/', '', $name );

		return is_string( $name ) ? $name : '';
	}
}
