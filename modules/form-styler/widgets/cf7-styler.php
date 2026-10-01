<?php
/**
 * Contact Form 7 styler widget.
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
class Cf7_Styler extends Form_Styler_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-cf7-styler';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'CF7 Styler', 'landtech-extras-for-elementor' );
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
		return array( 'form', 'cf7', 'contact form 7', 'styler' );
	}

	/**
	 * @inheritDoc
	 */
	protected function ltxe_form_tag() {
		return 'contact-form-7';
	}

	/**
	 * @inheritDoc
	 */
	protected function ltxe_form_plugin_active() {
		return defined( 'WPCF7_VERSION' ) || function_exists( 'wpcf7' );
	}

	/**
	 * @inheritDoc
	 */
	protected function ltxe_missing_plugin_notice() {
		return __( 'Contact Form 7 is not installed or active.', 'landtech-extras-for-elementor' );
	}
}
