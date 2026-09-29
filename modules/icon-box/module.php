<?php
namespace LandTechExtras\Modules\IconBox;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Icon box / feature box module.
 *
 * @since 2.9.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'icon-box';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Icon_Box',
		);
	}
}
