<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Optional SVG upload support for the Inline SVG widget and Media Library.
 *
 * @since 2.2.96
 */
class Landtech_Extras_Svg_Upload {

	/**
	 * Register upload filters.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'upload_mimes', array( __CLASS__, 'allow_svg_mime_type' ) );
		add_filter( 'wp_check_filetype_and_ext', array( __CLASS__, 'fix_svg_filetype' ), 10, 4 );
		add_filter( 'wp_handle_upload_prefilter', array( __CLASS__, 'sanitize_svg_upload' ) );
		add_filter( 'elementor/files/allow_unfiltered_upload', array( __CLASS__, 'allow_elementor_svg_upload' ) );
	}

	/**
	 * Whether SVG uploads are enabled in LandTech Extras settings.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		if ( function_exists( 'landtech_extras_svg_uploads_enabled' ) ) {
			return landtech_extras_svg_uploads_enabled();
		}

		$options = get_option( 'landtech_extras_advanced', array() );

		return is_array( $options ) && isset( $options['enable_svg_uploads'] ) && 'yes' === $options['enable_svg_uploads'];
	}

	/**
	 * Whether the current user may upload SVG files.
	 *
	 * @return bool
	 */
	public static function current_user_can_upload() {
		return current_user_can( 'upload_files' );
	}

	/**
	 * Add SVG to allowed MIME types when the feature is enabled.
	 *
	 * @param array<string,string> $mimes Allowed MIME types.
	 * @return array<string,string>
	 */
	public static function allow_svg_mime_type( $mimes ) {
		if ( ! self::is_enabled() || ! self::current_user_can_upload() ) {
			return $mimes;
		}

		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';

		return $mimes;
	}

	/**
	 * Fix SVG extension detection on hosts with inconsistent fileinfo output.
	 *
	 * @param array<string,mixed> $data     File data array.
	 * @param string              $file     Full path to the file.
	 * @param string              $filename File name.
	 * @param array<string,string> $mimes    Allowed MIME types.
	 * @return array<string,mixed>
	 */
	public static function fix_svg_filetype( $data, $file, $filename, $mimes ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( ! self::is_enabled() || ! self::current_user_can_upload() ) {
			return $data;
		}

		unset( $file, $mimes );

		$extension = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

		if ( 'svg' === $extension || 'svgz' === $extension ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}

		return $data;
	}

	/**
	 * Sanitize SVG uploads before WordPress moves them into uploads/.
	 *
	 * @param array<string,mixed> $file Upload file data.
	 * @return array<string,mixed>
	 */
	public static function sanitize_svg_upload( $file ) {
		if ( ! self::is_enabled() || ! self::current_user_can_upload() ) {
			return $file;
		}

		if ( empty( $file['tmp_name'] ) || ! is_string( $file['tmp_name'] ) ) {
			return $file;
		}

		$extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

		if ( 'svg' !== $extension && 'svgz' !== $extension ) {
			return $file;
		}

		if ( ! Landtech_Extras_Svg_Sanitizer::can_sanitize() ) {
			$file['error'] = esc_html__( 'SVG uploads require the PHP DOM extension on this server.', 'landtech-extras-for-elementor' );
			return $file;
		}

		$sanitizer = new Landtech_Extras_Svg_Sanitizer();

		if ( ! $sanitizer->sanitize_file( $file['tmp_name'] ) ) {
			$file['error'] = esc_html__( 'This SVG file could not be sanitized and was rejected for security reasons.', 'landtech-extras-for-elementor' );
		}

		return $file;
	}

	/**
	 * Allow Elementor media uploads for SVG when LandTech Extras enables sanitized uploads.
	 *
	 * @param bool $enabled Whether Elementor unfiltered uploads are enabled.
	 * @return bool
	 */
	public static function allow_elementor_svg_upload( $enabled ) {
		if ( $enabled ) {
			return $enabled;
		}

		if ( ! self::is_enabled() || ! self::current_user_can_upload() ) {
			return $enabled;
		}

		return Landtech_Extras_Svg_Sanitizer::can_sanitize();
	}
}

Landtech_Extras_Svg_Upload::init();
