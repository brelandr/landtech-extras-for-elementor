<?php
namespace LandTechExtras\Modules\Cta;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Call to action module.
 *
 * @since 2.9.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'cta';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Cta',
		);
	}
}
