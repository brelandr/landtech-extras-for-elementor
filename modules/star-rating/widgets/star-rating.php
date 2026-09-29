<?php
namespace LandTechExtras\Modules\StarRating\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PHP-only star rating display.
 *
 * @since 2.8.0
 */
class Star_Rating extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-star-rating';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Star Rating', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-rating';
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-star-rating' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_rating',
			array(
				'label' => __( 'Rating', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'rating',
			array(
				'label'   => __( 'Rating', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 4.5,
				),
				'range'   => array(
					'px' => array(
						'min'  => 0,
						'max'  => 10,
						'step' => 0.5,
					),
				),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'max_stars',
			array(
				'label'   => __( 'Max Stars', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '5',
				'options' => array(
					'3'  => '3',
					'4'  => '4',
					'5'  => '5',
					'10' => '10',
				),
			)
		);

		$this->add_control(
			'icon_style',
			array(
				'label'   => __( 'Icon', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'star',
				'options' => array(
					'star'  => __( 'Star', 'landtech-extras-for-elementor' ),
					'heart' => __( 'Heart', 'landtech-extras-for-elementor' ),
					'thumb' => __( 'Thumb', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'show_numeric',
			array(
				'label'        => __( 'Show Numeric Value', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'review_count',
			array(
				'label'   => __( 'Review Count', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => array(
					'inline'  => __( 'Stars left + text right', 'landtech-extras-for-elementor' ),
					'stacked' => __( 'Stars stacked above text', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'add_schema',
			array(
				'label'        => __( 'Add Schema', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
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
			'filled_color',
			array(
				'label'     => __( 'Filled Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-star--full, {{WRAPPER}} .ltxe-star--half .ltxe-star__fill' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'empty_color',
			array(
				'label'     => __( 'Empty Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-star--empty, {{WRAPPER}} .ltxe-star--half .ltxe-star__empty' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'size',
			array(
				'label'      => __( 'Size', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'size' => 22,
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-star' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typo',
				'selector' => '{{WRAPPER}} .ltxe-star-rating__meta',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$rating   = isset( $settings['rating']['size'] ) ? (float) $settings['rating']['size'] : 0;
		$max      = isset( $settings['max_stars'] ) ? absint( $settings['max_stars'] ) : 5;
		if ( $max < 3 ) {
			$max = 5;
		}
		if ( $rating > $max ) {
			$rating = $max;
		}
		if ( $rating < 0 ) {
			$rating = 0;
		}

		$icon    = isset( $settings['icon_style'] ) ? sanitize_key( $settings['icon_style'] ) : 'star';
		$layout  = isset( $settings['layout'] ) && 'stacked' === $settings['layout'] ? 'stacked' : 'inline';
		$glyph   = $this->get_icon_glyph( $icon );
		$filled  = (int) floor( $rating );
		$half    = ( ( $rating - $filled ) >= 0.5 ) ? 1 : 0;
		$empty   = max( 0, $max - $filled - $half );

		echo '<div class="ltxe-star-rating ltxe-star-rating--' . esc_attr( $layout ) . '">';
		echo '<div class="ltxe-star-rating__stars" aria-hidden="true">';
		for ( $i = 0; $i < $filled; $i++ ) {
			echo '<span class="ltxe-star ltxe-star--full">' . esc_html( $glyph ) . '</span>';
		}
		if ( $half ) {
			echo '<span class="ltxe-star ltxe-star--half"><span class="ltxe-star__empty">' . esc_html( $glyph ) . '</span><span class="ltxe-star__fill">' . esc_html( $glyph ) . '</span></span>';
		}
		for ( $i = 0; $i < $empty; $i++ ) {
			echo '<span class="ltxe-star ltxe-star--empty">' . esc_html( $glyph ) . '</span>';
		}
		echo '</div>';

		$show_num = isset( $settings['show_numeric'] ) && 'yes' === $settings['show_numeric'];
		$count    = isset( $settings['review_count'] ) ? (string) $settings['review_count'] : '';
		if ( $show_num || '' !== $count ) {
			echo '<div class="ltxe-star-rating__meta">';
			if ( $show_num ) {
				echo '<span class="ltxe-star-rating__value">' . esc_html( (string) $rating ) . ' / ' . esc_html( (string) $max ) . '</span>';
			}
			if ( '' !== $count ) {
				echo ' <span class="ltxe-star-rating__count">(' . esc_html( $count ) . ')</span>';
			}
			echo '</div>';
		}
		echo '</div>';

		if ( isset( $settings['add_schema'] ) && 'yes' === $settings['add_schema'] ) {
			$meta  = '<meta itemprop="ratingValue" content="' . esc_attr( (string) $rating ) . '">';
			$meta .= '<meta itemprop="bestRating" content="' . esc_attr( (string) $max ) . '">';
			if ( '' !== $count ) {
				$meta .= '<meta itemprop="reviewCount" content="' . esc_attr( $count ) . '">';
			}
			echo '<div itemscope itemtype="https://schema.org/AggregateRating" class="ltxe-star-rating__schema">';
			echo wp_kses(
				$meta,
				array(
					'meta' => array(
						'itemprop' => true,
						'content'  => true,
					),
				)
			);
			echo '</div>';
		}
	}

	/**
	 * @param string $icon Icon key.
	 * @return string
	 */
	protected function get_icon_glyph( $icon ) {
		if ( 'heart' === $icon ) {
			return '♥';
		}
		if ( 'thumb' === $icon ) {
			return '👍';
		}
		return '★';
	}
}
