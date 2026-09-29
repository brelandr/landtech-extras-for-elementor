<?php
namespace LandTechExtras\Modules\ProgressBar;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Linear progress bar module.
 *
 * @since 2.7.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'progress-bar';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Progress_Bar',
		);
	}
}
