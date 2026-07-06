<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Posts\Skins\Presets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Editorial preset skin for Posts Extra.
 *
 * @since 2.2.54
 */
class Skin_Posts_Editorial extends Skin_Posts_Preset_Base {

	/**
	 * @inheritDoc
	 */
	protected function get_preset_slug() {
		return 'editorial';
	}

	/**
	 * @inheritDoc
	 */
	protected function get_preset_title() {
		return __( 'Editorial', 'landtech-extras-for-elementor' );
	}
}
