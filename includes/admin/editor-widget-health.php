<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.

namespace LandTechExtras\Admin;

use LandTechExtras\Dismiss_Notice;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Editor panel health notices for LandTech Extras widgets (missing API keys, empty content, etc.).
 *
 * @since 2.3.4
 */
class Editor_Widget_Health {

	/**
	 * Register hooks.
	 *
	 * @since 2.3.4
	 * @return void
	 */
	public static function init() {
		add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue editor health script and localized configuration.
	 *
	 * @since 2.3.4
	 * @return void
	 */
	public static function enqueue_scripts() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script(
			'landtech-extras-editor-widget-health',
			plugins_url( '/assets/js/editor-widget-health' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			array( 'jquery', 'landtech-extras-editor' ),
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_localize_script(
			'landtech-extras-editor-widget-health',
			'landtechExtrasWidgetHealth',
			self::get_editor_config()
		);

		wp_enqueue_script( 'landtech-extras-editor-widget-health' );
	}

	/**
	 * Build configuration passed to the Elementor editor script.
	 *
	 * @since 2.3.4
	 * @return array<string,mixed>
	 */
	public static function get_editor_config() {
		$google_maps_api_key = '';

		if ( class_exists( '\LandTechExtras\LandTechExtrasPlugin' ) && isset( \LandTechExtras\LandTechExtrasPlugin::$instance->settings ) ) {
			$google_maps_api_key = (string) \LandTechExtras\LandTechExtrasPlugin::$instance->settings->get_option(
				'google_maps_api_key',
				'landtech_extras_apis',
				''
			);
		}

		return array(
			'ajaxUrl'             => admin_url( 'admin-ajax.php' ),
			'dismissNonce'        => wp_create_nonce( 'dismissible-notice' ),
			'googleMapsApiKeySet' => '' !== trim( $google_maps_api_key ),
			'dismissed'           => self::get_dismissed_notice_keys(),
			'strings'             => array(
				'dismiss'              => __( 'Dismiss', 'landtech-extras-for-elementor' ),
				'googleMapsMissingKey' => __( 'Google Maps is selected but no API key is configured. Add your key under Elementor → LandTech Extras → APIs.', 'landtech-extras-for-elementor' ),
				'galleryEmpty'         => __( 'This gallery has no images yet. Add images in the Gallery section or switch the gallery source.', 'landtech-extras-for-elementor' ),
				'inlineSvgMissing'     => __( 'Inline SVG needs a media file or custom URL before it will display on the front end.', 'landtech-extras-for-elementor' ),
				'audioPlaylistEmpty'   => __( 'The audio player playlist is empty. Add at least one track in the Playlist section.', 'landtech-extras-for-elementor' ),
				'tableEmpty'           => __( 'The table has no rows. Add rows in the Table section or import CSV data.', 'landtech-extras-for-elementor' ),
			),
		);
	}

	/**
	 * Return notice keys the current user has dismissed (forever or still active).
	 *
	 * @since 2.3.4
	 * @return array<int,string>
	 */
	protected static function get_dismissed_notice_keys() {
		$keys = array(
			'ltxe-editor-health-google-map',
			'ltxe-editor-health-gallery-extra',
			'ltxe-editor-health-inline-svg',
			'ltxe-editor-health-audio-player',
			'ltxe-editor-health-table',
		);

		$dismissed = array();

		foreach ( $keys as $key ) {
			if ( ! Dismiss_Notice::is_admin_notice_active( $key . '-forever' ) ) {
				$dismissed[] = $key;
			}
		}

		return $dismissed;
	}
}
