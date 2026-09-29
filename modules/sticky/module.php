<?php
namespace LandTechExtras\Modules\Sticky;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sticky element wrapper module.
 *
 * @since 2.9.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'sticky';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Sticky_Wrapper',
		);
	}
}
