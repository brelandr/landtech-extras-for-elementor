<?php
/**
 * Tags cloud sphere module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\TagsCloudSphere;

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
		return 'tags-cloud-sphere';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Tags_Cloud_Sphere' );
	}
}
