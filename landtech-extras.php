<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Disclaimer: This is an independent fork of the original Elementor Extras plugin by Namogo.
 * It is not affiliated with or endorsed by Namogo or Elementor Ltd.
 *
 * Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
 *
 * Plugin Name:       LandTech Extras for Elementor
 * Plugin URI:        https://landtechwebdesigns.com/
 * Description:       Elementor widgets & extensions — fork of Elementor Extras (Extras for Elementor). Free on WordPress.org.
 * Version:           2.4.0
 * Elementor tested up to: 3.28
 * Elementor Pro tested up to: 3.28
 *
 * Original plugin: Elementor Extras (copyright Namogo, https://extrasforelementor.com/).
 * Author:            brelandr
 * Author URI:        https://profiles.wordpress.org/brelandr/
 *
 * Maintainer:        Land Tech Web Designs
 * Maintainer URI:    https://landtechwebdesigns.com/
 *
 * Text Domain:        landtech-extras-for-elementor
 * Domain Path:        /languages
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * License:            GPLv3 or later
 * License URI:        https://www.gnu.org/licenses/gpl-3.0.html
 *
 * This plugin is free software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see https://www.gnu.org/licenses/
 * Maintained by Land Tech Web Designs: https://landtechwebdesigns.com/
 *
 * LandTech Extras for Elementor incorporates code from:

 * — jquery-circle-progress v1.2.2, Copyright Rostyslav Bryzgunov Licenses: MIT Source: link http://kottenator.github.io/jquery-circle-progress/
 * — jQuery appear plugin v0.3.6, Copyright 2012 Andrey Sidorov Licenses: MIT Source: link https://github.com/morr/jquery.appear/
 * — LongShadow jQuery Plugin v1.1.0, Copyright 2013 - 2016 Dang Van Thanh Licenses: MIT Source: link git://github.com/dangvanthanh/jquery.longShadow.git
 * — HC-Sticky 2.2.3, Copyright Some Web Media License: MIT Source: link https://github.com/somewebmedia/hc-sticky
 * — jQuery Mobile v1.4.3, Copyright 2010, 2014 jQuery Foundation, Inc. Licenses: jquery.org/license
 * — jquery-visible, Copyright 2012, Digital Fusion, License: http://teamdf.com/jquery-plugins/license/ Source: http://teamdf.com/jquery-plugins/license/
 * — Parallax Background v1.2, by Eren Suleymanoglu Licenses: MIT Source: link https://github.com/erensuleymanoglu/parallax-background
 * — TableSorter v2.32.0 (Mottie fork), Copyright 2007 Christian Bach / Rob Garrison Licenses: Dual licensed under the MIT and GPL licenses Source: link https://github.com/Mottie/tablesorter
 * — Isotope v3.0.6, Copyright Metafizzy License: GPLv3 Source: link https://github.com/metafizzy/isotope
 * — Infinite Scroll v4.0.1, Copyright Metafizzy License: GPLv3 Source: link https://infinite-scroll.com (vanilla DOM API; no bundled jQuery)
 * — Packery v2.1.2 Copyright Metafizzy License: GPLv3 Source: link https://github.com/metafizzy/packery
 * — javascript-detect-element-resize, 0.5.3 Copyright (c) 2013 Sebastián Décima License: MIT Source: link https://github.com/sdecima/javascript-detect-element-resize
 * — tilt.js 1.2.1, Copyright (c) 2017 Gijs Rogé License: MIT Source: link https://github.com/gijsroge/tilt.js
 * - CLNDR v1.4.7, Copyright Kyle Stetz (github.com/kylestetz) License: MIT Source: link https://github.com/kylestetz/CLNDR
 * — GMAP3 Plugin for jQuery v7.2 Copyright DEMONTE Jean-Baptiste License: GPL-3.0+ Source: link http://gmap3.net
 * — Leaflet v1.9.4, Copyright Vladimir Agafonkin License: BSD-2-Clause Source: link https://github.com/Leaflet/Leaflet
 * — Moment.js (WordPress core script handle `moment`; MIT) Source: https://github.com/moment/moment/
 * — Slidebars v2 Copyright Adam Charles Smith License: MIT http://www.adchsm.com/slidebars/license/ Source: link http://www.adchsm.com/slidebars/
 * — anime.js v4.0.2, Copyright Julian Garnier License: MIT Source: link https://github.com/juliangarnier/anime
 * — Schedule-X Calendar v4.6.1, Copyright Schedule-X License: MIT Source: link https://github.com/schedule-x/schedule-x
 * — @lottiefiles/lottie-player (bundled as lottie-player.js), Copyright LottieFiles License: MIT Source: link https://github.com/LottieFiles/lottie-player
 * — WaveSurfer.js v7.9.9, Copyright katspaugh License: BSD-3-Clause Source: link https://github.com/kwavesurfer/wavesurfer.js
 * — Splitting.js v1.0.6, Copyright Shaw License: MIT Source: link https://github.com/shshaw/Splitting
 * — GLightbox v3.3.1, Copyright Biati Digital License: MIT Source: link https://github.com/biati-digital/glightbox
 */

require_once dirname( __FILE__ ) . '/includes/landtech-extras-org-coexistence.php';
require_once dirname( __FILE__ ) . '/includes/landtech-extras-bootstrap-guard.php';

if (
	! landtech_extras_org_premium_addon_coexistence_live( __FILE__ )
	&& ! landtech_extras_prepare_main_bootstrap( __FILE__ )
) {
	return;
}

if ( landtech_extras_shared_constants_claimed_by_other_bootstrap( __FILE__ ) ) {
	add_action( 'admin_notices', 'landtech_extras_shared_constant_collision_admin_notice', 5 );
} else {

if ( ! defined( 'LANDTECH_EXTRAS__FILE__' ) ) {
	define( 'LANDTECH_EXTRAS__FILE__', __FILE__ );
}
if ( ! defined( 'LANDTECH_EXTRAS_FREE_MAIN_FILE' ) ) {
	define( 'LANDTECH_EXTRAS_FREE_MAIN_FILE', __FILE__ );
}
if ( ! defined( 'LANDTECH_EXTRAS_PLUGIN_BASE' ) ) {
	define( 'LANDTECH_EXTRAS_PLUGIN_BASE', plugin_basename( LANDTECH_EXTRAS__FILE__ ) );
}
if ( ! defined( 'LANDTECH_EXTRAS_URL' ) ) {
	define( 'LANDTECH_EXTRAS_URL', plugins_url( '/', LANDTECH_EXTRAS__FILE__ ) );
}
if ( ! defined( 'LANDTECH_EXTRAS_PATH' ) ) {
	define( 'LANDTECH_EXTRAS_PATH', plugin_dir_path( LANDTECH_EXTRAS__FILE__ ) );
}
if ( ! defined( 'LANDTECH_EXTRAS_ASSETS_URL' ) ) {
	define( 'LANDTECH_EXTRAS_ASSETS_URL', LANDTECH_EXTRAS_URL . 'assets/' );
}
if ( ! defined( 'LANDTECH_EXTRAS_VERSION' ) ) {
	define( 'LANDTECH_EXTRAS_VERSION', '2.4.0' );
}
if ( ! defined( 'LANDTECH_EXTRAS_PREVIOUS_STABLE_VERSION' ) ) {
	define( 'LANDTECH_EXTRAS_PREVIOUS_STABLE_VERSION', '2.2.64' );
}
if ( ! defined( 'LANDTECH_EXTRAS_ELEMENTOR_VERSION_REQUIRED' ) ) {
	define( 'LANDTECH_EXTRAS_ELEMENTOR_VERSION_REQUIRED', '3.5.0' );
}
if ( ! defined( 'LANDTECH_EXTRAS_ELEMENTOR_PRO_VERSION_REQUIRED' ) ) {
	define( 'LANDTECH_EXTRAS_ELEMENTOR_PRO_VERSION_REQUIRED', '3.5.0' );
}
if ( ! defined( 'LANDTECH_EXTRAS_PHP_VERSION_REQUIRED' ) ) {
	define( 'LANDTECH_EXTRAS_PHP_VERSION_REQUIRED', '7.4' );
}
if ( ! defined( 'LANDTECH_EXTRAS_PHP_VERSION_RECOMMENDED' ) ) {
	define( 'LANDTECH_EXTRAS_PHP_VERSION_RECOMMENDED', '8.0' );
}
if ( ! defined( 'LANDTECH_EXTRAS_TEXTDOMAIN' ) ) {
	define( 'LANDTECH_EXTRAS_TEXTDOMAIN', 'landtech-extras-for-elementor' );
}

if ( ! defined( 'LANDTECH_EXTRAS_PLUGIN_EDITION' ) ) {
	define( 'LANDTECH_EXTRAS_PLUGIN_EDITION', 'free' );
}

/*
 * Org package runtime marker (Premium add-on detection). Read-only sentinel.
 */
if ( ! defined( 'LANDTECH_EXTRAS_RUNTIME_FAMILY' ) ) {
	define( 'LANDTECH_EXTRAS_RUNTIME_FAMILY', 'free' );
}

/**
 * Eager-load the plugin’s `.mo` before other plugins run on `plugins_loaded`.
 *
 * WordPress.org-hosted plugins: core loads translations for the plugin text domain automatically
 * (WordPress 4.6+); we do not call {@see load_plugin_textdomain()} (Plugin Check).
 *
 * WordPress 6.7+: JIT text loading is deferred until {@see after_setup_theme}; gettext on
 * `plugins_loaded` can log “triggered too early”. This callback loads a readable `.mo` via
 * {@see load_textdomain()} when present, otherwise seeds {@see NOOP_Translations} so the domain is
 * marked loaded and JIT is skipped until core/locale files are available.
 *
 * Priority **-10000** runs before typical add-on bootstraps (e.g. `-1`) so Premium or other code
 * never hits {@see _load_textdomain_just_in_time()} while `did_action( 'after_setup_theme' )` is still
 * false.
 *
 * @since 2.2.64
 * @since 2.2.65 No-op catalog when no `.mo` exists (avoids 6.7 JIT on default-locale sites).
 * @return void
 */
function landtech_extras_load_textdomain_mo_early() {

	if ( ! defined( 'LANDTECH_EXTRAS__FILE__' ) || ! defined( 'LANDTECH_EXTRAS_TEXTDOMAIN' ) || ! defined( 'LANDTECH_EXTRAS_PATH' ) ) {
		return;
	}

	if ( ! function_exists( 'determine_locale' ) || ! function_exists( 'load_textdomain' ) ) {
		return;
	}

	$domain = LANDTECH_EXTRAS_TEXTDOMAIN;
	$locale = determine_locale();

	$candidates = array();

	if ( defined( 'WP_LANG_DIR' ) ) {
		$candidates[] = trailingslashit( (string) constant( 'WP_LANG_DIR' ) ) . 'plugins/' . $domain . '-' . $locale . '.mo';
	}

	$candidates[] = trailingslashit( LANDTECH_EXTRAS_PATH ) . 'languages/' . $domain . '-' . $locale . '.mo';

	foreach ( $candidates as $mofile ) {
		if ( is_string( $mofile ) && '' !== $mofile && is_readable( $mofile ) ) {
			load_textdomain( $domain, $mofile, $locale );
			return;
		}
	}

	/*
	 * No `.mo` on disk (typical for `en_US` / unpublished locales). Without an entry in `$l10n`,
	 * the first `__()` still invokes {@see _load_textdomain_just_in_time()} before `after_setup_theme`
	 * and WordPress 6.7+ logs “triggered too early”. A `NOOP_Translations` instance marks the
	 * domain loaded so gettext returns source strings and skips JIT.
	 */
	global $l10n;

	if ( isset( $l10n[ $domain ] ) ) {
		return;
	}

	if ( class_exists( 'NOOP_Translations' ) ) {
		$l10n[ $domain ] = new NOOP_Translations();
	}
}

add_action( 'plugins_loaded', 'landtech_extras_load_textdomain_mo_early', -10000 );

/**
 * Bootstrap: load after Elementor fires `elementor/loaded`.
 *
 * LandTech must not bail on `plugins_loaded` when Elementor has not run yet — otherwise
 * a later alphabetical/load order can skip the entire plugin (no widgets in the editor).
 *
 * @since 2.2.53
 */
function landtech_extras_bootstrap() {
	if ( did_action( 'elementor/loaded' ) ) {
		landtech_extras_load();
		return;
	}

	add_action( 'elementor/loaded', 'landtech_extras_load' );
}

/**
 * Show an admin notice only if `elementor/loaded` never ran (inactive/missing Elementor).
 *
 * @since 2.2.53
 */
function landtech_extras_maybe_admin_notice_elementor_missing() {
	if ( did_action( 'elementor/loaded' ) ) {
		return;
	}

	if ( ! is_admin() ) {
		return;
	}

	add_action( 'admin_notices', 'landtech_extras_fail_load' );
}

/**
 * Load Extras
 *
 * Runs after Elementor has fired `elementor/loaded` (see {@see landtech_extras_bootstrap()}).
 *
 * @since 0.1.0
 */
function landtech_extras_load() {

	// add_action( 'admin_notices', 'landtech_extras_disable_widgets_notice' );

	// Dismissable notices
	if ( is_admin() ) {
		landtech_extras_include( 'admin/dismiss-notice.php' );

		add_action( 'admin_init', array( '\LandTechExtras\Dismiss_Notice', 'init' ) );
	}

	// Check Elementor version required
	if ( ! version_compare( ELEMENTOR_VERSION, LANDTECH_EXTRAS_ELEMENTOR_VERSION_REQUIRED, '>=' ) ) {

		add_action( 'admin_notices', 	'landtech_extras_fail_load_out_of_date' );
		add_action( 'admin_init', 		'landtech_extras_deactivate' );
		return;
	}

	// Check Elementor Pro version required
	if ( landtech_extras_is_elementor_pro_active() ) {
		if ( ! version_compare( ELEMENTOR_PRO_VERSION, LANDTECH_EXTRAS_ELEMENTOR_PRO_VERSION_REQUIRED, '>=' ) ) {
			add_action( 'admin_notices', 	'landtech_extras_fail_load_elementor_pro_out_of_date', 9999 );
			add_action( 'admin_init', 		'landtech_extras_deactivate' );
			return;
		}
	}

	// Check for required PHP version
	if ( version_compare( PHP_VERSION, LANDTECH_EXTRAS_PHP_VERSION_REQUIRED, '<' ) ) {

		add_action( 'admin_notices', 	'landtech_extras_php_fail' );
		add_action( 'admin_init', 		'landtech_extras_deactivate' );
		return;
	}

	// Check for recommended PHP version
	if ( version_compare( PHP_VERSION, LANDTECH_EXTRAS_PHP_VERSION_RECOMMENDED, '<' ) ) {
		add_action( 'admin_notices', 	'landtech_extras_php_notice' );
	}

	// add_action( 'admin_init', 'landtech_extras_info_redirect' );

	// Includes (extension API before main plugin singleton).
	landtech_extras_include( 'admin/settings-api.php' );
	landtech_extras_include( 'includes/extension-api.php' );
	landtech_extras_include( 'includes/search-rest-controller.php' );
	landtech_extras_include( 'includes/plugin.php' );

	// Admin-only editor tools.
	if ( is_admin() ) {
		landtech_extras_include( 'includes/admin/editor-widget-health.php' );
		landtech_extras_include( 'includes/admin/widget-preset-io.php' );
		landtech_extras_include( 'includes/admin/platform-command-palette-lite.php' );
		add_action( 'elementor/init', array( '\LandTechExtras\Admin\Editor_Widget_Health', 'init' ) );
		add_action( 'init', array( '\LandTechExtras\Admin\Widget_Preset_Io', 'init' ) );
		add_action( 'init', array( '\LandTechExtras\Admin\Platform_Command_Palette_Lite', 'init' ) );
		landtech_extras_include( 'admin/settings-page.php' );
		landtech_extras_include( 'admin/settings.php' );
	}
}

add_action( 'plugins_loaded', 'landtech_extras_bootstrap', 20 );
add_action( 'plugins_loaded', 'landtech_extras_maybe_admin_notice_elementor_missing', 1000 );
add_action( 'activate_plugin', 	'landtech_extras_before_activation' , 10, 2);
add_filter( 'plugin_action_links_' . LANDTECH_EXTRAS_PLUGIN_BASE, 'landtech_extras_plugin_action_links' );
add_filter( 'plugin_row_meta', 'landtech_extras_plugin_row_meta', 10, 2 );

register_activation_hook( LANDTECH_EXTRAS__FILE__, 'landtech_extras_activate' );

/**
 * Runs code upon activation
 *
 * @since 1.1.3
 */
function landtech_extras_activate() {
	add_option( 'landtech_extras_do_activation_redirect', true );
}

/**
 * Deactivates the plugin
 *
 * @since 0.1.0
 */
function landtech_extras_deactivate() {
	deactivate_plugins( LANDTECH_EXTRAS_PLUGIN_BASE );
}

/**
 * Redirects to info page
 *
 * @since 1.1.3
 */
function landtech_extras_info_redirect(  ) {

	if ( get_option( 'landtech_extras_do_activation_redirect', false ) ) {
    	delete_option( 'landtech_extras_do_activation_redirect' );

		// Bulk activation skips single-plugin redirect UX.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only branching on core plugins.php query args.
		if ( ! isset( $_GET['activate-multi'] ) && version_compare( LANDTECH_EXTRAS_VERSION, get_option( '_landtech_extras_was_activated_version' ), '>' ) ) {
			
			update_option( '_landtech_extras_was_activated_version', LANDTECH_EXTRAS_VERSION );

			wp_safe_redirect( admin_url( 'admin.php?page=landtech-extras' ) );
			exit;
		}
	}
}

/**
 * Wrapper for including files
 *
 * @since 1.1.3
 */
function landtech_extras_include( $file ) {

	$path = landtech_extras_get_path( $file );

	if ( file_exists( $path ) ) {
		include_once( $path );
	}
}

/**
 * Returns the path to a file relative to our plugin
 *
 * @since 1.1.3
 */
function landtech_extras_get_path( $path ) {
	
	return LANDTECH_EXTRAS_PATH . $path;
	
}

/**
 * Handles admin notice for non-active
 * Elementor plugin situations
 *
 * @since 0.1.0
 */
function landtech_extras_fail_load() {
	$class = 'notice notice-error';
	$message = sprintf(
		/* translators: 1: Opening <strong> tag, 2: Closing </strong> tag. */
		__( 'You need %1$sElementor%2$s for %1$sLandTech Extras%2$s to work.', 'landtech-extras-for-elementor' ),
		'<strong>',
		'</strong>'
	);

	$plugin = 'elementor/elementor.php';

	if ( landtech_extras_is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$message = sprintf(
			/* translators: 1: Opening <strong> tag, 2: Closing </strong> tag. */
			__( 'You need to activate %1$sElementor%2$s for %1$sLandTech Extras%2$s to work.', 'landtech-extras-for-elementor' ),
			'<strong>',
			'</strong>'
		);

		$action_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
		$button_label = __( 'Activate Elementor', 'landtech-extras-for-elementor' );

	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$action_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
		$button_label = __( 'Install Elementor', 'landtech-extras-for-elementor' );
	}

	$button = '<p><a href="' . esc_url( $action_url ) . '" class="button-primary">' . esc_html( $button_label ) . '</a></p><p></p>';

	printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $class ), wp_kses_post( $message ), wp_kses_post( $button ) );
}

/**
 * Handles admin notice for outdated Elementor version
 *
 * @since 0.1.0
 */
function landtech_extras_fail_load_out_of_date() {
	$class = 'notice notice-error';
	$message = sprintf(
		/* translators: 1: Minimum Elementor version. 2: Previous LandTech Extras stable version. */
		__( 'This version of LandTech Extras requires at least Elementor version %1$s. Please update Elementor and re-activate LandTech Extras, or revert LandTech Extras to version %2$s.', 'landtech-extras-for-elementor' ),
		LANDTECH_EXTRAS_ELEMENTOR_VERSION_REQUIRED,
		LANDTECH_EXTRAS_PREVIOUS_STABLE_VERSION
	);

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

/**
 * Handles admin notice for outdated Elementor Pro version
 *
 * @since 1.1.2
 */
function landtech_extras_fail_load_elementor_pro_out_of_date() {
	$class = 'notice notice-error';
	$message = sprintf(
		/* translators: 1: Minimum Elementor Pro version. 2: Previous LandTech Extras stable version. */
		__( 'This version of LandTech Extras requires you update Elementor Pro to at least version %1$s to avoid any issues. We have deactivated LandTech Extras for now. Please update Elementor Pro and re-activate LandTech Extras, or revert to LandTech Extras version %2$s.', 'landtech-extras-for-elementor' ),
		LANDTECH_EXTRAS_ELEMENTOR_PRO_VERSION_REQUIRED,
		LANDTECH_EXTRAS_PREVIOUS_STABLE_VERSION
	);

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

/**
 * Handles admin notice for outdated Elementor Pro version
 *
 * @since 2.1.7
 */
function landtech_extras_before_activation() {
	if ( defined( 'ELEMENTOR_VERSION' ) && ! version_compare( ELEMENTOR_VERSION, LANDTECH_EXTRAS_ELEMENTOR_VERSION_REQUIRED, '>=' ) ) {
		wp_die(
			esc_html(
				sprintf(
					/* translators: 1: Minimum Elementor version. 2: Previous LandTech Extras stable version. */
					__( 'This version of LandTech Extras requires at least Elementor version %1$s. Please update Elementor and re-activate LandTech Extras for Elementor, or revert LandTech Extras to version %2$s.', 'landtech-extras-for-elementor' ),
					LANDTECH_EXTRAS_ELEMENTOR_VERSION_REQUIRED,
					LANDTECH_EXTRAS_PREVIOUS_STABLE_VERSION
				)
			)
		);
	}

	if ( landtech_extras_is_elementor_pro_active() ) {
		if ( defined( 'ELEMENTOR_PRO_VERSION' ) && ! version_compare( ELEMENTOR_PRO_VERSION, LANDTECH_EXTRAS_ELEMENTOR_PRO_VERSION_REQUIRED, '>=' ) ) {
			wp_die(
				esc_html(
					sprintf(
						/* translators: %s: Minimum Elementor Pro version. */
						__( 'This version of LandTech Extras requires you update Elementor Pro to at least version %s to avoid any issues. Please update Elementor Pro and re-activate LandTech Extras.', 'landtech-extras-for-elementor' ),
						LANDTECH_EXTRAS_ELEMENTOR_PRO_VERSION_REQUIRED
					)
				)
			);
		}
	}
}

/**
 * Handles admin notice for the disable widgets recommendation
 *
 * @since 2.0.0
 */
function landtech_extras_disable_widgets_notice() {

	if ( ! \LandTechExtras\Dismiss_Notice::is_admin_notice_active( 'landtech-extras-disable-widget-notice-forever' ) )
        return;

	$class = 'notice notice-error is-dismissible';
	$message = __( 'Take a moment to disable the LandTech Extras widgets and extensions that you don\'t plan on using. This will speed up the load time of the Elementor editor.', 'landtech-extras-for-elementor' );

	printf(
		'<div data-dismissible="landtech-extras-disable-widget-notice-forever" class="%1$s"><p>%2$s <a href="%3$s">%4$s</a></p></div>',
		esc_attr( $class ),
		esc_html( $message ),
		esc_url( admin_url( 'admin.php?page=landtech-extras#landtech_extras_widgets' ) ),
		esc_html( __( 'Manage widgets', 'landtech-extras-for-elementor' ) )
	);
}

/**
 * Handles admin notice for minimum PHP version required
 *
 * @since 0.1.0
 */
function landtech_extras_php_fail() {

	$class = 'notice notice-error';
	$message = sprintf(
		/* translators: %s: Minimum PHP version. */
		__( 'LandTech Extras for Elementor needs at least PHP version %s to work properly. We deactivated the plugin for now.', 'landtech-extras-for-elementor' ),
		LANDTECH_EXTRAS_PHP_VERSION_REQUIRED
	);

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );

}

/**
 * Handles admin notice for recommended PHP version
 *
 * @since 1.8.4
 */
function landtech_extras_php_notice() {

	if ( ! \LandTechExtras\Dismiss_Notice::is_admin_notice_active( 'landtech-extras-php-recommend-notice-forever' ) )
        return;

	$class = 'notice notice-warning is-dismissible';
	$message = sprintf(
		/* translators: 1: Current PHP version. 2: Recommended PHP version. */
		__( 'LandTech Extras for Elementor: You are currently running PHP version %1$s. If you experience issues loading the Elementor editor, we recommend upgrading to version %2$s or above.', 'landtech-extras-for-elementor' ),
		PHP_VERSION,
		LANDTECH_EXTRAS_PHP_VERSION_RECOMMENDED
	);

	printf( '<div data-dismissible="landtech-extras-php-recommend-notice-forever" class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

/**
 * Load wp-admin plugin API when is_plugin_active() is unavailable.
 *
 * @since 2.2.74
 */
if ( ! function_exists( 'landtech_extras_require_plugin_api' ) ) {
	function landtech_extras_require_plugin_api() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
	}
}

/**
 * Check if Elementor Pro is active
 *
 * @since 1.1.2
 *
 */
if ( ! function_exists( 'landtech_extras_is_elementor_pro_active' ) ) {
	function landtech_extras_is_elementor_pro_active() {
		landtech_extras_require_plugin_api();

		$plugin = 'elementor-pro/elementor-pro.php';

		return is_plugin_active( $plugin ) || function_exists( 'elementor_pro_load_plugin' );
	}
}

/**
 * Check if WPML String Translation plugin is active
 *
 * @since 1.8.0
 *
 */
if ( ! function_exists( 'landtech_extras_is_wpml_string_translation_active' ) ) {
	function landtech_extras_is_wpml_string_translation_active() {
		landtech_extras_require_plugin_api();

		return is_plugin_active( 'wpml-string-translation/plugin.php' );
	}
}

/**
 * Check if WooCommerce is active
 *
 * @since 1.6.0
 *
 */
if ( ! function_exists( 'landtech_extras_is_woocommerce_active' ) ) {
	function landtech_extras_is_woocommerce_active() {
		landtech_extras_require_plugin_api();

		return is_plugin_active( 'woocommerce/woocommerce.php' );
	}
}

/**
 * Check if Elementor Pro is installed
 *
 * @since 1.1.2
 *
 * @access public
 */
if ( ! function_exists( 'landtech_extras_is_elementor_installed' ) ) {
	function landtech_extras_is_elementor_installed() {
		$path 		= 'elementor/elementor.php';
		$plugins 	= get_plugins();

		return isset( $plugins[ $path ] );
	}
}

/**
 * WordPress Playground demo URL (Try It Live blueprint).
 *
 * @since 2.2.76
 *
 * @return string
 */
if ( ! function_exists( 'landtech_extras_get_playground_demo_url' ) ) {
	function landtech_extras_get_playground_demo_url() {
		$blueprint_url = 'https://plugins.svn.wordpress.org/landtech-extras-for-elementor/assets/blueprints/blueprint.json';

		/**
		 * Filter the blueprint JSON URL used for the WordPress Playground live demo.
		 *
		 * @since 2.2.76
		 *
		 * @param string $blueprint_url Public URL to blueprint.json.
		 */
		$blueprint_url = apply_filters( 'landtech_extras_playground_blueprint_url', $blueprint_url );

		return 'https://playground.wordpress.net/?blueprint-url=' . rawurlencode( $blueprint_url );
	}
}

/**
 * Add Live demo link on the Plugins screen action links.
 *
 * @since 2.2.76
 *
 * @param array<string,string> $links Existing action links.
 * @return array<string,string>
 */
if ( ! function_exists( 'landtech_extras_plugin_action_links' ) ) {
	function landtech_extras_plugin_action_links( $links ) {
		$demo_url = landtech_extras_get_playground_demo_url();

		$links['landtech_extras_playground'] = sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( $demo_url ),
			esc_html__( 'Live demo', 'landtech-extras-for-elementor' )
		);

		return $links;
	}
}

/**
 * Add Live demo link on the Plugins screen row meta.
 *
 * @since 2.2.76
 *
 * @param array<int,string> $links Existing row meta links.
 * @param string            $file  Plugin basename.
 * @return array<int,string>
 */
if ( ! function_exists( 'landtech_extras_plugin_row_meta' ) ) {
	function landtech_extras_plugin_row_meta( $links, $file ) {
		if ( LANDTECH_EXTRAS_PLUGIN_BASE !== $file ) {
			return $links;
		}

		$demo_url = landtech_extras_get_playground_demo_url();

		$links[] = sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( $demo_url ),
			esc_html__( 'Live demo', 'landtech-extras-for-elementor' )
		);

		return $links;
	}
}
}
