<?php
/**
 * Flip box module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\FlipBox;

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
		return 'flip-box';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Flip_Box' );
	}
}
