<?php
/**
 * REST route to refresh a cached iCal feed from the Elementor editor.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\Calendar;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST: refresh cached iCal feeds.
 *
 * @since 2.6.0
 */
class Calendar_Rest {

	/**
	 * Register POST landtech-extras/v1/calendar/refresh-ical.
	 *
	 * @since 2.6.0
	 *
	 * @return void
	 */
	public static function register() {
		register_rest_route(
			'landtech-extras/v1',
			'/calendar/refresh-ical',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'refresh' ),
				'permission_callback' => array( __CLASS__, 'can_refresh' ),
				'args'                => array(
					'url' => array(
						'required'          => true,
						'sanitize_callback' => 'esc_url_raw',
						'validate_callback' => array( __CLASS__, 'is_http_url' ),
					),
				),
			)
		);
	}

	/**
	 * Editors refresh feeds they configured.
	 *
	 * @since 2.6.0
	 *
	 * @return bool
	 */
	public static function can_refresh() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Whether the value is an http or https URL.
	 *
	 * @since 2.9.0
	 *
	 * @param mixed $url Raw URL after sanitize_callback.
	 * @return bool
	 */
	public static function is_http_url( $url ) {
		$parts = wp_parse_url( (string) $url );
		if ( ! is_array( $parts ) || empty( $parts['scheme'] ) ) {
			return false;
		}

		return in_array( strtolower( (string) $parts['scheme'] ), array( 'http', 'https' ), true );
	}

	/**
	 * Re-fetch one iCal URL and replace its transients.
	 *
	 * Cookie REST nonce is required by WordPress for cookie-authenticated POSTs.
	 *
	 * @since 2.6.0
	 *
	 * @param \WP_REST_Request $request Request with sanitized `url`.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function refresh( $request ) {
		$url = esc_url_raw( (string) $request->get_param( 'url' ) );
		if ( '' === $url || ! self::is_http_url( $url ) ) {
			return new \WP_Error( 'ltxe_ical_url', __( 'Missing iCal URL.', 'landtech-extras-for-elementor' ), array( 'status' => 400 ) );
		}

		$fetcher = new Ical_Fetcher();
		$ok      = $fetcher->refresh( $url );
		if ( ! $ok ) {
			return new \WP_Error( 'ltxe_ical_refresh', __( 'Could not refresh this iCal feed.', 'landtech-extras-for-elementor' ), array( 'status' => 502 ) );
		}

		return rest_ensure_response(
			array(
				'ok' => true,
			)
		);
	}
}
