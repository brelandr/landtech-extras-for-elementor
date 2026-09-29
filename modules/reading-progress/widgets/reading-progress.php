<?php
namespace LandTechExtras\Modules\ReadingProgress\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fixed reading progress indicator.
 *
 * @since 2.7.0
 */
class Reading_Progress extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-reading-progress';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Reading Progress', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-slider-push';
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-reading-progress' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-reading-progress' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_bar',
			array(
				'label' => __( 'Reading Progress', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'position',
			array(
				'label'   => __( 'Position', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'    => __( 'Top of viewport', 'landtech-extras-for-elementor' ),
					'bottom' => __( 'Bottom of viewport', 'landtech-extras-for-elementor' ),
					'left'   => __( 'Left side', 'landtech-extras-for-elementor' ),
					'right'  => __( 'Right side', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'selector',
			array(
				'label'       => __( 'Track Selector', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Optional CSS selector. Leave empty to track the full page.', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_responsive_control(
			'thickness',
			array(
				'label'      => __( 'Thickness', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 4,
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-reading-progress--top .ltxe-reading-progress__track, {{WRAPPER}} .ltxe-reading-progress--bottom .ltxe-reading-progress__track' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ltxe-reading-progress--left .ltxe-reading-progress__track, {{WRAPPER}} .ltxe-reading-progress--right .ltxe-reading-progress__track' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bar_color',
			array(
				'label'     => __( 'Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-reading-progress__bar' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'track_color',
			array(
				'label'     => __( 'Track Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-reading-progress__track' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'z_index',
			array(
				'label'     => __( 'Z-index', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 9999,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-reading-progress' => 'z-index: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hide_mobile',
			array(
				'label'        => __( 'Hide on Mobile', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'smooth',
			array(
				'label'        => __( 'Smooth Transition', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$position = isset( $settings['position'] ) ? sanitize_key( $settings['position'] ) : 'top';
		if ( ! in_array( $position, array( 'top', 'bottom', 'left', 'right' ), true ) ) {
			$position = 'top';
		}
		$axis = ( 'left' === $position || 'right' === $position ) ? 'vertical' : 'horizontal';

		$config = array(
			'selector' => isset( $settings['selector'] ) ? (string) $settings['selector'] : '',
			'axis'     => $axis,
		);
		$json = wp_json_encode( $config );
		if ( ! is_string( $json ) ) {
			$json = '{}';
		}

		$classes = array(
			'ltxe-reading-progress',
			'ltxe-reading-progress--' . $position,
		);
		if ( isset( $settings['hide_mobile'] ) && 'yes' === $settings['hide_mobile'] ) {
			$classes[] = 'ltxe-reading-progress--hide-mobile';
		}
		if ( isset( $settings['smooth'] ) && 'yes' === $settings['smooth'] ) {
			$classes[] = 'ltxe-reading-progress--smooth';
		}

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-ltxe-reading="' . esc_attr( $json ) . '" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">';
		echo '<div class="ltxe-reading-progress__track"><div class="ltxe-reading-progress__bar"></div></div>';
		echo '</div>';
	}
}
