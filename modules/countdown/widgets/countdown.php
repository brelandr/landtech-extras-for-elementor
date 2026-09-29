<?php
namespace LandTechExtras\Modules\Countdown\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Free countdown timer — fixed datetime only.
 *
 * @since 2.7.0
 */
class Countdown extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-countdown';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Countdown', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-countdown';
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-countdown' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-countdown' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_countdown',
			array(
				'label' => __( 'Countdown', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'end_datetime',
			array(
				'label'          => __( 'End Date/Time', 'landtech-extras-for-elementor' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => array(
					'enableTime' => true,
				),
			)
		);

		$this->add_control(
			'show_days',
			array(
				'label'        => __( 'Days', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_hours',
			array(
				'label'        => __( 'Hours', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_minutes',
			array(
				'label'        => __( 'Minutes', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_seconds',
			array(
				'label'        => __( 'Seconds', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'label_days',
			array(
				'label'   => __( 'Days Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Days', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'label_hours',
			array(
				'label'   => __( 'Hours Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Hours', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'label_minutes',
			array(
				'label'   => __( 'Minutes Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Minutes', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'label_seconds',
			array(
				'label'   => __( 'Seconds Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Seconds', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'separator',
			array(
				'label'   => __( 'Separator', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => ':',
			)
		);

		$this->add_control(
			'zero_pad',
			array(
				'label'        => __( 'Zero Padding', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'expire_action',
			array(
				'label'   => __( 'On Expire', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'message',
				'options' => array(
					'hide'     => __( 'Hide widget', 'landtech-extras-for-elementor' ),
					'message'  => __( 'Show message', 'landtech-extras-for-elementor' ),
					'redirect' => __( 'Redirect (browser)', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'expire_message',
			array(
				'label'     => __( 'Expire Message', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => __( 'This offer has ended.', 'landtech-extras-for-elementor' ),
				'condition' => array(
					'expire_action' => 'message',
				),
			)
		);

		$this->add_control(
			'expire_url',
			array(
				'label'       => __( 'Redirect URL', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'condition'   => array(
					'expire_action' => 'redirect',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Style', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'digits',
				'selector' => '{{WRAPPER}} .ltxe-countdown__digit',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$end      = isset( $settings['end_datetime'] ) ? (string) $settings['end_datetime'] : '';
		if ( '' === $end ) {
			return;
		}

		$ts = strtotime( $end . ' UTC' );
		if ( false === $ts ) {
			return;
		}

		$config = array(
			'end'     => (int) $ts,
			'pad'     => ( isset( $settings['zero_pad'] ) && 'yes' === $settings['zero_pad'] ),
			'expire'  => isset( $settings['expire_action'] ) ? sanitize_key( $settings['expire_action'] ) : 'message',
			'message' => isset( $settings['expire_message'] ) ? (string) $settings['expire_message'] : '',
			'url'     => ! empty( $settings['expire_url']['url'] ) ? esc_url( $settings['expire_url']['url'] ) : '',
		);
		$json = wp_json_encode( $config );
		if ( ! is_string( $json ) ) {
			$json = '{}';
		}

		$units = array(
			'days'    => array( 'show_days', 'label_days' ),
			'hours'   => array( 'show_hours', 'label_hours' ),
			'minutes' => array( 'show_minutes', 'label_minutes' ),
			'seconds' => array( 'show_seconds', 'label_seconds' ),
		);
		$sep   = isset( $settings['separator'] ) ? (string) $settings['separator'] : ':';

		echo '<div class="ltxe-countdown" data-ltxe-countdown="' . esc_attr( $json ) . '" aria-live="polite" aria-atomic="true">';
		echo '<div class="ltxe-countdown__units">';
		$first = true;
		foreach ( $units as $key => $map ) {
			if ( ! isset( $settings[ $map[0] ] ) || 'yes' !== $settings[ $map[0] ] ) {
				continue;
			}
			if ( ! $first && '' !== $sep ) {
				echo '<span class="ltxe-countdown__sep">' . esc_html( $sep ) . '</span>';
			}
			$first = false;
			echo '<div class="ltxe-countdown__unit" data-unit="' . esc_attr( $key ) . '">';
			echo '<span class="ltxe-countdown__digit" data-digit="' . esc_attr( $key ) . '">0</span>';
			echo '<span class="ltxe-countdown__label">' . esc_html( (string) ( $settings[ $map[1] ] ?? '' ) ) . '</span>';
			echo '</div>';
		}
		echo '</div>';
		echo '<div class="ltxe-countdown__expired" hidden></div>';
		echo '</div>';
	}
}
