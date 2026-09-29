<?php
namespace LandTechExtras\Modules\CookieConsent;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cookie consent banner module.
 *
 * @since 2.9.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'cookie-consent';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Cookie_Consent',
		);
	}
}
