<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Map\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Google_Map
 *
 * @since 2.0.0
 */
class Google_Map extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-google-map';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Google Map', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-map';
	}

	/**
	 * Map provider for asset registration (safe during preview before settings hydrate).
	 *
	 * @since 2.3.2
	 * @return string
	 */
	private function get_map_provider_for_assets() {
		if ( function_exists( 'landtech_extras_posts_extra_widget_try_get_settings_for_display' ) ) {
			$provider = landtech_extras_posts_extra_widget_try_get_settings_for_display( $this, 'map_provider' );
			if ( is_string( $provider ) && '' !== $provider ) {
				return $provider;
			}
		}

		return 'google';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public function get_script_depends() {
		$provider = $this->get_map_provider_for_assets();

		if ( 'openstreetmap' === $provider ) {
			return [
				'landtech-extras-leaflet',
				'landtech-extras-jquery-resize',
			];
		}

		return [
			'landtech-extras-gmap3',
			'landtech-extras-google-maps',
			'landtech-extras-jquery-resize',
		];
	}

	/**
	 * @return array
	 */
	public function get_style_depends() {
		$provider = $this->get_map_provider_for_assets();

		if ( 'openstreetmap' === $provider ) {
			return [ 'landtech-extras-leaflet' ];
		}

		return [];
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_pins',
			[
				'label' => __( 'Locations', 'landtech-extras-for-elementor' ),
			]
		);

			$repeater = new Repeater();

			$repeater->start_controls_tabs( 'pins_repeater' );

			$repeater->start_controls_tab( 'pins_pin', [ 'label' => __( 'Pin', 'landtech-extras-for-elementor' ) ] );

				$repeater->add_control(
					'lat',
					[
						'label'		=> __( 'Latitude', 'landtech-extras-for-elementor' ),
						'dynamic'	=> [ 'active' => true ],
						'type' 		=> Controls_Manager::TEXT,
						'default' 	=> '',
					]
				);

				$repeater->add_control(
					'lng',
					[
						'label'		=> __( 'Longitude', 'landtech-extras-for-elementor' ),
						'dynamic'	=> [ 'active' => true ],
						'type' 		=> Controls_Manager::TEXT,
						'default' 	=> '',
					]
				);

				$repeater->add_control(
					'icon',
					[
						'label' 	=> __( 'Icon', 'landtech-extras-for-elementor' ),
						'dynamic'	=> [ 'active' => true ],
						'description' => __( 'IMPORTANT: Your icon image needs to be a square to avoid distortion of the artwork.', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::MEDIA,
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab( 'pins_info', [ 'label' => __( 'Popup', 'landtech-extras-for-elementor' ) ] );

				$repeater->add_control(
					'name',
					[
						'label'		=> __( 'Title', 'landtech-extras-for-elementor' ),
						'dynamic'	=> [ 'active' => true ],
						'type' 		=> Controls_Manager::TEXT,
						'label_block' => true,
						'default' 	=> __( 'Pin', 'landtech-extras-for-elementor' ),
					]
				);

				$repeater->add_control(
					'description',
					[
						'label'		=> __( 'Description', 'landtech-extras-for-elementor' ),
						'dynamic'	=> [ 'active' => true ],
						'type' 		=> Controls_Manager::WYSIWYG,
					]
				);

				$repeater->add_control(
					'trigger',
					[
						'label'		=> __( 'Trigger', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 'click',
						'label_block' => true,
						'options'	=> [
							'click' 	=> __( 'Click', 'landtech-extras-for-elementor' ),
							'auto' 		=> __( 'Auto', 'landtech-extras-for-elementor' ),
							'mouseover' => __( 'Mouse Over', 'landtech-extras-for-elementor' ),
						],
					]
				);

			$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'pins',
				[
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[
							'name' => __( 'Tour Eiffel', 'landtech-extras-for-elementor' ),
							'lat' => '48.8583736',
							'lng' => '2.2922873',
						],
						[
							'name' => __( 'Arc de Triomphe', 'landtech-extras-for-elementor' ),
							'lat' => '48.8737952',
							'lng' => '2.2928335',
						],
						[
							'name' => __( 'Louvre Museum', 'landtech-extras-for-elementor' ),
							'lat' => '48.8606146',
							'lng' => '2.33545',
						],
					],
					'fields' 		=> $repeater->get_controls(),
					'title_field' 	=> '{{{ name }}}',
				]
			);
			
		$this->end_controls_section();

		$this->start_controls_section(
			'section_popups',
			[
				'label' => __( 'Popups', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'popups',
				[
					'label' 		=> __( 'Enable Popups', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'title_tag',
				[
					'label' 	=> __( 'Title Tag', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'options' 	=> [
						'h1' 	=> __( 'H1', 'landtech-extras-for-elementor' ),
						'h2' 	=> __( 'H2', 'landtech-extras-for-elementor' ),
						'h3' 	=> __( 'H3', 'landtech-extras-for-elementor' ),
						'h4' 	=> __( 'H4', 'landtech-extras-for-elementor' ),
						'h5' 	=> __( 'H5', 'landtech-extras-for-elementor' ),
						'h6' 	=> __( 'H6', 'landtech-extras-for-elementor' ),
						'div'	=> __( 'div', 'landtech-extras-for-elementor' ),
						'span' 	=> __( 'span', 'landtech-extras-for-elementor' ),
					],
					'default' => 'h5',
					'condition' => [
						'popups' => 'yes',
					],
				]
			);

			$this->add_control(
				'description_tag',
				[
					'label' 	=> __( 'Description Tag', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'p',
					'options' 	=> [
						'p' 	=> __( 'p', 'landtech-extras-for-elementor' ),
						'div'	=> __( 'div', 'landtech-extras-for-elementor' ),
						'span' 	=> __( 'span', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						'popups' => 'yes',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_map',
			[
				'label' => __( 'Map', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'map_provider',
				[
					'label'   => __( 'Map provider', 'landtech-extras-for-elementor' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'google',
					'options' => [
						'google'        => __( 'Google Maps (API key required)', 'landtech-extras-for-elementor' ),
						'openstreetmap' => __( 'OpenStreetMap (no API key)', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'map_provider_osm_notice',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( 'OpenStreetMap mode uses Leaflet and standard map tiles. Routes, polygons, and Snazzy styling require Google Maps.', 'landtech-extras-for-elementor' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition'       => [
						'map_provider' => 'openstreetmap',
					],
				]
			);

			$this->add_control(
				'heading_center',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Center Map', 'landtech-extras-for-elementor' ),
					'condition'	=> [
						'route'	=> '',
					],
				]
			);

			$this->add_control(
				'fit',
				[
					'label' 		=> __( 'Fit to Locations', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
					'condition'		=> [
						'route'		=> '',
					],
				]
			);

			$this->add_control(
				'lat',
				[
					'label'		=> __( 'Latitude', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic'	=> [ 'active' => true ],
					'default' 	=> '48.8583736',
					'condition'	=> [
						'fit' 	=> '',
						'route'	=> '',
					],
				]
			);

			$this->add_control(
				'lng',
				[
					'label'		=> __( 'Longitude', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic'	=> [ 'active' => true ],
					'default' 	=> '2.2922873',
					'condition'	=> [
						'fit' 	=> '',
						'route'	=> '',
					],
				]
			);

			$this->add_control(
				'zoom',
				[
					'label' 		=> __( 'Zoom', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 10,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 18,
							'step'	=> 1,
						],
					],
					'condition' => [
						'fit' 	=> '',
						'route'	=> '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'heading_settings',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Settings', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
				]
			);

			$this->add_control(
				'map_type',
				[
					'label'		=> __( 'Map Type', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'ROADMAP',
					'options'	=> [
						'ROADMAP' 	=> __( 'Roadmap', 'landtech-extras-for-elementor' ),
						'SATELLITE' => __( 'Satellite', 'landtech-extras-for-elementor' ),
						'TERRAIN' 	=> __( 'Terrain', 'landtech-extras-for-elementor' ),
						'HYBRID' 	=> __( 'Hybrid', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'scrollwheel',
				[
					'label' 		=> __( 'Scrollwheel', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'clickable_icons',
				[
					'label' 		=> __( 'Clickable Icons', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'doubleclick_zoom',
				[
					'label' 		=> __( 'Double Click to Zoom', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'draggable',
				[
					'label' 		=> __( 'Draggable', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Note: Map is not draggable in edit mode.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'keyboard_shortcuts',
				[
					'label' 		=> __( 'Keyboard Shortcuts', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'heading_controls',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Interface', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
				]
			);

			$this->add_control(
				'fullscreen_control',
				[
					'label' 		=> __( 'Fullscreen Control', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'map_type_control',
				[
					'label' 		=> __( 'Map Type Control', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'rotate_control',
				[
					'label' 		=> __( 'Rotate Control', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'scale_control',
				[
					'label' 		=> __( 'Scale Control', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'streetview_control',
				[
					'label' 		=> __( 'Street View Control', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'zoom_control',
				[
					'label' 		=> __( 'Zoom Control', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_polygon',
			[
				'label'     => __( 'Polygon', 'landtech-extras-for-elementor' ),
				'condition' => [
					'map_provider' => 'google',
				],
			]
		);

			$this->add_control(
				'polygon',
				[
					'label' 		=> __( 'Enable', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Draws a polygon on the map by connecting the locations.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_route',
			[
				'label'     => __( 'Route', 'landtech-extras-for-elementor' ),
				'condition' => [
					'map_provider' => 'google',
				],
			]
		);

			$this->add_control(
				'route',
				[
					'label' 		=> __( 'Enable', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Draws a route on the map between the locations.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'route_mode',
				[
					'label' 	=> __( 'Mode', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'DRIVING',
					'options' 	=> [
						'DRIVING' 	=> __( 'Driving', 'landtech-extras-for-elementor' ),
						'WALKING' 	=> __( 'Walking', 'landtech-extras-for-elementor' ),
						'BICYCLING' => __( 'Bicycling', 'landtech-extras-for-elementor' ),
						'TRANSIT' 	=> __( 'Transit', 'landtech-extras-for-elementor' ),
					],
					'condition' 	=> [
						'route!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'route_markers',
				[
					'label' 		=> __( 'Markers', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Enables direction markers to be shown on your route.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'condition' 	=> [
						'route!' => '',
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation',
			[
				'label' => __( 'Navigation', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_responsive_control(
				'navigation',
				[
					'label' 		=> __( 'Enable', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Adds a list which visitors can use to navigate through your locations.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'navigation_zoom',
				[
					'label' 	=> __( 'Zoom Level', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 18,
					],
					'range' 	=> [
						'px' 	=> [
							'max' => 18,
						],
					],
					'condition' => [
						'navigation!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'navigation_hide_on',
				[
					'label' 	=> __( 'Hide On', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'mobile',
					'options' 	=> [
						'' 			=> __( 'None', 'landtech-extras-for-elementor' ),
						'tablet' 	=> __( 'Mobile & Tablet', 'landtech-extras-for-elementor' ),
						'mobile' 	=> __( 'Mobile Only', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						'navigation!' => '',
					],
					'prefix_class' => 'ee-google-map-navigation--hide-',
				]
			);

			$this->add_control(
				'all_text',
				[
					'label'		=> __( 'All label', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::TEXT,
					'default' 	=> __( 'All locations', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'selected_navigation_icon',
				[
					'label' 			=> __( 'Icon', 'landtech-extras-for-elementor' ),
					'type' 				=> Controls_Manager::ICONS,
					'fa4compatibility' 	=> 'navigation_icon',
					'default' 			=> [
						'value' 		=> 'fas fa-map-marker-alt',
						'library' 		=> 'fa-solid',
					],
					'label_block'		=> false,
					'skin' 				=> 'inline',
					'condition' 		=> [
						'navigation!' 	=> '',
					],
				]
			);

			$this->add_control(
				'navigation_icon_align',
				[
					'label' => __( 'Icon Position', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'left',
					'options' => [
						'left' => __( 'Before', 'landtech-extras-for-elementor' ),
						'right' => __( 'After', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						'navigation!' => '',
						'selected_navigation_icon[value]!' => '',
					],
				]
			);

			$this->add_control(
				'navigation_icon_indent',
				[
					'label' => __( 'Icon Spacing', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 50,
						],
					],
					'condition' => [
						'navigation!' => '',
						'selected_navigation_icon[value]!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-icon--right' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-icon--left' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pins',
			[
				'label' => __( 'Pins', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'pin_size',
				[
					'label' 		=> __( 'Size', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'description' 	=> __( 'Note: This setting only applies to custom pins.', 'landtech-extras-for-elementor' ),
					'default' 	=> [
						'size' 	=> 50,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
							'step'	=> 1,
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'pin_position_horizontal',
				[
					'label' 		=> __( 'Horizontal Position', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Note: This setting only applies to custom pins.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'center',
					'options' 		=> [
						'left'    		=> [
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
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'pin_position_vertical',
				[
					'label' 		=> __( 'Vertical Position', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Note: This setting only applies to custom pins.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'top',
					'options' 		=> [
						'top'    		=> [
							'title' 	=> __( 'Top', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-top',
						],
						'middle'    		=> [
							'title' 	=> __( 'Middle', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-middle',
						],
						'bottom' 		=> [
							'title' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-bottom',
						],
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_map',
			[
				'label' => __( 'Map', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'map_style_type',
				[
					'label' => __( 'Add style from', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'api',
					'options' => [
						'api' 	=> __( 'Snazzy Maps API', 'landtech-extras-for-elementor' ),
						'json' 	=> __( 'Custom JSON', 'landtech-extras-for-elementor' ),
					],
					'label_block' => true,
					'frontend_available' => true,
					'condition' => [
						'map_provider' => 'google',
					],
				]
			);

			$sm_endpoint_option = \LandTechExtras\LandTechExtrasPlugin::$instance->settings->get_option( 'snazzy_maps_endpoint', 'landtech_extras_apis', false );

			$this->add_control(
				'map_style_api',
				[
					'label' 				=> __( 'Search Snazzy Maps', 'landtech-extras-for-elementor' ),
					'type' 					=> 'ee-snazzy',
					'placeholder'			=> __( 'Search styles', 'landtech-extras-for-elementor' ),
					'snazzy_options'		=> [
						'endpoint'			=> $sm_endpoint_option ? $sm_endpoint_option : 'explore',
					],
					'default'				=> '',
					'frontend_available' 	=> true,
					'condition'				=> [
						'map_style_type'	=> 'api',
					],
				]
			);

			$this->add_control(
				'map_style_json',
				[
					'label'					=> __( 'Custom JSON', 'landtech-extras-for-elementor' ),
					'description' 			=> sprintf(
						/* translators: 1–2: link markup to Snazzy Maps. */
						__( 'Paste the JSON code for styling the map. You can get it from %1$sSnazzyMaps%2$s or similar services. Note: If you enter an invalid JSON string you\'ll be alerted.', 'landtech-extras-for-elementor' ),
						'<a target="_blank" href="https://snazzymaps.com/explore">',
						'</a>'
					),
					'type' 					=> Controls_Manager::TEXTAREA,
					'default' 				=> '',
					'frontend_available' 	=> true,
					'condition'				=> [
						'map_style_type'	=> 'json',
					],
				]
			);

			$this->add_responsive_control(
				'map_height',
				[
					'label' 		=> __( 'Height', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', 'vh', '%' ],
					'default' 	=> [
						'size' 	=> 400,
					],
					'range' 	=> [
						'vh' 		=> [
							'min' => 0,
							'max' => 100,
						],
						'%' 	=> [
							'min' 	=> 10,
							'max' 	=> 100,
							'step'	=> 1,
						],
						'px' 	=> [
							'min' 	=> 100,
							'max' 	=> 1000,
							'step'	=> 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-google-map' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_polygon',
			[
				'label'     => __( 'Polygon', 'landtech-extras-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'polygon!'     => '',
					'map_provider' => 'google',
				],
			]
		);

			$this->start_controls_tabs( 'polygon_tabs' );

			$this->start_controls_tab( 'polygon_default', [ 'label' => __( 'Default', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'heading_polygon_stroke',
					[
						'type'		=> Controls_Manager::HEADING,
						'label' 	=> __( 'Stroke', 'landtech-extras-for-elementor' ),
						'condition' => [
							'polygon!' => '',
						],
					]
				);

				$this->add_control(
					'polygon_stroke_weight',
					[
						'label' 		=> __( 'Weight', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 2,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 10,
								'step'	=> 1,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_stroke_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_stroke_opacity',
					[
						'label' 		=> __( 'Opacity', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0.8,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 1,
								'step'	=> 0.01,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'heading_polygon_fill',
					[
						'type'		=> Controls_Manager::HEADING,
						'label' 	=> __( 'Fill', 'landtech-extras-for-elementor' ),
						'separator' => 'before',
						'condition' => [
							'polygon!' => '',
						],
					]
				);

				$this->add_control(
					'polygon_fill_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_fill_opacity',
					[
						'label' 		=> __( 'Opacity', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0.35,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 1,
								'step'	=> 0.01,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'polygon_hover', [ 'label' => __( 'Hover', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'heading_polygon_stroke_hover',
					[
						'type'		=> Controls_Manager::HEADING,
						'label' 	=> __( 'Stroke', 'landtech-extras-for-elementor' ),
						'condition' => [
							'polygon!' => '',
						],
					]
				);

				$this->add_control(
					'polygon_stroke_weight_hover',
					[
						'label' 		=> __( 'Weight', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 2,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 10,
								'step'	=> 1,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_stroke_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_stroke_opacity_hover',
					[
						'label' 		=> __( 'Opacity', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0.8,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 1,
								'step'	=> 0.01,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'heading_polygon_fill_hover',
					[
						'type'		=> Controls_Manager::HEADING,
						'label' 	=> __( 'Fill', 'landtech-extras-for-elementor' ),
						'separator' => 'before',
						'condition' => [
							'polygon!' => '',
						],
					]
				);

				$this->add_control(
					'polygon_fill_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'polygon_fill_opacity_hover',
					[
						'label' 		=> __( 'Opacity', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0.35,
						],
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 1,
								'step'	=> 0.01,
							],
						],
						'condition' => [
							'polygon!' => '',
						],
						'frontend_available' => true,
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label' => __( 'Navigation', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation!' => '',
				],
			]
		);

			$this->add_responsive_control(
				'navigation_position',
				[
					'label'		=> __( 'Position', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'top-left',
					'options'	=> [
						'top-left' 		=> __( 'Top Left', 'landtech-extras-for-elementor' ),
						'top-right' 	=> __( 'Top Right', 'landtech-extras-for-elementor' ),
						'bottom-right' 	=> __( 'Bottom Right', 'landtech-extras-for-elementor' ),
						'bottom-left' 	=> __( 'Bottom Left', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
					'prefix_class' => 'ee-google-map-navigation%s--',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_width',
				[
					'label' 		=> __( 'Width', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%' ],
					'range' 		=> [
						'%' 		=> [
							'min' => 0,
							'max' => 100,
						],
						'px' 		=> [
							'min' => 100,
							'max' => 1000,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_margin',
				[
					'label' 		=> __( 'Margin', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation' => 'margin: {{SIZE}}{{UNIT}}; max-height: calc( 100% - {{SIZE}}px * 2 );',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'navigation_background',
				[
					'label' 	=> __( 'Background', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'default'	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-google-map__navigation' => 'background-color: {{VALUE}};',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'navigation_border',
					'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '{{WRAPPER}} .ee-google-map__navigation',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'navigation_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} .ee-google-map__navigation__item:first-child a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 0;',
						'{{WRAPPER}} .ee-google-map__navigation__item:last-child a' => 'border-radius: 0 0 {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'navigation_box_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-google-map__navigation',
					'separator'	=> '',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'heading_navigation_separator',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Separator', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_links_separator_thickness',
				[
					'label' 		=> __( 'Thickness', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation__item:not(:last-child) a' => 'border-bottom: {{SIZE}}px solid;',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'heading_navigation_links',
				[
					'type'		=> Controls_Manager::HEADING,
					'label' 	=> __( 'Links', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'navigation_links_spacing',
				[
					'label' 		=> __( 'Spacing', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default'		=> [
						'size'		=> 0,
					],
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 50,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation__item:not(:last-child)' => 'margin-bottom: {{SIZE}}px;',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'navigation_links_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'selectors' 	=> [
						'{{WRAPPER}} .ee-google-map__navigation__link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'navigation_links_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-google-map__navigation',
					'condition' => [
						'navigation!' => '',
					],
				]
			);

			$this->add_control(
				'navigation_links_text_align',
				[
					'label' 		=> __( 'Align Text', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'left',
					'options' 		=> [
						'left'    		=> [
							'title' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'condition' => [
						'navigation!' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-google-map__navigation__link' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'image',
					'selector' 	=> '{{WRAPPER}} .ee-google-map__navigation__link',
					'separator'	=> '',
				]
			);

			$this->start_controls_tabs( 'navigation_tabs' );

			$this->start_controls_tab( 'navigation_default', [ 'label' => __( 'Default', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'navigation_links_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__link' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_separator_color',
					[
						'label' 	=> __( 'Separator Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__item:not(:last-child) a' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_background',
					[
						'label' 	=> __( 'Background', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__link' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'navigation!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'navigation_hover', [ 'label' => __( 'Hover', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'navigation_links_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__link:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_separator_color_hover',
					[
						'label' 	=> __( 'Separator Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__item:not(:last-child) .ee-google-map__navigation__link:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_background_hover',
					[
						'label' 	=> __( 'Background', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__link:hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'navigation!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'navigation_current', [ 'label' => __( 'Current', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'navigation_links_color_current',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__link,
							 {{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__link:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_separator_color_current',
					[
						'label' 	=> __( 'Separator Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'condition' => [
							'navigation!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__item:not(:last-child) .ee-google-map__navigation__link,
							 {{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__item:not(:last-child) .ee-google-map__navigation__link:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'navigation_links_background_current',
					[
						'label' 	=> __( 'Background', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__link,
							 {{WRAPPER}} .ee-google-map__navigation__item.ee--is-active .ee-google-map__navigation__link:hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'navigation!' => '',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render
	 * 
	 * Render widget contents on frontend
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$plugin   = \LandTechExtras\LandTechExtrasPlugin::$instance;
		$provider = isset( $settings['map_provider'] ) ? $settings['map_provider'] : 'google';

		if ( 'google' === $provider && '' === $plugin->settings->get_option( 'google_maps_api_key', 'landtech_extras_apis', false ) ) {
			$this->render_placeholder( [
				'body' => __( 'You have not set your Google Maps API key.', 'landtech-extras-for-elementor' ),
			] );

			return;
		}

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => [
					'ee-google-map-wrapper',
				],
			],
			'map' => [
				'class' => [
					'ee-google-map',
				],
				'data-lat' => $settings['lat'],
				'data-lng' => $settings['lng'],
			],
			'title' => [
				'class' => 'ee-google-map__pin__title',
			],
			'description' => [
				'class' => 'ee-google-map__pin__description',
			],
		] );

		if ( ! empty( $settings['pins'] ) ) {

			?><div <?php $this->print_render_attribute_string( 'wrapper' ); ?>><?php

				if ( '' !== $settings['navigation'] ) {
					$this->render_navigation();
				}
				
				?><div <?php $this->print_render_attribute_string( 'map' ); ?>>
						
					<?php foreach ( $settings['pins'] as $index => $item ) {

						$key = $this->get_repeater_setting_key( 'pin', 'pins', $index );
						$title_key = $this->get_repeater_setting_key( 'title', 'pins', $index );
						$description_key = $this->get_repeater_setting_key( 'description', 'pins', $index );

						$this->add_render_attribute( [
							$key => [
								'class' => [
									'ee-google-map__pin',
								],
								'data-trigger' 	=> $item['trigger'],
								'data-lat' 		=> $item['lat'],
								'data-lng' 		=> $item['lng'],
								'data-id' 		=> $item['_id'],
							],
						] );

						if ( ! empty( $item['icon']['url'] ) ) {
							$this->add_render_attribute( $key, [
								'data-icon' => esc_url( $item['icon']['url'] ),
							] );
						}

						?><div <?php $this->print_render_attribute_string( $key ); ?>>
							<?php if ( '' !== $settings['popups'] ) {

								$title_tag = $settings['title_tag'];
								$description_tag = $settings['description_tag'];
								
								?><<?php echo esc_html( $this->ltxe_sanitize_heading_tag( $title_tag ) ); ?> <?php $this->print_render_attribute_string( 'title' ); ?>>
									<?php echo wp_kses_post( $item['name'] ); ?>
								</<?php echo esc_html( $this->ltxe_sanitize_heading_tag( $title_tag ) ); ?>>
								<<?php echo esc_html( $this->ltxe_sanitize_heading_tag( $description_tag ) ); ?> <?php $this->print_render_attribute_string( 'description' ); ?>>
									<?php echo wp_kses_post( $item['description'] ); ?>
								</<?php echo esc_html( $this->ltxe_sanitize_heading_tag( $description_tag ) ); ?>>

							<?php } ?>
						</div><?php 
					}

				?></div><?php

			?></div><?php

		}
	}

	/**
	 * Render Navigation
	 * 
	 * Render widget navigation on frontend
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render_navigation() {

		$settings = $this->get_settings_for_display();
		$has_icon = false;

		$this->add_render_attribute( [
			'navigation-wrapper' => [
				'class' => [
					'ee-google-map__navigation',
				],
			],
			'navigation' => [
				'class' => [
					'ee-nav',
					'ee-nav--stacked',
					'ee-google-map__navigation__items',
				],
			],
			'text' => [
				'class' => [
					'ee-google-map__navigation__text'
				],
			],
		] );

		if ( ! empty( $settings['navigation_icon'] ) || ! empty( $settings['selected_navigation_icon']['value'] ) ) {
			$this->add_render_attribute( 'icon', 'class', [
				'ee-button-icon',
				'ee-icon',
				'ee-icon-support--svg',
				'ee-icon--' . $settings['navigation_icon_align'],
			] );

			$has_icon = true;
		}

		?><div <?php $this->print_render_attribute_string( 'navigation-wrapper' ); ?>>
			<ul <?php $this->print_render_attribute_string( 'navigation' ); ?>><?php

				$this->render_all_link( $has_icon );

				foreach ( $settings['pins'] as $index => $item ) {

					$item_key = $this->get_repeater_setting_key( 'item', 'pins', $index );
					$link_key = $this->get_repeater_setting_key( 'link', 'pins', $index );

					$this->add_render_attribute( [
						$item_key => [
							'class' => [
								'ee-google-map__navigation__item',
								'elementor-repeater-item-' . $item['_id'],
							],
							'data-id' => $item['_id'],
						],
						$link_key => [
							'class' => [
								'ee-google-map__navigation__link',
								'ee-button',
								'ee-button-link',
							],
						],
					] );

					?><li <?php $this->print_render_attribute_string( $item_key ); ?>>
						<a <?php $this->print_render_attribute_string( $link_key ); ?>><?php

							if ( $has_icon ) {
								$this->render_navigation_icon();
							}

							?><span <?php $this->print_render_attribute_string( 'text' ); ?>>
								<?php echo esc_html( $item['name'] ); ?>
							</span>
						</a>
					</li><?php 
				} ?>

			</ul>
		</div><?php
	}

	/**
	 * Render Navigation Icon
	 *
	 * @since  2.1.5
	 * @return void
	 */
	protected function render_navigation_icon() {
		$settings = $this->get_settings();

		$migrated = isset( $settings['__fa4_migrated']['selected_navigation_icon'] );
		$is_new = empty( $settings['navigation_icon'] ) && Icons_Manager::is_migration_allowed();
		
		?><span <?php $this->print_render_attribute_string( 'icon' ); ?>><?php
			if ( $is_new || $migrated ) {
				Icons_Manager::render_icon( $settings['selected_navigation_icon'], [ 'aria-hidden' => 'true' ] );
			} else {
				?><i class="<?php echo esc_attr( $settings['navigation_icon'] ); ?>" aria-hidden="true"></i><?php
			}
		?></span><?php
	}

	/**
	 * Render All Link
	 * 
	 * Render widget navigations' "all" link
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render_all_link( $icon = false ) {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
				'all' => [
					'class' => [
						'ee-google-map__navigation__item',
						'ee-google-map__navigation__item--all',
					],
				],
				'link' => [
					'class' => [
						'ee-google-map__navigation__link',
						'ee-button',
						'ee-button-link',
					],
				],
			] );

			?><li <?php $this->print_render_attribute_string( 'all' ); ?>>
				<a <?php $this->print_render_attribute_string( 'link' ); ?>><?php

					if ( $icon ) {
						$this->render_navigation_icon();
					}
					
					?><span <?php $this->print_render_attribute_string( 'text' ); ?>>
						<?php echo esc_html( $settings['all_text'] ); ?>
					</span>
				</a>
			</li><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function content_template() {}
}
