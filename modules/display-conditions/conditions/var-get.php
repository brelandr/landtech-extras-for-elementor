<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Var_Base
 *
 * @since  2.2.0
 */
class Var_Get extends Var_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'var_get';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_title() {
		return __( 'GET', 'landtech-extras-for-elementor' );
	}

	/**
	 * Check condition
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 *
	 * @param string  	$name  		The control name to check
	 * @param string 	$operator  	Comparison operator
	 * @param mixed  	$value  	The control value to check
	 */
	public function check( $operator, $value, $name = null ) {
		$show = false;
		$key  = $this->sanitize_request_var_key( $name );

		if ( '' === $key || ! isset( $_GET[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display condition on public requests.
			return $this->compare( $show, true, $operator );
		}

		$raw = map_deep( wp_unslash( $_GET[ $key ] ), 'sanitize_text_field' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display condition on public requests.

		if ( is_array( $raw ) ) {
			// MANUAL REVIEW REQUIRED: GET arrays not compared to target value; blank target only matches "presence".
			if ( '' !== trim( (string) $value ) ) {
				return $this->compare( $show, true, $operator );
			}
			$show = true;
		} else {
			$incoming = (string) $raw;
			$value_in = sanitize_text_field( (string) $value );

			if ( '' === trim( (string) $value ) ) {
				$show = true;
			} elseif ( $value_in === $incoming ) {
				$show = true;
			}
		}

		return $this->compare( $show, true, $operator );
	}
}
