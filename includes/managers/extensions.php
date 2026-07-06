<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Extensions_Manager {

	const DISPLAY_CONDITIONS 	= 'display-conditions';
	const PORTFOLIO_PARALLAX 	= 'portfolio-parallax';
	const STICKY_ELEMENTS 		= 'sticky-elements';
	const PARALLAX_ELELENTS		= 'parallax-elements';
	const PARALLAX_BACKGROUND 	= 'parallax-background';
	const TOOLTIP 				= 'tooltip';

	private $_extensions = null;

	public $available_extensions = [
		self::DISPLAY_CONDITIONS,
		self::PORTFOLIO_PARALLAX,
		self::STICKY_ELEMENTS,
		self::PARALLAX_ELELENTS,
		self::PARALLAX_BACKGROUND,
		self::TOOLTIP,
	];

	/**
	 * Loops though available extensions and registers them
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 * @return void
	 */
	public function register_extensions() {

		$this->_extensions = [];

		/**
		 * Extra extension IDs registered by the Premium add-on (files may live under `LANDTECH_EXTRAS_PREMIUM_PATH`).
		 *
		 * @since 2.2.74
		 *
		 * @param string[] $ids Hyphenated extension slugs.
		 */
		$available_extensions = apply_filters( 'landtech_extras/available_extensions', $this->available_extensions );

		if ( ! is_array( $available_extensions ) ) {
			$available_extensions = $this->available_extensions;
		}

		foreach ( $available_extensions as $index => $extension_id ) {
			$extension_name     = str_replace( '-', '_', $extension_id );
			$basename           = str_replace( '_', '-', $extension_id );
			$ext_path           = LANDTECH_EXTRAS_PATH . "extensions/{$basename}.php";

			if ( ! is_readable( $ext_path ) && defined( 'LANDTECH_EXTRAS_PREMIUM_PATH' ) ) {
				$premium_try = trailingslashit( wp_normalize_path( (string) constant( 'LANDTECH_EXTRAS_PREMIUM_PATH' ) ) )
					. "extensions/{$basename}.php";
				if ( is_readable( $premium_try ) ) {
					$ext_path = $premium_try;
				}
			}

			if ( ! is_readable( $ext_path ) ) {
				continue;
			}

			require_once $ext_path;

			$class_name = str_replace( '-', '_', $extension_id );

			$class_name = 'LandTechExtras\Extensions\Extension_' . ucwords( $class_name );

			if ( ! $this->is_available( $extension_name ) )
				unset( $this->available_extensions[ $index ] );

			// Skip extension if it's disabled in admin settings or is dependant on non-exisiting Elementor Pro plugin
			if ( $this->is_disabled( $extension_name ) ) {
				continue;
			}

			$this->register_extension( $extension_id, new $class_name() );
		}

		do_action( 'landtech_extras/extensions/extensions_registered', $this );
	}

	/**
	 * Check if extension is disabled through admin settings
	 *
	 * @since 1.8.0
	 *
	 * @access public
	 * @return bool
	 */
	public function is_disabled( $extension_name ) {
		if ( ! $extension_name )
			return false;

		$option_name 	= 'enable_' . $extension_name;
		$section 		= 'landtech_extras_extensions';
		$option 		= \LandTechExtras\LandTechExtrasPlugin::instance()->settings->get_option( $option_name, $section, false );

		return ( 'off' === $option ) || ( ! $option && $this->is_default_disabled( $extension_name ) );
	}

	/**
	 * Check if extension is disabled by default
	 *
	 * @since 2.0.0
	 *
	 * @access public
	 * @return bool
	 */
	public function is_default_disabled( $extension_name ) {
		if ( ! $extension_name )
			return false;

		$class_name = str_replace( '-', '_', $extension_name );
		$class_name = 'LandTechExtras\Extensions\Extension_' . ucwords( $class_name );

		if ( $class_name::is_default_disabled() )
			return true;

		return false;
	}

	/**
	 * Check if extension is available at all
	 *
	 * @since 1.8.0
	 *
	 * @access public
	 * @return bool
	 */
	public function is_available( $extension_name ) {
		if ( ! $extension_name )
			return false;

		$class_name = str_replace( '-', '_', $extension_name );
		$class_name = 'LandTechExtras\Extensions\Extension_' . ucwords( $class_name );

		if ( $class_name::requires_elementor_pro() && ! landtech_extras_is_elementor_pro_active() )
			return false;

		return true;
	}

	/**
	 * @since 0.1.0
	 *
	 * @param $extension_id
	 * @param Extension_Base $extension_instance
	 */
	public function register_extension( $extension_id, Base\Extension_Base $extension_instance ) {
		$this->_extensions[ $extension_id ] = $extension_instance;
	}

	/**
	 * @since 0.1.0
	 *
	 * @param $extension_id
	 * @return bool
	 */
	public function unregister_extension( $extension_id ) {
		if ( ! isset( $this->_extensions[ $extension_id ] ) ) {
			return false;
		}

		unset( $this->_extensions[ $extension_id ] );

		return true;
	}

	/**
	 * @since 0.1.0
	 *
	 * @return Extension_Base[]
	 */
	public function get_extensions() {
		if ( null === $this->_extensions ) {
			$this->register_extensions();
		}

		return $this->_extensions;
	}

	/**
	 * @since 0.1.0
	 *
	 * @param $extension_id
	 * @return bool|\LandTechExtras\Extension_Base
	 */
	public function get_extension( $extension_id ) {
		$extensions = $this->get_extensions();

		return isset( $extensions[ $extension_id ] ) ? $extensions[ $extension_id ] : false;
	}

	private function require_files() {
		require_once LANDTECH_EXTRAS_PATH . 'base/extension.php';
	}

	public function __construct() {
		$this->require_files();
		$this->register_extensions();
	}
}
