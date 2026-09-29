<?php
namespace LandTechExtras\Modules\CookieConsent\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * GDPR/CCPA consent UI. Stores choice in localStorage only.
 *
 * @since 2.9.0
 */
class Cookie_Consent extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-cookie-consent';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Cookie Consent', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-check-circle';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'cookie', 'consent', 'gdpr', 'ccpa' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-cookie-consent' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-cookie-consent' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_consent',
			array(
				'label' => __( 'Consent', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'message',
			array(
				'label'   => __( 'Message', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'We use cookies to improve your experience. You can accept all cookies or decline non-essential ones.', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'policy_text',
			array(
				'label'   => __( 'Policy Link Text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Privacy Policy', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'policy_url',
			array(
				'label' => __( 'Policy URL', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'accept_label',
			array(
				'label'   => __( 'Accept Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Accept', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'decline_label',
			array(
				'label'   => __( 'Decline Label', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Decline', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'show_decline',
			array(
				'label'        => __( 'Show Decline Button', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'granular',
			array(
				'label'        => __( 'Granular Categories', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'position',
			array(
				'label'   => __( 'Position', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => array(
					'bottom'       => __( 'Bottom Bar', 'landtech-extras-for-elementor' ),
					'top'          => __( 'Top Bar', 'landtech-extras-for-elementor' ),
					'bottom-left'  => __( 'Bottom Left', 'landtech-extras-for-elementor' ),
					'bottom-right' => __( 'Bottom Right', 'landtech-extras-for-elementor' ),
					'modal'        => __( 'Center Modal', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'consent_version',
			array(
				'label'   => __( 'Consent Version', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 1,
			)
		);

		$this->add_control(
			'expiry_days',
			array(
				'label'   => __( 'Expiry (days)', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 365,
				'min'     => 1,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$pos      = isset( $settings['position'] ) ? sanitize_key( (string) $settings['position'] ) : 'bottom';
		$allowed  = array( 'bottom', 'top', 'bottom-left', 'bottom-right', 'modal' );
		if ( ! in_array( $pos, $allowed, true ) ) {
			$pos = 'bottom';
		}
		$config = wp_json_encode(
			array(
				'version'     => isset( $settings['consent_version'] ) ? absint( $settings['consent_version'] ) : 1,
				'expiry_days' => isset( $settings['expiry_days'] ) ? absint( $settings['expiry_days'] ) : 365,
				'categories'  => array( 'analytics', 'marketing' ),
			)
		);
		$policy_url = ( ! empty( $settings['policy_url']['url'] ) ) ? esc_url_raw( (string) $settings['policy_url']['url'] ) : '';

		echo '<div class="ltxe-cookie-consent ltxe-cookie-consent--' . esc_attr( $pos ) . '" hidden data-ltxe-consent="' . esc_attr( $config ) . '" role="dialog" aria-live="polite">';
		echo '<p class="ltxe-cookie-consent__msg">' . esc_html( isset( $settings['message'] ) ? (string) $settings['message'] : '' ) . '</p>';
		if ( '' !== $policy_url && ! empty( $settings['policy_text'] ) ) {
			echo '<p class="ltxe-cookie-consent__policy"><a href="' . esc_url( $policy_url ) . '">' . esc_html( (string) $settings['policy_text'] ) . '</a></p>';
		}
		if ( isset( $settings['granular'] ) && 'yes' === $settings['granular'] ) {
			echo '<div class="ltxe-cookie-consent__cats">';
			echo '<label><input type="checkbox" class="ltxe-consent-cat" value="analytics" checked /> ' . esc_html__( 'Analytics', 'landtech-extras-for-elementor' ) . '</label>';
			echo '<label><input type="checkbox" class="ltxe-consent-cat" value="marketing" /> ' . esc_html__( 'Marketing', 'landtech-extras-for-elementor' ) . '</label>';
			echo '</div>';
		}
		echo '<div class="ltxe-cookie-consent__actions">';
		echo '<button type="button" class="ltxe-consent-accept">' . esc_html( isset( $settings['accept_label'] ) ? (string) $settings['accept_label'] : __( 'Accept', 'landtech-extras-for-elementor' ) ) . '</button>';
		if ( isset( $settings['show_decline'] ) && 'yes' === $settings['show_decline'] ) {
			echo '<button type="button" class="ltxe-consent-decline">' . esc_html( isset( $settings['decline_label'] ) ? (string) $settings['decline_label'] : __( 'Decline', 'landtech-extras-for-elementor' ) ) . '</button>';
		}
		echo '</div></div>';
	}
}
