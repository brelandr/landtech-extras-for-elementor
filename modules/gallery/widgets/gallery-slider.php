<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Gallery\Widgets;

use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Gallery\ACF_Gallery_Bridge;
use LandTechExtras\Group_Control_Transition;
use LandTechExtras\Modules\Gallery\Module;
use LandTechExtras\Modules\Image\Module as ImageModule;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Gallery_Slider
 *
 * @since 1.1.0
 */
class Gallery_Slider extends Extras_Widget {

	/**
	 * Resolved gallery rows for this render (Media Library or ACF).
	 *
	 * @since 2.2.54
	 *
	 * @var array<int, array<string, mixed>>
	 */
	private $slider_gallery_dataset = array();

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_name() {
		return 'gallery-slider';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Gallery Slider', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-slider-gallery';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  1.1.0
	 * @return array
	 */
	public function get_script_depends() {
		return [
			'swiper',
		];
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  1.1.0
	 * @return void
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_gallery',
			[
				'label' => __( 'Gallery', 'landtech-extras-for-elementor' ),
			]
		);

			if ( ACF_Gallery_Bridge::is_enabled() ) {
				$this->add_control(
					'gallery_source',
					[
						'label'   => __( 'Source', 'landtech-extras-for-elementor' ),
						'type'    => Controls_Manager::SELECT,
						'default' => 'media_library',
						'options' => [
							'media_library' => __( 'Media Library', 'landtech-extras-for-elementor' ),
							'acf_gallery'   => __( 'ACF Gallery', 'landtech-extras-for-elementor' ),
						],
					]
				);
			}

			$wp_gallery_field = [
				'label' 	=> __( 'Add Images', 'landtech-extras-for-elementor' ),
				'type' 		=> Controls_Manager::GALLERY,
				'dynamic'	=> [ 'active' => true ],
			];

			if ( ACF_Gallery_Bridge::is_enabled() ) {
				$wp_gallery_field['condition'] = [
					'gallery_source!' => 'acf_gallery',
				];
			}

			$this->add_control( 'wp_gallery', $wp_gallery_field );

			if ( ACF_Gallery_Bridge::is_enabled() ) {
				$this->add_control(
					'acf_field_name',
					[
						'label'       => __( 'ACF Field Name', 'landtech-extras-for-elementor' ),
						'type'        => Controls_Manager::TEXT,
						'description' => __( 'Name of the ACF Gallery field (not the label).', 'landtech-extras-for-elementor' ),
						'dynamic'     => [
							'active' => true,
						],
						'condition'   => [
							'gallery_source' => 'acf_gallery',
						],
					]
				);

				$this->add_control(
					'acf_post_id',
					[
						'label'       => __( 'Post ID', 'landtech-extras-for-elementor' ),
						'type'        => Controls_Manager::NUMBER,
						'description' => __( 'Leave empty to use the current post.', 'landtech-extras-for-elementor' ),
						'dynamic'     => [
							'active' => true,
						],
						'min'         => 0,
						'default'     => 0,
						'condition'   => [
							'gallery_source' => 'acf_gallery',
						],
					]
				);
			}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_thumbnails',
			[
				'label' => __( 'Thumbnails', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'show_thumbnails',
				[
					'type' 		=> Controls_Manager::SWITCHER,
					'label' 	=> __( 'Thumbnails', 'landtech-extras-for-elementor' ),
					'default' 	=> 'yes',
					'label_off' => __( 'Hide', 'landtech-extras-for-elementor' ),
					'label_on' 	=> __( 'Show', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'thumbnails_carousel',
				[
					'type' 		=> Controls_Manager::SWITCHER,
					'label' 	=> __( 'Enable Carousel', 'landtech-extras-for-elementor' ),
					'default' 	=> '',
					'label_off' => __( 'Hide', 'landtech-extras-for-elementor' ),
					'label_on' 	=> __( 'Show', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'thumbnails_hide_single',
				[
					'type' 		=> Controls_Manager::SWITCHER,
					'label' 	=> __( 'Hide for Single Image', 'landtech-extras-for-elementor' ),
					'default' 	=> 'yes',
					'condition' => [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'thumbnails_stretch',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Stretch Horizontally', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Stretch thumbnails horizontally if their number is smaller that the selected Slides per View number', 'landtech-extras-for-elementor' ),
					'default' 		=> 'yes',
					'condition' 	=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 		=> 'thumbnail',
					'label'		=> __( 'Thumbnails Size', 'landtech-extras-for-elementor' ),
					'condition' => [
						'show_thumbnails!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'columns',
				[
					'label' 	=> __( 'Columns', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '3',
					'tablet_default' 	=> '6',
					'mobile_default' 	=> '4',
					'options' 			=> [
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'11' => '11',
						'12' => '12',
					],
					'prefix_class'	=> 'ee-grid-columns%s-',
					'frontend_available' => true,
					'condition' => [
						'show_thumbnails!' => '',
						'thumbnails_carousel' => '',
					],
				]
			);

			$this->add_control(
				'gallery_rand',
				[
					'label' 	=> __( 'Ordering', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'options' 	=> [
						'' 		=> __( 'Default', 'landtech-extras-for-elementor' ),
						'rand' 	=> __( 'Random', 'landtech-extras-for-elementor' ),
					],
					'default' 	=> '',
					'condition' => [
						'show_thumbnails!' => '',
					],
				]
			);

			$this->add_control(
				'thumbnails_caption_type',
				[
					'label' 	=> __( 'Caption', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 				=> __( 'None', 'landtech-extras-for-elementor' ),
						'title' 		=> __( 'Title', 'landtech-extras-for-elementor' ),
						'caption' 		=> __( 'Caption', 'landtech-extras-for-elementor' ),
						'description' 	=> __( 'Description', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						'show_thumbnails!' => '',
					],
				]
			);

			$this->add_control(
				'view',
				[
					'label' 	=> __( 'View', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HIDDEN,
					'default' 	=> 'traditional',
					'condition' => [
						'show_thumbnails!' => '',
					],
				]
			);

			$this->add_control(
				'carousel_heading',
				[
					'label' 	=> __( 'Carousel', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
					'type' 		=> Controls_Manager::HEADING,
					'condition'		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
					]
				]
			);

			$this->add_control(
				'carousel_orientation',
				[
					'type' 				=> Controls_Manager::SELECT,
					'label' 			=> __( 'Orientation', 'landtech-extras-for-elementor' ),
					'default'			=> 'horizontal',
					'tablet_default'	=> 'horizontal',
					'mobile_default'	=> 'horizontal',
					'options' 			=> [
						'horizontal' 	=> __( 'Horizontal', 'landtech-extras-for-elementor' ),
						'vertical' 		=> __( 'Vertical', 'landtech-extras-for-elementor' ),
					],
					'condition' 		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
					],
					'frontend_available' => true,
				]
			);


			$slides_per_column = range( 1, 6 );
			$slides_per_column = array_combine( $slides_per_column, $slides_per_column );

			$this->add_responsive_control(
				'carousel_slides_per_view',
				[
					'label' 			=> __( 'Slides per View', 'landtech-extras-for-elementor' ),
					'type' 				=> Controls_Manager::SELECT,
					'default' 			=> '',
					'tablet_default' 	=> '',
					'mobile_default' 	=> '',
					'options' 			=> [
						'' => __( 'Default', 'landtech-extras-for-elementor' ),
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'11' => '11',
						'12' => '12',
					],
					'condition' 		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'carousel_slides_per_column',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Slides per Column', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'For Vertical direction this defines the number of slides per row.', 'landtech-extras-for-elementor' ),
					'options' 		=> [ '' => __( 'Default', 'landtech-extras-for-elementor' ) ] + $slides_per_column,
					'condition' 	=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
						'carousel_orientation!' => 'vertical',
					],
					'frontend_available' 	=> true,
				]
			);

			$this->add_responsive_control(
				'carousel_slides_to_scroll',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Slides to Scroll', 'landtech-extras-for-elementor' ),
					'options' 		=> [ '' => __( 'Default', 'landtech-extras-for-elementor' ) ] + $slides_per_column,
					'condition' 	=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'carousel_spacing',
				[
					'label' 			=> __( 'Spacing', 'landtech-extras-for-elementor' ),
					'type' 				=> Controls_Manager::SLIDER,
					'default'			=> [
						'size' => 24,
						'unit' => 'px',
					],
					'tablet_default'	=> [
						'size' => 12,
						'unit' => 'px',
					],
					'mobile_default'	=> [
						'size' => 6,
						'unit' => 'px',
					],
					'size_units' 		=> [ 'px' ],
					'range' 			=> [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'condition' 		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'carousel_resistance',
				[
					'label' 		=> __( 'Resistance', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Set the value for resistant bounds.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> 0.25,
						'unit' 		=> 'px',
					],
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 1,
							'step'	=> 0.05,
						],
					],
					'condition' 	=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'carousel_speed',
				[
					'label' 	=> __( 'Speed (ms)', 'landtech-extras-for-elementor' ),
					'description' => __( 'Duration of the effect transition.', 'landtech-extras-for-elementor' ) ,
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 500,
						'unit' 	=> 'px',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 2000,
							'step'	=> 100,
						],
					],
					'condition' 	=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'carousel_arrows',
				[
					'label' 		=> __( 'Arrows', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'on',
					'label_on' 		=> __( 'On', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'Off', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'on',
					'condition' 	=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_preview',
			[
				'label' => __( 'Preview', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 		=> 'preview',
					'label'		=> __( 'Preview Size', 'landtech-extras-for-elementor' ),
					'default'	=> 'full',
				]
			);

			// $this->add_control(
			// 	'lazy',
			// 	[
			// 		'type' 		=> Controls_Manager::SELECT,
			// 		'label' 	=> __( 'Lazy Load', 'landtech-extras-for-elementor' ),
			// 		'description' 	=> __( 'On Demand: load image when slide is changed, Progressive: load all images after page loads.', 'landtech-extras-for-elementor' ),
			// 		'options'	=> [
			// 			'' 				=> __( 'Off', 'landtech-extras-for-elementor' ),
			// 			'ondemand' 		=> __( 'On Demand', 'landtech-extras-for-elementor' ),
			// 			'progressive' 	=> __( 'Progressive', 'landtech-extras-for-elementor' ),
			// 		],
			// 		'default' 	=> '',
			// 		'frontend_available' => true,
			// 	]
			// );

			$this->add_control(
				'link_to',
				[
					'label' 	=> __( 'Link to', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'none',
					'options' 	=> [
						'none' 		=> __( 'None', 'landtech-extras-for-elementor' ),
						'file' 		=> __( 'Media File', 'landtech-extras-for-elementor' ),
						'custom' 	=> __( 'Custom URL', 'landtech-extras-for-elementor' ),
					],
				]
			);

			$this->add_control(
				'link',
				[
					'label' 		=> 'Link to',
					'type' 			=> Controls_Manager::URL,
					'placeholder' 	=> __( 'http://your-link.com', 'landtech-extras-for-elementor' ),
					'condition' 	=> [
						'link_to' 	=> 'custom',
					],
					'show_label' 	=> false,
				]
			);

			$this->add_control(
				'open_lightbox',
				[
					'label' 	=> __( 'Lightbox', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'default',
					'options' 	=> [
						'default' 	=> __( 'Default', 'landtech-extras-for-elementor' ),
						'yes' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
						'no' 		=> __( 'No', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						'link_to' => 'file',
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
						'link_to' => 'file',
						'open_lightbox' => ['default', 'yes'],
					],
				]
			);

			// $this->add_control(
			// 	'preview_stretch',
			// 	[
			// 		'label' 	=> __( 'Image Stretch', 'landtech-extras-for-elementor' ),
			// 		'type' 		=> Controls_Manager::SELECT,
			// 		'default' 	=> 'yes',
			// 		'options' 	=> [
			// 			'no' 	=> __( 'No', 'landtech-extras-for-elementor' ),
			// 			'yes' 	=> __( 'Yes', 'landtech-extras-for-elementor' ),
			// 		],
			// 	]
			// );

			$this->add_control(
				'caption_type',
				[
					'label' 	=> __( 'Caption', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT2,
					'multiple'	=> true,
					'label_block' => true,
					'default' 	=> ['caption'],
					'options' 	=> [
						'' 				=> __( 'None', 'landtech-extras-for-elementor' ),
						'title' 		=> __( 'Title', 'landtech-extras-for-elementor' ),
						'caption' 		=> __( 'Caption', 'landtech-extras-for-elementor' ),
						'description' 	=> __( 'Description', 'landtech-extras-for-elementor' ),
					],
				]
			);

			$this->add_control(
				'preview_slider_heading',
				[
					'label' 	=> __( 'Slider', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
					'type' 		=> Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'direction',
				[
					'label' 	=> __( 'Slide Direction', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'ltr',
					'options' 	=> [
						'ltr' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
						'rtl' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'infinite',
				[
					'label' 	=> __( 'Infinite Loop', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'yes',
					'options' 	=> [
						'yes' 	=> __( 'Yes', 'landtech-extras-for-elementor' ),
						'no' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'effect',
				[
					'label' 	=> __( 'Effect', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'separator' => 'before',
					'default' 	=> 'slide',
					'options' 	=> [
						'slide' 	=> __( 'Slide', 'landtech-extras-for-elementor' ),
						'fade' 		=> __( 'Fade', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'speed',
				[
					'label' 	=> __( 'Duration (ms)', 'landtech-extras-for-elementor' ),
					'description' => __( 'How long should the effect transition last.', 'landtech-extras-for-elementor' ) ,
					'type' 		=> Controls_Manager::NUMBER,
					'default' 	=> 1000,
					'min' 		=> 0,
					'max' 		=> 2000,
					'step'		=> 100,
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'ken_burns',
				[
					'label' 	=> __( 'Ken Burns', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::POPOVER_TOGGLE,
					'default' 	=> '',
					'condition' => [
						'effect' => 'fade',
					],
				]
			);

			$this->start_popover();

			$this->add_control(
				'ken_burns_scale',
				[
					'label' 	=> __( 'Scale', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 1,
							'max' 	=> 2,
							'step'	=> 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-swiper__container--kenburns .ee-swiper__slide img' => 'transform: scale({{SIZE}});',
					],
					'condition' => [
						'effect' => 'fade',
						'ken_burns!' => '',
					],
				]
			);

			$this->add_control(
				'ken_burns_origin',
				[
					'label' 	=> __( 'Transform Origin', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'separator' => 'before',
					'label_block' => true,
					'options'	=> [
						'' => __('Default', 'landtech-extras-for-elementor'),
						'random' => __('Random', 'landtech-extras-for-elementor'),
						'custom' => __('Custom', 'landtech-extras-for-elementor'),
					],
					'default' 	=> '',
					'condition' => [
						'effect' => 'fade',
						'ken_burns!' => '',
					],
				]
			);

			$this->add_control(
				'ken_burns_origin_x',
				[
					'label' 	=> __( 'X Anchor Point', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'default' 	=> 'center',
					'options' 	=> [
						'left' 	=> [
							'title' => __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 	=> 'eicon-h-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 	=> 'eicon-h-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' => 'eicon-h-align-right',
						],
					],
					'condition' => [
						'effect' => 'fade',
						'ken_burns!' => '',
						'ken_burns_origin' => 'custom',
					],
				]
			);

			$this->add_control(
				'ken_burns_origin_y',
				[
					'label' 	=> __( 'Y Anchor Point', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'default' 	=> 'center',
					'options' 	=> [
						'top' => [
							'title' => __( 'Top', 'landtech-extras-for-elementor' ),
							'icon' 	=> 'eicon-v-align-top',
						],
						'center' => [
							'title' => __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 	=> 'eicon-v-align-middle',
						],
						'bottom' => [
							'title' => __( 'Bottom', 'landtech-extras-for-elementor' ),
							'icon' 	=> 'eicon-v-align-bottom',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-swiper__container--kenburns .ee-swiper__slide img' => 'transform-origin: {{ken_burns_origin_x.VALUE}} {{VALUE}}',
					],
					'condition' => [
						'effect' => 'fade',
						'ken_burns!' => '',
						'ken_burns_origin_x!' => '',
						'ken_burns_origin' => 'custom',
					],
				]
			);

			$this->add_control(
				'ken_burns_duration',
				[
					'label' 	=> __( 'Duration', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
					'type' 		=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0.1,
							'max' 	=> 10,
							'step'	=> 0.5,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-swiper__container--kenburns .ee-swiper__slide img' => 'transition-duration: {{SIZE}}s;',
					],
					'condition' => [
						'effect' => 'fade',
						'ken_burns!' => '',
					],
				]
			);

			$this->add_control(
				'ken_burns_easing',
				[
					'label' 	=> __( 'Easing', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'options'	=> Group_Control_Transition::get_easings(),
					'default' 	=> 'linear',
					'selectors' => [
						'{{WRAPPER}} .ee-swiper__container--kenburns .ee-swiper__slide img' => 'transition-timing-function: {{VALUE}};',
					],
					'condition' => [
						'effect' => 'fade',
						'ken_burns!' => '',
					],
				]
			);

			$this->end_popover();

			$this->add_control(
				'resistance',
				[
					'label' 		=> __( 'Resistance', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Set the value for resistant bounds.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> 0.25,
						'unit' 		=> 'px',
					],
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 1,
							'step'	=> 0.05,
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay',
				[
					'label' 	=> __( 'Autoplay', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
					'type' 		=> Controls_Manager::POPOVER_TOGGLE,
					'default' 	=> '',
					'frontend_available' => true,
				]
			);

			$this->start_popover();

			$this->add_control(
				'autoplay_speed',
				[
					'label' 	=> __( 'Autoplay Speed', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 5000,
						'unit' 	=> 'px',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 10000,
							'step'	=> 1000,
						],
					],
					'condition'	=> [
						'autoplay' => 'yes',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'autoplay_disable_on_interaction',
				[
					'label' 	=> __( 'Disable on Interaction', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'default' 	=> '',
					'frontend_available' => true,
					'condition'	=> [
						'autoplay' => 'yes',
					],
				]
			);

			$this->add_control(
				'pause_on_hover',
				[
					'label' 	=> __( 'Pause on Hover', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'default' 	=> '',
					'frontend_available' => true,
					'condition'	=> [
						'autoplay' => 'yes',
					],
				]
			);

			$this->end_popover();

			$this->add_control(
				'adaptive_height',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Auto Height', 'landtech-extras-for-elementor' ),
					'default' 		=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'show_arrows',
				[
					'label' 		=> __( 'Arrows', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'On', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'Off', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_layout',
			[
				'label' 	=> __( 'Layout', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'preview_position',
				[
					'label' 	=> __( 'Preview Position', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'left',
					'tablet_default' 	=> 'top',
					'mobile_default' 	=> 'top',
					'options' 	=> [
						'top' 		=> __( 'Top', 'landtech-extras-for-elementor' ),
						'right' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
						'left' 		=> __( 'Left', 'landtech-extras-for-elementor' ),
					],
					'prefix_class'	=> 'ee-gallery-slider--',
					'condition' => [
						'show_thumbnails!' => '',
					],
				]
			);

			$this->add_control(
				'preview_stack',
				[
					'label' 	=> __( 'Stack on', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'tablet',
					'tablet_default' 	=> 'top',
					'mobile_default' 	=> 'top',
					'options' 	=> [
						'tablet' 	=> __( 'Tablet & Mobile', 'landtech-extras-for-elementor' ),
						'mobile' 	=> __( 'Mobile Only', 'landtech-extras-for-elementor' ),
					],
					'prefix_class'	=> 'ee-gallery-slider--stack-',
					'condition' => [
						'preview_position!' => 'top',
						'show_thumbnails!' => '',
					],
				]
			);

			$this->add_responsive_control(
				'layout_horizontal_align',
				[
					'label' 	=> __( 'Horizontal Align', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
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
					'default' 		=> 'flex-start',
					'selectors' 	=> [
						'{{WRAPPER}}.ee-gallery-slider--top .ee-gallery-slider' => 'align-items: {{VALUE}}',
					],
					'condition' 	=> [
						'preview_position' => 'top',
					],
				]
			);

			$this->add_responsive_control(
				'layout_vertical_align',
				[
					'label' 	=> __( 'Vertical Align', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'flex-start' 	=> [
							'title' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-top',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-middle',
						],
						'flex-end' 		=> [
							'title' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-bottom',
						],
					],
					'default' 		=> 'flex-start',
					'selectors' 	=> [
						'{{WRAPPER}}:not(.ee-gallery-slider--top) .ee-gallery-slider' => 'align-items: {{VALUE}}',
					],
					'condition' 	=> [
						'preview_position!' => 'top',
					],
				]
			);

			$this->add_responsive_control(
				'preview_width',
				[
					'label' 	=> __( 'Preview Width (%)', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' 	=> [
						'size' 	=> 70,
					],
					'condition'	=> [
						'preview_position!' => 'top',
						'show_thumbnails!' => '',
					],
					'selectors'		=> [
						'{{WRAPPER}}.ee-gallery-slider--left .ee-gallery-slider__preview' => 'width: {{SIZE}}%',
						'{{WRAPPER}}.ee-gallery-slider--right .ee-gallery-slider__preview' => 'width: {{SIZE}}%',
						'{{WRAPPER}}.ee-gallery-slider--left .ee-gallery-slider__gallery' => 'width: calc(100% - {{SIZE}}%)',
						'{{WRAPPER}}.ee-gallery-slider--right .ee-gallery-slider__gallery' => 'width: calc(100% - {{SIZE}}%)',
					],
				]
			);

			$this->add_responsive_control(
				'carousel_width',
				[
					'label' 	=> __( 'Carousel Width', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 1000,
						],
						'%' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' 	=> [
						'size' 	=> 100,
						'unit' 	=> '%',
					],
					'condition'	=> [
						'preview_position' => 'top',
						'show_thumbnails!' => '',
					],
					'selectors'		=> [
						'{{WRAPPER}}.ee-gallery-slider--top .ee-gallery-slider__gallery' => 'max-width: {{SIZE}}{{unit}}',
					],
				]
			);

			$wrapper_horizontal_margin = is_rtl() ? 'margin-right' : 'margin-left';
			$preview_horizontal_padding = is_rtl() ? 'padding-right' : 'padding-left';

			$this->add_responsive_control(
				'preview_spacing',
				[
					'label' 	=> __( 'Spacing', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'default' 	=> [
						'size' 	=> 24,
					],
					'selectors' => [
						'{{WRAPPER}}.ee-gallery-slider--left .ee-gallery-slider > *,
						 {{WRAPPER}}.ee-gallery-slider--right .ee-gallery-slider > *' => $preview_horizontal_padding . ': {{SIZE}}{{UNIT}};',

						'{{WRAPPER}}.ee-gallery-slider--left .ee-gallery-slider,
						 {{WRAPPER}}.ee-gallery-slider--right .ee-gallery-slider' => $wrapper_horizontal_margin . ': -{{SIZE}}{{UNIT}};',

						'{{WRAPPER}}.ee-gallery-slider--top .ee-gallery-slider__preview' => 'margin-bottom: {{SIZE}}{{UNIT}};',

						'(tablet){{WRAPPER}}.ee-gallery-slider--stack-tablet .ee-gallery-slider__preview' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						'(mobile){{WRAPPER}}.ee-gallery-slider--stack-mobile .ee-gallery-slider__preview' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'show_thumbnails!' => '',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_slides',
			[
				'label' 	=> __( 'Slides', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'slides_custom_height',
				[
					'label' 		=> __( 'Custom Height', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
				]
			);

			$this->add_control(
				'slides_image_fit',
				[
					'label' 		=> __( 'Image Fit', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'cover',
					'options'		=> [
						'cover' 	=> __('Cover', 'landtech-extras-for-elementor'),
						'contain' 	=> __('Contain', 'landtech-extras-for-elementor'),
					],
					'selectors'		=> [
						'{{WRAPPER}} .ee-slider__media__thumbnail img' => 'object-fit: {{VALUE}}',
					],
					'condition' 	=> [
						'slides_custom_height!' => '',
						'effect' => 'slide',
					],
				]
			);

			$this->add_responsive_control(
				'slides_height',
				[
					'label' 		=> __( 'Height', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 1,
							'max' => 200,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-slider__media:before' => 'padding-bottom: {{SIZE}}%',
					],
					'condition'		=> [
						'slides_custom_height!' => '',
					],
				]
			);



			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'preview_border',
					'label' 	=> __( 'Image Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '{{WRAPPER}} .ee-gallery-slider__slider',
				]
			);

			$this->add_control(
				'preview_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}}  .ee-gallery-slider__slider' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'preview_box_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-gallery-slider__slider',
					'separator'	=> '',
				]
			);

			$this->add_control(
				'arrows_style_heading',
				[
					'label' 	=> __( 'Arrows', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
					'type' 		=> Controls_Manager::HEADING,
					'condition'		=> [
						'show_arrows!' => '',
					]
				]
			);

			$this->add_control(
				'arrows_position',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Position', 'landtech-extras-for-elementor' ),
					'default'		=> 'middle',
					'options' 		=> [
						'top' 		=> __( 'Top', 'landtech-extras-for-elementor' ),
						'middle' 	=> __( 'Middle', 'landtech-extras-for-elementor' ),
						'bottom' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
					],
					'condition'		=> [
						'show_arrows!' 	=> '',
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
							'min' => 12,
							'max' => 48,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__button' => 'font-size: {{SIZE}}px;',
					],
					'condition'		=> [
						'show_arrows!' => '',
					]
				]
			);

			$this->add_responsive_control(
				'arrows_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 1,
							'step'	=> 0.1,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__button' => 'padding: {{SIZE}}em;',
					],
					'condition'		=> [
						'show_arrows!' => '',
					]
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
						'{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__navigation--inside.ee-swiper__navigation--middle.ee-arrows--horizontal .ee-swiper__button' => 'margin-left: {{SIZE}}px; margin-right: {{SIZE}}px;',
						'{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__navigation--inside:not(.ee-swiper__navigation--middle).ee-arrows--horizontal .ee-swiper__button' => 'margin: {{SIZE}}px;',
					],
					'condition'		=> [
						'show_arrows!' => '',
					]
				]
			);

			$this->add_responsive_control(
				'arrows_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__button' => 'border-radius: {{SIZE}}%;',
					],
					'condition'		=> [
						'show_arrows!' => '',
					],
					'separator'		=> 'after',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'arrows',
					'selector' 		=> '{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__button,
										{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__button i:before',
					'condition'		=> [
						'show_arrows!' => '',
					]
				]
			);

			$this->start_controls_tabs( 'arrows_tabs_hover' );

			$this->start_controls_tab( 'arrows_tab_default', [
				'label' => __( 'Default', 'landtech-extras-for-elementor' ),
				'condition'	=> [
					'show_arrows!' => '',
				]
			] );

				$this->add_control(
					'arrows_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__button i:before' => 'color: {{VALUE}};',
						],
						'condition'		=> [
							'show_arrows!' => '',
						]
					]
				);

				$this->add_control(
					'arrows_background_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__button' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							'show_arrows!' => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'arrows_tab_hover', [
				'label' => __( 'Hover', 'landtech-extras-for-elementor' ),
				'condition'	=> [
					'show_arrows!' => '',
				]
			] );

				$this->add_control(
					'arrows_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__button:hover i:before' => 'color: {{VALUE}};',
						],
						'condition'		=> [
							'show_arrows!' => '',
						]
					]
				);

				$this->add_control(
					'arrows_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__button:hover' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							'show_arrows!' => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'arrows_tab_disabled', [
				'label' => __( 'Disabled', 'landtech-extras-for-elementor' ),
				'condition'	=> [
					'show_arrows!' => '',
				]
			] );

				$this->add_responsive_control(
					'arrows_opacity_disabled',
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
							'{{WRAPPER}} .ee-gallery-slider__preview .ee-swiper__button.ee-swiper__button--disabled' => 'opacity: {{SIZE}};',
						],
						'condition'		=> [
							'show_arrows!' => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_preview_captions',
			[
				'label' 	=> __( 'Preview Captions', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'caption_type!' => [],
				],
			]
		);

			$this->add_control(
				'preview_vertical_align',
				[
					'label' 	=> __( 'Vertical Align', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'top' 	=> [
							'title' 	=> __( 'Top', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-top',
						],
						'middle' 		=> [
							'title' 	=> __( 'Middle', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-middle',
						],
						'bottom' 		=> [
							'title' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-bottom',
						],
					],
					'default' 		=> 'bottom',
					'condition' 	=> [
						'caption_type!' => [],
					],
				]
			);

			$this->add_control(
				'preview_horizontal_align',
				[
					'label' 	=> __( 'Horizontal Align', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' 	=> [
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
							'title' 	=> __( 'Justify', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-stretch',
						],
					],
					'default' 		=> 'justify',
					'condition' 	=> [
						'caption_type!' => [],
					],
				]
			);

			$this->add_control(
				'preview_align',
				[
					'label' 	=> __( 'Text Align', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' 	=> [
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
					'default' 	=> 'center',
					'selectors' => [
						'{{WRAPPER}} .ee-slider__media__caption' => 'text-align: {{VALUE}};',
					],
					'condition' => [
						'caption_type!' => [],
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'preview_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-slider__media__caption',
					'condition' 	=> [
						'caption_type!' => [],
					],
				]
			);

			$this->add_control(
				'preview_text_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-slider__media__caption' 	=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 	=> [
						'caption_type!' => [],
					],
				]
			);

			$this->add_control(
				'preview_text_margin',
				[
					'label' 		=> __( 'Margin', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-slider__media__caption' 	=> 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 	=> [
						'caption_type!' => [],
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'preview_text_border',
					'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '{{WRAPPER}} .ee-slider__media__caption',
					// 'separator' => '',
					'condition'	=> [
						'caption_type!' => [],
					],
				]
			);

			$this->add_control(
				'preview_text_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-slider__media__caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 	=> [
						'caption_type!' => [],
					],
				]
			);

			$this->add_control(
				'preview_captions_title',
				[
					'label' 	=> __( 'Title', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'caption_type' => 'title',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'preview_title_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-slider__media__caption .ee-caption__title',
					'condition' 	=> [
						'caption_type' => 'title',
					],
				]
			);

			$this->add_control(
				'preview_title_margin',
				[
					'label' 		=> __( 'Margin', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-slider__media__caption .ee-caption__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 	=> [
						'caption_type' => 'title',
					],
				]
			);

			$this->add_control(
				'preview_captions_caption',
				[
					'label' 	=> __( 'Caption', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'caption_type' => 'caption',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'preview_caption_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-slider__media__caption .ee-caption__caption',
					'condition' 	=> [
						'caption_type' => 'caption',
					],
				]
			);

			$this->add_control(
				'preview_caption_margin',
				[
					'label' 		=> __( 'Margin', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-slider__media__caption .ee-caption__caption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 	=> [
						'caption_type' => 'caption',
					],
				]
			);

			$this->add_control(
				'preview_captions_description',
				[
					'label' 	=> __( 'Description', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'caption_type' => 'description',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'preview_description_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-slider__media__caption .ee-caption__description',
					'condition' 	=> [
						'caption_type' => 'description',
					],
				]
			);

			$this->add_control(
				'preview_description_margin',
				[
					'label' 		=> __( 'Margin', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-slider__media__caption .ee-caption__description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' 	=> [
						'caption_type' => 'description',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_preview_hover_effects',
			[
				'label' 	=> __( 'Preview Hover Effects', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'caption_type!' => [],
				],
			]
		);

			$this->add_control(
				'hover_preview_captions_heading',
				[
					'label' 	=> __( 'Captions', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition' => [
						'caption_type!' => [],
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'preview_caption',
					'selector' 		=> '{{WRAPPER}} .ee-slider__media__content,
										{{WRAPPER}} .ee-slider__media__caption',
					'condition' 	=> [
						'caption_type!' => [],
					],
				]
			);

			$this->update_control( 'preview_caption_transition', array(
				'default' => 'custom',
			));

			$this->add_control(
				'preview_caption_effect',
				[
					'label' 	=> __( 'Effect', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' => [
						''					=> __( 'None', 'landtech-extras-for-elementor' ),
						'fade-in'			=> __( 'Fade In', 'landtech-extras-for-elementor' ),
						'fade-out'			=> __( 'Fade Out', 'landtech-extras-for-elementor' ),
						'from-top'			=> __( 'From Top', 'landtech-extras-for-elementor' ),
						'from-right'		=> __( 'From Right', 'landtech-extras-for-elementor' ),
						'from-bottom'		=> __( 'From Bottom', 'landtech-extras-for-elementor' ),
						'from-left'			=> __( 'From Left', 'landtech-extras-for-elementor' ),
						'fade-from-top'		=> __( 'Fade From Top', 'landtech-extras-for-elementor' ),
						'fade-from-right'	=> __( 'Fade From Right', 'landtech-extras-for-elementor' ),
						'fade-from-bottom'	=> __( 'Fade From Bottom', 'landtech-extras-for-elementor' ),
						'fade-from-left'	=> __( 'Fade From Left', 'landtech-extras-for-elementor' ),
						'to-top'			=> __( 'To Top', 'landtech-extras-for-elementor' ),
						'to-right'			=> __( 'To Right', 'landtech-extras-for-elementor' ),
						'to-bottom'			=> __( 'To Bottom', 'landtech-extras-for-elementor' ),
						'to-left'			=> __( 'To Left', 'landtech-extras-for-elementor' ),
						'fade-to-top'		=> __( 'Fade To Top', 'landtech-extras-for-elementor' ),
						'fade-to-right'		=> __( 'Fade To Right', 'landtech-extras-for-elementor' ),
						'fade-to-bottom'	=> __( 'Fade To Bottom', 'landtech-extras-for-elementor' ),
						'fade-to-left'		=> __( 'Fade To Left', 'landtech-extras-for-elementor' ),
					],
					'condition' 	=> [
						'caption_type!' => [],
						'preview_caption_transition!' => '',
					],
				]
			);

			$this->start_controls_tabs( 'preview_caption_style' );

				$this->start_controls_tab( 'preview_caption_style_default', [
					'label' 	=> __( 'Default', 'landtech-extras-for-elementor' ),
					'condition' 	=> [
						'caption_type!' => [],
					],
				] );

					$this->add_control(
						'preview_text_color',
						[
							'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-slider__media__caption' => 'color: {{VALUE}};',
							],
							'condition' 	=> [
								'caption_type!' => [],
							],
						]
					);

					$this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' 		=> 'preview_text_background',
							'types' 	=> [ 'classic', 'gradient' ],
							'selector' 	=> '{{WRAPPER}} .ee-slider__media__caption',
							'default'	=> 'classic',
							'condition' => [
								'caption_type!' => [],
							],
							// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor group control option, not WP_Query.
							'exclude'	=> [
								'image',
							]
						]
					);

					$this->add_control(
						'preview_text_opacity',
						[
							'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-slider__media__caption' => 'opacity: {{SIZE}}',
							],
							'condition'	=> [
								'caption_type!' => [],
							],
						]
					);

					$this->add_group_control(
						Group_Control_Text_Shadow::get_type(),
						[
							'name' 		=> 'preview_text_box_shadow',
							'selector' 	=> '{{WRAPPER}} .ee-slider__media__caption',
							'separator'	=> '',
							'condition'	=> [
								'caption_type!' => [],
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab( 'preview_caption_style_hover', [
					'label' 	=> __( 'Hover', 'landtech-extras-for-elementor' ),
					'condition'	=> [
						'caption_type!' => [],
					],
				] );

					$this->add_control(
						'preview_text_color_hover',
						[
							'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-slider__media:hover .ee-slider__media__caption' => 'color: {{VALUE}};',
							],
							'condition'	=> [
								'caption_type!' => [],
							],
						]
					);

					$this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' 		=> 'preview_text_background_hover',
							'types' 	=> [ 'classic', 'gradient' ],
							'selector' 	=> '{{WRAPPER}} .ee-slider__media:hover .ee-slider__media__caption',
							'default'	=> 'classic',
							'condition' => [
								'caption_type!' => [],
							],
							// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor group control option, not WP_Query.
							'exclude'	=> [
								'image',
							]
						]
					);

					$this->add_control(
						'preview_text_opacity_hover',
						[
							'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-slider__media:hover .ee-slider__media__caption' => 'opacity: {{SIZE}}',
							],
							'condition'	=> [
								'caption_type!' => [],
							],
						]
					);

					$this->add_control(
						'preview_text_border_color_hover',
						[
							'label' 	=> __( 'Border Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-slider__media:hover .ee-slider__media__caption' => 'border-color: {{VALUE}};',
							],
							'condition'	=> [
								'caption_type!' => [],
							],
						]
					);

					$this->add_group_control(
						Group_Control_Text_Shadow::get_type(),
						[
							'name' 		=> 'preview_text_box_shadow_hover',
							'selector' 	=> '{{WRAPPER}} .ee-slider__media:hover .ee-slider__media__caption',
							'separator'	=> '',
							'condition'	=> [
								'caption_type!' => [],
							],
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_thumbnails',
			[
				'label' 	=> __( 'Thumbnails', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_thumbnails!' => '',
				],
			]
		);

			$this->add_control(
				'image_align',
				[
					'label' 		=> __( 'Alignment', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
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
					'prefix_class'		=> 'ee-grid-halign--',
					'condition' => [
						'thumbnails_carousel' => '',
					],
				]
			);
			
			$this->add_control(
				'image_vertical_align',
				[
					'label' 		=> __( 'Vertical Alignment', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'top'    		=> [
							'title' 	=> __( 'Top', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-top',
						],
						'middle' 		=> [
							'title' 	=> __( 'Middle', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-middle',
						],
						'bottom' 		=> [
							'title' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-bottom',
						],
						'stretch' 		=> [
							'title' 	=> __( 'Stretch', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-stretch',
						],
					],
					'condition' => [
						'carousel_orientation!' => 'vertical',
					],
					'prefix_class'		=> 'ee-grid-align--',
				]
			);

			$this->add_control(
				'image_vertical_stretch',
				[
					'label' 		=> __( 'Stretch', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'stretch',
					'return_value'	=> 'stretch',
					'condition' => [
						'carousel_orientation' => 'vertical',
					],
					'prefix_class'		=> 'ee-grid-align--',
				]
			);

			$this->add_responsive_control(
				'image_stretch_ratio',
				[
					'label' 	=> __( 'Image Size Ratio', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> '100'
						],
					'range' 	=> [
						'px' 	=> [
							'min'	=> 10,
							'max' 	=> 200,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__media:before' => 'padding-bottom: {{SIZE}}%;',
					],
					'condition' 		=> [
						'image_vertical_align' => 'stretch',
						'carousel_orientation!' => 'vertical',
					],
				]
			);

			$columns_horizontal_margin = is_rtl() ? 'margin-right' : 'margin-left';
			$columns_horizontal_padding = is_rtl() ? 'padding-right' : 'padding-left';

			$this->add_responsive_control(
				'image_horizontal_spacing',
				[
					'label' 	=> __( 'Horizontal Spacing', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'default' 	=> [
						'size' 	=> 24,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-grid__item' 	=> $columns_horizontal_padding . ': {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-grid' 			=> $columns_horizontal_margin . ': -{{SIZE}}{{UNIT}};',

						'(desktop){{WRAPPER}} .ee-grid__item' 	=> 'max-width: calc( 100% / {{columns.SIZE}} );',
						'(tablet){{WRAPPER}} .ee-grid__item' 	=> 'max-width: calc( 100% / {{columns_tablet.SIZE}} );',
						'(mobile){{WRAPPER}} .ee-grid__item' 	=> 'max-width: calc( 100% / {{columns_mobile.SIZE}} );',
					],
					'condition' => [
						'thumbnails_carousel' => '',
					],
				]
			);

			$this->add_responsive_control(
				'image_vertical_spacing',
				[
					'label' 	=> __( 'Vertical spacing', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'default' 	=> [
						'size' 	=> 24,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-grid__item' 	=> 'padding-bottom: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-grid' 			=> 'margin-bottom: -{{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'thumbnails_carousel' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'image_border',
					'label' 	=> __( 'Image Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media-wrapper',
					'separator' => '',
				]
			);

			$this->add_control(
				'image_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery__media-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);


			$this->add_control(
				'carousel_style_heading',
				[
					'label' 	=> __( 'Carousel', 'landtech-extras-for-elementor' ),
					'separator'	=> 'before',
					'type' 		=> Controls_Manager::HEADING,
					'condition'		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
						'carousel_orientation' => 'vertical',
					],
				]
			);

			$this->add_responsive_control(
				'carousel_height',
				[
					'label' 			=> __( 'Height', 'landtech-extras-for-elementor' ),
					'type' 				=> Controls_Manager::SLIDER,
					'size_units' 	=> ['px'],
					'range' 		=> [
						'px' => [
							'min' => 100,
							'max' => 2000,
						],
					],
					'condition' 		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
						'carousel_orientation' => 'vertical',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery-slider__carousel' => 'height: {{SIZE}}px',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'carousel_arrows_style_heading',
				[
					'label' 	=> __( 'Arrows', 'landtech-extras-for-elementor' ),
					'separator'	=> 'before',
					'type' 		=> Controls_Manager::HEADING,
					'condition'		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
						'carousel_arrows!' => '',
					],
				]
			);

			$this->add_control(
				'carousel_arrows_position',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Position', 'landtech-extras-for-elementor' ),
					'default'		=> 'middle',
					'options' 		=> [
						'top' 		=> __( 'Top', 'landtech-extras-for-elementor' ),
						'middle' 	=> __( 'Middle', 'landtech-extras-for-elementor' ),
						'bottom' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
					],
					'condition'		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
						'carousel_arrows!' 	=> '',
						'carousel_orientation' => 'horizontal',
					],
				]
			);

			$this->add_control(
				'carousel_arrows_position_vertical',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Position', 'landtech-extras-for-elementor' ),
					'default'		=> 'center',
					'options' 		=> [
						'left' 		=> __( 'Left', 'landtech-extras-for-elementor' ),
						'center' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
						'right' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
					],
					'condition'		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
						'carousel_arrows!' 	=> '',
						'carousel_orientation' => 'vertical',
					],
				]
			);

			$this->add_responsive_control(
				'carousel_arrows_size',
				[
					'label' 		=> __( 'Size', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default'		=> [
						'size' => 12,
						'unit' => 'px',
					],
					'range' 		=> [
						'px' 		=> [
							'min' => 12,
							'max' => 48,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__button' => 'font-size: {{SIZE}}px;',
					],
					'condition'		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
						'carousel_arrows!' => '',
					]
				]
			);

			$this->add_responsive_control(
				'carousel_arrows_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default'		=> [
						'size' => 0.4,
						'unit' => 'px',
					],
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 1,
							'step'	=> 0.1,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__button' => 'padding: {{SIZE}}em;',
					],
					'condition'		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
						'carousel_arrows!' => '',
					]
				]
			);

			$this->add_responsive_control(
				'carousel_arrows_distance',
				[
					'label' 		=> __( 'Distance', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default'		=> [
						'size' => 24,
						'unit' => 'px',
					],
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__navigation.ee-swiper__navigation--middle.ee-arrows--horizontal .ee-swiper__button' => 'margin-left: {{SIZE}}px; margin-right: {{SIZE}}px;',
						'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__navigation:not(.ee-swiper__navigation--middle).ee-arrows--horizontal .ee-swiper__button' => 'margin: {{SIZE}}px;',

						'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__navigation .ee-swiper__navigation--center.ee-arrows--vertical .ee-swiper__button' => 'margin-top: {{SIZE}}px; margin-bottom: {{SIZE}}px;',
						'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__navigation:not(.ee-swiper__navigation--center).ee-arrows--vertical .ee-swiper__button' => 'margin: {{SIZE}}px;',
					],
					'condition'		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
						'carousel_arrows!' => '',
					]
				]
			);

			$this->add_responsive_control(
				'carousel_arrows_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__button' => 'border-radius: {{SIZE}}%;',
					],
					'condition'		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
						'carousel_arrows!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'carousel_arrows',
					'selector' 		=> '{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__button,
										{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__button i:before',
					'condition'		=> [
						'show_thumbnails!' => '',
						'thumbnails_carousel!' => '',
						'carousel_arrows!' => '',
					]
				]
			);

			$this->start_controls_tabs( 'carousel_arrows_tabs_hover' );

			$this->start_controls_tab( 'carousel_arrows_tab_default', [
				'label' => __( 'Default', 'landtech-extras-for-elementor' ),
				'condition'	=> [
					'show_thumbnails!' => '',
					'thumbnails_carousel!' => '',
					'carousel_arrows!' => '',
				]
			] );

				$this->add_control(
					'carousel_arrows_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__button i:before' => 'color: {{VALUE}};',
						],
						'condition'		=> [
							'show_thumbnails!' => '',
							'thumbnails_carousel!' => '',
							'carousel_arrows!' => '',
						]
					]
				);

				$this->add_control(
					'carousel_arrows_background_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__button' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							'show_thumbnails!' => '',
							'thumbnails_carousel!' => '',
							'carousel_arrows!' => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'carousel_arrows_tab_hover', [
				'label' => __( 'Hover', 'landtech-extras-for-elementor' ),
				'condition'	=> [
					'carousel_arrows!' => '',
					'show_thumbnails!' => '',
					'thumbnails_carousel!' => '',
				]
			] );

				$this->add_control(
					'carousel_arrows_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__button:hover i:before' => 'color: {{VALUE}};',
						],
						'condition'		=> [
							'show_thumbnails!' => '',
							'thumbnails_carousel!' => '',
							'carousel_arrows!' => '',
						]
					]
				);

				$this->add_control(
					'carousel_arrows_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__button:hover' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							'show_thumbnails!' => '',
							'thumbnails_carousel!' => '',
							'carousel_arrows!' => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'carousel_arrows_tab_disabled', [
				'label' => __( 'Disabled', 'landtech-extras-for-elementor' ),
				'condition'	=> [
					'show_thumbnails!' => '',
					'thumbnails_carousel!' => '',
					'carousel_arrows!' => '',
				]
			] );

				$this->add_responsive_control(
					'carousel_arrows_opacity_disabled',
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
							'{{WRAPPER}} .ee-gallery-slider__gallery .ee-swiper__button.ee-swiper__button--disabled' => 'opacity: {{SIZE}};',
						],
						'condition'	=> [
							'show_thumbnails!' => '',
							'thumbnails_carousel!' => '',
							'carousel_arrows!' => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_captions',
			[
				'label' 	=> __( 'Thumbnails Captions', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'thumbnails_caption_type!' => '',
					'show_thumbnails!' => '',
				],
			]
		);

			$this->add_control(
				'vertical_align',
				[
					'label' 	=> __( 'Vertical Align', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'top' 	=> [
							'title' 	=> __( 'Top', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-top',
						],
						'middle' 		=> [
							'title' 	=> __( 'Middle', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-middle',
						],
						'bottom' 		=> [
							'title' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-v-align-bottom',
						],
					],
					'default' 		=> 'bottom',
					'condition' 	=> [
						'thumbnails_caption_type!' => '',
					],
				]
			);

			$this->add_control(
				'horizontal_align',
				[
					'label' 	=> __( 'Horizontal Align', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' 	=> [
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
							'title' 	=> __( 'Justify', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-stretch',
						],
					],
					'default' 		=> 'justify',
					'condition' 	=> [
						'thumbnails_caption_type!' => '',
					],
				]
			);

			$this->add_control(
				'align',
				[
					'label' 	=> __( 'Text Align', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' 	=> [
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
					'default' 	=> 'center',
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__media__caption' => 'text-align: {{VALUE}};',
					],
					'condition' => [
						'thumbnails_caption_type!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media__caption',
					'condition' => [
						'thumbnails_caption_type!' => '',
					],
				]
			);

			$this->add_control(
				'text_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery__media__caption' 	=> 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'thumbnails_caption_type!' => '',
					],
				]
			);

			$this->add_control(
				'text_margin',
				[
					'label' 		=> __( 'Margin', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery__media__caption' 	=> 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'thumbnails_caption_type!' => '',
					],
					'separator'		=> 'after',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'text_border',
					'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media__caption',
					'separator' => '',
					'condition' => [
						'thumbnails_caption_type!' => '',
					],
				]
			);

			$this->add_control(
				'text_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-gallery__media__caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'thumbnails_caption_type!' => '',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_thumbnails_hover_effects',
			[
				'label' 	=> __( 'Thumbnails Hover Effects', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_thumbnails!' => '',
				],
			]
		);

			$this->add_control(
				'hover_thubmanils_images_heading',
				[
					'label' 	=> __( 'Images', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'image',
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media-wrapper,
									{{WRAPPER}} .ee-gallery__media__thumbnail,
									{{WRAPPER}} .ee-gallery__media__thumbnail img',
					'separator'	=> '',
				]
			);

			$this->update_control( 'image_transition', array(
				'default' => 'custom',
			));

			$this->start_controls_tabs( 'image_style' );

				$this->start_controls_tab( 'image_style_default', [ 'label' => __( 'Default', 'landtech-extras-for-elementor' ), ] );

					$this->add_control(
						'image_background_color',
						[
							'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media-wrapper' => 'background-color: {{VALUE}};',
							],
						]
					);

					$this->add_responsive_control(
						'image_opacity',
						[
							'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__thumbnail img' => 'opacity: {{SIZE}}',
							],
						]
					);

					$this->add_responsive_control(
						'image_scale',
						[
							'label' 		=> __( 'Scale', 'landtech-extras-for-elementor' ),
							'type' 			=> Controls_Manager::SLIDER,
							'range' 		=> [
								'px' 		=> [
									'min' => 1,
									'max' => 2,
									'step'=> 0.01,
								],
							],
							'selectors' 	=> [
								'{{WRAPPER}} .ee-gallery__media__thumbnail img' => 'transform: scale({{SIZE}});',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name' 		=> 'image_box_shadow',
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media-wrapper',
							'separator'	=> '',
						]
					);

					$this->add_group_control(
						Group_Control_Css_Filter::get_type(),
						[
							'name' => 'image_css_filters',
							'selector' => '{{WRAPPER}} .ee-gallery__media__thumbnail img',
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab( 'image_style_hover', [ 'label' 	=> __( 'Hover', 'landtech-extras-for-elementor' ), ] );

					$this->add_control(
						'image_background_color_hover',
						[
							'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media-wrapper' => 'background-color: {{VALUE}};',
							],
						]
					);

					$this->add_responsive_control(
						'image_opacity_hover',
						[
							'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__thumbnail img' => 'opacity: {{SIZE}}',
							],
						]
					);

					$this->add_responsive_control(
						'image_scale_hover',
						[
							'label' 		=> __( 'Scale', 'landtech-extras-for-elementor' ),
							'type' 			=> Controls_Manager::SLIDER,
							'range' 		=> [
								'px' 		=> [
									'min' => 1,
									'max' => 2,
									'step'=> 0.01,
								],
							],
							'selectors' 	=> [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__thumbnail img' => 'transform: scale({{SIZE}});',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name' 		=> 'image_box_shadow_hover',
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media-wrapper',
							'separator'	=> '',
						]
					);

					$this->add_control(
						'image_border_color_hover',
						[
							'label' 	=> __( 'Border Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media-wrapper' => 'border-color: {{VALUE}};',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Css_Filter::get_type(),
						[
							'name' => 'image_css_filters_hover',
							'selector' => '{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__thumbnail img',
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab( 'image_style_active', [ 'label' 	=> __( 'Active', 'landtech-extras-for-elementor' ), ] );

					$this->add_control(
						'image_background_color_active',
						[
							'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media-wrapper' => 'background-color: {{VALUE}};',
							],
						]
					);

					$this->add_responsive_control(
						'image_opacity_active',
						[
							'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__thumbnail img' => 'opacity: {{SIZE}}',
							],
						]
					);

					$this->add_responsive_control(
						'image_scale_active',
						[
							'label' 		=> __( 'Scale', 'landtech-extras-for-elementor' ),
							'type' 			=> Controls_Manager::SLIDER,
							'range' 		=> [
								'px' 		=> [
									'min' => 1,
									'max' => 2,
									'step'=> 0.01,
								],
							],
							'selectors' 	=> [
								'{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__thumbnail img' => 'transform: scale({{SIZE}});',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name' 		=> 'image_box_shadow_active',
							'selector' 	=> '{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media-wrapper',
							'separator'	=> '',
						]
					);

					$this->add_control(
						'image_border_color_active',
						[
							'label' 	=> __( 'Border Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media-wrapper' => 'border-color: {{VALUE}};',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Css_Filter::get_type(),
						[
							'name' => 'image_css_filters_active',
							'selector' => '{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__thumbnail img',
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'hover_thubmanils_captions_heading',
				[
					'label' 	=> __( 'Captions', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' 	=> [
						'thumbnails_caption_type!' => '',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'caption',
					'selector' 		=> '{{WRAPPER}} .ee-gallery__media__content,
										{{WRAPPER}} .ee-gallery__media__caption',
					'condition' 	=> [
						'thumbnails_caption_type!' => '',
					],
				]
			);

			$this->update_control( 'caption_transition', array(
				'default' => 'custom',
			));

			$this->add_control(
				'caption_effect',
				[
					'label' 	=> __( 'Effect', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' => [
						''					=> __( 'None', 'landtech-extras-for-elementor' ),
						'fade-in'			=> __( 'Fade In', 'landtech-extras-for-elementor' ),
						'fade-out'			=> __( 'Fade Out', 'landtech-extras-for-elementor' ),
						'from-top'			=> __( 'From Top', 'landtech-extras-for-elementor' ),
						'from-right'		=> __( 'From Right', 'landtech-extras-for-elementor' ),
						'from-bottom'		=> __( 'From Bottom', 'landtech-extras-for-elementor' ),
						'from-left'			=> __( 'From Left', 'landtech-extras-for-elementor' ),
						'fade-from-top'		=> __( 'Fade From Top', 'landtech-extras-for-elementor' ),
						'fade-from-right'	=> __( 'Fade From Right', 'landtech-extras-for-elementor' ),
						'fade-from-bottom'	=> __( 'Fade From Bottom', 'landtech-extras-for-elementor' ),
						'fade-from-left'	=> __( 'Fade From Left', 'landtech-extras-for-elementor' ),
						'to-top'			=> __( 'To Top', 'landtech-extras-for-elementor' ),
						'to-right'			=> __( 'To Right', 'landtech-extras-for-elementor' ),
						'to-bottom'			=> __( 'To Bottom', 'landtech-extras-for-elementor' ),
						'to-left'			=> __( 'To Left', 'landtech-extras-for-elementor' ),
						'fade-to-top'		=> __( 'Fade To Top', 'landtech-extras-for-elementor' ),
						'fade-to-right'		=> __( 'Fade To Right', 'landtech-extras-for-elementor' ),
						'fade-to-bottom'	=> __( 'Fade To Bottom', 'landtech-extras-for-elementor' ),
						'fade-to-left'		=> __( 'Fade To Left', 'landtech-extras-for-elementor' ),
					],
					'condition' 	=> [
						'thumbnails_caption_type!' 	=> '',
						'caption_transition!' 		=> '',
					],
				]
			);

			$this->start_controls_tabs( 'caption_style' );

				$this->start_controls_tab( 'caption_style_default', [
					'label' 	=> __( 'Default', 'landtech-extras-for-elementor' ),
					'condition' => [
						'thumbnails_caption_type!' => '',
					],
				] );

					$this->add_control(
						'text_color',
						[
							'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__caption' => 'color: {{VALUE}};',
							],
							'condition' => [
								'thumbnails_caption_type!' => '',
							],
						]
					);

					$this->add_control(
						'text_background_color',
						[
							'label' 	=> __( 'Background', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__caption' => 'background-color: {{VALUE}};',
							],
							'condition' => [
								'thumbnails_caption_type!' => '',
							],
						]
					);

					$this->add_control(
						'text_opacity',
						[
							'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__caption' => 'opacity: {{SIZE}}',
							],
							'condition'	=> [
								'thumbnails_caption_type!' => '',
								'tilt_enable' => 'yes',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Text_Shadow::get_type(),
						[
							'name' 		=> 'text_box_shadow',
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media__caption',
							'separator'	=> '',
							'condition' => [
								'thumbnails_caption_type!' => '',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab( 'caption_style_hover', [
					'label' 	=> __( 'Hover', 'landtech-extras-for-elementor' ),
					'condition' => [
						'thumbnails_caption_type!' => '',
					],
				] );

					$this->add_control(
						'text_color_hover',
						[
							'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__caption' => 'color: {{VALUE}};',
							],
							'condition' => [
								'thumbnails_caption_type!' => '',
							],
						]
					);

					$this->add_control(
						'text_background_color_hover',
						[
							'label' 	=> __( 'Background', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__caption' => 'background-color: {{VALUE}};',
							],
							'condition' => [
								'thumbnails_caption_type!' => '',
							],
						]
					);

					$this->add_control(
						'text_opacity_hover',
						[
							'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__caption' => 'opacity: {{SIZE}}',
							],
							'condition'	=> [
								'thumbnails_caption_type!' => '',
								'tilt_enable' => 'yes',
							],
						]
					);

					$this->add_control(
						'text_border_color_hover',
						[
							'label' 	=> __( 'Border Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__caption' => 'border-color: {{VALUE}};',
							],
							'condition' => [
								'thumbnails_caption_type!' => '',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Text_Shadow::get_type(),
						[
							'name' 		=> 'text_box_shadow_hover',
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__caption',
							'separator'	=> '',
							'condition'	=> [
								'thumbnails_caption_type!' => '',
								'tilt_enable' => 'yes',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab( 'caption_style_active', [
					'label' 	=> __( 'Active', 'landtech-extras-for-elementor' ),
					'condition' => [
						'thumbnails_caption_type!' => '',
					],
				] );

					$this->add_control(
						'text_color_active',
						[
							'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__caption' => 'color: {{VALUE}};',
							],
							'condition' => [
								'thumbnails_caption_type!' => '',
							],
						]
					);

					$this->add_control(
						'text_background_color_active',
						[
							'label' 	=> __( 'Background', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__caption' => 'background-color: {{VALUE}};',
							],
							'condition' => [
								'thumbnails_caption_type!' => '',
							],
						]
					);

					$this->add_control(
						'text_opacity_active',
						[
							'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__caption' => 'opacity: {{SIZE}}',
							],
							'condition'	=> [
								'thumbnails_caption_type!' => '',
								'tilt_enable' => 'yes',
							],
						]
					);

					$this->add_control(
						'text_border_color_active',
						[
							'label' 	=> __( 'Border Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'default' 	=> '',
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__caption' => 'border-color: {{VALUE}};',
							],
							'condition' => [
								'thumbnails_caption_type!' => '',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Text_Shadow::get_type(),
						[
							'name' 		=> 'text_box_shadow_active',
							'selector' 	=> '{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__caption',
							'separator'	=> '',
							'condition' => [
								'thumbnails_caption_type!' => '',
							],
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'hover_thubmanils_overlay_heading',
				[
					'label' 	=> __( 'Overlay', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'overlay',
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media__overlay',
					'separator'	=> 'after',
				]
			);

			$this->update_control( 'overlay_transition', array(
				'default' => 'custom',
			));

			$this->start_controls_tabs( 'overlay_style' );

				$this->start_controls_tab( 'overlay_style_default', [ 'label' => __( 'Default', 'landtech-extras-for-elementor' ) ] );

					$this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' 		=> 'overlay_background',
							'types' 	=> [ 'classic', 'gradient' ],
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media__overlay',
							'default'	=> 'classic',
							// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor group control option, not WP_Query.
							'exclude'	=> [
								'image',
							]
						]
					);

					$this->add_control(
						'overlay_blend',
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
								'{{WRAPPER}} .ee-gallery__media__overlay' => 'mix-blend-mode: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'overlay_blend_notice',
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
								'overlay_blend!' => 'normal'
							],
						]
					);

					$this->add_responsive_control(
						'overlay_margin',
						[
							'label' 	=> __( 'Margin', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 48,
									'min' 	=> 0,
									'step' 	=> 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__overlay' => 'top: {{SIZE}}px; right: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px',
							],
						]
					);

					$this->add_responsive_control(
						'overlay_opacity',
						[
							'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default' 	=> [
								'size' 	=> 1,
							],
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__overlay' => 'opacity: {{SIZE}}',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name' 		=> 'overlay_border',
							'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media__overlay',
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab( 'overlay_style_hover', [ 'label' => __( 'Hover', 'landtech-extras-for-elementor' ) ] );

					$this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' 		=> 'overlay_background_hover',
							'types' 	=> [ 'classic', 'gradient' ],
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__overlay',
							'default'	=> 'classic',
							// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor group control option, not WP_Query.
							'exclude'	=> [
								'image',
							]
						]
					);

					$this->add_responsive_control(
						'overlay_margin_hover',
						[
							'label' 	=> __( 'Margin', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 48,
									'min' 	=> 0,
									'step' 	=> 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__overlay' => 'top: {{SIZE}}px; right: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px',
							],
						]
					);

					$this->add_responsive_control(
						'overlay_opacity_hover',
						[
							'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__overlay' => 'opacity: {{SIZE}}',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name' 		=> 'overlay_border_hover',
							'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__overlay',
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab( 'overlay_style_active', [ 'label' => __( 'Active', 'landtech-extras-for-elementor' ) ] );

					$this->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name' 		=> 'overlay_background_active',
							'types' 	=> [ 'classic', 'gradient' ],
							'selector' 	=> '{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__overlay',
							'default'	=> 'classic',
							// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor group control option, not WP_Query.
							'exclude'	=> [
								'image',
							]
						]
					);

					$this->add_responsive_control(
						'overlay_margin_active',
						[
							'label' 	=> __( 'Margin', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 48,
									'min' 	=> 0,
									'step' 	=> 1,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__overlay' => 'top: {{SIZE}}px; right: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px',
							],
						]
					);

					$this->add_responsive_control(
						'overlay_opacity_active',
						[
							'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'range' 	=> [
								'px' 	=> [
									'max' 	=> 1,
									'min' 	=> 0,
									'step' 	=> 0.01,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__overlay' => 'opacity: {{SIZE}}',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name' 		=> 'overlay_border_active',
							'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
							'selector' 	=> '{{WRAPPER}} .ee-gallery__item.is--active .ee-gallery__media__overlay',
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Effective gallery rows for the slider (Media Library or ACF).
	 *
	 * @since 2.2.54
	 *
	 * @param array<string, mixed> $settings Widget settings (display).
	 * @return array<int, array<string, mixed>>
	 */
	protected function resolve_slider_gallery_dataset( array $settings ) {

		$source = isset( $settings['gallery_source'] ) ? (string) $settings['gallery_source'] : 'media_library';

		if ( 'acf_gallery' === $source && ACF_Gallery_Bridge::is_enabled() ) {
			$post_id = isset( $settings['acf_post_id'] ) ? absint( $settings['acf_post_id'] ) : 0;

			return ACF_Gallery_Bridge::get_items(
				isset( $settings['acf_field_name'] ) ? $settings['acf_field_name'] : '',
				$post_id
			);
		}

		if ( ! empty( $settings['wp_gallery'] ) && is_array( $settings['wp_gallery'] ) ) {
			return $settings['wp_gallery'];
		}

		return array();
	}

	/**
	 * Render
	 * 
	 * Render widget contents on frontend
	 *
	 * @since  1.1.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->slider_gallery_dataset = $this->resolve_slider_gallery_dataset( $settings );

		if ( empty( $this->slider_gallery_dataset ) ) {
			if ( $this->_is_edit_mode ) {
				$src = isset( $settings['gallery_source'] ) ? (string) $settings['gallery_source'] : '';
				if ( 'acf_gallery' === $src && ! ACF_Gallery_Bridge::is_enabled() ) {
					echo '<div class="ee-gallery--editor-notice">';
					esc_html_e( 'ACF Gallery requires Advanced Custom Fields (ACF) and a valid Gallery field on the target post.', 'landtech-extras-for-elementor' );
					echo '</div>';
				} elseif ( 'acf_gallery' === $src && ACF_Gallery_Bridge::is_enabled() ) {
					echo '<div class="ee-gallery--editor-notice">';
					esc_html_e( 'No gallery images found. Check the ACF field name, Post ID, and that the field contains images on that post.', 'landtech-extras-for-elementor' );
					echo '</div>';
				}
			}
			return;
		}

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => 'ee-gallery-slider',
			],
			'gallery-thumbnail' => [
				'class' => [
					'ee-media__thumbnail',
					'ee-gallery__media__thumbnail',
				],
			],
			'gallery-overlay' => [
				'class' => [
					'ee-media__overlay',
					'ee-gallery__media__overlay',
				],
			],
			'gallery-content' => [
				'class' => [
					'ee-media__content',
					'ee-gallery__media__content',
				],
			],
			'gallery-caption' => [
				'class' => [
					'wp-caption-text',
					'ee-media__content__caption',
					'ee-gallery__media__caption',
				],
			],
			'gallery-item' => [
				'class' => [
					'ee-gallery__item',
					'ee-grid__item',
				],
			],
		] );

		$gallery_src = isset( $settings['gallery_source'] ) ? (string) $settings['gallery_source'] : '';
		if ( 'acf_gallery' === $gallery_src && ACF_Gallery_Bridge::is_enabled() ) {
			$this->add_render_attribute( 'wrapper', 'data-ltx-acf-gallery', '1' );
		}

		if ( $settings['columns'] ) {
			$this->add_render_attribute( 'shortcode', 'columns', $settings['columns'] );
		}

		if ( ! empty( $settings['gallery_rand'] ) ) {
			$this->add_render_attribute( 'shortcode', 'orderby', $settings['gallery_rand'] );
		}

		?><div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php $this->render_preview(); ?>
			<?php $this->render_gallery(); ?>

		</div><?php
	}

	/**
	 * Render Gallery
	 * 
	 * Renders gallery for thumbnails
	 *
	 * @since  2.2.24
	 * @return void
	 */
	protected function render_gallery() {
		$settings = $this->get_settings_for_display();

		$hide_single = '' !== $settings['thumbnails_hide_single'] && 2 > count( $this->slider_gallery_dataset );

		if ( '' === $settings['show_thumbnails'] || $hide_single ) {
			return;
		}

		$is_carousel = '' !== $settings['thumbnails_carousel'];

		$this->add_render_attribute( [
			'gallery-wrapper' => [
				'class' => [
					'ee-gallery-slider__gallery',
				],
			],
			'gallery' => [
				'class' => [
					'ee-grid',
					'ee-grid--gallery',
					'ee-gallery',
					'ee-gallery__gallery',
					'ee-media-align--' . $settings['vertical_align'],
					'ee-media-align--' . $settings['horizontal_align'],
					'ee-media-effect__content--' . $settings['caption_effect'],
				],
			],
		] );

		if ( $is_carousel ) {
			$this->add_render_attribute( [
				'gallery-wrapper' => [
					'class' => [
						'ee-swiper',
					],
				],
				'swiper-container-wrapper' => [
					'class' => [
						'ee-gallery-slider__carousel-wrapper',
						'ee-swiper__container-wrapper',
					],
				],
				'swiper-container' => [
					'class' => [
						'swiper',
						'ee-swiper__container',
						'ee-gallery-slider__carousel',
					],
				],
				'gallery' => [
					'class' => [
						'ee-swiper__wrapper',
						'swiper-wrapper',
					],
				],
				'gallery-item' => [
					'class' => [
						'ee-swiper__slide',
						'swiper-slide',
					],
				],
			] );
		}

		?><div <?php $this->print_render_attribute_string( 'gallery-wrapper' ); ?>><?php 

		if ( $is_carousel ) {
			?><div <?php $this->print_render_attribute_string( 'swiper-container-wrapper' ); ?>><?php
				?><div <?php $this->print_render_attribute_string( 'swiper-container' ); ?>><?php
		}
					?><div <?php $this->print_render_attribute_string( 'gallery' ); ?>>
						<?php $this->render_gallery_items(); ?>
					</div><?php

		if ( $is_carousel ) {
				?></div><!-- .ee-swiper --><?php

				if ( '' !== $settings['carousel_arrows'] ) {
					$this->render_swiper_navigation( 'carousel',
						$settings['carousel_orientation'],
						$settings['carousel_arrows_position'],
						$settings['carousel_arrows_position_vertical']
					);
				}

			?></div><!-- .ee-swiper__container-wrapper --><?php
		}

		?></div><?php
	}

	/**
	 * Render WP Gallery
	 * 
	 * Render gallery from wp gallery data
	 *
	 * @since  1.1.0
	 * @return void
	 */
	protected function render_gallery_items() {

		$settings 			= $this->get_settings_for_display();
		$gallery 			= $this->slider_gallery_dataset;
		$media_tag 			= 'figure';

		foreach ( $gallery as $index => $item ) {

			$item_url = isset( $item['url'] ) ? $item['url'] : '';

			$image = Module::get_image_info( $item['id'], $item_url, $settings['thumbnail_size'] );

			$gallery_media_key = $this->get_repeater_setting_key( 'gallery-media', 'wp_gallery', $index );
			$gallery_media_wrapper_key = $this->get_repeater_setting_key( 'gallery-media-wrapper', 'wp_gallery', $index );
			$gallery_item_key    = $this->get_repeater_setting_key( 'gallery-item', 'wp_gallery', $index );

			$this->add_render_attribute( [
				$gallery_media_key => [
					'class' => [
						'ee-media',
						'ee-gallery__media',
					],
				],
				$gallery_media_wrapper_key => [
					'class' => [
						'ee-media__wrapper',
						'ee-gallery__media-wrapper',
					],
				],
				$gallery_item_key => [
					'data-gallery-index' => (string) $index,
				],
			] );

			if ( empty( $image ) )
				continue;

			?>

			<div <?php $this->print_render_attribute_string( 'gallery-item' ); ?> <?php $this->print_render_attribute_string( $gallery_item_key ); ?>>

				<<?php echo esc_html( $this->get_gallery_media_tag_name( $media_tag ) ); ?> <?php $this->print_render_attribute_string( $gallery_media_key ); ?>>
					<div <?php $this->print_render_attribute_string( $gallery_media_wrapper_key ); ?>>
						<?php $this->render_image_thumbnail( $image ); ?>
						<?php $this->render_image_overlay(); ?>
						<?php $this->render_image_caption( $item ); ?>
					</div>
				</<?php echo esc_html( $this->get_gallery_media_tag_name( $media_tag ) ); ?>>
				
			</div>

		<?php }
	}

	/**
	 * Render Image Thumbnail
	 *
	 * @since  1.1.0
	 * @param  image|array 		The image information
	 * @return void
	 */
	protected function render_image_thumbnail( $image ) {
		?><div <?php $this->print_render_attribute_string( 'gallery-thumbnail' ); ?>>
			<?php echo wp_kses_post( $image['image'] ); ?>
		</div><?php
	}

	/**
	 * Render Image Caption
	 *
	 * @since  1.1.0
	 * @param  item|array 		The repeater item
	 * @param  settings|array 	The widget settings
	 * @return void
	 */
	protected function render_image_caption( $item ) {
		if ( '' === $this->get_settings('thumbnails_caption_type') ) {
			return;
		}

		$caption = ImageModule::get_image_caption( $item['id'], $this->get_settings('thumbnails_caption_type') );

		if ( ! $caption ) {
			return;
		}

		?><figcaption <?php $this->print_render_attribute_string( 'gallery-content' ); ?>>
			<div <?php $this->print_render_attribute_string( 'gallery-caption' ); ?>>
				<?php echo wp_kses_post( $caption ); ?>
			</div>
		</figcaption><?php
	}

	/**
	 * Render Image Overlay
	 *
	 * @since  1.1.0
	 * @return void
	 */
	protected function render_image_overlay() {
		?><div <?php $this->print_render_attribute_string( 'gallery-overlay' ); ?>></div><?php
	}

	/**
	 * Render Carousel
	 *
	 * @since  1.1.0
	 * @return void
	 */
	private function render_preview() {
		$settings 	= $this->get_settings_for_display();

		$this->add_render_attribute( [
			'preview' => [
				'class' => [
					'ee-gallery-slider__preview',
					'ee-swiper',
				],
			],
			'slider-container' => [
				'class' => 'ee-gallery-slider__slider-wrapper',
			],
			'swiper-wrapper' => [
				'class' => [
					'ee-swiper__container',
					'swiper',
					'ee-gallery-slider__slider',
					'ee-media-align--' . $settings['preview_vertical_align'],
					'ee-media-align--' . $settings['preview_horizontal_align'],
					'ee-media-effect__content--' . $settings['preview_caption_effect'],
				],
			],
			'slider' => [
				'class' => [
					'swiper-wrapper',
					'ee-swiper__wrapper',
				],
			],
		] );

		if ( ! empty( $settings['ken_burns'] ) && '' !== $settings['ken_burns'] ) {
			$this->add_render_attribute( 'swiper-wrapper', 'class', 'ee-swiper__container--kenburns' );

			if ( 'random' === $settings['ken_burns_origin'] ) {
				$this->add_render_attribute( 'swiper-wrapper', 'class', 'ee-swiper__container--kenburns-random' );
			}
		}
		
		?><div <?php $this->print_render_attribute_string( 'preview' ); ?> dir="<?php echo esc_attr( in_array( $settings['direction'], array( 'ltr', 'rtl' ), true ) ? $settings['direction'] : 'ltr' ); ?>">
			<div <?php $this->print_render_attribute_string( 'slider-container' ); ?>>
				<div <?php $this->print_render_attribute_string( 'swiper-wrapper' ); ?>>
					<div <?php $this->print_render_attribute_string( 'slider' ); ?>>
						<?php $this->render_preview_items(); ?>
					</div>
				</div><?php

				if ( '' !== $settings['show_arrows'] ) {
					$this->render_swiper_navigation( 'slider',
						'horizontal',
						$settings['arrows_position']
					);
				}

			?></div><?php
		?></div><?php
	}

	/**
	 * Render Preview Items
	 *
	 * @since  2.2.24
	 * @return void
	 */
	private function render_preview_items() {
		$settings 	= $this->get_settings_for_display();
		$gallery 	= $this->slider_gallery_dataset;

		foreach ( $gallery as $index => $item ) {
			$url 		= Group_Control_Image_Size::get_attachment_image_src( $item['id'], 'preview', $settings );

			if ( ! $url && ! empty( $item['url'] ) ) {
				$url = $item['url'];
			}

			if ( ! $url ) {
				continue;
			}

			$link 		= Module::get_link_url( $item, $settings );
			$captions 	= $settings['caption_type'] ? ImageModule::get_image_caption( $item['id'], $settings['caption_type'] ) : false;

			$slide_key 			= $this->get_repeater_setting_key( 'slide', 'wp_gallery', $index );
			$media_key 			= $this->get_repeater_setting_key( 'media', 'wp_gallery', $index );
			$media_wrapper_key 	= $this->get_repeater_setting_key( 'media-wrapper', 'wp_gallery', $index );
			$thumbnail_key 		= $this->get_repeater_setting_key( 'thumbnail', 'wp_gallery', $index );
			$image_key 			= $this->get_repeater_setting_key( 'image', 'wp_gallery', $index );
			$media_tag 			= 'figure';

			$this->add_render_attribute( [
				$slide_key => [
					'class' => [
						'ee-slider__slide',
						'ee-swiper__slide',
						'swiper-slide',
					],
					'data-gallery-index' => (string) $index,
				],
				$media_key => [
					'class' => [
						'ee-media',
						'ee-slider__media',
						'ee-swiper__slide__media',
					],
				],
				$media_wrapper_key => [
					'class' => [
						'ee-media__wrapper',
						'ee-slider__media-wrapper',
					],
				],
				$thumbnail_key => [
					'class' => [
						'ee-media__thumbnail',
						'ee-slider__media__thumbnail',
					],
				],
				$image_key => [
					'alt' 	=> esc_attr( Control_Media::get_image_alt( $item ) ),
					'src' 	=> esc_attr( $url ),
					'class' => 'ee-media__thumbnail__image',
				],
			] );

			if ( '' !== $settings['slides_custom_height'] ) {
				$this->add_render_attribute( $media_key, 'class', 'ee-media--stretch' );
			}

			if ( $link ) {

				$media_tag = 'a';

				if ( ! empty( $link['url'] ) ) {
					$this->add_render_attribute( $media_key, 'href', $link['url'] );
				}

				if ( ! empty( $link['is_external'] ) ) {
					$this->add_render_attribute( $media_key, 'target', '_blank' );
				}

				if ( ! empty( $link['nofollow'] ) ) {
					$this->add_render_attribute( $media_key, 'rel', 'nofollow' );
				}

				$this->add_lightbox_data_attributes( $media_key, $item['id'], $settings['open_lightbox'], $this->get_id_for_loop() );

				if ( $this->_is_edit_mode ) {
					$this->add_render_attribute( $media_key, 'class', 'elementor-clickable' );
				}
			}

			if ( ! empty( $captions ) ) {

				$content_key = $this->get_repeater_setting_key( 'content', 'wp_gallery', $index );
				$caption_key = $this->get_repeater_setting_key( 'caption', 'wp_gallery', $index );

				$this->add_render_attribute( [
					$content_key => [
						'class' => [
							'ee-media__content',
							'ee-slider__media__content',
						],
					],
					$caption_key => [
						'class' => [
							'ee-caption',
							'ee-media__content__caption',
							'ee-slider__media__caption',
						],
					],
				] );
			}

			?><div <?php $this->print_render_attribute_string( $slide_key ); ?>>
				<<?php echo esc_html( $this->get_gallery_media_tag_name( $media_tag ) ); ?> <?php $this->print_render_attribute_string( $media_key ); ?>>
					<div <?php $this->print_render_attribute_string( $media_wrapper_key ); ?>>
						<div <?php $this->print_render_attribute_string( $thumbnail_key ); ?>>
							<img <?php $this->print_render_attribute_string( $image_key ); ?> />
						</div>
						<?php if ( $captions ) { ?>
						<div <?php $this->print_render_attribute_string( $content_key ); ?>>
							<figcaption <?php $this->print_render_attribute_string( $caption_key ); ?>>
								<?php foreach ( (array)$captions as $caption_type => $caption ) {
									call_user_func_array( '\LandTechExtras\Modules\Image\Module::render_image_' . $caption_type, [ $caption ] );
								} ?>
							</figcaption>
						</div>
						<?php } ?>
					</div>
				</<?php echo esc_html( $this->get_gallery_media_tag_name( $media_tag ) ); ?>>
			</div><?php

		}
	}

	/**
	 * Render Swiper Navigation
	 *
	 * Outputs markup for the swiper navigation
	 *
	 * @since  2.2.24
	 * @return void
	 */
	protected function render_swiper_navigation( $key, $direction, $halign = 'center', $valign = 'middle' ) {

		$settings = $this->get_settings();
		$nav_key = $this->get_repeater_setting_key( 'navigation', 'swiper', $key );

		$this->add_render_attribute( [
			$nav_key => [
				'class' => [
					'ee-arrows',
					'ee-arrows--' . $direction,
					'ee-swiper__navigation',
					'ee-swiper__navigation--inside',
					'ee-swiper__navigation--' . $halign,
					'ee-swiper__navigation--' . $valign,
				],
			],
		] );

		?><div <?php $this->print_render_attribute_string( $nav_key ); ?>><?php
			if ( 2 <= count( $this->slider_gallery_dataset ) ) {
				$this->render_swiper_arrows( $key );
			}
		?></div><?php
	}

	/**
	 * Render Swiper Arrows
	 *
	 * Outputs markup for the swiper arrows navigation
	 *
	 * @since  2.2.24
	 * @return void
	 */
	protected function render_swiper_arrows( $key ) {

		$prev = is_rtl() ? 'right' : 'left';
		$next = is_rtl() ? 'left' : 'right';

		$prev_key 		= $this->get_repeater_setting_key( 'arrow', 'prev', $key );
		$prev_icon_key 	= $this->get_repeater_setting_key( 'arrow-icon', 'prev', $key );
		$next_key 		= $this->get_repeater_setting_key( 'arrow', 'next', $key );
		$next_icon_key 	= $this->get_repeater_setting_key( 'arrow-icon', 'next', $key );

		$this->add_render_attribute( [
			$prev_key => [
				'class' => [
					'ee-swiper__button',
					'ee-swiper__button--prev',
					'ee-arrow',
					'ee-arrow--prev',
					'ee-swiper__button--prev-' . $key,
				],
			],
			$prev_icon_key => [
				'class' => 'eicon-chevron-' . $prev,
			],
			$next_key => [
				'class' => [
					'ee-swiper__button',
					'ee-swiper__button--next',
					'ee-arrow',
					'ee-arrow--next',
					'ee-swiper__button--next-' . $key,
				],
			],
			$next_icon_key => [
				'class' => 'eicon-chevron-' . $next,
			],
		] );

		?><div <?php $this->print_render_attribute_string( $prev_key ); ?>>
			<i <?php $this->print_render_attribute_string( $prev_icon_key ); ?>></i>
		</div>
		<div <?php $this->print_render_attribute_string( $next_key ); ?>>
			<i <?php $this->print_render_attribute_string( $next_icon_key ); ?>></i>
		</div><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  1.1.0
	 * @return void
	 */
	protected function content_template() {}
}
