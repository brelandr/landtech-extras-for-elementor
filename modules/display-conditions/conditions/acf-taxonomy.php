<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Acf_Taxonomy
 *
 * @since  2.2.0
 */
class Acf_Taxonomy extends Acf_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'acf_taxonomy';
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
		return __( 'ACF Taxonomy', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Name Control
	 * 
	 * Get the settings for the name control
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_name_control() {
		return wp_parse_args( [
			'description'	=> __( 'Search ACF "Taxonomy" fields by label.', 'landtech-extras-for-elementor' ),
			'placeholder'	=> __( 'Search Fields', 'landtech-extras-for-elementor' ),
		], $this->get_name_control_options() );
	}

	/**
	 * Additional query control options
	 *
	 * @since  2.2.33
	 * @return bool
	 */
	public function get_additional_query_options() {
		return [
			'field_type'	=> [
				'taxonomy',
			],
		];
	}

	/**
	 * Get Value Control
	 * 
	 * Get the settings for the value control
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_value_control() {
		return [
			'description'	=> __( 'Leave blank for any term.', 'landtech-extras-for-elementor' ),
			'placeholder'	=> __( 'Search Terms', 'landtech-extras-for-elementor' ),
			'type' 			=> 'ee-query',
			'post_type' 	=> '',
			'options' 		=> [],
			'label_block' 	=> true,
			'multiple' 		=> true,
			'query_type' 	=> 'terms',
			'include_type' 	=> true,
		];
	}

	/**
	 * Check condition
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 *
	 * @param string $operator Comparison operator.
	 * @param mixed  $value    Selected term IDs from control.
	 * @param string $name     ACF field key.
	 */
	public function check( $operator, $value, $name = null ) {
		$show = false;

		$field_value = $this->get_field( $name );

		if ( $field_value ) {
			if ( ! $value || '' === $value || empty( $value ) ) {
				return $this->compare( true, true, $operator );
			}

			$values 		= (array) $value;
			$field_term_ids = $this->parse_field_values( $field_value );
			$value_term_ids = array_map('intval', $values );

			$show = ! empty( array_intersect( $field_term_ids, $value_term_ids ) );
		}

		return $this->compare( $show, true, $operator );
	}

	/**
	 * Parse field values
	 *
	 * Depending on the return formats and number of field values
	 * this function returns an array with the term IDs set in
	 * the field settings
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 *
	 * @param 	string  	$posts  			The posts saved in the field
	 * @return 	array   	$return_values  	The array of post IDs
	 */
	public function parse_field_values( $terms ) {
		$return_values = [];

		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$return_values[] = ( is_a( $term, 'WP_Term' ) ) ? $term->term_id : $term;
			}
		} else {
			$return_values[] = ( is_a( $terms, 'WP_Term' ) ) ? $terms->term_id : $terms;
		}

		return $return_values;
	}
}
