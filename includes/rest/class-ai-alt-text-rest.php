<?php
/**
 * Single-image AI alt text via the WordPress AI Client.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the WordPress AI Client can run.
 *
 * @return bool
 */
function landtech_extras_ai_alt_is_available() {
	return function_exists( 'wp_ai_client_prompt' ) && function_exists( 'wp_supports_ai' ) && wp_supports_ai();
}

/**
 * Register landtech-extras/v1/ai/alt-text.
 *
 * @return void
 */
function landtech_extras_register_ai_alt_text_rest() {
	register_rest_route(
		'landtech-extras/v1',
		'/ai/alt-text',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'landtech_extras_ai_alt_text_callback',
			'permission_callback' => 'landtech_extras_ai_alt_text_permission',
			'args'                => array(
				'image_id' => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
					'validate_callback' => function ( $value ) {
						return is_numeric( $value ) && (int) $value > 0;
					},
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'landtech_extras_register_ai_alt_text_rest' );

/**
 * Editors only.
 *
 * @return bool
 */
function landtech_extras_ai_alt_text_permission() {
	return current_user_can( 'edit_posts' );
}

/**
 * Generate and store alt text for one attachment.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function landtech_extras_ai_alt_text_callback( WP_REST_Request $request ) {
	if ( ! landtech_extras_ai_alt_is_available() ) {
		return new WP_Error(
			'ltxe_ai_unavailable',
			__( 'The WordPress AI Client is not available. Configure a provider under Settings → Connectors.', 'landtech-extras-for-elementor' ),
			array( 'status' => 503 )
		);
	}

	$image_id = absint( $request->get_param( 'image_id' ) );
	if ( ! current_user_can( 'edit_post', $image_id ) ) {
		return new WP_Error(
			'ltxe_ai_forbidden',
			__( 'You cannot edit this image.', 'landtech-extras-for-elementor' ),
			array( 'status' => 403 )
		);
	}

	$url = wp_get_attachment_image_url( $image_id, 'large' );
	if ( ! $url ) {
		return new WP_Error(
			'ltxe_ai_invalid_image',
			__( 'Image not found.', 'landtech-extras-for-elementor' ),
			array( 'status' => 404 )
		);
	}

	$prompt = __( 'Write concise alt text for this image in under 125 characters. Describe what is shown without starting with "Image of" or "Photo of".', 'landtech-extras-for-elementor' );

	$result = wp_ai_client_prompt(
		array(
			'prompt' => $prompt . ' Image URL: ' . $url,
		)
	);

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	$alt = '';
	if ( is_string( $result ) ) {
		$alt = $result;
	} elseif ( is_array( $result ) ) {
		if ( isset( $result['text'] ) ) {
			$alt = (string) $result['text'];
		} elseif ( isset( $result['content'] ) ) {
			$alt = (string) $result['content'];
		}
	} elseif ( is_object( $result ) && isset( $result->text ) ) {
		$alt = (string) $result->text;
	}

	$alt = sanitize_text_field( trim( wp_strip_all_tags( $alt ) ) );
	if ( '' === $alt ) {
		return new WP_Error(
			'ltxe_ai_no_result',
			__( 'AI returned no alt text.', 'landtech-extras-for-elementor' ),
			array( 'status' => 500 )
		);
	}

	update_post_meta( $image_id, '_wp_attachment_image_alt', $alt );

	return rest_ensure_response(
		array(
			'alt_text' => $alt,
		)
	);
}
