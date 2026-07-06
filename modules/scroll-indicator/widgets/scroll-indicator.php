<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\ScrollIndicator\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\ScrollIndicator\Skins;
use LandTechExtras\Modules\ScrollIndicator\Module as Module;
use LandTechExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Scroll_Indicator
 *
 * @since 2.1.0
 */
class Scroll_Indicator extends Extras_Widget {

	/**
	 * Has template content
	 *
	 * @since  2.1.0
	 * @var    bool
	 */
	protected $_has_template_content = false;

	/**
	 * Nav menu index
	 *
	 * @since  2.1.0
	 * @var    int
	 */
	protected $nav_menu_index = 1;

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-scroll-indicator';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Scroll Indicator', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-scroll-indicator';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  2.1.0
	 * @return array
	 */
	public function get_script_depends() {
		return [
			'landtech-extras-scroll-indicator',
			'landtech-extras-hotips',
		];
	}

	/**
	 * Whether the reload preview is required or not.
	 *
	 * Used to determine whether the reload preview is required.
	 *
	 * @since  2.1.0
	 * @return bool
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register Skins
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_skins() {
		$this->add_skin( new Skins\Skin_List( $this ) );
		$this->add_skin( new Skins\Skin_Bar( $this ) );
		$this->add_skin( new Skins\Skin_Bullets( $this ) );
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function _register_controls() {
		// Content tab
		$this->register_settings_controls();
		$this->register_content_controls();
	}

	/**
	 * Register Settings Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_settings_controls() {
		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'click',
				[
					'label' 		=> __( 'Enable Click to Scroll', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'scroll_offset',
				[
					'label' 	=> __( 'Scroll Offset', 'landtech-extras-for-elementor' ),
					'description' => __( 'Offset for scrolling to element on click.', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'frontend_available' => true,
					'default' 	=> [
						'size' 	=> 0,
						'unit' 	=> 'px',
					],
					'range' => [
						'px' => [
							'min'	=> -200,
							'max' 	=> 200,
							'step' 	=> 1,
						],
					],
					'condition' => [
						'click!' => '',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_content_controls() {
		$this->start_controls_section(
			'section_elements',
			[
				'label' => __( 'Sections', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$repeater = new Repeater();

			$repeater->add_control(
				'selector',
				[
					'label' 		=> __( 'Element ID', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Enter the element CSS ID which you want the indicator to for this section.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'title' 		=> __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'landtech-extras-for-elementor' ),
				]
			);

			$repeater->add_control(
				'title',
				[
					'label'		=> __( 'Title', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 	=> __( 'Element title', 'landtech-extras-for-elementor' ),
				]
			);

			$repeater->add_control(
				'subtitle',
				[
					'label'		=> __( 'Subtitle', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::TEXT,
					'dynamic'		=> [ 'active' => true ],
					'default' 	=> __( 'Element subtitle', 'landtech-extras-for-elementor' ),
				]
			); 

			$repeater->add_control(
				'link',
				[
					'separator'		=> 'before',
					'label' 		=> __( 'Enable Link', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Links only work if Settings > Enable Click to Scroll is disabled', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'label_block' 	=> false,
				]
			);

			$repeater->add_control(
				'url',
				[
					'label' 		=> __( 'URL', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::URL,
					'placeholder' 	=> esc_url( home_url( '/' ) ),
					'dynamic'		=> [ 'active' => true ],
					'label_block' 	=> false,
					'condition'	=> [
						'link!' => '',
					],
				]
			);

			$repeater->add_control(
				'link_new_window',
				[
					'label' 		=> __( 'Open in new window', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
					'condition'	=> [
						'link!' => '',
					],
				]
			);

			$repeater->start_controls_tabs( 'progress' );

			$repeater->start_controls_tab( 'tab_progress_start', [ 'label' => __( 'Start', 'landtech-extras-for-elementor' ) ] );

				$repeater->add_control(
					'progress_start',
					[
						'label' 		=> __( 'Start At', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::CHOOSE,
						'description'	=> __( 'Set when the progress starts. Example: "Top to Top" means progress starts when the top of the window hits the top of section.', 'landtech-extras-for-elementor' ),
						'default'		=> 'top-top',
						'label_block'	=> false,
						'options' 		=> [
							'top-top'    	=> [
								'title' 	=> __( 'Top to Top', 'landtech-extras-for-elementor' ),
								'icon' 		=> 'nicon nicon-top-top',
							],
							'bottom-top'	=> [
								'title' 	=> __( 'Bottom to Top', 'landtech-extras-for-elementor' ),
								'icon' 		=> 'nicon nicon-bottom-top',
							],
						],
					]
				);

				$repeater->add_control(
					'progress_start_offset',
					[
						'label' 	=> __( 'Start Offset', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0,
							'unit' 	=> 'px',
						],
						'range' => [
							'px' => [
								'min'	=> -200,
								'max' 	=> 200,
								'step' 	=> 1,
							],
						],
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab( 'tab_progress_end', [ 'label' => __( 'End', 'landtech-extras-for-elementor' ) ] );

				$repeater->add_control(
					'progress_end',
					[
						'label' 		=> __( 'End At', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::CHOOSE,
						'description'	=> __( 'Set when the progress ends. Example: "Top to Bottom" means progress ends when the top of the window hits the bottom of section.', 'landtech-extras-for-elementor' ),
						'default'		=> 'top-bottom',
						'label_block'	=> false,
						'options' 		=> [
							'top-bottom'	=> [
								'title' 	=> __( 'Top to Bottom', 'landtech-extras-for-elementor' ),
								'icon' 		=> 'nicon nicon-top-bottom',
							],
							'bottom-bottom'	=> [
								'title' 	=> __( 'Bottom to Bottom', 'landtech-extras-for-elementor' ),
								'icon' 		=> 'nicon nicon-bottom-bottom',
							],
						],
					]
				);

				$repeater->add_control(
					'progress_end_offset',
					[
						'label' 	=> __( 'End Offset', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::SLIDER,
						'default' 	=> [
							'size' 	=> 0,
							'unit' 	=> 'px',
						],
						'range' => [
							'px' => [
								'min'	=> -200,
								'max' 	=> 200,
								'step' 	=> 1,
							],
						],
					]
				);

			$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'sections',
				[
					'label' 	=> '',
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[
							'selector'	=> '',
							'title' 	=> __( 'Section', 'landtech-extras-for-elementor' ),
							'subtitle' 	=> __( 'Section subtitle', 'landtech-extras-for-elementor' ),
						],
					],
					'fields' 		=> $repeater->get_controls(),
					'title_field' 	=> '{{{ title }}}',
				]
			);

		$this->end_controls_section();
	}

	/**
	 * parse_text_editor wrapper
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function _parse_text_editor( $content ) {
		return $this->parse_text_editor( $content );
	}

	/**
	 * get_repeater_setting_key wrapper
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function _get_repeater_setting_key( $setting_key, $repeater_key, $repeater_item_index ) {
		return $this->get_repeater_setting_key( $setting_key, $repeater_key, $repeater_item_index );
	}

	/**
	 * Render widget content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render() {

	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function content_template() {}

}