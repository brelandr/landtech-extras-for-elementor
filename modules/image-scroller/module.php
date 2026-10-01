<?php
/**
 * Image scroller module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\ImageScroller;

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
		return 'image-scroller';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Image_Scroller' );
	}
}
