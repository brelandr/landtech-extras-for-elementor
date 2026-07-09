<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

use LandTechExtras\Base\Condition;
use LandTechExtras\Modules\DisplayConditions\Viewport_Breakpoints;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Viewport visibility condition — composes with core block visibility breakpoints.
 *
 * @since 2.4.4
 */
class Viewport extends Condition {

	/**
	 * @return bool
	 */
	public static function is_supported() {

		if ( function_exists( 'landtech_extras_feature_enabled' ) ) {
			$default = version_compare( get_bloginfo( 'version' ), '7.0', '>=' );
			return landtech_extras_feature_enabled( 'platform_viewport_visibility', $default );
		}

		return true;
	}

	/**
	 * @return string
	 */
	public function get_group() {
		return 'visitor';
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return 'viewport';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Viewport', 'landtech-extras-for-elementor' );
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_value_control() {
		return array(
			'type'        => Controls_Manager::SELECT,
			'default'     => 'desktop',
			'label_block' => true,
			'options'     => array(
				'mobile'  => __( 'Mobile', 'landtech-extras-for-elementor' ),
				'tablet'  => __( 'Tablet', 'landtech-extras-for-elementor' ),
				'desktop' => __( 'Desktop', 'landtech-extras-for-elementor' ),
			),
		);
	}

	/**
	 * @param string      $operator Comparison operator.
	 * @param string|null $value    Viewport bucket.
	 * @param mixed       $name     Unused.
	 * @return bool
	 */
	public function check( $operator, $value, $name = null ) {

		unset( $name );

		$bucket = sanitize_key( (string) $value );
		if ( '' === $bucket ) {
			$bucket = 'desktop';
		}

		$show = Viewport_Breakpoints::matches_bucket( $bucket );

		return $this->compare( $show, true, $operator );
	}
}
