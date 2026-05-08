<?php
/**
 * Daily LMFWC license validation via WP-Cron (runs without admin traffic).
 *
 * @package ElementorExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ElementorExtras_License_Cron' ) ) {
	/**
	 * Registers and runs scheduled license validation.
	 */
	class ElementorExtras_License_Cron {

		const OPT_LICENSE_KEY        = 'elementor_extras_license_key';
		const OPT_LICENSE_STAT       = 'elementor_extras_license_status';
		const OPT_LICENSE_LAST_CHECK = 'elementor_extras_license_last_check';
		const CRON_DAILY_LICENSE     = 'elementor_extras_daily_license_validation';

		/**
		 * Last validate_stored_license() detail when result is null (admin messaging).
		 *
		 * @var array{reason:string,http_code:int}
		 */
		private static $last_validation_meta = array(
			'reason'    => '',
			'http_code' => 0,
		);

		/**
		 * Register cron hooks once.
		 *
		 * @return void
		 */
		public static function register_daily_license_cron() {
			static $registered = false;
			if ( $registered ) {
				return;
			}
			$registered = true;
			add_action( 'init', array( __CLASS__, 'schedule_daily_license_cron' ), 20 );
			add_action( self::CRON_DAILY_LICENSE, array( __CLASS__, 'cron_validate_license' ) );
		}

		/**
		 * Schedule daily validation if missing.
		 *
		 * @return void
		 */
		public static function schedule_daily_license_cron() {
			if ( wp_next_scheduled( self::CRON_DAILY_LICENSE ) ) {
				return;
			}
			wp_schedule_event( time() + wp_rand( 300, 3600 ), 'daily', self::CRON_DAILY_LICENSE );
		}

		/**
		 * Cron callback.
		 *
		 * @return void
		 */
		public static function cron_validate_license() {
			$key     = get_option( self::OPT_LICENSE_KEY, '' );
			$trusted = function_exists( 'elementor_extras_site_has_trusted_demo_license' ) && elementor_extras_site_has_trusted_demo_license();
			if ( empty( $key ) && ! $trusted ) {
				return;
			}
			self::validate_stored_license( 'cron' );
		}

		/**
		 * Validate the stored key with LMFWC (GET validate endpoint + Basic Auth).
		 *
		 * On transport errors or missing API credentials, leaves existing status unchanged.
		 *
		 * @param string $run_source cron|manual for diagnostics.
		 * @return bool|null True if valid, false if server says invalid (or empty key), null if not validated (network/credentials).
		 */
		public static function validate_stored_license( $run_source = 'cron' ) {
			self::$last_validation_meta = array(
				'reason'    => '',
				'http_code' => 0,
			);

			if ( function_exists( 'elementor_extras_license_debug_enabled' ) && elementor_extras_license_debug_enabled() && class_exists( 'ElementorExtras_License_Debug' ) ) {
				ElementorExtras_License_Debug::reset( sanitize_key( (string) $run_source ) );
			}

			if ( function_exists( 'elementor_extras_site_has_trusted_demo_license' ) && elementor_extras_site_has_trusted_demo_license() ) {
				update_option( self::OPT_LICENSE_STAT, 'valid' );
				self::debug_save(
					array(
						'outcome'           => 'valid',
						'reason'            => 'trusted_demo_host',
						'wpr_option_status' => 'valid',
					)
				);
				return true;
			}

			$license_key = get_option( self::OPT_LICENSE_KEY, '' );
			if ( function_exists( 'elementor_extras_license_mask_key' ) && class_exists( 'ElementorExtras_License_Debug' ) && function_exists( 'elementor_extras_license_debug_enabled' ) && elementor_extras_license_debug_enabled() ) {
				ElementorExtras_License_Debug::add(
					'license_key',
					array(
						'masked' => elementor_extras_license_mask_key( $license_key ),
					)
				);
			}

			if ( empty( $license_key ) ) {
				self::debug_save(
					array(
						'outcome'           => 'invalid',
						'reason'            => 'empty_license_key',
						'wpr_option_status' => get_option( self::OPT_LICENSE_STAT, '' ),
					)
				);
				update_option( self::OPT_LICENSE_STAT, 'invalid' );
				return false;
			}

			$consumer_key    = elementor_extras_get_consumer_key();
			$consumer_secret = elementor_extras_get_consumer_secret();
			if ( empty( $consumer_key ) || empty( $consumer_secret ) ) {
				self::$last_validation_meta = array(
					'reason'    => 'missing_lmfwc_consumer_credentials',
					'http_code' => 0,
				);
				self::debug_save(
					array(
						'outcome' => 'skipped',
						'reason'  => 'missing_lmfwc_consumer_credentials',
					)
				);
				return null;
			}

			if ( function_exists( 'elementor_extras_license_debug_enabled' ) && elementor_extras_license_debug_enabled() && class_exists( 'ElementorExtras_License_Debug' ) ) {
				ElementorExtras_License_Debug::add(
					'lmfwc_credentials',
					array(
						'consumer_key_prefix' => substr( (string) $consumer_key, 0, 7 ) . '…',
					)
				);
			}

			$base = elementor_extras_get_license_server_url();
			if ( empty( $base ) && defined( 'ELEMENTOR_EXTRAS_STORE_URL' ) ) {
				$base = ELEMENTOR_EXTRAS_STORE_URL;
			}
			$base = trailingslashit( esc_url_raw( $base ) );

			$encoded = rawurlencode( $license_key );
			$encoded = str_replace( '.', '%2E', $encoded );
			$url     = $base . 'wp-json/lmfwc/v2/licenses/validate/' . $encoded;
			if ( function_exists( 'elementor_extras_lmfwc_request_url_with_query_auth' ) ) {
				$url = elementor_extras_lmfwc_request_url_with_query_auth( $url, $consumer_key, $consumer_secret );
			}

			if ( function_exists( 'elementor_extras_license_debug_enabled' ) && elementor_extras_license_debug_enabled() && class_exists( 'ElementorExtras_License_Debug' ) ) {
				$safe_url = function_exists( 'elementor_extras_lmfwc_redact_url_for_log' ) ? elementor_extras_lmfwc_redact_url_for_log( $url ) : $url;
				ElementorExtras_License_Debug::add(
					'lmfwc_request',
					array(
						'validate_url' => $safe_url,
						'auth_mode'    => 'query_string',
					)
				);
			}

			$response = wp_remote_get(
				$url,
				array(
					'timeout'   => 20,
					'sslverify' => true,
					'headers'   => array(
						'Accept' => 'application/json',
					),
				)
			);

			update_option( self::OPT_LICENSE_LAST_CHECK, time() );

			if ( is_wp_error( $response ) ) {
				self::$last_validation_meta = array(
					'reason'    => 'lmfwc_transport_error',
					'http_code' => 0,
				);
				self::debug_save(
					array(
						'outcome' => 'unchanged',
						'reason'  => 'lmfwc_transport_error',
						'error'   => $response->get_error_message(),
					)
				);
				return null;
			}

			$response_code = wp_remote_retrieve_response_code( $response );
			$raw_body      = wp_remote_retrieve_body( $response );
			$body          = json_decode( $raw_body, true );

			if ( function_exists( 'elementor_extras_license_debug_enabled' ) && elementor_extras_license_debug_enabled() && class_exists( 'ElementorExtras_License_Debug' ) ) {
				ElementorExtras_License_Debug::add(
					'lmfwc_response',
					array(
						'http_code'    => (int) $response_code,
						'body_preview' => ElementorExtras_License_Debug::truncate( $raw_body, 1200 ),
					)
				);
			}

			$lmfwc_result = self::interpret_lmfwc_validate_response( $response_code, $body );

			if ( function_exists( 'elementor_extras_license_debug_enabled' ) && elementor_extras_license_debug_enabled() && class_exists( 'ElementorExtras_License_Debug' ) ) {
				$parsed_label = ( true === $lmfwc_result ) ? 'yes' : ( ( false === $lmfwc_result ) ? 'no' : 'inconclusive' );
				ElementorExtras_License_Debug::add(
					'lmfwc_parse',
					array(
						'parsed_valid' => $parsed_label,
					)
				);
			}

			// Inconclusive (5xx, timeouts, parse issues, auth noise): never demote a previously valid key.
			if ( null === $lmfwc_result ) {
				$hc = (int) $response_code;
				$mr = 'lmfwc_inconclusive';
				if ( 401 === $hc ) {
					$mr = 'lmfwc_store_api_unauthorized';
				} elseif ( 403 === $hc ) {
					$mr = 'lmfwc_store_api_forbidden';
				} elseif ( 400 === $hc ) {
					$mr = 'lmfwc_store_api_bad_request';
				}
				self::$last_validation_meta = array(
					'reason'    => $mr,
					'http_code' => $hc,
				);
				self::debug_save(
					array(
						'outcome'             => 'unchanged',
						'reason'              => 'lmfwc_inconclusive',
						'lmfwc_detail_reason' => $mr,
						'http_code'           => $hc,
						'wpr_option_status'   => get_option( self::OPT_LICENSE_STAT, '' ),
					)
				);
				return null;
			}

			if ( false === $lmfwc_result ) {
				self::clear_landtech_status_snapshot();
				update_option( self::OPT_LICENSE_STAT, 'invalid' );
				self::debug_save(
					array(
						'outcome'           => 'invalid',
						'reason'            => 'lmfwc_validate_failed',
						'wpr_option_status' => 'invalid',
					)
				);
				return false;
			}

			$landtech = self::evaluate_landtech_subscription_gate( $license_key );
			if ( false === $landtech ) {
				update_option( self::OPT_LICENSE_STAT, 'invalid' );
				self::debug_save(
					array(
						'outcome'           => 'invalid',
						'reason'            => 'landtech_blocked',
						'wpr_option_status' => 'invalid',
					)
				);
				return false;
			}

			if ( null === $landtech ) {
				if ( function_exists( 'elementor_extras_license_debug_enabled' ) && elementor_extras_license_debug_enabled() && class_exists( 'ElementorExtras_License_Debug' ) ) {
					ElementorExtras_License_Debug::add(
						'landtech_gate',
						array(
							'result' => 'skipped_or_inconclusive',
						)
					);
				}
			}

			update_option( self::OPT_LICENSE_STAT, 'valid' );
			self::debug_save(
				array(
					'outcome'           => 'valid',
					'reason'            => 'lmfwc_ok_landtech_ok_or_skipped',
					'wpr_option_status' => 'valid',
				)
			);
			return true;
		}

		/**
		 * Snapshot from the last validate_stored_license() run (use when return value is null).
		 *
		 * @return array{reason:string,http_code:int} Reason codes include missing_lmfwc_consumer_credentials, lmfwc_transport_error, lmfwc_store_api_unauthorized, lmfwc_inconclusive, etc.
		 */
		public static function get_last_validation_meta() {
			return self::$last_validation_meta;
		}

		/**
		 * Write diagnostics summary when debug is on.
		 *
		 * @param array<string,mixed> $summary Summary row.
		 * @return void
		 */
		private static function debug_save( array $summary ) {
			if ( ! function_exists( 'elementor_extras_license_debug_enabled' ) || ! elementor_extras_license_debug_enabled() ) {
				return;
			}
			if ( ! class_exists( 'ElementorExtras_License_Debug' ) ) {
				return;
			}
			ElementorExtras_License_Debug::save_final( $summary );
		}

		/**
		 * After LMFWC validates the key, consult LandTech License Server when shared secret is configured.
		 *
		 * Catches cancelled/expired trials and revocations that LMFWC may still report as "active".
		 *
		 * @param string $license_key License key.
		 * @return bool|null False if Pro must be blocked; true if LandTech explicitly allows; null if skipped or inconclusive.
		 */
		private static function evaluate_landtech_subscription_gate( $license_key ) {
			$secret = function_exists( 'elementor_extras_get_license_shared_secret' ) ? elementor_extras_get_license_shared_secret() : '';
			if ( empty( $secret ) ) {
				self::landtech_debug_add(
					'landtech_skip',
					array(
						'reason' => 'no_shared_secret_configured',
					)
				);
				return null;
			}

			if ( ! class_exists( 'ElementorExtras_Landtech_License_Client_SDK', false ) ) {
				self::landtech_debug_add(
					'landtech_skip',
					array(
						'reason' => 'sdk_class_missing',
					)
				);
				return null;
			}

			$base_url = function_exists( 'elementor_extras_get_license_server_url' ) ? elementor_extras_get_license_server_url() : '';
			if ( empty( $base_url ) && defined( 'ELEMENTOR_EXTRAS_STORE_URL' ) ) {
				$base_url = ELEMENTOR_EXTRAS_STORE_URL;
			}
			$base_url = esc_url_raw( (string) $base_url );
			if ( empty( $base_url ) ) {
				self::landtech_debug_add(
					'landtech_skip',
					array(
						'reason' => 'empty_license_server_url',
					)
				);
				return null;
			}

			$product_id = function_exists( 'elementor_extras_get_license_product_id' ) ? elementor_extras_get_license_product_id() : '';
			if ( empty( $product_id ) && defined( 'ELEMENTOR_EXTRAS_PRODUCT_ID' ) ) {
				$product_id = ELEMENTOR_EXTRAS_PRODUCT_ID;
			}

			self::landtech_debug_add(
				'landtech_request',
				array(
					'status_url' => trailingslashit( $base_url ) . 'wp-json/landtech-license/v1/status',
					'product_id' => (string) $product_id,
					'has_hmac'   => 'yes',
				)
			);

			$client = new ElementorExtras_Landtech_License_Client_SDK( $base_url, (string) $product_id, $secret );
			$lt     = $client->get_license_status( $license_key );

			if ( is_wp_error( $lt ) ) {
				$code = $lt->get_error_code();
				self::landtech_debug_add(
					'landtech_response_error',
					array(
						'wp_error_code'    => $code,
						'wp_error_message' => $lt->get_error_message(),
					)
				);
				if ( 'license_not_found' === $code ) {
					self::clear_landtech_status_snapshot();
				}
				return null;
			}

			$sub_status = isset( $lt['subscription_status'] ) ? sanitize_key( (string) $lt['subscription_status'] ) : '';
			$reason     = isset( $lt['reason_code'] ) ? sanitize_key( (string) $lt['reason_code'] ) : '';
			$purchase   = isset( $lt['purchase_type'] ) ? sanitize_key( (string) $lt['purchase_type'] ) : '';

			self::landtech_debug_add(
				'landtech_response_ok',
				array(
					'subscription_status'  => $sub_status,
					'reason_code'          => $reason,
					'purchase_type'        => $purchase,
					'eligible_for_updates' => ! empty( $lt['eligible_for_updates'] ) ? 'yes' : 'no',
					'is_revoked'           => ! empty( $lt['is_revoked'] ) ? 'yes' : 'no',
					'payload_preview'      => class_exists( 'ElementorExtras_License_Debug' ) ? ElementorExtras_License_Debug::truncate( $lt, 1500 ) : '',
				)
			);

			update_option( 'elementor_extras_landtech_subscription_status', $sub_status );
			update_option( 'elementor_extras_landtech_eligible_for_updates', ! empty( $lt['eligible_for_updates'] ) ? '1' : '0' );
			update_option( 'elementor_extras_landtech_reason_code', $reason );
			update_option( 'elementor_extras_landtech_purchase_type', $purchase );
			if ( isset( $lt['is_revoked'] ) ) {
				update_option( 'elementor_extras_landtech_is_revoked', ! empty( $lt['is_revoked'] ) ? '1' : '0' );
			}

			if ( ! empty( $lt['is_revoked'] ) || 'revoked' === $reason ) {
				self::landtech_debug_add(
					'landtech_decision',
					array(
						'block'  => 'yes',
						'reason' => 'revoked',
					)
				);
				return false;
			}

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Stable `elementor_extras_*` LandTech license hook API.
			$terminal = apply_filters(
				'elementor_extras_landtech_terminal_subscription_statuses',
				array( 'cancelled', 'expired', 'failed' ),
				$lt,
				$license_key
			);
			$terminal = is_array( $terminal ) ? $terminal : array( 'cancelled', 'expired', 'failed' );
			$terminal = array_map( 'sanitize_key', $terminal );

			if ( in_array( $sub_status, $terminal, true ) ) {
				self::landtech_debug_add(
					'landtech_decision',
					array(
						'block'               => 'yes',
						'reason'              => 'terminal_subscription_status',
						'subscription_status' => $sub_status,
						'terminal_list'       => implode( ',', $terminal ),
					)
				);
				return false;
			}

			self::landtech_debug_add(
				'landtech_decision',
				array(
					'block' => 'no',
					'note'  => 'landtech_ok',
				)
			);
			return true;
		}

		/**
		 * LandTech step for debug log (no-op unless debug on).
		 *
		 * @param string              $step Step name.
		 * @param array<string,mixed> $data Data.
		 * @return void
		 */
		private static function landtech_debug_add( $step, array $data ) {
			if ( ! function_exists( 'elementor_extras_license_debug_enabled' ) || ! elementor_extras_license_debug_enabled() ) {
				return;
			}
			if ( class_exists( 'ElementorExtras_License_Debug' ) ) {
				ElementorExtras_License_Debug::add( $step, $data );
			}
		}

		/**
		 * Clear cached LandTech status fields (LMFWC failed or key removed).
		 *
		 * @return void
		 */
		private static function clear_landtech_status_snapshot() {
			delete_option( 'elementor_extras_landtech_subscription_status' );
			delete_option( 'elementor_extras_landtech_eligible_for_updates' );
			delete_option( 'elementor_extras_landtech_reason_code' );
			delete_option( 'elementor_extras_landtech_purchase_type' );
			delete_option( 'elementor_extras_landtech_is_revoked' );
		}

		/**
		 * Interpret LMFWC validate HTTP response without false positives on transient errors.
		 *
		 * - true  = store definitively reports an active/valid license.
		 * - false = store definitively reports inactive/invalid (or key not found).
		 * - null  = inconclusive (network/5xx/unparseable/401 due to site credentials, etc.) — keep existing status.
		 *
		 * @param int   $response_code HTTP status code.
		 * @param mixed $body          json_decode() result (or null on failure).
		 * @return bool|null
		 */
		private static function interpret_lmfwc_validate_response( $response_code, $body ) {
			$code = (int) $response_code;

			if ( ! in_array( $code, array( 200, 201 ), true ) ) {
				if ( 404 === $code ) {
					return false;
				}
				// 401/403 often mean wrong consumer keys on the *site*, not an invalid customer license.
				if ( in_array( $code, array( 400, 401, 403 ), true ) ) {
					return null;
				}
				// Rate limits and server errors: do not revoke a paying customer.
				if ( in_array( $code, array( 408, 429, 500, 502, 503, 504 ), true ) ) {
					return null;
				}
				return null;
			}

			if ( ! is_array( $body ) ) {
				return null;
			}

			return self::parse_lmfwc_validate_body_200( $body );
		}

		/**
		 * Tri-state interpretation for LMFWC activate (same rules as validate/cron).
		 *
		 * @param int   $response_code HTTP status code.
		 * @param mixed $body          json_decode() result (or null on failure).
		 * @return bool|null True accepted, false definitively rejected, null inconclusive.
		 */
		public static function interpret_lmfwc_activate_response( $response_code, $body ) {
			return self::interpret_lmfwc_validate_response( $response_code, $body );
		}

		/**
		 * Parse LMFWC GET /licenses/validate/{key} JSON body (HTTP 200 only).
		 *
		 * When `data.valid` or `data.status` is present, it wins over top-level `success`
		 * so a generic success flag cannot imply an active license.
		 *
		 * @param array<string,mixed> $body json_decode() associative array.
		 * @return bool|null True valid, false invalid, null inconclusive.
		 */
		private static function parse_lmfwc_validate_body_200( array $body ) {
			$data = isset( $body['data'] ) && is_array( $body['data'] ) ? $body['data'] : null;

			if ( is_array( $data ) && array_key_exists( 'valid', $data ) ) {
				$v = $data['valid'];
				if ( true === $v || 1 === $v || '1' === $v ) {
					return true;
				}
				if ( false === $v || 0 === $v || '0' === $v || '' === $v ) {
					return false;
				}
				return null;
			}

			if ( is_array( $data ) && array_key_exists( 'status', $data ) ) {
				$status_value = $data['status'];
				if ( is_numeric( $status_value ) ) {
					$sn = (int) $status_value;
					// LMFWC: 1 / 2 are active/delivered in common setups; 3+ treated as not active.
					if ( in_array( $sn, array( 1, 2 ), true ) ) {
						return true;
					}
					if ( $sn >= 3 ) {
						return false;
					}
					return null;
				}
				$sk = sanitize_key( (string) $status_value );
				if ( in_array( $sk, array( 'active', 'valid', 'delivered', 'sold' ), true ) ) {
					return true;
				}
				if ( in_array( $sk, array( 'inactive', 'expired', 'disabled', 'revoked' ), true ) ) {
					return false;
				}
				return null;
			}

			if ( isset( $body['success'] ) ) {
				if ( true === $body['success'] || 1 === $body['success'] || '1' === (string) $body['success'] ) {
					return true;
				}
				if ( false === $body['success'] || 0 === $body['success'] || '0' === (string) $body['success'] ) {
					return false;
				}
			}

			if ( isset( $body['status'] ) ) {
				$status_value = $body['status'];
				if ( is_numeric( $status_value ) ) {
					$sn = (int) $status_value;
					if ( in_array( $sn, array( 1, 2 ), true ) ) {
						return true;
					}
					if ( $sn >= 3 ) {
						return false;
					}
					return null;
				}
				$sk = sanitize_key( (string) $status_value );
				if ( in_array( $sk, array( 'active', 'valid', 'delivered', 'sold' ), true ) ) {
					return true;
				}
				if ( in_array( $sk, array( 'inactive', 'expired', 'disabled', 'revoked' ), true ) ) {
					return false;
				}
				return null;
			}

			// 200 but no recognizable license fields — do not assume invalid (avoid accidental revokes).
			return null;
		}

		/**
		 * LMFWC activate endpoint (GET). Tri-state semantics match cron validation.
		 *
		 * @since 2.3.0
		 * @param string $license_key License key.
		 * @return bool|null True accepted, false rejected, null inconclusive.
		 */
		public static function activate_via_lmfwc( $license_key ) {
			$key = sanitize_text_field( (string) $license_key );
			if ( '' === $key ) {
				return false;
			}

			$consumer_key    = elementor_extras_get_consumer_key();
			$consumer_secret = elementor_extras_get_consumer_secret();
			if ( empty( $consumer_key ) || empty( $consumer_secret ) ) {
				return null;
			}

			$base = elementor_extras_get_license_server_url();
			if ( empty( $base ) && defined( 'ELEMENTOR_EXTRAS_STORE_URL' ) ) {
				$base = ELEMENTOR_EXTRAS_STORE_URL;
			}
			$base = trailingslashit( esc_url_raw( (string) $base ) );

			$encoded_key = rawurlencode( $key );
			$encoded_key = str_replace( '.', '%2E', $encoded_key );
			$license_url = $base . 'wp-json/lmfwc/v2/licenses/activate/' . $encoded_key;
			$license_url = elementor_extras_lmfwc_request_url_with_query_auth( $license_url, $consumer_key, $consumer_secret );

			$response = wp_remote_get(
				$license_url,
				array(
					'timeout'   => 20,
					'sslverify' => true,
					'headers'   => array(
						'Accept' => 'application/json',
					),
				)
			);

			if ( is_wp_error( $response ) ) {
				return null;
			}

			$code = wp_remote_retrieve_response_code( $response );
			$raw  = wp_remote_retrieve_body( $response );
			$body = json_decode( $raw, true );

			return self::interpret_lmfwc_activate_response( $code, $body );
		}
	}
}
