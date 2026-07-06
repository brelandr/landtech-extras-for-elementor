<?php
/**
 * Extension API: hooks for addon / Premium builds (module lists, entitlements).
 *
 * The WordPress.org Free distribution SHOULD additionally define {@see LANDTECH_EXTRAS_FREE_MAIN_FILE}
 * (alongside {@see LANDTECH_EXTRAS__FILE__}) so dependent plugins can gate on canonical core.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @since 2.3.0 Increment when addon-facing contracts change. */
if ( ! defined( 'LANDTECH_EXTRAS_EXTENSION_API' ) ) {
	define( 'LANDTECH_EXTRAS_EXTENSION_API', 1 );
}

/**
 * Whether an optional capability is enabled (filter hook for site-specific toggles).
 *
 * WordPress.org build: all bundled features are enabled by default; filters may opt out.
 *
 * @since 2.3.0
 *
 * @param string $feature_id Non-empty slug; dots/colons folded for normalization (colon → underscore).
 * @param bool   $default    Value before filters.
 *
 * @return bool
 */
function landtech_extras_feature_enabled( $feature_id, $default = true ) {
	$raw = strtolower( preg_replace( '/[^a-z0-9_.:-]/i', '', (string) $feature_id ) );
	$id  = str_replace(
		array( ':', '.' ),
		array( '_', '_' ),
		$raw
	);
	$id = sanitize_key( $id );
	if ( '' === $id ) {
		return (bool) $default;
	}

	/**
	 * Gate optional feature flags (site administrators or integrations may attach here).
	 *
	 * @since 2.3.0
	 *
	 * @param bool   $enabled    Whether enabled.
	 * @param string $feature_id Normalized id.
	 */
	return (bool) apply_filters( 'landtech_extras/feature_enabled', (bool) $default, $id );
}
