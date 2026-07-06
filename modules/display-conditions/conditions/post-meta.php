<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Condition
 *
 * @since  2.2.27
 */
class Post_Meta extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.27
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
	 * @since  2.2.27
	 * @return string
	 */
	public function get_name() {
		return 'post_meta';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the module
	 *
	 * @since  2.2.27
	 * @return string
	 */
	public function get_title() {
		return __( 'Post Meta', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Name Control
	 * 
	 * Get the settings for the name control
	 *
	 * @since  2.2.27
	 * @return array
	 */
	public function get_name_control() {
		return [
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'placeholder'	=> __('meta_key', 'landtech-extras-for-elementor'),
			'label_block' 	=> true,
		];
	}

	/**
	 * Get Value Control
	 * 
	 * Get the settings for the value control
	 *
	 * @since  2.2.27
	 * @return array
	 */
	public function get_value_control() {
		return [
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'placeholder'	=> __('meta_value', 'landtech-extras-for-elementor'),
			'description'	=> __('Leave empty to check if the current post has any meta value for the selected key.', 'landtech-extras-for-elementor'),
			'label_block' 	=> true,
		];
	}

	/**
	 * Check condition
	 *
	 * @since 2.2.27
	 *
	 * @access public
	 *
	 * @param string $operator Comparison operator.
	 * @param mixed  $value    The meta value to check.
	 * @param string $name     Meta key.
	 */
	public function check( $operator, $value, $name = null ) {
		$show = false;

		if ( $name ) {
			$meta_values = get_post_meta( get_the_ID(), $name ); // Returns false if no matching meta key is set

			if ( ( ! $value || '' === trim( $value ) || empty( $value ) ) && $meta_values ) {
				return $this->compare( true, true, $operator );
			}

			foreach ( $meta_values as $meta_value ) {
				if ( $value === $meta_value ) {
					$show = true;
					break;
				}
			}
		} else {
			$show = true;
		}

		return $this->compare( $show, true, $operator );
	}
}
