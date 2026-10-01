<?php
/**
 * Comparison table widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\ComparisonTable\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.1.0
 */
class Comparison_Table extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-comparison-table';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Comparison Table', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-table';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'compare', 'pricing', 'table', 'plans' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-comparison-table' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-comparison-table' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$cols = new Repeater();
		$cols->add_control(
			'title',
			array(
				'label'   => __( 'Title', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Pro', 'landtech-extras-for-elementor' ),
			)
		);
		$cols->add_control(
			'subtitle',
			array(
				'label' => __( 'Subtitle', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$cols->add_control(
			'price',
			array(
				'label'   => __( 'Price', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '$49',
			)
		);
		$cols->add_control(
			'highlight',
			array(
				'label'        => __( 'Featured', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);
		$cols->add_control(
			'button_text',
			array(
				'label'   => __( 'Button text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Get started', 'landtech-extras-for-elementor' ),
			)
		);
		$cols->add_control(
			'button_url',
			array(
				'label' => __( 'Button URL', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->start_controls_section(
			'section_columns',
			array(
				'label' => __( 'Columns', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'columns',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $cols->get_controls(),
				'default'     => array(
					array(
						'title' => __( 'Free', 'landtech-extras-for-elementor' ),
						'price' => '$0',
					),
					array(
						'title'     => __( 'Pro', 'landtech-extras-for-elementor' ),
						'price'     => '$49',
						'highlight' => 'yes',
					),
					array(
						'title' => __( 'Enterprise', 'landtech-extras-for-elementor' ),
						'price' => __( 'Talk to us', 'landtech-extras-for-elementor' ),
					),
				),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->end_controls_section();

		$rows = new Repeater();
		$rows->add_control(
			'label',
			array(
				'label'   => __( 'Feature', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Widgets', 'landtech-extras-for-elementor' ),
			)
		);
		$rows->add_control(
			'tooltip',
			array(
				'label' => __( 'Tooltip', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);
		$rows->add_control(
			'values',
			array(
				'label'       => __( 'Values (comma-separated: check, cross, text)', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'check,check,check',
				'description' => __( 'One value per column.', 'landtech-extras-for-elementor' ),
			)
		);

		$this->start_controls_section(
			'section_rows',
			array(
				'label' => __( 'Rows', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'rows',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rows->get_controls(),
				'default'     => array(
					array(
						'label'  => __( 'Core widgets', 'landtech-extras-for-elementor' ),
						'values' => 'check,check,check',
					),
					array(
						'label'  => __( 'Premium support', 'landtech-extras-for-elementor' ),
						'values' => 'cross,check,check',
					),
					array(
						'label'  => __( 'Sites', 'landtech-extras-for-elementor' ),
						'values' => '1,5,Unlimited',
					),
				),
				'title_field' => '{{{ label }}}',
			)
		);
		$this->add_control(
			'mobile_mode',
			array(
				'label'   => __( 'Mobile behavior', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'tabs',
				'options' => array(
					'scroll' => __( 'Horizontal scroll', 'landtech-extras-for-elementor' ),
					'stack'  => __( 'Stack columns', 'landtech-extras-for-elementor' ),
					'tabs'   => __( 'Toggle between columns', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->add_control(
			'sticky_header',
			array(
				'label'        => __( 'Sticky header', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_cmp',
			array(
				'label' => __( 'Highlight', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'highlight_bg',
			array(
				'label'     => __( 'Featured background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-cmp__col--featured' => 'background-color: {{VALUE}};',
				),
			)
		);
		$this->add_responsive_control(
			'table_pad',
			array(
				'label'      => __( 'Cell padding', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-cmp td, {{WRAPPER}} .ltxe-cmp th' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render a cell value.
	 *
	 * @param string $raw Raw token.
	 * @return string HTML.
	 */
	private function cell_html( $raw ) {
		$raw = trim( (string) $raw );
		if ( 'check' === $raw ) {
			return '<span class="ltxe-cmp__check" aria-label="' . esc_attr__( 'Yes', 'landtech-extras-for-elementor' ) . '">✓</span>';
		}
		if ( 'cross' === $raw ) {
			return '<span class="ltxe-cmp__cross" aria-label="' . esc_attr__( 'No', 'landtech-extras-for-elementor' ) . '">✕</span>';
		}
		if ( 'partial' === $raw ) {
			return '<span class="ltxe-cmp__partial">~</span>';
		}
		return esc_html( $raw );
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$s       = $this->get_settings_for_display();
		$cols    = ( ! empty( $s['columns'] ) && is_array( $s['columns'] ) ) ? $s['columns'] : array();
		$rows    = ( ! empty( $s['rows'] ) && is_array( $s['rows'] ) ) ? $s['rows'] : array();
		$mobile  = isset( $s['mobile_mode'] ) ? sanitize_key( (string) $s['mobile_mode'] ) : 'tabs';
		$sticky  = isset( $s['sticky_header'] ) && 'yes' === $s['sticky_header'];

		echo '<div class="ltxe-cmp" data-ltxe-cmp="1" data-mobile="' . esc_attr( $mobile ) . '"' . ( $sticky ? ' data-sticky="1"' : '' ) . '>';
		echo '<div class="ltxe-cmp__tabs" hidden></div>';
		echo '<div class="ltxe-cmp__scroll">';
		echo '<table class="ltxe-cmp__table">';
		echo '<thead><tr><th class="ltxe-cmp__corner"></th>';
		foreach ( $cols as $i => $col ) {
			if ( ! is_array( $col ) ) {
				continue;
			}
			$feat = ! empty( $col['highlight'] ) && 'yes' === $col['highlight'];
			echo '<th class="ltxe-cmp__col' . ( $feat ? ' ltxe-cmp__col--featured' : '' ) . '" data-col="' . esc_attr( (string) $i ) . '">';
			if ( $feat ) {
				echo '<span class="ltxe-cmp__badge">' . esc_html__( 'Recommended', 'landtech-extras-for-elementor' ) . '</span>';
			}
			echo '<span class="ltxe-cmp__title">' . esc_html( isset( $col['title'] ) ? (string) $col['title'] : '' ) . '</span>';
			if ( ! empty( $col['subtitle'] ) ) {
				echo '<span class="ltxe-cmp__sub">' . esc_html( (string) $col['subtitle'] ) . '</span>';
			}
			echo '<span class="ltxe-cmp__price">' . esc_html( isset( $col['price'] ) ? (string) $col['price'] : '' ) . '</span>';
			$url = ( isset( $col['button_url']['url'] ) && is_string( $col['button_url']['url'] ) ) ? $col['button_url']['url'] : '#';
			echo '<a class="ltxe-cmp__btn" href="' . esc_url( $url ) . '">' . esc_html( isset( $col['button_text'] ) ? (string) $col['button_text'] : '' ) . '</a>';
			echo '</th>';
		}
		echo '</tr></thead><tbody>';
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$vals = isset( $row['values'] ) ? explode( ',', (string) $row['values'] ) : array();
			echo '<tr>';
			echo '<th scope="row" class="ltxe-cmp__row-label"';
			if ( ! empty( $row['tooltip'] ) ) {
				echo ' data-tooltip="' . esc_attr( (string) $row['tooltip'] ) . '"';
			}
			echo '>' . esc_html( isset( $row['label'] ) ? (string) $row['label'] : '' ) . '</th>';
			foreach ( $cols as $i => $col ) {
				$feat = is_array( $col ) && ! empty( $col['highlight'] ) && 'yes' === $col['highlight'];
				$cell = isset( $vals[ $i ] ) ? $vals[ $i ] : '';
				echo '<td class="' . ( $feat ? 'ltxe-cmp__col--featured' : '' ) . '" data-col="' . esc_attr( (string) $i ) . '">' . wp_kses( $this->cell_html( $cell ), array( 'span' => array( 'class' => true, 'aria-label' => true ) ) ) . '</td>';
			}
			echo '</tr>';
		}
		echo '</tbody></table></div></div>';
	}
}
