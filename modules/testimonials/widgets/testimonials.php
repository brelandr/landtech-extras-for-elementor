<?php
namespace LandTechExtras\Modules\Testimonials\Widgets;

use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\Testimonials\Review_Source;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Testimonials / reviews carousel.
 *
 * @since 2.9.0
 */
class Testimonials extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-testimonials';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Testimonials', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'testimonial', 'review', 'carousel', 'rating' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-testimonials' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-testimonials' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_source',
			array(
				'label' => __( 'Source', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Review Source', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => array(
					'manual'      => __( 'Manual', 'landtech-extras-for-elementor' ),
					'woocommerce' => __( 'WooCommerce Product Reviews', 'landtech-extras-for-elementor' ),
					'comments'    => __( 'WordPress Comments', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'wc_product_id',
			array(
				'label'       => __( 'Product ID (blank = all products)', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'condition'   => array(
					'source' => 'woocommerce',
				),
			)
		);

		$this->add_control(
			'comment_post_id',
			array(
				'label'       => __( 'Post ID (blank = current post)', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'condition'   => array(
					'source' => 'comments',
				),
			)
		);

		$this->add_control(
			'min_rating',
			array(
				'label'     => __( 'Minimum Rating', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '0',
				'options'   => array(
					'0' => __( 'Any', 'landtech-extras-for-elementor' ),
					'1' => '1+',
					'2' => '2+',
					'3' => '3+',
					'4' => '4+',
					'5' => __( '5 only', 'landtech-extras-for-elementor' ),
				),
				'condition' => array(
					'source!' => 'manual',
				),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'   => __( 'Maximum Items', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => Review_Source::MAX_ITEMS,
				'default' => 12,
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'author_name',
			array(
				'label'   => __( 'Name', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);
		$repeater->add_control(
			'author_role',
			array(
				'label'   => __( 'Role / Company', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);
		$repeater->add_control(
			'author_image',
			array(
				'label' => __( 'Photo', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$repeater->add_control(
			'rating',
			array(
				'label'   => __( 'Rating', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '5',
				'options' => array(
					'5' => '★★★★★',
					'4' => '★★★★',
					'3' => '★★★',
					'2' => '★★',
					'1' => '★',
				),
			)
		);
		$repeater->add_control(
			'content',
			array(
				'label'   => __( 'Review Text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
			)
		);
		$repeater->add_control(
			'date',
			array(
				'label' => __( 'Date', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'reviews',
			array(
				'label'       => __( 'Reviews', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->get_default_reviews(),
				'title_field' => '{{{ author_name }}}',
				'condition'   => array(
					'source' => 'manual',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'skin',
			array(
				'label'   => __( 'Skin', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cards',
				'options' => array(
					'cards'   => __( 'Cards', 'landtech-extras-for-elementor' ),
					'bubbles' => __( 'Bubbles', 'landtech-extras-for-elementor' ),
					'minimal' => __( 'Minimal', 'landtech-extras-for-elementor' ),
					'quote'   => __( 'Full-Width Quote', 'landtech-extras-for-elementor' ),
					'masonry' => __( 'Masonry Grid', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'display',
			array(
				'label'   => __( 'Display', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'carousel',
				'options' => array(
					'carousel' => __( 'Carousel', 'landtech-extras-for-elementor' ),
					'grid'     => __( 'Grid', 'landtech-extras-for-elementor' ),
					'single'   => __( 'Single', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => __( 'Columns', 'landtech-extras-for-elementor' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '1',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'selectors'      => array(
					'{{WRAPPER}} .ltxe-testimonials' => '--ltxe-tm-cols: {{VALUE}};',
				),
				'condition'      => array(
					'display!' => 'single',
				),
			)
		);

		$this->add_control(
			'show_avatar',
			array(
				'label'        => __( 'Show Photo', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_rating',
			array(
				'label'        => __( 'Show Rating', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_date',
			array(
				'label'        => __( 'Show Date', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_source',
			array(
				'label'        => __( 'Show Source', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'display' => 'carousel',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => __( 'Autoplay Speed (ms)', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 2000,
				'max'       => 20000,
				'default'   => 5000,
				'condition' => array(
					'display'  => 'carousel',
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'display' => 'carousel',
				),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => __( 'Arrows', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'display' => 'carousel',
				),
			)
		);

		$this->add_control(
			'show_dots',
			array(
				'label'        => __( 'Dots', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'display' => 'carousel',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_schema',
			array(
				'label' => __( 'Schema', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'add_schema',
			array(
				'label'        => __( 'AggregateRating Schema', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'schema_type',
			array(
				'label'     => __( 'Schema Type', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'Organization',
				'options'   => array(
					'Organization'   => __( 'Organization', 'landtech-extras-for-elementor' ),
					'LocalBusiness'  => __( 'Local Business', 'landtech-extras-for-elementor' ),
					'Product'        => __( 'Product', 'landtech-extras-for-elementor' ),
				),
				'condition' => array(
					'add_schema' => 'yes',
				),
			)
		);

		$this->add_control(
			'schema_name',
			array(
				'label'     => __( 'Name', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'add_schema' => 'yes',
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
			'card_bg',
			array(
				'label'     => __( 'Card Background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-testimonial' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-testimonial' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'star_color',
			array(
				'label'     => __( 'Star Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-testimonial__stars' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_gap',
			array(
				'label'      => __( 'Gap', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 64,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-testimonials' => '--ltxe-tm-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_pad',
			array(
				'label'      => __( 'Card Padding', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 64,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-testimonial' => 'padding: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'content_typo',
				'selector' => '{{WRAPPER}} .ltxe-testimonial__content',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .ltxe-testimonial',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .ltxe-testimonial',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return array<int,array<string,string>>
	 */
	private function get_default_reviews() {
		return array(
			array(
				'author_name' => __( 'Maya Chen', 'landtech-extras-for-elementor' ),
				'author_role' => __( 'Studio lead', 'landtech-extras-for-elementor' ),
				'rating'      => '5',
				'content'     => __( 'Clear, fast, and easy to restyle. We dropped this on a client homepage the same afternoon.', 'landtech-extras-for-elementor' ),
				'date'        => '',
			),
			array(
				'author_name' => __( 'Jordan Hale', 'landtech-extras-for-elementor' ),
				'author_role' => __( 'Shop owner', 'landtech-extras-for-elementor' ),
				'rating'      => '5',
				'content'     => __( 'WooCommerce reviews pulled in without extra plugins. The carousel is readable on a phone.', 'landtech-extras-for-elementor' ),
				'date'        => '',
			),
			array(
				'author_name' => __( 'Priya Shah', 'landtech-extras-for-elementor' ),
				'author_role' => __( 'Designer', 'landtech-extras-for-elementor' ),
				'rating'      => '4',
				'content'     => __( 'The quote skin is what I wanted for a case-study page. Schema toggle is a nice extra.', 'landtech-extras-for-elementor' ),
				'date'        => '',
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = Review_Source::collect( $settings );
		if ( empty( $items ) ) {
			echo '<p class="ltxe-testimonials__empty">' . esc_html__( 'Add reviews to the Testimonials widget.', 'landtech-extras-for-elementor' ) . '</p>';
			return;
		}

		$skin    = isset( $settings['skin'] ) ? sanitize_key( (string) $settings['skin'] ) : 'cards';
		$display = isset( $settings['display'] ) ? sanitize_key( (string) $settings['display'] ) : 'carousel';
		$allowed_skins = array( 'cards', 'bubbles', 'minimal', 'quote', 'masonry' );
		if ( ! in_array( $skin, $allowed_skins, true ) ) {
			$skin = 'cards';
		}
		if ( ! in_array( $display, array( 'carousel', 'grid', 'single' ), true ) ) {
			$display = 'carousel';
		}
		if ( 'masonry' === $skin && 'carousel' === $display ) {
			$display = 'grid';
		}
		if ( 'single' === $display ) {
			$items = array_slice( $items, 0, 1 );
		}

		$show_avatar = isset( $settings['show_avatar'] ) && 'yes' === $settings['show_avatar'];
		$show_rating = isset( $settings['show_rating'] ) && 'yes' === $settings['show_rating'];
		$show_date   = isset( $settings['show_date'] ) && 'yes' === $settings['show_date'];
		$show_source = isset( $settings['show_source'] ) && 'yes' === $settings['show_source'];

		$config = array(
			'display'  => $display,
			'autoplay' => isset( $settings['autoplay'] ) && 'yes' === $settings['autoplay'],
			'speed'    => isset( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000,
			'loop'     => isset( $settings['loop'] ) && 'yes' === $settings['loop'],
		);

		$classes = array(
			'ltxe-testimonials',
			'ltxe-testimonials--' . $skin,
			'ltxe-testimonials--' . $display,
		);

		$role = 'carousel' === $display ? 'region' : 'list';

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-ltxe-testimonials="' . esc_attr( (string) wp_json_encode( $config ) ) . '" data-slide-label="';
		/* translators: %s: testimonial slide number. */
		echo esc_attr__( 'Review %s', 'landtech-extras-for-elementor' );
		echo '" role="' . esc_attr( $role ) . '"';
		if ( 'carousel' === $display ) {
			echo ' aria-roledescription="carousel" aria-label="' . esc_attr__( 'Testimonials', 'landtech-extras-for-elementor' ) . '"';
		}
		echo '>';

		if ( 'carousel' === $display ) {
			echo '<div class="ltxe-testimonials__viewport">';
			echo '<div class="ltxe-testimonials__track">';
		} else {
			echo '<div class="ltxe-testimonials__grid">';
		}

		$index = 0;
		foreach ( $items as $item ) {
			$this->render_item( $item, $index, $show_avatar, $show_rating, $show_date, $show_source, $display );
			$index++;
		}

		echo '</div>';
		if ( 'carousel' === $display ) {
			echo '</div>';
			$show_arrows = isset( $settings['show_arrows'] ) && 'yes' === $settings['show_arrows'];
			if ( $show_arrows || ! empty( $config['autoplay'] ) ) {
				echo '<div class="ltxe-testimonials__nav">';
				if ( $show_arrows ) {
					echo '<button type="button" class="ltxe-testimonials__btn ltxe-testimonials__btn--prev" aria-label="' . esc_attr__( 'Previous review', 'landtech-extras-for-elementor' ) . '">' . esc_html__( 'Previous', 'landtech-extras-for-elementor' ) . '</button>';
					echo '<button type="button" class="ltxe-testimonials__btn ltxe-testimonials__btn--next" aria-label="' . esc_attr__( 'Next review', 'landtech-extras-for-elementor' ) . '">' . esc_html__( 'Next', 'landtech-extras-for-elementor' ) . '</button>';
				}
				if ( ! empty( $config['autoplay'] ) ) {
					echo '<button type="button" class="ltxe-testimonials__pause" aria-pressed="false" data-pause="' . esc_attr__( 'Pause', 'landtech-extras-for-elementor' ) . '" data-play="' . esc_attr__( 'Play', 'landtech-extras-for-elementor' ) . '" data-pause-label="' . esc_attr__( 'Pause carousel', 'landtech-extras-for-elementor' ) . '" data-play-label="' . esc_attr__( 'Play carousel', 'landtech-extras-for-elementor' ) . '" aria-label="' . esc_attr__( 'Pause carousel', 'landtech-extras-for-elementor' ) . '">' . esc_html__( 'Pause', 'landtech-extras-for-elementor' ) . '</button>';
				}
				echo '</div>';
			}
			if ( isset( $settings['show_dots'] ) && 'yes' === $settings['show_dots'] ) {
				echo '<div class="ltxe-testimonials__dots" role="tablist" aria-label="' . esc_attr__( 'Review slides', 'landtech-extras-for-elementor' ) . '"></div>';
			}
			echo '<p class="ltxe-testimonials__status" aria-live="polite"></p>';
		}
		echo '</div>';

		if ( isset( $settings['add_schema'] ) && 'yes' === $settings['add_schema'] ) {
			$this->render_schema( $settings, $items );
		}
	}

	/**
	 * @param array<string,mixed> $item        Review.
	 * @param int                 $index       Index.
	 * @param bool                $show_avatar Avatar.
	 * @param bool                $show_rating Rating.
	 * @param bool                $show_date   Date.
	 * @param bool                $show_source Source.
	 * @param string              $display     Display mode.
	 * @return void
	 */
	private function render_item( $item, $index, $show_avatar, $show_rating, $show_date, $show_source, $display ) {
		$item_role = 'carousel' === $display ? 'group' : 'listitem';
		echo '<article class="ltxe-testimonial" role="' . esc_attr( $item_role ) . '" data-index="' . esc_attr( (string) $index ) . '">';

		if ( $show_rating && ! empty( $item['rating'] ) ) {
			$full = (int) floor( (float) $item['rating'] );
			echo '<p class="ltxe-testimonial__stars" aria-label="' . esc_attr( sprintf( /* translators: %s: rating value */ __( 'Rated %s out of 5', 'landtech-extras-for-elementor' ), $item['rating'] ) ) . '">';
			for ( $i = 0; $i < 5; $i++ ) {
				echo '<span aria-hidden="true">' . ( $i < $full ? '★' : '☆' ) . '</span>';
			}
			echo '</p>';
		}

		if ( ! empty( $item['content'] ) ) {
			echo '<div class="ltxe-testimonial__content">';
			echo wp_kses_post( wpautop( $item['content'] ) );
			echo '</div>';
		}

		echo '<footer class="ltxe-testimonial__meta">';
		if ( $show_avatar && ! empty( $item['avatar'] ) ) {
			echo '<img class="ltxe-testimonial__avatar" src="' . esc_url( $item['avatar'] ) . '" alt="" width="56" height="56" />';
		}
		echo '<div class="ltxe-testimonial__who">';
		if ( ! empty( $item['author'] ) ) {
			echo '<cite class="ltxe-testimonial__author">' . esc_html( $item['author'] ) . '</cite>';
		}
		if ( ! empty( $item['role'] ) ) {
			echo '<span class="ltxe-testimonial__role">' . esc_html( $item['role'] ) . '</span>';
		}
		if ( $show_date && ! empty( $item['date'] ) ) {
			echo '<time class="ltxe-testimonial__date">' . esc_html( $item['date'] ) . '</time>';
		}
		if ( $show_source && ! empty( $item['source'] ) ) {
			$label = $this->source_label( $item['source'] );
			if ( ! empty( $item['source_url'] ) ) {
				echo '<a class="ltxe-testimonial__source" href="' . esc_url( $item['source_url'] ) . '">' . esc_html( $label ) . '</a>';
			} else {
				echo '<span class="ltxe-testimonial__source">' . esc_html( $label ) . '</span>';
			}
		}
		echo '</div>';
		echo '</footer>';
		echo '</article>';
	}

	/**
	 * @param string $source Source key.
	 * @return string
	 */
	private function source_label( $source ) {
		if ( 'woocommerce' === $source ) {
			return __( 'Verified purchase', 'landtech-extras-for-elementor' );
		}
		if ( 'comments' === $source ) {
			return __( 'Comment', 'landtech-extras-for-elementor' );
		}
		return __( 'Review', 'landtech-extras-for-elementor' );
	}

	/**
	 * @param array<string,mixed>            $settings Settings.
	 * @param array<int,array<string,mixed>> $items    Reviews.
	 * @return void
	 */
	private function render_schema( $settings, $items ) {
		$agg  = Review_Source::aggregate( $items );
		$type = isset( $settings['schema_type'] ) ? sanitize_text_field( (string) $settings['schema_type'] ) : 'Organization';
		if ( ! in_array( $type, array( 'Organization', 'LocalBusiness', 'Product' ), true ) ) {
			$type = 'Organization';
		}
		$name = isset( $settings['schema_name'] ) ? sanitize_text_field( (string) $settings['schema_name'] ) : '';
		if ( '' === $name ) {
			$name = wp_strip_all_tags( get_bloginfo( 'name' ) );
		}

		$reviews = array();
		foreach ( $items as $item ) {
			$reviews[] = array(
				'@type'        => 'Review',
				'author'       => array(
					'@type' => 'Person',
					'name'  => $item['author'],
				),
				'reviewBody'   => wp_strip_all_tags( $item['content'] ),
				'reviewRating' => array(
					'@type'       => 'Rating',
					'ratingValue' => $item['rating'],
					'bestRating'  => 5,
				),
			);
		}

		$schema = array(
			'@context'        => 'https://schema.org',
			'@type'           => $type,
			'name'            => $name,
			'aggregateRating' => array(
				'@type'       => 'AggregateRating',
				'ratingValue' => $agg['average'],
				'reviewCount' => $agg['count'],
				'bestRating'  => 5,
			),
			'review'          => $reviews,
		);

		$json = wp_json_encode( $schema );
		if ( ! is_string( $json ) || '' === $json ) {
			return;
		}
		wp_print_inline_script_tag( $json, array( 'type' => 'application/ld+json' ) );
	}
}
