<?php
namespace LandTechExtras\Modules\DynamicTags\Tags\AcfFree;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF Free textarea field.
 *
 * @since 2.9.0
 */
class Acf_Textarea extends Acf_Free_Tag {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-acf-free-textarea';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'ACF Textarea Field', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function render() {
		echo wp_kses_post( (string) $this->get_field_raw() );
	}
}
