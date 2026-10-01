<?php
/**
 * Media Gallery module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\MediaGallery;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.0.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'media-gallery';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Media_Gallery' );
	}
}
