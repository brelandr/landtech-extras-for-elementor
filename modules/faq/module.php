<?php
namespace LandTechExtras\Modules\Faq;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FAQ accordion module (separate from the existing FAQ Schema widget).
 *
 * @since 2.9.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'faq';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Faq',
		);
	}
}
