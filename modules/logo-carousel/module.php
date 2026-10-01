<?php
/**
 * Logo carousel module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\LogoCarousel;

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
		return 'logo-carousel';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Logo_Carousel' );
	}
}
