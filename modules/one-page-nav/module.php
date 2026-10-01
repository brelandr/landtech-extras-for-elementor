<?php
/**
 * One page navigation module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\OnePageNav;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.10.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'one-page-nav';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Dot_Nav' );
	}
}
