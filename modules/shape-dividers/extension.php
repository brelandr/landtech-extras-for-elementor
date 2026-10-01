<?php
/**
 * Shape divider section/container controls + frontend inject.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\ShapeDividers;

use Elementor\Controls_Manager;
use Elementor\Element_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.1.0
 */
class Shape_Dividers_Extension {

	/**
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * @return self
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hook controls and render.
	 */
	private function __construct() {
		add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'register_controls' ), 10, 2 );
		add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'register_controls' ), 10, 2 );
		add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ) );
		add_action( 'elementor/frontend/container/before_render', array( $this, 'before_render' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Register CSS.
	 *
	 * @return void
	 */
	public function register_assets() {
		wp_register_style(
			'landtech-extras-shape-dividers',
			plugins_url( '/modules/shape-dividers/assets/css/ltxe-shape-dividers.css', LANDTECH_EXTRAS__FILE__ ),
			array(),
			LANDTECH_EXTRAS_VERSION
		);
		wp_register_script(
			'landtech-extras-shape-dividers',
			plugins_url( '/modules/shape-dividers/assets/js/ltxe-shape-dividers-editor.js', LANDTECH_EXTRAS__FILE__ ),
			array( 'jquery' ),
			LANDTECH_EXTRAS_VERSION,
			true
		);
	}

	/**
	 * Shape slug labels.
	 *
	 * @return array
	 */
	public static function shape_options() {
		$out = array( '' => __( 'None', 'landtech-extras-for-elementor' ) );
		foreach ( array_keys( self::path_map() ) as $slug ) {
			$out[ $slug ] = ucwords( str_replace( '-', ' ', $slug ) );
		}
		return $out;
	}

	/**
	 * SVG path `d` values (viewBox 0 0 1200 120).
	 *
	 * @return array<string,string>
	 */
	public static function path_map() {
		return array(
			'wave-1'           => 'M0,64 C150,120 350,0 600,64 C850,128 1050,16 1200,64 L1200,120 L0,120 Z',
			'wave-2'           => 'M0,80 C200,20 400,20 600,80 C800,140 1000,140 1200,80 L1200,120 L0,120 Z',
			'wave-3'           => 'M0,40 C300,100 500,0 800,50 C1000,80 1100,20 1200,40 L1200,120 L0,120 Z',
			'slant-1'          => 'M0,80 L1200,0 L1200,120 L0,120 Z',
			'slant-2'          => 'M0,0 L1200,80 L1200,120 L0,120 Z',
			'triangle-1'       => 'M0,120 L600,0 L1200,120 Z',
			'curve-1'          => 'M0,120 Q600,0 1200,120 Z',
			'curve-2'          => 'M0,0 Q600,120 1200,0 L1200,120 L0,120 Z',
			'zigzag-1'         => 'M0,80 L150,20 L300,80 L450,20 L600,80 L750,20 L900,80 L1050,20 L1200,80 L1200,120 L0,120 Z',
			'arrow-1'          => 'M0,80 L560,80 L600,20 L640,80 L1200,80 L1200,120 L0,120 Z',
			'tilt-1'           => 'M0,40 L1200,100 L1200,120 L0,120 Z',
			'clouds-1'         => 'M0,80 C80,80 80,40 160,40 C200,10 280,10 320,40 C400,20 480,40 520,70 C600,40 700,40 760,70 C840,30 960,40 1020,70 C1100,50 1160,70 1200,80 L1200,120 L0,120 Z',
			'fan-1'            => 'M0,120 L0,80 C200,80 200,20 400,20 C600,20 600,80 800,80 C1000,80 1000,20 1200,20 L1200,120 Z',
			'drops-1'          => 'M0,90 Q150,20 300,90 T600,90 T900,90 T1200,90 L1200,120 L0,120 Z',
			'mountains-1'      => 'M0,120 L0,90 L200,20 L400,90 L600,10 L800,90 L1000,40 L1200,90 L1200,120 Z',
			'book-1'           => 'M0,40 Q300,100 600,40 Q900,0 1200,40 L1200,120 L0,120 Z',
			'split-1'          => 'M0,120 L0,60 L600,10 L1200,60 L1200,120 Z',
			'waves-opacity-1'  => 'M0,70 C200,10 400,130 600,70 C800,10 1000,130 1200,70 L1200,120 L0,120 Z',
			'pyramids-1'       => 'M0,120 L200,40 L400,120 L600,20 L800,120 L1000,50 L1200,120 Z',
			'curve-asym-1'     => 'M0,90 C200,10 800,110 1200,30 L1200,120 L0,120 Z',
		);
	}

	/**
	 * Add Layout tab controls.
	 *
	 * @param Element_Base $element Element.
	 * @param array        $args    Args.
	 * @return void
	 */
	public function register_controls( $element, $args ) {
		unset( $args );
		$element->start_controls_section(
			'ltxe_shape_dividers',
			array(
				'label' => __( 'Shape Dividers', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);
		foreach ( array( 'top', 'bottom' ) as $side ) {
			$element->add_control(
				'ltxe_sd_' . $side,
				array(
					'label'   => ( 'top' === $side ) ? __( 'Top shape', 'landtech-extras-for-elementor' ) : __( 'Bottom shape', 'landtech-extras-for-elementor' ),
					'type'    => Controls_Manager::SELECT,
					'options' => self::shape_options(),
					'default' => '',
				)
			);
			$element->add_control(
				'ltxe_sd_' . $side . '_color',
				array(
					'label'     => __( 'Color', 'landtech-extras-for-elementor' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#0b1020',
					'condition' => array( 'ltxe_sd_' . $side . '!' => '' ),
				)
			);
			$element->add_responsive_control(
				'ltxe_sd_' . $side . '_width',
				array(
					'label'      => __( 'Width', 'landtech-extras-for-elementor' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'size' => 100,
						'unit' => '%',
					),
					'condition'  => array( 'ltxe_sd_' . $side . '!' => '' ),
				)
			);
			$element->add_responsive_control(
				'ltxe_sd_' . $side . '_height',
				array(
					'label'      => __( 'Height', 'landtech-extras-for-elementor' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'size' => 80,
						'unit' => 'px',
					),
					'condition'  => array( 'ltxe_sd_' . $side . '!' => '' ),
				)
			);
			$element->add_control(
				'ltxe_sd_' . $side . '_flip',
				array(
					'label'        => __( 'Flip horizontal', 'landtech-extras-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'condition'    => array( 'ltxe_sd_' . $side . '!' => '' ),
				)
			);
			$element->add_control(
				'ltxe_sd_' . $side . '_invert',
				array(
					'label'        => __( 'Invert (inside edge)', 'landtech-extras-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'condition'    => array( 'ltxe_sd_' . $side . '!' => '' ),
				)
			);
			$element->add_control(
				'ltxe_sd_' . $side . '_z',
				array(
					'label'     => __( 'Z-index', 'landtech-extras-for-elementor' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 1,
					'condition' => array( 'ltxe_sd_' . $side . '!' => '' ),
				)
			);
		}
		$element->end_controls_section();
	}

	/**
	 * Enqueue and store pending markup.
	 *
	 * @param Element_Base $element Element.
	 * @return void
	 */
	public function before_render( $element ) {
		$s = $element->get_settings_for_display();
		if ( empty( $s['ltxe_sd_top'] ) && empty( $s['ltxe_sd_bottom'] ) ) {
			return;
		}
		wp_enqueue_style( 'landtech-extras-shape-dividers' );
		wp_enqueue_script( 'landtech-extras-shape-dividers' );
		$element->add_render_attribute( '_wrapper', 'class', 'ltxe-has-shape-divider' );
		$payload = array();
		foreach ( array( 'top', 'bottom' ) as $side ) {
			$key = 'ltxe_sd_' . $side;
			if ( empty( $s[ $key ] ) ) {
				continue;
			}
			$payload[ $side ] = array(
				'slug'   => sanitize_key( (string) $s[ $key ] ),
				'color'  => isset( $s[ $key . '_color' ] ) ? (string) $s[ $key . '_color' ] : '#0b1020',
				'flip'   => ! empty( $s[ $key . '_flip' ] ) && 'yes' === $s[ $key . '_flip' ],
				'invert' => ! empty( $s[ $key . '_invert' ] ) && 'yes' === $s[ $key . '_invert' ],
				'z'      => isset( $s[ $key . '_z' ] ) ? (int) $s[ $key . '_z' ] : 1,
				'h'      => isset( $s[ $key . '_height' ]['size'] ) ? (int) $s[ $key . '_height' ]['size'] : 80,
				'w'      => isset( $s[ $key . '_width' ]['size'] ) ? (int) $s[ $key . '_width' ]['size'] : 100,
				'svg'    => self::svg_for( (string) $s[ $key ], isset( $s[ $key . '_color' ] ) ? (string) $s[ $key . '_color' ] : '#0b1020' ),
			);
		}
		$element->add_render_attribute( '_wrapper', 'data-ltxe-sd', wp_json_encode( $payload ) );
	}

	/**
	 * Build divider HTML.
	 *
	 * @param array $s Settings.
	 * @return string
	 */
	public static function render_dividers_html( $s ) {
		if ( ! is_array( $s ) ) {
			return '';
		}
		$html = '';
		foreach ( array( 'top', 'bottom' ) as $side ) {
			$key = 'ltxe_sd_' . $side;
			if ( empty( $s[ $key ] ) ) {
				continue;
			}
			$slug  = sanitize_key( (string) $s[ $key ] );
			$color = isset( $s[ $key . '_color' ] ) ? (string) $s[ $key . '_color' ] : '#0b1020';
			$flip  = ! empty( $s[ $key . '_flip' ] ) && 'yes' === $s[ $key . '_flip' ];
			$inv   = ! empty( $s[ $key . '_invert' ] ) && 'yes' === $s[ $key . '_invert' ];
			$z     = isset( $s[ $key . '_z' ] ) ? (int) $s[ $key . '_z' ] : 1;
			$h     = ( isset( $s[ $key . '_height' ]['size'] ) ) ? (int) $s[ $key . '_height' ]['size'] : 80;
			$w     = ( isset( $s[ $key . '_width' ]['size'] ) ) ? (int) $s[ $key . '_width' ]['size'] : 100;
			$class = 'ltxe-shape-divider ltxe-shape-divider--' . $side;
			if ( $flip ) {
				$class .= ' is-flip';
			}
			if ( $inv ) {
				$class .= ' is-invert';
			}
			$style = 'z-index:' . $z . ';height:' . $h . 'px;width:' . $w . '%;';
			$html .= '<div class="' . esc_attr( $class ) . '" data-ltxe-shape="' . esc_attr( $slug ) . '" style="' . esc_attr( $style ) . '">';
			$html .= self::svg_for( $slug, $color );
			$html .= '</div>';
		}
		return $html;
	}

	/**
	 * SVG markup for a slug.
	 *
	 * @param string $slug  Shape slug.
	 * @param string $color Fill color.
	 * @return string
	 */
	public static function svg_for( $slug, $color ) {
		$map = self::path_map();
		$slug = sanitize_key( $slug );
		if ( ! isset( $map[ $slug ] ) ) {
			return '';
		}
		$hex = sanitize_hex_color( $color );
		if ( ! $hex ) {
			$hex = '#0b1020';
		}
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none" aria-hidden="true"><path fill="' . esc_attr( $hex ) . '" d="' . esc_attr( $map[ $slug ] ) . '"></path></svg>';
	}
}
