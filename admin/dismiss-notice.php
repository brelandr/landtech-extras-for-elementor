<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Dismiss_Notice {

	/**
	 * Init hooks.
	 *
	 * @since 1.8.4
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_script' ) );
		// AJAX: wp_ajax_landtech_extras_dismiss_notice + check_ajax_referer( 'dismissible-notice', 'nonce' ).
		add_action( 'wp_ajax_landtech_extras_dismiss_notice', array( __CLASS__, 'dismiss_admin_notice' ) );
	}

	/**
	 * Enqueue javascript and variables.
	 *
	 * @since 1.8.4
	 */
	public static function enqueue_script() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( is_customize_preview() ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_script(
			'landtech-extras-notices',
			plugins_url( '/assets/js/notice' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			array( 'jquery', 'common' ),
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_localize_script(
			'landtech-extras-notices',
			'landtechExtrasDismissibleNotice',
			array(
				/** Nonce action matches {@see dismiss_admin_notice()} `check_ajax_referer( 'dismissible-notice', … )`. */
				'nonce' => wp_create_nonce( 'dismissible-notice' ),
			)
		);
	}

	/**
	 * Normalize a data-dismissible attribute to the site transient key (hyphenated, prefixed).
	 *
	 * @since 2.2.75
	 *
	 * @param string $arg Raw `data-dismissible` value.
	 * @return string Transient key or empty string when invalid.
	 */
	public static function get_dismissible_transient_key( $arg ) {
		$arg = (string) $arg;

		if ( '' === $arg ) {
			return '';
		}

		$parts = explode( '-', $arg );
		if ( count( $parts ) < 2 ) {
			return '';
		}

		array_pop( $parts );
		$key = implode( '-', $parts );

		if ( ! self::is_valid_dismissible_transient_key( $key ) ) {
			return '';
		}

		return $key;
	}

	/**
	 * Whether a dismissible notice transient key uses an allowed plugin prefix.
	 *
	 * @since 2.2.75
	 *
	 * @param string $key Transient key without the trailing duration segment.
	 * @return bool
	 */
	public static function is_valid_dismissible_transient_key( $key ) {
		return (bool) preg_match( '/^(?:landtech-extras|ltxe)-[a-z0-9-]+$/i', (string) $key );
	}

	/**
	 * AJAX callback: persist dismissal of an administrator notice keyed by `$option_name` transient key.
	 *
	 * Security: {@see check_ajax_referer()} for action `dismissible-notice` and request field `nonce`,
	 * then requires `manage_options`. POST fields are sanitized after checks.
	 *
	 * @since 1.8.4
	 * @return void
	 */
	public static function dismiss_admin_notice() {

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified via check_ajax_referer; no other POST use before this.
		if ( ! check_ajax_referer( 'dismissible-notice', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid nonce.', 'landtech-extras-for-elementor' ) ), 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Forbidden.', 'landtech-extras-for-elementor' ) ), 403 );
		}

		$option_name = isset( $_POST['option_name'] ) ? sanitize_text_field( wp_unslash( $_POST['option_name'] ) ) : '';
		if ( strlen( $option_name ) > 191 || ! self::is_valid_dismissible_transient_key( $option_name ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid dismissal key.', 'landtech-extras-for-elementor' ) ), 400 );
		}

		$dismissible_length_raw = isset( $_POST['dismissible_length'] ) ? sanitize_text_field( wp_unslash( $_POST['dismissible_length'] ) ) : '';

		$transient           = 0;
		$dismissible_storage = $dismissible_length_raw;

		if ( 'forever' !== $dismissible_length_raw ) {
			$dismissible_length_days = absint( $dismissible_length_raw );
			if ( 0 === $dismissible_length_days ) {
				$dismissible_length_days = 1;
			}
			$transient           = $dismissible_length_days * DAY_IN_SECONDS;
			$dismissible_storage = strtotime( (string) $dismissible_length_days . ' days' );
		}

		set_site_transient( $option_name, $dismissible_storage, $transient );
		wp_send_json_success();
	}

	/**
	 * Is admin notice active?
	 *
	 * @since 1.8.4
	 * @param string $arg data-dismissible content of notice.
	 * @return bool
	 */
	public static function is_admin_notice_active( $arg ) {

		$option_name = self::get_dismissible_transient_key( $arg );
		if ( '' === $option_name ) {
			return true;
		}

		$db_record = get_site_transient( $option_name );

		if ( 'forever' === $db_record ) {
			return false;
		}

		if ( absint( $db_record ) >= time() ) {
			return false;
		}

		return true;
	}
}
