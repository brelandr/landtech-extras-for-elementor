<?php
namespace LandTechExtras\Modules\PricingTable\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Monthly / annual billing toggle.
 *
 * @since 2.8.0
 */
class Pricing_Toggle extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-pricing-toggle';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Pricing Toggle', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-dual-button';
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
			'section_toggle',
			array(
				'label' => __( 'Toggle', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'monthly_label',
			array(
				'label'   => __( 'Monthly Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Monthly', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'annual_label',
			array(
				'label'   => __( 'Annual Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Annual', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'default_period',
			array(
				'label'   => __( 'Default Period', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'monthly',
				'options' => array(
					'monthly' => __( 'Monthly', 'landtech-extras-for-elementor' ),
					'annual'  => __( 'Annual', 'landtech-extras-for-elementor' ),
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
		$period   = isset( $settings['default_period'] ) && 'annual' === $settings['default_period'] ? 'annual' : 'monthly';

		echo '<div class="ltxe-pricing-toggle" data-period="' . esc_attr( $period ) . '">';
		echo '<button type="button" class="ltxe-pricing-toggle__btn' . ( 'monthly' === $period ? ' is-active' : '' ) . '" data-period="monthly">';
		echo esc_html( (string) ( $settings['monthly_label'] ?? '' ) );
		echo '</button>';
		echo '<button type="button" class="ltxe-pricing-toggle__btn' . ( 'annual' === $period ? ' is-active' : '' ) . '" data-period="annual">';
		echo esc_html( (string) ( $settings['annual_label'] ?? '' ) );
		echo '</button>';
		echo '</div>';
	}
}
