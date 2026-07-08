<?php
/**
 * Widget preset JSON import/export for the Elementor editor.
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
final class Widget_Preset_Io {

	/**
	 * @return void
	 */
	public static function init() {
		add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'enqueue_editor_assets' ) );
		add_action( 'wp_ajax_landtech_extras_export_widget_preset', array( __CLASS__, 'ajax_export_preset' ) );
		add_action( 'wp_ajax_landtech_extras_import_widget_preset', array( __CLASS__, 'ajax_import_preset' ) );
	}

	/**
	 * @return void
	 */
	public static function enqueue_editor_assets() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$handle = 'landtech-extras-widget-preset-io';
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$file   = plugin_dir_path( LANDTECH_EXTRAS__FILE__ ) . 'assets/js/widget-preset-io' . $suffix . '.js';
		if ( ! is_readable( $file ) ) {
			$file = plugin_dir_path( LANDTECH_EXTRAS__FILE__ ) . 'assets/js/widget-preset-io.js';
		}

		wp_register_script(
			$handle,
			plugins_url( 'assets/js/' . basename( $file ), LANDTECH_EXTRAS__FILE__ ),
			array( 'jquery', 'elementor-editor' ),
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_enqueue_script( $handle );

		wp_localize_script(
			$handle,
			'ltxWidgetPresetIo',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'landtech_extras_widget_preset_io' ),
				'i18n'    => array(
					'export'       => __( 'Export widget preset', 'landtech-extras-for-elementor' ),
					'import'       => __( 'Import widget preset', 'landtech-extras-for-elementor' ),
					'working'      => __( 'Working…', 'landtech-extras-for-elementor' ),
					'exportOk'     => __( 'Preset exported to clipboard.', 'landtech-extras-for-elementor' ),
					'importOk'     => __( 'Preset imported into the selected widget.', 'landtech-extras-for-elementor' ),
					'exportFail'   => __( 'Could not export preset.', 'landtech-extras-for-elementor' ),
					'importFail'   => __( 'Could not import preset.', 'landtech-extras-for-elementor' ),
					'selectWidget' => __( 'Select a LandTech widget in the editor first.', 'landtech-extras-for-elementor' ),
					'pasteJson'    => __( 'Paste widget preset JSON:', 'landtech-extras-for-elementor' ),
				),
			)
		);
	}

	/**
	 * @return void
	 */
	public static function ajax_export_preset() {
		check_ajax_referer( 'landtech_extras_widget_preset_io', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( __( 'Unauthorized.', 'landtech-extras-for-elementor' ), 403 );
		}

		$post_id = isset( $_POST['document_id'] ) ? absint( wp_unslash( $_POST['document_id'] ) ) : 0;
		if ( $post_id < 1 || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( __( 'Invalid document.', 'landtech-extras-for-elementor' ), 403 );
		}

		$widget_id   = isset( $_POST['widget_id'] ) ? sanitize_text_field( wp_unslash( $_POST['widget_id'] ) ) : '';
		$widget_type = isset( $_POST['widget_type'] ) ? sanitize_key( wp_unslash( $_POST['widget_type'] ) ) : '';
		$settings    = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : '';

		if ( '' === $widget_id || '' === $widget_type ) {
			wp_send_json_error( __( 'Missing widget metadata.', 'landtech-extras-for-elementor' ), 400 );
		}

		$decoded = json_decode( (string) $settings, true );
		if ( ! is_array( $decoded ) ) {
			wp_send_json_error( __( 'Invalid settings payload.', 'landtech-extras-for-elementor' ), 400 );
		}

		$export = array(
			'version'     => 1,
			'plugin'      => 'landtech-extras-for-elementor',
			'widget_type' => $widget_type,
			'exported_at' => gmdate( 'c' ),
			'settings'    => self::sanitize_settings_array( $decoded ),
		);

		wp_send_json_success(
			array(
				'preset' => $export,
			)
		);
		wp_die();
	}

	/**
	 * @return void
	 */
	public static function ajax_import_preset() {
		check_ajax_referer( 'landtech_extras_widget_preset_io', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( __( 'Unauthorized.', 'landtech-extras-for-elementor' ), 403 );
		}

		$post_id = isset( $_POST['document_id'] ) ? absint( wp_unslash( $_POST['document_id'] ) ) : 0;
		if ( $post_id < 1 || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( __( 'Invalid document.', 'landtech-extras-for-elementor' ), 403 );
		}

		$widget_type = isset( $_POST['widget_type'] ) ? sanitize_key( wp_unslash( $_POST['widget_type'] ) ) : '';
		$raw         = isset( $_POST['preset_json'] ) ? wp_unslash( $_POST['preset_json'] ) : '';

		$decoded = json_decode( (string) $raw, true );
		if ( ! is_array( $decoded ) ) {
			wp_send_json_error( __( 'Invalid preset JSON.', 'landtech-extras-for-elementor' ), 400 );
		}

		$preset_type = isset( $decoded['widget_type'] ) ? sanitize_key( (string) $decoded['widget_type'] ) : '';
		if ( '' === $preset_type || $preset_type !== $widget_type ) {
			wp_send_json_error( __( 'Preset widget type does not match the selected widget.', 'landtech-extras-for-elementor' ), 400 );
		}

		$settings = isset( $decoded['settings'] ) && is_array( $decoded['settings'] ) ? $decoded['settings'] : array();

		wp_send_json_success(
			array(
				'settings' => self::sanitize_settings_array( $settings ),
			)
		);
		wp_die();
	}

	/**
	 * @param array<string, mixed> $settings Raw settings.
	 * @return array<string, mixed>
	 */
	private static function sanitize_settings_array( $settings ) {
		$out = array();
		foreach ( $settings as $key => $value ) {
			$sk = sanitize_key( (string) $key );
			if ( '' === $sk ) {
				continue;
			}
			if ( is_array( $value ) ) {
				$out[ $sk ] = self::sanitize_settings_array( $value );
				continue;
			}
			if ( is_bool( $value ) || is_int( $value ) || is_float( $value ) ) {
				$out[ $sk ] = $value;
				continue;
			}
			$out[ $sk ] = sanitize_text_field( (string) $value );
		}
		return $out;
	}
}
