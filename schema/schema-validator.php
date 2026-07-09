<?php
/**
 * Local Schema.org required-field validation (no remote API).
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.3.0
 */
final class Schema_Validator {

	/**
	 * Validate schema payload for a given @type.
	 *
	 * @param string               $type Schema @type.
	 * @param array<string, mixed> $data Schema graph.
	 * @return array{valid: bool, errors: string[], warnings: string[]}
	 */
	public static function validate( $type, $data ) {
		$type   = sanitize_text_field( (string) $type );
		$errors = array();
		$warnings = array();

		if ( ! is_array( $data ) ) {
			return array(
				'valid'    => false,
				'errors'   => array( __( 'Schema data must be an array.', 'landtech-extras-for-elementor' ) ),
				'warnings' => array(),
			);
		}

		switch ( $type ) {
			case 'FAQPage':
				if ( empty( $data['mainEntity'] ) || ! is_array( $data['mainEntity'] ) ) {
					$errors[] = __( 'FAQPage requires at least one Question in mainEntity.', 'landtech-extras-for-elementor' );
				}
				break;

			case 'BreadcrumbList':
				if ( empty( $data['itemListElement'] ) || ! is_array( $data['itemListElement'] ) ) {
					$errors[] = __( 'BreadcrumbList requires itemListElement.', 'landtech-extras-for-elementor' );
				}
				break;

			case 'LocalBusiness':
				foreach ( array( 'name', 'address' ) as $required ) {
					if ( empty( $data[ $required ] ) ) {
						$errors[] = sprintf(
							/* translators: %s: schema property name */
							__( '%s is recommended for LocalBusiness.', 'landtech-extras-for-elementor' ),
							$required
						);
					}
				}
				if ( empty( $data['telephone'] ) && empty( $data['url'] ) ) {
					$warnings[] = __( 'Add telephone or url for richer local results.', 'landtech-extras-for-elementor' );
				}
				break;

			case 'Organization':
				if ( empty( $data['name'] ) ) {
					$errors[] = __( 'Organization requires name.', 'landtech-extras-for-elementor' );
				}
				if ( empty( $data['url'] ) && empty( $data['logo'] ) ) {
					$warnings[] = __( 'Add a website URL or logo for Organization schema.', 'landtech-extras-for-elementor' );
				}
				break;

			case 'Event':
				if ( empty( $data['name'] ) ) {
					$errors[] = __( 'Event requires name.', 'landtech-extras-for-elementor' );
				}
				if ( empty( $data['startDate'] ) ) {
					$errors[] = __( 'Event requires startDate.', 'landtech-extras-for-elementor' );
				}
				if ( empty( $data['location'] ) ) {
					$warnings[] = __( 'Event location is recommended for rich results.', 'landtech-extras-for-elementor' );
				}
				if ( empty( $data['endDate'] ) ) {
					$warnings[] = __( 'Event endDate is recommended.', 'landtech-extras-for-elementor' );
				}
				break;

			case 'Product':
			case 'Service':
				if ( empty( $data['name'] ) ) {
					$errors[] = __( 'Product/Service requires name.', 'landtech-extras-for-elementor' );
				}
				break;

			case 'Review':
				if ( empty( $data['reviewRating'] ) ) {
					$warnings[] = __( 'Review should include reviewRating.', 'landtech-extras-for-elementor' );
				}
				break;

			case 'HowTo':
				if ( empty( $data['step'] ) || ! is_array( $data['step'] ) ) {
					$errors[] = __( 'HowTo requires at least one step.', 'landtech-extras-for-elementor' );
				}
				if ( empty( $data['name'] ) ) {
					$warnings[] = __( 'HowTo name is recommended.', 'landtech-extras-for-elementor' );
				}
				break;
		}

		return array(
			'valid'    => empty( $errors ),
			'errors'   => $errors,
			'warnings' => $warnings,
		);
	}
}
