<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Author_Archive
 *
 * @since  2.2.0
 */
class Author_Archive extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {
		return 'archive';
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
		return 'author_archive';
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
		return __( 'Author', 'landtech-extras-for-elementor' );
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
			'type' 			=> 'ee-query',
			'default' 		=> '',
			'placeholder'	=> __( 'Any', 'landtech-extras-for-elementor' ),
			'description'	=> __( 'Leave blank for all authors.', 'landtech-extras-for-elementor' ),
			'multiple'		=> true,
			'label_block' 	=> true,
			'query_type'	=> 'authors',
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
		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_author( $_value ) ) {
					$show = true; break;
				}
			}
		} else { $show = is_author( $value ); }

		return $this->compare( $show, true, $operator );
	}
}
