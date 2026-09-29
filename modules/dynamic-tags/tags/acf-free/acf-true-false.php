<?php
namespace LandTechExtras\Modules\DynamicTags\Tags\AcfFree;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF Free true/false field.
 *
 * @since 2.9.0
 */
class Acf_True_False extends Acf_Free_Tag {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-acf-free-true-false';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'ACF True/False Field', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function render() {
		$value = $this->get_field_raw();
		echo esc_html( $value ? '1' : '0' );
	}
}
