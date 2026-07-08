<?php
/**
 * Schema.org widgets (free tier).
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\Schema;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.3.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'schema';
	}

	/**
	 * @inheritDoc
	 */
	public static function is_supported() {
		if ( function_exists( 'landtech_extras_feature_enabled' ) && ! landtech_extras_feature_enabled( 'schema_widgets', true ) ) {
			return false;
		}

		return parent::is_supported();
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Faq_Schema',
		);
	}
}
