<?php
namespace LandTechExtras\Modules\PricingTable\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pricing table card.
 *
 * @since 2.8.0
 */
class Pricing_Table extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-pricing-table';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Pricing Table', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-price-table';
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-pricing-table' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-pricing-table' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_header',
			array(
				'label' => __( 'Header', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'plan_name',
			array(
				'label'   => __( 'Plan Name', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Starter', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'plan_description',
			array(
				'label'   => __( 'Description', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'show_badge',
			array(
				'label'        => __( 'Badge', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'badge_label',
			array(
				'label'     => __( 'Badge Label', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Most Popular', 'landtech-extras-for-elementor' ),
				'condition' => array(
					'show_badge' => 'yes',
				),
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label' => __( 'Icon', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::ICONS,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pricing',
			array(
				'label' => __( 'Pricing', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'currency',
			array(
				'label'   => __( 'Currency', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '$',
			)
		);

		$this->add_control(
			'currency_position',
			array(
				'label'   => __( 'Currency Position', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'before',
				'options' => array(
					'before' => __( 'Before', 'landtech-extras-for-elementor' ),
					'after'  => __( 'After', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'price',
			array(
				'label'   => __( 'Price', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '29',
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'period',
			array(
				'label'   => __( 'Period', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '/month', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'original_price',
			array(
				'label'   => __( 'Original Price', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'annual_price',
			array(
				'label'   => __( 'Annual Price', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'annual_period',
			array(
				'label'   => __( 'Annual Period', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( '/year', 'landtech-extras-for-elementor' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_features',
			array(
				'label' => __( 'Features', 'landtech-extras-for-elementor' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'feature_text',
			array(
				'label'   => __( 'Feature', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Feature', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'status',
			array(
				'label'   => __( 'Status', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'included',
				'options' => array(
					'included'  => __( 'Included', 'landtech-extras-for-elementor' ),
					'excluded'  => __( 'Excluded', 'landtech-extras-for-elementor' ),
					'highlight' => __( 'Highlight', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$repeater->add_control(
			'tooltip',
			array(
				'label' => __( 'Tooltip', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'features',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ feature_text }}}',
				'default'     => array(
					array(
						'feature_text' => __( 'Feature one', 'landtech-extras-for-elementor' ),
						'status'       => 'included',
					),
					array(
						'feature_text' => __( 'Feature two', 'landtech-extras-for-elementor' ),
						'status'       => 'included',
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cta',
			array(
				'label' => __( 'Call to Action', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button Text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Get Started', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'button_url',
			array(
				'label' => __( 'Button URL', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::URL,
			)
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$this->add_control(
				'woo_product_id',
				array(
					'label'       => __( 'WooCommerce Product ID', 'landtech-extras-for-elementor' ),
					'type'        => Controls_Manager::NUMBER,
					'description' => __( 'When set, the button adds this product to the cart.', 'landtech-extras-for-elementor' ),
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Style', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'featured',
			array(
				'label'        => __( 'Highlighted Plan', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typo',
				'selector' => '{{WRAPPER}} .ltxe-pricing-table__name',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .ltxe-pricing-table',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .ltxe-pricing-table',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$classes  = array( 'ltxe-pricing-table' );
		if ( isset( $settings['featured'] ) && 'yes' === $settings['featured'] ) {
			$classes[] = 'ltxe-pricing-table--featured';
		}

		$currency = isset( $settings['currency'] ) ? (string) $settings['currency'] : '';
		$before   = ! isset( $settings['currency_position'] ) || 'after' !== $settings['currency_position'];

		echo '<article class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		if ( isset( $settings['show_badge'] ) && 'yes' === $settings['show_badge'] && ! empty( $settings['badge_label'] ) ) {
			echo '<span class="ltxe-pricing-table__badge">' . esc_html( (string) $settings['badge_label'] ) . '</span>';
		}

		if ( ! empty( $settings['selected_icon']['value'] ) ) {
			echo '<div class="ltxe-pricing-table__icon">';
			Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) );
			echo '</div>';
		}

		echo '<h3 class="ltxe-pricing-table__name">' . esc_html( (string) ( $settings['plan_name'] ?? '' ) ) . '</h3>';
		if ( ! empty( $settings['plan_description'] ) ) {
			echo '<p class="ltxe-pricing-table__desc">' . esc_html( (string) $settings['plan_description'] ) . '</p>';
		}

		echo '<div class="ltxe-pricing-table__price ltxe-pricing-table__price--monthly">';
		$this->render_price_block( $currency, $before, $settings['price'] ?? '', $settings['period'] ?? '', $settings['original_price'] ?? '' );
		echo '</div>';

		if ( ! empty( $settings['annual_price'] ) ) {
			echo '<div class="ltxe-pricing-table__price ltxe-pricing-table__price--annual" hidden>';
			$this->render_price_block( $currency, $before, $settings['annual_price'], $settings['annual_period'] ?? '', '' );
			echo '</div>';
		}

		$features = isset( $settings['features'] ) && is_array( $settings['features'] ) ? $settings['features'] : array();
		if ( ! empty( $features ) ) {
			echo '<ul class="ltxe-pricing-table__features">';
			foreach ( $features as $row ) {
				$status = isset( $row['status'] ) ? sanitize_key( $row['status'] ) : 'included';
				echo '<li class="ltxe-pricing-table__feature ltxe-pricing-table__feature--' . esc_attr( $status ) . '"';
				if ( ! empty( $row['tooltip'] ) ) {
					echo ' title="' . esc_attr( (string) $row['tooltip'] ) . '"';
				}
				echo '>';
				echo esc_html( (string) ( $row['feature_text'] ?? '' ) );
				echo '</li>';
			}
			echo '</ul>';
		}

		$btn_text = isset( $settings['button_text'] ) ? (string) $settings['button_text'] : '';
		$woo_id   = isset( $settings['woo_product_id'] ) ? absint( $settings['woo_product_id'] ) : 0;
		if ( $woo_id && class_exists( 'WooCommerce' ) && function_exists( 'wc_get_cart_url' ) ) {
			$url = add_query_arg( 'add-to-cart', $woo_id, wc_get_cart_url() );
			echo '<a class="ltxe-pricing-table__cta" href="' . esc_url( $url ) . '">' . esc_html( $btn_text ) . '</a>';
		} elseif ( ! empty( $settings['button_url']['url'] ) ) {
			echo '<a class="ltxe-pricing-table__cta" href="' . esc_url( $settings['button_url']['url'] ) . '"';
			if ( ! empty( $settings['button_url']['is_external'] ) ) {
				echo ' target="_blank" rel="noopener noreferrer"';
			}
			if ( ! empty( $settings['button_url']['nofollow'] ) ) {
				echo ' rel="nofollow"';
			}
			echo '>' . esc_html( $btn_text ) . '</a>';
		}

		echo '</article>';
	}

	/**
	 * @param string $currency Currency symbol.
	 * @param bool   $before   Whether the symbol is before the amount.
	 * @param string $price    Price text.
	 * @param string $period   Period label.
	 * @param string $original Struck-through original price.
	 * @return void
	 */
	protected function render_price_block( $currency, $before, $price, $period, $original ) {
		if ( '' !== $original ) {
			echo '<s class="ltxe-pricing-table__original">';
			if ( $before ) {
				echo esc_html( $currency );
			}
			echo esc_html( (string) $original );
			if ( ! $before ) {
				echo esc_html( $currency );
			}
			echo '</s> ';
		}
		echo '<span class="ltxe-pricing-table__amount">';
		if ( $before ) {
			echo esc_html( $currency );
		}
		echo esc_html( (string) $price );
		if ( ! $before ) {
			echo esc_html( $currency );
		}
		echo '</span>';
		if ( '' !== $period ) {
			echo '<span class="ltxe-pricing-table__period">' . esc_html( (string) $period ) . '</span>';
		}
	}
}
