<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Search;

// LandTech Extras for Elementor Classes
use LandTechExtras\Utils;
use LandTechExtras\Base\Module_Base;
use LandTechExtras\Modules\Search\Conditions;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Search\Module
 *
 * @since  2.1.0
 */
class Module extends Module_Base {

	/**
	 * Constructor
	 *
	 * Hook into Elementor to register the widgets
	 *
	 * @access public
	 * @since  2.1.0
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		
		add_filter( 'query_vars', [ $this, 'register_query_vars' ] );
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ], 1 );

		if (  is_plugin_active( 'relevanssi/relevanssi.php' ) ) {
			add_filter( 'landtech_extras/widgets/posts/query', [ $this, 'add_relevanssi_compatibility' ], 10, 1 );
		}

		if ( landtech_extras_is_elementor_pro_active() ) {
			add_action( 'elementor/theme/register_conditions', [ $this, 'register_conditions' ] );
		}
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_name() {
		return 'search';
	}

	/**
	 * Get Widgets
	 * 
	 * Get the modules' widgets
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function get_widgets() {
		return [
			'Search_Form',
		];
	}

	/**
	 * Register Custom Query Vars
	 *
	 * @since  2.1.0
	 * @return array
	 * @link   https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
	 */
	public function register_query_vars( $vars ) {
		$vars[] = 'ltxe_search_query';
		$vars[] = 'ltxe_search_id';

		return $vars;
	}

	/**
	 * Register Theme Builder Conditions
	 *
	 * @since  2.1.0
	 * @access public
	 * @param  Conditions_Manager $conditions_manager
	 */
	public function register_conditions( $conditions_manager ) {
		$woocommerce_condition = new Conditions\Search_Id();

		$conditions_manager->get_condition( 'search' )->register_sub_condition( $woocommerce_condition );
	}

	/**
	 * Pre Get Posts
	 *
	 * Filter search results query
	 *
	 * @since  2.1.0
	 * @return array
	 * @link   https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
	 */
	function pre_get_posts( $query ) {
		
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ){
			return;
		}

		// Save query vars separately
		$query_vars = $query->query_vars;

		if ( ! array_key_exists( 'ltxe_search_query', $query_vars ) ) {
			return;
		}

		// Check if search query get var exists
		if ( $query_vars['ltxe_search_query'] ) {

			// Decode both url and json
			$search_query = json_decode( stripcslashes( $query_vars['ltxe_search_query'] ), JSON_UNESCAPED_SLASHES );

			if ( ! is_array( $search_query ) ) {
				return;
			}

			// Set post types
			if ( isset( $search_query['post_type'] ) ) {
				$this->apply_post_type_restriction( $query, $search_query['post_type'] );
			}

			// Set post authors
			if ( isset( $search_query['author'] ) ) {
				// Set the query var
				$query->set( 'author__in', $search_query['author'] );
			}

			// Get taxnomnies as names
			$taxonomies  = get_taxonomies( [ 'show_in_nav_menus' => true ] );
			$tax_queries = [];

			// Loop through all public taxonomies
			foreach ( $taxonomies as $taxonomy ) {

				if ( ! array_key_exists( $taxonomy, $search_query ) ) { // Taxnonomy appears in restrictions
					continue;
				}

				// If no taxnomy terms sent
				if ( ! $search_query[ $taxonomy ] ) {
					continue;
				}

				$tax_clause = $this->build_tax_query_clause( $taxonomy, $search_query[ $taxonomy ] );
				if ( $tax_clause ) {
					$tax_queries[] = $tax_clause;
				}
			}

			if ( count( $tax_queries ) > 1 ) {
				$tax_queries['relation'] = 'AND';
			}

			if ( ! empty( $tax_queries ) ) {
				// Set the query var
				$query->set( 'tax_query', $tax_queries );
			}
		}

		return $query;
	}

	/**
	 * Apply post type restriction from search query JSON.
	 *
	 * @since 2.5.1
	 *
	 * @param \WP_Query $query WP_Query instance.
	 * @param mixed     $payload Raw post_type payload.
	 * @return void
	 */
	protected function apply_post_type_restriction( $query, $payload ) {
		if ( is_array( $payload ) && isset( $payload['mode'] ) ) {
			$mode  = sanitize_key( (string) $payload['mode'] );
			$terms = isset( $payload['terms'] ) ? (array) $payload['terms'] : array();
			$terms = array_values( array_filter( array_map( 'sanitize_key', $terms ) ) );

			if ( 'include' === $mode && ! empty( $terms ) ) {
				$query->set( 'post_type', $terms );
				return;
			}

			if ( 'exclude' === $mode && ! empty( $terms ) ) {
				$all_types = array_keys( Utils::get_public_post_types_options( true, false ) );
				$query->set( 'post_type', array_values( array_diff( $all_types, $terms ) ) );
				return;
			}

			if ( 'all' === $mode ) {
				return;
			}
		}

		$query->set( 'post_type', $payload );
	}

	/**
	 * Build a tax_query clause from legacy or structured payloads.
	 *
	 * @since 2.5.1
	 *
	 * @param string $taxonomy Taxonomy slug.
	 * @param mixed  $payload  Term payload.
	 * @return array|null
	 */
	protected function build_tax_query_clause( $taxonomy, $payload ) {
		if ( is_array( $payload ) && isset( $payload['mode'] ) ) {
			$mode  = sanitize_key( (string) $payload['mode'] );
			$terms = isset( $payload['terms'] ) ? (array) $payload['terms'] : array();
			$terms = array_values( array_filter( array_map( 'sanitize_title', $terms ) ) );

			if ( 'include' === $mode && ! empty( $terms ) ) {
				return array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'operator' => 'IN',
					'terms'    => $terms,
				);
			}

			if ( 'exclude' === $mode && ! empty( $terms ) ) {
				return array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'operator' => 'NOT IN',
					'terms'    => $terms,
				);
			}

			if ( 'all' === $mode ) {
				return null;
			}

			return null;
		}

		$terms    = $payload;
		$operator = 'IN';

		if ( is_array( $terms ) && in_array( 'all', $terms, true ) ) {
			array_splice( $terms, array_search( 'all', $terms, true ), 1 );
		} elseif ( 'all' === $terms ) {
			$terms = array_keys( Utils::get_terms_options( $taxonomy, 'slug', false ) );
		} elseif ( 'any' === $terms ) {
			return null;
		}

		if ( empty( $terms ) ) {
			return null;
		}

		return array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'operator' => $operator,
			'terms'    => $terms,
		);
	}

	/**
	 * Add Relevanssi compatibility
	 *
	 * Replaces the default query by running Relevanssi logic on it
	 * if the search form id is present in the query vars
	 *
	 * @since  2.2.2
	 * @return array
	 * @link   https://codex.wordpress.org/Plugin_API/Filter_Reference/query_vars
	 */
	public function add_relevanssi_compatibility( $query ) {
		if ( function_exists( 'relevanssi_do_query' ) && is_search() && get_query_var( 'ltxe_search_id' ) ) {
			$relevanssi_query = new \WP_Query();
			$relevanssi_query->parse_query( $query->query_vars );
			relevanssi_do_query( $relevanssi_query );

			return $relevanssi_query;
		}

		return $query;
	}
}
