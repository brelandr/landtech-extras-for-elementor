<?php
/**
 * Image scroller widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\ImageScroller\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.1.0
 */
class Image_Scroller extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-image-scroller';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Image Scroller', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-scroll';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'image', 'scroll', 'screenshot', 'parallax' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-image-scroller' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-image-scroller' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_scroller',
			array(
				'label' => __( 'Scroller', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'image',
			array(
				'label' => __( 'Image', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'trigger',
			array(
				'label'   => __( 'Scroll trigger', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => array(
					'scroll' => __( 'On page scroll', 'landtech-extras-for-elementor' ),
					'hover'  => __( 'On hover', 'landtech-extras-for-elementor' ),
					'click'  => __( 'On click', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->add_control(
			'direction',
			array(
				'label'   => __( 'Direction', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'vertical',
				'options' => array(
					'vertical'   => __( 'Vertical', 'landtech-extras-for-elementor' ),
					'horizontal' => __( 'Horizontal', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->add_responsive_control(
			'box_height',
			array(
				'label'      => __( 'Container height', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'default'    => array(
					'size' => 320,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-iscroll' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Speed', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'step'    => 0.1,
			)
		);
		$this->add_control(
			'overlay',
			array(
				'label'   => __( 'Overlay', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'     => __( 'None', 'landtech-extras-for-elementor' ),
					'gradient' => __( 'Gradient from bottom', 'landtech-extras-for-elementor' ),
					'color'    => __( 'Color', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$s     = $this->get_settings_for_display();
		$url   = ( isset( $s['image']['url'] ) && is_string( $s['image']['url'] ) ) ? $s['image']['url'] : '';
		$trig  = isset( $s['trigger'] ) ? sanitize_key( (string) $s['trigger'] ) : 'hover';
		$dir   = isset( $s['direction'] ) ? sanitize_key( (string) $s['direction'] ) : 'vertical';
		$speed = isset( $s['speed'] ) ? (float) $s['speed'] : 1;
		$over  = isset( $s['overlay'] ) ? sanitize_key( (string) $s['overlay'] ) : 'none';
		if ( '' === $url ) {
			$url = 'https://extrasforelementor.com/wp-content/uploads/2024/01/placeholder.png';
		}
		echo '<div class="ltxe-iscroll ltxe-iscroll--' . esc_attr( $dir ) . '" data-scroll-trigger="' . esc_attr( $trig ) . '" data-ltxe-iscroll="' . esc_attr( wp_json_encode( array( 'trigger' => $trig, 'direction' => $dir, 'speed' => $speed ) ) ) . '">';
		echo '<img class="ltxe-iscroll__img" src="' . esc_url( $url ) . '" alt="" />';
		if ( 'none' !== $over ) {
			echo '<span class="ltxe-iscroll__overlay ltxe-iscroll__overlay--' . esc_attr( $over ) . '" aria-hidden="true"></span>';
		}
		echo '</div>';
	}
}
