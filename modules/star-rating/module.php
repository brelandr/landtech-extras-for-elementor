<?php
namespace LandTechExtras\Modules\StarRating;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Star rating display module.
 *
 * @since 2.8.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'star-rating';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Star_Rating',
		);
	}
}
