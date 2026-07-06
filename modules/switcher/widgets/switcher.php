<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Switcher\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\Switcher\Skins;
use LandTechExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Switcher
 *
 * @since 1.9.0
 */
class Switcher extends Extras_Widget {

	/**
	 * Has template content
	 *
	 * @since  1.9.0
	 * @var    bool
	 */
	protected $_has_template_content = false;

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  1.9.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-switcher';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  1.9.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Switcher', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  1.9.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-switcher';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  1.9.0
	 * @return array
	 */
	public function get_script_depends() {
		return [
			'landtech-extras-switcher',
			'landtech-extras-parallax-element',
			'landtech-extras-splitting',
			'landtech-extras-jquery-resize',
			'landtech-extras-jquery-appear',
			'landtech-extras-jquery-visible',
		];
	}

	/**
	 * Get Style Depends
	 *
	 * @since  2.2.71
	 * @return array
	 */
	public function get_style_depends() {
		return [
			'landtech-extras-splitting',
		];
	}

	/**
	 * Register Skins
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function register_skins() {
		$this->add_skin( new Skins\Skin_Classic( $this ) );
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function _register_controls() {
		$this->register_content_controls();
		$this->register_settings_controls();
		$this->register_effects_controls();
		$this->register_interaction_controls();
		$this->register_layout_style_controls();
		$this->register_media_style_controls();
		$this->register_title_style_controls();
		$this->register_description_style_controls();
		$this->register_menu_style_controls();
		$this->register_arrows_style_controls();
	}

	/**
	 * Register Content Controls
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function register_content_controls() {

		$this->start_controls_section(
			'section_items',
			[
				'label' => __( 'Content', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'layout',
				[
					'label' 	=> __( 'Skin', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'default',
					'options' 	=> [
						'default' 	=> __( 'Default', 'landtech-extras-for-elementor' ),
						'overlay' 	=> __( 'Overlay', 'landtech-extras-for-elementor' ),
					],
					'prefix_class' => 'ee-switcher-skin--',
				]
			);

			$content = new Repeater();

			$content->start_controls_tabs( 'items_repeater' );

			$content->start_controls_tab( 'tab_content', [ 'label' => __( 'Content', 'landtech-extras-for-elementor' ) ] );

				$content->add_control(
					'image',
					[
						'label' 	=> __( 'Image', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::MEDIA,
						'dynamic' 	=> [ 'active' => true ],
						'default' 	=> [
							'url' 	=> Utils::get_placeholder_image_src(),
						],
					]
				);

				$content->add_control(
					'title',
					[
						'label' 	=> __( 'Title', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::TEXT,
						'dynamic' 	=> [ 'active' => true ],
						'default' 	=> __( 'Content Title', 'landtech-extras-for-elementor' ),
					]
				);

				$content->add_control(
					'description',
					[
						'label' 		=> __( 'Description', 'landtech-extras-for-elementor' ),
						'description'	=> __( 'Remeber to enables the display of description under the Settings section.', 'landtech-extras-for-elementor' ),
						'dynamic' 		=> [ 'active' => true ],
						'type' 			=> Controls_Manager::WYSIWYG,
						'default' 		=> '',
						'rows' 			=> 5,
					]
				);

				$content->add_control(
					'label',
					[
						'label' 	=> __( 'Label', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::TEXT,
						'dynamic' 	=> [ 'active' => true ],
						'default' 	=> __( 'Navigation Label', 'landtech-extras-for-elementor' ),
					]
				);

				$content->add_control(
					'selected_icon',
					[
						'label' => __( 'Icon', 'landtech-extras-for-elementor' ),
						'type' => Controls_Manager::ICONS,
						'skin' => 'inline',
						'label_block' => false,
						'fa4compatibility' => 'icon',
					]
				);

				$content->add_control(
					'icon_align',
					[
						'label' 	=> __( 'Icon Position', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 'left',
						'options' 	=> [
							'left' 		=> __( 'Before', 'landtech-extras-for-elementor' ),
							'right' 	=> __( 'After', 'landtech-extras-for-elementor' ),
						],
						'condition' => [
							'selected_icon[value]!' => '',
						],
					]
				);

				$content->add_control(
					'icon_indent',
					[
						'label' 	=> __( 'Icon Spacing', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::SLIDER,
						'range' 	=> [
							'px' 	=> [
								'min' => 0,
								'max' => 50,
							],
						],
						'condition' => [
							'selected_icon[value]!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-icon--right' => 'margin-left: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-icon--left' => 'margin-right: {{SIZE}}{{UNIT}};',
						],
					]
				);

			$content->end_controls_tab();

			$content->start_controls_tab( 'tab_settings', [ 'label' => __( 'Settings', 'landtech-extras-for-elementor' ) ] );

				$content->add_control(
					'link',
					[
						'label' 		=> __( 'Link', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::URL,
						'dynamic' 		=> [ 'active' => true ],
						'placeholder' 	=> esc_url( home_url( '/' ) ),
						'frontend_available' => true,
					]
				);

			$content->end_controls_tab();

			$content->start_controls_tab( 'tab_style', [ 'label' => __( 'Style', 'landtech-extras-for-elementor' ) ] );

				$content->add_control(
					'background_switcher_heading',
					[
						'label' => __( 'Background Switcher', 'landtech-extras-for-elementor' ),
						'type'	=> Controls_Manager::HEADING,
					]
				);

				$content->add_control(
					'background_switcher_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'frontend_available' => true,
					]
				);

			$content->end_controls_tab();

			$content->end_controls_tabs();

			$this->add_control(
				'items',
				[
					'label' 	=> __( 'Items', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[
							'title' 	=> __( 'Title', 'landtech-extras-for-elementor' ),
							'label' 	=> __( 'Item #1', 'landtech-extras-for-elementor' ),
							'description' => __( 'Lorem ipsum dolor sit amet, dictas evertitur philosophia an duo. At tamquam similique constituam vis, his tale similique disputationi an.', 'landtech-extras-for-elementor' ),
						],
						[
							'title' 	=> __( 'Title', 'landtech-extras-for-elementor' ),
							'label' 	=> __( 'Item #2', 'landtech-extras-for-elementor' ),
							'description' => __( 'Periculis voluptatum vis ad, ex nam alienum iudicabit reprehendunt. Movet iisque voluptatum nec at.', 'landtech-extras-for-elementor' ),
						],
						[
							'title' 	=> __( 'Title', 'landtech-extras-for-elementor' ),
							'label' 	=> __( 'Item #2', 'landtech-extras-for-elementor' ),
							'description' => __( 'Hinc novum id mei, mel nominavi probatus id. Meis iudicabit ei nam, vidit latine atomorum vim at, ne pro purto cotidieque.', 'landtech-extras-for-elementor' ),
						],
					],
					'fields' 		=> $content->get_controls(),
					'title_field' 	=> '{{{ label }}}',
				]
			);

			$this->add_control(
				'linking_heading',
				[
					'label' => __( 'Linking', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'link_to',
				[
					'label' 	=> __( 'Link to', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'custom',
					'options' 	=> [
						'file' 			=> __( 'Media File', 'landtech-extras-for-elementor' ),
						'attachment' 	=> __( 'Attachment Page', 'landtech-extras-for-elementor' ),
						'custom' 		=> __( 'Item URL', 'landtech-extras-for-elementor' ),
					],
				]
			);

			$this->add_control(
				'link_image',
				[
					'label' 		=> __( 'Link Image', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> 'yes',
					'return_value' 	=> 'yes',
					'condition'	=> [
						'layout!' => 'overlay',
					],
				]
			);

			$this->add_control(
				'link_title',
				[
					'label' 		=> __( 'Link Title', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> 'yes',
					'return_value' 	=> 'yes',
				]
			);

			$this->add_control(
				'link_description',
				[
					'label' 		=> __( 'Link Description', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Make sure you don\'t have any links inside your descriptions in order to avoid HTML and display errors', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'yes',
					'condition'		=> [
						'description!' => '',
					],
				]
			);

			$this->add_control(
				'link_open_lightbox',
				[
					'label' 	=> __( 'Lightbox', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'default',
					'separator' => 'before',
					'options' 	=> [
						'default' 	=> __( 'Default', 'landtech-extras-for-elementor' ),
						'yes' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
						'no' 		=> __( 'No', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						'link_to' 		=> 'file',
					],
				]
			);

			$this->add_control(
				'lightbox_slideshow',
				[
					'label' 	=> __( 'Lightbox Slideshow', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'default' 	=> 'yes',
					'condition' => [
						'link_to' 		=> 'file',
						'link_open_lightbox' => ['default', 'yes'],
					],
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register Settings Controls
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function register_settings_controls() {

		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'settings_switcher_heading',
				[
					'label' => __( 'General', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'autoplay',
				[
					'label' 		=> __( 'Autoplay', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'duration',
				[
					'label' 		=> __( 'Autoplay Duration (s)', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'How long should an item stay on screen before being switched.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0.2,
							'max' => 5,
							'step'=> 0.1,
						],
					],
					'condition'		=> [
						'autoplay!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay_preview',
				[
					'label' 		=> __( 'Autoplay in Editor', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'yes',
					'condition'		=> [
						'autoplay!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay_cancel',
				[
					'label' 		=> __( 'Stop on Interaction', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'yes',
					'condition'		=> [
						'autoplay!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'loop',
				[
					'label' 		=> __( 'Loop', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'background_switcher_heading',
				[
					'label' => __( 'Background switcher', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'background_switcher',
				[
					'label' 		=> __( 'Enable', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Turn on changing of widget, section or page background color when switching items.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'background_switcher_notice',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'Select the background color for each item under Content > Item > Style Tab.', 'landtech-extras-for-elementor' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
					'condition' 		=> [
						'background_switcher!' => ''
					],
				]
			);

			$this->add_control(
				'background_switcher_element',
				[
					'label' 	=> __( 'Change Element', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 			=> __( 'Body', 'landtech-extras-for-elementor' ),
						'widget' 	=> __( 'Widget', 'landtech-extras-for-elementor' ),
						'section' 	=> __( 'Section', 'landtech-extras-for-elementor' ),
					],
					'condition' 		=> [
						'background_switcher!' => ''
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'settings_images_heading',
				[
					'label' => __( 'Images', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 		=> 'image', // Actually its `original_image_size`.
					'label' 	=> __( 'Image Size', 'landtech-extras-for-elementor' ),
					'exclude'	=> ['custom'], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control schema.
					'default' 	=> 'full',
				]
			);

			$this->add_control(
				'settings_title_heading',
				[
					'label' => __( 'Title', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'title_heading_tag',
				[
					'label' 	=> __( 'HTML Tag', 'landtech-extras-for-elementor' ),
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
					'default' => 'h1',
				]
			);

			$this->add_control(
				'settings_description_heading',
				[
					'label' => __( 'Description', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'description',
				[
					'label' 		=> __( 'Enable', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Disable this to hide the description wrappers completely.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'yes',
				]
			);

			$this->add_control(
				'settings_navigation_heading',
				[
					'label' => __( 'Navigation', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'menu',
				[
					'label' 	=> __( 'Menu', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'show',
					'tablet_default' 	=> 'show',
					'mobile_default' 	=> 'hide',
					'options' 	=> [
						'show' 	=> __( 'Show', 'landtech-extras-for-elementor' ),
						'hide' 	=> __( 'Hide', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
					'prefix_class' => 'ee-switcher-menu%s-',
				]
			);

			$this->add_responsive_control(
				'arrows',
				[
					'label' 			=> __( 'Arrows', 'landtech-extras-for-elementor' ),
					'type' 				=> Controls_Manager::SELECT,
					'default'			=> 'hide',
					'tablet_default' 	=> 'hide',
					'mobile_default' 	=> 'show',
					'options' 	=> [
						'show' 	=> __( 'Show', 'landtech-extras-for-elementor' ),
						'hide' 	=> __( 'Hide', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
					'prefix_class' => 'ee-switcher-arrows%s-',
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Effects Controls
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function register_effects_controls() {

		$this->start_controls_section(
			'_section_effects',
			[
				'label' => __( 'Effects', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'effect_entrance',
				[
					'label' 		=> __( 'Entrance Animation', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Animate the first item when entering viewport.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> 'yes',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'effect_entrance_preview',
				[
					'label' 		=> __( 'Preview in Editor', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'separator'		=> 'after',
					'condition'		=> [
						'effect_entrance!' => '',
					],
				]
			);

			$this->add_control(
				'speed',
				[
					'label' 		=> __( 'Animation Speed (s)', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'The time it takes for the transition to complete.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0.2,
							'max' => 3,
							'step'=> 0.1,
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'effects_media_heading',
				[
					'label' => __( 'Media', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_control(
				'effect_media',
				[
					'label' 	=> __( 'Media Effect', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'swipeLeft',
					'options' 	=> [
						'coverLeft' 			=> __( 'Cover Left', 'landtech-extras-for-elementor' ),
						'coverRight' 			=> __( 'Cover Right', 'landtech-extras-for-elementor' ),
						'coverBottom' 			=> __( 'Cover Bottom', 'landtech-extras-for-elementor' ),
						'coverTop'	 			=> __( 'Cover Top', 'landtech-extras-for-elementor' ),
						'uncoverLeft' 			=> __( 'Uncover Left', 'landtech-extras-for-elementor' ),
						'uncoverRight' 			=> __( 'Uncover Right', 'landtech-extras-for-elementor' ),
						'uncoverBottom' 		=> __( 'Uncover Bottom', 'landtech-extras-for-elementor' ),
						'uncoverTop' 			=> __( 'Uncover Top', 'landtech-extras-for-elementor' ),
						'fade' 					=> __( 'Fade', 'landtech-extras-for-elementor' ),
						'slideLeft' 			=> __( 'Slide Left', 'landtech-extras-for-elementor' ),
						'slideRight' 			=> __( 'Slide Right', 'landtech-extras-for-elementor' ),
						'slideTop' 				=> __( 'Slide Top', 'landtech-extras-for-elementor' ),
						'slideBottom' 			=> __( 'Slide Bottom', 'landtech-extras-for-elementor' ),
						'flipHorizontal' 		=> __( 'Flip Horizontal', 'landtech-extras-for-elementor' ),
						'flipVertical' 			=> __( 'Flip Vertical', 'landtech-extras-for-elementor' ),
						'swipeLeft' 			=> __( 'Swipe Left', 'landtech-extras-for-elementor' ),
						'swipeRight' 			=> __( 'Swipe Right', 'landtech-extras-for-elementor' ),
						'swipeBottom' 			=> __( 'Swipe Bottom', 'landtech-extras-for-elementor' ),
						'swipeTop' 				=> __( 'Swipe Top', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'effect_media_zoom',
				[
					'label' 		=> __( 'Zoom', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> 'yes',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'conditions'	=> [
						'relation'	=> 'or',
						'terms'		=> [
							[
								'name' 		=> 'effect_media',
								'operator'	=> '!=',
								'value'		=> 'flipVertical',
							],
							[
								'name' 		=> 'effect_media',
								'operator'	=> '!=',
								'value'		=> 'flipHorizontal',
							],
						]
					],
				]
			);

			$this->add_control(
				'effects_text_heading',
				[
					'label' => __( 'Title', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_control(
				'effect_title',
				[
					'label' 	=> __( 'Title Effect', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'slideLeft',
					'options' 	=> [
						'slideLeft' 	=> __( 'Slide Left', 'landtech-extras-for-elementor' ),
						'slideRight' 	=> __( 'Slide Right', 'landtech-extras-for-elementor' ),
						'slideTop' 		=> __( 'Slide Top', 'landtech-extras-for-elementor' ),
						'slideBottom' 	=> __( 'Slide Bottom', 'landtech-extras-for-elementor' ),
						'fade' 			=> __( 'Fade', 'landtech-extras-for-elementor' ),
						'scale'			=> __( 'Scale', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'effect_title_stagger',
				[
					'label' 		=> __( 'Character Delay', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Wether or not to animate each character with a slight delay.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> 'yes',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register Interaction Controls
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function register_interaction_controls() {
		$this->start_controls_section(
			'section_interactions',
			[
				'label' => __( 'Interactions', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'pan_heading',
				[
					'label' => __( 'Mouse Parallax', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'parallax_enable',
				[
					'label' 		=> __( 'Enable', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'parallax_amount',
				[
					'label' 		=> __( 'Pan Amount', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0.1,
							'max' => 1,
							'step'=> 0.01,
						],
					],
					'frontend_available' => true,
					'condition'	=> [
						'parallax_enable!'	=> '',
					],
				]
			);

			$this->add_control(
				'parallax_pan_axis',
				[
					'label' 	=> __( 'Pan Axis', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'both',
					'options' 	=> [
						'both' 			=> __( 'Both', 'landtech-extras-for-elementor' ),
						'vertical' 		=> __( 'Vertical', 'landtech-extras-for-elementor' ),
						'horizontal' 	=> __( 'Horizontal', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
					'condition'	=> [
						'parallax_enable!'	=> '',
					],
				]
			);

			// $this->add_control(
			// 	'tilt_heading',
			// 	[
			// 		'label' => __( 'Tilt', 'landtech-extras-for-elementor' ),
			// 		'type'	=> Controls_Manager::HEADING,
			// 		'separator' => 'before',
			// 		'condition'	=> [
			// 			'skin!' => 'overlay',
			// 		],
			// 	]
			// );

			// $this->add_control(
			// 	'tilt_enable',
			// 	[
			// 		'label'			=> __( 'Enable', 'landtech-extras-for-elementor' ),
			// 		'type' 			=> Controls_Manager::SWITCHER,
			// 		'default' 		=> '',
			// 		'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
			// 		'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
			// 		'return_value' 	=> 'yes',
			// 		'frontend_available' => true,
			// 		'condition'	=> [
			// 			'skin!' => 'overlay',
			// 		],
			// 	]
			// );

			// $this->add_control(
			// 	'tilt_axis',
			// 	[
			// 		'label'			=> __( 'Axis', 'landtech-extras-for-elementor' ),
			// 		'type' 			=> Controls_Manager::SELECT,
			// 		'default' 		=> '',
			// 		'options' 			=> [
			// 			'' 		=> __( 'Both', 'landtech-extras-for-elementor' ),
			// 			'x' 	=> __( 'X Only', 'landtech-extras-for-elementor' ),
			// 			'y' 	=> __( 'Y Only', 'landtech-extras-for-elementor' ),
			// 		],
			// 		'frontend_available' => true,
			// 		'condition' => [
			// 			'skin!' => 'overlay',
			// 			'tilt_enable' => 'yes',
			// 		],
			// 	]
			// );

			// $this->add_control(
			// 	'tilt_amount',
			// 	[
			// 		'label' 	=> __( 'Amount', 'landtech-extras-for-elementor' ),
			// 		'type' 		=> Controls_Manager::SLIDER,
			// 		'range' 	=> [
			// 			'px' 	=> [
			// 				'min' => 10,
			// 				'max' => 40,
			// 			],
			// 		],
			// 		'default' 	=> [
			// 			'size' 	=> 20,
			// 		],
			// 		'frontend_available' => true,
			// 		'condition' => [
			// 			'skin!' => 'overlay',
			// 			'tilt_enable' => 'yes',
			// 		],
			// 	]
			// );

			// $this->add_control(
			// 	'tilt_scale',
			// 	[
			// 		'label' 	=> __( 'Scale', 'landtech-extras-for-elementor' ),
			// 		'type' 		=> Controls_Manager::SLIDER,
			// 		'range' 	=> [
			// 			'px' 	=> [
			// 				'min' 	=> 1,
			// 				'max' 	=> 1.5,
			// 				'step'	=> 0.01,
			// 			],
			// 		],
			// 		'default' 		=> [
			// 			'size' 		=> 1.05,
			// 		],
			// 		'frontend_available' => true,
			// 		'condition' => [
			// 			'skin!' => 'overlay',
			// 			'tilt_enable' => 'yes',
			// 		],
			// 	]
			// );

			// $this->add_control(
			// 	'tilt_speed',
			// 	[
			// 		'label' 	=> __( 'Speed', 'landtech-extras-for-elementor' ),
			// 		'type' 		=> Controls_Manager::SLIDER,
			// 		'range' 	=> [
			// 			'px' 	=> [
			// 				'min' 	=> 100,
			// 				'max' 	=> 1000,
			// 				'step'	=> 50,
			// 			],
			// 		],
			// 		'default' 		=> [
			// 			'size' 		=> 800,
			// 		],
			// 		'frontend_available' => true,
			// 		'condition' => [
			// 			'skin!' => 'overlay',
			// 			'tilt_enable' => 'yes',
			// 		],
			// 	]
			// );

		$this->end_controls_section();
	}

	/**
	 * Register Layout Style Controls
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function register_layout_style_controls() {
		$this->start_controls_section(
			'section_style_layout',
			[
				'label' => __( 'Layout', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'layout_stack',
				[
					'label' 	=> __( 'Stack on', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'tablet',
					'options' 	=> [
						'mobile' 	=> __( 'Mobile', 'landtech-extras-for-elementor' ),
						'tablet' 	=> __( 'Tablet', 'landtech-extras-for-elementor' ),
						'desktop' 	=> __( 'Desktop', 'landtech-extras-for-elementor' ),
					],
					'condition'		=> [
						'layout' => 'default',
					],
					'prefix_class' => 'ee-switcher-stack-'
				]
			);

			$this->add_responsive_control(
				'layout_height',
				[
					'label' 		=> __( 'Min Height', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 800,
						],
						'vh' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'condition'		=> [
						'layout' => 'overlay',
					],
					'size_units' => [ 'px', 'vh' ],
					'selectors'	=> [
						'{{WRAPPER}}.ee-switcher-skin--overlay .ee-switcher__wrapper' => 'min-height: {{SIZE}}{{UNIT}}',
					]
				]
			);

			$this->add_responsive_control(
				'layout_spacing',
				[
					'label' 	=> __( 'Spacing', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors'	=> [
						'{{WRAPPER}}.ee-switcher-skin--default .ee-switcher__wrapper' => 'margin-left: -{{SIZE}}px;',
						'{{WRAPPER}}.ee-switcher-skin--default .ee-switcher__media-wrapper,
						 {{WRAPPER}}.ee-switcher-skin--default .ee-switcher__content-wrapper' => 'padding-left: {{SIZE}}px;',
					],
					'condition'	=> [
						'layout' => 'default',
					]
				]
			);

			$this->add_responsive_control(
				'layout_content_padding',
				[
					'label' 		=> __( 'Content Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-switcher__content' => 'padding: {{SIZE}}px',
					],
				]
			);

			$this->add_control(
				'layout_vertical_aligment',
				[
					'label' 		=> __( 'Vertical Align', 'landtech-extras-for-elementor' ),
					'label_block' 	=> false,
					'type' 			=> Controls_Manager::CHOOSE,
					'options' 		=> [
						'flex-start' 	=> [
							'title' 	=> __( 'Top', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-top',
						],
						'center' 		=> [
							'title' 	=> __( 'Middle', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-middle',
						],
						'flex-end' 		=> [
							'title' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-bottom',
						],
						'stretch' 		=> [
							'title' 	=> __( 'Stretch', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-stretch',
						],
					],
					'default' 		=> 'center',
					'selectors'		=> [
						'{{WRAPPER}} .ee-switcher__wrapper' => 'align-items: {{VALUE}};',
						'{{WRAPPER}} .ee-switcher__content-wrapper' => 'align-items: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'layout_reverse',
				[
					'label' 		=> __( 'Reverse', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default'		=> '',
					'return_value' 	=> 'reverse',
					'prefix_class'	=> 'ee-switcher-layout--',
					'condition'		=> [
						'layout' => 'default',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Media Style Controls
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function register_media_style_controls() {

		$this->start_controls_section(
			'section_style_media',
			[
				'label' => __( 'Media', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'media_width',
				[
					'label' 		=> __( 'Width', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 500,
						],
						'%' 		=> [
							'min' => 10,
							'max' => 80,
						],
					],
					'condition'		=> [
						'layout' => 'default',
					],
					'size_units' 	=> [ 'px', '%' ],
					'selectors'		=> [
						'{{WRAPPER}}.ee-switcher-skin--default .ee-switcher__media-wrapper' => 'min-width: {{SIZE}}{{UNIT}}',
					],
					'condition'		=> [
						'layout' => 'default',
					],
				]
			);

			$this->add_responsive_control(
				'media_height',
				[
					'label' 		=> __( 'Min Height', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 800,
						],
						'vh' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'condition'		=> [
						'layout' => 'default',
					],
					'size_units' => [ 'px', 'vh' ],
					'selectors'	=> [
						'{{WRAPPER}} .ee-switcher__media' => 'min-height: {{SIZE}}{{UNIT}}',
					],
					'condition'		=> [
						'layout' => 'default',
					],
				]
			);

			$this->add_responsive_control(
				'media_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 20,
						],
					],
					'condition'		=> [
						'layout' => 'default',
					],
					'selectors'	=> [
						'{{WRAPPER}} .ee-switcher__media__items' => 'border-radius: {{SIZE}}px',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'media',
					'selector' 	=> '{{WRAPPER}} .ee-switcher__media',
					'exclude'	=> [ 'box_shadow_position' ], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control schema.
					'separator'	=> '',
				]
			);

			$this->add_control(
				'media_overlay',
				[
					'label' => __( 'Overlay', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'condition'		=> [
						'layout' => 'overlay',
					],
					'separator' => 'before',
				]
			);

			$this->add_control(
				'media_overlay_color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-media__overlay' => 'background-color: {{VALUE}};',
					],
					'condition'		=> [
						'layout' => 'overlay',
					],
				]
			);

			$this->add_control(
				'media_overlay_blend',
				[
					'label' 		=> __( 'Blend mode', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'normal',
					'options' => [
						'normal'			=> __( 'Normal', 'landtech-extras-for-elementor' ),
						'multiply'			=> __( 'Multiply', 'landtech-extras-for-elementor' ),
						'screen'			=> __( 'Screen', 'landtech-extras-for-elementor' ),
						'overlay'			=> __( 'Overlay', 'landtech-extras-for-elementor' ),
						'darken'			=> __( 'Darken', 'landtech-extras-for-elementor' ),
						'lighten'			=> __( 'Lighten', 'landtech-extras-for-elementor' ),
						'color'				=> __( 'Color', 'landtech-extras-for-elementor' ),
						'color-dodge'		=> __( 'Color Dodge', 'landtech-extras-for-elementor' ),
						'hue'				=> __( 'Hue', 'landtech-extras-for-elementor' ),
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-switcher__media__overlay' => 'mix-blend-mode: {{VALUE}};',
					],
					'condition'		=> [
						'layout' => 'overlay',
					],
				]
			);

			$this->add_control(
				'media_overlay_blend_notice',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> sprintf(
						/* translators: 1–2: link markup to caniuse.com mix-blend-mode. */
						__( 'Please check blend mode support for your browser %1$s here %2$s', 'landtech-extras-for-elementor' ),
						'<a href="https://caniuse.com/#search=mix-blend-mode" target="_blank">',
						'</a>'
					),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
					'condition' 		=> [
						'overlay_blend!' => 'normal',
						'layout' => 'overlay',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				[
					'name' => 'media_css_filters',
					'selector' => '{{WRAPPER}} .ee-switcher__media',
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Register Title Style Controls
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function register_title_style_controls() {

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => __( 'Title', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'title_color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-switcher__title' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'title_align',
				[
					'label' 		=> __( 'Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
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
					'selectors'		=> [
						'{{WRAPPER}} .ee-switcher__titles' 	=> 'text-align: {{VALUE}};',
					]
				]
			);

			$this->add_responsive_control(
				'title_overlap',
				[
					'label' 		=> __( 'Overlap', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'vw' 		=> [
							'min' => 0,
							'max' => 50,
						],
						'px' 		=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'size_units' => [ 'vw', 'px' ],
					'condition'		=> [
						'layout' => 'default',
					],
					'selectors'		=> [
						'{{WRAPPER}} .ee-switcher__titles' 								=> 'margin-left: -{{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.ee-switcher-layout--reverse .ee-switcher__titles' 	=> 'margin-left: 0px; margin-right: -{{SIZE}}{{UNIT}};',
					]
				]
			);

			$this->add_responsive_control(
				'title_distance',
				[
					'label' 		=> __( 'Distance', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-switcher__titles' => 'margin-top: {{SIZE}}px',
					],
				]
			);

			$this->add_responsive_control(
				'title_font_size',
				[
					'label' 		=> __( 'Font Size (vw)', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 30,
						],
					],
					'default'		=> [
						'size'		=> 8,
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-switcher__title' => 'font-size: {{SIZE}}vw',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'exclude'	=> [ 'font_size', 'font_style' ], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control schema.
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' 	=> '{{WRAPPER}} .ee-switcher__title',
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register Description Style Controls
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function register_description_style_controls() {

		$this->start_controls_section(
			'section_style_description',
			[
				'label' => __( 'Description', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'description!' => '',
				],
			]
		);

			$this->add_control(
				'description_color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-switcher__descriptions__description' => 'color: {{VALUE}};',
					],
					'condition' => [
						'description!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'description_align',
				[
					'label' 		=> __( 'Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
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
						'description!' => '',
					],
					'selectors'		=> [
						'{{WRAPPER}} .ee-switcher__descriptions__description' 	=> 'text-align: {{VALUE}};',
					]
				]
			);

			$this->add_responsive_control(
				'description_overlap',
				[
					'label' 		=> __( 'Overlap', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'vw' 		=> [
							'min' => 0,
							'max' => 50,
						],
						'px' 		=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'size_units' => [ 'vw', 'px' ],
					'condition'		=> [
						'layout' => 'default',
						'description!' => '',
					],
					'selectors'		=> [
						'{{WRAPPER}} .ee-switcher__descriptions' => 'margin-left: -{{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.ee-switcher-layout--reverse .ee-switcher__descriptions' => 'margin-left: 0px; margin-right: -{{SIZE}}{{UNIT}};',
					]
				]
			);

			$this->add_responsive_control(
				'description_distance',
				[
					'label' 		=> __( 'Distance', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'condition' => [
						'description!' => '',
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-switcher__descriptions' => 'margin-top: {{SIZE}}px',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'description_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-switcher__descriptions__description',
					'condition' => [
						'description!' => '',
					],
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register Menu Style Controls
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function register_menu_style_controls() {

		$this->start_controls_section(
			'section_style_menu',
			[
				'label' => __( 'Menu', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'menu_layout_heading',
				[
					'label' => __( 'Layout', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'menu_direction',
				[
					'label' 		=> __( 'Direction', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'row',
					'options' 		=> [
						'row'    	=> [
							'title' 	=> __( 'Vertical', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'nicon nicon-block',
						],
						'column' 		=> [
							'title' 	=> __( 'Horizontal', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'nicon nicon-inline',
						],
					],
					'label_block'	=> false,
				]
			);

			$this->add_responsive_control(
				'menu_align',
				[
					'label' 		=> __( 'Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'justify',
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
					'label_block'		=> false,
					'prefix_class' 		=> 'ee-switcher-menu%s-align--',
				]
			);

			$this->add_responsive_control(
				'menu_text_align',
				[
					'label' 		=> __( 'Text Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'flex-start'	=> [
							'title' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'flex-end' 		=> [
							'title' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'condition'			=> [
						'menu_align' => 'justify',
					],
					'label_block'		=> false,
					'selectors'			=> [
						'{{WRAPPER}} .ee-switcher__nav__item' => 'justify-content: {{VALUE}};',
					]
				]
			);

			$this->add_responsive_control(
				'menu_distance',
				[
					'label' 		=> __( 'Distance', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-switcher__nav' => 'margin-top: {{SIZE}}px',
					],
				]
			);

			$this->add_control(
				'menu_items_heading',
				[
					'label' => __( 'Items', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'menu_items_spacing',
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
						'{{WRAPPER}} .ee-nav.ee-nav--stacked .ee-switcher__nav__item' 	=> 'margin-bottom: {{SIZE}}px',
						'{{WRAPPER}} .ee-nav.ee-nav--inline' 							=> 'margin-left: -{{SIZE}}px; margin-bottom: {{SIZE}}px',
						'{{WRAPPER}} .ee-nav.ee-nav--inline .ee-switcher__nav__item' 	=> 'margin-left: {{SIZE}}px',
					],
				]
			);

			$this->add_responsive_control(
				'menu_items_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-switcher__nav__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'menu_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-switcher__nav__item',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'menu',
					'selector' 		=> '{{WRAPPER}} .ee-switcher__nav__item',
				]
			);

			$this->update_control( 'menu_transition', array(
				'default' => 'custom',
			));

			$this->start_controls_tabs( 'menu_items_tabs' );

			$this->start_controls_tab( 'menu_items_tab_default', [ 'label' => __( 'Default', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'menu_items_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-switcher__nav__item' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'menu_items_background_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-switcher__nav__item' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'menu_items_tab_hover', [ 'label' => __( 'Hover', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'menu_items_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-switcher__nav__item:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'menu_items_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-switcher__nav__item:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'menu_items_tab_active', [ 'label' => __( 'Active', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'menu_items_color_active',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-switcher__nav__item.is--active' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'menu_items_background_color_active',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-switcher__nav__item.is--active' => 'background-color: {{VALUE}};',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'menu_style_loader',
				[
					'label' => __( 'Separator', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'menu_separator_color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-loader' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'menu_loader_color',
				[
					'label' 	=> __( 'Loader Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-loader__progress' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'menu_separator_thickness',
				[
					'label' 		=> __( 'Thickness', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 10,
						],
					],
					'default'		=> [
						'size'		=> 1,
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-loader' => 'height: {{SIZE}}{{UNIT}}',
					],
				]
			);

		$this->end_controls_section();

	}

	/**
	 * Register Arrows Style Controls
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function register_arrows_style_controls() {

		$this->start_controls_section(
			'section_style_arrows',
			[
				'label' => __( 'Arrows', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'arrows_distance',
				[
					'label' 		=> __( 'Distance', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-switcher__arrows' => 'margin-top: {{SIZE}}px',
					],
				]
			);

			$this->add_responsive_control(
				'arrows_align',
				[
					'label' 			=> __( 'Align', 'landtech-extras-for-elementor' ),
					'type' 				=> Controls_Manager::CHOOSE,
					'mobile_default' 	=> 'center',
					'options' 			=> [
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
						'space-between' => [
							'title' 	=> __( 'Stretch', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-stretch',
						],
					],
					'label_block'	=> false,
					'selectors' 	=> [
						'{{WRAPPER}} .ee-switcher__arrows' => 'justify-content: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'arrows_spacing',
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
						'{{WRAPPER}} .ee-arrow--next' => 'margin-left: {{SIZE}}px;',
					],
					'condition'		=> [
						'arrows_align!' => 'space-between'
					],
				]
			);

			$this->add_responsive_control(
				'arrows_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0.2,
							'max' => 2,
							'step'=> 0.1
						],
					],
					'default'		=> [
						'size'		=> 0.6,
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-arrow' => 'padding: {{SIZE}}em;',
					],
				]
			);

			$this->add_responsive_control(
				'arrows_size',
				[
					'label' 		=> __( 'Size', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'default'		=> [
						'size'		=> 24,
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-arrow' => 'font-size: {{SIZE}}px;',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'arrows',
					'selector' 		=> '{{WRAPPER}} .ee-arrow',
				]
			);

			$this->update_control( 'arrows_transition', array(
				'default' => 'custom',
			));

			$this->start_controls_tabs( 'arrows_tabs' );

			$this->start_controls_tab( 'arrows_tab_default', [ 'label' => __( 'Default', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'arrows_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-arrow' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'arrows_background_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-arrow' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'arrows_opacity',
					[
						'label' 		=> __( 'Opacity', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 1,
								'step'=> 0.1,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ee-arrow' => 'opacity: {{SIZE}}',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'arrows_tab_hover', [ 'label' => __( 'Hover', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'arrows_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-arrow:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'arrows_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-arrow:hover' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'arrows_opacity_hover',
					[
						'label' 		=> __( 'Opacity', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 1,
								'step'=> 0.1,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ee-arrow:hover' => 'opacity: {{SIZE}}',
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'arrows_tab_disabled', [ 'label' => __( 'Disabled', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'arrows_color_disabled',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-arrow.ee-arrow--disabled' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'arrows_background_color_disabled',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-arrow.ee-arrow--disabled' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_responsive_control(
					'arrows_opacity_disabled',
					[
						'label' 		=> __( 'Opacity', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 1,
								'step'=> 0.1,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ee-arrow.ee-arrow--disabled' => 'opacity: {{SIZE}}',
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'arrows_style_loader',
				[
					'label' => __( 'Loader', 'landtech-extras-for-elementor' ),
					'type'	=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'arrows_loader_color',
				[
					'label' 	=> __( 'Loader Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-arrow__circle--loader.is--animating' => 'stroke: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'arrows_loader_thickness',
				[
					'label' 		=> __( 'Thickness', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 10,
						],
					],
					'default'		=> [
						'size'		=> 2,
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-arrow__circle--loader' => 'stroke-width: -webkit-calc({{SIZE}} * 2); stroke-width: calc({{SIZE}} * 2);',
						'{{WRAPPER}} .ee-arrow .ee-arrow__circle--loader' => 'stroke-width: -webkit-calc({{SIZE}}px * 2); stroke-width: calc({{SIZE}}px * 2);',
					],
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Render
	 * 
	 * Render widget contents on frontend
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function render() {

		$this->add_render_attribute( [
			'switcher' => [
				'class' => [
					'ee-switcher',
					'ee-switcher--stack-' . $this->get_settings( 'layout_stack' ),
				],
			],
			'switcher-wrapper' => [
				'class' => 'ee-switcher__wrapper',
			],
			'switcher-media-wrapper' => [
				'class' => [
					'ee-switcher__media-wrapper',
					'ee-media--stretch',
				],
			],
			'switcher-content-wrapper' => [
				'class' => 'ee-switcher__content-wrapper',
			],
			'switcher-content' => [
				'class' => 'ee-switcher__content',
			],
		] );

		?>

		<div <?php $this->print_render_attribute_string( 'switcher' ); ?>>
			<div <?php $this->print_render_attribute_string( 'switcher-wrapper' ); ?>>
	
				<div <?php $this->print_render_attribute_string( 'switcher-media-wrapper' ); ?>>
					<?php $this->render_media_loop(); ?>
				</div>

				<div <?php $this->print_render_attribute_string( 'switcher-content-wrapper' ); ?>>
					<div <?php $this->print_render_attribute_string( 'switcher-content' ); ?>>
						<?php
						$this->render_titles_loop();
						$this->render_descriptions_loop();
						$this->render_nav_loop();
						$this->render_arrows(); ?>
					</div>
				</div>

			</div>
		</div>

		<?php

	}

	/**
	 * Render Titles Loop
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function render_titles_loop() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'items', 'class', 'ee-switcher__titles' );

		?><div <?php $this->print_render_attribute_string( 'items' ); ?>>
			<?php foreach ( $settings['items'] as $index => $item ) {

				$title_tag     = 'div';
				$heading_tag   = $this->ltxe_sanitize_heading_tag( $settings['title_heading_tag'] );
				$item_key 		= $this->get_repeater_setting_key( 'item', 'items', $index );
				$item_title_key = $this->get_repeater_setting_key( 'item-title', 'items', $index );

				$this->add_render_attribute( [
					$item_key => [
						'class' => 'ee-switcher__titles__title'
					],
					$item_title_key => [
						'class' => 'ee-switcher__title',
					],
				] );

				if ( 'yes' === $settings['link_title'] ) {
					$title_tag = 'a';
					$this->set_item_link_attributes( $item, $item_key, 'title' );
				}
			?>

			<<?php echo esc_html( $title_tag ); ?> <?php $this->print_render_attribute_string( $item_key ); ?>>
				<<?php echo esc_html( $heading_tag ); ?> <?php $this->print_render_attribute_string( $item_title_key ); ?>><?php echo esc_html( $item['title'] ); ?></<?php echo esc_html( $heading_tag ); ?>>
			</<?php echo esc_html( $title_tag ); ?>>

			<?php } ?>
		</div>

		<?php
	}

	/**
	 * Render Descriptions Loop
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function render_descriptions_loop() {

		$settings = $this->get_settings_for_display();

		if ( 'yes' !== $settings['description'] )
			return;

		$this->add_render_attribute( 'descriptions', 'class', 'ee-switcher__descriptions' );

		?>

		<div <?php $this->print_render_attribute_string( 'descriptions' ); ?>>
			<?php foreach ( $settings['items'] as $index => $item ) {

				$description_tag = 'div';
				$description_key = $this->get_repeater_setting_key( 'description', 'items', $index );

				$this->add_render_attribute( $description_key, 'class', 'ee-switcher__descriptions__description' );

				if ( 'yes' === $settings['link_description'] ) {
					$description_tag = 'a';
					$this->set_item_link_attributes( $item, $description_key, 'description' );
				}
			?>

			<<?php echo esc_html( $description_tag ); ?> <?php $this->print_render_attribute_string( $description_key ); ?>>
				<?php echo wp_kses_post( $item['description'] ); ?>
			</<?php echo esc_html( $description_tag ); ?>>

			<?php } ?>
		</div>

		<?php
	}

	/**
	 * Render Media Loop
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function render_media_loop() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'media' => [
				'class' => [
					'ee-switcher__media',
					'ee-media',
					'ee-effect--' . $settings['effect_media'],
				],
			],
			'media-items' => [
				'class' => [
					'ee-media__wrapper',
					'ee-switcher__media__items',
				],
			],
			'media-overlay' => [
				'class' => [
					'ee-switcher__media__overlay',
					'ee-media__overlay',
				],
			],
		] );

		?>

		<div <?php $this->print_render_attribute_string( 'media' ); ?>>
			<div <?php $this->print_render_attribute_string( 'media-items' ); ?>>
				<?php foreach ( $settings['items'] as $index => $item ) {

					$media_tag			= 'div';
					$media_item_key 	= $this->get_repeater_setting_key( 'media-item', 'items', $index );
					$image_key 			= $this->get_repeater_setting_key( 'image', 'items', $index );

					$this->add_render_attribute( $media_item_key, 'class', [
						'ee-switcher__media__item',
						'ee-media__thumbnail',
					]);

					if ( 'yes' === $settings['link_image'] ) {
						$media_tag = 'a';
						$this->set_item_link_attributes( $item, $media_item_key, 'media' );
					}
				?>

				<<?php echo esc_html( $media_tag ); ?> <?php $this->print_render_attribute_string( $media_item_key ); ?>><?php
					$this->add_render_attribute( $image_key, [
						'src' 	=> $this->get_item_image_url( $item ),
						'alt' 	=> Control_Media::get_image_alt( $item['image'] ),
						'title' => Control_Media::get_image_title( $item['image'] )
					] );

					?><img <?php $this->print_render_attribute_string( $image_key ) ?>/>
				</<?php echo esc_html( $media_tag ); ?>>

				<?php } ?>
			</div>
			<div <?php $this->print_render_attribute_string( 'media-overlay' ); ?>></div>
		</div>

		<?php
	}

	/**
	 * Render Nav Loop
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function render_nav_loop() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'nav' => [
				'class' => [
					'ee-switcher__nav',
					'ee-nav',
				],
			],
			'loader' => [
				'class' => 'ee-loader',
			],
			'loader-progress' => [
				'class' => 'ee-loader__progress',
			],
		]);

		if ( 'row' === $settings['menu_direction'] ) {
			$this->add_render_attribute( 'nav', 'class', 'ee-nav--stacked' );
		} else {
			$this->add_render_attribute( 'nav', 'class', 'ee-nav--inline' );
		} ?>

		<ul  <?php $this->print_render_attribute_string( 'nav' ); ?>>
			<?php foreach ( $settings['items'] as $index => $item ) {

				$has_icon 				= false;
				$nav_item_key 			= $this->get_repeater_setting_key( 'nav-item', 'items', $index );
				$nav_item_label_key 	= $this->get_repeater_setting_key( 'nav-item-label', 'items', $index );
				$nav_item_icon_key 		= $this->get_repeater_setting_key( 'nav-item-icon', 'items', $index );
				$nav_icon_wrapper_key 	= $this->get_repeater_setting_key( 'nav-icon-wrapper', 'items', $index );

				$migrated = isset( $item['__fa4_migrated']['selected_icon'] );
				$is_new = empty( $item['icon'] ) && Icons_Manager::is_migration_allowed();

				if ( ! empty( $item['icon'] ) || ! empty( $item['selected_icon']['value'] ) ) {

					$this->add_render_attribute( $nav_icon_wrapper_key, 'class', [
						'ee-icon',
						'ee-icon--' . $item['icon_align'],
						'ee-icon-support--svg',
						'ee-icon-support--svg-large',
						'ee-switcher__nav__icon',
					] );

					if ( ! empty( $item['icon'] ) ) {
						$this->add_render_attribute( [
							$nav_item_icon_key => [
								'class' => esc_attr( $item['icon'] ),
								'aria-hidden' => 'true',
							],
						] );
					}

					$this->add_render_attribute( $nav_item_key, 'class', 'has--icon' );

					$has_icon = true;
				}

				if ( 'yes' === $settings['background_switcher'] ) {
					$this->add_render_attribute( $nav_item_key, 'data-switcher-background', $item['background_switcher_color'] );
				}

				// $this->add_inline_editing_attributes( $navItemLabelKey, 'none' );

				$this->add_render_attribute( $nav_item_key, 'class', [
					'ee-switcher__nav__item',
					'ee-nav__item',
					'elementor-repeater-item-' . $item['_id'],
				]);
			?>

			<li <?php $this->print_render_attribute_string( $nav_item_key ); ?>><?php
				if ( $has_icon ) {
					?><span <?php $this->print_render_attribute_string( $nav_icon_wrapper_key ); ?>><?php
					if ( $is_new || $migrated ) {
						Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ], 'i' );
					} else {
						?><span <?php $this->print_render_attribute_string( $nav_item_icon_key ); ?>></span><?php
					}
					?></span><?php
				}

				if ( '' !== $item['label'] ) {
					?><span <?php $this->print_render_attribute_string( $nav_item_label_key ); ?>><?php
						echo esc_html( $item['label'] );
					?></span><?php 
				}
				?><span <?php $this->print_render_attribute_string( 'loader' ); ?>>
					<span <?php $this->print_render_attribute_string( 'loader-progress' ); ?>></span>
				</span>
			</li>

			<?php } ?>
		</ul>

		<?php
	}

	/**
	 * Render Arrows
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function render_arrows() {

		$this->add_render_attribute( [
			'arrows' => [
				'class' => 'ee-switcher__arrows',
			],
			'arrow-prev' => [
				'class' => [
					'ee-arrow',
					'ee-arrow--prev',
				],
			],
			'arrow-prev-icon' => [
				'class' => 'eicon-chevron-left',
			],
			'arrow-next' => [
				'class' => [
					'ee-arrow',
					'ee-arrow--next',
				],
			],
			'arrow-next-icon' => [
				'class' => 'eicon-chevron-right',
			],
		] );

		$switcher_clip_id = 'clipLoader' . $this->get_id();
		?>
		<ul <?php $this->print_render_attribute_string( 'arrows' ); ?>>
			<li <?php $this->print_render_attribute_string( 'arrow-prev' ); ?>>
				<i <?php $this->print_render_attribute_string( 'arrow-prev-icon' ); ?>></i>
			</li>
			<li <?php $this->print_render_attribute_string( 'arrow-next' ); ?>>
				<i <?php $this->print_render_attribute_string( 'arrow-next-icon' ); ?>></i>
				<svg x="0px" y="0px" viewBox="0 0 80 80" xml:space="preserve" class="ee-arrow__svg">
					<defs>
						<clipPath id="<?php echo esc_attr( $switcher_clip_id ); ?>">
							<circle cx="40" cy="40" r="40"/>
						</clipPath>
					</defs>
					<circle transform="rotate(-90 40 40)" class="ee-arrow__circle--loader" stroke-dasharray="227" stroke-dashoffset="227" cx="40" cy="40" r="40" fill="transparent" stroke="transparent" stroke-width="4" vector-effect="non-scaling-stroke" clip-path="url(#<?php echo esc_attr( $switcher_clip_id ); ?>)" />
				</svg>
			</li>
		</ul><?php
	}

	/**
	 * Render Loader
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function render_loader() {
		$this->add_render_attribute( [
			'loader' => [
				'class' => [
					'ee-switcher__loader',
					'ee-progress-loader',
				],
			],
			'loader-inner' => [
				'class' => 'ee-progress-loader__inner',
			],
		] );

		?><div <?php $this->print_render_attribute_string( 'loader' ); ?>>
			<span <?php $this->print_render_attribute_string( 'loader-inner' ); ?>></span>
		</div><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function content_template() { ?><#

			view.addRenderAttribute( {
				'switcher' : {
					'class' : [
						'ee-switcher',
						'ee-switcher--stack-' + settings.layout_stack,
					],
				},
				'switcher-wrapper' : {
					'class' : 'ee-switcher__wrapper',
				},
				'switcher-media-wrapper' : {
					'class' : [
						'ee-switcher__media-wrapper',
						'ee-media--stretch',
					],
				},
				'switcher-content-wrapper' : {
					'class' : 'ee-switcher__content-wrapper',
				},
				'switcher-content' : {
					'class' : 'ee-switcher__content',
				},
			} );

		#><div {{{ view.getRenderAttributeString( 'switcher' ) }}}>
			<div {{{ view.getRenderAttributeString( 'switcher-wrapper' ) }}}>
				
				<div {{{ view.getRenderAttributeString( 'switcher-media-wrapper' ) }}}>
					<?php $this->_media_loop_template(); ?>
				</div>

				<div {{{ view.getRenderAttributeString( 'switcher-content-wrapper' ) }}}>
					<div {{{ view.getRenderAttributeString( 'switcher-content' ) }}}>
						<?php
						$this->_titles_loop_template();
						$this->_descriptions_loop_template();
						$this->_nav_loop_template();
						$this->render_arrows(); ?>
					</div>
				</div>

			</div>
		</div>

		<?php
	}

	/**
	 * Titles Loop Template
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function _titles_loop_template() { ?><#

		view.addRenderAttribute( 'items', 'class', 'ee-switcher__titles' );

		#><div {{{ view.getRenderAttributeString( 'items' ) }}}>
			<# _.each( settings.items, function( item, index ) {

				var titleTag 		= 'div',
					headingTag 		= settings.title_heading_tag,
					itemKey 		= view.getRepeaterSettingKey( 'item', 'items', index ),
					itemTitleKey 	= view.getRepeaterSettingKey( 'itemTitle', 'items', index );

				view.addRenderAttribute( itemKey, 'class', 'ee-switcher__titles__title' );
				view.addRenderAttribute( itemTitleKey, 'class', 'ee-switcher__title' );

				if ( 'yes' === settings.link_title ) {
					if ( 'file' == settings.link_to ) {

						titleTag = 'a';
						view.addRenderAttribute( itemKey, 'href', item.image.url );
						view.addRenderAttribute( itemKey, 'class', 'elementor-clickable' );
						view.addRenderAttribute( itemKey, 'data-elementor-open-lightbox', settings.link_open_lightbox );
						view.addRenderAttribute( itemKey, 'data-elementor-lightbox-slideshow', view.$el.data('id') );

					} else if ( 'attachment' === settings.link_to ) {

						titleTag = 'a';
						view.addRenderAttribute( itemKey, 'href', '' );

					} else if ( 'custom' === settings.link_to && '' !== item.link.url ) {

						titleTag = 'a';
						view.addRenderAttribute( itemKey, 'href', item.link.url );

					}
				}

			#>

			<{{{ titleTag }}} {{{ view.getRenderAttributeString( itemKey ) }}}>
				<{{{ headingTag }}} {{{ view.getRenderAttributeString( itemTitleKey ) }}}>{{ item.title }}</{{{ headingTag }}}>
			</{{{ titleTag }}}>

			<# }); #>
		</div>

		<?php
	}

	/**
	 * Descriptions Loop Template
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function _descriptions_loop_template() { ?><#

		if ( 'yes' === settings.description ) {

		view.addRenderAttribute( 'descriptions', 'class', 'ee-switcher__descriptions' );

		#><div {{{ view.getRenderAttributeString( 'descriptions' ) }}}>
			<# _.each( settings.items, function( item, index ) {

				var descriptionTag 	= 'div',
					descriptionKey 	= view.getRepeaterSettingKey( 'description', 'items', index );

				view.addRenderAttribute( descriptionKey, 'class', 'ee-switcher__descriptions__description' );

				if ( 'yes' === settings.link_description ) {
					descriptionTag = 'a';

					if ( 'file' == settings.link_to && '' !== item.image.url ) {

						view.addRenderAttribute( descriptionKey, 'href', item.image.url );
						view.addRenderAttribute( descriptionKey, 'class', 'elementor-clickable' );
						view.addRenderAttribute( descriptionKey, 'data-elementor-open-lightbox', settings.link_open_lightbox );
						view.addRenderAttribute( descriptionKey, 'data-elementor-lightbox-slideshow', view.$el.data('id') );

					} else if ( 'attachment' === settings.link_to ) {

						view.addRenderAttribute( descriptionKey, 'href', '' );

					} else if ( 'custom' === settings.link_to && '' !== item.link.url ) {

						view.addRenderAttribute( descriptionKey, 'href', item.link.url );

					}
				}
			#>

			<{{{ descriptionTag }}} {{{ view.getRenderAttributeString( descriptionKey ) }}}>
				{{{ item.description }}}
			</{{{ descriptionTag }}}>

			<# }); #>
		</div><#

		} #>

		<?php
	}

	/**
	 * Media Loop Template
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function _media_loop_template() { ?><#

		view.addRenderAttribute( {
			'media' : {
				'class' : [
					'ee-switcher__media',
					'ee-media',
					'ee-effect--' + settings.effect_media,
				],
			},
			'media-items' : {
				'class' : [
					
				
					'ee-switcher__media__items',
				],
			},
			'media-overlay' : {
				'class' : [
					'ee-switcher__media__overlay',
					'ee-media__overlay',
				],
			},
		} );

		#>

		<div {{{ view.getRenderAttributeString( 'media' ) }}}>
			<div {{{ view.getRenderAttributeString( 'media-items' ) }}}>
				<# _.each( settings.items, function( item, index ) {

					var image = {
							id: item.image.id,
							url: item.image.url,
							size: settings.image_size,
							dimension: settings.image_custom_dimension,
							model: view.getEditModel()
						},
						mediaTag 		= 'div',
						imageURL 		= elementor.imagesManager.getImageUrl( image ),
						mediaItemKey 	= view.getRepeaterSettingKey( 'media-item', 'items', index ),
						imageKey 		= view.getRepeaterSettingKey( 'image', 'items', index );

					view.addRenderAttribute( mediaItemKey, 'class', [
						'ee-switcher__media__item',
						'ee-media__thumbnail',
					]);

					if ( 'yes' === settings.link_image ) {
						if ( 'file' == settings.link_to ) {

							mediaTag = 'a';
							view.addRenderAttribute( mediaItemKey, 'href', item.image.url );
							view.addRenderAttribute( mediaItemKey, 'class', 'elementor-clickable' );
							view.addRenderAttribute( mediaItemKey, 'data-elementor-open-lightbox', settings.link_open_lightbox );
							view.addRenderAttribute( mediaItemKey, 'data-elementor-lightbox-slideshow', view.$el.data('id') );

						} else if ( 'attachment' === settings.link_to ) {

							mediaTag = 'a';
							view.addRenderAttribute( mediaItemKey, 'href', '' );

						} else if ( 'custom' === settings.link_to && '' !== item.link.url ) {

							mediaTag = 'a';
							view.addRenderAttribute( mediaItemKey, 'href', item.link.url );

						}
					}

					view.addRenderAttribute( imageKey, 'src', imageURL );
				#>

				<{{{ mediaTag }}} {{{ view.getRenderAttributeString( mediaItemKey ) }}}>
					<# if ( imageURL ) { #>
						<img {{{ view.getRenderAttributeString( imageKey ) }}}>
					<# } #>
				</{{{ mediaTag }}}>

				<# }); #>
			</div>
			<div {{{ view.getRenderAttributeString( 'media-overlay' ) }}}></div>
		</div>

		<?php
	}

	/**
	 * Navigation Loop Template
	 *
	 * @since  1.9.0
	 * @return void
	 */
	public function _nav_loop_template() { ?><#

		view.addRenderAttribute( {
			'nav' : {
				'class' : [
					'ee-switcher__nav',
					'ee-nav',
				],
			},
			'loader' : {
				'class' : 'ee-loader',
			},
			'loader-progress' : {
				'class' : 'ee-loader__progress',
			},
		} );

		if ( 'row' === settings.menu_direction ) {
			view.addRenderAttribute( 'nav', 'class', 'ee-nav--stacked' );
		} else {
			view.addRenderAttribute( 'nav', 'class', 'ee-nav--inline' );
		}

		#><ul {{{ view.getRenderAttributeString( 'nav' ) }}}>
			<# _.each( settings.items, function( item, index ) {

				var has_icon 			= false,
					navItemKey 			= view.getRepeaterSettingKey( 'nav-item', 'items', index ),
					navItemLabelKey 	= view.getRepeaterSettingKey( 'nav-item-label', 'items', index ),
					navItemIconKey 		= view.getRepeaterSettingKey( 'nav-item-icon', 'items', index );
					navIconWrapperKey 	= view.getRepeaterSettingKey( 'nav-icon-wrapper', 'items', index );

				var iconHTML = elementor.helpers.renderIcon( view, item.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
					migrated = elementor.helpers.isIconMigrated( item, 'selected_icon' );

				if ( item.icon || item.selected_icon ) {

					view.addRenderAttribute( navIconWrapperKey, {
						'class' : [
							'ee-icon',
							'ee-icon-support--svg',
							'ee-icon-support--svg-large',
							'ee-switcher__nav__icon',
							'ee-icon--' + item.icon_align,
						],
					} );

					if ( item.icon ) {
						view.addRenderAttribute( navItemIconKey, {
							'class' : item.icon,
							'aria-hidden' : 'true',
						} );
					}

					view.addRenderAttribute( navItemKey, 'class', 'has--icon' );

					has_icon = true;
				}

				if ( 'yes' === settings.background_switcher ) {
					view.addRenderAttribute( navItemKey, 'data-switcher-background', item.background_switcher_color );
				}

				// view.addInlineEditingAttributes( navItemLabelKey, 'none' );

				view.addRenderAttribute( navItemKey, 'class', [
					'ee-switcher__nav__item',
					'ee-nav__item',
					'elementor-repeater-item-' + item._id
				]);

			#><li {{{ view.getRenderAttributeString( navItemKey ) }}}><#
				if ( has_icon ) {
					#><span {{{ view.getRenderAttributeString( navIconWrapperKey ) }}}><#
					if ( ( migrated || ! item.icon ) && iconHTML.rendered ) { #>
						{{{ iconHTML.value }}}
					<# } else { #>
						<span {{{ view.getRenderAttributeString( navItemIconKey ) }}}></span>
					<# }
					#></span><#
				}
				
				if ( '' !== item.label ) { #>
					<span {{{ view.getRenderAttributeString( navItemLabelKey ) }}}>{{{ item.label }}}</span>
				<# } #>
				<span {{{ view.getRenderAttributeString( 'loader' ) }}}>
					<span {{{ view.getRenderAttributeString( 'loader-progress' ) }}}></span>
				</span>
			</li>
			
			<# }); #>
		</ul>

		<?php
	}

	/**
	 * Get Item Image URL
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function get_item_image_url( $item ) {
		if ( empty( $item['image']['id'] ) ) {
			$item['image']['url'] = Utils::get_placeholder_image_src();
		}

		$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], 'image', $this->get_settings() );

		if ( ! $image_url ) {
			$image_url = $item['image']['url'];
		}

		return $image_url;
	}

	/**
	 * Set Item Link Attributes
	 *
	 * @since  2.2.18
	 * @return void
	 */
	protected function get_item_link( $item ) {
		switch ( $this->get_settings('link_to') ) {
			case  'file' :
				return $this->get_item_image_url( $item );
				break;
			case 'attachment' :
				return get_attachment_link( $item['image']['id'] );
				break;
			case 'custom' : 
				return $item['link']['url'];
				break;
			default :
				return false;
		}

		return false;
	}

	/**
	 * Set Item Link Attributes
	 *
	 * @since  1.9.0
	 * @return void
	 */
	protected function set_item_link_attributes( $item, $key, $slideshow_key = false ) {

		$link_to = $this->get_settings('link_to');

		if ( 'file' == $link_to ) {

			$slideshow_key = $slideshow_key ? implode( '-', [ $this->get_id(), $slideshow_key ] ) : false;
			$has_slideshow = $this->get_settings('lightbox_slideshow') && $slideshow_key ? $slideshow_key : false;

			$this->add_lightbox_data_attributes( $key, $item['image']['id'], $this->get_settings('link_open_lightbox'), $has_slideshow );

			if ( $this->_is_edit_mode ) {
				$this->add_render_attribute( $key, 'class', 'elementor-clickable' );
			}

		} else if ( 'custom' === $link_to ) {
			if ( ! empty( $item['link']['url'] ) ) {
				if ( $item['link']['is_external'] ) {
					$this->add_render_attribute( $key, 'target', '_blank' );
				}

				if ( ! empty( $item['link']['nofollow'] ) ) {
					$this->add_render_attribute( $key, 'rel', 'nofollow' );
				}
			}
		}

		$this->add_render_attribute( $key, 'href', $this->get_item_link( $item ) );
	}

}