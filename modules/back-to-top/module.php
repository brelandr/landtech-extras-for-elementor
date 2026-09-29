<?php
namespace LandTechExtras\Modules\BackToTop;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Back to top button module.
 *
 * @since 2.9.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'back-to-top';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Back_To_Top',
		);
	}
}
