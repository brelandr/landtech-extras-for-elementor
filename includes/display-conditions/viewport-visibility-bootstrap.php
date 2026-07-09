<?php
/**
 * Viewport visibility bridge — frontend cookie + script registration.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return void
 */
function landtech_extras_viewport_visibility_bootstrap() {

	static $bootstrapped = false;

	if ( $bootstrapped ) {
		return;
	}

	$bootstrapped = true;

	add_action(
		'wp_enqueue_scripts',
		static function () {
			if ( is_admin() ) {
				return;
			}

			$default = version_compare( get_bloginfo( 'version' ), '7.0', '>=' );
			if ( function_exists( 'landtech_extras_feature_enabled' ) && ! landtech_extras_feature_enabled( 'platform_viewport_visibility', $default ) ) {
				return;
			}

			if ( ! defined( 'LANDTECH_EXTRAS__FILE__' ) || ! defined( 'LANDTECH_EXTRAS_PATH' ) ) {
				return;
			}

			$script_path = LANDTECH_EXTRAS_PATH . 'assets/js/viewport-bridge.js';

			if ( ! is_readable( $script_path ) ) {
				return;
			}

			wp_register_script(
				'landtech-extras-viewport-bridge',
				plugins_url( 'assets/js/viewport-bridge.js', LANDTECH_EXTRAS__FILE__ ),
				array(),
				defined( 'LANDTECH_EXTRAS_VERSION' ) ? LANDTECH_EXTRAS_VERSION : '2.4.4',
				true
			);
			wp_enqueue_script( 'landtech-extras-viewport-bridge' );
		},
		20
	);
}

landtech_extras_viewport_visibility_bootstrap();
