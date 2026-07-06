<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Popup;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Module_Base;
use LandTechExtras\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Popup\Module
 *
 * @since  2.0.0
 */
class Module extends Module_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'popup';
	}

	/**
	 * Get Widgets
	 * 
	 * Get the modules' widgets
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public function get_widgets() {
		return [
			'Popup',
			'Age_Gate',
		];
	}

	/**
	 * Get Animation Options
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public static function get_animation_options() {
		return [
			'' 				=> __( 'None', 'landtech-extras-for-elementor' ),
			'zoom-in' 		=> __( 'Zoom In', 'landtech-extras-for-elementor' ),
			'zoom-out' 		=> __( 'Zoom Out', 'landtech-extras-for-elementor' ),
			'slide-right' 	=> __( 'Slide Right', 'landtech-extras-for-elementor' ),
			'slide-left' 	=> __( 'Slide Left', 'landtech-extras-for-elementor' ),
			'slide-top' 	=> __( 'Slide Top', 'landtech-extras-for-elementor' ),
			'slide-bottom' 	=> __( 'Slide Bottom', 'landtech-extras-for-elementor' ),
			'unfold-horizontal' => __( 'Unfold Horizontal', 'landtech-extras-for-elementor' ),
			'unfold-vertical' => __( 'Unfold Vertical', 'landtech-extras-for-elementor' ),
		];
	}
}
