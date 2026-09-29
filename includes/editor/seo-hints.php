<?php
/**
 * Editor SEO hint assets (U35).
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue SEO hint panel in the Elementor editor only.
 *
 * @return void
 */
function landtech_extras_enqueue_seo_hints() {
	wp_register_style(
		'landtech-extras-seo-hints',
		plugins_url( '/assets/css/editor/seo-hints.css', LANDTECH_EXTRAS__FILE__ ),
		array(),
		LANDTECH_EXTRAS_VERSION
	);
	wp_enqueue_style( 'landtech-extras-seo-hints' );

	wp_register_script(
		'landtech-extras-seo-hints',
		plugins_url( '/assets/js/editor/seo-hints.js', LANDTECH_EXTRAS__FILE__ ),
		array( 'elementor-editor' ),
		LANDTECH_EXTRAS_VERSION,
		true
	);
	wp_enqueue_script( 'landtech-extras-seo-hints' );
}
add_action( 'elementor/editor/after_enqueue_scripts', 'landtech_extras_enqueue_seo_hints' );
