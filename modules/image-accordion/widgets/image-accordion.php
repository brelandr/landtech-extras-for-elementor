<?php
/**
 * Image accordion widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\ImageAccordion\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.10.0
 */
class Image_Accordion extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-image-accordion';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Image Accordion', 'landtech-extras-for-elementor' );
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
	public function get_keywords() {
		return array( 'accordion', 'image', 'expand', 'panels' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-image-accordion' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-image-accordion' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_panels',
			array(
				'label' => __( 'Panels', 'landtech-extras-for-elementor' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label' => __( 'Background Image', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$repeater->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Panel', 'landtech-extras-for-elementor' ),
			)
		);
		$repeater->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => '',
			)
		);
		$repeater->add_control(
			'button_text',
			array(
				'label'   => __( 'Button Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Learn More', 'landtech-extras-for-elementor' ),
			)
		);
		$repeater->add_control(
			'button_link',
			array(
				'label' => __( 'Button Link', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'panels',
			array(
				'label'       => __( 'Panels', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'title' => __( 'Architecture', 'landtech-extras-for-elementor' ) ),
					array( 'title' => __( 'Interior Design', 'landtech-extras-for-elementor' ) ),
					array( 'title' => __( 'Landscape', 'landtech-extras-for-elementor' ) ),
					array( 'title' => __( 'Commercial', 'landtech-extras-for-elementor' ) ),
					array( 'title' => __( 'Residential', 'landtech-extras-for-elementor' ) ),
				),
				'title_field' => '{{{ title }}}',
			)
		);

		$this->add_control(
			'trigger',
			array(
				'label'   => __( 'Trigger', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => array(
					'hover' => __( 'Hover', 'landtech-extras-for-elementor' ),
					'click' => __( 'Click', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'default_open',
			array(
				'label'   => __( 'Default Open', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => -1,
				'max'     => 20,
			)
		);

		$this->add_responsive_control(
			'collapsed_width',
			array(
				'label'      => __( 'Collapsed Width', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 200,
					),
				),
				'default'    => array(
					'size' => 80,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-ia' => '--ltxe-ia-collapsed: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'expanded_width',
			array(
				'label'      => __( 'Expanded Width', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 900,
					),
				),
				'default'    => array(
					'size' => 500,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-ia' => '--ltxe-ia-expanded: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'duration',
			array(
				'label'     => __( 'Transition (ms)', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 400,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-ia' => '--ltxe-ia-duration: {{VALUE}}ms;',
				),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a1a2e',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-ia__overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_opacity',
			array(
				'label'     => __( 'Overlay Opacity', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.05,
					),
				),
				'default'   => array(
					'size' => 0.45,
				),
				'selectors' => array(
					'{{WRAPPER}} .ltxe-ia__overlay' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'content_pos',
			array(
				'label'   => __( 'Content Position', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom-left',
				'options' => array(
					'bottom-left'  => __( 'Bottom Left', 'landtech-extras-for-elementor' ),
					'center'       => __( 'Center', 'landtech-extras-for-elementor' ),
					'bottom-right' => __( 'Bottom Right', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'      => __( 'Height', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 700,
					),
				),
				'default'    => array(
					'size' => 420,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-ia' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'radius',
			array(
				'label'      => __( 'Border Radius', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-ia' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'show_button',
			array(
				'label'        => __( 'Show Button', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_typo',
			array(
				'label' => __( 'Typography', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typo',
				'selector' => '{{WRAPPER}} .ltxe-ia__title',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'desc_typo',
				'selector' => '{{WRAPPER}} .ltxe-ia__desc',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$trigger  = isset( $settings['trigger'] ) ? sanitize_key( (string) $settings['trigger'] ) : 'hover';
		$pos      = isset( $settings['content_pos'] ) ? sanitize_key( (string) $settings['content_pos'] ) : 'bottom-left';
		$open     = isset( $settings['default_open'] ) ? (int) $settings['default_open'] : 0;
		$show_btn = ( isset( $settings['show_button'] ) && 'yes' === $settings['show_button'] );
		$panels   = ! empty( $settings['panels'] ) && is_array( $settings['panels'] ) ? $settings['panels'] : array();
		$cfg      = array(
			'trigger' => $trigger,
			'open'    => $open,
		);

		echo '<div class="ltxe-ia ltxe-ia--' . esc_attr( $trigger ) . ' ltxe-ia--' . esc_attr( $pos ) . '" data-ltxe-ia="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
		foreach ( $panels as $index => $panel ) {
			$is_open = ( (int) $index === $open );
			$title   = isset( $panel['title'] ) ? (string) $panel['title'] : '';
			$bg      = ! empty( $panel['image']['url'] ) ? esc_url( (string) $panel['image']['url'] ) : '';
			$tab     = ( 'click' === $trigger ) ? '0' : '-1';
			echo '<div class="ltxe-ia__panel' . ( $is_open ? ' is-open' : '' ) . '" tabindex="' . esc_attr( $tab ) . '" role="button" aria-expanded="' . ( $is_open ? 'true' : 'false' ) . '" aria-label="' . esc_attr( $title ) . '"' . ( $bg ? ' style="background-image:url(' . esc_url( $bg ) . ')"' : '' ) . '>';
			echo '<span class="ltxe-ia__overlay"></span>';
			echo '<div class="ltxe-ia__content">';
			if ( '' !== $title ) {
				echo '<h3 class="ltxe-ia__title">' . esc_html( $title ) . '</h3>';
			}
			if ( ! empty( $panel['description'] ) ) {
				echo '<p class="ltxe-ia__desc">' . esc_html( (string) $panel['description'] ) . '</p>';
			}
			if ( $show_btn && ! empty( $panel['button_text'] ) ) {
				$href = ! empty( $panel['button_link']['url'] ) ? (string) $panel['button_link']['url'] : '#';
				echo '<a class="ltxe-ia__btn" href="' . esc_url( $href ) . '">' . esc_html( (string) $panel['button_text'] ) . '</a>';
			}
			echo '</div></div>';
		}
		echo '</div>';
	}
}
