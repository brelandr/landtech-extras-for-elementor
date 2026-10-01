<?php
/**
 * Public shape-divider library (read-only SVG markup).
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public on purpose: returns only bundled SVG path markup for the
 * shape-divider library. No credentials, user data, or writes.
 *
 * @return bool
 */
function landtech_extras_shapes_rest_permission() {
	return true;
}

/**
 * Register GET landtech-extras/v1/shapes.
 *
 * @return void
 */
function landtech_extras_register_shapes_rest() {
	register_rest_route(
		'landtech-extras/v1',
		'/shapes',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'landtech_extras_shapes_rest_callback',
			'permission_callback' => 'landtech_extras_shapes_rest_permission',
		)
	);
}
add_action( 'rest_api_init', 'landtech_extras_register_shapes_rest' );

/**
 * List shapes.
 *
 * @return WP_REST_Response
 */
function landtech_extras_shapes_rest_callback() {
	if ( ! class_exists( '\LandTechExtras\Modules\ShapeDividers\Shape_Dividers_Extension' ) ) {
		$path = LANDTECH_EXTRAS_PATH . 'modules/shape-dividers/extension.php';
		if ( is_readable( $path ) ) {
			require_once $path;
		}
	}
	$items = array();
	if ( class_exists( '\LandTechExtras\Modules\ShapeDividers\Shape_Dividers_Extension' ) ) {
		foreach ( \LandTechExtras\Modules\ShapeDividers\Shape_Dividers_Extension::path_map() as $slug => $path_d ) {
			unset( $path_d );
			$items[] = array(
				'slug' => $slug,
				'svg'  => \LandTechExtras\Modules\ShapeDividers\Shape_Dividers_Extension::svg_for( $slug, '#0b1020' ),
			);
		}
	}
	return rest_ensure_response( $items );
}
