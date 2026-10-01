<?php
/**
 * Logo / client carousel widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\LogoCarousel\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.10.0
 */
class Logo_Carousel extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-logo-carousel';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Logo Carousel', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-logo';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'logo', 'client', 'brand', 'carousel', 'marquee' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-logo-carousel' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-logo-carousel' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_logos',
			array(
				'label' => __( 'Logos', 'landtech-extras-for-elementor' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			array(
				'label' => __( 'Logo', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$repeater->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);
		$repeater->add_control(
			'alt',
			array(
				'label'       => __( 'Alt Text Override', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$this->add_control(
			'logos',
			array(
				'label'       => __( 'Logos', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => __( 'Logo', 'landtech-extras-for-elementor' ),
				'default'     => array(
					array(),
					array(),
					array(),
					array(),
					array(),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'landtech-extras-for-elementor' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 2,
				'max'            => 8,
				'default'        => 5,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'selectors'      => array(
					'{{WRAPPER}} .ltxe-logo-carousel' => '--ltxe-lc-cols: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Gap', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 32,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-logo-carousel' => '--ltxe-lc-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Image Height', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 160,
					),
				),
				'default'    => array(
					'size' => 48,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-logo-carousel' => '--ltxe-lc-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'grayscale',
			array(
				'label'        => __( 'Grayscale', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'infinite',
			array(
				'label'        => __( 'Infinite Scroll', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'     => __( 'Scroll Speed', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'medium',
				'options'   => array(
					'slow'   => __( 'Slow', 'landtech-extras-for-elementor' ),
					'medium' => __( 'Medium', 'landtech-extras-for-elementor' ),
					'fast'   => __( 'Fast', 'landtech-extras-for-elementor' ),
				),
				'condition' => array(
					'infinite' => 'yes',
				),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'     => __( 'Scroll Direction', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'left',
				'options'   => array(
					'left'  => __( 'Left', 'landtech-extras-for-elementor' ),
					'right' => __( 'Right', 'landtech-extras-for-elementor' ),
				),
				'condition' => array(
					'infinite' => 'yes',
				),
			)
		);

		$this->add_control(
			'pause_hover',
			array(
				'label'        => __( 'Pause on Hover', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'infinite' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_border',
			array(
				'label'        => __( 'Dividers', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'opacity',
			array(
				'label'     => __( 'Image Opacity', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.05,
					),
				),
				'default'   => array(
					'size' => 0.6,
				),
				'selectors' => array(
					'{{WRAPPER}} .ltxe-logo-carousel' => '--ltxe-lc-op: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'opacity_hover',
			array(
				'label'     => __( 'Hover Opacity', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.05,
					),
				),
				'default'   => array(
					'size' => 1,
				),
				'selectors' => array(
					'{{WRAPPER}} .ltxe-logo-carousel' => '--ltxe-lc-op-h: {{SIZE}};',
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
		$logos     = isset( $settings['logos'] ) && is_array( $settings['logos'] ) ? $settings['logos'] : array();
		$infinite  = isset( $settings['infinite'] ) && 'yes' === $settings['infinite'];
		$gray      = isset( $settings['grayscale'] ) && 'yes' === $settings['grayscale'];
		$pause     = isset( $settings['pause_hover'] ) && 'yes' === $settings['pause_hover'];
		$border    = isset( $settings['show_border'] ) && 'yes' === $settings['show_border'];
		$speed     = isset( $settings['speed'] ) ? sanitize_key( (string) $settings['speed'] ) : 'medium';
		$direction = isset( $settings['direction'] ) ? sanitize_key( (string) $settings['direction'] ) : 'left';

		$classes = array( 'ltxe-logo-carousel' );
		$classes[] = $infinite ? 'ltxe-logo-carousel--marquee' : 'ltxe-logo-carousel--static';
		if ( $gray ) {
			$classes[] = 'ltxe-logo-carousel--gray';
		}
		if ( $pause ) {
			$classes[] = 'ltxe-logo-carousel--pause';
		}
		if ( $border ) {
			$classes[] = 'ltxe-logo-carousel--border';
		}
		if ( 'right' === $direction ) {
			$classes[] = 'ltxe-logo-carousel--rtl';
		}
		$classes[] = 'ltxe-logo-carousel--' . $speed;

		$items = $this->build_items( $logos );

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
		echo '<div class="ltxe-logo-carousel__track">';
		echo '<div class="ltxe-logo-carousel__set">';
		echo $this->render_items_html( $items, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled from escaped fragments.
		echo '</div>';
		if ( $infinite ) {
			echo '<div class="ltxe-logo-carousel__set" aria-hidden="true">';
			echo $this->render_items_html( $items, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled from escaped fragments.
			echo '</div>';
		}
		echo '</div></div>';
	}

	/**
	 * @param array<int,array<string,mixed>> $logos Repeater rows.
	 * @return array<int,array<string,string>>
	 */
	private function build_items( $logos ) {
		$items = array();
		$i     = 0;
		foreach ( $logos as $row ) {
			++$i;
			$url = '';
			if ( ! empty( $row['image']['url'] ) ) {
				$url = esc_url_raw( (string) $row['image']['url'] );
			}
			$alt = '';
			if ( ! empty( $row['alt'] ) ) {
				$alt = sanitize_text_field( (string) $row['alt'] );
			} elseif ( ! empty( $row['image']['alt'] ) ) {
				$alt = sanitize_text_field( (string) $row['image']['alt'] );
			} else {
				$alt = sprintf(
					/* translators: %d: logo index */
					__( 'Client logo %d', 'landtech-extras-for-elementor' ),
					$i
				);
			}
			$href = '';
			if ( ! empty( $row['link']['url'] ) ) {
				$href = esc_url_raw( (string) $row['link']['url'] );
			}
			$items[] = array(
				'src'  => $url,
				'alt'  => $alt,
				'href' => $href,
			);
		}
		if ( empty( $items ) ) {
			$items[] = array(
				'src'  => '',
				'alt'  => __( 'Client logo', 'landtech-extras-for-elementor' ),
				'href' => '',
			);
		}
		return $items;
	}

	/**
	 * @param array<int,array<string,string>> $items Items.
	 * @param bool                            $clone Duplicate set (no links).
	 * @return string
	 */
	private function render_items_html( $items, $clone ) {
		$html = '';
		foreach ( $items as $index => $item ) {
			$label = $index + 1;
			$src   = $item['src'];
			$img   = '';
			if ( '' !== $src ) {
				$img = '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $item['alt'] ) . '" />';
			} else {
				$img = '<span class="ltxe-logo-carousel__ph" aria-hidden="true">' . esc_html( sprintf( 'Logo %d', $label ) ) . '</span>';
			}
			$inner = '<span class="ltxe-logo-carousel__item">' . $img . '</span>';
			if ( ! $clone && '' !== $item['href'] ) {
				$html .= '<a class="ltxe-logo-carousel__link" href="' . esc_url( $item['href'] ) . '">' . $inner . '</a>';
			} else {
				$html .= $inner;
			}
		}
		return $html;
	}
}
