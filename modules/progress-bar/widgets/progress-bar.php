<?php
namespace LandTechExtras\Modules\ProgressBar\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Linear progress bar.
 *
 * @since 2.7.0
 */
class Progress_Bar extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-progress-bar';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Progress Bar', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-skill-bar';
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-progress-bar' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-progress-bar' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_bar',
			array(
				'label' => __( 'Progress', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Progress', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'value',
			array(
				'label'   => __( 'Value', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 65,
				'min'     => 0,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'max',
			array(
				'label'   => __( 'Max Value', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 100,
				'min'     => 1,
			)
		);

		$this->add_control(
			'bar_style',
			array(
				'label'   => __( 'Style', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Horizontal', 'landtech-extras-for-elementor' ),
					'vertical'   => __( 'Vertical', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'show_value',
			array(
				'label'        => __( 'Show Value', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'value_position',
			array(
				'label'     => __( 'Value Position', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inside',
				'options'   => array(
					'inside' => __( 'Inside bar', 'landtech-extras-for-elementor' ),
					'above'  => __( 'Above bar', 'landtech-extras-for-elementor' ),
					'after'  => __( 'After bar', 'landtech-extras-for-elementor' ),
				),
				'condition' => array(
					'show_value' => 'yes',
				),
			)
		);

		$this->add_control(
			'striped',
			array(
				'label'        => __( 'Striped', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'animate',
			array(
				'label'        => __( 'Animate on Scroll', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'duration',
			array(
				'label'     => __( 'Duration (ms)', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 800,
				'condition' => array(
					'animate' => 'yes',
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

		$this->add_control(
			'fill_color',
			array(
				'label'     => __( 'Bar Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-progress-bar__fill' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'track_color',
			array(
				'label'     => __( 'Track Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-progress-bar__track' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'      => __( 'Height', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-progress-bar--horizontal .ltxe-progress-bar__track' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ltxe-progress-bar--vertical .ltxe-progress-bar__track' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'radius',
			array(
				'label'      => __( 'Border Radius', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-progress-bar__track, {{WRAPPER}} .ltxe-progress-bar__fill' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typo',
				'selector' => '{{WRAPPER}} .ltxe-progress-bar__label, {{WRAPPER}} .ltxe-progress-bar__value',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$value    = isset( $settings['value'] ) ? (float) $settings['value'] : 0;
		$max      = isset( $settings['max'] ) ? (float) $settings['max'] : 100;
		if ( $max <= 0 ) {
			$max = 100;
		}
		$pct   = min( 100, max( 0, ( $value / $max ) * 100 ) );
		$style = isset( $settings['bar_style'] ) && 'vertical' === $settings['bar_style'] ? 'vertical' : 'horizontal';
		$show  = isset( $settings['show_value'] ) && 'yes' === $settings['show_value'];
		$pos   = isset( $settings['value_position'] ) ? sanitize_key( $settings['value_position'] ) : 'inside';
		$label = isset( $settings['label'] ) ? (string) $settings['label'] : '';

		$classes = array(
			'ltxe-progress-bar',
			'ltxe-progress-bar--' . $style,
		);
		if ( isset( $settings['striped'] ) && 'yes' === $settings['striped'] ) {
			$classes[] = 'ltxe-progress-bar--striped';
		}

		$config = array(
			'pct'      => $pct,
			'animate'  => ( isset( $settings['animate'] ) && 'yes' === $settings['animate'] ),
			'duration' => isset( $settings['duration'] ) ? absint( $settings['duration'] ) : 800,
			'axis'     => $style,
		);
		$json = wp_json_encode( $config );
		if ( ! is_string( $json ) ) {
			$json = '{}';
		}

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" role="progressbar"';
		echo ' aria-valuenow="' . esc_attr( (string) $value ) . '"';
		echo ' aria-valuemin="0"';
		echo ' aria-valuemax="' . esc_attr( (string) $max ) . '"';
		echo ' data-ltxe-progress="' . esc_attr( $json ) . '">';
		if ( '' !== $label || ( $show && 'above' === $pos ) ) {
			echo '<div class="ltxe-progress-bar__label">';
			echo esc_html( $label );
			if ( $show && 'above' === $pos ) {
				echo '<span class="ltxe-progress-bar__value">' . esc_html( (string) $value ) . '</span>';
			}
			echo '</div>';
		}
		echo '<div class="ltxe-progress-bar__track">';
		echo '<div class="ltxe-progress-bar__fill" style="' . ( 'vertical' === $style ? 'height:0%' : 'width:0%' ) . '">';
		if ( $show && 'inside' === $pos ) {
			echo '<span class="ltxe-progress-bar__value">' . esc_html( (string) $value ) . '</span>';
		}
		echo '</div></div>';
		if ( $show && 'after' === $pos ) {
			echo '<span class="ltxe-progress-bar__value">' . esc_html( (string) $value ) . '</span>';
		}
		echo '</div>';
	}
}
