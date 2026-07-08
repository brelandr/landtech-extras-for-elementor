<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * UTM query parameter presets (GET).
 *
 * @since 2.2.102
 */
class Var_Utm extends Var_Get {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'var_utm';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'UTM parameter', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_name_control() {
		return array(
			'type'        => Controls_Manager::SELECT,
			'default'     => 'utm_source',
			'label_block' => true,
			'options'     => array(
				'utm_source'   => __( 'UTM Source', 'landtech-extras-for-elementor' ),
				'utm_medium'   => __( 'UTM Medium', 'landtech-extras-for-elementor' ),
				'utm_campaign' => __( 'UTM Campaign', 'landtech-extras-for-elementor' ),
				'utm_term'     => __( 'UTM Term', 'landtech-extras-for-elementor' ),
				'utm_content'  => __( 'UTM Content', 'landtech-extras-for-elementor' ),
			),
		);
	}
}
