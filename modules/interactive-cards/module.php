<?php
/**
 * Interactive cards module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\InteractiveCards;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.1.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'interactive-cards';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Interactive_Card' );
	}
}
