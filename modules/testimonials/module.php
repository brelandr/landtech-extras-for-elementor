<?php
namespace LandTechExtras\Modules\Testimonials;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Testimonials / reviews carousel module.
 *
 * @since 2.9.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'testimonials';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Testimonials',
		);
	}
}
