<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.

namespace LandTechExtras\Base;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once __DIR__ . '/ltxe-container-query-support.php';

abstract class Extras_Widget extends Widget_Base {

	use LTXE_Container_Query_Support;

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
	 * Default is false (dynamic). Override in widgets whose rendered HTML is fully determined
	 * by saved settings and does not vary by user, request, or runtime context — e.g. Table,
	 * Inline SVG (data-url shell), Text Divider. Progressive-enhancement JS may still run after cache.
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
	 * Heading-level control. The default matches the tag the widget used before the control existed.
	 *
	 * @param string $control_id Control id.
	 * @param string $default    h1–h6, div, span, or p.
	 * @param string $label      Control label.
	 * @return void
	 */
	protected function ltxe_add_heading_tag_control( $control_id, $default = 'h3', $label = '' ) {
		if ( '' === $label ) {
			$label = __( 'HTML Tag', 'landtech-extras-for-elementor' );
		}

		$this->add_control(
			$control_id,
			array(
				'label'   => $label,
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => $default,
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
			)
		);
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

	/**
	 * Opt-in container query name for U16+ widgets (empty = viewport CSS only).
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	protected function ltxe_container_query_name() {
		return '';
	}

	/**
	 * Open a container-query wrapper when {@see ltxe_container_query_name()} is set.
	 *
	 * @since 2.9.0
	 *
	 * @return void
	 */
	protected function ltxe_open_container_query() {
		$name = sanitize_key( (string) $this->ltxe_container_query_name() );
		if ( '' === $name ) {
			return;
		}
		$cq = $name . '-' . sanitize_key( (string) $this->get_id() );
		echo '<div class="ltxe-cq-wrapper" data-ltxe-cq="' . esc_attr( $cq ) . '">';
	}

	/**
	 * Close the container-query wrapper.
	 *
	 * @since 2.9.0
	 *
	 * @return void
	 */
	protected function ltxe_close_container_query() {
		if ( '' === sanitize_key( (string) $this->ltxe_container_query_name() ) ) {
			return;
		}
		echo '</div>';
	}

	/**
	 * Inject the Style-tab container-query toggle on Extras widgets only.
	 *
	 * Default remains off. Existing widget CSS is not converted to @container.
	 *
	 * @since 2.9.1
	 *
	 * @param \Elementor\Controls_Stack $element    Element stack.
	 * @param string                    $section_id Section ID.
	 * @param array                     $args       Section arguments.
	 * @return void
	 */
	public static function ltxe_maybe_register_container_query_controls( $element, $section_id, $args ): void {
		unset( $args );

		if ( ! $element instanceof self ) {
			return;
		}

		if ( $element->get_controls( 'section_ltxe_container_query' ) ) {
			return;
		}

		$controls = $element->get_controls();
		if ( ! isset( $controls[ $section_id ]['tab'] ) ) {
			return;
		}

		if ( Controls_Manager::TAB_STYLE !== $controls[ $section_id ]['tab'] ) {
			return;
		}

		$element->ltxe_register_container_query_style_controls();
	}

	/**
	 * One-click button shape. Current leaves the widget as it is.
	 *
	 * @param string $id Control prefix, unique inside the widget.
	 * @return void
	 */
	protected function landtech_extras_register_button_look_control( $id ) {
		$id = sanitize_key( (string) $id );
		if ( '' === $id ) {
			return;
		}

		$this->add_control(
			$id . '_look',
			array(
				'label'        => __( 'Look', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => '',
				'options'      => array(
					''        => __( 'Current', 'landtech-extras-for-elementor' ),
					'pill'    => __( 'Pill', 'landtech-extras-for-elementor' ),
					'soft'    => __( 'Soft', 'landtech-extras-for-elementor' ),
					'outline' => __( 'Outline', 'landtech-extras-for-elementor' ),
				),
				'prefix_class' => 'ltxe-btn-look-',
			)
		);
	}

	/**
	 * Shared Style tab for a widget button.
	 *
	 * Color, background, padding, radius, border, and shadow. Nothing is filled in,
	 * so an existing button keeps its current look until someone sets these.
	 *
	 * @param array $args id, label, selector, active, active_suffix, bar.
	 * @return void
	 */
	protected function landtech_extras_register_button_style_controls( $args ) {
		$id = isset( $args['id'] ) ? sanitize_key( (string) $args['id'] ) : '';
		if ( '' === $id ) {
			return;
		}

		$selectors = array();
		if ( isset( $args['selector'] ) ) {
			foreach ( (array) $args['selector'] as $selector ) {
				$selector = trim( (string) $selector );
				if ( '' !== $selector ) {
					$selectors[] = $selector;
				}
			}
		}
		if ( empty( $selectors ) ) {
			return;
		}

		$label         = isset( $args['label'] ) ? (string) $args['label'] : __( 'Button', 'landtech-extras-for-elementor' );
		$active        = ! empty( $args['active'] );
		$active_suffix = isset( $args['active_suffix'] ) ? (string) $args['active_suffix'] : '.is-active';
		$bar           = isset( $args['bar'] ) ? trim( (string) $args['bar'] ) : '';
		$target        = $this->ltxe_button_style_selector( $selectors, '' );

		$this->start_controls_section(
			$id . '_style',
			array(
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->landtech_extras_register_button_look_control( $id );

		if ( '' !== $bar ) {
			$bar_target = $this->ltxe_button_style_selector( array( $bar ), '' );

			$this->add_control(
				$id . '_bar_heading',
				array(
					'label' => __( 'Bar', 'landtech-extras-for-elementor' ),
					'type'  => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				$id . '_bar_background',
				array(
					'label'     => __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						$bar_target => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				$id . '_bar_padding',
				array(
					'label'      => __( 'Padding', 'landtech-extras-for-elementor' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', 'em', '%' ),
					'selectors'  => array(
						$bar_target => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				$id . '_bar_radius',
				array(
					'label'      => __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						$bar_target => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => $id . '_bar_shadow',
					'selector' => $bar_target,
					'exclude'  => array( 'box_shadow_position' ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control schema.
				)
			);

			$this->add_control(
				$id . '_items_heading',
				array(
					'label'     => __( 'Buttons', 'landtech-extras-for-elementor' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => $id . '_typo',
				'selector' => $target,
			)
		);

		$this->add_responsive_control(
			$id . '_padding',
			array(
				'label'      => __( 'Padding', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					$target => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( $id . '_tabs' );

		$this->start_controls_tab(
			$id . '_tab_default',
			array(
				'label' => __( 'Default', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			$id . '_color',
			array(
				'label'     => __( 'Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$target => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$id . '_background',
			array(
				'label'     => __( 'Background Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$target => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => $id . '_shadow',
				'selector' => $target,
				'exclude'  => array( 'box_shadow_position' ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control schema.
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			$id . '_tab_hover',
			array(
				'label' => __( 'Hover', 'landtech-extras-for-elementor' ),
			)
		);

		$hover = $this->ltxe_button_style_selector( $selectors, ':hover' );

		$this->add_control(
			$id . '_color_hover',
			array(
				'label'     => __( 'Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$hover => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$id . '_background_hover',
			array(
				'label'     => __( 'Background Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$hover => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => $id . '_shadow_hover',
				'selector' => $hover,
				'exclude'  => array( 'box_shadow_position' ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control schema.
			)
		);

		$this->end_controls_tab();

		if ( $active ) {
			$this->start_controls_tab(
				$id . '_tab_active',
				array(
					'label' => __( 'Active', 'landtech-extras-for-elementor' ),
				)
			);

			$active_target = $this->ltxe_button_style_selector( $selectors, $active_suffix );

			$this->add_control(
				$id . '_color_active',
				array(
					'label'     => __( 'Color', 'landtech-extras-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						$active_target => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				$id . '_background_active',
				array(
					'label'     => __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array(
						$active_target => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => $id . '_shadow_active',
					'selector' => $active_target,
					'exclude'  => array( 'box_shadow_position' ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control schema.
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => $id . '_border',
				'selector'  => $target,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			$id . '_radius',
			array(
				'label'      => __( 'Border Radius', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$target => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build a wrapper-scoped selector list.
	 *
	 * @param array  $selectors Button selectors.
	 * @param string $suffix    State suffix such as :hover or .is-active.
	 * @return string
	 */
	private function ltxe_button_style_selector( $selectors, $suffix ) {
		$parts = array();
		foreach ( $selectors as $selector ) {
			if ( false === strpos( $selector, '{{WRAPPER}}' ) ) {
				$selector = '{{WRAPPER}} ' . $selector;
			}
			$parts[] = $selector . $suffix;
		}
		return implode( ', ', $parts );
	}
}

add_action( 'elementor/element/after_section_end', array( Extras_Widget::class, 'ltxe_maybe_register_container_query_controls' ), 10, 3 );
