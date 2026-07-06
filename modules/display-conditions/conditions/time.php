<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Time
 *
 * @since  2.2.0
 */
class Time extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
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
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'time';
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
		return __( 'Time of Day', 'landtech-extras-for-elementor' );
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
			'label'		=> __( 'Before', 'landtech-extras-for-elementor' ),
			'type' 		=> \Elementor\Controls_Manager::DATE_TIME,
			'picker_options' => [
				'dateFormat' 	=> "H:i",
				'enableTime' 	=> true,
				'noCalendar' 	=> true,
			],
			'label_block'	=> true,
			'default' 		=> '',
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
		// Split control valur into two dates
		$parsed = strtotime( preg_replace( '/\s+/', '', $value ) );
		$time   = false !== $parsed ? wp_date( 'H:i', $parsed ) : '';
		$now    = wp_date( 'H:i', current_time( 'timestamp' ) );

		// Default returned bool to false
		$show 	= false;

		// Check vars
		if ( \DateTime::createFromFormat( 'H:i', $time ) === false ) // Make sure it's a valid DateTime format
			return;

		// Convert to timestamp
		$time_ts 	= strtotime( $time );
		$now_ts 	= strtotime( $now );

		// Check that user date is between start & end
		$show = ( $now_ts < $time_ts );

		return $this->compare( $show, true, $operator );
	}
}
