<?php
/**
 * Recipe module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\Recipe;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.1.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'recipe';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Recipe' );
	}
}
