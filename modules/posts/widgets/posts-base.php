<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Posts\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\Posts\Module;

// Elementor Classes
use Elementor\Controls_Manager; 

// Elementor Pro Classes
use ElementorPro\Modules\QueryControl\Controls\Group_Control_Related;
use ElementorPro\Modules\QueryControl\Module as Module_Query;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Posts
 *
 * @since 1.6.0
 */
abstract class Posts_Base extends Extras_Widget {

	/**
	 * Query
	 *
	 * @since  2.2.0
	 * @var    \WP_Query
	 */
	protected $_query = null;

	/**
	 * Get Query
	 *
	 * @since  2.2.0
	 * @return object|\WP_Query
	 */
	public function get_query() {
		return $this->_query;
	}

	/**
	 * Register Query Content Controls
	 *
	 * @since  2.2.0
	 * @return void
	 */
	protected function register_query_content_controls( $condition = [] ) {

		// Posts_Base query UI depends on Elementor Pro group controls; avoid fatal if Pro is inactive.
		if ( ! landtech_extras_is_elementor_pro_active() ) {
			return;
		}

		$this->start_controls_section(
			'section_query',
			[
				'label' => __( 'Query', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
				'condition' => $condition,
			]
		);

			$this->add_group_control(
				Group_Control_Related::get_type(),
				[
					'name' => 'posts',
					'presets' => [ 'full' ],
					'exclude' => [ // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor group control keys; not WP_Query exclude.
						'posts_per_page', //use the one from Layout section
						'ignore_sticky_posts'
					],
				]
			);

		$this->end_controls_section();

		$this->start_injection( [
			'at' => 'after',
			'of' => 'posts_select_date',
		] );

			$this->update_control( 'posts_orderby', [
				// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- WP_Query orderby keys for Elementor control UI; not an executed query.
				'options' => [
					'post_date' 		=> __( 'Date', 'landtech-extras-for-elementor' ),
					'post_title' 		=> __( 'Title', 'landtech-extras-for-elementor' ),
					'menu_order' 		=> __( 'Menu Order', 'landtech-extras-for-elementor' ),
					'rand' 				=> __( 'Random', 'landtech-extras-for-elementor' ),
					'meta_value'		=> __( 'Meta Value (text)', 'landtech-extras-for-elementor' ),
					'meta_value_num'	=> __( 'Meta Value (number)', 'landtech-extras-for-elementor' )
				],
				// phpcs:enable
			] );

		$this->end_injection();

		$this->start_injection( [
			'at' => 'after',
			'of' => 'posts_orderby',
		] );

			$this->add_control( 'posts_orderby_meta_key',
				[
					'label' 		=> __( 'Meta Key', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> '',
					'condition' => [
						'posts_orderby' => [ 'meta_value', 'meta_value_num' ], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- WP_Query orderby values for Elementor control visibility.
					],
				]
			);

		$this->end_injection();

		$this->start_injection( [
			'at' => 'after',
			'of' => 'posts_order',
		] );

			$this->add_control(
				'sticky_posts',
				[
					'label' 		=> __( 'Sticky Posts', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'separator'		=> 'before',
					'return_value' 	=> 'yes',
				]
			);

			$this->add_control(
				'sticky_posts_info',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'Preview of sticky posts option is only available on frontend.', 'landtech-extras-for-elementor' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
					'condition' 		=> [
						'sticky_posts!' => '',
						'sticky_only' => '',
					],
				]
			);

			$this->add_control(
				'sticky_only',
				[
					'label' 		=> __( 'Show Only Sticky Posts', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'condition' 	=> [
						'sticky_posts!' => '',
						'posts_post_type!' => 'by_id',
					],
					'return_value' 	=> 'yes',
				]
			);

		$this->end_injection();
	}

	/**
	 * Query Posts
	 *
	 * @since  2.2.0
	 * @return void
	 */
	public function query_posts() {
		$query_args = [
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' 		=> $this->get_posts_per_page(),
			'paged' 				=> $this->get_current_page(),
		];

		if ( $this->get_settings( 'posts_orderby_meta_key' ) ) {
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- User-selected orderby meta; required for Elementor/WP_Query meta ordering.
			$query_args['meta_key'] = $this->get_settings( 'posts_orderby_meta_key' );
		}

		if ( 'yes' === $this->get_settings('sticky_posts') ) {
			$sticky_posts = get_option('sticky_posts');

			$post__in = ! empty( $query_args['post__in'] ) ? $query_args['post__in'] : [];

			if ( 'yes' === $this->get_settings('sticky_only') ) {
				if ( empty( $sticky_posts ) ) {
					$sticky_posts = [0];
				}

				$query_args['post__in'] = array_merge( $post__in, $sticky_posts );
			} else {
				$query_args['ignore_sticky_posts'] = 0;
			}
		}

		/**
		 * Allow Premium and add-ons to merge query args before `set_query()`.
		 *
		 * Filter order matches `admin/PHASE1-AJAX-LOOPS-SPEC.md`: advanced merge, forced `paged`
		 * (facet REST), then facet/meta merge and later-priority listeners.
		 *
		 * @since 2.2.59
		 *
		 * @param array<string,mixed> $query_args Arguments passed to Elementor Query Module / WP_Query.
		 * @param Posts_Base          $widget     Widget instance implementing this base (Premium expects
		 *                                        `\LandTechExtras\Modules\Posts\Widgets\Posts_Base`).
		 */
		$query_args = (array) apply_filters( 'landtech_extras/posts/advanced_query_args', $query_args, $this );

		$paged_base = isset( $query_args['paged'] ) ? (int) $query_args['paged'] : 1;

		/**
		 * Override paged (e.g. LandTech Extras Premium AJAX facet REST).
		 *
		 * @since 2.2.59
		 *
		 * @param int         $paged  Current page number.
		 * @param Posts_Base $widget Widget instance.
		 */
		$query_args['paged'] = (int) apply_filters( 'landtech_extras/posts/query_paged', $paged_base, $this );

		/**
		 * Merge taxonomy / price facets, semantic query args, etc.
		 *
		 * @since 2.2.59
		 *
		 * @param array<string,mixed> $query_args Query arguments.
		 * @param Posts_Base          $widget     Widget instance.
		 */
		$query_args = (array) apply_filters( 'landtech_extras/posts/set_query_args', $query_args, $this );

		$this->set_query( $query_args );
	}

	/**
	 * Retrieve posts per page setting
	 *
	 * @since 	2.2.16
	 * @return 	int
	 */
	public function get_posts_per_page() {
		$posts_per_page = $this->get_settings('posts_per_page');

		if ( 'current_query' === $this->get_settings('posts_post_type') || ! $posts_per_page ) {
			$posts_per_page = (int)get_option( 'posts_per_page' );
		} else if ( 0 >= $posts_per_page ) {
			$posts_per_page = -1;
		}

		return $posts_per_page;
	}

	/**
	 * Checks for the Query ID and inits the WP_Query object
	 *
	 * @since 	2.2.0
	 * @param 	Array $query_args
	 * @return 	void
	 */
	public function set_query( $query_args ) {

		if ( ! landtech_extras_is_elementor_pro_active() ) {
			return;
		}

		if ( '' === $query_args['posts_per_page'] ) {
			// Handle empty posts per page setting
			$query_args['posts_per_page'] = (int)get_option( 'posts_per_page' );
		}
		
		$elementor_query = Module_Query::instance();
		
		add_filter( 'elementor/query/get_query_args/current_query', [ $this, 'fix_default_query_args' ] );

		$this->_query = $elementor_query->get_query( $this, 'posts', $query_args, [] );

		/**
		 * Query Filter
		 *
		 * Filters the current query
		 *
		 * @since 2.1.3
		 * @param WP_Query 			$query 		The initial query
		 */
		$this->_query = apply_filters( 'landtech_extras/widgets/posts/query', $this->_query );

		remove_filter( 'elementor/query/get_query_args/current_query', [ $this, 'fix_default_query_args' ] );
	}

	/**
	 * Filter to override posts per page on current query setting
	 *
	 * @since 2.2.0
	 * @param Array $global_args
	 */
	public function fix_default_query_args( $global_args ) {

		// When using current_query some default categories are set with a new WP_Query
		// which restrict results in archive pages
		if ( 'current_query' === $this->get_settings( 'posts_post_type' ) ) {
			if ( ! is_category() ) {
				$global_args['cat'] = false;
				$global_args['category_name'] = '';
			}
		}

		return $global_args;
	}

	/**
	 * Get Formatted Date
	 *
	 * Format a date based on format settings
	 *
	 * @since 2.2.0
	 * @param string       $custom_format Custom PHP date format when custom mode is on.
	 * @param string       $date_format   Date format preset key or string.
	 * @param string       $time_format   Time format preset key or string.
	 * @param bool|string  $custom        Whether the format is custom or not.
	 * @param int|null     $post_id       Optional post ID for get_the_date().
	 *
	 * @return string
	 */
	public function get_date_formatted( $custom_format, $date_format, $time_format, $custom = false, $post_id = null ) {
		if ( $custom ) {
			$format = $custom_format;
		} else {
			$date_format = $date_format;
			$time_format = $time_format;
			$format = '';

			if ( 'default' === $date_format ) {
				$date_format = get_option( 'date_format' );
			}

			if ( 'default' === $time_format ) {
				$time_format = get_option( 'time_format' );
			}

			if ( $date_format ) {
				$format = $date_format;
				$has_date = true;
			} else {
				$has_date = false;
			}

			if ( $time_format ) {
				if ( $has_date ) {
					$format .= ' ';
				}
				$format .= $time_format;
			}
		}

		$value = get_the_date( $format, $post_id );
		
		return wp_kses_post( $value );
	}

	/**
	 * Filter pagination link for page number
	 *
	 * @since  	2.2.38
	 * @return 	string
	 */
	public function filter_page_link( $link, $page ) {
		return add_query_arg( 'posts', $this->get_id(), $link );
	}

	/**
	 * Fetch wp link for page number
	 * Based on https://developer.wordpress.org/reference/functions/_wp_link_page/
	 *
	 * @since  	2.2.38
	 * @return 	string
	 */
	private function wp_link_page( $page ) {
		global $wp_rewrite;

		$post = get_post();
		$query_args = [];

		if ( $page == 1 ) {
			$url = get_permalink();
		} else {
			if ( '' === get_option( 'permalink_structure' ) || in_array( $post->post_status, [ 'draft', 'pending' ] ) ) {
				$url = add_query_arg( 'page', $page, get_permalink() );
			} elseif ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) === $post->ID ) {
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $page, 'single_paged' );
			} else {
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( $page, 'single_paged' );
			}
		}

		if ( is_preview() ) {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended -- WordPress core preview request; args are sanitized for get_preview_post_link() only.
			if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
				$preview_id_raw = wp_unslash( $_GET['preview_id'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below once scalar-safe.
				$query_args['preview_id'] = absint( is_scalar( $preview_id_raw ) ? $preview_id_raw : 0 );

				$pnonce_raw = wp_unslash( $_GET['preview_nonce'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below once scalar-safe.
				if ( ! is_scalar( $pnonce_raw ) ) {
					$pnonce_raw = '';
				}
				$query_args['preview_nonce'] = sanitize_text_field( (string) $pnonce_raw );
			}
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			$url = get_preview_post_link( $post, $query_args, $url );
		}

		return $url;
	}

	/**
	 * Get Pagination Link
	 * 
	 * Fetch link for page number
	 * Based on https://developer.wordpress.org/reference/functions/_wp_link_page/
	 *
	 * @since  	2.2.38
	 * @return 	string
	 */
	private function get_pagination_link( $page ) {
		$link = Module::is_custom_pagination() ? $this->wp_link_page( $page ) : get_pagenum_link( $page );

		/**
		 * Pagination Link Filter
		 *
		 * Filters the pagination link of the specified page
		 *
		 * @since 2.2.38
		 * @param string 			$link 	The initial link
		 * @param object|WP_Post 	$page 	The page number
		 */
		return apply_filters( 'landtech_extras/widgets/posts/pagination_link', $link, $page );
	}

	/**
	 * Render pagination link by page number
	 *
	 * @since  2.2.38
	 * @return void
	 */
	public function render_pagination_link( $label, $direction = NULL, $limit = NULL ) {
		$page = $this->get_current_page();

		if ( is_null( $limit ) ) {
			$limit = $this->get_query()->max_num_pages;
		}

		if ( 'next' === $direction ) {
			$page = intval( $page ) + 1;

			if ( $page > $limit ) {
				return;
			}

		} else if ( 'previous' === $direction ) {
			$page = intval( $page ) - 1;

			if ( $page < 1 ) {
				return;
			}
		} else {
			if ( $page < 1 || $page > $limit ) {
				return;
			}

			$label = $page;
		}

		$nav_link_key = $this->_get_repeater_setting_key( 'pagination', $direction, $page );

		$this->add_render_attribute( $nav_link_key, [
			'class' => [
				'ee-pagination__' . $direction,
				'page-numbers',
			],
			'href' => $this->get_pagination_link( $page ),
		] );

		?><a <?php $this->print_render_attribute_string( $nav_link_key ); ?>><?php
			echo esc_html( (string) $label );
		?></a><?php
	}

	/**
	 * Get Pagination Query Var
	 *
	 * @since  2.2.39
	 * @return string
	 */
	protected function get_pagination_query_var() {

		if ( is_front_page() && ! is_home() ) {

			// Page et as homepage uses paged in query
			// string and page as query var
			return 'page';

		} else if ( Module::is_custom_pagination() ) {

			return 'ltxe-page';
		}

		// Default
		return 'paged';
	}

	/**
	 * Get Current Page
	 *
	 * @since  1.6.0
	 * @return array
	 */
	public function get_current_page() {
		$pagination = '' !== $this->get_skin_setting( 'pagination' );
		$multiple 	= '' !== $this->get_skin_setting( 'pagination_multiple' );
		$infinite 	= '' !== $this->get_skin_setting( 'infinite_scroll' );
		$posts    = isset( $_GET['posts'] ) ? sanitize_text_field( wp_unslash( $_GET['posts'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Pagination widget compares public GET to widget ID only.
		$query_var 	= $this->get_pagination_query_var();

		if ( ! $infinite && ! $pagination ) {
			return 1;
		}

		$page = get_query_var( $query_var );

		if ( ! $page || $page < 2 ) {
			return 1;
		}

		if ( $posts && $this->get_id() !== $posts ) {
			return 1;
		} else {
			if ( $multiple && ! $posts ) {
				return 1;
			}
		}

		return $page;
	}

	/**
	 * get_repeater_setting_key wrapper
	 *
	 * @since 2.1.2
	 * @return string
	 */
	public function _get_repeater_setting_key( $setting_key, $repeater_key, $repeater_item_index ) {
		return $this->get_repeater_setting_key( $setting_key, $repeater_key, $repeater_item_index );
	}
}
