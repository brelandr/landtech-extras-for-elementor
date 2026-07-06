<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
/**
 * Build helper: replaces the LMFWC_BAKED_INJECT_BLOCK section in landtech-extras.php (temp copy used by create-plugin-zip.sh only).
 *
 * Env (supply consumer key / secret plaintext as today):
 *   LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_KEY
 *   LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_SECRET
 *
 * Default output: ciphertext blobs + LANDTECH_EXTRAS_BAKED_LMFWC_PK_* (split hex fragments of a 256-bit build key).
 * Plaintext ck_/cs_* in the zip instead: LANDTECH_EXTRAS_PREMIUM_BUILD_PLAINTEXT_IN_ZIP=1
 * Optional deterministic build key from CI: LANDTECH_EXTRAS_PREMIUM_BUILD_BLOB_KEY (any string → SHA-256-derived 32-byte key)
 *
 * @package LandTechExtras
 */

if ( php_sapi_name() !== 'cli' ) {
	exit( 1 );
}

if ( $argc < 2 ) {
	fwrite( STDERR, "Usage: php inject-lmfwc-credentials-for-zip.php <plugin-directory>\n" );
	exit( 1 );
}

$root = rtrim( $argv[1], '/' );

$plugin_file = $root . '/landtech-extras.php';
if ( ! is_readable( $plugin_file ) ) {
	fwrite( STDERR, 'inject-lmfwc-credentials-for-zip: missing landtech-extras.php' . PHP_EOL );
	exit( 1 );
}

$src = file_get_contents( $plugin_file );
if ( false === $src ) {
	fwrite( STDERR, 'inject-lmfwc-credentials-for-zip: could not read main plugin file' . PHP_EOL );
	exit( 1 );
}

if ( false === strpos( $src, '// BEGIN LMFWC_BAKED_INJECT_BLOCK' ) ) {
	fwrite( STDERR, 'inject-lmfwc-credentials-for-zip: no LMFWC block in landtech-extras.php (WordPress.org / free tree); skipping inject.' . PHP_EOL );
	exit( 0 );
}

$key    = getenv( 'LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_KEY' );
$secret = getenv( 'LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_SECRET' );

$key    = false === $key ? '' : trim( (string) $key );
$secret = false === $secret ? '' : trim( (string) $secret );
$key    = preg_replace( '/^\xEF\xBB\xBF/', '', $key );
$secret = preg_replace( '/^\xEF\xBB\xBF/', '', $secret );
$key    = str_replace( "\r", '', $key );
$secret = str_replace( "\r", '', $secret );

if ( '' === $key || '' === $secret ) {
	fwrite( STDERR, "inject-lmfwc-credentials-for-zip: missing LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_KEY or LANDTECH_EXTRAS_PREMIUM_BUILD_CONSUMER_SECRET\n" );
	exit( 1 );
}

$plaintext_zip = getenv( 'LANDTECH_EXTRAS_PREMIUM_BUILD_PLAINTEXT_IN_ZIP' );
$plaintext_zip = in_array(
	strtolower( (string) $plaintext_zip ),
	array( '1', 'true', 'yes', 'on' ),
	true
);

$replacement_inner = injector_lmfwc_build_inject_inner( $key, $secret, $plaintext_zip );

$markers_pattern   = '/\/\/ BEGIN LMFWC_BAKED_INJECT_BLOCK\n.*?\n\/\/ END LMFWC_BAKED_INJECT_BLOCK/s';
$replacement_block = '// BEGIN LMFWC_BAKED_INJECT_BLOCK' . "\n" . $replacement_inner . "\n// END LMFWC_BAKED_INJECT_BLOCK";

$out       = preg_replace( $markers_pattern, $replacement_block, $src, 1, $repl_count );
$repl_fail = ! is_string( $out );

if ( 1 !== (int) $repl_count || $repl_fail ) {
	fwrite(
		STDERR,
		'inject-lmfwc-credentials-for-zip: could not find // BEGIN LMFWC_BAKED_INJECT_BLOCK ... // END LMFWC_BAKED_INJECT_BLOCK in landtech-extras.php' . PHP_EOL
	);
	exit( 1 );
}

if ( false === file_put_contents( $plugin_file, $out ) ) {
	fwrite( STDERR, 'inject-lmfwc-credentials-for-zip: could not write main plugin file' . PHP_EOL );
	exit( 1 );
}

$inject_mode_report = $plaintext_zip ? 'plaintext_zip' : 'obfuscated_blob';
fwrite(
	STDOUT,
	'inject-lmfwc-credentials-for-zip: OK (' . $inject_mode_report . '; key length ' . strlen( $key ) . ', secret length ' . strlen( $secret ) . ')' . PHP_EOL
);
exit( 0 );

/**
 * @param string $key      Consumer key plaintext.
 * @param string $secret   Consumer secret plaintext.
 * @param bool   $plain_zip When true: define plaintext constants; blobs empty.
 * @return string
 */
function injector_lmfwc_build_inject_inner( $key, $secret, $plain_zip ) {
	$key    = (string) $key;
	$secret = (string) $secret;

	if ( ! function_exists( 'openssl_encrypt' ) ) {
		fwrite(
			STDERR,
			'inject-lmfwc-credentials-for-zip: OpenSSL openssl_encrypt is required (build machine).' . PHP_EOL
		);
		exit( 1 );
	}

	$key_enc_blocks    = '';
	$secret_enc_blocks = '';
	$pk_a              = '';
	$pk_b              = '';
	$define_key        = '';
	$define_secret     = '';

	if ( $plain_zip ) {
		$define_key        = $key;
		$define_secret     = $secret;
		$key_enc_blocks    = '';
		$secret_enc_blocks = '';
		$pk_a              = '';
		$pk_b              = '';
	} else {
		$blob_env = getenv( 'LANDTECH_EXTRAS_PREMIUM_BUILD_BLOB_KEY' );
		if ( false !== $blob_env && '' !== trim( (string) $blob_env ) ) {
			$aes_key_binary = hash( 'sha256', (string) $blob_env, true );
		} elseif ( function_exists( 'random_bytes' ) ) {
			$aes_key_binary = random_bytes( 32 );
		} else {
			$aes_key_binary = openssl_random_pseudo_bytes( 32 );
			if ( false === $aes_key_binary ) {
				fwrite( STDERR, 'inject-lmfwc-credentials-for-zip: could not derive random build key.' . PHP_EOL );
				exit( 1 );
			}
		}
		$key_hex                       = bin2hex( $aes_key_binary );
		$pk_a                          = substr( $key_hex, 0, 32 );
		$pk_b                          = substr( $key_hex, 32, 32 );
		$key_enc_blocks                = injector_lmfwc_encrypt_build_blob( $key, $aes_key_binary );
		$secret_enc_blocks             = injector_lmfwc_encrypt_build_blob( $secret, $aes_key_binary );
		$define_key                    = '';
		$define_secret                 = '';
	}

	return implode(
		"\n",
		array(
			'// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Distribution LMFWC embedding.',
			'if ( ! defined( \'LANDTECH_EXTRAS_DEFAULT_CONSUMER_KEY\' ) ) {',
			"\tdefine( 'LANDTECH_EXTRAS_DEFAULT_CONSUMER_KEY', " . var_export( $define_key, true ) . ' );',
			'}',
			'if ( ! defined( \'LANDTECH_EXTRAS_DEFAULT_CONSUMER_SECRET\' ) ) {',
			"\tdefine( 'LANDTECH_EXTRAS_DEFAULT_CONSUMER_SECRET', " . var_export( $define_secret, true ) . ' );',
			'}',
			'if ( ! defined( \'LANDTECH_EXTRAS_DEFAULT_CONSUMER_KEY_ENC\' ) ) {',
			"\tdefine( 'LANDTECH_EXTRAS_DEFAULT_CONSUMER_KEY_ENC', " . var_export( $key_enc_blocks, true ) . ' );',
			'}',
			'if ( ! defined( \'LANDTECH_EXTRAS_DEFAULT_CONSUMER_SECRET_ENC\' ) ) {',
			"\tdefine( 'LANDTECH_EXTRAS_DEFAULT_CONSUMER_SECRET_ENC', " . var_export( $secret_enc_blocks, true ) . ' );',
			'}',
			'if ( ! defined( \'LANDTECH_EXTRAS_BAKED_LMFWC_PK_A\' ) ) {',
			"\tdefine( 'LANDTECH_EXTRAS_BAKED_LMFWC_PK_A', " . var_export( $pk_a, true ) . ' );',
			'}',
			'if ( ! defined( \'LANDTECH_EXTRAS_BAKED_LMFWC_PK_B\' ) ) {',
			"\tdefine( 'LANDTECH_EXTRAS_BAKED_LMFWC_PK_B', " . var_export( $pk_b, true ) . ' );',
			'}',
			'// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound',
			'//',
		)
	);
}

/**
 * @param string $plaintext Plaintext LMFWC value.
 * @param string $key32    32-byte AES key.
 * @return string base64_iv_plus_ciphertext.
 */
function injector_lmfwc_encrypt_build_blob( $plaintext, $key32 ) {
	$plaintext = (string) $plaintext;
	$key32     = (string) $key32;
	if ( strlen( $key32 ) !== 32 ) {
		fwrite( STDERR, 'inject-lmfwc-credentials-for-zip: internal build key wrong length.' . PHP_EOL );
		exit( 1 );
	}
	if ( function_exists( 'random_bytes' ) ) {
		$iv = random_bytes( 16 );
	} else {
		$iv = openssl_random_pseudo_bytes( 16 );
		if ( false === $iv ) {
			fwrite( STDERR, 'inject-lmfwc-credentials-for-zip: IV generation failed.' . PHP_EOL );
			exit( 1 );
		}
	}
	$cipher = openssl_encrypt( $plaintext, 'AES-256-CBC', $key32, OPENSSL_RAW_DATA, $iv );
	if ( false === $cipher ) {
		fwrite( STDERR, 'inject-lmfwc-credentials-for-zip: openssl_encrypt failed.' . PHP_EOL );
		exit( 1 );
	}

	return base64_encode( $iv . $cipher );
}
