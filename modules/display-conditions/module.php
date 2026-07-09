<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Utils;
use LandTechExtras\Base\Module_Base;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Module
 *
 * @since  2.2.0
 */
class Module extends Module_Base {

	/**
	 * Display Conditions 
	 *
	 * Holds all the conditions for display on the frontend
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @var bool
	 */
	protected $conditions = [];

	/**
	 * Display Conditions 
	 *
	 * Holds all the conditions classes
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @var bool
	 */
	protected $_conditions = [];

	/**
	 * Conditions Repeater
	 *
	 * The repeater control
	 *
	 * @since 2.2.0
	 * @access protected
	 *
	 * @var bool
	 */
	protected $_conditions_repeater;

	/**
	 * Whether Elementor hooks were already registered for this module instance.
	 *
	 * @since 2.4.1
	 * @var bool
	 */
	private static $actions_registered = false;

	/**
	 * Element stacks that already received display condition controls.
	 *
	 * @since 2.4.1
	 * @var array<string, bool>
	 */
	private static $controls_registered = array();

	const VISITOR_GROUP 	= 'visitor';
	const DATE_TIME_GROUP 	= 'date_time';
	const SINGLE_GROUP 		= 'single';
	const ARCHIVE_GROUP 	= 'archive';
	const ACF_GROUP 		= 'acf';
	const EDD_GROUP 		= 'edd';
	const VAR_GROUP 		= 'var';
	const MISC_GROUP 		= 'misc';

	public function get_groups() {
		return [
			self::VISITOR_GROUP => [
				'label' => __( 'Visitor', 'landtech-extras-for-elementor' ),
			],
			self::DATE_TIME_GROUP => [
				'label' => __( 'Date & Time', 'landtech-extras-for-elementor' ),
			],
			self::SINGLE_GROUP => [
				'label' => __( 'Single', 'landtech-extras-for-elementor' ),
			],
			self::ARCHIVE_GROUP => [
				'label' => __( 'Archive', 'landtech-extras-for-elementor' ),
			],
			self::ACF_GROUP => [
				'label' => __( 'Post ACF', 'landtech-extras-for-elementor' ),
			],
			self::EDD_GROUP => [
				'label' => __( 'Easy Digital Downloads', 'landtech-extras-for-elementor' ),
			],
			self::VAR_GROUP => [
				'label' => __( 'Variables', 'landtech-extras-for-elementor' ),
			],
			self::MISC_GROUP => [
				'label' => __( 'Misc', 'landtech-extras-for-elementor' ),
			],
		];
	}

	/**
	 * Display Conditions 
	 *
	 * Holds all the conditions for display on the frontend
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @var bool
	 */
	protected $conditions_options = [];

	public function __construct() {
		parent::__construct();

		require_once __DIR__ . '/viewport-breakpoints.php';

		$this->register_conditions();
	}

	/**
	 * @since 0.1.0
	 */
	public function register_conditions() {

		$available_conditions = [
			// Visitor
			'authentication',
			'user',
			'role',
			'cookie',
			'os',
			'browser',
			'viewport',

			// Date Time
			'date',
			'date_time_before',
			'time',
			'business_hours',
			'day',

			// Single
			'page',
			'post',
			'static_page',
			'post_type',
			'post_meta',
			'post_term',

			//Archive
			'taxonomy_archive',
			'term_archive',
			'post_type_archive',
			'date_archive',
			'author_archive',
			'search_results',

			// ACF
			'acf_text',
			'acf_choice',
			'acf_true_false',
			'acf_post',
			'acf_taxonomy',
			'acf_date_time',

			// EDD
			'edd_cart',

			// Variables
			'var_get',
			'var_utm',
			'var_post',

			// Misc
			'shortcode',
		];

		foreach ( $available_conditions as $condition_name ) {

			$class_name = str_replace( '-', ' ', $condition_name );
			$class_name = str_replace( ' ', '', ucwords( $class_name ) );
			$class_name = __NAMESPACE__ . '\\Conditions\\' . $class_name;

			if ( class_exists( $class_name )  ) {
				if ( $class_name::is_supported() ) {
					$this->_conditions[ $condition_name ] = $class_name::instance();
				}
			}
		}

		/**
		 * Register additional display condition drivers (Premium extensions).
		 *
		 * @since 2.3.0
		 *
		 * @param array<string, \LandTechExtras\Base\Condition> $conditions Registered conditions.
		 */
		$extra = apply_filters( 'landtech_extras/display_conditions/register', array() );

		if ( is_array( $extra ) ) {
			foreach ( $extra as $slug => $condition ) {
				if ( is_object( $condition ) && $condition instanceof \LandTechExtras\Base\Condition ) {
					$this->_conditions[ sanitize_key( (string) $slug ) ] = $condition;
				}
			}
		}
	}

	/**
	 * @param string $condition_name
	 *
	 * @return Module_Base|Module_Base[]
	 */
	public function get_conditions( $condition_name = null ) {
		if ( $condition_name ) {
			if ( isset( $this->_conditions[ $condition_name ] ) ) {
				return $this->_conditions[ $condition_name ];
			}
			return null;
		}

		return $this->_conditions;
	}

	/**
	 * Set conditions.
	 *
	 * Sets the conditions property to all conditions comparison values
	 *
	 * @since 2.0.0
	 * @access protected
	 * @static
	 *
	 * @param mixed  $conditions  The conditions from the repeater field control
	 *
	 * @return void
	 */
	protected function set_conditions( $id, $conditions = [] ) {
		if ( ! $conditions )
			return;

		foreach ( $conditions as $index => $condition ) {

			$key 		= $condition['ltxe_condition_key'];
			$name 		= null;

			if ( array_key_exists( 'ltxe_condition_' . $key . '_name' , $condition ) ) {
				$name = $condition['ltxe_condition_' . $key . '_name'];
			}

			$operator 	= $condition['ltxe_condition_operator'];
			$value 		= $condition['ltxe_condition_' . $key . '_value'];

			$_condition = $this->get_conditions( $key );

			if ( ! $_condition ) {
				continue;
			}

			$_condition->set_element_id( $id );

			$check = $_condition->check( $operator, $value, $name );

			/**
			 * Filter individual display condition evaluation (Premium membership + viewport stacking).
			 *
			 * @since 2.4.4
			 *
			 * @param bool   $check    Whether the condition passed.
			 * @param string $key      Condition slug.
			 * @param string $operator Comparison operator.
			 * @param mixed  $value    Condition value.
			 * @param mixed  $name     Optional condition name control.
			 * @param string $id       Elementor element id.
			 */
			$check = apply_filters( 'landtech_extras/display_conditions/evaluate', $check, $key, $operator, $value, $name, $id );

			$this->conditions[ $id ][ $key . '_' . $condition['_id'] ] = $check;
		}
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
		return 'display-conditions';
	}

	/**
	 * Add Actions
	 *
	 * @since 2.0.0
	 *
	 * @access protected
	 */
	public function add_actions() {
		if ( self::$actions_registered ) {
			return;
		}

		self::$actions_registered = true;

		// Activate controls for widgets
		add_action( 'elementor/element/common/section_landtech_extras_advanced/before_section_end', function( $element, $args ) {
			$this->add_controls( $element, $args );
		}, 10, 2 );

		add_action( 'elementor/element/section/section_landtech_extras_advanced/before_section_end', function( $element, $args ) {
			$this->add_controls( $element, $args );
		}, 10, 2 );

		add_action( 'elementor/element/container/section_landtech_extras_advanced/before_section_end', function( $element, $args ) {
			$this->add_controls( $element, $args );
		}, 10, 2 );

		// Conditions for widgets
		add_action( 'elementor/widget/render_content', function( $widget_content, $element ) {
			$settings = $element->get_settings();
			if ( ! empty( $settings['ltxe_display_conditions_enable'] ) && 'yes' === $settings[ 'ltxe_display_conditions_enable' ] ) {

				// Set the conditions
				$this->set_conditions( $element->get_id(), $settings['ltxe_display_conditions'] );

				if ( ! $this->is_visible( $element->get_id(), $settings['ltxe_display_conditions_relation'] ) ) { // Check the conditions
					if ( 'yes' !== $settings['ltxe_display_conditions_output'] ) {
						return; // And on frontend we stop the rendering of the widget
					}
				}
			}
   
			return $widget_content;
		
		}, 10, 2 );

		// Conditions for widgets
		add_action( 'elementor/frontend/widget/before_render', function( $element ) {
			$settings = $element->get_settings();

			if ( ! empty( $settings['ltxe_display_conditions_enable'] ) && 'yes' === $settings[ 'ltxe_display_conditions_enable' ] ) {

				// Set the conditions
				$this->set_conditions( $element->get_id(), $settings['ltxe_display_conditions'] );

				if ( ! $this->is_visible( $element->get_id(), $settings['ltxe_display_conditions_relation'] ) ) { // Check the conditions
					$element->add_render_attribute( '_wrapper', 'class', 'ee-conditions--hidden' );
				}
			}

		}, 10, 1 );

		// Conditions for sections
		add_action( 'elementor/frontend/section/before_render', function( $element ) {
			$settings = $element->get_settings();

			if ( ! empty( $settings['ltxe_display_conditions_enable'] ) && 'yes' === $settings[ 'ltxe_display_conditions_enable' ] ) {

				// Set the conditions
				$this->set_conditions( $element->get_id(), $settings['ltxe_display_conditions'] );

				if ( ! $this->is_visible( $element->get_id(), $settings['ltxe_display_conditions_relation'] ) ) { // Check the conditions
					$element->add_render_attribute( '_wrapper', 'class', 'ee-conditions--hidden' );
				}
			}

		}, 10, 1 );

		add_action( 'elementor/frontend/container/before_render', function( $element ) {
			$settings = $element->get_settings();

			if ( ! empty( $settings['ltxe_display_conditions_enable'] ) && 'yes' === $settings[ 'ltxe_display_conditions_enable' ] ) {

				$this->set_conditions( $element->get_id(), $settings['ltxe_display_conditions'] );

				if ( ! $this->is_visible( $element->get_id(), $settings['ltxe_display_conditions_relation'] ) ) {
					$element->add_render_attribute( '_wrapper', 'class', 'ee-conditions--hidden' );
				}
			}

		}, 10, 1 );
	}

	/**
	 * Add Controls
	 *
	 * @since 2.2.0
	 *
	 * @access private
	 */
	public function add_controls( $element, $args ) {
		unset( $args );

		$stack_id = $element->get_unique_name();

		if ( ! empty( self::$controls_registered[ $stack_id ] ) ) {
			return;
		}

		$existing = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $stack_id, 'ltxe_display_conditions_enable' );

		if ( ! is_wp_error( $existing ) ) {
			self::$controls_registered[ $stack_id ] = true;
			return;
		}

		$this->_conditions_repeater = new Repeater();

		$element_type = $element->get_type();

		$element->add_control(
			'ltxe_display_conditions_enable',
			[
				'label'			=> __( 'Display Conditions', 'landtech-extras-for-elementor' ),
				'type' 			=> Controls_Manager::SWITCHER,
				'default' 		=> '',
				'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
				'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
				'return_value' 	=> 'yes',
				'frontend_available'	=> true,
			]
		);

		if ( 'widget' === $element_type ) {
			$element->add_control(
				'ltxe_display_conditions_output',
				[
					'label'		=> __( 'Output HTML', 'landtech-extras-for-elementor' ),
					'description' => sprintf(
						/* translators: %s: Element type (e.g. widget). */
						__( 'If enabled, the HTML code will exist on the page but the %s will be hidden using CSS.', 'landtech-extras-for-elementor' ),
						$element_type
					),
					'default'	=> 'yes',
					'type' 		=> Controls_Manager::SWITCHER,
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'condition'	=> [
						'ltxe_display_conditions_enable' => 'yes',
					],
				]
			);
		}

		$element->add_control(
			'ltxe_display_conditions_relation',
			[
				'label'		=> __( 'Display on', 'landtech-extras-for-elementor' ),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'all',
				'options' 	=> [
					'all' 		=> __( 'All conditions met', 'landtech-extras-for-elementor' ),
					'any' 		=> __( 'Any condition met', 'landtech-extras-for-elementor' ),
				],
				'condition'	=> [
					'ltxe_display_conditions_enable' => 'yes',
				],
			]
		);

		$this->_conditions_repeater->add_control(
			'ltxe_condition_key',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'authentication',
				'label_block' 	=> true,
				'groups' 		=> $this->get_conditions_options(),
			]
		);

		$this->add_name_controls();

		$this->_conditions_repeater->add_control(
			'ltxe_condition_operator',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'is',
				'label_block' 	=> true,
				'options' 		=> [
					'is' 		=> __( 'Is', 'landtech-extras-for-elementor' ),
					'not' 		=> __( 'Is not', 'landtech-extras-for-elementor' ),
				],
			]
		);

		$this->add_value_controls();

		$element->add_control(
			'ltxe_display_conditions',
			[
				'label' 	=> __( 'Conditions', 'landtech-extras-for-elementor' ),
				'type' 		=> Controls_Manager::REPEATER,
				'default' 	=> [
					[
						'ltxe_condition_key' 					=> 'authentication',
						'ltxe_condition_operator' 			=> 'is',
						'ltxe_condition_authentication_value' => 'authenticated',
					],
				],
				'condition'		=> [
					'ltxe_display_conditions_enable' => 'yes',
				],
				'fields' 		=> $this->_conditions_repeater->get_controls(),
				'title_field' 	=> __( 'Display If', 'landtech-extras-for-elementor'),
			]
		);

		self::$controls_registered[ $stack_id ] = true;
	}

	/**
	 * Add Value Controls
	 *
	 * Loops through conditions and adds the controls
	 * which select the value to check
	 *
	 * @since 2.2.0
	 *
	 * @access private
	 * @return void
	 */
	private function add_name_controls() {
		if ( ! $this->_conditions )
			return;

		foreach ( $this->_conditions as $_condition ) {

			if ( false === $_condition->get_name_control() ) {
				continue;
			}

			$condition_name 	= $_condition->get_name();
			$control_key 		= 'ltxe_condition_' . $condition_name . '_name';
			$control_settings 	= $_condition->get_name_control();

			// Show this only if the user select this specific condition
			$control_settings['condition'] = [
				'ltxe_condition_key' => $condition_name,
			];

			// 
			$this->_conditions_repeater->add_control( $control_key, $control_settings );
		}
	}

	/**
	 * Add Value Controls
	 *
	 * Loops through conditions and adds the controls
	 * which select the value to check
	 *
	 * @since 2.2.0
	 *
	 * @access private
	 * @return void
	 */
	private function add_value_controls() {
		if ( ! $this->_conditions )
			return;

		foreach ( $this->_conditions as $_condition ) {

			$condition_name 	= $_condition->get_name();
			$control_key 		= 'ltxe_condition_' . $condition_name . '_value';
			$control_settings 	= $_condition->get_value_control();

			// Show this only if the user select this specific condition
			$control_settings['condition'] = [
				'ltxe_condition_key' => $condition_name,
			];

			// 
			$this->_conditions_repeater->add_control( $control_key, $control_settings );
		}
	}

	/**
	 * Set the Conditions options array
	 *
	 * @since 2.1.0
	 *
	 * @access private
	 */
	private function get_conditions_options() {

		$groups = $this->get_groups();

		foreach ( $this->_conditions as $_condition ) {
			$groups[ $_condition->get_group() ]['options'][ $_condition->get_name() ] = $_condition->get_title();
		}

		return $groups;
	}

	/**
	 * Check conditions.
	 *
	 * Checks for all or any conditions and returns true or false
	 * depending on wether the content can be shown or not
	 *
	 * @since 2.0.0
	 * @access protected
	 * @static
	 *
	 * @param mixed  $relation  Required conditions relation
	 *
	 * @return bool
	 */
	protected function is_visible( $id, $relation ) {

		if ( ! array_key_exists( $id, $this->conditions ) )
			return;

		if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			if ( 'any' === $relation ) {
				if ( ! in_array( true, $this->conditions[ $id ] ) )
					return false;
			} else {
				if ( in_array( false, $this->conditions[ $id ] ) )
					return false;
			}
		}

		return true;
	}
}
