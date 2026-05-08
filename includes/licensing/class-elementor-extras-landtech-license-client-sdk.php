<?php
/**
 * Drop-in client SDK for premium plugins.
 *
 * @package LandTechLicenseServer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ElementorExtras_Landtech_License_Client_SDK', false ) ) {
	/**
	 * Minimal client for LandTech licensing update checks.
	 *
	 * When a shared secret is set, obtains a 24h (filterable server-side) Bearer token via
	 * POST /landtech-license/v1/access-token (HMAC bootstrap) and uses it on /check-update and /status,
	 * falling back to per-request HMAC if the token is missing or rejected.
	 *
	 * Product ID may be a positive WooCommerce product ID (int) or a string slug (e.g. elementor-extras).
	 * Do not use absint() alone — slugs must be sent as strings to the API.
	 */
	class ElementorExtras_Landtech_License_Client_SDK {

		/**
		 * Seconds before token expiry to refresh.
		 *
		 * @var int
		 */
		const TOKEN_REFRESH_BUFFER = 600;

		/**
		 * License server URL.
		 *
		 * @var string
		 */
		protected $server_url = '';

		/**
		 * Product ID for API JSON: positive int or non-empty string slug; null if invalid/unset.
		 *
		 * @var int|string|null
		 */
		protected $product_id = null;

		/**
		 * Shared secret (optional).
		 *
		 * @var string
		 */
		protected $shared_secret = '';

		/**
		 * Constructor.
		 *
		 * @param string     $server_url    Server URL.
		 * @param int|string $product_id    Numeric WC product ID or string slug.
		 * @param string     $shared_secret Shared secret.
		 */
		public function __construct( $server_url, $product_id, $shared_secret = '' ) {
			$this->server_url    = trailingslashit( esc_url_raw( $server_url ) );
			$this->product_id    = self::normalize_product_id( $product_id );
			$this->shared_secret = sanitize_text_field( (string) $shared_secret );
		}

		/**
		 * Normalize product id: positive integer or non-empty string slug; null if missing/invalid.
		 *
		 * @param mixed $product_id Raw from wp-config or option.
		 * @return int|string|null
		 */
		protected static function normalize_product_id( $product_id ) {
			if ( is_int( $product_id ) ) {
				return $product_id > 0 ? $product_id : null;
			}

			$s = '';
			if ( is_string( $product_id ) ) {
				$s = trim( $product_id );
			} elseif ( is_scalar( $product_id ) && ! is_bool( $product_id ) ) {
				$s = trim( (string) $product_id );
			}

			if ( '' === $s ) {
				return null;
			}

			if ( is_numeric( $s ) ) {
				$n = absint( $s );
				return $n > 0 ? $n : null;
			}

			return sanitize_text_field( $s );
		}

		/**
		 * @return bool
		 */
		protected function has_valid_product_id() {
			return null !== $this->product_id;
		}

		/**
		 * Stable fragment for transient/cache keys.
		 *
		 * @return string
		 */
		protected function product_id_cache_key() {
			if ( null === $this->product_id ) {
				return '';
			}
			return is_int( $this->product_id ) ? (string) $this->product_id : (string) $this->product_id;
		}

		/**
		 * Check update eligibility and retrieve package URL.
		 *
		 * @param string $license_key     License key.
		 * @param string $current_version Installed (or requested) version sent as current_version.
		 * @param string $target_version  Optional offered release for server latest_version resolution.
		 * @return array<string,mixed>|WP_Error
		 */
		public function check_update( $license_key, $current_version, $target_version = '' ) {
			$license_key = sanitize_text_field( $license_key );

			if ( empty( $license_key ) || empty( $this->server_url ) || ! $this->has_valid_product_id() ) {
				return new WP_Error( 'invalid_client_configuration', __( 'License client is not configured correctly.', 'elementor-extras' ) );
			}

			$body = array(
				'license_key'     => $license_key,
				'product_id'      => $this->product_id,
				'current_version' => sanitize_text_field( (string) $current_version ),
			);

			$target_version = sanitize_text_field( (string) $target_version );
			if ( '' !== $target_version ) {
				$body['target_version'] = $target_version;
			}

			return $this->post_landtech_endpoint( 'check-update', $license_key, $body, true );
		}

		/**
		 * POST /status — same auth as check_update.
		 *
		 * @param string $license_key License key.
		 * @return array<string,mixed>|WP_Error
		 */
		public function get_license_status( $license_key ) {
			$license_key = sanitize_text_field( $license_key );

			if ( empty( $license_key ) || empty( $this->server_url ) || ! $this->has_valid_product_id() ) {
				return new WP_Error( 'invalid_client_configuration', __( 'License client is not configured correctly.', 'elementor-extras' ) );
			}

			$body = array(
				'license_key' => $license_key,
				'product_id'  => $this->product_id,
			);

			return $this->post_landtech_endpoint( 'status', $license_key, $body, true );
		}

		/**
		 * Convenience helper for package URL extraction.
		 *
		 * @param string $license_key     License key.
		 * @param string $current_version Current (installed) version.
		 * @param string $target_version  Optional offered release.
		 * @return string|WP_Error
		 */
		public function get_package_url( $license_key, $current_version, $target_version = '' ) {
			$result = $this->check_update( $license_key, $current_version, $target_version );
			if ( is_wp_error( $result ) ) {
				return $result;
			}

			if ( empty( $result['package_url'] ) ) {
				return new WP_Error( 'missing_package_url', __( 'No package URL returned by licensing server.', 'elementor-extras' ) );
			}

			return esc_url_raw( (string) $result['package_url'] );
		}

		/**
		 * Transient key for cached Bearer token.
		 *
		 * @param string $license_key License key.
		 * @return string
		 */
		protected function access_token_transient_name( $license_key ) {
			return 'lt_lic_at_' . md5( $this->server_url . '|' . $this->product_id_cache_key() . '|' . $license_key );
		}

		/**
		 * Clear cached token (e.g. after invalid_auth).
		 *
		 * @param string $license_key License key.
		 * @return void
		 */
		public function clear_access_token_cache( $license_key ) {
			delete_transient( $this->access_token_transient_name( $license_key ) );
		}

		/**
		 * POST JSON to a landtech-license/v1 route with Bearer/HMAC auth and one HMAC retry on invalid_auth.
		 *
		 * @param string $route       Route segment (e.g. check-update).
		 * @param string $license_key License key.
		 * @param array  $body        JSON body.
		 * @param bool   $retry_hmac       Retry with HMAC only after invalid_auth.
		 * @param bool   $force_hmac_only  Skip Bearer; use timestamp signature only.
		 * @return array<string,mixed>|WP_Error
		 */
		protected function post_landtech_endpoint( $route, $license_key, $body, $retry_hmac = true, $force_hmac_only = false ) {
			$request_url = $this->server_url . 'wp-json/landtech-license/v1/' . ltrim( (string) $route, '/' );
			$headers     = $this->build_auth_headers( $license_key, $force_hmac_only );

			$response = wp_safe_remote_post(
				$request_url,
				array(
					'timeout' => 20,
					'headers' => array_merge(
						array(
							'Content-Type' => 'application/json',
							'Accept'       => 'application/json',
						),
						$headers
					),
					'body'    => wp_json_encode( $body ),
				)
			);

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$code = wp_remote_retrieve_response_code( $response );
			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( $code >= 400 ) {
				$error_code    = isset( $data['code'] ) ? sanitize_text_field( (string) $data['code'] ) : 'license_api_error';
				$error_message = isset( $data['message'] ) ? sanitize_text_field( (string) $data['message'] ) : __( 'License API request failed.', 'elementor-extras' );
				$error_data    = isset( $data['data'] ) && is_array( $data['data'] ) ? $data['data'] : array();
				$reason_code   = isset( $error_data['reason_code'] ) ? sanitize_key( (string) $error_data['reason_code'] ) : '';

				if ( $retry_hmac && 'check-update' === $route && 'invalid_auth' === $reason_code && ! empty( $this->shared_secret ) ) {
					$this->clear_access_token_cache( $license_key );
					return $this->post_landtech_endpoint( $route, $license_key, $body, false, true );
				}

				if ( $retry_hmac && 'status' === $route && 'invalid_auth' === $reason_code && ! empty( $this->shared_secret ) ) {
					$this->clear_access_token_cache( $license_key );
					return $this->post_landtech_endpoint( $route, $license_key, $body, false, true );
				}

				if ( 'check-update' === $route ) {
					$error_message = $this->build_human_error_message( $error_message, $reason_code, $error_data );
				}

				return new WP_Error( $error_code, $error_message, $error_data );
			}

			if ( ! is_array( $data ) ) {
				return new WP_Error( 'invalid_license_response', __( 'License server returned an invalid response.', 'elementor-extras' ) );
			}

			return $data;
		}

		/**
		 * Build Authorization / HMAC headers.
		 *
		 * @param string $license_key    License key.
		 * @param bool   $force_hmac_only Only HMAC (no Bearer).
		 * @return array<string,string>
		 */
		protected function build_auth_headers( $license_key, $force_hmac_only ) {
			$headers = array();

			if ( ! empty( $this->shared_secret ) && ! $force_hmac_only ) {
				$token = $this->get_cached_or_fetch_access_token( $license_key );
				if ( is_string( $token ) && '' !== $token ) {
					$headers['Authorization'] = 'Bearer ' . $token;
					return $headers;
				}
			}

			if ( ! empty( $this->shared_secret ) ) {
				$timestamp                       = time();
				$headers['X-Landtech-Timestamp'] = (string) $timestamp;
				$headers['X-Landtech-Signature'] = hash_hmac( 'sha256', $license_key . '|' . $timestamp, $this->shared_secret );
			}

			return $headers;
		}

		/**
		 * Return Bearer token string or empty if unavailable.
		 *
		 * @param string $license_key License key.
		 * @return string
		 */
		protected function get_cached_or_fetch_access_token( $license_key ) {
			$name = $this->access_token_transient_name( $license_key );
			$now  = time();
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Stable landtech_* SDK filter API (shared with licensing package).
			$buf  = (int) apply_filters( 'landtech_license_client_token_refresh_buffer', self::TOKEN_REFRESH_BUFFER );

			$cached = get_transient( $name );
			if ( is_array( $cached ) && ! empty( $cached['token'] ) && ! empty( $cached['exp'] ) && (int) $cached['exp'] > ( $now + $buf ) ) {
				return (string) $cached['token'];
			}

			$fetched = $this->fetch_access_token_remote( $license_key );
			if ( is_wp_error( $fetched ) || empty( $fetched['access_token'] ) || empty( $fetched['expires_at'] ) ) {
				return '';
			}

			$expires_at = (int) $fetched['expires_at'];
			$ttl        = max( 300, $expires_at - $now - $buf );
			$ttl        = min( $ttl, DAY_IN_SECONDS );

			set_transient(
				$name,
				array(
					'token' => (string) $fetched['access_token'],
					'exp'   => $expires_at,
				),
				$ttl
			);

			return (string) $fetched['access_token'];
		}

		/**
		 * POST /access-token with HMAC only.
		 *
		 * @param string $license_key License key.
		 * @return array<string,mixed>|WP_Error
		 */
		protected function fetch_access_token_remote( $license_key ) {
			if ( empty( $this->shared_secret ) ) {
				return new WP_Error( 'no_shared_secret', __( 'Shared secret is not configured.', 'elementor-extras' ) );
			}

			$url  = $this->server_url . 'wp-json/landtech-license/v1/access-token';
			$body = array(
				'license_key' => $license_key,
				'product_id'  => $this->product_id,
			);

			$timestamp                       = time();
			$headers                         = array(
				'Content-Type'                 => 'application/json',
				'Accept'                       => 'application/json',
				'X-Landtech-Timestamp'         => (string) $timestamp,
				'X-Landtech-Signature'         => hash_hmac( 'sha256', $license_key . '|' . $timestamp, $this->shared_secret ),
			);

			$response = wp_safe_remote_post(
				$url,
				array(
					'timeout' => 20,
					'headers' => $headers,
					'body'    => wp_json_encode( $body ),
				)
			);

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$code = wp_remote_retrieve_response_code( $response );
			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( $code >= 400 || ! is_array( $data ) || empty( $data['access_token'] ) ) {
				return new WP_Error( 'token_fetch_failed', __( 'Could not obtain access token from license server.', 'elementor-extras' ) );
			}

			return $data;
		}

		/**
		 * Human-readable errors for check-update.
		 *
		 * @param string              $fallback_message Message from API.
		 * @param string              $reason_code      Machine reason code.
		 * @param array<string,mixed> $error_data       Error payload data.
		 * @return string
		 */
		protected function build_human_error_message( $fallback_message, $reason_code, $error_data ) {
			$message = sanitize_text_field( (string) $fallback_message );

			if ( 'revoked' === $reason_code ) {
				$reason = isset( $error_data['revocation_reason'] ) ? sanitize_text_field( (string) $error_data['revocation_reason'] ) : '';
				$url    = isset( $error_data['renewal_url'] ) ? esc_url_raw( (string) $error_data['renewal_url'] ) : '';
				$message = __( 'This license has been revoked.', 'elementor-extras' );
				if ( ! empty( $reason ) ) {
					$message .= ' ' . sprintf(
						/* translators: %s: revocation reason */
						__( 'Reason: %s.', 'elementor-extras' ),
						$reason
					);
				}
				if ( ! empty( $url ) ) {
					$message .= ' ' . sprintf(
						/* translators: %s: renewal URL */
						__( 'Resolve access: %s', 'elementor-extras' ),
						$url
					);
				}
				return $message;
			}

			if ( 'updates_expired' === $reason_code ) {
				$url = isset( $error_data['renewal_url'] ) ? esc_url_raw( (string) $error_data['renewal_url'] ) : '';
				$message = __( 'Your update entitlement has expired.', 'elementor-extras' );
				if ( ! empty( $url ) ) {
					$message .= ' ' . sprintf(
						/* translators: %s: renewal URL */
						__( 'Renew here: %s', 'elementor-extras' ),
						$url
					);
				}
				return $message;
			}

			if ( 'license_not_found' === $reason_code ) {
				return __(
					'The LandTech server has no entitlement row for this key (common for License Manager keys created manually). Sync or import the key on the server, or match Settings → General license URL, product ID, and shared secret. The updater may still use GitHub when a token and release zip are available.',
					'elementor-extras'
				);
			}

			return $message;
		}
	}
}

if ( ! function_exists( 'elementor_extras_landtech_license_precheck_activation_blocked_message' ) ) {
	/**
	 * Optional client-side check before LMFWC activate: block when LandTech reports revoked (needs shared secret).
	 * Server-side REST enforcement remains authoritative when the store runs LandTech License Server.
	 *
	 * @param string               $license_key   Key to activate.
	 * @param string               $server_url    Store base URL.
	 * @param int|string           $product_id    Product ID (numeric) or slug.
	 * @param string               $shared_secret LandTech HMAC secret (empty = skip check).
	 * @return string Empty to allow LMFWC activation; non-empty user-facing message if blocked.
	 */
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Stable elementor_extras_* API.
	function elementor_extras_landtech_license_precheck_activation_blocked_message( $license_key, $server_url, $product_id, $shared_secret ) {
		$license_key   = sanitize_text_field( (string) $license_key );
		$server_url    = esc_url_raw( (string) $server_url );
		$shared_secret = sanitize_text_field( (string) $shared_secret );

		if ( '' === $license_key || '' === $server_url || '' === $shared_secret ) {
			return '';
		}

		if ( ! class_exists( 'ElementorExtras_Landtech_License_Client_SDK', false ) ) {
			return '';
		}

		$client = new ElementorExtras_Landtech_License_Client_SDK( $server_url, $product_id, $shared_secret );
		$st     = $client->get_license_status( $license_key );

		if ( is_wp_error( $st ) ) {
			$code = $st->get_error_code();
			if ( in_array( $code, array( 'license_revoked', 'invalid_product' ), true ) ) {
				return sanitize_text_field( $st->get_error_message() );
			}
			$data = $st->get_error_data();
			if ( is_array( $data ) ) {
				$reason = isset( $data['reason_code'] ) ? sanitize_key( (string) $data['reason_code'] ) : '';
				if ( 'revoked' === $reason ) {
					$msg = __( 'This license has been revoked on the entitlement server and cannot be activated.', 'elementor-extras' );
					if ( ! empty( $data['revocation_reason'] ) ) {
						$msg .= ' ' . sprintf(
							/* translators: %s: revocation reason */
							__( 'Reason: %s', 'elementor-extras' ),
							sanitize_text_field( (string) $data['revocation_reason'] )
						);
					}
					return $msg;
				}
			}
			return '';
		}

		if ( ! is_array( $st ) ) {
			return '';
		}

		$reason  = isset( $st['reason_code'] ) ? sanitize_key( (string) $st['reason_code'] ) : '';
		$revoked = ! empty( $st['is_revoked'] ) || 'revoked' === $reason;
		if ( ! $revoked ) {
			return '';
		}

		$msg = __( 'This license has been revoked on the entitlement server and cannot be activated.', 'elementor-extras' );
		if ( ! empty( $st['revocation_reason'] ) ) {
			$msg .= ' ' . sprintf(
				/* translators: %s: revocation reason */
				__( 'Reason: %s', 'elementor-extras' ),
				sanitize_text_field( (string) $st['revocation_reason'] )
			);
		}
		return $msg;
	}
}
