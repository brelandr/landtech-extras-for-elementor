<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Tabs;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EE Tabs module.
 *
 * @since 3.0.0
 */
class Module extends Module_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'tabs';
	}

	/**
	 * @return string[]
	 */
	public function get_widgets() {
		return array(
			'Tabs',
		);
	}
}
