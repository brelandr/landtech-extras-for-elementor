<?php
/**
 * Number counter module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\NumberCounter;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.10.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'number-counter';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Number_Counter' );
	}
}
