<?php
namespace LandTechExtras\Modules\Lottie;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lottie animation module.
 *
 * @since 2.2.102
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'lottie';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Lottie',
		);
	}
}
