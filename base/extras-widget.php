<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.

namespace LandTechExtras\Base;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Extras_Widget extends Widget_Base {

	/**
	 * Wether or not we are in edit mode
	 *
	 * Used for the add_helper_render_attribute method which needs to
	 * add attributes only in edit mode.
	 *
	 * @access public
	 *
	 * @var bool
	 */
	public $_is_edit_mode = false;

	/**
	 * Loop Dynamic Settings
	 *
	 * Used to keep dynamic settings for posts inside
	 * a custom loop used in the widget
	 *
	 * @access private
	 * @since  2.2.2
	 *
	 * @var null|array
	 */
	private $ltxe_loop_dynamic_settings = [];

	/**
	 * Get Categories
	 * 
	 * Get the categories in which this widget can be found
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public function get_categories() {
		return [ 'landtech-extras' ];
	}

	/**
	 * Treat all Extras widgets as dynamic output for Element Caching (Elementor 3.22+).
	 *
	 * Returning true prevents serving cached HTML that could be wrong for WP_Query loops,
	 * user/state-dependent behavior, or settings evaluated at render time. Opt in per widget
	 * with {@see static::ltxe_allows_element_html_cache()} when the output is fully static.
	 *
	 * @since 2.2.53
	 */
	protected function is_dynamic_content(): bool {
		return ! static::ltxe_allows_element_html_cache();
	}

	/**
	 * Whether this widget may use Elementor element HTML caching (static output only).
	 *
	 * @since 2.2.53
	 */
	protected static function ltxe_allows_element_html_cache(): bool {
		return false;
	}

	/**
	 * Support Elementor Optimized Markup when the experiment is active (single wrapper; no `.elementor-widget-container`).
	 *
	 * @return bool
	 */
	public function has_widget_inner_wrapper(): bool {
		$plugin = \Elementor\Plugin::instance();

		if ( ! isset( $plugin->experiments ) || ! is_object( $plugin->experiments ) ) {
			return true;
		}

		if ( method_exists( $plugin->experiments, 'is_feature_active' ) && $plugin->experiments->is_feature_active( 'e_optimized_markup' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Widget base constructor.
	 *
	 * Initializing the widget base class.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array       $data Widget data. Default is an empty array.
	 * @param array|null  $args Optional. Widget default arguments. Default is null.
	 */
	public function __construct( $data = [], $args = null ) {

		parent::__construct( $data, $args );

		// Set edit mode
		$this->_is_edit_mode = \Elementor\Plugin::instance()->editor->is_edit_mode();
	}

	/**
	 * Allowlisted heading / title tag for dynamic opening and closing HTML tags.
	 *
	 * @param string $tag Requested tag.
	 * @return string
	 */
	public function ltxe_sanitize_heading_tag( $tag ) {
		$tag = strtolower( (string) $tag );
		$allowed = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' );

		return in_array( $tag, $allowed, true ) ? $tag : 'h3';
	}

	/**
	 * Safe settings read for asset registration (preview may run before settings hydrate).
	 *
	 * @since 2.3.3
	 *
	 * @param string $key Control id.
	 * @return mixed|null
	 */
	protected function ltxe_try_get_settings( $key ) {
		if ( function_exists( 'landtech_extras_posts_extra_widget_try_get_settings' ) ) {
			return landtech_extras_posts_extra_widget_try_get_settings( $this, $key );
		}

		return null;
	}

	/**
	 * Safe display settings read for asset registration (preview may run before settings hydrate).
	 *
	 * @since 2.3.3
	 *
	 * @param string|null $key Control id, or null for the full settings array.
	 * @return array|mixed Full settings array, single value, or empty array / null on failure.
	 */
	protected function ltxe_try_get_settings_for_display( $key = null ) {
		if ( function_exists( 'landtech_extras_posts_extra_widget_try_get_settings_for_display' ) ) {
			return landtech_extras_posts_extra_widget_try_get_settings_for_display( $this, $key );
		}

		return null === $key ? array() : null;
	}

	/**
	 * Method for adding editor helper attributes
	 *
	 * Adds attributes that enable a display of a label for a specific html element
	 *
	 * @access public
	 * @since 1.6.0
	 * @return void
	 */
	public function add_helper_render_attribute( $key, $name = '' ) {

		if ( ! $this->_is_edit_mode )
			return;

		$this->add_render_attribute( $key, [
			'data-ee-helper' 	=> $name,
			'class'				=> 'ee-editor-helper',
		] );
	}

	/**
	 * Method for adding a placeholder for the widget in the preview area
	 *
	 * @access public
	 * @since 2.0.0
	 * @return void
	 */
	public function render_placeholder( $args ) {

		if ( ! $this->_is_edit_mode )
			return;

		$defaults = [
			'title_tag' => 'h4',
			'title' => $this->get_title(),
			'body' 	=> __( 'This is a placeholder for this widget and will not shown on the page.', 'landtech-extras-for-elementor' ),
		];

		$args = wp_parse_args( $args, $defaults );

		$title_tag = $this->ltxe_sanitize_heading_tag( $args['title_tag'] );

		$this->add_render_attribute([
			'ee-placeholder' => [
				'class' => 'ee-editor-placeholder',
			],
			'ee-placeholder-title' => [
				'class' => 'ee-editor-placeholder__title',
			],
			'ee-placeholder-body' => [
				'class' => 'ee-editor-placeholder__body',
			],
		]);

		?><div <?php $this->print_render_attribute_string( 'ee-placeholder' ); ?>>
			<<?php echo esc_html( $title_tag ); ?> <?php $this->print_render_attribute_string( 'ee-placeholder-title' ); ?>>
				<?php echo wp_kses_post( $args['title'] ); ?>
			</<?php echo esc_html( $title_tag ); ?>>
			<div <?php $this->print_render_attribute_string( 'ee-placeholder-body' ); ?>><?php echo wp_kses_post( $args['body'] ); ?></div>
		</div><?php
	}

	/**
	 * Method for setting widget dependancy on Elementor Pro plugin
	 *
	 * When returning true it doesn't allow the widget to be registered
	 *
	 * @access public
	 * @since 1.6.0
	 * @return bool
	 */
	public static function requires_elementor_pro() {
		return false;
	}

	/**
	 * Get skin setting
	 *
	 * Retrieves the current skin setting
	 *
	 * @access protected
	 * @since 2.1.0
	 * @return mixed
	 */
	protected function get_skin_setting( $setting_key ) {
		if ( ! $setting_key )
			return false;

		return $this->get_current_skin()->get_instance_value( $setting_key );
	}

	/**
	 * Set Loop Dynamic Settings
	 *
	 * @access protected
	 * @since 2.2.2
	 * @return void
	 *
	 * @param WP_Query 	  $query      The query to generate the dynamic settings for
	 */
	protected function set_settings_for_loop( $query ) {

		global $wp_query;

		// Temporarily force a query for the template and set it as the currenty query
		$old_query 	= $wp_query;
		$wp_query 	= $query;

		while ( $query->have_posts() ) {

			$query->the_post();

			$this->set_settings_for_post( get_the_ID() );
		}

		// Revert to the initial query
		$wp_query = $old_query;

		wp_reset_postdata();
	}

	/**
	 * Set Post Dynamic Settings
	 *
	 * @access protected
	 * @since 2.2.33
	 * @return void
	 *
	 * @param int $post_id The post to generate the dynamic settings for
	 */
	protected function set_settings_for_post( $post_id ) {
		if ( ! $post_id ) {
			return;
		}

		$settings 		= $this->get_settings_for_display();
		$all_settings 	= $this->get_settings();
		$controls 		= $this->get_controls();
		
		$this->ltxe_loop_dynamic_settings[ $post_id ] = [];

		foreach ( $controls as $control ) {
			$control_name = $control['name'];
			$control_obj = \Elementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

			if ( empty( $control['dynamic'] ) ) {
				continue;
			}

			$dynamic_settings = array_merge( $control_obj->get_settings( 'dynamic' ), $control['dynamic'] );
			$parsed_value = '';

			if ( ! isset( $all_settings[ '__dynamic__' ][ $control_name ] ) || empty( $control['dynamic']['loop'] ) ) {
				$parsed_value = $all_settings[ $control_name ];
			} else {
				$parsed_value = $control_obj->parse_tags( $settings[ '__dynamic__' ][ $control_name ], $dynamic_settings );
			}

			$this->ltxe_loop_dynamic_settings[ $post_id ][ $control_name ] = $parsed_value;
		}
	}

	/**
	 * Get Loop Dynamic Settings
	 *
	 * Fetches the dynamic settings for all looped posts
	 * or for a single post
	 *
	 * @access protected
	 * @since 2.2.2
	 * @return array
	 *
	 * @param int 	  $post_id      The ID of the post for which to fetch the settings
	 */
	protected function get_settings_for_loop_display( $post_id = false ) {

		if ( $post_id ) {
			if ( array_key_exists( $post_id, $this->ltxe_loop_dynamic_settings ) ) {
				return $this->ltxe_loop_dynamic_settings[ $post_id ];
			}
		}

		return $this->ltxe_loop_dynamic_settings;
	}

	/**
	 * Get ID for Loop
	 * 
	 * Returns a unique ID based on the global post id and widget id
	 *
	 * @since  2.2.33
	 * @return tring
	 */
	public function get_id_for_loop() {
		global $post;

		if ( ! $post ) {
			return $this->get_id();
		}

		return implode( '_', [ $this->get_id(), $post->ID ] );
	}

	/**
	 * Print HTML attributes from Elementor's render attribute API.
	 *
	 * Must remain public: {@see \Elementor\Controls_Stack::print_render_attribute_string()}.
	 *
	 * @param string $element Render key.
	 * @return void
	 */
	public function print_render_attribute_string( $element ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Widget_Base::get_render_attribute_string() returns escaped attribute HTML.
		echo $this->get_render_attribute_string( $element );
	}

	/**
	 * Allowlisted tag for gallery-style media wrappers (figure vs anchor).
	 *
	 * @param string $tag Tag name.
	 * @return string
	 */
	protected function get_gallery_media_tag_name( $tag ) {
		return in_array( $tag, array( 'figure', 'a' ), true ) ? $tag : 'figure';
	}
}
