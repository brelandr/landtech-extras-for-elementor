<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Modules_Manager {

	/**
	 * Registered modules.
	 *
	 * Holds the list of all the registered modules.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @var array
	 */
	public $_modules = [];

	/**
	 * Core module slugs bundled with LandTech Extras (before addons append via filter).
	 *
	 * @since 2.3.0
	 *
	 * @return string[]
	 */
	private function get_builtin_module_slugs() {
		return [
			'posts',
			'gallery',
			'scroll-indicator',
			'hotspots',
			'switcher',
			'devices',
			'calendar',
			'navigation',
			'search',
			'toggle',
			'map',
			'unfold',
			'media-player',
			'circle-progress',
			'image',
			'popup',
			'buttons',
			'svg',
			'heading',
			'table',
			'breadcrumbs',
			'schema',
			'lottie',
			'templates-control',
			'query-control',
			'display-conditions',
			'tabs',
			'table-of-contents',
		];
	}

	/**
	 * Get modules names.
	 *
	 * Retrieve the modules names.
	 *
	 * @since 2.2.30
	 * @access public
	 *
	 * @return string[] Modules names (slugs).
	 */
	public function get_modules_names() {
		$core = $this->get_builtin_module_slugs();

		/**
		 * Extend registered LandTech modules (Premium-only slugs appended here).
		 *
		 * Each slug must match `^[a-z0-9_-]+$`. Unknown slugs resolve via {@see landtech_extras/module_class}.
		 *
		 * @since 2.3.0
		 *
		 * @param string[] $core Built-in slug list.
		 */
		$list = \apply_filters( 'landtech_extras/module_names', $core );

		if ( ! is_array( $list ) ) {
			return $core;
		}

		$out = [];
		foreach ( $list as $slug ) {
			if ( ! is_scalar( $slug ) ) {
				continue;
			}
			$slug = strtolower( \sanitize_text_field( (string) $slug ) );
			if ( '' === $slug || ! preg_match( '/^[a-z0-9_-]+$/', $slug ) ) {
				continue;
			}
			$out[] = $slug;
		}

		return array_values( array_unique( $out ) );
	}

	/**
	 * @since 0.1.0
	 */
	public function register_modules() {

		foreach ( $this->get_modules_names() as $module_name ) {

			$studly_token = str_replace( '-', ' ', $module_name );

			$studly_token = str_replace( ' ', '', ucwords( $studly_token ) );

			$default_class = __NAMESPACE__ . '\\Modules\\' . $studly_token . '\Module';

			/**
			 * Override module class FQCN for a slug (premium modules may use `\LandTechExtras\Premium\...`).
			 *
			 * @since 2.3.0
			 *
			 * @param string $default_class Resolved core namespace class.
			 * @param string $module_name    Module slug.
			 */
			$class_name = \apply_filters( 'landtech_extras/module_class', $default_class, $module_name );

			if ( ! is_string( $class_name ) || '' === trim( $class_name ) ) {
				continue;
			}

			$class_name = trim( $class_name );
			$class_name = '\\' . ltrim( str_replace( '/', '\\', $class_name ), '\\' );

			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			if ( ! is_subclass_of( $class_name, Module_Base::class ) ) {
				continue;
			}

			if ( ! $class_name::is_supported() ) {
				continue;
			}

			$this->_modules[ $module_name ] = $class_name::instance();
		}
	}

	/**
	 * @param string $module_name
	 *
	 * @return Module_Base|Module_Base[]
	 */
	public function get_modules( $module_name = null ) {
		if ( $module_name ) {
			if ( isset( $this->_modules[ $module_name ] ) ) {
				return $this->_modules[ $module_name ];
			}
			return null;
		}

		return $this->_modules;
	}

	private function require_files() {
		require_once LANDTECH_EXTRAS_PATH . 'base/module.php';
	}

	public function __construct() {
		$this->require_files();
		$this->register_modules();
	}
}
