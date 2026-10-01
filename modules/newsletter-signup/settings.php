<?php
/**
 * Mailchimp key helpers (encrypted option).
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Derive a 32-byte sodium key from AUTH_KEY.
 *
 * @return string
 */
function landtech_extras_newsletter_secret_key() {
	$seed = defined( 'AUTH_KEY' ) ? (string) AUTH_KEY : 'landtech-extras-newsletter';
	return hash( 'sha256', $seed, true );
}

/**
 * Encrypt a Mailchimp API key.
 *
 * @param string $plain Plain key.
 * @return string Base64 nonce+ciphertext, or empty.
 */
function landtech_extras_newsletter_encrypt_key( $plain ) {
	$plain = is_string( $plain ) ? trim( $plain ) : '';
	if ( '' === $plain || ! function_exists( 'sodium_crypto_secretbox' ) ) {
		return '';
	}
	$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	$box   = sodium_crypto_secretbox( $plain, $nonce, landtech_extras_newsletter_secret_key() );
	return base64_encode( $nonce . $box );
}

/**
 * Decrypt the stored Mailchimp API key.
 *
 * @return string
 */
function landtech_extras_newsletter_decrypt_key() {
	$stored = get_option( 'ltxe_mailchimp_api_key', '' );
	if ( ! is_string( $stored ) || '' === $stored ) {
		$apis = get_option( 'landtech_extras_apis', array() );
		if ( is_array( $apis ) && ! empty( $apis['mailchimp_api_key'] ) && is_string( $apis['mailchimp_api_key'] ) ) {
			$stored = $apis['mailchimp_api_key'];
		}
	}
	if ( ! is_string( $stored ) || '' === $stored ) {
		return '';
	}
	if ( ! function_exists( 'sodium_crypto_secretbox_open' ) ) {
		return $stored;
	}
	$raw = base64_decode( $stored, true );
	if ( false === $raw || strlen( $raw ) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + 16 ) {
		return $stored;
	}
	$nonce = substr( $raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	$box   = substr( $raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
	$plain = sodium_crypto_secretbox_open( $box, $nonce, landtech_extras_newsletter_secret_key() );
	return ( false === $plain ) ? '' : $plain;
}
