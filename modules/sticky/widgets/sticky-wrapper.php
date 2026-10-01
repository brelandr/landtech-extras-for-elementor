<?php
namespace LandTechExtras\Modules\Sticky\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CSS position:sticky wrapper around an Elementor template.
 *
 * @since 2.9.0
 */
class Sticky_Wrapper extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-sticky-wrapper';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Sticky Wrapper', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-inner-section';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'sticky', 'sidebar', 'wrapper' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-sticky-wrapper' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-sticky-wrapper' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_sticky',
			array(
				'label' => __( 'Sticky', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_responsive_control(
			'offset_top',
			array(
				'label'      => __( 'Offset from Top', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 30 ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-sticky' => '--ltxe-sticky-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'z_index',
			array(
				'label'   => __( 'Z-Index', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 100,
			)
		);

		$this->add_control(
			'sticky_on',
			array(
				'label'    => __( 'Sticky On', 'landtech-extras-for-elementor' ),
				'type'     => Controls_Manager::SELECT2,
				'multiple' => true,
				'default'  => array( 'desktop', 'tablet' ),
				'options'  => array(
					'desktop' => __( 'Desktop', 'landtech-extras-for-elementor' ),
					'tablet'  => __( 'Tablet', 'landtech-extras-for-elementor' ),
					'mobile'  => __( 'Mobile', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'template_id',
			array(
				'label'   => __( 'Inner Template', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_available_templates(),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$on       = isset( $settings['sticky_on'] ) && is_array( $settings['sticky_on'] ) ? $settings['sticky_on'] : array();
		$classes  = array( 'ltxe-sticky' );
		foreach ( array( 'desktop', 'tablet', 'mobile' ) as $bp ) {
			if ( in_array( $bp, $on, true ) ) {
				$classes[] = 'ltxe-sticky--' . $bp;
			}
		}
		$z = isset( $settings['z_index'] ) ? absint( $settings['z_index'] ) : 100;
		$top = isset( $settings['offset_top']['size'] ) ? absint( $settings['offset_top']['size'] ) : 30;
		$config = wp_json_encode(
			array(
				'top' => $top,
				'on'  => $on,
				'z'   => $z,
			)
		);
		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-ltxe-sticky="' . esc_attr( $config ) . '">';
		$template_id = isset( $settings['template_id'] ) ? absint( $settings['template_id'] ) : 0;
		if ( $template_id && class_exists( '\Elementor\Plugin' ) ) {
			echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor builder HTML; already sanitized by Elementor at save.
		} else {
			echo '<p class="ltxe-sticky__placeholder">' . esc_html__( 'Select an Elementor template to stick.', 'landtech-extras-for-elementor' ) . '</p>';
		}
		echo '</div>';
	}

	/**
	 * Published Elementor templates.
	 *
	 * @return array<int,string>
	 */
	protected function get_available_templates() {
		$posts = get_posts(
			array(
				'post_type'              => 'elementor_library',
				'posts_per_page'         => 100,
				'post_status'            => 'publish',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- editor-only template picker, capped at 100.
					array(
						'key'     => '_elementor_template_type',
						'value'   => array( 'section', 'page', 'container' ),
						'compare' => 'IN',
					),
				),
			)
		);
		$options = array();
		if ( empty( $posts ) || ! is_array( $posts ) ) {
			return $options;
		}
		foreach ( $posts as $post ) {
			if ( $post instanceof \WP_Post ) {
				$options[ (int) $post->ID ] = get_the_title( $post );
			}
		}
		return $options;
	}
}
