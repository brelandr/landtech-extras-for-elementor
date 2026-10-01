<?php
/**
 * WPForms styler widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\FormStyler\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/class-form-styler-widget.php';

/**
 * @since 2.10.0
 */
class Wpforms_Styler extends Form_Styler_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-wpforms-styler';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'WPForms Styler', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'form', 'wpforms', 'styler' );
	}

	/**
	 * @inheritDoc
	 */
	protected function ltxe_form_tag() {
		return 'wpforms';
	}

	/**
	 * @inheritDoc
	 */
	protected function ltxe_form_plugin_active() {
		return function_exists( 'wpforms' ) || defined( 'WPFORMS_VERSION' );
	}

	/**
	 * @inheritDoc
	 */
	protected function ltxe_missing_plugin_notice() {
		return __( 'WPForms is not installed or active.', 'landtech-extras-for-elementor' );
	}
}
