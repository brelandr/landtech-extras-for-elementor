<?php
namespace LandTechExtras\Modules\Lottie\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lottie animation widget.
 *
 * @since 2.2.102
 */
class Lottie extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ee-lottie';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Lottie', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'nicon nicon-animation';
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		$settings = $this->ltxe_try_get_settings_for_display();
		if ( ! is_array( $settings ) || empty( $settings ) ) {
			return array( 'landtech-extras-lottie-web', 'landtech-extras-lottie', 'landtech-extras-lottie-player' );
		}
		if ( $this->uses_lottie_web( $settings ) ) {
			return array( 'landtech-extras-lottie-web', 'landtech-extras-lottie' );
		}
		return array( 'landtech-extras-lottie-player' );
	}

	/**
	 * Whether advanced playback needs the lottie-web API.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return bool
	 */
	public function uses_lottie_web( $settings ) {
		if ( ! is_array( $settings ) ) {
			return false;
		}
		$trigger   = isset( $settings['trigger'] ) ? (string) $settings['trigger'] : 'autoplay';
		$direction = isset( $settings['direction'] ) ? (string) $settings['direction'] : 'forward';
		$speed     = isset( $settings['speed']['size'] ) ? (float) $settings['speed']['size'] : 1.0;
		$segment   = isset( $settings['use_segment'] ) && 'yes' === $settings['use_segment'];
		if ( 'autoplay' !== $trigger ) {
			return true;
		}
		if ( $segment ) {
			return true;
		}
		if ( 'forward' !== $direction ) {
			return true;
		}
		if ( abs( $speed - 1.0 ) > 0.001 ) {
			return true;
		}
		return false;
	}

	/**
	 * Resolve the animation JSON URL from settings.
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string
	 */
	public function resolve_src( $settings ) {
		if ( ! is_array( $settings ) ) {
			return '';
		}
		if ( 'upload' === ( $settings['source'] ?? 'url' ) && ! empty( $settings['animation_file']['url'] ) ) {
			return esc_url_raw( $settings['animation_file']['url'] );
		}
		if ( ! empty( $settings['animation_url']['url'] ) ) {
			return esc_url_raw( $settings['animation_url']['url'] );
		}
		return '';
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_lottie',
			array(
				'label' => __( 'Animation', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'url',
				'options' => array(
					'url'    => __( 'External URL', 'landtech-extras-for-elementor' ),
					'upload' => __( 'Media library JSON', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'animation_url',
			array(
				'label'       => __( 'Animation URL', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
				'condition'   => array(
					'source' => 'url',
				),
			)
		);

		$this->add_control(
			'animation_file',
			array(
				'label'      => __( 'Lottie JSON', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::MEDIA,
				'media_type' => 'application/json',
				'condition'  => array(
					'source' => 'upload',
				),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'trigger' => 'autoplay',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_playback',
			array(
				'label' => __( 'Playback', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'trigger',
			array(
				'label'   => __( 'Trigger', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'autoplay',
				'options' => array(
					'autoplay'     => __( 'Auto Play', 'landtech-extras-for-elementor' ),
					'scroll'       => __( 'Scroll Into View', 'landtech-extras-for-elementor' ),
					'scroll_scrub' => __( 'Scroll Scrub (synced to scroll position)', 'landtech-extras-for-elementor' ),
					'hover'        => __( 'Hover', 'landtech-extras-for-elementor' ),
					'click'        => __( 'Click', 'landtech-extras-for-elementor' ),
					'none'         => __( 'None (manual via JS)', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'     => __( 'Speed', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 1,
				),
				'range'     => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 3,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'trigger!' => 'scroll_scrub',
				),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'   => __( 'Direction', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'forward',
				'options' => array(
					'forward'   => __( 'Forward', 'landtech-extras-for-elementor' ),
					'reverse'   => __( 'Reverse', 'landtech-extras-for-elementor' ),
					'alternate' => __( 'Alternate (ping-pong)', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'use_segment',
			array(
				'label'        => __( 'Play Segment Only', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'segment_start',
			array(
				'label'     => __( 'Start Frame', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'condition' => array(
					'use_segment' => 'yes',
				),
			)
		);

		$this->add_control(
			'segment_end',
			array(
				'label'     => __( 'End Frame', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 60,
				'condition' => array(
					'use_segment' => 'yes',
				),
			)
		);

		$this->add_control(
			'scrub_start',
			array(
				'label'     => __( 'Start Scrub When', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'enter_bottom',
				'options'   => array(
					'enter_bottom' => __( 'Element enters bottom of viewport', 'landtech-extras-for-elementor' ),
					'enter_center' => __( 'Element reaches center of viewport', 'landtech-extras-for-elementor' ),
					'enter_top'    => __( 'Element reaches top of viewport', 'landtech-extras-for-elementor' ),
				),
				'condition' => array(
					'trigger' => 'scroll_scrub',
				),
			)
		);

		$this->add_control(
			'scrub_end',
			array(
				'label'     => __( 'End Scrub When', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'exit_top',
				'options'   => array(
					'exit_top'    => __( 'Element exits top of viewport', 'landtech-extras-for-elementor' ),
					'exit_center' => __( 'Element center exits viewport', 'landtech-extras-for-elementor' ),
				),
				'condition' => array(
					'trigger' => 'scroll_scrub',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$src      = $this->resolve_src( $settings );

		if ( '' === $src ) {
			return;
		}

		if ( ! $this->uses_lottie_web( $settings ) ) {
			$loop     = ( 'yes' === ( $settings['loop'] ?? '' ) ) ? 'true' : 'false';
			$autoplay = ( 'yes' === ( $settings['autoplay'] ?? '' ) ) ? 'true' : 'false';

			echo '<lottie-player';
			echo ' src="' . esc_url( $src ) . '"';
			echo ' background="transparent"';
			echo ' speed="1"';
			echo ' loop="' . esc_attr( $loop ) . '"';
			echo ' autoplay="' . esc_attr( $autoplay ) . '"';
			echo '></lottie-player>';
			return;
		}

		$config = array(
			'src'           => $src,
			'trigger'       => isset( $settings['trigger'] ) ? sanitize_key( $settings['trigger'] ) : 'autoplay',
			'loop'          => ( 'yes' === ( $settings['loop'] ?? '' ) ),
			'autoplay'      => ( 'yes' === ( $settings['autoplay'] ?? '' ) ),
			'speed'         => isset( $settings['speed']['size'] ) ? (float) $settings['speed']['size'] : 1.0,
			'direction'     => isset( $settings['direction'] ) ? sanitize_key( $settings['direction'] ) : 'forward',
			'use_segment'   => ( 'yes' === ( $settings['use_segment'] ?? '' ) ),
			'segment_start' => isset( $settings['segment_start'] ) ? (int) $settings['segment_start'] : 0,
			'segment_end'   => isset( $settings['segment_end'] ) ? (int) $settings['segment_end'] : 60,
			'scrub_start'   => isset( $settings['scrub_start'] ) ? sanitize_key( $settings['scrub_start'] ) : 'enter_bottom',
			'scrub_end'     => isset( $settings['scrub_end'] ) ? sanitize_key( $settings['scrub_end'] ) : 'exit_top',
		);

		$json = wp_json_encode( $config );
		if ( ! is_string( $json ) ) {
			$json = '{}';
		}

		$role = ( 'click' === $config['trigger'] ) ? 'button' : 'img';

		echo '<div class="ee-lottie" data-ltxe-lottie="' . esc_attr( $json ) . '"';
		if ( 'click' === $config['trigger'] ) {
			echo ' role="button" tabindex="0"';
		} else {
			echo ' role="' . esc_attr( $role ) . '"';
		}
		echo '><div class="ee-lottie__canvas"></div></div>';
	}
}
