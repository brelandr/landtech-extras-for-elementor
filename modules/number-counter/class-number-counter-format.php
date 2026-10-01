<?php
/**
 * Format a counter value for display.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\NumberCounter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.10.0
 */
class Number_Counter_Format {

	/**
	 * @param mixed  $value     Target number.
	 * @param int    $decimals  0–4.
	 * @param string $delimiter none|comma|period.
	 * @return string
	 */
	public static function format( $value, $decimals, $delimiter ) {
		$value     = (float) $value;
		$decimals  = max( 0, min( 4, (int) $decimals ) );
		$delimiter = sanitize_key( (string) $delimiter );
		$raw       = number_format( $value, $decimals, '.', '' );
		$parts     = explode( '.', $raw );
		$int       = $parts[0];
		$frac      = isset( $parts[1] ) ? $parts[1] : '';

		if ( 'comma' === $delimiter ) {
			$int = preg_replace( '/\B(?=(\d{3})+(?!\d))/', ',', $int );
		} elseif ( 'period' === $delimiter ) {
			$int = preg_replace( '/\B(?=(\d{3})+(?!\d))/', '.', $int );
		}

		return ( '' !== $frac ) ? $int . '.' . $frac : $int;
	}
}
