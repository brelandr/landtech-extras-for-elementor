<?php
/**
 * World clock widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\WorldClock\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.1.0
 */
class World_Clock extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-world-clock';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'World Clock', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-clock-o';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'clock', 'timezone', 'time', 'world' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-world-clock' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-world-clock' );
	}

	/**
	 * IANA timezone choices.
	 *
	 * @return array
	 */
	private function tz_options() {
		return array(
			'America/New_York'    => 'New York',
			'America/Los_Angeles' => 'Los Angeles',
			'America/Chicago'     => 'Chicago',
			'Europe/London'       => 'London',
			'Europe/Paris'        => 'Paris',
			'Europe/Berlin'       => 'Berlin',
			'Asia/Dubai'          => 'Dubai',
			'Asia/Tokyo'          => 'Tokyo',
			'Asia/Singapore'      => 'Singapore',
			'Australia/Sydney'    => 'Sydney',
			'UTC'                 => 'UTC',
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$rep = new Repeater();
		$rep->add_control(
			'tz',
			array(
				'label'   => __( 'Timezone', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'Europe/London',
				'options' => $this->tz_options(),
			)
		);
		$rep->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'London',
			)
		);
		$rep->add_control(
			'show_date',
			array(
				'label'        => __( 'Show date', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$rep->add_control(
			'show_seconds',
			array(
				'label'        => __( 'Show seconds', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->start_controls_section(
			'section_clocks',
			array(
				'label' => __( 'Clocks', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'clocks',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'default'     => array(
					array( 'tz' => 'America/New_York', 'label' => 'New York' ),
					array( 'tz' => 'Europe/London', 'label' => 'London' ),
					array( 'tz' => 'Asia/Dubai', 'label' => 'Dubai' ),
					array( 'tz' => 'Asia/Tokyo', 'label' => 'Tokyo' ),
				),
				'title_field' => '{{{ label }}}',
			)
		);
		$this->add_control(
			'mode',
			array(
				'label'   => __( 'Display', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => array(
					'digital' => __( 'Digital', 'landtech-extras-for-elementor' ),
					'analog'  => __( 'Analog', 'landtech-extras-for-elementor' ),
					'both'    => __( 'Both', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->add_control(
			'hour12',
			array(
				'label'   => __( 'Time format', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '24',
				'options' => array(
					'12' => __( '12-hour', 'landtech-extras-for-elementor' ),
					'24' => __( '24-hour', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => array(
					'row'   => __( 'Horizontal strip', 'landtech-extras-for-elementor' ),
					'stack' => __( 'Vertical stack', 'landtech-extras-for-elementor' ),
					'grid'  => __( 'Grid', 'landtech-extras-for-elementor' ),
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
		$mode   = isset( $s['mode'] ) ? sanitize_key( (string) $s['mode'] ) : 'both';
		$layout = isset( $s['layout'] ) ? sanitize_key( (string) $s['layout'] ) : 'grid';
		$h12    = isset( $s['hour12'] ) && '12' === (string) $s['hour12'];
		$clocks = ( ! empty( $s['clocks'] ) && is_array( $s['clocks'] ) ) ? $s['clocks'] : array();
		echo '<div class="ltxe-wclock ltxe-wclock--' . esc_attr( $layout ) . '" data-ltxe-wclock="' . esc_attr( wp_json_encode( array( 'mode' => $mode, 'hour12' => $h12 ) ) ) . '">';
		foreach ( $clocks as $clock ) {
			if ( ! is_array( $clock ) ) {
				continue;
			}
			$tz    = isset( $clock['tz'] ) ? (string) $clock['tz'] : 'UTC';
			$label = isset( $clock['label'] ) ? (string) $clock['label'] : $tz;
			echo '<div class="ltxe-wclock__item" data-tz="' . esc_attr( $tz ) . '" data-seconds="' . ( ! empty( $clock['show_seconds'] ) && 'yes' === $clock['show_seconds'] ? '1' : '0' ) . '" data-date="' . ( ! empty( $clock['show_date'] ) && 'yes' === $clock['show_date'] ? '1' : '0' ) . '">';
			echo '<p class="ltxe-wclock__city">' . esc_html( $label ) . '</p>';
			if ( 'digital' !== $mode ) {
				echo '<div class="ltxe-wclock__analog" aria-hidden="true"><span class="ltxe-wclock__hand ltxe-wclock__hand--h"></span><span class="ltxe-wclock__hand ltxe-wclock__hand--m"></span><span class="ltxe-wclock__hand ltxe-wclock__hand--s"></span></div>';
			}
			if ( 'analog' !== $mode ) {
				echo '<p class="ltxe-wclock__digital">--:--</p>';
			}
			echo '<p class="ltxe-wclock__date"></p>';
			echo '</div>';
		}
		echo '</div>';
	}
}
