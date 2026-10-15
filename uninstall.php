<?php
/**
 * Remove this plugin's stored data when the site owner deletes it.
 *
 * Runs only from WordPress on delete, and only after Advanced → Delete data
 * on uninstall has been saved as on. Deactivation does not call this file.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete this plugin's options, transients, and user meta on the current site.
 *
 * @return void
 */
function landtech_extras_uninstall_current_site() {
	$advanced = get_option( 'landtech_extras_advanced', array() );
	if ( ! is_array( $advanced ) || ! isset( $advanced['delete_data_on_uninstall'] ) || 'on' !== $advanced['delete_data_on_uninstall'] ) {
		return;
	}

	$options = array(
		'landtech_extras_widgets',
		'landtech_extras_extensions',
		'landtech_extras_advanced',
		'landtech_extras_apis',
		'landtech_extras_version',
		'landtech_extras_do_activation_redirect',
		'_landtech_extras_was_activated_version',
		'ltxe_mailchimp_api_key',
		'ltxe_openai_key',
	);

	foreach ( $options as $option_name ) {
		delete_option( $option_name );
	}

	delete_transient( 'landtech_extras_updated_instagram_access_token' );

	$prefixes = array(
		'ltxe_weather_',
		'ltxe_wx_rl_',
		'ltxe_ical_',
		'ltxe_search_',
		'ltxe_vimeo_thumb_',
		'landtech_extras_instagram_posts_',
	);

	foreach ( $prefixes as $prefix ) {
		landtech_extras_uninstall_delete_transient_prefix( $prefix );
	}

	delete_metadata( 'user', 0, 'ltxe_gf_upsell_dismissed', '', true );
	delete_metadata( 'user', 0, 'ltxe_review_nudge_dismissed', '', true );
}

/**
 * Delete transients whose names start with a plugin prefix.
 *
 * @param string $prefix Transient name prefix, without the WordPress `_transient_` wrapper.
 * @return void
 */
function landtech_extras_uninstall_delete_transient_prefix( $prefix ) {
	global $wpdb;

	$like_value   = $wpdb->esc_like( '_transient_' . $prefix ) . '%';
	$like_timeout = $wpdb->esc_like( '_transient_timeout_' . $prefix ) . '%';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Targeted cleanup of this plugin's transients during uninstall.
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
			$like_value,
			$like_timeout
		)
	);
}

/**
 * Run the current-site cleanup on every site when the plugin is removed from the network.
 *
 * @return void
 */
function landtech_extras_uninstall_network() {
	$offset = 0;

	do {
		$sites = get_sites(
			array(
				'number' => 100,
				'offset' => $offset,
				'fields' => 'ids',
			)
		);

		foreach ( $sites as $blog_id ) {
			switch_to_blog( (int) $blog_id );
			landtech_extras_uninstall_current_site();
			restore_current_blog();
		}

		$offset += 100;
	} while ( 100 === count( $sites ) );
}

if ( is_multisite() && is_network_admin() ) {
	landtech_extras_uninstall_network();
} else {
	landtech_extras_uninstall_current_site();
}
