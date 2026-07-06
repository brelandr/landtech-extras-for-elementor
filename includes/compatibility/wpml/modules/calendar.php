<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Compatibility\WPML;

use WPML_Elementor_Module_With_Items;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Calendar
 *
 * Registers translatable module with items
 *
 * @since 2.0.0
 */
class Calendar extends WPML_Elementor_Module_With_Items {

	/**
	 * @since 2.0.0
	 * @return string
	 */
	public function get_items_field() {
		return 'events';
	}

	/**
	 * Retrieve the fields inside the repeater
	 * 
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_fields() {
		return array(
			'title',
			'link' => array( 'url' ),
		);
	}

	/**
	 * Method for setting the title for each translatable field
	 *
	 * @since 2.0.0
	 *
	 * @param string    $field The name of the field
	 * @return string
	 */
	protected function get_title( $field ) {
		if ( 'title' === $field ) {
			return esc_html__( 'Calendar: Event Title', 'landtech-extras-for-elementor' );
		}

		if ( 'url' === $field ) {
			return esc_html__( 'Calendar: Event Link', 'landtech-extras-for-elementor' );
		}

		return '';
	}

	/**
	 * Method for determining the editor type for each field
	 * @since 2.0.0
	 *
	 * @param  string    $field Name of the field
	 * @return string
	 */
	protected function get_editor_type( $field ) {

		switch( $field ) {
			case 'text':
			case 'url':
				return 'LINE';
	 
			default:
				return '';
		 }
	}

}
