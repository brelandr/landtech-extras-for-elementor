<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\QueryControl\Types;

// LandTech Extras for Elementor Classes
use LandTechExtras\Modules\QueryControl\Types\Type_Base;
use LandTechExtras\Utils;

// Elementor Classes
use Elementor\Core\Base\Document;
use Elementor\TemplateLibrary\Source_Local;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Types\Templates
 *
 * @since  2.2.0
 */
class Templates extends Type_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_name() {
		return 'templates';
	}

	/**
	 * Gets autocomplete values
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_autocomplete_values( array $data ) {
		$results = [];

		$document_types = Utils::elementor()->documents->get_document_types( [
			'show_in_library' => true,
		] );

		$query_params = [
			's' 				=> $data['q'],
			'post_type' 		=> Source_Local::CPT,
			'posts_per_page' 	=> -1,
			'orderby' 			=> 'meta_value',
			'order' 			=> 'ASC',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Template library autocomplete; narrow meta_query on Elementor document type key.
			'meta_query' => [
				[
					'key' 		=> Document::TYPE_META_KEY,
					'value' 	=> array_keys( $document_types ),
					'compare' 	=> 'IN',
				],
			],
		];

		$query = new \WP_Query( $query_params );

		foreach ( $query->posts as $post ) {
			$document = Utils::elementor()->documents->get( $post->ID );
			if ( ! $document )
				continue;

			$results[] = [
				'id' 	=> $post->ID,
				'text' 	=> $post->post_title . ' (' . $document->get_post_type_title() . ')',
			];
		}

		return $results;
	}

	/**
	 * Gets control values titles
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_value_titles( array $request ) {
		$ids = (array) $request['id'];
		$results = [];

		$query = new \WP_Query( [
			'post_type' 		=> Source_Local::CPT,
			'post__in' 			=> $ids,
			'posts_per_page' 	=> -1,
		]);

		foreach ( $query->posts as $post ) {
			$document = Utils::elementor()->documents->get( $post->ID );
			if ( ! $document )
				continue;

			$results[ $post->ID ] = $post->post_title . ' (' . $document->get_post_type_title() . ')';
		}

		return $results;
	}
}