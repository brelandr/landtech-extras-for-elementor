<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

use LandTechExtras\Base\Condition;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Business hours presets (weekday + time range, site timezone via wp_date()).
 *
 * @since 2.2.102
 */
class Business_Hours extends Condition {

	/**
	 * @inheritDoc
	 */
	public function get_group() {
		return 'date_time';
	}

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'business_hours';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Business hours', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_value_control() {
		return array(
			'label'       => __( 'Schedule', 'landtech-extras-for-elementor' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'weekdays_9_17',
			'label_block' => true,
			'description' => __( 'Uses the WordPress site timezone. Combine with the Day condition for custom weekday rules.', 'landtech-extras-for-elementor' ),
			'options'     => array(
				'weekdays_9_17'  => __( 'Mon–Fri, 9:00–17:00', 'landtech-extras-for-elementor' ),
				'weekdays_8_18'  => __( 'Mon–Fri, 8:00–18:00', 'landtech-extras-for-elementor' ),
				'weekdays_10_16' => __( 'Mon–Fri, 10:00–16:00', 'landtech-extras-for-elementor' ),
				'daily_9_17'     => __( 'Every day, 9:00–17:00', 'landtech-extras-for-elementor' ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function check( $operator, $value, $name = null ) {
		$presets = array(
			'weekdays_9_17'  => array( 'days' => array( 1, 2, 3, 4, 5 ), 'start' => '09:00', 'end' => '17:00' ),
			'weekdays_8_18'  => array( 'days' => array( 1, 2, 3, 4, 5 ), 'start' => '08:00', 'end' => '18:00' ),
			'weekdays_10_16' => array( 'days' => array( 1, 2, 3, 4, 5 ), 'start' => '10:00', 'end' => '16:00' ),
			'daily_9_17'     => array( 'days' => array( 0, 1, 2, 3, 4, 5, 6 ), 'start' => '09:00', 'end' => '17:00' ),
		);

		$preset_key = sanitize_key( (string) $value );
		if ( ! isset( $presets[ $preset_key ] ) ) {
			return $this->compare( false, true, $operator );
		}

		$preset  = $presets[ $preset_key ];
		$now_ts  = (int) current_time( 'timestamp' );
		$weekday = (int) wp_date( 'w', $now_ts );
		$time    = wp_date( 'H:i', $now_ts );

		$show = in_array( $weekday, $preset['days'], true )
			&& $this->time_in_range( $time, $preset['start'], $preset['end'] );

		return $this->compare( $show, true, $operator );
	}

	/**
	 * Whether HH:MM is within an inclusive start/end range (same day).
	 *
	 * @param string $time   Current time H:i.
	 * @param string $start  Range start H:i.
	 * @param string $end    Range end H:i.
	 * @return bool
	 */
	private function time_in_range( $time, $start, $end ) {
		$time_ts  = strtotime( $time );
		$start_ts = strtotime( $start );
		$end_ts   = strtotime( $end );

		if ( false === $time_ts || false === $start_ts || false === $end_ts ) {
			return false;
		}

		return $time_ts >= $start_ts && $time_ts <= $end_ts;
	}
}
