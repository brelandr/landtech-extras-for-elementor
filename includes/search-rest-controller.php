<?php
/**
 * Public live search REST endpoint for Search Form widget.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register landtech-extras/v1/search.
 *
 * @return void
 */
function landtech_extras_register_search_rest_route() {
	register_rest_route(
		'landtech-extras/v1',
		'/search',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'landtech_extras_search_rest_callback',
			'permission_callback' => 'landtech_extras_search_rest_permission',
			'args'                => array(
				'q'         => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => function( $value ) {
						return is_string( $value ) && strlen( $value ) >= 2;
					},
				),
				'post_type' => array(
					'default'           => 'post',
					'sanitize_callback' => 'sanitize_key',
				),
				'per_page'  => array(
					'default'           => 8,
					'sanitize_callback' => 'absint',
					'validate_callback' => function( $value ) {
						return is_numeric( $value ) && (int) $value >= 1 && (int) $value <= 20;
					},
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'landtech_extras_register_search_rest_route' );

/**
 * Public read-only search — no private data exposed; rate limited per IP.
 *
 * @return bool
 */
function landtech_extras_search_rest_permission() {
	return true;
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function landtech_extras_search_rest_callback( WP_REST_Request $request ) {
	$query_text = sanitize_text_field( (string) $request->get_param( 'q' ) );
	$post_type  = sanitize_key( (string) $request->get_param( 'post_type' ) );
	$per_page   = min( 20, max( 1, absint( $request->get_param( 'per_page' ) ) ) );

	if ( strlen( $query_text ) < 2 ) {
		return new WP_Error( 'ltxe_search_short', __( 'Search query is too short.', 'landtech-extras-for-elementor' ), array( 'status' => 400 ) );
	}

	$allowed_types = array( 'post', 'page', 'product' );
	if ( ! in_array( $post_type, $allowed_types, true ) ) {
		$post_type = 'post';
	}
	if ( 'product' === $post_type && ! class_exists( 'WooCommerce' ) ) {
		$post_type = 'post';
	}

	$rate_key = 'ltxe_search_' . md5( landtech_extras_search_rest_client_ip() );
	$attempts = (int) get_transient( $rate_key );
	if ( $attempts > 60 ) {
		return new WP_Error( 'ltxe_search_rate', __( 'Too many search requests. Please try again later.', 'landtech-extras-for-elementor' ), array( 'status' => 429 ) );
	}
	set_transient( $rate_key, $attempts + 1, MINUTE_IN_SECONDS );

	$query = new WP_Query(
		array(
			's'              => $query_text,
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'no_found_rows'  => true,
		)
	);

	$results = array();
	foreach ( $query->posts as $post ) {
		if ( ! $post instanceof WP_Post ) {
			continue;
		}
		$results[] = array(
			'id'    => $post->ID,
			'title' => get_the_title( $post ),
			'url'   => get_permalink( $post ),
			'type'  => $post->post_type,
		);
	}

	return rest_ensure_response(
		array(
			'results' => $results,
		)
	);
}

/**
 * @return string
 */
function landtech_extras_search_rest_client_ip() {
	$ip = '';
	if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
	}
	return $ip ? $ip : '0.0.0.0';
}
