<?php
namespace ElementorExtras;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Injects plugin updates via LandTech License Server check-update JSON.
 *
 * Replaces the legacy EDD Software Licensing updater for Extras for Elementor.
 *
 * @since 2.3.0
 */
class Landtech_Plugin_Updater {

	/**
	 * Absolute path to the main plugin bootstrap file.
	 *
	 * @var string
	 */
	private $plugin_file = '';

	/**
	 * @param string $plugin_file Absolute path (__FILE__ of main plugin).
	 */
	public function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'inject_landtech_update' ), 20 );
	}

	/**
	 * @param mixed $transient Site transient.
	 * @return mixed
	 */
	public function inject_landtech_update( $transient ) {
		if ( ! is_object( $transient ) ) {
			return $transient;
		}

		if ( ! function_exists( 'elementor_extras_effective_license_is_valid' ) || ! elementor_extras_effective_license_is_valid() ) {
			return $transient;
		}

		$key = get_option( 'elementor_extras_license_key', '' );
		$key = is_string( $key ) ? trim( $key ) : '';
		if ( '' === $key ) {
			return $transient;
		}

		if ( ! class_exists( 'Landtech_Premium_Update_Offer', false ) || ! class_exists( 'ElementorExtras_Landtech_License_Client_SDK', false ) ) {
			return $transient;
		}

		$url     = elementor_extras_get_license_server_url();
		$secret  = elementor_extras_get_license_shared_secret();
		$product = elementor_extras_get_license_product_id();
		if ( '' === $url || '' === $secret || '' === $product ) {
			return $transient;
		}

		$basename = plugin_basename( $this->plugin_file );
		if ( isset( $transient->response[ $basename ] ) ) {
			return $transient;
		}

		$installed = defined( 'ELEMENTOR_EXTRAS_VERSION' ) ? ELEMENTOR_EXTRAS_VERSION : '';

		$client       = new \ElementorExtras_Landtech_License_Client_SDK( $url, $product, $secret );
		$release_stub = (object) array();

		if ( $this->beta_channel_enabled() ) {
			$probe_ver = $this->bump_patch_for_beta_probe( $installed );
			if ( '' !== $probe_ver ) {
				$o_beta = \Landtech_Premium_Update_Offer::query_landtech_update_offer( $client, $key, $probe_ver, $release_stub );
				if ( $this->inject_if_newer( $o_beta, $installed, $basename, $transient ) ) {
					return $transient;
				}
			}
		}

		$o_stable = \Landtech_Premium_Update_Offer::query_landtech_update_offer( $client, $key, $installed, $release_stub );
		$this->inject_if_newer( $o_stable, $installed, $basename, $transient );

		return $transient;
	}

	/**
	 * @return bool
	 */
	private function beta_channel_enabled() {
		elementor_extras_include( 'admin/settings-api.php' );
		if ( ! class_exists( '\ElementorExtras\Settings_API' ) ) {
			return false;
		}
		$settings = new Settings_API();

		return 'yes' === $settings->get_option( 'enable_beta', 'elementor_extras_advanced', false );
	}

	/**
	 * Nudge semver patch so beta-only server rules can reply.
	 *
	 * @param string $version Installed version string.
	 * @return string Empty if not semver x.y.z.
	 */
	private function bump_patch_for_beta_probe( $version ) {
		$version = sanitize_text_field( (string) $version );
		if ( ! preg_match( '/^(\d+)\.(\d+)\.(\d+)/', $version, $m ) ) {
			return '';
		}
		$patch = (int) $m[3];

		return $m[1] . '.' . $m[2] . '.' . (string) ( $patch + 1 );
	}

	/**
	 * @param array  $offer     Offer array.
	 * @param string $installed Normalized baseline version.
	 * @param string $basename  plugin_basename.
	 * @param object $transient Transient body.
	 * @return bool True if injected.
	 */
	private function inject_if_newer( array $offer, $installed, $basename, $transient ) {
		$latest = isset( $offer['latest_version'] ) ? sanitize_text_field( (string) $offer['latest_version'] ) : '';
		$pkg    = isset( $offer['package_url'] ) ? esc_url_raw( (string) $offer['package_url'] ) : '';
		if ( '' === $latest || '' === $pkg ) {
			return false;
		}

		$norm_latest = \Landtech_Premium_Update_Offer::normalize_semver( $latest );
		$norm_local  = \Landtech_Premium_Update_Offer::normalize_semver( $installed );

		if ( ! version_compare( $norm_latest, $norm_local, '>' ) ) {
			return false;
		}

		$headers     = @get_file_data( $this->plugin_file, array( 'Name' => 'Plugin Name' ), 'plugin' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		$plugin_name = ( is_array( $headers ) && ! empty( $headers['Name'] ) ) ? $headers['Name'] : 'Extras for Elementor';

		$item                = new \stdClass();
		$item->id            = $basename;
		$item->slug          = dirname( $basename );
		$item->plugin        = $basename;
		$item->new_version   = $latest;
		$item->package       = $pkg;
		$item->url           = esc_url_raw( elementor_extras_get_license_server_url() );
		$item->name          = $plugin_name;
		$item->icons         = array();
		$item->banners       = array();
		$item->banners_rtl   = array();
		$item->compatibility = new \stdClass();

		$transient->response[ $basename ] = $item;
		if ( ! isset( $transient->checked ) || ! is_array( $transient->checked ) ) {
			$transient->checked = array();
		}
		$transient->checked[ $basename ] = $installed;

		return true;
	}
}
