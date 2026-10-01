<?php
/**
 * Free-tier one page / dot navigation.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\OnePageNav\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.10.0
 */
class Dot_Nav extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-one-page-nav';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'One Page Nav', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'dot', 'nav', 'one page', 'scroll' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-one-page-nav' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-one-page-nav' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_nav',
			array(
				'label' => __( 'Navigation', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'target_selector',
			array(
				'label'   => __( 'Target Sections', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '.elementor-section',
			)
		);

		$this->add_control(
			'position',
			array(
				'label'   => __( 'Position', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'right',
				'options' => array(
					'left'  => array(
						'title' => __( 'Left', 'landtech-extras-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'landtech-extras-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
			)
		);

		$this->add_responsive_control(
			'dot_size',
			array(
				'label'      => __( 'Dot Size', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 28,
					),
				),
				'default'    => array(
					'size' => 12,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-opn__dot' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'active_color',
			array(
				'label'     => __( 'Active Dot Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e94560',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-opn__btn.is-active .ltxe-opn__dot' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'inactive_color',
			array(
				'label'     => __( 'Inactive Dot Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a1a2e',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-opn__dot' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tooltip',
			array(
				'label'        => __( 'Tooltip on Hover', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'smooth',
			array(
				'label'        => __( 'Smooth Scroll', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'   => __( 'Offset (px)', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'max'     => 200,
			)
		);

		$this->add_control(
			'premium_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<p>' . esc_html__( 'Premium unlocks scroll analytics, custom dot shapes, and keyboard navigation mode.', 'landtech-extras-for-elementor' ) . '</p>',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$pos      = isset( $settings['position'] ) ? sanitize_key( (string) $settings['position'] ) : 'right';
		if ( 'left' !== $pos ) {
			$pos = 'right';
		}
		$cfg = array(
			'selector' => isset( $settings['target_selector'] ) ? sanitize_text_field( (string) $settings['target_selector'] ) : '.elementor-section',
			'tooltip'  => ( isset( $settings['tooltip'] ) && 'yes' === $settings['tooltip'] ),
			'smooth'   => ( isset( $settings['smooth'] ) && 'yes' === $settings['smooth'] ),
			'offset'   => isset( $settings['offset'] ) ? absint( $settings['offset'] ) : 0,
		);

		echo '<nav class="ltxe-opn ltxe-opn--' . esc_attr( $pos ) . '" data-ltxe-opn="' . esc_attr( wp_json_encode( $cfg ) ) . '" aria-label="' . esc_attr__( 'On this page', 'landtech-extras-for-elementor' ) . '">';
		echo '<ul class="ltxe-opn__list"></ul>';
		echo '</nav>';
	}
}
