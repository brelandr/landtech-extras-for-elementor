<?php
/**
 * Opt-in CSS container-query wrapper for Elementor widgets.
 *
 * @package LandTechExtras
 * @since   2.9.1
 */

namespace LandTechExtras\Base;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wraps widget output in a named CSS container when a widget opts in.
 *
 * Opt-in paths (never forced):
 * - Override {@see get_container_query_name()} with a non-empty slug.
 * - Enable the Style-tab switcher "Respond to column width instead of screen width".
 *
 * Existing widgets stay on viewport media queries until they opt in.
 *
 * @since 2.9.1
 */
trait LTXE_Container_Query_Support {

	/**
	 * Whether this instance printed the container-query wrapper.
	 *
	 * @var bool
	 */
	private $ltxe_cq_wrapper_open = false;

	/**
	 * Container query name slug, or null when the widget does not opt in via code.
	 *
	 * @since 2.9.1
	 *
	 * @return string|null
	 */
	protected function get_container_query_name(): ?string {
		return null;
	}

	/**
	 * Whether this instance should emit the container-query wrapper.
	 *
	 * @since 2.9.1
	 *
	 * @return bool
	 */
	protected function ltxe_should_use_container_query(): bool {
		// Widgets that already wrap via ltxe_open_container_query() must not get a second wrapper.
		if ( method_exists( $this, 'ltxe_container_query_name' ) ) {
			$legacy = $this->ltxe_container_query_name();
			if ( is_string( $legacy ) && '' !== $legacy ) {
				return false;
			}
		}

		$name = $this->get_container_query_name();
		if ( is_string( $name ) && '' !== $name ) {
			return true;
		}

		$toggle = $this->get_settings_for_display( 'ltxe_respond_to_column' );

		return ( 'yes' === $toggle );
	}

	/**
	 * Resolved container-name token (without the element id suffix).
	 *
	 * @since 2.9.1
	 *
	 * @return string
	 */
	protected function ltxe_resolved_container_query_name(): string {
		$name = $this->get_container_query_name();
		if ( is_string( $name ) && '' !== $name ) {
			return sanitize_key( $name );
		}

		return 'ltxe-cq';
	}

	/**
	 * Open the container-query wrapper, then call Elementor's before_render().
	 *
	 * @since 2.9.1
	 *
	 * @return void
	 */
	public function before_render(): void {
		$this->ltxe_cq_wrapper_open = false;

		if ( $this->ltxe_should_use_container_query() ) {
			$cq = sanitize_key( $this->ltxe_resolved_container_query_name() . '-' . (string) $this->get_id() );
			if ( '' !== $cq ) {
				$this->ltxe_cq_wrapper_open = true;
				echo '<div class="ltxe-cq-wrapper" data-ltxe-cq="' . esc_attr( $cq ) . '">';
			}
		}

		parent::before_render();
	}

	/**
	 * Close the container-query wrapper after Elementor's after_render().
	 *
	 * @since 2.9.1
	 *
	 * @return void
	 */
	public function after_render(): void {
		parent::after_render();

		if ( $this->ltxe_cq_wrapper_open ) {
			echo '</div>';
			$this->ltxe_cq_wrapper_open = false;
		}
	}

	/**
	 * Style-tab opt-in: respond to column width instead of viewport width.
	 *
	 * Default is off so existing widgets keep viewport media-query CSS.
	 *
	 * @since 2.9.1
	 *
	 * @return void
	 */
	public function ltxe_register_container_query_style_controls(): void {
		if ( $this->get_controls( 'ltxe_respond_to_column' ) ) {
			return;
		}

		$this->start_controls_section(
			'section_ltxe_container_query',
			array(
				'label' => __( 'Column width (container queries)', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'ltxe_respond_to_column',
			array(
				'label'        => __( 'Respond to column width instead of screen width', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'landtech-extras-for-elementor' ),
				'label_off'    => __( 'No', 'landtech-extras-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'When enabled, this widget is wrapped in a CSS container so @container rules can follow the column width. Viewport media queries are unchanged until the widget CSS opts in.', 'landtech-extras-for-elementor' ),
			)
		);

		$this->end_controls_section();
	}
}
