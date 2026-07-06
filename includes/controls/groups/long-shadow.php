<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Custom long shadow group control
 *
 * @since 0.1.0
 */
class Group_Control_Long_Shadow extends Group_Control_Base {

	protected static $fields;

	/**
	 * @since 0.1.0
	 * @access public
	 */
	public static function get_type() {
		return 'long-shadow';
	}

	/**
	 * @since 0.1.0
	 * @access protected
	 */
	protected function init_fields() {
		$controls = [];

		$controls['enable'] = [
			'label'			=> _x( 'Long Shadow', 'Long Shadow Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SWITCHER,
			'default' 		=> '',
			'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
			'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
			'return_value' 	=> 'yes',
			'frontend_available' => true,
		];

		$controls['color'] = [
			'label'			=> _x( 'Color', 'Long Shadow Control', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::COLOR,
			'global' => [
				'default' => Global_Colors::COLOR_PRIMARY,
			],
			'condition' => [
				'enable!' => ''
			],
			'frontend_available' => true,
		];

		$controls['size'] = [
			'label'			=> _x( 'Size', 'Long Shadow Control', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::SLIDER,
			'default' 	=> [
				'size' 	=> 50,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 500,
				],
			],
			'condition' => [
				'enable!' => ''
			],
			'frontend_available' => true,
		];

		$controls['direction'] = [
			'label' 		=> _x( 'Direction', 'Long Shadow Control', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::SELECT,
			'options' 	=> [
				'top' 			=> __( 'Top', 'landtech-extras-for-elementor' ),
				'top-right' 	=> __( 'Top Right', 'landtech-extras-for-elementor' ),
				'right' 		=> __( 'Right', 'landtech-extras-for-elementor' ),
				'bottom-right' 	=> __( 'Bottom Right', 'landtech-extras-for-elementor' ),
				'bottom' 		=> __( 'Bottom', 'landtech-extras-for-elementor' ),
				'bottom-left' 	=> __( 'Bottom Left', 'landtech-extras-for-elementor' ),
				'left' 			=> __( 'Left', 'landtech-extras-for-elementor' ),
				'top-left' 		=> __( 'Top Left', 'landtech-extras-for-elementor' ),
			],
			'condition' => [
				'enable!' => ''
			],
			'default' 		=> 'bottom-right',
			'frontend_available' => true,
		];

		return $controls;
	}

	/**
	 * @since 0.1.0
	 * @access protected
	 */
	protected function get_default_options() {
		return [
			'popover' => false,
		];
	}
}
