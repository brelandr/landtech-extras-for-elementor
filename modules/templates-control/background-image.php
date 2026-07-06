<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\TemplatesControl;

// LandTech Extras for Elementor Classes
use LandTechExtras\Utils;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Provides temporary support for background images inside
 * our templates used in loops
 * @since 2.2.4
 */
class BackgroundImage {

	/**
	 * Accumulated inline CSS keyed by style block id.
	 *
	 * @since 2.2.71
	 * @var array<string, string>
	 */
	private static $inline_css_blocks = array();

	/**
	 * Whether the wp_enqueue hook was registered.
	 *
	 * @since 2.2.71
	 * @var bool
	 */
	private static $enqueue_hook_registered = false;

	/**
	 * Template post ID for loop context.
	 *
	 * @since 2.2.4
	 * @access private
	 *
	 * @var int
	 */
	private $template_id;

	/**
	 * The current template ID
	 *
	 * @since 2.2.4
	 * @access private
	 *
	 * @var int
	 */
	private $elements = [
		'section' => [
			'background_image' => [
				'allow_static' => false,
				'condition' => 'background_background',
				'selector' 	=> '
					{{WRAPPER}}:not(.elementor-motion-effects-element-type-background),
					{{WRAPPER}} > .elementor-motion-effects-container > .elementor-motion-effects-layer',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
			'background_hover_image' => [
				'allow_static'	=> true,
				'condition' => 'background_hover_background',
				'selector' 	=> '{{WRAPPER}}:hover, {{WRAPPER}}:hover > .elementor-motion-effects-container > .elementor-motion-effects-layer',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
		],
		'column' => [
			'background_image' => [
				'allow_static' => false,
				'condition' => 'background_background',
				'selector' 	=> '
					{{WRAPPER}}:not(.elementor-motion-effects-element-type-background) > .elementor-element-populated,
					{{WRAPPER}} > .elementor-column-wrap > .elementor-motion-effects-container > .elementor-motion-effects-layer',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
			'background_hover_image' => [
				'allow_static'	=> true,
				'condition' => 'background_hover_background',
				'selector' 	=> '{{WRAPPER}}:hover > .elementor-element-populated',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
		],
		'widget' => [
			'_background_image' => [
				'allow_static' => false,
				'condition' => '_background_background',
				'selector' 	=> '{{WRAPPER}}, {{WRAPPER}} > .elementor-widget-container',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
			'_background_hover_image' => [
				'allow_static'	=> true,
				'condition' => '_background_hover_background',
				'selector' 	=> '{{WRAPPER}}:hover, {{WRAPPER}}:hover > .elementor-widget-container',
				'styles' 	=> [
					[
						'property' 	=> 'background-image',
						'value' 	=> 'url({{URL}});',
					]
				],
			],
		],
	];

	/**
	 * Set Template Id
	 *
	 * Sets the current template id
	 *
	 * @param 	int 		$template_id 	The template post ID
	 * @since 	2.2.4
	 * @return 	void
	 */
	public function set_template_id( $template_id ) {
		if ( ! $template_id )
			return;

		$this->template_id = $template_id;
	}

	/**
	 * Get Element Selector
	 *
	 * Retrieves the element selector for printing styles
	 *
	 * @param 	int 		$element_id 	The element ID
	 * @param 	int 		$post_id 		The current post ID in the loop
	 * @since 	2.2.4
	 * @return 	void
	 */
	protected function get_element_selector( $element_id, $post_id ) {
		$unique_id 	= $element_id . '-' . $post_id;

		return '.elementor-' . $this->template_id . ' .elementor-element.elementor-element-'. $element_id .'.elementor-ee-element-'. $unique_id;
	}

	/**
	 * Add Actions
	 *
	 * @param 	Element_Base 		$element 	The Elementor element object
	 * @since 	2.2.4
	 * @return 	void
	 */
	public function add_actions( $element ) {
		$this->parse_controls( $element );
	}

	/**
	 * Add Inline CSS
	 *
	 * Contains logic for determining wether the element needs
	 * template specific inline css addded before it's rendered
	 *
	 * @param 	Element_Base 		$element 	The Elementor element object
	 * @since 	2.2.4
	 * @return 	void
	 */
	protected function parse_controls( $element ) {

		$_settings 	= $element->get_settings();
		$settings 	= $element->get_settings_for_display();
		$has_styles = false;

		foreach ( $this->elements as $element_type => $styles ) {
			foreach ( $styles as $control_name => $control_settings ) {
				if ( 'classic' !== $element->get_settings( $control_settings['condition'] ) ) {
					continue;
				}

				$control = $element->get_controls( $control_name );

				if ( ! $control ) {
					continue;
				}

				$control_name = $control['name'];

				if ( empty( $control['type'] ) ) {
					continue;
				}

				$control_obj = \Elementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

				if ( empty( $control['dynamic'] ) ) {
					continue;
				}

				$dynamic_settings = array_merge( $control_obj->get_settings( 'dynamic' ), $control['dynamic'] );

				if ( ! isset( $settings[ '__dynamic__' ][ $control_name ] ) ) {
					if ( true === $control_settings['allow_static'] && array_key_exists( $control_name, $settings ) ) {
						$parsed_value = $settings[ $control_name ];
					} else {
						continue;
					}
				} else {
					$parsed_value = $control_obj->parse_tags( $settings[ '__dynamic__' ][ $control_name ], $dynamic_settings );
				}

				if ( $parsed_value && array_key_exists( 'url' , $parsed_value ) ) { // Keep empty urls values to remove inherited style
					$this->parse_control_styles( $element, $control, $parsed_value['url'] );
					$has_styles = true;
				} else {
					continue;
				}
			}
		}

		if ( $has_styles ) {
			$this->print_styles( $element );
		}
	}

	/**
	 * Passed through the control names and replaces
	 * css selectors and values
	 *
	 * @param 	Element_Base 		$element 	The Elementor element object
	 * @param 	Controls_Stack 		$control 	The control object
	 * @param 	string 				$value 		The value for the CSS style
	 * @since 	2.2.4
	 * @return 	void
	 */
	private function parse_control_styles( $element, $control, $value ) {
		$selector 			= $this->get_element_selector( $element->get_id(), get_the_ID() );
		$parsable_selector 	= $this->elements[ $element->get_type() ][ $control['name'] ]['selector'];
		$parsable_styles 	= $this->elements[ $element->get_type() ][ $control['name'] ]['styles'];

		// Replace selector
		$this->elements[ $element->get_type() ][ $control['name'] ]['selector'] = str_replace( '{{WRAPPER}}', $selector, $parsable_selector );
		
		// Replace url
		foreach ( $parsable_styles as $index => $style ) {

			$this->elements[ $element->get_type() ][ $control['name'] ]['styles'][ $index ]['value'] = $value ? str_replace( '{{URL}}', $value, $parsable_styles[ $index ]['value'] ) : 'none'; // Use none to remove inherited background images
		}

		$this->elements[ $element->get_type() ][ $control['name'] ]['print'] = true;
	}

	/**
	 * Print Background Image Styles
	 *
	 * @param 	string 		$selector 	The base CSS selector
	 * @param 	string 		$url 	The base CSS selector
	 * @since 	2.2.4
	 * @return 	void
	 */
	private function print_styles( $element ) {
		$style_id = 'ee-template-loop-css-' . $this->template_id . '-' . $element->get_id();
		$css      = '';

		foreach ( $this->elements[ $element->get_type() ] as $control_name => $control_settings ) {
			if ( array_key_exists( 'print', $control_settings ) && true === $control_settings['print'] ) {
				$css .= wp_strip_all_tags( (string) $control_settings['selector'] ) . '{';
				foreach ( $control_settings['styles'] as $style ) {
					$css .= wp_strip_all_tags( (string) $style['property'] ) . ':' . $style['value'];
				}
				$css .= '}';
			}
		}

		if ( '' === $css ) {
			return;
		}

		self::$inline_css_blocks[ $style_id ] = $css;
		self::register_enqueue_hook();
	}

	/**
	 * Attach accumulated CSS via wp_add_inline_style().
	 *
	 * @since 2.2.71
	 * @return void
	 */
	public static function flush_inline_styles() {
		if ( empty( self::$inline_css_blocks ) ) {
			return;
		}

		if ( ! wp_style_is( 'landtech-extras-frontend', 'enqueued' ) && ! wp_style_is( 'landtech-extras-frontend', 'registered' ) ) {
			return;
		}

		if ( ! wp_style_is( 'landtech-extras-frontend', 'enqueued' ) ) {
			wp_enqueue_style( 'landtech-extras-frontend' );
		}

		wp_add_inline_style( 'landtech-extras-frontend', implode( '', self::$inline_css_blocks ) );
		self::$inline_css_blocks = array();
	}

	/**
	 * Register a late enqueue hook so loop CSS is printed with registered styles.
	 *
	 * @since 2.2.71
	 * @return void
	 */
	private static function register_enqueue_hook() {
		if ( self::$enqueue_hook_registered ) {
			return;
		}

		self::$enqueue_hook_registered = true;

		add_action(
			'elementor/frontend/after_enqueue_styles',
			array( __CLASS__, 'flush_inline_styles' ),
			99
		);
	}
}
