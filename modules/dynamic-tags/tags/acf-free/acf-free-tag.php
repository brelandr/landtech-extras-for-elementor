<?php
namespace LandTechExtras\Modules\DynamicTags\Tags\AcfFree;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared ACF Free text-style tag.
 *
 * @since 2.9.0
 */
abstract class Acf_Free_Tag extends Tag {

	/**
	 * @inheritDoc
	 */
	public function get_group() {
		return 'ltxe-acf';
	}

	/**
	 * @inheritDoc
	 */
	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

	/**
	 * @inheritDoc
	 */
	protected function register_controls() {
		$this->add_control(
			'key',
			array(
				'label'       => __( 'Field Key or Name', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'field_abc123 or my_field_name',
			)
		);
	}

	/**
	 * Raw ACF value.
	 *
	 * @return mixed
	 */
	protected function get_field_raw() {
		if ( ! function_exists( 'get_field' ) ) {
			return '';
		}
		$key = $this->get_settings( 'key' );
		if ( ! is_string( $key ) || '' === $key ) {
			return '';
		}
		return get_field( $key );
	}
}
