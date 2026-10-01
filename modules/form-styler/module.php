<?php
/**
 * Form styler module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\FormStyler;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.10.0
 */
class Module extends Module_Base {

	/**
	 * User meta set when an editor dismisses the Gravity Forms upsell.
	 *
	 * @since 3.0.0
	 */
	const UPSELL_DISMISS_META = 'ltxe_gf_upsell_dismissed';

	/**
	 * Nonce action for the editor dismiss request.
	 *
	 * @since 3.0.0
	 */
	const UPSELL_NONCE_ACTION = 'landtech_extras_dismiss_gf_upsell';

	/**
	 * Hook editor assets and the dismiss handler.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_gf_upsell' ) );
		add_action( 'wp_ajax_landtech_extras_dismiss_gf_upsell', array( $this, 'ajax_dismiss_gf_upsell' ) );
	}

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'form-styler';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Cf7_Styler',
			'Wpforms_Styler',
		);
	}

	/**
	 * Whether the Elementor editor should show the Gravity Forms Premium notice.
	 *
	 * Gravity Forms is active, Premium is not entitled, and this user has not dismissed it.
	 * The enqueue hook only runs inside the Elementor editor, so the notice never prints on the frontend.
	 *
	 * @since 3.0.0
	 * @return bool
	 */
	public function should_show_gf_upsell() {
		if ( ! class_exists( 'GFForms' ) ) {
			return false;
		}
		if ( function_exists( 'landtech_extras_premium_addon_runtime_entitled' ) && landtech_extras_premium_addon_runtime_entitled() ) {
			return false;
		}
		$user_id = get_current_user_id();
		if ( $user_id < 1 ) {
			return false;
		}
		return '1' !== (string) get_user_meta( $user_id, self::UPSELL_DISMISS_META, true );
	}

	/**
	 * Enqueue the panel notice inside the Elementor editor.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function enqueue_gf_upsell() {
		if ( ! $this->should_show_gf_upsell() ) {
			return;
		}

		$style_path = LANDTECH_EXTRAS_PATH . 'assets/css/form-styler-gf-upsell.css';
		$script_path = LANDTECH_EXTRAS_PATH . 'assets/js/form-styler-gf-upsell.js';
		$version     = LANDTECH_EXTRAS_VERSION;
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			if ( is_readable( $style_path ) ) {
				$version = (string) filemtime( $style_path );
			}
		}

		wp_enqueue_style(
			'landtech-extras-form-styler-gf-upsell',
			plugins_url( 'assets/css/form-styler-gf-upsell.css', LANDTECH_EXTRAS__FILE__ ),
			array(),
			$version
		);

		$script_ver = $version;
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && is_readable( $script_path ) ) {
			$script_ver = (string) filemtime( $script_path );
		}

		wp_enqueue_script(
			'landtech-extras-form-styler-gf-upsell',
			plugins_url( 'assets/js/form-styler-gf-upsell.js', LANDTECH_EXTRAS__FILE__ ),
			array(),
			$script_ver,
			true
		);

		wp_localize_script(
			'landtech-extras-form-styler-gf-upsell',
			'landtechExtrasGfUpsell',
			array(
				'message'    => esc_html__( 'Using Gravity Forms? Style your forms with the Gravity Forms Styler — available in', 'landtech-extras-for-elementor' ),
				'linkLabel'  => esc_html__( 'LandTech Extras Premium', 'landtech-extras-for-elementor' ),
				'dismiss'    => esc_html__( 'Dismiss', 'landtech-extras-for-elementor' ),
				'url'        => esc_url( $this->gf_upsell_url() ),
				'ajaxUrl'    => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'      => wp_create_nonce( self::UPSELL_NONCE_ACTION ),
				'action'     => 'landtech_extras_dismiss_gf_upsell',
			)
		);
	}

	/**
	 * Upgrade URL for the Gravity Forms styler notice.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	private function gf_upsell_url() {
		$url = 'https://extrasforelementor.com/#premium';
		/**
		 * Filter the Gravity Forms styler upsell URL shown in the Elementor editor.
		 *
		 * @since 3.0.0
		 *
		 * @param string $url Default extrasforelementor.com premium anchor.
		 */
		$filtered = apply_filters( 'landtech_extras/form_styler_gf_upsell_url', $url );
		if ( ! is_string( $filtered ) || '' === $filtered ) {
			return $url;
		}
		return $filtered;
	}

	/**
	 * Store a per-user dismissal of the editor notice.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function ajax_dismiss_gf_upsell() {
		check_ajax_referer( self::UPSELL_NONCE_ACTION, 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'You cannot dismiss this notice.', 'landtech-extras-for-elementor' ),
				),
				403
			);
		}

		update_user_meta( get_current_user_id(), self::UPSELL_DISMISS_META, '1' );
		wp_send_json_success();
	}
}
