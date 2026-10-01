<?php
/**
 * Animated headline module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\AnimatedHeadline;

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
		return 'animated-headline';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Animated_Headline' );
	}
}
