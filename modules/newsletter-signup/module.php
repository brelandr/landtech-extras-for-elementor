<?php
/**
 * Newsletter signup module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\NewsletterSignup;

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
		return 'newsletter-signup';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Newsletter_Signup' );
	}
}
