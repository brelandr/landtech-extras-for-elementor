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
class Var_Post extends Var_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'var_post';
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
		return __( 'POST', 'landtech-extras-for-elementor' );
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
		if ( '' === $key || ! isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Read-only display condition on public/front requests.
			return $this->compare( $show, true, $operator );
		}
		$raw      = map_deep( wp_unslash( $_POST[ $key ] ), 'sanitize_text_field' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Read-only display condition on public requests.
		$value_in = sanitize_text_field( (string) $value );

		if ( is_array( $raw ) ) {
			// MANUAL REVIEW REQUIRED: Multi-value/array POST compares are not normalized; blank "any value" check still applies.
			if ( '' !== trim( (string) $value ) ) {
				return $this->compare( $show, true, $operator );
			}
			$show = true;
		} else {
			$incoming = (string) $raw;
			if ( '' === trim( (string) $value ) ) {
				$show = true;
			} elseif ( $value_in === $incoming ) {
				$show = true;
			}
		}

		return $this->compare( $show, true, $operator );
	}
}
