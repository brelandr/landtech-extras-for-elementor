<?php
/**
 * World clock module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\WorldClock;

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
		return 'world-clock';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'World_Clock' );
	}
}
