<?php
/**
 * Comparison table module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\ComparisonTable;

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
		return 'comparison-table';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Comparison_Table' );
	}
}
