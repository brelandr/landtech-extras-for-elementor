<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions\Search_Results
 *
 * @since  2.2.0
 */
class Search_Results extends Condition {

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
		return 'search_results';
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
		return __( 'Search', 'landtech-extras-for-elementor' );
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
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'placeholder'	=> __( 'Keywords', 'landtech-extras-for-elementor' ),
			'description'	=> __( 'Enter keywords, separated by commas, to condition the display on specific keywords and leave blank for any.', 'landtech-extras-for-elementor' ),
			'label_block' 	=> true,
		];
	}

	/**
	 * Check if keyword exists in phrase
	 *
	 * @since 2.2.0
	 *
	 * @access public
	 *
	 * @param string  	$keyword  	Keyword to search
	 * @param string 	$phrase  	Phrase to search in
	 */
	protected function keyword_exists( $keyword, $phrase ) {
		$needle = trim( is_scalar( $keyword ) ? (string) $keyword : '' );
		if ( '' === $needle ) {
			return false;
		}
		$haystack = is_scalar( $phrase ) ? (string) $phrase : '';

		return strpos( $haystack, $needle ) !== false;
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

		if ( is_search() ) {

			if ( empty( $value ) ) { // We're showing on all search pages

				$show = true;

			} else { // We're showing on specific keywords

				$phrase = get_search_query(); // The user search query

				if ( '' !== $phrase && ! empty( $phrase ) ) { // Only proceed if there is a query

					$keywords = explode( ',', $value ); // Separate keywords

					foreach ( $keywords as $index => $keyword ) {
						if ( $this->keyword_exists( trim( $keyword ), $phrase ) ) {
							$show = true; break;
						}
					}
				}
			}
		}

		return $this->compare( $show, true, $operator );
	}
}
