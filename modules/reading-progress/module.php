<?php
namespace LandTechExtras\Modules\ReadingProgress;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reading progress bar module.
 *
 * @since 2.7.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'reading-progress';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Reading_Progress',
		);
	}
}
