<?php
/**
 * Image accordion module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\ImageAccordion;

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
		return 'image-accordion';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Image_Accordion' );
	}
}
