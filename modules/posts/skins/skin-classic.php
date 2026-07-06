<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Posts\Skins;

// LandTech Extras for Elementor Classes
use LandTechExtras\Utils;
use LandTechExtras\Group_Control_Transition;
use LandTechExtras\Modules\Posts\Module;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Posts\Skins
 *
 * @since  1.6.0
 */
class Skin_Classic extends Skin_Base {

	/**
	 * Get Title
	 * 
	 * Gets the current skin ID
	 *
	 * @since  1.6.0
	 * @return string
	 */
	public function get_id() {
		return 'classic';
	}

	/**
	 * Get Title
	 * 
	 * Gets the current skin title
	 *
	 * @since  1.6.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Classic', 'landtech-extras-for-elementor' );
	}

	/**
	 * Editor preview: expose loop layout so editor JS can initialise Isotope without inline PHP strings.
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_loop_start() {

		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			$this->parent->add_render_attribute(
				'loop',
				'data-ee-editor-isotope-layout',
				(string) $this->get_instance_value( 'layout' )
			);
		}

		parent::render_loop_start();
	}

	/**
	 * Register Controls Actions
	 * 
	 * Registers controls at specific points in the Controls Stack
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function _register_controls_actions() {
		parent::_register_controls_actions();

		// add_action( 'elementor/element/posts-extra/section_query/after_section_end', [ $this, 'register_parallax_controls' ] );
		add_action( 'elementor/element/posts-extra/section_query/after_section_end', [ $this, 'register_filters_controls' ] );
		add_action( 'elementor/element/posts-extra/section_query/after_section_end', [ $this, 'register_infinite_scroll_controls' ] );
		add_action( 'elementor/element/posts-extra/section_query/after_section_end', [ $this, 'register_pagination_controls' ] );

		add_action( 'elementor/element/posts-extra/section_style_terms/after_section_end', [ $this, 'register_filters_style_controls' ] );
		add_action( 'elementor/element/posts-extra/section_style_terms/after_section_end', [ $this, 'register_pagination_style_controls' ] );
		add_action( 'elementor/element/posts-extra/section_style_terms/after_section_end', [ $this, 'register_infinite_scroll_style_controls' ] );
	}

	/**
	 * Register Layout Content Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_layout_content_controls() {
		parent::register_layout_content_controls();

		$this->update_responsive_control(
			'grid_columns_spacing',
			[
				'selectors' => [
					'{{WRAPPER}} .ee-grid__item' => 'padding-left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ee-grid' => 'margin-left: -{{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->parent->start_injection( [
			'at' => 'before',
			'of' => 'classic_grid_columns_spacing',
		] );

			$this->add_control(
				'grid_heading',
				[
					'label' 	=> __( 'Grid', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_control(
				'layout',
				[
					'label' 		=> __( 'Layout', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'default',
					'options' 		=> apply_filters(
						'landtech_extras/posts/grid_layout_mode_choices',
						[
							'default' 		=> __( 'Default', 'landtech-extras-for-elementor' ),
							'masonry' 		=> __( 'Masonry', 'landtech-extras-for-elementor' ),
						],
						$this
					),
					'condition'		=> [
						'columns!'	=> '1',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'packery_featured_width',
				[
					'label'       => __( 'Packery: first item width', 'landtech-extras-for-elementor' ),
					'description' => __( 'Width of the first grid item when using Metro tiles (Packery). Does not apply to other layouts.', 'landtech-extras-for-elementor' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => [ '%' ],
					'range'       => [
						'%' => [
							'min'  => 25,
							'max'  => 100,
							'step' => 0.1,
						],
					],
					'default'     => [
						'unit' => '%',
						'size' => 66.666,
					],
					'tablet_default' => [
						'unit' => '%',
						'size' => 66.666,
					],
					'mobile_default' => [
						'unit' => '%',
						'size' => 100,
					],
					'selectors'   => [
						'{{WRAPPER}} .ee-grid--packery .ltxee-packery-featured' => 'width: {{SIZE}}{{UNIT}} !important;',
					],
					'condition'   => [
						$this->get_control_id( 'layout' ) => 'packery',
						'columns!' => '1',
					],
				]
			);

			$this->add_responsive_control(
				'packery_second_width',
				[
					'label'       => __( 'Packery: second item width', 'landtech-extras-for-elementor' ),
					'description' => __( 'Width of the second grid item when using Metro tiles (Packery). Does not apply to other layouts.', 'landtech-extras-for-elementor' ),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => [ '%' ],
					'range'       => [
						'%' => [
							'min'  => 25,
							'max'  => 100,
							'step' => 0.1,
						],
					],
					'default'     => [
						'unit' => '%',
						'size' => 33.333,
					],
					'tablet_default' => [
						'unit' => '%',
						'size' => 33.333,
					],
					'mobile_default' => [
						'unit' => '%',
						'size' => 100,
					],
					'selectors'   => [
						'{{WRAPPER}} .ee-grid--packery .ltxee-packery-second' => 'width: {{SIZE}}{{UNIT}} !important;',
					],
					'condition'   => [
						$this->get_control_id( 'layout' ) => 'packery',
						'columns!' => '1',
					],
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Register Parallax Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_parallax_controls() {

		$this->start_controls_section(
			'section_parallax',
			[
				'label' => __( 'Parallax', 'landtech-extras-for-elementor' ),
				'condition' 	=> [
					$this->get_control_id( 'parallax!' ) => '',
				],
			]
		);
		

			$this->add_control(
				'parallax_disable_on',
				[
					'label' 	=> __( 'Disable for', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'mobile',
					'options' 			=> [
						'none' 		=> __( 'None', 'landtech-extras-for-elementor' ),
						'tablet' 	=> __( 'Mobile and tablet', 'landtech-extras-for-elementor' ),
						'mobile' 	=> __( 'Mobile only', 'landtech-extras-for-elementor' ),
					],
					'condition' 	=> [
						$this->get_control_id( 'parallax!' ) => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'parallax_speed',
				[
					'label' 	=> __( 'Parallax speed', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> 0.5,
						'unit'	=> 'px',
					],
					'tablet_default' => [
						'size'	=> 0.5,
						'unit'	=> 'px',
					],
					'mobile_default' => [
						'size'	=> 0.5,
						'unit'	=> 'px',
					],
					'range' 	=> [
						'px' 	=> [
							'min'	=> 0.05,
							'max' 	=> 1,
							'step'	=> 0.01,
						],
					],
					'condition' 	=> [
						$this->get_control_id( 'parallax!' ) => '',
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Infinite Scroll Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_infinite_scroll_controls() {

		$this->start_controls_section(
			'section_infinite_scroll',
			[
				'label' => __( 'Infinite Scroll', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'infinite_scroll',
				[
					'label' 		=> __( 'Infinite Scroll', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'separator'		=> 'after',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'infinite_scroll_history',
				[
					'label' 		=> __( 'Enable History', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Change the browser history and URL when loading new posts.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'condition'		=> [
						$this->get_control_id( 'infinite_scroll!' ) => '',
					],
				]
			);

			$this->add_control(
				'infinite_scroll_status_heading',
				[
					'separator'	=> 'before',
					'label' 	=> __( 'Status and Loader', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'	=> [
						$this->get_control_id( 'infinite_scroll!' ) => '',
					],
				]
			);

			$this->add_control(
				'infinite_scroll_status',
				[
					'label' 		=> __( 'Show Statuses', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'return_value' 	=> 'yes',
					'condition' 	=> [
						$this->get_control_id( 'infinite_scroll!' ) => '',
					],
				]
			);

			$this->add_control(
				'infinite_scroll_status_helper',
				[
					'label' 		=> __( 'Preview in Editor', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Preview loader and status texts in editor mode.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'return_value' 	=> 'on',
					'condition' 	=> [
						$this->get_control_id( 'infinite_scroll_status!' ) => '',
						$this->get_control_id( 'infinite_scroll!' ) => '',
					],
					'prefix_class'	=> 'ee-load-status-helper-'
				]
			);

			$this->add_control(
				'infinite_scroll_loading_type',
				[
					'label' 		=> __( 'Loading Type', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'loader',
					'options' 		=> [
						'loader' 	=> __( 'Loader', 'landtech-extras-for-elementor' ),
						'text' 		=> __( 'Text', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_status!' ) => '',
					],
				]
			);

			$this->add_control(
				'infinite_scroll_loading_loader',
				[
					'label' 		=> __( 'Loader', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default'		=> 'track',
					'options' 		=> [
						'track'    	=> [
							'title' 	=> __( 'Circle Track', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'nicon nicon-loader-track',
						],
						'circle' 	=> [
							'title' 	=> __( 'Circle', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'nicon nicon-loader-circle',
						],
						'bars-equal' => [
							'title' 	=> __( 'Equal Bars', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'nicon nicon-loader-bars-equal',
						],
						'bars-flex' => [
							'title' 	=> __( 'Flexible Bars', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'nicon nicon-loader-bars-flex',
						],
					],
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_status!' ) => '',
						$this->get_control_id( 'infinite_scroll_loading_type' ) => 'loader',
					],
				]
			);

			$this->add_control(
				'infinite_scroll_loading_text',
				[
					'label' 		=> __( 'Loading Text', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> __( 'Loading', 'landtech-extras-for-elementor' ),
					'placeholder' 	=> __( 'Loading', 'landtech-extras-for-elementor' ),
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_status!' ) => '',
						$this->get_control_id( 'infinite_scroll_loading_type' ) => 'text',
					],
				]
			);

			$this->add_control(
				'infinite_scroll_last_text',
				[
					'label' 		=> __( 'Last Text', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> __( 'All articles loaded', 'landtech-extras-for-elementor' ),
					'placeholder' 	=> __( 'All articles loaded', 'landtech-extras-for-elementor' ),
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_status!' ) => '',
					],
				]
			);

			$this->add_control(
				'infinite_scroll_error_text',
				[
					'label' 		=> __( 'Error Text', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> __( 'No more articles to load', 'landtech-extras-for-elementor' ),
					'placeholder' 	=> __( 'No more articles to load', 'landtech-extras-for-elementor' ),
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_status!' ) => '',
					],
				]
			);

			$this->add_control(
				'infinite_scroll_button_heading',
				[
					'separator'	=> 'before',
					'label' 	=> __( 'Load Button', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'	=> [
						$this->get_control_id( 'infinite_scroll!' ) => '',
					],
				]
			);

			$this->add_control(
				'infinite_scroll_button',
				[
					'label' 		=> __( 'Show Load Button', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'condition'		=> [
						$this->get_control_id( 'infinite_scroll!' ) => '',
					],
				]
			);

			$this->add_control(
				'infinite_scroll_button_text',
				[
					'label' 		=> __( 'Button Text', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> __( 'Load more', 'landtech-extras-for-elementor' ),
					'placeholder' 	=> __( 'Load more', 'landtech-extras-for-elementor' ),
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_button' ) => 'yes',
					],
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register Filters Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_filters_controls() {

		$this->start_controls_section(
			'section_filters',
			[
				'label' => __( 'Filters', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'filters',
				[
					'label' 				=> __( 'Enable Filters', 'landtech-extras-for-elementor' ),
					'type' 					=> Controls_Manager::SWITCHER,
					'default' 				=> '',
					'separator'				=> 'after',
					'return_value' 			=> 'yes',
					'frontend_available' 	=> true,
				]
			);

			$this->add_control(
				'filters_is_warning',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'Warning: Filters are not meant to work optimally with Infinite Scroll because posts posts will load upon scroll might result in empty filters until the posts are loaded. ', 'landtech-extras-for-elementor' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
					'condition' 	=> [
						$this->get_control_id( 'filters!' ) => '',
					],
				]
			);

			$taxonomies = Utils::get_taxonomies_options();

			$this->add_control(
				'filters_taxonomy',
				[
					'label' 		=> __( 'Taxonomy', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT2,
					'label_block' 	=> true,
					'default'		=> 'category',
					'options' 		=> $taxonomies,
					'condition' 	=> [
						$this->get_control_id( 'filters!' ) => '',
					],
				]
			);

			foreach ( $taxonomies as $name => $label ) {
				$terms = Utils::get_terms_options( $name );

				$this->add_control(
					'filters_taxonomy_' . str_replace( '-', '_', $name ),
					[
						'label' 		=> __( 'Default term', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SELECT,
						'label_block' 	=> true,
						'default'		=> '',
						'options' 		=> $terms,
						'condition' 	=> [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy' ) => $name,
						],
					]
				);

				$exclude_terms = Utils::get_terms_options( $name, 'id', false );

				$this->add_control(
					'filters_taxonomy_exclude_' . str_replace( '-', '_', $name ),
					[
						'label'			=> __( 'Exclude Terms', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SELECT2,
						'placeholder'	=> __( 'None', 'landtech-extras-for-elementor' ),
						'multiple'		=> true,
						'options' 		=> $exclude_terms,
						'label_block'	=> true,
						'condition' 	=> [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy' ) => $name,
						],
					]
				);
			}

			$this->add_control(
				'filters_show_all',
				[
					'label' 		=> __( 'Show All Terms', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Show all filters (except excluded ones) instead of just those corresponding to the initial queried posts?', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'condition'		=> [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'infinite_scroll' ) 	=> 'yes',
					]
				]
			);

			landtech_extras_require_plugin_api();

			if ( is_plugin_active( 'intuitive-custom-post-order/intuitive-custom-post-order.php' ) ) {
				$this->add_control(
					'filters_order_warning',
					[
						'type' 				=> Controls_Manager::RAW_HTML,
						'raw' 				=> __( 'Looks like you\'re using the Intuitive Custom Posts Order plugin. If you enable ordering on your taxonomy with this plugin, the ordering options below won\'t have any effect.', 'landtech-extras-for-elementor' ),
						'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
						'condition' 	=> [
							$this->get_control_id( 'filters!' ) => '',
						],
					]
				);
			}

			$this->add_control(
				'filters_orderby',
				[
					'label' 		=> __( 'Order By', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default'		=> 'name',
					'options' 		=> [
						'name'			=> __( 'Name', 'landtech-extras-for-elementor' ),
						'term_id'		=> __( 'Term ID', 'landtech-extras-for-elementor' ),
						'count'			=> __( 'Post Count', 'landtech-extras-for-elementor' ),
						'slug'			=> __( 'Slug', 'landtech-extras-for-elementor' ),
						'description'	=> __( 'Description', 'landtech-extras-for-elementor' ),
						'parent'		=> __( 'Term Parent', 'landtech-extras-for-elementor' ),
						'menu_order'	=> __( 'Menu Order', 'landtech-extras-for-elementor' ),
					],
					'condition' 	=> [
						$this->get_control_id( 'filters!' ) => '',
					],
				]
			);

			$this->add_control(
				'filters_order',
				[
					'label' 		=> __( 'Order', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default'		=> 'ASC',
					'options' 		=> [
						'ASC'		=> __( 'Ascending', 'landtech-extras-for-elementor' ),
						'DESC'		=> __( 'Descending', 'landtech-extras-for-elementor' ),
					],
					'condition' 	=> [
						$this->get_control_id( 'filters!' ) => '',
					],
				]
			);

			$this->add_control(
				'filters_show_count',
				[
					'label' 		=> __( 'Show Post Count', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'condition'		=> [
						$this->get_control_id( 'filters!' ) => '',
					]
				]
			);

			$this->add_control(
				'filters_not_found_text',
				[
					'label' 		=> __( 'Not Found text', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> __( 'No posts available', 'landtech-extras-for-elementor' ),
					'placeholder' 	=> __( 'No posts available', 'landtech-extras-for-elementor' ),
					'condition'		=> [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'infinite_scroll' ) 	=> 'yes',
						$this->get_control_id( 'filters_show_all' ) => 'yes',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'filters_all_show',
				[
					'label' 		=> __( 'Show "All" Filter', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'return_value' 	=> 'yes',
					'separator'		=> 'before',
					'condition'		=> [
						$this->get_control_id( 'filters!' ) => '',
					]
				]
			);

			$this->add_control(
				'filters_all_text',
				[
					'label' 		=> __( 'All Text', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::TEXT,
					'default' 		=> __( 'All', 'landtech-extras-for-elementor' ),
					'placeholder' 	=> __( 'All', 'landtech-extras-for-elementor' ),
					'condition' 	=> [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_all_show!' ) => '',
					],
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register Pagination Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_pagination_controls() {
		$this->start_controls_section(
			'section_pagination',
			[
				'label' 	=> __( 'Pagination', 'landtech-extras-for-elementor' ),
				'condition'	=> [
					$this->get_control_id( 'infinite_scroll' ) 	=> '',
				],
			]
		);

			$this->add_control(
				'pagination',
				[
					'label' 		=> __( 'Pagination', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'separator'		=> 'after',
					'return_value' 	=> 'yes',
					'condition'		=> [
						$this->get_control_id( 'infinite_scroll' ) => '',
					]
				]
			);

			$this->add_control(
				'pagination_numbers',
				[
					'label' 		=> __( 'Show Numbers', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'return_value' 	=> 'yes',
					'condition'		=> [
						$this->get_control_id( 'infinite_scroll' ) 	=> '',
						$this->get_control_id( 'pagination' ) 		=> 'yes',
					]
				]
			);

			$this->add_control(
				'pagination_show_all',
				[
					'label' 		=> __( 'Show All Numbers', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'return_value' 	=> 'yes',
					'condition' 	=> [
						$this->get_control_id( 'pagination_numbers' ) 	=> 'yes',
						$this->get_control_id( 'infinite_scroll' ) 		=> '',
						$this->get_control_id( 'pagination' ) 			=> 'yes',
					],
				]
			);

			$this->add_control(
				'pagination_prev_next',
				[
					'label' 		=> __( 'Show Prev Next', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'return_value' 	=> 'yes',
					'condition'		=> [
						$this->get_control_id( 'infinite_scroll' ) 	=> '',
						$this->get_control_id( 'pagination' ) 		=> 'yes',
					]
				]
			);

			$this->add_control(
				'pagination_page_limit',
				[
					'label' 		=> __( 'Page Limit', 'landtech-extras-for-elementor' ),
					'default' 		=> '5',
					'conditions' => [
						'relation' 	=> 'or',
						'terms' 	=> [
							[
								'name' 		=> $this->get_control_id( 'infinite_scroll' ),
								'operator' 	=> '==',
								'value' 	=> 'yes',
							],
							[
								'name' 		=> $this->get_control_id( 'pagination' ),
								'operator' 	=> '==',
								'value' 	=> 'yes',
							],
						],
					],
				]
			);

			$this->add_control(
				'pagination_multiple',
				[
					'label' 		=> __( 'Handle Multiple', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'If you have multiple Posts Extra widgets on this page, enable this to make sure one pagination doesn\'t affect the others', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'return_value' 	=> 'yes',
					'condition' 	=> [
						$this->get_control_id( 'infinite_scroll' ) => '',
						$this->get_control_id( 'pagination' ) => 'yes',
						'posts_post_type!' => 'current_query',
					],
				]
			);

			$this->add_control(
				'pagination_previous_label',
				[
					'label' 		=> __( 'Previous Label', 'landtech-extras-for-elementor' ),
					'default' 		=> __( '&larr; Previous', 'landtech-extras-for-elementor' ),
					'condition' 	=> [
						$this->get_control_id( 'pagination_prev_next' ) 	=> 'yes',
						$this->get_control_id( 'infinite_scroll' ) 			=> '',
						$this->get_control_id( 'pagination' ) 				=> 'yes',
					],
				]
			);

			$this->add_control(
				'pagination_next_label',
				[
					'label' 		=> __( 'Next Label', 'landtech-extras-for-elementor' ),
					'default' 		=> __( 'Next &rarr;', 'landtech-extras-for-elementor' ),
					'condition' 	=> [
						$this->get_control_id( 'pagination_prev_next' ) 	=> 'yes',
						$this->get_control_id( 'infinite_scroll' ) 			=> '',
						$this->get_control_id( 'pagination' ) 				=> 'yes',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Filters Style Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_filters_style_controls() {

		$this->start_controls_section(
			'section_style_filters',
			[
				'label' => __( 'Filters', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'filters!' ) => '',
				]
			]
		);

			$this->add_control(
				'filters_filters_heading',
				[
					'separator' => 'before',
					'label' 	=> __( 'Filters', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'stack',
				[
					'label' 		=> __( 'Stack', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'return_value'	=> 'stack',
					'prefix_class'	=> 'ee-filters%s--',
				]
			);

			$this->add_responsive_control(
				'filters_align',
				[
					'label' 		=> __( 'Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'left' 			=> [
							'title' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-right',
						],
						'justify' 		=> [
							'title' 	=> __( 'Stretch', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-stretch',
						],
					],
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
					],
					'prefix_class' 	=> 'ee-filters-align%s-',
				]
			);

			$this->add_responsive_control(
				'filters_distance',
				[
					'label' 		=> __( 'Distance', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 48,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-filters' => 'margin-bottom: {{SIZE}}px',
					],
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
					]
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'filters_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-filters__item',
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
					]
				]
			);

			$this->add_control(
				'filters_filter_heading',
				[
					'separator' => 'before',
					'label' 	=> __( 'Filter', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'filters_filter_spacing',
				[
					'label' 		=> __( 'Horizontal Spacing', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 48,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-filters' => 'margin-left: -{{SIZE}}px',
						'{{WRAPPER}} .ee-filters__item' => 'margin-left: {{SIZE}}px',
					],
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'filters_filter_vertical_spacing',
				[
					'label' 		=> __( 'Vertical Spacing', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'If your terms are stacked, this will help you distance them from one another.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 48,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-filters__item' => 'margin-bottom: {{SIZE}}px',
					],
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'filters_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-filters__item a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
					]
				]
			);

			$this->add_control(
				'filters_border_radius',
				[
					'type' 			=> Controls_Manager::DIMENSIONS,
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-filters__item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
					]
				]
			);

			$this->start_controls_tabs( 'filters_tabs_hover' );

			$this->start_controls_tab( 'filters_tab_default', [ 'label' => __( 'Default', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'filters_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
						]
					]
				);

				$this->add_control(
					'filters_background_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
						]
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' 		=> 'filters_border',
						'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
						'selector' 	=> '{{WRAPPER}} .ee-filters__item a',
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'filters_tab_hover', [ 'label' => __( 'Hover', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'filters_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a:hover' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
						]
					]
				);

				$this->add_control(
					'filters_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a:hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
						]
					]
				);

				$this->add_control(
					'filters_border_color_hover',
					[
						'label' 	=> __( 'Border Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a:hover' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'filters_tab_active', [ 'label' => __( 'Active', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'filters_color_active',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a.ee--active' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
						]
					]
				);

				$this->add_control(
					'filters_background_color_active',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a.ee--active' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
						]
					]
				);

				$this->add_control(
					'filters_border_color_active',
					[
						'label' 	=> __( 'Border Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a.ee--active' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'filters_count_heading',
				[
					'separator' => 'before',
					'label' 	=> __( 'Post Count', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
						$this->get_control_id( 'filters_show_count!' ) => '',
					],
				]
			);

			$this->add_responsive_control(
				'filters_count_distance',
				[
					'label' 		=> __( 'Distance', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 48,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-filters__item__count' => 'margin-left: {{SIZE}}px',
					],
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
						$this->get_control_id( 'filters_show_count!' ) => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'filters_count_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'selector' 	=> '{{WRAPPER}} .ee-filters__item__count',
					'exclude'	=> [ // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor typography control keys.
						'font-family',
						'line_height',
					],
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
						$this->get_control_id( 'filters_show_count!' ) => '',
					],
				]
			);

			$this->add_responsive_control(
				'filters_count_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-filters__item__count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
						$this->get_control_id( 'filters_show_count!' ) => '',
					],
				]
			);

			$this->add_control(
				'filters_count_border_radius',
				[
					'type' 			=> Controls_Manager::DIMENSIONS,
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-filters__item__count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'filters!' ) => '',
						$this->get_control_id( 'filters_taxonomy!' ) => '',
						$this->get_control_id( 'filters_show_count!' ) => '',
					],
				]
			);

			$this->start_controls_tabs( 'filters_count_tabs' );

			$this->start_controls_tab( 'filters_count_default', [
				'label' => __( 'Default', 'landtech-extras-for-elementor' ),
				'condition' => [
					$this->get_control_id( 'filters!' ) => '',
					$this->get_control_id( 'filters_taxonomy!' ) => '',
					$this->get_control_id( 'filters_show_count!' ) => '',
				],
			] );

				$this->add_control(
					'filters_count_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item__count' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
							$this->get_control_id( 'filters_show_count!' ) => '',
						]
					]
				);

				$this->add_control(
					'filters_count_background_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item__count' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
							$this->get_control_id( 'filters_show_count!' ) => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'filters_count_hover', [
				'label' => __( 'Hover', 'landtech-extras-for-elementor' ),
				'condition' => [
					$this->get_control_id( 'filters!' ) => '',
					$this->get_control_id( 'filters_taxonomy!' ) => '',
					$this->get_control_id( 'filters_show_count!' ) => '',
				],
			] );

				$this->add_control(
					'filters_count_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a:hover .ee-filters__item__count' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
							$this->get_control_id( 'filters_show_count!' ) => '',
						]
					]
				);

				$this->add_control(
					'filters_count_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a:hover .ee-filters__item__count' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
							$this->get_control_id( 'filters_show_count!' ) => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'filters_count_active', [
				'label' => __( 'Active', 'landtech-extras-for-elementor' ),
				'condition' => [
					$this->get_control_id( 'filters!' ) => '',
					$this->get_control_id( 'filters_taxonomy!' ) => '',
					$this->get_control_id( 'filters_show_count!' ) => '',
				],
			] );

				$this->add_control(
					'filters_count_color_active',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a.ee--active .ee-filters__item__count' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
							$this->get_control_id( 'filters_show_count!' ) => '',
						]
					]
				);

				$this->add_control(
					'filters_count_background_color_active',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-filters__item a.ee--active .ee-filters__item__count' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'filters!' ) => '',
							$this->get_control_id( 'filters_taxonomy!' ) => '',
							$this->get_control_id( 'filters_show_count!' ) => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Register Pagination Style Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_pagination_style_controls() {

		$this->start_controls_section(
			'section_style_pagination',
			[
				'label' => __( 'Pagination', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'pagination!' ) => '',
				]
			]
		);

			$this->add_control(
				'pagination_heading',
				[
					'separator' => 'before',
					'label' 	=> __( 'Pagination', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition' => [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_align',
				[
					'label' 		=> __( 'Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'left' 			=> [
							'title' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-right',
						],
					],
					'condition' => [
						$this->get_control_id( 'pagination!' ) => '',
					],
					'selectors'	=> [
						'{{WRAPPER}} .ee-pagination' => 'text-align: {{VALUE}};',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_distance',
				[
					'label' 		=> __( 'Distance', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 48,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-pagination' => 'margin-top: {{SIZE}}px',
					],
					'condition' => [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'pagination_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-pagination .page-numbers',
					'condition' => [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->add_control(
				'pagination_numbers_heading',
				[
					'separator' => 'before',
					'label' 	=> __( 'Numbers', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition' => [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_numbers_spacing',
				[
					'label' 		=> __( 'Spacing', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-pagination .page-numbers' => 'margin: 0 {{SIZE}}px',
					],
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_numbers_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-pagination .page-numbers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->add_control(
				'pagination_numbers_border_radius',
				[
					'separator'		=> 'after',
					'type' 			=> Controls_Manager::DIMENSIONS,
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-pagination .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->start_controls_tabs( 'pagination_numbers_tabs_hover' );

			$this->start_controls_tab( 'pagination_numbers_tab_default', [ 'label' => __( 'Default', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'pagination_numbers_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-pagination .page-numbers' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'pagination_numbers_background_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-pagination .page-numbers' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'pagination_numbers_opacity',
					[
						'label' 		=> __( 'Opacity', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 1,
								'step'=> 0.05,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ee-pagination .page-numbers' => 'opacity: {{SIZE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' 		=> 'pagination_numbers_border',
						'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
						'selector' 	=> '{{WRAPPER}} .ee-pagination .page-numbers',
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'pagination_numbers_tab_hover', [ 'label' => __( 'Hover', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'pagination_numbers_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-pagination .page-numbers[href]:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'pagination_numbers_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-pagination .page-numbers[href]:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'pagination_numbers_border_color_hover',
					[
						'label' 	=> __( 'Border Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-pagination .page-numbers[href]:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'pagination_numbers_opacity_hover',
					[
						'label' 		=> __( 'Opacity', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 1,
								'step'=> 0.05,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ee-pagination .page-numbers[href]:hover' => 'opacity: {{SIZE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'pagination_numbers_tab_current', [ 'label' => __( 'Current', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'pagination_numbers_color_current',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-pagination .page-numbers.current' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'pagination_numbers_background_color_current',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-pagination .page-numbers.current' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'pagination_numbers_border_color_current',
					[
						'label' 	=> __( 'Border Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-pagination .page-numbers.current' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'pagination_numbers_opacity_current',
					[
						'label' 		=> __( 'Opacity', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 1,
								'step'=> 0.05,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ee-pagination .page-numbers.current' => 'opacity: {{SIZE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Register Infinite Scroll Style Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_infinite_scroll_style_controls() {

		$this->start_controls_section(
			'section_style_infinite_scroll',
			[
				'label' => __( 'Infinite Scroll', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'infinite_scroll!' ) => '',
				]
			]
		);

			$this->add_control(
				'infinite_scroll_status_style_heading',
				[
					'separator' => 'before',
					'label' 	=> __( 'Status', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_status!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'infinite_scroll_status_spacing',
				[
					'label' 		=> __( 'Spacing', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 48,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-load-status' => 'margin-top: {{SIZE}}px',
					],
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_status!' ) => '',
					]
				]
			);

			$this->add_control(
				'infinite_scroll_loader_style_heading',
				[
					'separator' => 'before',
					'label' 	=> __( 'Loader', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_status!' ) => '',
						$this->get_control_id( 'infinite_scroll_loading_type' ) => 'loader',
					]
				]
			);

			$this->add_control(
				'infinite_scroll_loader_color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-load-status__request svg *[fill]' => 'fill: {{VALUE}};',
					],
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_status!' ) => '',
						$this->get_control_id( 'infinite_scroll_loading_type' ) => 'loader',
					]
				]
			);

			$this->add_control(
				'infinite_scroll_button_style_heading',
				[
					'separator' => 'before',
					'label' 	=> __( 'Button', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_button!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'infinite_scroll_button_spacing',
				[
					'label' 		=> __( 'Spacing', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 48,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-load-button' => 'margin-top: {{SIZE}}px',
					],
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_button!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'infinite_scroll_button_align',
				[
					'label' 		=> __( 'Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'center',
					'options' 		=> [
						'flex-start' 	=> [
							'title' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-center',
						],
						'flex-end' 		=> [
							'title' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-right',
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-load-button' => 'display: flex; justify-content: {{VALUE}};'
					],
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_button!' ) => '',
					],
				]
			);

			$this->add_responsive_control(
				'infinite_scroll_button_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-load-button__trigger .ee-button-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_button!' ) => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'load_button',
					'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '{{WRAPPER}} .ee-load-button__trigger',
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_button!' ) => '',
					]
				]
			);

			$this->add_control(
				'infinite_scroll_button_border_radius',
				[
					'separator'		=> 'after',
					'type' 			=> Controls_Manager::DIMENSIONS,
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}}  .ee-load-button__trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_button!' ) => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'infinite_scroll_button_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-load-button__trigger',
					'condition' => [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_button!' ) => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'load_button',
					'selector' 	=> '{{WRAPPER}} .ee-load-button__trigger',
					'condition'	=> [
						$this->get_control_id( 'infinite_scroll!' ) => '',
						$this->get_control_id( 'infinite_scroll_button!' ) => '',
					],
				]
			);

			$this->start_controls_tabs( 'infinite_scroll_button_tabs_hover' );

			$this->start_controls_tab( 'infinite_scroll_button_tab_default', [
				'label' 	=> __( 'Default', 'landtech-extras-for-elementor' ),
				'condition' => [
					$this->get_control_id( 'infinite_scroll!' ) => '',
					$this->get_control_id( 'infinite_scroll_button!' ) => '',
				],
			] );

				$this->add_control(
					'infinite_scroll_button_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-load-button__trigger' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'infinite_scroll!' ) => '',
							$this->get_control_id( 'infinite_scroll_button!' ) => '',
						],
					]
				);

				$this->add_control(
					'infinite_scroll_button_background_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-load-button__trigger' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'infinite_scroll!' ) => '',
							$this->get_control_id( 'infinite_scroll_button!' ) => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'infinite_scroll_button_tab_hover', [
				'label' 	=> __( 'Hover', 'landtech-extras-for-elementor' ),
				'condition' => [
					$this->get_control_id( 'infinite_scroll!' ) => '',
					$this->get_control_id( 'infinite_scroll_button!' ) => '',
				],
			] );

				$this->add_control(
					'infinite_scroll_button_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-load-button__trigger:hover' => 'color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'infinite_scroll!' ) => '',
							$this->get_control_id( 'infinite_scroll_button!' ) => '',
						],
					]
				);

				$this->add_control(
					'infinite_scroll_button_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-load-button__trigger:hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							$this->get_control_id( 'infinite_scroll!' ) => '',
							$this->get_control_id( 'infinite_scroll_button!' ) => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

	}

	/**
	 * Before Loop
	 *
	 * Executes before the loop is started
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function before_loop() {
		$this->render_filters();
		parent::before_loop();

		add_filter( 'landtech_extras/widgets/posts/item_classes', [ $this, 'filter_item_classes' ], 10, 3 );
	}

	/**
	 * Before Loop
	 *
	 * Executes after the loop has ended
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function after_loop() {
		remove_filter( 'landtech_extras/widgets/posts/item_classes', [ $this, 'filter_item_classes' ] );

		parent::after_loop();

		if ( 'yes' === $this->get_instance_value( 'infinite_scroll' ) || 'yes' === $this->get_instance_value( 'pagination' ) ) {

			$this->parent->add_render_attribute( 'pagination', [
				'aria-label' 	=> __( 'Pagination', 'landtech-extras-for-elementor' ),
				'class' 		=> 'ee-pagination',
				'role' 			=> 'navigation',
			] );

			if ( 'yes' === $this->get_instance_value('pagination_multiple') ) {
				add_filter( 'landtech_extras/widgets/posts/pagination_link', [ $this->parent, 'filter_page_link' ], 10, 2 );
			}

			if ( 'yes' === $this->get_instance_value('infinite_scroll') ) {
				$this->render_infinite_scroll();
				$this->render_load_status();
				$this->render_load_button();
			} else {
				$this->render_pagination();
			}

			if ( 'yes' === $this->get_instance_value('pagination_multiple') ) {
				remove_filter( 'landtech_extras/widgets/posts/pagination_link', [ $this->parent, 'filter_page_link' ] );
			}
		}
	}

	/**
	 * Filter for item classes
	 * 
	 *
	 * @since  2.2.28
	 * @return array
	 */
	public function filter_item_classes( $classes, $post, $settings ) {
		// Generate array with class names from filters
		if ( isset( $post->filters ) ) {
			foreach ( $post->filters as $filter ) {
				$classes[] = 'ee-filter-' . $filter->term_id;
			}
		}

		return $classes;
	}

	/**
	 * Render Filters
	 * 
	 * Outputs filters from taxonomy terms
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_filters() {

		$taxonomy = $this->get_instance_value( 'filters_taxonomy' );

		if ( '' === $this->get_instance_value( 'filters' ) || ! $taxonomy )
			return;
		
		$this->parent->set_filters( $taxonomy );
		
		$filters 					= $this->parent->get_filters();
		$default_filter_control_id 	= $this->get_control_id( 'filters_taxonomy_' . str_replace( '-', '_', $taxonomy ) );
		$default_filter 			= $this->parent->get_settings( $default_filter_control_id );

		if ( empty( $filters ) )
			return;

		$this->parent->add_render_attribute( [
			'filters' => [
				'class' => [
					'ee-filters',
					'ee-filters--' . $taxonomy,
				],
			],
			'filter-all' => [
				'class' => [
					'ee-filters__item',
					'o-nav__item',
				],
			],
			'filter-count' => [
				'class' => [
					'ee-filters__item__count',
				],
			],
			$this->get_control_id( 'filters_all_text' ) => [
				'data-filter' => '*',
			],
		] );

		if ( '' === $default_filter ) {
			$this->parent->add_render_attribute( $this->get_control_id( 'filters_all_text' ), 'class', 'ee--active' );
		}

		?><ul <?php $this->parent->print_render_attribute_string( 'filters' ); ?>>

			<?php if ( $this->get_instance_value( 'filters_all_show' ) ) : ?>
			<li <?php $this->parent->print_render_attribute_string( 'filter-all' ); ?>><a <?php $this->parent->print_render_attribute_string( $this->get_control_id( 'filters_all_text' ) ); ?>>
				<?php echo esc_html( $this->get_instance_value( 'filters_all_text' ) ); ?>
			</a></li>
			<?php endif; ?>

			<?php foreach ( $filters as $filter ) {

				$filter_term_key = 'filter-term-' . $filter->term_id;
				$filter_link_key = 'filter-link-' . $filter->term_id;

				$this->parent->add_render_attribute( [
					$filter_term_key => [
						'class' => [
							'ee-filters__item',
							'o-nav__item',
							'ee-term',
							'ee-term--' . $filter->slug,
						],
					],
					$filter_link_key => [
						'data-filter' 	=> '.ee-filter-' . $filter->term_id,
						'class' 		=> 'ee-term__link'
					],
				] );

				if ( $filter->slug === $default_filter ) {
					$this->parent->add_render_attribute( $filter_link_key, 'class', 'ee--active' );
				}

				?><li <?php $this->parent->print_render_attribute_string( $filter_term_key ); ?>>
					<a <?php $this->parent->print_render_attribute_string( $filter_link_key ); ?>>
						<?php echo esc_html( $filter->name ); ?>
						<?php if ( $this->parent->get_settings( $this->get_control_id( 'filters_show_count' ) ) ) { ?>
						<span <?php $this->parent->print_render_attribute_string( 'filter-count' ); ?>>
							<?php echo esc_html( (string) $filter->count ); ?>
						</span>
						<?php } ?>
					</a>
				</li>
			<?php } ?>
		</ul><?php

		if ( 'yes' === $this->parent->get_settings( $this->get_control_id( 'filters_show_all' ) ) && 'yes' === $this->parent->get_settings( $this->get_control_id( 'infinite_scroll' ) ) ) {
			$this->render_filters_not_found();
		}
	}

	/**
	 * Render Filters Not Found
	 * 
	 * Outputs html for message to be displayed when no filters are found
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_filters_not_found() {
		$this->parent->add_render_attribute( 'filters-not-found', [
			'class' => [
				'ee-grid__notice',
				'ee-grid__notice--not-found',
				'ee-text--center'
			],
		] );

		?><p <?php $this->parent->print_render_attribute_string( 'filters-not-found' ); ?>>
			<?php echo esc_html( $this->parent->get_settings( $this->get_control_id( 'filters_not_found_text' ) ) ); ?>
		</p><?php
	}

	/**
	 * Render Load Status
	 * 
	 * The status of the infinite scroll loading process
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function render_load_status() {

		if ( 'yes' !== $this->get_instance_value( 'infinite_scroll_status' ) )
			return;

		$this->parent->add_render_attribute( [
			'status' => [
				'class' => 'ee-load-status',
			],
			'status-request' => [
				'class' => [
					'ee-load-status__request',
					'infinite-scroll-request',
				],
			],
			'status-last' => [
				'class' => [
					'ee-load-status__last',
					'infinite-scroll-last',
				],
			],
			'status-error' => [
				'class' => [
					'ee-load-status__error',
					'infinite-scroll-error',
				],
			],
		] );

		?><div <?php $this->parent->print_render_attribute_string( 'status' ); ?>>
			<div <?php $this->parent->print_render_attribute_string( 'status-request' ); ?>>
				<?php
					if ( 'text' === $this->get_instance_value( 'infinite_scroll_loading_type' ) ) {
						echo esc_html( $this->get_instance_value( 'infinite_scroll_loading_text' ) );
					} elseif ( 'loader' === $this->get_instance_value( 'infinite_scroll_loading_type' ) ) {
						$this->render_loading_svg();
					}
				?>
			</div>
			<div <?php $this->parent->print_render_attribute_string( 'status-last' ); ?>>
				<?php echo esc_html( $this->get_instance_value( 'infinite_scroll_last_text' ) ); ?>
			</div>
			<div <?php $this->parent->print_render_attribute_string( 'status-error' ); ?>>
				<?php echo esc_html( $this->get_instance_value( 'infinite_scroll_error_text' ) ); ?>
			</div>
		</div><?php

	}

	/**
	 * Render Loading SVG
	 * 
	 * Svg code for the loading state of infinite scroll
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_loading_svg() {

		$allowed_loaders = array( 'track', 'circle', 'bars-equal', 'bars-flex' );
		$loader_filename = 'track';
		$requested       = $this->get_instance_value( 'infinite_scroll_loading_loader' );

		if ( is_string( $requested ) && in_array( $requested, $allowed_loaders, true ) ) {
			$loader_filename = $requested;
		}

		$svg_path = LANDTECH_EXTRAS_PATH . 'assets/shapes/loader-' . $loader_filename . '.svg';

		if ( is_readable( $svg_path ) ) {
			include $svg_path;
		}
	}

	/**
	 * Render Load Button
	 * 
	 * Markup to display the load more button for infinite scroll
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function render_load_button() {

		if ( '' === $this->get_instance_value( 'infinite_scroll_button' ) || 2 > $this->parent->get_query()->max_num_pages ) {
			return;
		}

		$this->parent->add_render_attribute( [
			'load' => [
				'class' => [
					'ee-load-button',
				],
			],
			'load-button' => [
				'class' => [
					'ee-load-button__trigger',
					'ee-load-button__trigger--' . $this->parent->get_id(),
					'ee-button',
					'ee-size-sm',
				],
				'href' => '',
			],
			'load-button-content-wrapper' => [
				'class' => 'ee-button-content-wrapper',
			],
			'load-button-text' => [
				'class' => 'ee-button-text',
			],
		] );

		?><div <?php $this->parent->print_render_attribute_string( 'load' ); ?>>
			<a <?php $this->parent->print_render_attribute_string( 'load-button' ); ?>>
				<span <?php $this->parent->print_render_attribute_string( 'load-button-content-wrapper' ); ?>>
					<span <?php $this->parent->print_render_attribute_string( 'load-button-text' ); ?>>
						<?php echo esc_html( $this->get_instance_value( 'infinite_scroll_button_text' ) ); ?>
					</span>
				</span>
			</a>
		</div><?php

	}

	/**
	 * Render Infinite Scroll
	 *
	 * Renders links used for infinite scroll to
	 * fetch next pages and load content into the
	 * list of posts
	 *
	 * @since  2.2.8
	 * @return void
	 */
	public function render_infinite_scroll() {

		// Prevent output when using infinite scroll in edit mode
		if ( $this->parent->_is_edit_mode ) {
			return;
		}

		$this->parent->add_render_attribute( 'pagination', 'class', 'ee-pagination--is' );

		?><nav <?php $this->parent->print_render_attribute_string( 'pagination' ); ?>><?php
			$this->parent->render_pagination_link( $this->get_instance_value('pagination_next_label'), 'next' );
		?></nav><?php
	}

	/**
	 * Render Pagination
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function render_pagination() {
		$limit = $this->parent->get_query()->max_num_pages;

		if ( empty( $this->get_instance_value('pagination') ) || $limit < 2 ) {
			return;
		}

		$custom_limit 	= $this->get_instance_value('pagination_page_limit');
		$has_next_prev 	= 'yes' === $this->get_instance_value('pagination_prev_next');
		$has_numbers 	= 'yes' === $this->get_instance_value('pagination_numbers');
		$multiple 		= 'yes' === $this->get_instance_value('pagination_multiple');

		if ( ! empty( $custom_limit ) ) {
			$limit = min( $custom_limit, $limit );
		}

		?><nav <?php $this->parent->print_render_attribute_string('pagination'); ?>><?php

			if ( $has_next_prev ) {
				$this->parent->render_pagination_link( $this->get_instance_value('pagination_previous_label'), 'previous', $limit );
			}

			if ( $has_numbers ) {

				/**
				 * Paginate Links Args Filter
				 *
				 * Filters the paginate_links args
				 *
				 * @since 2.2.38
				 * @param array 			$args 	The initial args
				 */
				$paginate_args = apply_filters( 'landtech_extras/widgets/posts/paginate_links_args', [
					'type' 					=> 'plain',
					'total' 				=> $limit,
					'current' 				=> $this->parent->get_current_page(),
					'show_all' 				=> 'yes' === $this->get_instance_value('pagination_show_all'),
					'prev_next' 			=> false,
					'before_page_number' 	=> '<span class="elementor-screen-only">' . __( 'Page ', 'landtech-extras-for-elementor' ) . '</span>',
				] );

				/*
				 * On single pages we use a custom format
				 * for pagination based on custom query var
				 */
				if ( Module::is_custom_pagination() ) {
					global $wp_rewrite;

					if ( $wp_rewrite->using_permalinks() ) {
						$paginate_args['base'] = trailingslashit( get_permalink() ) . '%_%';
						$paginate_args['format'] = user_trailingslashit( "%#%", 'single_paged' );
					} else {
						$paginate_args['format'] = "?page=%#%";
					}
				}

				if ( $multiple ) {
					add_filter( 'paginate_links', [ $this, 'add_unique_pagination_query_arg' ] );
				} else {
					add_filter( 'paginate_links', [ $this, 'remove_unique_pagination_query_arg' ] );
				}

				echo wp_kses_post( paginate_links( $paginate_args ) );

				// Remove all filters
				remove_filter( 'paginate_links', [ $this, 'add_unique_pagination_query_arg' ] );
				remove_filter( 'paginate_links', [ $this, 'remove_unique_pagination_query_arg' ] );
			}

			if ( $has_next_prev ) {
				$this->parent->render_pagination_link( $this->get_instance_value('pagination_next_label'), 'next', $limit );
			}

		?></nav><?php
	}

	/**
	 * Filters the paginate_links page numbers links
	 *
	 * Add posts widget id to current pagination numbers links
	 *
	 * @since  2.2.38
	 * @access public
	 * @return string
	 */
	public function add_unique_pagination_query_arg( $link ) {
		return add_query_arg( 'posts', $this->parent->get_id(), $link );
	}

	/**
	 * Filters the paginate_links page numbers links
	 *
	 * Remove posts widget id from current pagination numbers links
	 *
	 * @since  2.2.38
	 * @access public
	 * @return string
	 */
	public function remove_unique_pagination_query_arg( $link ) {
		return filter_input( INPUT_GET, 'posts' ) ? remove_query_arg( 'posts', $link ) : $link;
	}

	/**
	 * Render Scripts
	 *
	 * Editor preview behaviour moved to assets/js/posts-loop-isotope-editor.js (enqueued via LandTechExtrasPlugin).
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function render_scripts() {}
}