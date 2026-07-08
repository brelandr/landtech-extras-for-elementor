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
		return array( 'landtech-extras-lottie-player' );
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
					'url'      => __( 'External URL', 'landtech-extras-for-elementor' ),
					'upload'   => __( 'Media library JSON', 'landtech-extras-for-elementor' ),
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
				'label'     => __( 'Lottie JSON', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'media_type'=> 'application/json',
				'condition' => array(
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
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$src      = '';

		if ( 'upload' === ( $settings['source'] ?? 'url' ) && ! empty( $settings['animation_file']['url'] ) ) {
			$src = esc_url_raw( $settings['animation_file']['url'] );
		} elseif ( ! empty( $settings['animation_url']['url'] ) ) {
			$src = esc_url_raw( $settings['animation_url']['url'] );
		}

		if ( '' === $src ) {
			return;
		}

		$loop     = ( 'yes' === ( $settings['loop'] ?? '' ) ) ? 'true' : 'false';
		$autoplay = ( 'yes' === ( $settings['autoplay'] ?? '' ) ) ? 'true' : 'false';

		echo '<lottie-player';
		echo ' src="' . esc_url( $src ) . '"';
		echo ' background="transparent"';
		echo ' speed="1"';
		echo ' style="width:100%;max-width:480px;"';
		echo ' loop="' . esc_attr( $loop ) . '"';
		echo ' autoplay="' . esc_attr( $autoplay ) . '"';
		echo '></lottie-player>';
	}
}
