<?php
namespace LandTechExtras\Modules\DynamicTags\Tags\AcfFree;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF Free number field.
 *
 * @since 2.9.0
 */
class Acf_Number extends Acf_Free_Tag {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-acf-free-number';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'ACF Number Field', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function render() {
		echo esc_html( (string) $this->get_field_raw() );
	}
}
