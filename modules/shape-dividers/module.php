<?php
/**
 * Shape dividers module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\ShapeDividers;

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
		return 'shape-dividers';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array();
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		require_once __DIR__ . '/extension.php';
		Shape_Dividers_Extension::instance();
	}
}
