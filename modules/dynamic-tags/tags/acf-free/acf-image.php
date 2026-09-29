<?php
namespace LandTechExtras\Modules\DynamicTags\Tags\AcfFree;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF Free image field.
 *
 * @since 2.9.0
 */
class Acf_Image extends Data_Tag {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-acf-free-image';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'ACF Image Field', 'landtech-extras-for-elementor' );
	}

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
		return array( TagsModule::IMAGE_CATEGORY );
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
	 * @inheritDoc
	 */
	public function get_value( array $options = array() ) {
		if ( ! function_exists( 'get_field' ) ) {
			return array(
				'id'  => 0,
				'url' => '',
			);
		}
		$key = $this->get_settings( 'key' );
		if ( ! is_string( $key ) || '' === $key ) {
			return array(
				'id'  => 0,
				'url' => '',
			);
		}
		$field = get_field( $key );
		if ( is_array( $field ) && ! empty( $field['ID'] ) ) {
			return array(
				'id'  => absint( $field['ID'] ),
				'url' => isset( $field['url'] ) ? esc_url_raw( (string) $field['url'] ) : '',
			);
		}
		if ( is_numeric( $field ) ) {
			$id = absint( $field );
			return array(
				'id'  => $id,
				'url' => $id ? (string) wp_get_attachment_url( $id ) : '',
			);
		}
		if ( is_string( $field ) ) {
			return array(
				'id'  => 0,
				'url' => esc_url_raw( $field ),
			);
		}
		return array(
			'id'  => 0,
			'url' => '',
		);
	}
}
