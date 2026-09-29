<?php
namespace LandTechExtras\Modules\Countdown;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fixed-datetime countdown module (free).
 *
 * @since 2.7.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'countdown';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Countdown',
		);
	}
}
