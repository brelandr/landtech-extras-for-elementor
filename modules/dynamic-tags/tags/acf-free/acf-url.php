<?php
namespace LandTechExtras\Modules\DynamicTags\Tags\AcfFree;

use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF Free URL field.
 *
 * @since 2.9.0
 */
class Acf_Url extends Acf_Free_Tag {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-acf-free-url';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'ACF URL Field', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_categories() {
		return array( TagsModule::URL_CATEGORY, TagsModule::TEXT_CATEGORY );
	}

	/**
	 * @inheritDoc
	 */
	public function render() {
		echo esc_url( (string) $this->get_field_raw() );
	}
}
