<?php
/**
 * Flip box widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\FlipBox\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.10.0
 */
class Flip_Box extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-flip-box';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Flip Box', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-flip-box';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'flip', 'card', 'hover', '3d' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-flip-box' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-flip-box' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_front',
			array(
				'label' => __( 'Front', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'front_graphic',
			array(
				'label'   => __( 'Graphic', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => array(
					'icon'  => __( 'Icon', 'landtech-extras-for-elementor' ),
					'image' => __( 'Image', 'landtech-extras-for-elementor' ),
					'none'  => __( 'None', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'front_icon',
			array(
				'label'     => __( 'Icon', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => array( 'front_graphic' => 'icon' ),
			)
		);

		$this->add_control(
			'front_image',
			array(
				'label'     => __( 'Image', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'front_graphic' => 'image' ),
			)
		);

		$this->add_responsive_control(
			'front_icon_size',
			array(
				'label'      => __( 'Graphic Size', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-flip-box__graphic' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'front_title',
			array(
				'label'   => __( 'Title', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Our Approach', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'front_desc',
			array(
				'label'   => __( 'Description', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Hover or tap to see more.', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'front_bg',
				'selector' => '{{WRAPPER}} .ltxe-flip-box__front',
			)
		);

		$this->add_responsive_control(
			'front_padding',
			array(
				'label'      => __( 'Padding', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-flip-box__front' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'front_radius',
			array(
				'label'      => __( 'Border Radius', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-flip-box__front' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'front_align',
			array(
				'label'     => __( 'Alignment', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array( 'title' => __( 'Left', 'landtech-extras-for-elementor' ), 'icon' => 'eicon-text-align-left' ),
					'center' => array( 'title' => __( 'Center', 'landtech-extras-for-elementor' ), 'icon' => 'eicon-text-align-center' ),
					'right'  => array( 'title' => __( 'Right', 'landtech-extras-for-elementor' ), 'icon' => 'eicon-text-align-right' ),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-flip-box__front' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'front_title_typo',
				'selector' => '{{WRAPPER}} .ltxe-flip-box__front-title',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'front_desc_typo',
				'selector' => '{{WRAPPER}} .ltxe-flip-box__front-desc',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_back',
			array(
				'label' => __( 'Back', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'back_title',
			array(
				'label'   => __( 'Title', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Let’s talk', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'back_desc',
			array(
				'label'   => __( 'Description', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'A short explanation that appears on the reverse side.', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Learn more', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label' => __( 'Button Link', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'back_bg',
				'selector' => '{{WRAPPER}} .ltxe-flip-box__back',
			)
		);

		$this->add_responsive_control(
			'back_padding',
			array(
				'label'      => __( 'Padding', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-flip-box__back' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'back_radius',
			array(
				'label'      => __( 'Border Radius', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-flip-box__back' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'back_align',
			array(
				'label'     => __( 'Alignment', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array( 'title' => __( 'Left', 'landtech-extras-for-elementor' ), 'icon' => 'eicon-text-align-left' ),
					'center' => array( 'title' => __( 'Center', 'landtech-extras-for-elementor' ), 'icon' => 'eicon-text-align-center' ),
					'right'  => array( 'title' => __( 'Right', 'landtech-extras-for-elementor' ), 'icon' => 'eicon-text-align-right' ),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-flip-box__back' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'back_title_typo',
				'selector' => '{{WRAPPER}} .ltxe-flip-box__back-title',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'back_desc_typo',
				'selector' => '{{WRAPPER}} .ltxe-flip-box__back-desc',
			)
		);

		$this->add_control(
			'button_bg',
			array(
				'label'     => __( 'Button Background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e94560',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-flip-box__btn, {{WRAPPER}} a.ltxe-flip-box__btn:link, {{WRAPPER}} a.ltxe-flip-box__btn:visited' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Button Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-flip-box__btn, {{WRAPPER}} a.ltxe-flip-box__btn:link, {{WRAPPER}} a.ltxe-flip-box__btn:visited' => 'color: {{VALUE}}; -webkit-text-fill-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_box',
			array(
				'label' => __( 'Box', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'   => __( 'Flip Direction', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Horizontal', 'landtech-extras-for-elementor' ),
					'vertical'   => __( 'Vertical', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'trigger',
			array(
				'label'   => __( 'Flip Trigger', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => array(
					'hover' => __( 'Hover', 'landtech-extras-for-elementor' ),
					'click' => __( 'Click', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'box_height',
			array(
				'label'      => __( 'Height', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 160,
						'max' => 600,
					),
				),
				'default'    => array(
					'size' => 280,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-flip-box' => '--ltxe-fb-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'depth_3d',
			array(
				'label'        => __( '3D Depth', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'equal_height',
			array(
				'label'        => __( 'Equal Height in Grid', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings  = $this->get_settings_for_display();
		$trigger   = isset( $settings['trigger'] ) ? sanitize_key( (string) $settings['trigger'] ) : 'hover';
		$direction = isset( $settings['direction'] ) ? sanitize_key( (string) $settings['direction'] ) : 'horizontal';
		$front_t   = isset( $settings['front_title'] ) ? (string) $settings['front_title'] : '';
		$back_t    = isset( $settings['back_title'] ) ? (string) $settings['back_title'] : '';
		$label     = trim( $front_t . ' / ' . $back_t );
		$classes   = array(
			'ltxe-flip-box',
			'ltxe-flip-box--' . $direction,
			'ltxe-flip-box--' . $trigger,
		);
		if ( isset( $settings['depth_3d'] ) && 'yes' === $settings['depth_3d'] ) {
			$classes[] = 'ltxe-flip-box--3d';
		}
		if ( isset( $settings['equal_height'] ) && 'yes' === $settings['equal_height'] ) {
			$classes[] = 'ltxe-flip-box--equal';
		}

		$tabindex = ( 'click' === $trigger ) ? '0' : '-1';

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" tabindex="' . esc_attr( $tabindex ) . '" role="button" aria-label="' . esc_attr( $label ) . '">';
		echo '<div class="ltxe-flip-box__inner">';
		echo '<div class="ltxe-flip-box__face ltxe-flip-box__front">';
		$this->render_front( $settings );
		echo '</div>';
		echo '<div class="ltxe-flip-box__face ltxe-flip-box__back">';
		$this->render_back( $settings );
		echo '</div>';
		echo '</div></div>';
	}

	/**
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_front( $settings ) {
		$graphic = isset( $settings['front_graphic'] ) ? sanitize_key( (string) $settings['front_graphic'] ) : 'icon';
		if ( 'icon' === $graphic && ! empty( $settings['front_icon']['value'] ) ) {
			echo '<span class="ltxe-flip-box__graphic">';
			Icons_Manager::render_icon( $settings['front_icon'], array( 'aria-hidden' => 'true' ) );
			echo '</span>';
		} elseif ( 'image' === $graphic && ! empty( $settings['front_image']['url'] ) ) {
			echo '<img class="ltxe-flip-box__graphic" src="' . esc_url( $settings['front_image']['url'] ) . '" alt="" />';
		}
		if ( ! empty( $settings['front_title'] ) ) {
			echo '<h3 class="ltxe-flip-box__front-title">' . esc_html( (string) $settings['front_title'] ) . '</h3>';
		}
		if ( ! empty( $settings['front_desc'] ) ) {
			echo '<p class="ltxe-flip-box__front-desc">' . esc_html( (string) $settings['front_desc'] ) . '</p>';
		}
	}

	/**
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_back( $settings ) {
		if ( ! empty( $settings['back_title'] ) ) {
			echo '<h3 class="ltxe-flip-box__back-title">' . esc_html( (string) $settings['back_title'] ) . '</h3>';
		}
		if ( ! empty( $settings['back_desc'] ) ) {
			echo '<p class="ltxe-flip-box__back-desc">' . esc_html( (string) $settings['back_desc'] ) . '</p>';
		}
		if ( ! empty( $settings['button_text'] ) ) {
			$href = ! empty( $settings['button_link']['url'] ) ? esc_url( (string) $settings['button_link']['url'] ) : '#';
			echo '<a class="ltxe-flip-box__btn" href="' . esc_url( $href ) . '">' . esc_html( (string) $settings['button_text'] ) . '</a>';
		}
	}
}
