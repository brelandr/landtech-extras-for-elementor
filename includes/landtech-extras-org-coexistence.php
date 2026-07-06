<?php
/**
 * WordPress.org bootstrap: coexistence with LandTech Premium thin router.
 *
 * The shared `landtech-extras-bootstrap-guard.php` is loaded with `function_exists` guards, so an
 * older Premium build that loads first can pin legacy “Premium wins” duplicate resolution. This
 * module is org-only and always loaded by the org main file so coexistence still works.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'landtech_extras_org_normalize_plugin_main_path' ) ) {
	/**
	 * @param string $abs Absolute path.
	 * @return string
	 */
	function landtech_extras_org_normalize_plugin_main_path( $abs ) {
		$abs = (string) $abs;
		$real = realpath( $abs );

		return wp_normalize_path( false !== $real ? $real : $abs );
	}
}

if ( ! function_exists( 'landtech_extras_org_landtech_bootstrap_targets_premium_coexistence' ) ) {

	/**
	 * True when `$full` bootstrap looks like Premium coexistence-era packaging (thin router dispatch).
	 *
	 * Matches multiple fingerprints so minor header edits cannot disable WordPress.org + Premium side-by-side.
	 *
	 * @param string $head Leading bytes of `landtech-extras.php`.
	 * @return bool
	 */
	function landtech_extras_org_landtech_bootstrap_targets_premium_coexistence( $head ) {
		if ( ! is_string( $head ) || '' === $head ) {
			return false;
		}

		if ( false !== strpos( $head, 'LANDTECH_EXTRAS_PREMIUM_ADDON_MARKER' ) ) {
			return true;
		}

		if ( false !== strpos( $head, 'includes/premium-bootstrap-dispatch.php' ) ) {
			return true;
		}

		if ( false !== strpos( $head, 'LANDTECH_EXTRAS_PREMIUM_FILE' ) && false !== strpos( $head, 'LANDTECH_EXTRAS_PREMIUM_PATH' ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'landtech_extras_org_premium_addon_coexistence_live' ) ) {
	/**
	 * True when another active `landtech-extras.php` is the Premium coexistence thin router.
	 *
	 * @param string $this_org_main_file Absolute path — org `landtech-extras.php` {@see __FILE__}.
	 * @return bool
	 */
	function landtech_extras_org_premium_addon_coexistence_live( $this_org_main_file ) {
		if ( ! function_exists( 'get_option' ) || ! defined( 'WP_PLUGIN_DIR' ) ) {
			return false;
		}

		$this_norm = landtech_extras_org_normalize_plugin_main_path( (string) $this_org_main_file );
		if ( '' === $this_norm ) {
			return false;
		}

		$rels = array();
		$pump = static function ( $list ) use ( &$rels ) {
			foreach ( (array) $list as $rel ) {
				if ( preg_match( '#/landtech-extras\\.php$#', (string) $rel ) ) {
					$rels[] = wp_normalize_path( (string) $rel );
				}
			}
		};
		$pump( get_option( 'active_plugins', array() ) );
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$pump( array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) );
		}

		$rels = array_values( array_unique( $rels ) );

		foreach ( $rels as $rel ) {
			if ( preg_match( '#(^|/)\\.\\./|^\\.\\.#', $rel ) ) {
				continue;
			}

			$full = WP_PLUGIN_DIR . '/' . str_replace( array( '/', '\\' ), '/', $rel );
			$full = wp_normalize_path( $full );

			if ( ! is_readable( $full ) ) {
				continue;
			}

			if ( landtech_extras_org_normalize_plugin_main_path( $full ) === $this_norm ) {
				continue;
			}

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local bootstrap sniff; path validated readable.
			$head = file_get_contents( $full, false, null, 0, 65536 );

			if ( landtech_extras_org_landtech_bootstrap_targets_premium_coexistence( $head ) ) {
				return true;
			}
		}

		return false;
	}
}
