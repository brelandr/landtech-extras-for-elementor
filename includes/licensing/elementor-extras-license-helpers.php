<?php
/**
 * LandTech / LMFWC licensing helpers for Extras for Elementor (mirrors wedding-party-rsvp-pro pattern).
 *
 * @package ElementorExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load bundled LandTech licensing classes once.
 *
 * @return void
 */
function elementor_extras_load_landtech_licensing_files() {
	static $loaded = false;
	if ( $loaded ) {
		return;
	}
	$loaded       = true;
	$license_path = ELEMENTOR_EXTRAS_PATH . 'includes/licensing/';
	require_once $license_path . 'class-landtech-license-admin-settings-helper.php';
	require_once $license_path . 'class-landtech-premium-update-offer.php';
	require_once $license_path . 'class-elementor-extras-landtech-license-client-sdk.php';
	require_once $license_path . 'class-elementor-extras-license-cron.php';
}

/**
 * Diagnostics: set ELEMENTOR_EXTRAS_LICENSE_DEBUG in wp-config.php to enable (reserved for future use).
 *
 * @return bool
 */
function elementor_extras_license_debug_enabled() {
	return defined( 'ELEMENTOR_EXTRAS_LICENSE_DEBUG' ) && ELEMENTOR_EXTRAS_LICENSE_DEBUG;
}

/**
 * @param string $data Plaintext.
 * @return string Encrypted/base64 blob.
 */
function elementor_extras_encrypt( $data ) {
	if ( empty( $data ) ) {
		return '';
	}
	if ( ! function_exists( 'openssl_encrypt' ) ) {
		return base64_encode( $data ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}
	$key = wp_salt();
	$iv  = substr( hash( 'sha256', $key ), 0, 16 );

	return base64_encode( openssl_encrypt( $data, 'AES-256-CBC', $key, 0, $iv ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
}

/**
 * @param string $data Stored blob.
 * @return string
 */
function elementor_extras_decrypt( $data ) {
	if ( empty( $data ) ) {
		return '';
	}
	if ( ! function_exists( 'openssl_decrypt' ) ) {
		$d = base64_decode( $data, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		return false !== $d ? (string) $d : '';
	}
	$key = wp_salt();
	$iv  = substr( hash( 'sha256', $key ), 0, 16 );

	return openssl_decrypt( base64_decode( $data, true ), 'AES-256-CBC', $key, 0, $iv ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
}

/**
 * @param string $key License key for logs.
 * @return string
 */
function elementor_extras_license_mask_key( $key ) {
	$key = (string) $key;
	if ( '' === $key || strlen( $key ) < 8 ) {
		return '••••••••';
	}
	return substr( $key, 0, 4 ) . '…' . substr( $key, -4 );
}

/**
 * LandTech shared secret (wp-config or Settings → General).
 *
 * @return string
 */
function elementor_extras_get_license_shared_secret() {
	if ( defined( 'ELEMENTOR_EXTRAS_LANDTECH_SHARED_SECRET' ) && ! empty( constant( 'ELEMENTOR_EXTRAS_LANDTECH_SHARED_SECRET' ) ) ) {
		return sanitize_text_field( (string) constant( 'ELEMENTOR_EXTRAS_LANDTECH_SHARED_SECRET' ) );
	}

	return sanitize_text_field( (string) get_option( 'elementor_extras_license_shared_secret', '' ) );
}

/**
 * Store base URL (constant wins when defined, matching RSVP semantics).
 *
 * @return string
 */
function elementor_extras_get_license_server_url() {
	if ( defined( 'ELEMENTOR_EXTRAS_STORE_URL' ) && ! empty( ELEMENTOR_EXTRAS_STORE_URL ) ) {
		return esc_url_raw( (string) ELEMENTOR_EXTRAS_STORE_URL );
	}

	return esc_url_raw( (string) get_option( 'elementor_extras_license_store_url', '' ) );
}

/**
 * Product slug or numeric ID for LandTech / LMFWC.
 *
 * @return string
 */
function elementor_extras_get_license_product_id() {
	if ( defined( 'ELEMENTOR_EXTRAS_PRODUCT_ID' ) && '' !== trim( (string) ELEMENTOR_EXTRAS_PRODUCT_ID ) ) {
		return sanitize_text_field( (string) ELEMENTOR_EXTRAS_PRODUCT_ID );
	}

	return sanitize_text_field( (string) get_option( 'elementor_extras_license_product_id', '' ) );
}

/**
 * @return string
 */
function elementor_extras_get_consumer_key() {
	if ( defined( 'ELEMENTOR_EXTRAS_CONSUMER_KEY' ) && ! empty( ELEMENTOR_EXTRAS_CONSUMER_KEY ) ) {
		return trim( (string) ELEMENTOR_EXTRAS_CONSUMER_KEY );
	}

	$encrypted = get_option( 'elementor_extras_consumer_key_encrypted', '' );
	if ( ! empty( $encrypted ) ) {
		return elementor_extras_decrypt( $encrypted );
	}

	return trim( (string) ELEMENTOR_EXTRAS_DEFAULT_CONSUMER_KEY );
}

/**
 * @return string
 */
function elementor_extras_get_consumer_secret() {
	if ( defined( 'ELEMENTOR_EXTRAS_CONSUMER_SECRET' ) && ! empty( ELEMENTOR_EXTRAS_CONSUMER_SECRET ) ) {
		return trim( (string) ELEMENTOR_EXTRAS_CONSUMER_SECRET );
	}

	$encrypted = get_option( 'elementor_extras_consumer_secret_encrypted', '' );
	if ( ! empty( $encrypted ) ) {
		return elementor_extras_decrypt( $encrypted );
	}

	return trim( (string) ELEMENTOR_EXTRAS_DEFAULT_CONSUMER_SECRET );
}

/**
 * @param string $url             REST URL without query auth.
 * @param string $consumer_key    LMFWC key.
 * @param string $consumer_secret LMFWC secret.
 * @return string
 */
function elementor_extras_lmfwc_request_url_with_query_auth( $url, $consumer_key, $consumer_secret ) {
	return add_query_arg(
		array(
			'consumer_key'    => $consumer_key,
			'consumer_secret' => $consumer_secret,
		),
		$url
	);
}

/**
 * @param string $url URL that may embed credentials.
 * @return string
 */
function elementor_extras_lmfwc_redact_url_for_log( $url ) {
	$url = (string) $url;
	if ( '' === $url ) {
		return '';
	}
	if ( false === strpos( $url, 'consumer_secret=' ) && false === strpos( $url, 'consumer_key=' ) ) {
		return $url;
	}

	return remove_query_arg( array( 'consumer_key', 'consumer_secret' ), $url );
}

/**
 * @param string $suffix Hostname suffix.
 * @return bool
 */
function elementor_extras_is_valid_trusted_demo_license_domain_suffix( $suffix ) {
	$suffix = (string) $suffix;
	if ( '' === $suffix ) {
		return false;
	}
	if ( false !== strpos( $suffix, '/' ) || false !== strpos( $suffix, '*' ) ) {
		return false;
	}

	return (bool) preg_match( '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/', $suffix );
}

/**
 * Trusted hosts bypass LMFWC (optional; empty by default).
 *
 * @return bool
 */
function elementor_extras_site_has_trusted_demo_license() {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	if ( ! is_string( $host ) || '' === $host ) {
		$host = wp_parse_url( site_url(), PHP_URL_HOST );
	}
	$host = strtolower( (string) $host );
	if ( 0 === strpos( $host, 'www.' ) ) {
		$host = substr( $host, 4 );
	}

	$trusted = array();
	/**
	 * Hostnames that bypass the stored LMFWC status gate (lowercase, no leading www.).
	 *
	 * @param string[] $trusted Trusted hosts.
	 * @param string   $host    Current site host (normalized).
	 */
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	$trusted = apply_filters( 'elementor_extras_trusted_demo_license_hosts', $trusted, $host );
	$trusted = is_array( $trusted ) ? $trusted : array();
	$trusted = array_map( 'strtolower', array_map( 'sanitize_text_field', $trusted ) );

	if ( in_array( $host, $trusted, true ) ) {
		return true;
	}

	$suffix_default = '';
	/**
	 * DNS suffix for demo installs ending with `.suffix`.
	 *
	 * @param string $suffix Default empty.
	 * @param string $host   Current site host (normalized).
	 */
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	$suffix = apply_filters( 'elementor_extras_trusted_demo_license_domain_suffix', $suffix_default, $host );
	$suffix = strtolower( trim( sanitize_text_field( (string) $suffix ) ) );
	if ( ! elementor_extras_is_valid_trusted_demo_license_domain_suffix( $suffix ) ) {
		return false;
	}

	if ( $host === $suffix ) {
		return true;
	}

	$tail = '.' . $suffix;
	$len  = strlen( $tail );

	return strlen( $host ) > $len && substr( $host, -$len ) === $tail;
}

/**
 * LMFWC consumer key forced via wp-config.
 *
 * @return bool
 */
function elementor_extras_lmfwc_consumer_key_from_constant() {
	return defined( 'ELEMENTOR_EXTRAS_CONSUMER_KEY' ) && '' !== trim( (string) ELEMENTOR_EXTRAS_CONSUMER_KEY );
}

/**
 * LMFWC consumer secret forced via wp-config.
 *
 * @return bool
 */
function elementor_extras_lmfwc_consumer_secret_from_constant() {
	return defined( 'ELEMENTOR_EXTRAS_CONSUMER_SECRET' ) && '' !== trim( (string) ELEMENTOR_EXTRAS_CONSUMER_SECRET );
}

/**
 * Premium update / cron gate.
 *
 * @return bool
 */
function elementor_extras_effective_license_is_valid() {
	if ( elementor_extras_site_has_trusted_demo_license() ) {
		return true;
	}

	return 'valid' === get_option( 'elementor_extras_license_status', '' );
}

/**
 * Register Settings → General fields for LandTech URL / product / shared secret.
 *
 * @return void
 */
function elementor_extras_register_license_server_settings() {
	elementor_extras_load_landtech_licensing_files();
	if ( ! class_exists( 'Landtech_License_Admin_Settings_Helper' ) ) {
		return;
	}

	Landtech_License_Admin_Settings_Helper::register_general_settings(
		array(
			'section_id'           => 'elementor_extras_license_server_section',
			// phpcs:disable WordPress.WP.I18n.TextDomainMismatch -- Labels use plugin textdomain.
			'section_title'        => __( 'Extras for Elementor — License Server', 'elementor-extras' ),
			'option_store_url'     => 'elementor_extras_license_store_url',
			'option_product_id'    => 'elementor_extras_license_product_id',
			'option_shared_secret' => 'elementor_extras_license_shared_secret',
			'default_store_url'    => defined( 'ELEMENTOR_EXTRAS_STORE_URL' ) ? ELEMENTOR_EXTRAS_STORE_URL : '',
			'default_product_id'   => defined( 'ELEMENTOR_EXTRAS_PRODUCT_ID' ) ? ELEMENTOR_EXTRAS_PRODUCT_ID : '',
			'store_url_label'      => __( 'License Server URL', 'elementor-extras' ),
			'product_id_label'     => __( 'License Product ID', 'elementor-extras' ),
			'shared_secret_label'  => __( 'License Shared Secret', 'elementor-extras' ),
			// phpcs:enable WordPress.WP.I18n.TextDomainMismatch
		)
	);
}
