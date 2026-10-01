<?php
/**
 * Interactive card widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\InteractiveCards\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.1.0
 */
class Interactive_Card extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-interactive-card';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Interactive Card', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-info-box';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'card', 'tilt', '3d', 'hover' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-interactive-card' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-interactive-card' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_card',
			array(
				'label' => __( 'Card', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'effect',
			array(
				'label'   => __( 'Effect', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'tilt',
				'options' => array(
					'tilt'    => __( '3D Tilt', 'landtech-extras-for-elementor' ),
					'float'   => __( 'Floating Shadow', 'landtech-extras-for-elementor' ),
					'none'    => __( 'None', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->add_control(
			'tilt_max',
			array(
				'label'   => __( 'Tilt max angle', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 15,
			)
		);
		$this->add_control(
			'perspective',
			array(
				'label'   => __( 'Perspective (px)', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1000,
			)
		);
		$this->add_control(
			'glare',
			array(
				'label'        => __( 'Glare', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Hover this card', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$this->add_control(
			'subtitle',
			array(
				'label'   => __( 'Subtitle', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '3D tilt', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'The card follows your cursor. Reduced motion keeps it still.', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'selected_icon',
			array(
				'label' => __( 'Icon', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::ICONS,
			)
		);
		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Learn more', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'button_url',
			array(
				'label' => __( 'Button URL', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::URL,
			)
		);
		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Transition (ms)', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 180,
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_ic',
			array(
				'label' => __( 'Style', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-icard' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'card_pad',
			array(
				'label'      => __( 'Padding', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-icard' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$s      = $this->get_settings_for_display();
		$effect = isset( $s['effect'] ) ? sanitize_key( (string) $s['effect'] ) : 'tilt';
		$max    = isset( $s['tilt_max'] ) ? (float) $s['tilt_max'] : 15;
		$persp  = isset( $s['perspective'] ) ? (int) $s['perspective'] : 1000;
		$speed  = isset( $s['speed'] ) ? (int) $s['speed'] : 180;
		$url    = ( isset( $s['button_url']['url'] ) && is_string( $s['button_url']['url'] ) ) ? $s['button_url']['url'] : '#';
		$cfg    = array(
			'effect'      => $effect,
			'max'         => $max,
			'perspective' => $persp,
			'speed'       => $speed,
			'glare'       => isset( $s['glare'] ) && 'yes' === $s['glare'],
		);
		echo '<div class="ltxe-icard ltxe-icard--' . esc_attr( $effect ) . '"' . ( 'tilt' === $effect ? ' data-ltxe-tilt="1"' : '' ) . ' data-ltxe-icard="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
		if ( ! empty( $s['selected_icon']['value'] ) ) {
			echo '<span class="ltxe-icard__icon">';
			Icons_Manager::render_icon( $s['selected_icon'], array( 'aria-hidden' => 'true' ) );
			echo '</span>';
		}
		echo '<p class="ltxe-icard__sub">' . esc_html( isset( $s['subtitle'] ) ? (string) $s['subtitle'] : '' ) . '</p>';
		echo '<h3 class="ltxe-icard__title">' . esc_html( isset( $s['title'] ) ? (string) $s['title'] : '' ) . '</h3>';
		echo '<p class="ltxe-icard__desc">' . esc_html( isset( $s['description'] ) ? (string) $s['description'] : '' ) . '</p>';
		echo '<a class="ltxe-icard__btn" href="' . esc_url( $url ) . '">' . esc_html( isset( $s['button_text'] ) ? (string) $s['button_text'] : '' ) . '</a>';
		if ( isset( $s['glare'] ) && 'yes' === $s['glare'] ) {
			echo '<span class="ltxe-icard__glare" aria-hidden="true"></span>';
		}
		echo '</div>';
	}
}
