<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves viewport width buckets aligned with theme.json when available.
 *
 * @since 2.4.4
 */
final class Viewport_Breakpoints {

	/**
	 * Cookie name for client-reported viewport width (optional bridge).
	 */
	public const WIDTH_COOKIE = 'ltxe_vp_width';

	/**
	 * @return int Current viewport width in CSS pixels (best effort).
	 */
	public static function get_current_width() {

		if ( isset( $_COOKIE[ self::WIDTH_COOKIE ] ) ) {
			$raw = sanitize_text_field( wp_unslash( $_COOKIE[ self::WIDTH_COOKIE ] ) );
			if ( is_numeric( $raw ) ) {
				$w = (int) $raw;
				if ( $w > 0 && $w <= 10000 ) {
					return $w;
				}
			}
		}

		if ( function_exists( 'wp_is_mobile' ) && wp_is_mobile() ) {
			return 375;
		}

		return 1280;
	}

	/**
	 * @return array{mobile:int,tablet:int,desktop:int}
	 */
	public static function get_breakpoints() {

		$defaults = array(
			'mobile'  => 767,
			'tablet'  => 1024,
			'desktop' => 1025,
		);

		if ( ! function_exists( 'wp_get_global_settings' ) ) {
			return $defaults;
		}

		$settings = wp_get_global_settings();
		if ( ! is_array( $settings ) || empty( $settings['layout']['wideSize'] ) ) {
			return $defaults;
		}

		/**
		 * Filter viewport breakpoint map used by the viewport display condition.
		 *
		 * @since 2.4.4
		 *
		 * @param array{mobile:int,tablet:int,desktop:int} $defaults Breakpoint max widths.
		 */
		return apply_filters( 'landtech_extras/viewport_breakpoints', $defaults );
	}

	/**
	 * @param string $bucket mobile|tablet|desktop.
	 * @return bool
	 */
	public static function matches_bucket( $bucket ) {

		$width = self::get_current_width();
		$bp    = self::get_breakpoints();

		switch ( $bucket ) {
			case 'mobile':
				return $width <= (int) $bp['mobile'];
			case 'tablet':
				return $width > (int) $bp['mobile'] && $width <= (int) $bp['tablet'];
			case 'desktop':
				return $width >= (int) $bp['desktop'];
			default:
				return false;
		}
	}
}
