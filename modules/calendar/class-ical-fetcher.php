<?php
/**
 * Remote iCal (.ics) fetch and transient cache.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\Calendar;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetch and cache remote .ics feeds via the WordPress HTTP API.
 *
 * @since 2.6.0
 */
class Ical_Fetcher {

	/**
	 * Return parsed events for a feed URL, using a fresh or stale transient when possible.
	 *
	 * On HTTP failure the last successful parse (stale transient) is returned so the
	 * frontend calendar can still render.
	 *
	 * @since 2.6.0
	 *
	 * @param string $url Feed URL.
	 * @param int    $ttl Cache TTL in seconds (minimum 300).
	 * @return array<int,array<string,mixed>> Parsed events, or an empty array.
	 */
	public function get_events( $url, $ttl = 3600 ) {
		$url = esc_url_raw( $url );
		if ( '' === $url ) {
			return array();
		}

		$ttl       = max( 300, (int) $ttl );
		$cache_key = 'ltxe_ical_' . md5( $url );
		$stale_key = $cache_key . '_stale';
		$cached    = get_transient( $cache_key );
		if ( false !== $cached && is_array( $cached ) ) {
			return $cached;
		}

		$events = $this->fetch_and_store( $url, $ttl );
		if ( is_array( $events ) ) {
			return $events;
		}

		$stale = get_transient( $stale_key );
		return is_array( $stale ) ? $stale : array();
	}

	/**
	 * Drop the fresh cache and re-fetch the feed.
	 *
	 * @since 2.6.0
	 *
	 * @param string $url Feed URL.
	 * @return bool True when the remote responded HTTP 200 and the body was stored.
	 */
	public function refresh( $url ) {
		$url = esc_url_raw( $url );
		if ( '' === $url ) {
			return false;
		}

		delete_transient( 'ltxe_ical_' . md5( $url ) );
		$events = $this->fetch_and_store( $url, 3600 );

		return is_array( $events );
	}

	/**
	 * GET the ICS URL, parse it, and write fresh + stale transients.
	 *
	 * @since 2.9.0
	 *
	 * @param string $url Feed URL (already passed through esc_url_raw()).
	 * @param int    $ttl Fresh-cache TTL in seconds.
	 * @return array<int,array<string,mixed>>|false Events on HTTP 200, false on transport or non-200.
	 */
	private function fetch_and_store( $url, $ttl ) {
		$response = wp_safe_remote_get(
			$url,
			array(
				'timeout' => 10,
			)
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$body   = wp_remote_retrieve_body( $response );
		$parser = new Ical_Parser();
		$events = $parser->parse( $body );

		$cache_key = 'ltxe_ical_' . md5( $url );
		set_transient( $cache_key, $events, $ttl );
		set_transient( $cache_key . '_stale', $events, WEEK_IN_SECONDS );

		return $events;
	}
}
