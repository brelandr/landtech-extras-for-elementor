<?php
/**
 * News ticker module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\NewsTicker;

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
		return 'news-ticker';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'News_Ticker' );
	}
}
