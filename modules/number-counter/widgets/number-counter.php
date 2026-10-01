<?php
/**
 * Animated number counter widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\NumberCounter\Widgets;

use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\NumberCounter\Number_Counter_Format;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __DIR__ ) . '/class-number-counter-format.php';

/**
 * @since 2.10.0
 */
class Number_Counter extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-number-counter';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Number Counter', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-counter';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'counter', 'number', 'stats', 'count' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-number-counter' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-number-counter' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_counter',
			array(
				'label' => __( 'Counter', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'number',
			array(
				'label'   => __( 'Number', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
				'step'    => 0.01,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'prefix',
			array(
				'label'   => __( 'Prefix', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'suffix',
			array(
				'label'   => __( 'Suffix', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '+',
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'duration',
			array(
				'label'   => __( 'Duration (ms)', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2000,
				'min'     => 0,
				'step'    => 50,
			)
		);

		$this->add_control(
			'easing',
			array(
				'label'   => __( 'Easing', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ease-out',
				'options' => array(
					'linear'      => __( 'Linear', 'landtech-extras-for-elementor' ),
					'ease-out'    => __( 'Ease out', 'landtech-extras-for-elementor' ),
					'ease-in-out' => __( 'Ease in out', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'delimiter',
			array(
				'label'   => __( 'Thousands Separator', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'comma',
				'options' => array(
					'none'   => __( 'None', 'landtech-extras-for-elementor' ),
					'comma'  => __( 'Comma', 'landtech-extras-for-elementor' ),
					'period' => __( 'Period', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'decimals',
			array(
				'label'   => __( 'Decimal Places', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'min'     => 0,
				'max'     => 4,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Happy Clients', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
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
			'trigger_once',
			array(
				'label'        => __( 'Trigger Once', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'landtech-extras-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'landtech-extras-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'landtech-extras-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-number-counter' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_number',
			array(
				'label' => __( 'Number', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'number_color',
			array(
				'label'     => __( 'Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-number-counter__value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_typo',
				'selector' => '{{WRAPPER}} .ltxe-number-counter__value',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			array(
				'label' => __( 'Title', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-number-counter__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typo',
				'selector' => '{{WRAPPER}} .ltxe-number-counter__title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon',
			array(
				'label' => __( 'Icon', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-number-counter__icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Size', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-number-counter__icon' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings  = $this->get_settings_for_display();
		$number    = isset( $settings['number'] ) ? (float) $settings['number'] : 0;
		$decimals  = isset( $settings['decimals'] ) ? (int) $settings['decimals'] : 0;
		$delimiter = isset( $settings['delimiter'] ) ? sanitize_key( (string) $settings['delimiter'] ) : 'none';
		$prefix    = isset( $settings['prefix'] ) ? (string) $settings['prefix'] : '';
		$suffix    = isset( $settings['suffix'] ) ? (string) $settings['suffix'] : '';
		$title     = isset( $settings['title'] ) ? (string) $settings['title'] : '';
		$duration  = isset( $settings['duration'] ) ? absint( $settings['duration'] ) : 2000;
		$easing    = isset( $settings['easing'] ) ? sanitize_key( (string) $settings['easing'] ) : 'ease-out';
		$once      = isset( $settings['trigger_once'] ) && 'yes' === $settings['trigger_once'];
		$formatted = Number_Counter_Format::format( $number, $decimals, $delimiter );

		$config = array(
			'target'    => $number,
			'duration'  => $duration,
			'easing'    => $easing,
			'delimiter' => $delimiter,
			'decimals'  => $decimals,
			'once'      => $once,
		);

		echo '<div class="ltxe-number-counter" data-ltxe-counter="' . esc_attr( wp_json_encode( $config ) ) . '">';
		if ( ! empty( $settings['selected_icon']['value'] ) ) {
			echo '<span class="ltxe-number-counter__icon">';
			Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) );
			echo '</span>';
		}
		echo '<div class="ltxe-number-counter__value">';
		echo '<span class="ltxe-number-counter__prefix">' . esc_html( $prefix ) . '</span>';
		echo '<span class="ltxe-number-counter__digits" data-start="0">' . esc_html( Number_Counter_Format::format( 0, $decimals, $delimiter ) ) . '</span>';
		echo '<span class="ltxe-number-counter__suffix">' . esc_html( $suffix ) . '</span>';
		echo '</div>';
		if ( '' !== $title ) {
			echo '<div class="ltxe-number-counter__title">' . esc_html( $title ) . '</div>';
		}
		echo '<span class="screen-reader-text">' . esc_html( $prefix . $formatted . $suffix . ' ' . $title ) . '</span>';
		echo '</div>';
	}
}
