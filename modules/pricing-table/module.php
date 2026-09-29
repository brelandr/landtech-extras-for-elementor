<?php
namespace LandTechExtras\Modules\PricingTable;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pricing table + billing toggle.
 *
 * @since 2.8.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'pricing-table';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Pricing_Table',
			'Pricing_Toggle',
		);
	}
}
