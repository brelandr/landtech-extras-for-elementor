<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Browser
 *
 * @since  2.2.0
 */
class Browser extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {
		return 'visitor';
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'browser';
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
		return __( 'Browser', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Value Control
	 * 
	 * Get the settings for the value control
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_value_control() {
		return [
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'ie',
			'label_block' 	=> true,
			'options' 		=> [
				'ie'         => __( 'Internet Explorer', 'landtech-extras-for-elementor' ),
				'firefox'    => __( 'Mozilla Firefox', 'landtech-extras-for-elementor' ),
				'chrome'     => __( 'Google Chrome', 'landtech-extras-for-elementor' ),
				'opera_mini' => __( 'Opera Mini', 'landtech-extras-for-elementor' ),
				'opera'      => __( 'Opera', 'landtech-extras-for-elementor' ),
				'safari'     => __( 'Safari', 'landtech-extras-for-elementor' ),
			],
		];
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
		$browsers = [
			'ie'			=> [
				'MSIE',
				'Trident',
			],
			'firefox'		=> 'Firefox',
			'chrome'		=> 'Chrome',
			'opera_mini'	=> 'Opera Mini',
			'opera'			=> 'Opera',
			'safari'		=> 'Safari',
		];
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		$show = false;

		if ( 'ie' === $value ) {
			if ( '' !== $user_agent && ( false !== strpos( $user_agent, $browsers[ $value ][0] ) || false !== strpos( $user_agent, $browsers[ $value ][1] ) ) ) {
				$show = true;
			}
		} else {
			if ( '' !== $user_agent && isset( $browsers[ $value ] ) && false !== strpos( $user_agent, $browsers[ $value ] ) ) {
				$show = true;

				// Additional check for Chrome that returns Safari
				if ( 'safari' === $value || 'firefox' === $value ) {
					if ( false !== strpos( $user_agent, 'Chrome' ) ) {
						$show = false;
					}
				}
			}
		}

		return $this->compare( $show, true, $operator );
	}
}
