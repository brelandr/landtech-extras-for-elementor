<?php
/**
 * Filterable gallery widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\Gallery\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.10.0
 */
class Gallery_Filterable extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-gallery-filterable';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Filterable Gallery', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'gallery', 'filter', 'portfolio', 'isotope' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-gallery-filterable' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-gallery-filterable' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => __( 'Items', 'landtech-extras-for-elementor' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label' => __( 'Image', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$repeater->add_control(
			'category',
			array(
				'label'   => __( 'Category', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Architecture',
			)
		);
		$repeater->add_control(
			'caption',
			array(
				'label' => __( 'Caption', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Gallery Items', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ category }}}',
				'default'     => array(
					array( 'category' => 'Architecture' ),
					array( 'category' => 'Interior' ),
					array( 'category' => 'Landscape' ),
					array( 'category' => 'People' ),
					array( 'category' => 'Architecture' ),
					array( 'category' => 'Interior' ),
					array( 'category' => 'Landscape' ),
					array( 'category' => 'People' ),
				),
			)
		);

		$this->add_control(
			'all_label',
			array(
				'label'   => __( 'All Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'All', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'bar_position',
			array(
				'label'   => __( 'Filter Bar Position', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'above',
				'options' => array(
					'above' => __( 'Above', 'landtech-extras-for-elementor' ),
					'below' => __( 'Below', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'button_style',
			array(
				'label'   => __( 'Filter Button Style', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'pill',
				'options' => array(
					'pill'      => __( 'Pill', 'landtech-extras-for-elementor' ),
					'underline' => __( 'Underline', 'landtech-extras-for-elementor' ),
					'solid'     => __( 'Solid', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'active_color',
			array(
				'label'     => __( 'Active Filter Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e94560',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-gf' => '--ltxe-gf-active: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'masonry',
				'options' => array(
					'masonry'  => __( 'Masonry', 'landtech-extras-for-elementor' ),
					'fitRows'  => __( 'Grid', 'landtech-extras-for-elementor' ),
					'fit-rows' => __( 'Fit rows', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'animation',
			array(
				'label'   => __( 'Animation', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => array(
					'fade'  => __( 'Fade', 'landtech-extras-for-elementor' ),
					'scale' => __( 'Scale', 'landtech-extras-for-elementor' ),
					'slide' => __( 'Slide', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'landtech-extras-for-elementor' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 6,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'selectors'      => array(
					'{{WRAPPER}} .ltxe-gf' => '--ltxe-gf-cols: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-gf' => '--ltxe-gf-gap: {{SIZE}}{{UNIT}};',
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
		$items    = isset( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		$all      = isset( $settings['all_label'] ) ? (string) $settings['all_label'] : 'All';
		$pos      = isset( $settings['bar_position'] ) ? sanitize_key( (string) $settings['bar_position'] ) : 'above';
		$style    = isset( $settings['button_style'] ) ? sanitize_key( (string) $settings['button_style'] ) : 'pill';
		$layout   = isset( $settings['layout'] ) ? sanitize_text_field( (string) $settings['layout'] ) : 'masonry';
		$anim     = isset( $settings['animation'] ) ? sanitize_key( (string) $settings['animation'] ) : 'fade';
		if ( 'fit-rows' === $layout ) {
			$layout = 'fitRows';
		}

		$cats = array();
		foreach ( $items as $row ) {
			$cat = isset( $row['category'] ) ? sanitize_text_field( (string) $row['category'] ) : '';
			if ( '' !== $cat ) {
				$cats[ $cat ] = $cat;
			}
		}

		$config = array(
			'layout' => $layout,
			'anim'   => $anim,
		);

		echo '<div class="ltxe-gf ltxe-gf--' . esc_attr( $style ) . ' ltxe-gf--' . esc_attr( $pos ) . '" data-ltxe-gf="' . esc_attr( wp_json_encode( $config ) ) . '">';
		$bar = $this->render_bar( $cats, $all );
		if ( 'below' !== $pos ) {
			echo $bar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled from escaped fragments.
		}
		echo '<div class="ltxe-gf__grid">';
		foreach ( $items as $i => $row ) {
			$this->render_item( $row, $i );
		}
		echo '</div>';
		if ( 'below' === $pos ) {
			echo $bar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled from escaped fragments.
		}
		echo '</div>';
	}

	/**
	 * @param array<string,string> $cats Categories.
	 * @param string               $all  All label.
	 * @return string
	 */
	private function render_bar( $cats, $all ) {
		$html  = '<div class="ltxe-gf__bar" role="toolbar" aria-label="' . esc_attr__( 'Gallery filters', 'landtech-extras-for-elementor' ) . '">';
		$html .= '<button type="button" class="ltxe-gf__btn is-active" data-filter="*" aria-pressed="true">' . esc_html( $all ) . '</button>';
		foreach ( $cats as $cat ) {
			$slug  = sanitize_title( $cat );
			$html .= '<button type="button" class="ltxe-gf__btn" data-filter=".' . esc_attr( 'cat-' . $slug ) . '" aria-pressed="false">' . esc_html( $cat ) . '</button>';
		}
		$html .= '</div>';
		return $html;
	}

	/**
	 * @param array<string,mixed> $row Row.
	 * @param int                 $i   Index.
	 * @return void
	 */
	private function render_item( $row, $i ) {
		$cat  = isset( $row['category'] ) ? sanitize_text_field( (string) $row['category'] ) : '';
		$slug = sanitize_title( $cat );
		$src  = ! empty( $row['image']['url'] ) ? esc_url( (string) $row['image']['url'] ) : '';
		$cap  = isset( $row['caption'] ) ? (string) $row['caption'] : $cat;
		echo '<figure class="ltxe-gf__item cat-' . esc_attr( $slug ) . '" data-category="' . esc_attr( $cat ) . '">';
		if ( '' !== $src ) {
			echo '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $cap ) . '" />';
		} else {
			echo '<span class="ltxe-gf__ph">' . esc_html( $cap ? $cap : sprintf( /* translators: %d: item index */ __( 'Item %d', 'landtech-extras-for-elementor' ), $i + 1 ) ) . '</span>';
		}
		if ( '' !== $cap ) {
			echo '<figcaption>' . esc_html( $cap ) . '</figcaption>';
		}
		echo '</figure>';
	}
}
