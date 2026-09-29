<?php
namespace LandTechExtras\Modules\BackToTop\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fixed back-to-top button with optional scroll progress ring.
 *
 * @since 2.9.0
 */
class Back_To_Top extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-back-to-top';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Back to Top', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-v-align-top';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'back', 'top', 'scroll' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-back-to-top' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-back-to-top' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_btt',
			array(
				'label' => __( 'Button', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label'   => __( 'Icon', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-chevron-up',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'sr_label',
			array(
				'label'   => __( 'Screen Reader Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Back to top', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'position',
			array(
				'label'   => __( 'Position', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'left'   => __( 'Left', 'landtech-extras-for-elementor' ),
					'center' => __( 'Center', 'landtech-extras-for-elementor' ),
					'right'  => __( 'Right', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'offset_x',
			array(
				'label'      => __( 'Horizontal Offset', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 24 ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-btt' => '--ltxe-btt-x: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'offset_y',
			array(
				'label'      => __( 'Bottom Offset', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 24 ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-btt' => '--ltxe-btt-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'show_after',
			array(
				'label'   => __( 'Show After (px scrolled)', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 300,
				'min'     => 0,
			)
		);

		$this->add_control(
			'scroll_behavior',
			array(
				'label'   => __( 'Scroll Behavior', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'smooth',
				'options' => array(
					'smooth'  => __( 'Smooth', 'landtech-extras-for-elementor' ),
					'instant' => __( 'Instant', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'progress_ring',
			array(
				'label'        => __( 'Show Scroll Progress Ring', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->add_responsive_control(
			'button_size',
			array(
				'label'      => __( 'Button Size', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 44 ),
				'range'      => array(
					'px' => array(
						'min' => 44,
						'max' => 72,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-btt' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Button Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-btt' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-btt' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ltxe-btt .ltxe-btt__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ltxe-btt .ltxe-btt__icon svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .ltxe-btt .e-font-icon-svg' => 'fill: {{VALUE}};',
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
		$pos      = isset( $settings['position'] ) ? sanitize_key( (string) $settings['position'] ) : 'right';
		if ( ! in_array( $pos, array( 'left', 'center', 'right' ), true ) ) {
			$pos = 'right';
		}
		$after = isset( $settings['show_after'] ) ? absint( $settings['show_after'] ) : 300;
		$beh   = ( isset( $settings['scroll_behavior'] ) && 'instant' === $settings['scroll_behavior'] ) ? 'instant' : 'smooth';
		$ring  = ( isset( $settings['progress_ring'] ) && 'yes' === $settings['progress_ring'] );
		$label = isset( $settings['sr_label'] ) ? (string) $settings['sr_label'] : __( 'Back to top', 'landtech-extras-for-elementor' );

		$config = wp_json_encode(
			array(
				'after'    => $after,
				'behavior' => $beh,
			)
		);

		echo '<button type="button" class="ltxe-btt ltxe-btt--' . esc_attr( $pos ) . '" data-ltxe-btt="' . esc_attr( $config ) . '" aria-label="' . esc_attr( $label ) . '">';
		if ( $ring ) {
			echo '<svg class="ltxe-btt__ring" viewBox="0 0 36 36" aria-hidden="true"><circle class="ltxe-btt__track" cx="18" cy="18" r="16"></circle><circle class="ltxe-btt__progress" cx="18" cy="18" r="16"></circle></svg>';
		}
		echo '<span class="ltxe-btt__icon" aria-hidden="true">';
		if ( ! empty( $settings['selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) );
		} else {
			echo '↑';
		}
		echo '</span>';
		echo '</button>';
	}
}
