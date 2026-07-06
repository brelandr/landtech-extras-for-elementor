<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Custom effect group control
 *
 * @since 1.4.0
 */
class Group_Control_Button_Effect extends Group_Control_Base {

	protected static $fields;

	private static $_types;
	private static $_directions;
	private static $_easings;
	private static $_entrances;
	private static $_shapes;
	private static $_filters;

	/**
	 * @since 1.4.0
	 * @access public
	 */
	public static function get_type() {
		return 'effect';
	}

	/**
	 * Retrieve the effect types
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array.  $_types The available array of effects
	 */
	public static function get_types() {
		if ( is_null( self::$_types ) ) {
			self::$_types = [
				'' 			=> __( 'None', 'landtech-extras-for-elementor' ),
				'clone' 	=> __( 'Clone', 'landtech-extras-for-elementor' ),
				'flip' 		=> __( 'Flip', 'landtech-extras-for-elementor' ),
				'back' 		=> __( 'Background', 'landtech-extras-for-elementor' ),
				'3d' 		=> __( '3D', 'landtech-extras-for-elementor' ),
				'cube' 		=> __( 'Cube', 'landtech-extras-for-elementor' ),
			];
		}

		return self::$_types;
	}

	/**
	 * Retrieve the filters array
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array.  $_filters The current array of filters
	 */
	public static function get_filters() {
		if ( is_null( self::$_filters ) ) {
			self::$_filters = [
				'displace' 	=> __( 'Displace', 'landtech-extras-for-elementor' ),
				'blur' 		=> __( 'Blur', 'landtech-extras-for-elementor' ),
			];
		}

		return self::$_filters;
	}

	/**
	 * Retrieve the entrance type
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array.  $_entrances The current array of entrance animation types
	 */
	public static function get_entrances() {
		if ( is_null( self::$_entrances ) ) {
			self::$_entrances = [
				'cover' 	=> __( 'Cover', 'landtech-extras-for-elementor' ),
				'move' 		=> __( 'Move', 'landtech-extras-for-elementor' ),
				'push' 		=> __( 'Push', 'landtech-extras-for-elementor' ),
			];
		}

		return self::$_entrances;
	}

	/**
	 * Retrieve the easings array
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array.  $_easings The current array of easing types
	 */
	public static function get_easings() {
		if ( is_null( self::$_easings ) ) {
			self::$_easings = [
				'linear' 		=> __( 'Linear', 'landtech-extras-for-elementor' ),
				'ease-in' 		=> __( 'Ease In', 'landtech-extras-for-elementor' ),
				'ease-out' 		=> __( 'Ease Out', 'landtech-extras-for-elementor' ),
				'ease-in-out' 	=> __( 'Ease In Out', 'landtech-extras-for-elementor' ),
			];
		}

		return self::$_easings;
	}

	/**
	 * @since 1.4.0
	 * @access protected
	 */
	protected function init_fields() {
		$controls = [];

		$controls['heading'] = [
			'label'			=> _x( 'Effect', 'Effect Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::HEADING,
			'separator' 	=> 'before',
		];

		$controls['type'] = [
			'label'			=> _x( 'Type', 'Effect Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> '',
			'options'		=> self::get_types(),
		];

		$controls['entrance'] = [
			'label'			=> _x( 'Entrance', 'Effect Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'cover',
			'options'		=> self::get_entrances(),
			'condition' 	=> [
				'type' 		=> [ 'clone', 'icon' ]
			]
		];

		$controls['text'] = [
			'label'			=> _x( 'Text', 'Effect Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'condition'		=> [
				'type' 		=> [ 'clone', 'flip', 'cube' ]
			]
		];

		$controls['direction'] = [
			'label'			=> _x( 'Direction', 'Effect Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::CHOOSE,
			'default' 		=> 'down',
			'options' => [
				'down' 		=> [
					'title' => __( 'Down', 'landtech-extras-for-elementor' ),
					'icon' 	=> 'eicon-v-align-bottom',
				],
				'up'    	=> [
					'title' => __( 'Up', 'landtech-extras-for-elementor' ),
					'icon' 	=> 'eicon-v-align-top',
				],
				'right' 	=> [
					'title' => __( 'Right', 'landtech-extras-for-elementor' ),
					'icon' 	=> 'eicon-h-align-right',
				],
				'left' 		=> [
					'title' => __( 'Left', 'landtech-extras-for-elementor' ),
					'icon' 	=> 'eicon-h-align-left',
				],
			],
			'label_block' 	=> false,
			'condition' 	=> [
				'type' 		=> [ 'clone', 'back', '3d', 'flip', 'cube' ]
			]
		];

		$controls['orientation'] = [
			'label'			=> _x( 'Orientation', 'Effect Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'horizontal',
			'options' => [
				'horizontal' 	=> __( 'Horizontal', 'landtech-extras-for-elementor' ),
				'vertical' 		=> __( 'Vertical', 'landtech-extras-for-elementor' ),
			],
			'condition' 	=> [
				'direction' => '',
				'type' 		=> [ 'back' ]
			]
		];

		$controls['shape'] = [
			'label'			=> _x( 'Shape', 'Effect Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::CHOOSE,
			'default' 		=> '',
			'options' => [
				''    	=> [
					'title' => __( 'Square', 'landtech-extras-for-elementor' ),
					'icon' 	=> 'nicon nicon-shape-square',
				],
				'round' 	=> [
					'title' => __( 'Round', 'landtech-extras-for-elementor' ),
					'icon' 	=> 'nicon nicon-shape-round',
				],
				'skewed' 	=> [
					'title' => __( 'Skewed', 'landtech-extras-for-elementor' ),
					'icon' 	=> 'nicon nicon-shape-skewed',
				],
			],
			'label_block' 	=> false,
			'condition' 	=> [
				'type' 		=> [ 'clone', 'back' ]
			]
		];

		$controls['double'] = [
			'label' 		=> __( 'Double', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SWITCHER,
			'default' 		=> '',
			'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
			'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
			'return_value' 	=> 'double',
			'condition' 	=> [
				'type' 		=> [ 'back' ],
			]
		];

		$controls['color'] = [
			'label' 	=> __( 'Effect Color', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::COLOR,
			'default' 	=> '#FFFFFF',
			'selectors' => [
				'{{SELECTOR}}.ee-effect--foreground .ee-button:after' => 'color: {{VALUE}};',
			],
			'condition' 	=> [
				'type' 		=> [ 'clone', 'back', 'flip', 'cube' ]
			]
		];

		$controls['background_color'] = [
			'label' 	=> __( 'Effect Background', 'landtech-extras-for-elementor' ),
			'type' 		=> Controls_Manager::COLOR,
			'default' 	=> '#000000',
			'selectors' => [
				 '{{SELECTOR}}.ee-effect--background .ee-button:before,
				  {{SELECTOR}}.ee-effect--double-background .ee-button:after' => 'background-color: {{VALUE}};',
			],
			'condition' 	=> [
				'type' 		=> [ 'clone', 'back', '3d', 'flip', 'cube' ]
			]
		];

		$controls['zoom'] = [
			'label'			=> _x( 'Zoom', 'Effect Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> '',
			'options'		=> [
				'' 			=> __( 'Default', 'landtech-extras-for-elementor' ),
				'zoom-in' 	=> __( 'Zoom In', 'landtech-extras-for-elementor' ),
				'zoom-out' 	=> __( 'Zoom Out', 'landtech-extras-for-elementor' ),
			],
			'condition' 	=> [
				'type' 		=> [ 'clone', '3d', 'flip', 'cube' ]
			]
		];

		$controls['easing'] = [
			'label'			=> _x( 'Easing', 'Effect Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'ease-in-out',
			'options'		=> self::get_easings(),
			'selectors' => [
				'{{SELECTOR}} .ee-button:before,
				 {{SELECTOR}} .ee-button:after,
				 {{SELECTOR}} .ee-button,
				 {{SELECTOR}}.ee-effect-type--clone .ee-button-content-wrapper,
				 {{SELECTOR}}.ee-effect-type--flip .ee-button-content-wrapper' => 'transition-timing-function: {{VALUE}}',
			],
		];

		$controls['duration'] = [
			'label'			=> _x( 'Duration', 'Effect Control', 'landtech-extras-for-elementor' ),
			'type' 			=> Controls_Manager::NUMBER,
			'default' 		=> 0.2,
			'min' 			=> 0.05,
			'max' 			=> 2,
			'step' 			=> 0.05,
			'label_block' 	=> false,
			'selectors' 	=> [
				'{{SELECTOR}} .ee-button:before,
				 {{SELECTOR}} .ee-button:after,
				 {{SELECTOR}} .ee-button,
				 {{SELECTOR}}.ee-effect-type--clone .ee-button-content-wrapper,
				 {{SELECTOR}}.ee-effect-type--flip .ee-button-content-wrapper' => 'transition-duration: {{VALUE}}s;',
			],
			'separator' 	=> 'after',
		];

		return $controls;
	}

	/**
	 * @since 1.4.0
	 * @access protected
	 */
	protected function get_default_options() {
		return [
			'popover' => false,
		];
	}
}
