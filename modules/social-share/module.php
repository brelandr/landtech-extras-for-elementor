<?php
namespace LandTechExtras\Modules\SocialShare;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Social share buttons module.
 *
 * @since 2.9.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'social-share';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Social_Share',
		);
	}
}
