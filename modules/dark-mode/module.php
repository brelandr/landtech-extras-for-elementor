<?php
namespace LandTechExtras\Modules\DarkMode;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dark mode toggle module.
 *
 * @since 2.9.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'dark-mode';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Dark_Mode_Toggle',
		);
	}
}
