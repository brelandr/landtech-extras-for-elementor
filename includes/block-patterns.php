<?php
/**
 * Block pattern registration (U31).
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register LandTech Extras patterns for the block inserter.
 *
 * @return void
 */
function landtech_extras_register_block_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category(
		'landtech-extras',
		array(
			'label' => __( 'LandTech Extras for Elementor', 'landtech-extras-for-elementor' ),
		)
	);

	$patterns = array(
		'hero-with-cta'         => __( 'Hero Section with CTA', 'landtech-extras-for-elementor' ),
		'three-column-features' => __( 'Three Column Feature Boxes', 'landtech-extras-for-elementor' ),
		'testimonials-grid'     => __( 'Testimonials Grid', 'landtech-extras-for-elementor' ),
		'pricing-table-row'     => __( 'Pricing Table — 3 Plans', 'landtech-extras-for-elementor' ),
		'team-members-row'      => __( 'Team Members — 4 Columns', 'landtech-extras-for-elementor' ),
		'faq-section'           => __( 'FAQ Section with Schema', 'landtech-extras-for-elementor' ),
	);

	foreach ( $patterns as $slug => $title ) {
		$file = LANDTECH_EXTRAS_PATH . 'patterns/' . $slug . '.php';
		if ( ! is_readable( $file ) ) {
			continue;
		}
		ob_start();
		include $file;
		$content = ob_get_clean();
		if ( ! is_string( $content ) || '' === trim( $content ) ) {
			continue;
		}
		register_block_pattern(
			'landtech-extras/' . $slug,
			array(
				'title'      => $title,
				'categories' => array( 'landtech-extras' ),
				'content'    => $content,
			)
		);
	}
}
add_action( 'init', 'landtech_extras_register_block_patterns' );
