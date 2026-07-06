<?php
/**
 * When two full-copy editions collide: canonical globals/win-one-runner semantics stay deterministic.
 * Premium thin bootstrap (coexistence router) does not participate as a second full runner.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'landtech_extras_normalize_plugin_main_path' ) ) {
	/**
	 * Collapse symlinks so duplicate detection matches `__FILE__` vs active plugin list.
	 *
	 * @param string $abs Absolute path to landtech-extras.php.
	 * @return string Normalized path.
	 */
	function landtech_extras_normalize_plugin_main_path( $abs ) {
		$abs = (string) $abs;
		$real = realpath( $abs );

		return wp_normalize_path( false !== $real ? $real : $abs );
	}
}

if ( ! function_exists( 'landtech_extras_landtech_main_file_is_premium_addon_router' ) ) {

	/**
	 * Detect Premium thin coexistence bootstrap (Marker in main plugin file; never removed from shipping builds).
	 *
	 * @param string $abs Absolute path to landtech-extras.php.
	 * @return bool
	 */
	function landtech_extras_landtech_main_file_is_premium_addon_router( $abs ) {
		static $cache = array();

		$key = landtech_extras_normalize_plugin_main_path( (string) $abs );
		if ( isset( $cache[ $key ] ) ) {
			return $cache[ $key ];
		}

		if ( '' === $key || ! is_readable( $abs ) ) {
			$cache[ $key ] = false;
			return false;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local bootstrap sniff; path validated readable.
		$head = file_get_contents( $abs, false, null, 0, 16384 );
		if ( ! is_string( $head ) ) {
			$cache[ $key ] = false;
			return false;
		}

		$cache[ $key ] = false !== strpos( $head, 'LANDTECH_EXTRAS_PREMIUM_ADDON_MARKER' );

		return $cache[ $key ];
	}
}

if ( ! function_exists( 'landtech_extras_collect_active_landtech_roots' ) ) {

	/**
	 * Return active bootstrap files named landtech-extras.php (distinct plugin directories).
	 *
	 * @return array<int, array{rel:string, full:string, is_premium:bool, is_addon_router:bool}> Meta per install.
	 */
	function landtech_extras_collect_active_landtech_roots() {
		static $cache = null;

		if ( null !== $cache ) {
			return $cache;
		}

		$cache = array();

		if ( ! function_exists( 'get_option' ) || ! defined( 'WP_PLUGIN_DIR' ) ) {
			return $cache;
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
		sort( $rels );

		foreach ( $rels as $rel ) {
			if ( preg_match( '#(^|/)\\.\\./|^\\.\\.#', $rel ) ) {
				continue;
			}

			$full = WP_PLUGIN_DIR . '/' . str_replace( array( '/', '\\' ), '/', $rel );
			$full = wp_normalize_path( $full );

			if ( ! is_readable( $full ) ) {
				continue;
			}

			$name = '';

			if ( function_exists( 'get_file_data' ) ) {
				$headers = get_file_data( $full, array( 'plugin_name' => 'Plugin Name' ), 'plugin' );
				if ( isset( $headers['plugin_name'] ) ) {
					$name = (string) $headers['plugin_name'];
				}
			}

			$cache[] = array(
				'rel'             => $rel,
				'full'            => landtech_extras_normalize_plugin_main_path( $full ),
				'is_premium'      => ( '' !== $name && false !== stripos( $name, 'premium' ) ),
				'is_addon_router' => landtech_extras_landtech_main_file_is_premium_addon_router( $full ),
			);
		}

		return $cache;
	}
}

if ( ! function_exists( 'landtech_extras_collect_full_bootstrap_landtech_roots' ) ) {

	/**
	 * Active landtech-extras.php roots that own the shared constant namespace (excludes Premium thin router).
	 *
	 * @return array<int, array{rel:string, full:string, is_premium:bool, is_addon_router:bool}>
	 */
	function landtech_extras_collect_full_bootstrap_landtech_roots() {
		$rows = landtech_extras_collect_active_landtech_roots();
		$full = array();

		foreach ( $rows as $row ) {
			if ( ! empty( $row['is_addon_router'] ) ) {
				continue;
			}
			$full[] = $row;
		}

		return $full;
	}
}

if ( ! function_exists( 'landtech_extras_shared_constants_claimed_by_other_bootstrap' ) ) {
	/**
	 * Whether LANDTECH_EXTRAS__FILE__ was defined by another landtech-extras.php in the same request.
	 *
	 * @param string $this_main_file Absolute path — pass {@see __FILE__} from each edition's bootstrap.
	 * @return bool
	 */
	function landtech_extras_shared_constants_claimed_by_other_bootstrap( $this_main_file ) {
		if ( ! defined( 'LANDTECH_EXTRAS__FILE__' ) ) {
			return false;
		}

		return landtech_extras_normalize_plugin_main_path( (string) constant( 'LANDTECH_EXTRAS__FILE__' ) ) !== landtech_extras_normalize_plugin_main_path( (string) $this_main_file );
	}
}

if ( ! function_exists( 'landtech_extras_shared_constant_collision_admin_notice' ) ) {

	/**
	 * Admin notice when shared constants point at the wrong edition (load order collision).
	 *
	 * @return void
	 */
	function landtech_extras_shared_constant_collision_admin_notice() {
		static $shown = false;

		if ( $shown ) {
			return;
		}
		if ( ! function_exists( 'is_admin' ) || ! is_admin() || ! function_exists( 'current_user_can' ) || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$shown = true;

		echo '<div class="notice notice-error"><p>';
		echo esc_html(
			__(
				'LandTech Extras: Two editions attempted to define the same plugin constants (WordPress.org + add-on package cannot both bootstrap in one request when load order overlaps). Deactivate the duplicate plugin under Plugins; only one LandTech Extras install may be active.',
				'landtech-extras-for-elementor'
			)
		);
		echo '</p></div>';
	}
}

if ( ! function_exists( 'landtech_extras_resolve_canonical_landtech_main_file' ) ) {
	/**
	 * Pick the bootstrap file whose globals/hooks win when duplicates are detected among full installs.
	 *
	 * Prefer WordPress.org (non‑Premium headers), then alphabetical rel for determinism.
	 *
	 * @return string Canonical absolute normalized path or empty when no ambiguity.
	 */
	function landtech_extras_resolve_canonical_landtech_main_file() {
		static $resolved = '__unset';

		if ( '__unset' !== $resolved ) {
			return $resolved;
		}

		$mains = landtech_extras_collect_full_bootstrap_landtech_roots();

		if ( count( $mains ) < 2 ) {
			$resolved = '';
			return $resolved;
		}

		foreach ( $mains as $row ) {
			if ( empty( $row['is_premium'] ) && ! empty( $row['full'] ) ) {
				$resolved = landtech_extras_normalize_plugin_main_path( $row['full'] );
				return $resolved;
			}
		}

		usort(
			$mains,
			static function ( $a, $b ) {
				return strcmp( $a['rel'], $b['rel'] );
			}
		);

		if ( isset( $mains[0]['full'] ) ) {
			$resolved = landtech_extras_normalize_plugin_main_path( $mains[0]['full'] );
			return $resolved;
		}

		$resolved = '';
		return $resolved;
	}
}

if ( ! function_exists( 'landtech_extras_prepare_main_bootstrap' ) ) {
	/**
	 * Call from landtech-extras.php immediately after ABSPATH; wrap the rest of the file in its truth branch.
	 *
	 * @param string $main_file __FILE__ of landtech-extras.php.
	 * @return bool True when bootstrap should execute; false when duplicate (show notice only).
	 */
	function landtech_extras_prepare_main_bootstrap( $main_file ) {
		if ( '' === trim( (string) $main_file ) ) {
			return true;
		}

		$this_norm = landtech_extras_normalize_plugin_main_path( (string) $main_file );

		if ( landtech_extras_landtech_main_file_is_premium_addon_router( (string) $main_file ) ) {
			return true;
		}

		$mains = landtech_extras_collect_full_bootstrap_landtech_roots();

		if ( count( $mains ) < 2 ) {
			return true;
		}

		$canonical = landtech_extras_resolve_canonical_landtech_main_file();

		if ( '' !== $canonical && $canonical === $this_norm ) {
			return true;
		}

		if ( '' === $canonical ) {
			return true;
		}

		if ( ! function_exists( 'add_action' ) ) {
			return false;
		}

		static $notice_hooked = false;

		if ( ! $notice_hooked ) {

			add_action(
				'admin_notices',
				static function () use ( $main_file ) {

					static $shown = false;

					if ( $shown ) {
						return;
					}

					if ( ! is_admin() || ! function_exists( 'current_user_can' ) || ! current_user_can( 'activate_plugins' ) ) {
						return;
					}

					$shown = true;

					$folder = basename( dirname( (string) $main_file ) );

					echo '<div class="notice notice-error"><p>';
					echo esc_html(
						__(
							'LandTech Extras conflict: Both the WordPress.org plugin (LandTech Extras for Elementor) and the separate add-on are active at the same time. Only one can run. Please deactivate whichever edition you do not need under Plugins.',
							'landtech-extras-for-elementor'
						)
					);
					echo '</p><p>';

					printf(
						/* translators: %s: Plugin directory slug for the inactive duplicate. */
						esc_html__( 'This copy skipped loading to prevent fatal errors (folder %s).', 'landtech-extras-for-elementor' ),
						esc_html( $folder )
					);

					echo '</p></div>';
				},
				5
			);

			$notice_hooked = true;
		}

		return false;
	}
}
