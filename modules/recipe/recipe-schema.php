<?php
/**
 * Recipe schema helpers.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\Recipe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ISO-8601 duration and Recipe JSON-LD.
 *
 * @since 3.1.0
 */
class Recipe_Schema {

	/**
	 * Format minutes or hours as ISO-8601 duration.
	 *
	 * @param int    $value Quantity.
	 * @param string $unit  minutes|hours.
	 * @return string
	 */
	public static function iso_duration( $value, $unit ) {
		$value = absint( $value );
		$unit  = strtolower( (string) $unit );
		if ( 'hours' === $unit ) {
			return 'PT' . $value . 'H';
		}
		return 'PT' . $value . 'M';
	}

	/**
	 * Build a Schema.org Recipe graph.
	 *
	 * @param array $args Recipe fields.
	 * @return array
	 */
	public static function build( $args ) {
		$args = is_array( $args ) ? $args : array();
		$name = isset( $args['name'] ) ? (string) $args['name'] : '';
		$out  = array(
			'@context' => 'https://schema.org/',
			'@type'    => 'Recipe',
			'name'     => $name,
		);
		if ( ! empty( $args['description'] ) ) {
			$out['description'] = (string) $args['description'];
		}
		if ( ! empty( $args['author'] ) ) {
			$out['author'] = array(
				'@type' => 'Person',
				'name'  => (string) $args['author'],
			);
		}
		if ( ! empty( $args['image'] ) ) {
			$out['image'] = (string) $args['image'];
		}
		foreach ( array( 'prepTime', 'cookTime', 'totalTime' ) as $key ) {
			if ( ! empty( $args[ $key ] ) ) {
				$out[ $key ] = (string) $args[ $key ];
			}
		}
		if ( isset( $args['recipeYield'] ) && '' !== (string) $args['recipeYield'] ) {
			$out['recipeYield'] = (string) $args['recipeYield'];
		}
		if ( ! empty( $args['recipeCategory'] ) ) {
			$out['recipeCategory'] = (string) $args['recipeCategory'];
		}
		if ( ! empty( $args['recipeCuisine'] ) ) {
			$out['recipeCuisine'] = (string) $args['recipeCuisine'];
		}
		if ( ! empty( $args['keywords'] ) ) {
			$out['keywords'] = (string) $args['keywords'];
		}
		if ( ! empty( $args['recipeIngredient'] ) && is_array( $args['recipeIngredient'] ) ) {
			$out['recipeIngredient'] = array_values( $args['recipeIngredient'] );
		}
		if ( ! empty( $args['recipeInstructions'] ) && is_array( $args['recipeInstructions'] ) ) {
			$out['recipeInstructions'] = array_values( $args['recipeInstructions'] );
		}
		if ( ! empty( $args['nutrition'] ) && is_array( $args['nutrition'] ) ) {
			$out['nutrition'] = array_merge(
				array( '@type' => 'NutritionInformation' ),
				$args['nutrition']
			);
		}
		if ( isset( $args['rating'] ) && (float) $args['rating'] > 0 ) {
			$out['aggregateRating'] = array(
				'@type'       => 'AggregateRating',
				'ratingValue' => (string) $args['rating'],
				'bestRating'  => '5',
				'worstRating' => '0',
				'ratingCount' => '1',
			);
		}
		return $out;
	}
}
