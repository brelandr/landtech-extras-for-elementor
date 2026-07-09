<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.

namespace LandTechExtras\Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Module
 *
 * Base 
 *
 * @since 1.6.0
 */
abstract class Module_Base {

	/**
	 * @var \ReflectionClass
	 */
	private $reflection;

	/**
	 * Module components.
	 *
	 * Holds the module components.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $components = [];

	/**
	 * @var Module_Base
	 */
	protected static $_instances = [];

	/**
	 * Abstract method for retrieveing the module name
	 *
	 * @access public
	 * @since 1.6.0
	 */
	abstract public function get_name();

	/**
	 * Return the current module class name
	 *
	 * @access public
	 * @since 1.6.0
	 *
	 * @eturn string
	 */
	public static function class_name() {
		return get_called_class();
	}

	/**
	 * @return static
	 */
	public static function instance() {
		if ( empty( static::$_instances[ static::class_name() ] ) ) {
			static::$_instances[ static::class_name() ] = new static();
		}

		return static::$_instances[ static::class_name() ];
	}

	/**
	 * Constructor
	 *
	 * Hook into Elementor to register the widgets
	 *
	 * @access public
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->reflection = new \ReflectionClass( $this );

		add_action( 'elementor/widgets/register', [ $this, 'init_widgets' ] );
	}

	/**
	 * Initializes all widget for the current module
	 *
	 * @access public
	 * @since 1.6.0
	 *
	 * @param \Elementor\Widgets_Manager|null $widgets_manager Widgets manager passed from {@see 'elementor/widgets/register'} (Elementor 3.5+).
	 * @return void
	 */
	public function init_widgets( $widgets_manager = null ) {
		$widget_manager = $widgets_manager instanceof \Elementor\Widgets_Manager
			? $widgets_manager
			: \Elementor\Plugin::instance()->widgets_manager;

		foreach ( $this->get_widgets() as $widget ) {

			$class_name = $this->resolve_widget_class_name( $widget );

			if ( null === $class_name ) {
				continue;
			}

			if ( $class_name::requires_elementor_pro() && ! landtech_extras_is_elementor_pro_active() ) {
				continue;
			}

			$widget_name = strtolower( $widget );

			// Skip widget if it's disabled in admin settings
			if ( $this->is_widget_disabled( $widget_name ) ) {
				continue;
			}

			$widget_manager->register( new $class_name() );
		}
	}

	/**
	 * Resolve and load a widget FQCN from the module widgets list.
	 *
	 * @since 3.0.1
	 *
	 * @param string $widget Widget class short name (e.g. Posts, Faq_Schema).
	 * @return string|null Fully qualified class name when loadable.
	 */
	protected function resolve_widget_class_name( $widget ) {

		$class_name = $this->reflection->getNamespaceName() . '\Widgets\\' . $widget;

		if ( class_exists( $class_name, false ) ) {
			return $class_name;
		}

		if ( defined( 'LANDTECH_EXTRAS_PATH' ) ) {
			$relative = strtolower(
				preg_replace(
					[ '/^LandTechExtras\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
					[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
					$class_name
				)
			);

			$file = LANDTECH_EXTRAS_PATH . $relative . '.php';

			if ( is_readable( $file ) ) {
				require_once $file;
			}
		}

		return class_exists( $class_name, false ) ? $class_name : null;
	}

	/**
	 * Check if widget is disabled through admin settings
	 *
	 * @access public
	 * @since 1.8.0
	 *
	 * @param  string   $widget_name Widget unique name
	 * @return bool
	 */
	public function is_widget_disabled( $widget_name ) {
		if ( ! $widget_name )
			return false;

		$option_name 	= 'enable_' . $widget_name;
		$section 		= 'landtech_extras_widgets';
		$option 		= \LandTechExtras\LandTechExtrasPlugin::instance()->settings->get_option( $option_name, $section, false );

		if ( 'off' === $option ) {
			return true;
		}

		return false;
	}

	/**
	 * Method for retrieveing this module's widgets
	 *
	 * @access public
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function get_widgets() {
		return [];
	}

	/**
	 * Is Supported
	 *
	 * Wether or not the current module is supported
	 *
	 * @access public
	 * @since 2.2.30
	 *
	 * @return bool
	 */
	public static function is_supported() {
		return ! self::requires_elementor_pro() || ( self::requires_elementor_pro() && landtech_extras_is_elementor_pro_active() );
	}

	/**
	 * Method for setting module dependancy on Elementor Pro plugin
	 *
	 * When returning false it doesn't allow the module to be registered
	 *
	 * @access public
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public static function requires_elementor_pro() {
		return false;
	}

	/**
	 * Add module component.
	 *
	 * Add new component to the current module.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param string $id       Component ID.
	 * @param mixed  $instance An instance of the component.
	 */
	public function add_component( $id, $instance ) {
		$this->components[ $id ] = $instance;
	}

	/**
	 * Get Components.
	 *
	 * Retrieve the module components.
	 *
	 * @since 2.3.0
	 * @access public
	 * @return Module[]
	 */
	public function get_components() {
		return $this->components;
	}

	/**
	 * Get Component.
	 *
	 * Retrieve the module component.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param string $id Component ID.
	 *
	 * @return mixed An instance of the component, or `false` if the component
	 *               doesn't exist.
	 */
	public function get_component( $id ) {
		if ( isset( $this->components[ $id ] ) ) {
			return $this->components[ $id ];
		}

		return false;
	}
}
