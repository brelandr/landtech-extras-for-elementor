<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Navigation;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Buttons\Module
 *
 * @since  2.0.0
 */
class Module extends Module_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'navigation';
	}

	/**
	 * Get Widgets
	 * 
	 * Get the modules' widgets
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public function get_widgets() {
		return [
			'Offcanvas',
			'Slide_Menu',
		];
	}
}
