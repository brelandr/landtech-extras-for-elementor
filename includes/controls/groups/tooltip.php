<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Group Control Tooltip
 *
 * @since 1.8.0
 */
class Group_Control_Tooltip extends Group_Control_Base {

	protected static $fields;

	/**
	 * @since 1.8.0
	 * @access public
	 */
	public static function get_type() {
		return 'ee-tooltip';
	}

	/**
	 * @since 1.8.0
	 * @access protected
	 */
	protected function init_fields() {
		$controls = [];

		$controls['content'] = [
			'label'			=> _x( 'Content', 'Tooltip Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> __( 'I am a tooltip', 'landtech-extras-for-elementor' ),
			'dynamic' 		=> [ 'active' => true ],
			'frontend_available'	=> true,
		];

		$controls['target'] = [
			'label'		=> __( 'Target', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> 'element',
			'options' 	=> [
				'element' 	=> __( 'Current Element', 'landtech-extras-for-elementor' ),
				'custom' 	=> __( 'Custom Selector', 'landtech-extras-for-elementor' ),
			],
			'frontend_available' => true
		];

		$controls['selector'] = [
			'label'			=> _x( 'CSS Selector', 'Tooltip Control', 'landtech-extras-for-elementor' ),
			'description'	=> __( 'Use a CSS selector for any html element WITHIN this element.', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'placeholder' 	=> __( '.css-selector', 'landtech-extras-for-elementor' ),
			'frontend_available'	=> true,
			'condition'	=> [
				'target' => 'custom',
			],
		];

		$controls['trigger'] = [
			'responsive'=> true,
			'label'		=> __( 'Trigger', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> 'mouseenter',
			'tablet_default' 	=> 'click_target',
			'mobile_default' 	=> 'click_target',
			'options' 	=> [
				'mouseenter' 	=> __( 'Mouse Over', 'landtech-extras-for-elementor' ),
				'click_target' 	=> __( 'Click Target', 'landtech-extras-for-elementor' ),
				'load' 			=> __( 'Page Load', 'landtech-extras-for-elementor' ),
			],
			'frontend_available' => true
		];

		$controls['trigger_warning'] = [
			'type' 					=> Controls_Manager::RAW_HTML,
			'raw' 					=> __( 'Notice: If you element is a link, clicking it will result in both opening the tooltip and navigating to the link URL.', 'landtech-extras-for-elementor' ),
			'content_classes' 		=> 'elementor-panel-alert elementor-panel-alert-warning',
			'condition' 			=> [
				'trigger' 			=> 'click_target',
			],
		];

		$controls['_hide'] = [
			'responsive'=> true,
			'label'		=> __( 'Hide on', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 			=> 'mouseleave',
			'tablet_default' 	=> 'click_any',
			'mobile_default' 	=> 'click_any',
			'options' 	=> [
				'mouseleave' 	=> __( 'Mouse Leave', 'landtech-extras-for-elementor' ),
				'click_out' 	=> __( 'Click Outside', 'landtech-extras-for-elementor' ),
				'click_target' 	=> __( 'Click Target', 'landtech-extras-for-elementor' ),
				'click_any' 	=> __( 'Click Anywhere', 'landtech-extras-for-elementor' ),
			],
			'frontend_available' => true
		];

		$controls['position'] = [
			'label'			=> _x( 'Show to', 'Tooltip Control', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> '',
			'options' 	=> [
				'' 			=> __( 'Global', 'landtech-extras-for-elementor' ),
				'bottom' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
				'left' 		=> __( 'Left', 'landtech-extras-for-elementor' ),
				'top' 		=> __( 'Top', 'landtech-extras-for-elementor' ),
				'right' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
			],
			'frontend_available' => true
		];

		$controls['arrow_position_h'] = [
			'label'			=> _x( 'Show at', 'Tooltip Control', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> '',
			'options' 	=> [
				'' 			=> __( 'Global', 'landtech-extras-for-elementor' ),
				'center' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
				'left' 		=> __( 'Left', 'landtech-extras-for-elementor' ),
				'right' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
			],
			'condition'		=> [
				'position'	=> [ 'top', 'bottom' ],
			],
			'frontend_available' => true
		];

		$controls['arrow_position_v'] = [
			'label'			=> _x( 'Show at', 'Tooltip Control', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> '',
			'options' 	=> [
				'' 			=> __( 'Global', 'landtech-extras-for-elementor' ),
				'center' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
				'bottom' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
				'top' 		=> __( 'Top', 'landtech-extras-for-elementor' ),
			],
			'condition'		=> [
				'position'	=> [ 'left', 'right' ],
			],
			'frontend_available' => true
		];

		$controls['css_position'] = [
			'label' 		=> _x( 'CSS Position', 'Tooltip Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> '',
			'options'		=> [
				'' 			=> 'Absolute',
				'fixed'		=> 'Fixed',
			],
			'frontend_available' => true,
		];

		$controls['disable'] = [
			'label'		=> _x( 'Disable On', 'Tooltip Control', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::SELECT,
			'default' 	=> '',
			'options' 	=> [
				'' 			=> __( 'None', 'landtech-extras-for-elementor' ),
				'tablet' 	=> __( 'Tablet & Mobile', 'landtech-extras-for-elementor' ),
				'mobile' 	=> __( 'Mobile', 'landtech-extras-for-elementor' ),
			],
			'frontend_available' => true
		];

		$controls['delay_in'] = [
			'label' 		=> _x( 'Delay in (s)', 'Tooltip Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SLIDER,
			'range' 	=> [
				'px' 	=> [
					'min' 	=> 0,
					'max' 	=> 1,
					'step'	=> 0.1,
				],
			],
			'frontend_available' => true
		];

		$controls['delay_out'] = [
			'label' 		=> _x( 'Delay out (s)', 'Tooltip Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SLIDER,
			'range' 	=> [
				'px' 	=> [
					'min' 	=> 0,
					'max' 	=> 1,
					'step'	=> 0.1,
				],
			],
			'frontend_available' => true
		];

		$controls['duration'] = [
			'label' 		=> _x( 'Duration', 'Tooltip Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SLIDER,
			'range' 	=> [
				'px' 	=> [
					'min' 	=> 0,
					'max' 	=> 2,
					'step'	=> 0.1,
				],
			],
			'frontend_available' => true
		];

		return $controls;
	}

	/**
	 * @since 1.8.0
	 * @access protected
	 */
	protected function get_default_options() {
		return [
			'popover' => false,
		];
	}
}
