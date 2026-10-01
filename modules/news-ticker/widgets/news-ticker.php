<?php
/**
 * News ticker widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\NewsTicker\Widgets;

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
class News_Ticker extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-news-ticker';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'News Ticker', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-posts-ticker';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'ticker', 'news', 'marquee', 'announcement' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-news-ticker' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-news-ticker' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => __( 'Ticker', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => array(
					'manual' => __( 'Manual items', 'landtech-extras-for-elementor' ),
					'posts'  => __( 'Latest posts', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'text',
			array(
				'label'   => __( 'Text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);
		$repeater->add_control(
			'url',
			array(
				'label' => __( 'Link', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'text' => __( 'Welcome to the news ticker', 'landtech-extras-for-elementor' ) ),
					array( 'text' => __( 'Add your announcements here', 'landtech-extras-for-elementor' ) ),
				),
				'title_field' => '{{{ text }}}',
				'condition'   => array(
					'source' => 'manual',
				),
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'     => __( 'Post Type', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'post',
				'condition' => array(
					'source' => 'posts',
				),
			)
		);

		$this->add_control(
			'label',
			array(
				'label'   => __( 'Ticker Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Latest:', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => __( 'Scroll Speed', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium',
				'options' => array(
					'slow'   => __( 'Slow', 'landtech-extras-for-elementor' ),
					'medium' => __( 'Medium', 'landtech-extras-for-elementor' ),
					'fast'   => __( 'Fast', 'landtech-extras-for-elementor' ),
					'custom' => __( 'Custom', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'custom_speed',
			array(
				'label'     => __( 'Custom duration (s)', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 30,
				'condition' => array(
					'speed' => 'custom',
				),
				'selectors' => array(
					'{{WRAPPER}} .ltxe-nt' => '--ltxe-nt-speed: {{VALUE}}s;',
				),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'   => __( 'Direction', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => __( 'Left', 'landtech-extras-for-elementor' ),
					'right' => __( 'Right', 'landtech-extras-for-elementor' ),
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
			)
		);

		$this->add_control(
			'separator',
			array(
				'label'   => __( 'Separator', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '•',
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
			'label_bg',
			array(
				'label'     => __( 'Label Background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e94560',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-nt__label' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Label Text Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-nt__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sep_color',
			array(
				'label'     => __( 'Separator Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e94560',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-nt__sep' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bar_bg',
			array(
				'label'     => __( 'Bar Background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1a1a2e',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-nt' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bar_height',
			array(
				'label'      => __( 'Bar Height', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 36,
						'max' => 80,
					),
				),
				'default'    => array(
					'size' => 48,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-nt' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bar_border',
			array(
				'label'     => __( 'Border Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-nt' => 'border-top: 1px solid {{VALUE}}; border-bottom: 1px solid {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_typo',
				'selector' => '{{WRAPPER}} .ltxe-nt__item',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return array<int,array{text:string,url:string}>
	 */
	private function collect_items( $settings ) {
		$items = array();
		if ( isset( $settings['source'] ) && 'posts' === $settings['source'] ) {
			$pt = isset( $settings['post_type'] ) ? sanitize_key( (string) $settings['post_type'] ) : 'post';
			if ( ! post_type_exists( $pt ) ) {
				$pt = 'post';
			}
			$query = new \WP_Query(
				array(
					'post_type'      => $pt,
					'posts_per_page' => 10,
					'post_status'    => 'publish',
					'no_found_rows'  => true,
				)
			);
			if ( $query->have_posts() ) {
				foreach ( $query->posts as $post ) {
					$items[] = array(
						'text' => get_the_title( $post ),
						'url'  => get_permalink( $post ),
					);
				}
			}
			wp_reset_postdata();
			return $items;
		}

		if ( ! empty( $settings['items'] ) && is_array( $settings['items'] ) ) {
			foreach ( $settings['items'] as $row ) {
				if ( empty( $row['text'] ) ) {
					continue;
				}
				$items[] = array(
					'text' => (string) $row['text'],
					'url'  => ! empty( $row['url']['url'] ) ? (string) $row['url']['url'] : '',
				);
			}
		}
		return $items;
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings  = $this->get_settings_for_display();
		$items     = $this->collect_items( $settings );
		$speed     = isset( $settings['speed'] ) ? sanitize_key( (string) $settings['speed'] ) : 'medium';
		$direction = isset( $settings['direction'] ) && 'right' === $settings['direction'] ? 'right' : 'left';
		$pause     = ( isset( $settings['pause_hover'] ) && 'yes' === $settings['pause_hover'] );
		$sep       = isset( $settings['separator'] ) ? (string) $settings['separator'] : '•';
		$classes   = array(
			'ltxe-nt',
			'ltxe-nt--' . $speed,
			'ltxe-nt--' . $direction,
		);
		if ( $pause ) {
			$classes[] = 'ltxe-nt--pause';
		}

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-ltxe-nt="1">';
		if ( ! empty( $settings['label'] ) ) {
			echo '<span class="ltxe-nt__label">' . esc_html( (string) $settings['label'] ) . '</span>';
		}
		echo '<div class="ltxe-nt__viewport"><div class="ltxe-nt__track">';
		$this->render_set( $items, $sep );
		$this->render_set( $items, $sep );
		echo '</div></div></div>';
	}

	/**
	 * @param array<int,array{text:string,url:string}> $items Items.
	 * @param string                                   $sep   Separator.
	 * @return void
	 */
	private function render_set( $items, $sep ) {
		echo '<span class="ltxe-nt__set">';
		foreach ( $items as $index => $item ) {
			if ( $index > 0 && '' !== $sep ) {
				echo '<span class="ltxe-nt__sep" aria-hidden="true">' . esc_html( $sep ) . '</span>';
			}
			$text = isset( $item['text'] ) ? (string) $item['text'] : '';
			$url  = isset( $item['url'] ) ? (string) $item['url'] : '';
			if ( '' !== $url ) {
				echo '<a class="ltxe-nt__item" href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a>';
			} else {
				echo '<span class="ltxe-nt__item">' . esc_html( $text ) . '</span>';
			}
		}
		echo '</span>';
	}
}
