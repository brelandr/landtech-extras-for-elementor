<?php
/**
 * Shared LandTech check-update resolution (GitHub target_version + server latest_version).
 *
 * Bundled in premium plugins. Loaded once per request via class_exists guard.
 *
 * @package LandTechLicenseServer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Landtech_Premium_Update_Offer', false ) ) {
	/**
	 * Resolves update offers from LandTech check-update JSON (aligned with AdFusion Premium).
	 */
	class Landtech_Premium_Update_Offer {

		/**
		 * Normalize semver for comparisons (strip leading v/V).
		 *
		 * @param string $version Version string.
		 * @return string
		 */
		public static function normalize_semver( $version ) {
			return ltrim( sanitize_text_field( (string) $version ), 'vV' );
		}

		/**
		 * Flatten REST bodies that nest fields under data.
		 *
		 * @param array<string,mixed>|mixed $result Raw API payload.
		 * @return array<string,mixed>
		 */
		public static function normalize_check_update_response( $result ) {
			if ( ! is_array( $result ) ) {
				return array();
			}
			if ( isset( $result['data'] ) && is_array( $result['data'] ) ) {
				return array_merge( $result, $result['data'] );
			}
			return $result;
		}

		/**
		 * Parse LandTech check-update success payload.
		 *
		 * @param array<string,mixed>|mixed $raw Raw JSON-decoded body.
		 * @return array{latest_version:string,package_url:string,update_flag:?bool}
		 */
		public static function parse_landtech_check_payload( $raw ) {
			$data = self::normalize_check_update_response( $raw );
			$flag = null;
			if ( array_key_exists( 'update_available', $data ) ) {
				$flag = (bool) $data['update_available'];
			}

			return array(
				'latest_version' => isset( $data['latest_version'] ) ? sanitize_text_field( (string) $data['latest_version'] ) : '',
				'package_url'    => isset( $data['package_url'] ) ? esc_url_raw( (string) $data['package_url'] ) : '',
				'update_flag'    => $flag,
			);
		}

		/**
		 * Infer offered version when the server omits latest_version.
		 *
		 * @param array  $parsed           Output of parse_landtech_check_payload (latest_version may be updated in place by caller).
		 * @param string $requested_target target_version sent for this attempt, or ''.
		 * @param mixed  $raw              Raw API array or WP_Error.
		 * @param object $release_object   GitHub release with tag_name, or empty object.
		 * @return string
		 */
		public static function infer_latest_version_for_offer( $parsed, $requested_target, $raw, $release_object ) {
			if ( '' !== $parsed['latest_version'] ) {
				return $parsed['latest_version'];
			}
			if ( '' !== $requested_target ) {
				return sanitize_text_field( (string) $requested_target );
			}
			if ( is_wp_error( $raw ) || ! is_array( $raw ) ) {
				return '';
			}
			$data = self::normalize_check_update_response( $raw );
			foreach ( array( 'offered_version', 'new_version', 'remote_version', 'available_version' ) as $k ) {
				if ( ! empty( $data[ $k ] ) ) {
					return sanitize_text_field( (string) $data[ $k ] );
				}
			}
			if ( ! empty( $data['success'] ) && '' !== $parsed['package_url'] && is_object( $release_object ) && ! empty( $release_object->tag_name ) ) {
				return self::normalize_semver( (string) $release_object->tag_name );
			}
			return '';
		}

		/**
		 * Whether the payload authorizes showing an in-dashboard update.
		 *
		 * @param array  $parsed            Parsed payload.
		 * @param string $installed_version Installed version from the site.
		 * @param mixed  $raw               Raw API response.
		 * @return bool
		 */
		public static function payload_qualifies_for_update( $parsed, $installed_version, $raw ) {
			if ( false === $parsed['update_flag'] ) {
				return false;
			}
			$data = ( is_array( $raw ) && ! is_wp_error( $raw ) ) ? self::normalize_check_update_response( $raw ) : array();
			if ( array_key_exists( 'success', $data ) && ! $data['success'] ) {
				return false;
			}
			$reason = isset( $data['reason_code'] ) ? sanitize_key( (string) $data['reason_code'] ) : '';
			$block  = array( 'revoked', 'license_revoked', 'updates_expired', 'invalid_product' );
			if ( '' !== $reason && in_array( $reason, $block, true ) ) {
				return false;
			}
			if ( '' === $parsed['package_url'] || '' === $parsed['latest_version'] ) {
				return false;
			}
			$latest = self::normalize_semver( $parsed['latest_version'] );
			$local  = self::normalize_semver( $installed_version );
			return version_compare( $latest, $local, '>' );
		}

		/**
		 * Ordered target_version values for LandTech (GitHub latest first when newer, then plain check-update).
		 *
		 * @param string $installed_version Installed semver.
		 * @param object $release_object    Object with tag_name or empty.
		 * @return string[]
		 */
		public static function build_landtech_check_targets( $installed_version, $release_object ) {
			$targets = array();
			if ( is_object( $release_object ) && ! empty( $release_object->tag_name ) ) {
				$remote = self::normalize_semver( (string) $release_object->tag_name );
				$local  = self::normalize_semver( $installed_version );
				if ( version_compare( $remote, $local, '>' ) ) {
					$targets[] = $remote;
				}
			}
			$targets[] = '';
			return array_values( array_unique( $targets ) );
		}

		/**
		 * Query LandTech until an offer qualifies (client must implement check_update( key, current, target )).
		 *
		 * @param object $client            Entitlement client.
		 * @param string $license_key       License key.
		 * @param string $installed_version Installed version.
		 * @param object $release_object    GitHub release (tag_name) or false.
		 * @return array{latest_version:string,package_url:string,error:WP_Error|null}
		 */
		public static function query_landtech_update_offer( $client, $license_key, $installed_version, $release_object ) {
			$out = array(
				'latest_version' => '',
				'package_url'    => '',
				'error'          => null,
			);

			if ( ! is_object( $client ) || ! is_callable( array( $client, 'check_update' ) ) ) {
				return $out;
			}

			$rel = is_object( $release_object ) ? $release_object : (object) array();

			foreach ( self::build_landtech_check_targets( $installed_version, $rel ) as $target ) {
				$result = $client->check_update( $license_key, $installed_version, $target );
				if ( is_wp_error( $result ) ) {
					if ( null === $out['error'] ) {
						$out['error'] = $result;
					}
					continue;
				}

				$parsed                   = self::parse_landtech_check_payload( $result );
				$parsed['latest_version'] = self::infer_latest_version_for_offer( $parsed, $target, $result, $rel );
				if ( self::payload_qualifies_for_update( $parsed, $installed_version, $result ) ) {
					$out['latest_version'] = $parsed['latest_version'];
					$out['package_url']    = $parsed['package_url'];
					$out['error']          = null;
					return $out;
				}
			}

			return $out;
		}
	}
}
