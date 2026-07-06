<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Day
 *
 * @since  2.2.6
 */
class Day extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.6
	 * @return string
	 */
	public function get_group() {
		return 'date_time';
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.6
	 * @return string
	 */
	public function get_name() {
		return 'day';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the module
	 *
	 * @since  2.2.6
	 * @return string
	 */
	public function get_title() {
		return __( 'Day of Week', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Value Control
	 * 
	 * Get the settings for the value control
	 *
	 * @since  2.2.6
	 * @return string
	 */
	public function get_value_control() {
		return [
			'type' 			=> Controls_Manager::SELECT2,
			'multiple'		=> true,
			'options' => [
				'1' => __( 'Monday', 'landtech-extras-for-elementor' ),
				'2' => __( 'Tuesday', 'landtech-extras-for-elementor' ),
				'3' => __( 'Wednesday', 'landtech-extras-for-elementor' ),
				'4' => __( 'Thursday', 'landtech-extras-for-elementor' ),
				'5' => __( 'Friday', 'landtech-extras-for-elementor' ),
				'6' => __( 'Saturday', 'landtech-extras-for-elementor' ),
				'0' => __( 'Sunday', 'landtech-extras-for-elementor' ),
			],
			'label_block'	=> true,
			'default' 		=> '1',
		];
	}

	/**
	 * Check day of week
	 *
	 * Checks wether today falls inside a
	 * specified day of the week
	 *
	 * @since 2.2.6
	 *
	 * @access protected
	 *
	 * @param string $operator Comparison operator.
	 * @param mixed  $value    The control value to check.
	 * @param mixed  $name     Unused (reserved).
	 */
	public function check( $operator, $value, $name = null ) {

		$show 	= false;
		$today 	= new \DateTime();

		if ( function_exists( 'wp_timezone' ) ) {
			$timezone = wp_timezone();

			// Set timezone
			$today->setTimeZone( $timezone );
		}

		$day = $today->format('w');

		$show = is_array( $value ) && ! empty( $value ) ? in_array( $day, $value ) : $value === $day;

		return self::compare( $show, true, $operator );
	}
}
