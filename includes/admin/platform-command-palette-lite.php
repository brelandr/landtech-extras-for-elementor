<?php
/**
 * Free admin command palette — LandTech settings + documentation shortcuts only.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.4.0
 */
final class Platform_Command_Palette_Lite {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ), 50 );
	}

	/**
	 * @return void
	 */
	public static function enqueue() {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! wp_script_is( 'wp-commands', 'registered' ) ) {
			return;
		}

		$handle = 'landtech-extras-admin-command-palette-lite';
		wp_register_script(
			$handle,
			plugins_url( 'assets/js/admin-command-palette-lite.js', LANDTECH_EXTRAS__FILE__ ),
			array( 'wp-commands' ),
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_enqueue_script( $handle );

		wp_localize_script(
			$handle,
			'ltxCommandPaletteLite',
			array(
				'settingsUrl' => esc_url_raw( admin_url( 'admin.php?page=landtech-extras' ) ),
				'docsUrl'     => esc_url_raw( 'https://landtechwebdesigns.com/' ),
				'i18n'        => array(
					'openSettings' => __( 'LandTech: Open LandTech Extras settings', 'landtech-extras-for-elementor' ),
					'openDocs'     => __( 'LandTech: Open plugin documentation', 'landtech-extras-for-elementor' ),
				),
			)
		);
	}
}
