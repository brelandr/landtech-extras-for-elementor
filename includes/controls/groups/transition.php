<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Custom transition group control
 *
 * @since 1.1.4
 */
class Group_Control_Transition extends Group_Control_Base {

	protected static $fields;

	/**
	 * @since 1.7.0
	 * @access public
	 */
	public static function get_type() {
		return 'ee-transition';
	}

	/**
	 * Retrieve the effect easings
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return array.  The available array of easing types
	 */
	public static function get_easings() {
		return [
			'linear' 		=> __( 'Linear', 'landtech-extras-for-elementor' ),
			'ease-in' 		=> __( 'Ease In', 'landtech-extras-for-elementor' ),
			'ease-out' 		=> __( 'Ease Out', 'landtech-extras-for-elementor' ),
			'ease-in-out' 	=> __( 'Ease In Out', 'landtech-extras-for-elementor' ),
		];
	}

	/**
	 * @since 1.7.0
	 * @access protected
	 */
	protected function init_fields() {
		$controls = [];

		$controls['property'] = [
			'label'			=> _x( 'Property', 'Transition Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'all',
			'options'		=> [
				'all'		=> __( 'All', 'landtech-extras-for-elementor' ),
			],
			'selectors' => [
				'{{SELECTOR}}' => 'transition-property: {{VALUE}}',
			],
		];

		$controls['easing'] = [
			'label'			=> _x( 'Easing', 'Transition Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'linear',
			'options'		=> self::get_easings(),
			'selectors' => [
				'{{SELECTOR}}' => 'transition-timing-function: {{VALUE}}',
			],
		];

		$controls['duration'] = [
			'label'			=> _x( 'Duration', 'Transition Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::NUMBER,
			'default' 		=> 0.3,
			'min' 			=> 0.05,
			'max' 			=> 2,
			'step' 			=> 0.05,
			'selectors' 	=> [
				'{{SELECTOR}}' => 'transition-duration: {{VALUE}}s;',
			],
			'separator' 	=> 'after',
		];

		return $controls;
	}

	/**
	 * Prepare fields.
	 *
	 * @since 1.7.0
	 * @access protected
	 *
	 * @param array $fields Control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {

		array_walk(
			$fields, function( &$field, $field_name ) {

				if ( in_array( $field_name, [ 'transition', 'popover_toggle' ] ) ) {
					return;
				}

				$field['condition']['transition'] = 'custom';
			}
		);

		return parent::prepare_fields( $fields );
	}

	/**
	 * @since 1.7.0
	 * @access protected
	 */
	protected function get_default_options() {
		return [
			'popover' => [
				'starter_name' 	=> 'transition',
				'starter_title' => _x( 'Transition', 'Transition Control', 'landtech-extras-for-elementor' ),
			],
		];
	}
}
