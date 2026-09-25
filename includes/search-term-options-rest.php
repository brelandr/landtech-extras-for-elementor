<?php
/**
 * Editor AJAX term options for Search Form Restrictions & Filters controls.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register landtech-extras/v1/taxonomy-terms.
 *
 * @return void
 */
function landtech_extras_register_search_term_options_route() {
	register_rest_route(
		'landtech-extras/v1',
		'/taxonomy-terms',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'landtech_extras_search_term_options_rest_callback',
			'permission_callback' => 'landtech_extras_search_term_options_rest_permission',
			'args'                => array(
				'taxonomy' => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => 'landtech_extras_search_term_options_validate_taxonomy',
				),
				'q'        => array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'slugs'    => array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'landtech_extras_register_search_term_options_route' );

/**
 * @return bool
 */
function landtech_extras_search_term_options_rest_permission() {
	return current_user_can( 'edit_posts' );
}

/**
 * @param string $taxonomy Taxonomy slug.
 * @return bool
 */
function landtech_extras_search_term_options_validate_taxonomy( $taxonomy ) {
	if ( ! is_string( $taxonomy ) || '' === $taxonomy ) {
		return false;
	}
	return taxonomy_exists( $taxonomy );
}

/**
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function landtech_extras_search_term_options_rest_callback( WP_REST_Request $request ) {
	$taxonomy = sanitize_key( (string) $request->get_param( 'taxonomy' ) );
	$search   = sanitize_text_field( (string) $request->get_param( 'q' ) );
	if ( '' === $search ) {
		$search = sanitize_text_field( (string) $request->get_param( 'term' ) );
	}
	$slugs    = sanitize_text_field( (string) $request->get_param( 'slugs' ) );

	$tax_object = get_taxonomy( $taxonomy );
	if ( ! $tax_object || empty( $tax_object->publicly_queryable ) ) {
		return new WP_Error( 'ltxe_invalid_taxonomy', __( 'Invalid taxonomy.', 'landtech-extras-for-elementor' ), array( 'status' => 400 ) );
	}

	$results = array();

	if ( '' !== $slugs ) {
		$slug_list = array_filter( array_map( 'sanitize_title', explode( ',', $slugs ) ) );
		if ( ! empty( $slug_list ) ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'slug'       => $slug_list,
					'hide_empty' => false,
					'number'     => 100,
				)
			);
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$results[] = array(
						'id'   => $term->slug,
						'text' => $term->name,
					);
				}
			}
		}
	}

	if ( '' === $search && empty( $results ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'number'     => 50,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);
	} else {
		$args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'number'     => 50,
			'orderby'    => 'name',
			'order'      => 'ASC',
		);
		if ( '' !== $search ) {
			$args['search'] = $search;
		}
		$terms = get_terms( $args );
	}

	if ( is_wp_error( $terms ) ) {
		return rest_ensure_response( array( 'results' => $results ) );
	}

	$seen = array();
	foreach ( $results as $row ) {
		$seen[ $row['id'] ] = true;
	}

	foreach ( $terms as $term ) {
		if ( isset( $seen[ $term->slug ] ) ) {
			continue;
		}
		$results[] = array(
			'id'   => $term->slug,
			'text' => $term->name,
		);
	}

	return rest_ensure_response(
		array(
			'results' => $results,
		)
	);
}
