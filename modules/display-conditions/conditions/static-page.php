<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Static_Page
 *
 * @since  2.2.0
 */
class Static_Page extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {
		return 'single';
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
		return 'static_page';
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
		return __( 'Static Page', 'landtech-extras-for-elementor' );
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
			'default' 		=> 'home',
			'label_block' 	=> true,
			'options' 		=> [
				'home'		=> __( 'Default Homepage', 'landtech-extras-for-elementor' ),
				'static'	=> __( 'Static Homepage', 'landtech-extras-for-elementor' ),
				'blog'		=> __( 'Blog Page', 'landtech-extras-for-elementor' ),
				'404'		=> __( '404 Page', 'landtech-extras-for-elementor' ),
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
		if ( 'home' === $value ) {
			return $this->compare( ( is_front_page() && is_home() ), true, $operator );
		} elseif ( 'static' === $value ) {
			return $this->compare( ( is_front_page() && ! is_home() ), true, $operator );
		} elseif ( 'blog' === $value ) {
			return $this->compare( ( ! is_front_page() && is_home() ), true, $operator );
		} elseif ( '404' === $value ) {
			return $this->compare( is_404(), true, $operator );
		}
	}
}
