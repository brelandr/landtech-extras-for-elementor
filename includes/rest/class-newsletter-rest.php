<?php
/**
 * Newsletter subscribe REST (Mailchimp).
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once LANDTECH_EXTRAS_PATH . 'modules/newsletter-signup/settings.php';

/**
 * Public subscribe route: writes only to Mailchimp with a stored site key.
 * Authentication is the WP REST nonce (`X-WP-Nonce`) so bots without a
 * page-issued nonce fail. The Mailchimp key is never returned.
 *
 * @return bool
 */
function landtech_extras_newsletter_rest_permission() {
	$nonce = '';
	if ( isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
		$nonce = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) );
	}
	if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
		return false;
	}
	return true;
}

/**
 * Register POST landtech-extras/v1/newsletter/subscribe.
 *
 * @return void
 */
function landtech_extras_register_newsletter_rest() {
	register_rest_route(
		'landtech-extras/v1',
		'/newsletter/subscribe',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'landtech_extras_newsletter_rest_callback',
			'permission_callback' => 'landtech_extras_newsletter_rest_permission',
			'args'                => array(
				'email'        => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_email',
				),
				'first_name'   => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'last_name'    => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'phone'        => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'audience_id'  => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'gdpr_consent' => array(
					'required' => false,
					'type'     => 'boolean',
				),
				'double_optin' => array(
					'required' => false,
					'type'     => 'boolean',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'landtech_extras_register_newsletter_rest' );

/**
 * Subscribe callback.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function landtech_extras_newsletter_rest_callback( $request ) {
	$email = $request->get_param( 'email' );
	if ( ! is_email( $email ) ) {
		return new WP_Error( 'ltxe_news_email', __( 'Please enter a valid email address.', 'landtech-extras-for-elementor' ), array( 'status' => 400 ) );
	}
	$audience = sanitize_text_field( (string) $request->get_param( 'audience_id' ) );
	if ( '' === $audience ) {
		return new WP_Error( 'ltxe_news_list', __( 'Audience ID is missing.', 'landtech-extras-for-elementor' ), array( 'status' => 400 ) );
	}
	$key = landtech_extras_newsletter_decrypt_key();
	if ( '' === $key ) {
		return new WP_Error( 'ltxe_news_key', __( 'Mailchimp is not configured.', 'landtech-extras-for-elementor' ), array( 'status' => 503 ) );
	}
	$dc = '';
	if ( preg_match( '/-([a-z0-9]+)$/', $key, $m ) ) {
		$dc = $m[1];
	}
	if ( '' === $dc ) {
		return new WP_Error( 'ltxe_news_dc', __( 'Invalid Mailchimp API key format.', 'landtech-extras-for-elementor' ), array( 'status' => 500 ) );
	}
	$status = $request->get_param( 'double_optin' ) ? 'pending' : 'subscribed';
	$body   = array(
		'email_address' => $email,
		'status'        => $status,
		'merge_fields'  => array(),
	);
	$first = $request->get_param( 'first_name' );
	$last  = $request->get_param( 'last_name' );
	$phone = $request->get_param( 'phone' );
	if ( is_string( $first ) && '' !== $first ) {
		$body['merge_fields']['FNAME'] = $first;
	}
	if ( is_string( $last ) && '' !== $last ) {
		$body['merge_fields']['LNAME'] = $last;
	}
	if ( is_string( $phone ) && '' !== $phone ) {
		$body['merge_fields']['PHONE'] = $phone;
	}
	if ( $request->get_param( 'gdpr_consent' ) ) {
		$body['tags'] = array( 'gdpr-consent' );
	}
	$url  = 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . rawurlencode( $audience ) . '/members';
	$resp = wp_remote_post(
		$url,
		array(
			'timeout' => 15,
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'anystring:' . $key ),
				'Content-Type'  => 'application/json',
			),
			'body'    => wp_json_encode( $body ),
		)
	);
	if ( is_wp_error( $resp ) ) {
		return new WP_Error( 'ltxe_news_http', $resp->get_error_message(), array( 'status' => 502 ) );
	}
	$code = (int) wp_remote_retrieve_response_code( $resp );
	if ( $code < 200 || $code >= 300 ) {
		return new WP_Error( 'ltxe_news_mc', __( 'Unable to subscribe. Please try again.', 'landtech-extras-for-elementor' ), array( 'status' => 400 ) );
	}
	return rest_ensure_response( array( 'subscribed' => true ) );
}
