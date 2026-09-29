<?php
namespace LandTechExtras\Modules\DynamicTags\Tags\AcfFree;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF Free email field.
 *
 * @since 2.9.0
 */
class Acf_Email extends Acf_Free_Tag {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-acf-free-email';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'ACF Email Field', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function render() {
		$email = sanitize_email( (string) $this->get_field_raw() );
		echo esc_html( $email );
	}
}
