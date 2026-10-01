<?php
/**
 * Newsletter signup widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\NewsletterSignup\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.1.0
 */
class Newsletter_Signup extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-newsletter-signup';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Newsletter Signup', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-email-field';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'newsletter', 'mailchimp', 'subscribe', 'email' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-newsletter-signup' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-newsletter-signup' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_form',
			array(
				'label' => __( 'Form', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'audience_id',
			array(
				'label'       => __( 'Mailchimp audience ID', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'API key is stored under Elementor → LandTech Extras → APIs. It is never printed on the frontend.', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'show_first',
			array(
				'label'        => __( 'First name', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'show_last',
			array(
				'label'        => __( 'Last name', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'show_phone',
			array(
				'label'        => __( 'Phone', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'show_gdpr',
			array(
				'label'        => __( 'GDPR consent', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'gdpr_text',
			array(
				'label'   => __( 'Consent text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'I agree to receive email updates.', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'gdpr_required',
			array(
				'label'        => __( 'Consent required', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'double_optin',
			array(
				'label'        => __( 'Double opt-in', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Subscribe', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'success_msg',
			array(
				'label'   => __( 'Success message', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Thanks — check your inbox to confirm.', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'error_msg',
			array(
				'label'   => __( 'Error message', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Unable to subscribe. Please try again.', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => array(
					'inline'  => __( 'Inline', 'landtech-extras-for-elementor' ),
					'stacked' => __( 'Stacked', 'landtech-extras-for-elementor' ),
					'card'    => __( 'Card', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$s    = $this->get_settings_for_display();
		$rest = rest_url( 'landtech-extras/v1/newsletter/subscribe' );
		$cfg  = array(
			'rest'        => $rest,
			'nonce'       => wp_create_nonce( 'wp_rest' ),
			'audience'    => isset( $s['audience_id'] ) ? sanitize_text_field( (string) $s['audience_id'] ) : '',
			'doubleOptin' => isset( $s['double_optin'] ) && 'yes' === $s['double_optin'],
			'success'     => isset( $s['success_msg'] ) ? (string) $s['success_msg'] : '',
			'error'       => isset( $s['error_msg'] ) ? (string) $s['error_msg'] : '',
		);
		$layout = isset( $s['layout'] ) ? sanitize_key( (string) $s['layout'] ) : 'inline';
		echo '<form class="ltxe-news ltxe-news--' . esc_attr( $layout ) . '" data-ltxe-news="' . esc_attr( wp_json_encode( $cfg ) ) . '" novalidate>';
		if ( isset( $s['show_first'] ) && 'yes' === $s['show_first'] ) {
			echo '<label class="ltxe-news__field"><span class="screen-reader-text">' . esc_html__( 'First name', 'landtech-extras-for-elementor' ) . '</span>';
			echo '<input type="text" name="first_name" autocomplete="given-name" placeholder="' . esc_attr__( 'First name', 'landtech-extras-for-elementor' ) . '" /></label>';
		}
		if ( isset( $s['show_last'] ) && 'yes' === $s['show_last'] ) {
			echo '<label class="ltxe-news__field"><span class="screen-reader-text">' . esc_html__( 'Last name', 'landtech-extras-for-elementor' ) . '</span>';
			echo '<input type="text" name="last_name" autocomplete="family-name" placeholder="' . esc_attr__( 'Last name', 'landtech-extras-for-elementor' ) . '" /></label>';
		}
		echo '<label class="ltxe-news__field"><span class="screen-reader-text">' . esc_html__( 'Email', 'landtech-extras-for-elementor' ) . '</span>';
		echo '<input type="email" name="email" required autocomplete="email" placeholder="' . esc_attr__( 'Email address', 'landtech-extras-for-elementor' ) . '" /></label>';
		if ( isset( $s['show_phone'] ) && 'yes' === $s['show_phone'] ) {
			echo '<label class="ltxe-news__field"><span class="screen-reader-text">' . esc_html__( 'Phone', 'landtech-extras-for-elementor' ) . '</span>';
			echo '<input type="tel" name="phone" autocomplete="tel" placeholder="' . esc_attr__( 'Phone', 'landtech-extras-for-elementor' ) . '" /></label>';
		}
		if ( isset( $s['show_gdpr'] ) && 'yes' === $s['show_gdpr'] ) {
			$req = isset( $s['gdpr_required'] ) && 'yes' === $s['gdpr_required'];
			echo '<label class="ltxe-news__gdpr"><input type="checkbox" name="gdpr_consent" value="1"' . ( $req ? ' required' : '' ) . ' /> ';
			echo esc_html( isset( $s['gdpr_text'] ) ? (string) $s['gdpr_text'] : '' );
			echo '</label>';
		}
		$btn = isset( $s['button_text'] ) ? (string) $s['button_text'] : __( 'Subscribe', 'landtech-extras-for-elementor' );
		echo '<button type="submit" class="ltxe-news__btn">' . esc_html( $btn ) . '</button>';
		echo '<p class="ltxe-news__msg" role="status" hidden></p>';
		echo '</form>';
	}
}
