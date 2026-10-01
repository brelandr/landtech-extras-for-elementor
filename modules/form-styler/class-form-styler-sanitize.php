<?php
/**
 * Sanitize CF7 / WPForms shortcodes down to an id-only tag.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\FormStyler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.10.0
 */
class Form_Styler_Sanitize {

	/**
	 * Keep only [tag id="123"]. Bare numeric IDs are accepted.
	 *
	 * @param mixed  $raw          Shortcode or form ID.
	 * @param string $allowed_tag  contact-form-7 or wpforms.
	 * @return string
	 */
	public static function shortcode( $raw, $allowed_tag ) {
		$allowed_tag = sanitize_key( (string) $allowed_tag );
		if ( ! in_array( $allowed_tag, array( 'contact-form-7', 'wpforms' ), true ) ) {
			return '';
		}

		$raw = is_string( $raw ) ? trim( $raw ) : '';
		if ( '' === $raw ) {
			return '';
		}

		if ( ctype_digit( $raw ) ) {
			return '[' . $allowed_tag . ' id="' . absint( $raw ) . '"]';
		}

		if ( preg_match( '/id\s*=\s*[\'"]?(\d+)[\'"]?/i', $raw, $match ) ) {
			return '[' . $allowed_tag . ' id="' . absint( $match[1] ) . '"]';
		}

		return '';
	}
}
