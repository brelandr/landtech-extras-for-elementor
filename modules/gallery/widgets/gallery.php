<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Gallery\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\LandTechExtrasPlugin as Plugin;
use LandTechExtras\Group_Control_Transition;
use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Gallery\ACF_Gallery_Bridge;
use LandTechExtras\Modules\Gallery\Module;
use LandTechExtras\Modules\Image\Module as ImageModule;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Gallery
 *
 * @since 2.1.0
 */
class Gallery extends Extras_Widget {

	/**
	 * Instagram Access token.
	 *
	 * @since 2.1.0
	 * @var   string
	 */
	private $insta_access_token = null;

	/**
	 * Instagram API URL.
	 *
	 * @since 2.1.0
	 * @var   string
	 */
	private $insta_api_url = 'https://www.instagram.com/';

	/**
	 * Official Instagram API URL.
	 *
	 * @since 2.1.0
	 * @var   string
	 */
	private $insta_official_api_url = 'https://graph.instagram.com/';

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_name() {
		return 'gallery-extra';
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
		return __( 'Gallery', 'landtech-extras-for-elementor' );
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
		return 'nicon nicon-image-gallery';
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
			'landtech-extras-tilt',
			'landtech-extras-parallax-gallery',
			'landtech-extras-jquery-resize',
			'landtech-extras-isotope',
			'imagesloaded',
		];
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_gallery',
			[
				'label' => __( 'Gallery', 'landtech-extras-for-elementor' ),
			]
		);

			$gallery_type_options = [
				'wordpress'	=> __( 'Wordpress', 'landtech-extras-for-elementor' ),
				'manual' 		=> __( 'Manual', 'landtech-extras-for-elementor' ),
				'instagram' 	=> __( 'Instagram', 'landtech-extras-for-elementor' ),
			];

			if ( ACF_Gallery_Bridge::is_enabled() ) {
				$gallery_type_options['acf_gallery'] = __( 'ACF Gallery', 'landtech-extras-for-elementor' );
			}

			$this->add_control(
				'gallery_type',
				[
					'label' 	=> __( 'Type', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'wordpress',
					'options' 	=> $gallery_type_options,
				]
			);

			$this->add_control(
				'insta_display',
				[
					'label' 	=> __( 'Display', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'feed',
					'options' 	=> [
						'feed'	=> __( 'My Photos', 'landtech-extras-for-elementor' ),
						'tags'	=> __( 'Tagged Photos', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						'gallery_type' => 'instagram',
					],
				]
			);

			if ( ! $this->get_insta_global_access_token() ) {
				$this->add_control(
					'access_token_missing',
					[
						'type' 				=> Controls_Manager::RAW_HTML,
						'raw'  				=> sprintf(
												/* translators: 1–2: link to APIs settings, 3–4: link to Instagram token documentation. */
												__( 'The global Instagram access token is missing. You can use a custom one below or add it %1$shere%2$s. Find out %3$show to get your access token%4$s.', 'landtech-extras-for-elementor' ),
												'<a target="_blank" href="' . admin_url( 'admin.php?page=landtech-extras#landtech_extras_apis' ) . '">',
												'</a>',
												'<a target="_blank" href="' . Plugin::$instance->get_link('docs_ig_token') . '">',
												'</a>'
											),
						'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
						'condition' 		=> [
							'gallery_type' 	=> 'instagram',
							'insta_display' => 'feed',
						],
					]
				);
			}

			$this->add_control(
				'access_token',
				[
					'label' 		=> __( 'Override Global Access Token', 'landtech-extras-for-elementor' ),
					'description'	=> sprintf(
						/* translators: 1–2: opening and closing link markup to Instagram token documentation. */
						__( 'Leave blank to use the global token set under Elementor > Extras > APIs. %1$sHow to get an access token%2$s', 'landtech-extras-for-elementor' ),
						'<a target="_blank" href="' . Plugin::$instance->get_link('docs_ig_token') . '">',
						'</a>'
					),
					'label_block'	=> true,
					'default'		=> '',
					'type'			=> Controls_Manager::TEXT,
					'condition' 	=> [
						'gallery_type' 	=> 'instagram',
						'insta_display' => 'feed',
					],
				]
			);

			$this->add_control(
				'insta_hashtag',
				[
					'label' 			=> __( 'Hashtag', 'landtech-extras-for-elementor' ),
					'description' 		=> __( 'Enter without the # symbol', 'landtech-extras-for-elementor' ),
					'type'  			=> Controls_Manager::TEXT,
					'condition' 		=> [
						'gallery_type' 	=> 'instagram',
						'insta_display' => 'tags',
					],
					'dynamic' 			=> [
						'active' 		=> true,
						'categories' 	=> [
							TagsModule::POST_META_CATEGORY,
						],
					],
				]
			);

			$gallery_items = new Repeater();

			$gallery_items->add_control(
				'image',
				[
					'label' 	=> __( 'Choose Image', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::MEDIA,
					'default' 	=> [
						'url' 	=> Utils::get_placeholder_image_src(),
					],
				]
			);

			$gallery_items->add_control(
				'link',
				[
					'label' 	=> __( 'Link to', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'file',
					'options' 	=> [
						'file' 			=> __( 'Media File', 'landtech-extras-for-elementor' ),
						'attachment' 	=> __( 'Attachment Page', 'landtech-extras-for-elementor' ),
						'custom' 		=> __( 'Custom URL', 'landtech-extras-for-elementor' ),
						'' 				=> __( 'None', 'landtech-extras-for-elementor' ),
					],
				]
			);

			$gallery_items->add_control(
				'link_url',
				[
					'label' 		=> __( 'Link', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::URL,
					'placeholder' 	=> esc_url( home_url( '/' ) ),
					'default' 		=> [
						'url' 		=> esc_url( home_url( '/' ) ),
					],
					'condition'	=> [
						'link'	=> 'custom',
					]
				]
			);

			$gallery_items->start_controls_tabs('custom');

				$gallery_items->start_controls_tab(
					'custom_desktop',
					[
						'label' => __( 'Desktop', 'landtech-extras-for-elementor' ),
					]
				);

					$gallery_items->add_control(
						'custom_size',
						[
							'label'			=> __( 'Custom Size', 'landtech-extras-for-elementor' ),
							'type' 			=> Controls_Manager::SWITCHER,
							'default' 		=> '',
							'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
							'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
							'return_value' 	=> 'yes',
						]
					);

					$gallery_items->add_control(
						'width',
						[
							'label' 		=> __( 'Width', 'landtech-extras-for-elementor' ),
							'type' 			=> Controls_Manager::SELECT,
							'label_block' 	=> true,
							'default' 		=> '',
							'options' 		=> [
								'' 			=> __( 'Default', 'landtech-extras-for-elementor' ),
								'100%' 		=> __( 'Full Width', 'landtech-extras-for-elementor' ),
								'50%' 		=> __( 'One Half', 'landtech-extras-for-elementor' ),
								'33.3333%' 	=> __( 'One Third', 'landtech-extras-for-elementor' ),
								'66.6666%' 	=> __( 'Two Thirds', 'landtech-extras-for-elementor' ),
								'25%' 		=> __( 'One Quarter', 'landtech-extras-for-elementor' ),
								'75%' 		=> __( 'Three Quarters', 'landtech-extras-for-elementor' ),
								'20%' 		=> __( 'One Fifth', 'landtech-extras-for-elementor' ),
								'40%' 		=> __( 'Two Fifths', 'landtech-extras-for-elementor' ),
								'60%' 		=> __( 'Three Fifths', 'landtech-extras-for-elementor' ),
								'80%' 		=> __( 'Four Fifths', 'landtech-extras-for-elementor' ),
								'16.6666%' 	=> __( 'One Sixth', 'landtech-extras-for-elementor' ),
								'83.3333%' 	=> __( 'Five Sixths', 'landtech-extras-for-elementor' ),
							],
							'selectors' => [
								'(desktop+){{WRAPPER}} {{CURRENT_ITEM}}.ee-grid__item--custom-size' => 'width: {{VALUE}};',
							],
							'condition' => [
								'custom_size!' => ''
							],
						]
					);

					$gallery_items->add_control(
						'height_ratio',
						[
							'label' 	=> __( 'Image Size Ratio', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default'	=> [
								'size'	=> '',
							],
							'range' 	=> [
								'px' 	=> [
									'min'	=> 10,
									'max' 	=> 200,
								],
							],
							'selectors' => [
								'(desktop+){{WRAPPER}} {{CURRENT_ITEM}} .ee-media--stretch:before' => 'padding-bottom: {{SIZE}}%;',
							],
							'condition' => [
								'custom_size!' => ''
							],
						]
					);

				$gallery_items->end_controls_tab();

				$gallery_items->start_controls_tab(
					'custom_tablet',
					[
						'label' => __( 'Tablet', 'landtech-extras-for-elementor' ),
					]
				);

					$gallery_items->add_control(
						'custom_size_tablet',
						[
							'label'			=> __( 'Custom Size', 'landtech-extras-for-elementor' ),
							'type' 			=> Controls_Manager::SWITCHER,
							'default' 		=> '',
							'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
							'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
							'return_value' 	=> 'yes',
						]
					);

					$gallery_items->add_control(
						'width_tablet',
						[
							'label' 		=> __( 'Width', 'landtech-extras-for-elementor' ),
							'type' 			=> Controls_Manager::SELECT,
							'default' 		=> '',
							'label_block' 	=> true,
							'options' 		=> [
								'' 			=> __( 'Default', 'landtech-extras-for-elementor' ),
								'100%' 		=> __( 'Full Width', 'landtech-extras-for-elementor' ),
								'50%' 		=> __( 'One Half', 'landtech-extras-for-elementor' ),
								'33.3333%' 	=> __( 'One Third', 'landtech-extras-for-elementor' ),
								'66.6666%' 	=> __( 'Two Thirds', 'landtech-extras-for-elementor' ),
								'25%' 		=> __( 'One Quarter', 'landtech-extras-for-elementor' ),
								'75%' 		=> __( 'Three Quarters', 'landtech-extras-for-elementor' ),
								'20%' 		=> __( 'One Fifth', 'landtech-extras-for-elementor' ),
								'40%' 		=> __( 'Two Fifths', 'landtech-extras-for-elementor' ),
								'60%' 		=> __( 'Three Fifths', 'landtech-extras-for-elementor' ),
								'80%' 		=> __( 'Four Fifths', 'landtech-extras-for-elementor' ),
								'16.6666%' 	=> __( 'One Sixth', 'landtech-extras-for-elementor' ),
								'83.3333%' 	=> __( 'Five Sixths', 'landtech-extras-for-elementor' ),
							],
							'selectors' => [
								'(tablet+)(tablet-){{WRAPPER}} {{CURRENT_ITEM}}.ee-grid__item--custom-size' => 'width: {{VALUE}};',
							],
							'condition' => [
								'custom_size_tablet!' => ''
							],
						]
					);

					$gallery_items->add_control(
						'height_ratio_tablet',
						[
							'label' 	=> __( 'Image Size Ratio', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default'	=> [
								'size'	=> '',
							],
							'range' 	=> [
								'px' 	=> [
									'min'	=> 10,
									'max' 	=> 200,
								],
							],
							'selectors' => [
								'(tablet+)(tablet-){{WRAPPER}} {{CURRENT_ITEM}} .ee-media--stretch:before' => 'padding-bottom: {{SIZE}}%;',
							],
							'condition' => [
								'custom_size_tablet!' => ''
							],
						]
					);

				$gallery_items->end_controls_tab();

				$gallery_items->start_controls_tab(
					'custom_mobile',
					[
						'label' => __( 'Mobile', 'landtech-extras-for-elementor' ),
					]
				);

					$gallery_items->add_control(
						'custom_size_mobile',
						[
							'label'			=> __( 'Custom Size', 'landtech-extras-for-elementor' ),
							'type' 			=> Controls_Manager::SWITCHER,
							'default' 		=> '',
							'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
							'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
							'return_value' 	=> 'yes',
						]
					);

					$gallery_items->add_control(
						'width_mobile',
						[
							'label' 		=> __( 'Width', 'landtech-extras-for-elementor' ),
							'type' 			=> Controls_Manager::SELECT,
							'default' 		=> '',
							'label_block' 	=> true,
							'options' 		=> [
								'' 			=> __( 'Default', 'landtech-extras-for-elementor' ),
								'100%' 		=> __( 'Full Width', 'landtech-extras-for-elementor' ),
								'50%' 		=> __( 'One Half', 'landtech-extras-for-elementor' ),
								'33.3333%' 	=> __( 'One Third', 'landtech-extras-for-elementor' ),
								'66.6666%' 	=> __( 'Two Thirds', 'landtech-extras-for-elementor' ),
								'25%' 		=> __( 'One Quarter', 'landtech-extras-for-elementor' ),
								'75%' 		=> __( 'Three Quarters', 'landtech-extras-for-elementor' ),
								'20%' 		=> __( 'One Fifth', 'landtech-extras-for-elementor' ),
								'40%' 		=> __( 'Two Fifths', 'landtech-extras-for-elementor' ),
								'60%' 		=> __( 'Three Fifths', 'landtech-extras-for-elementor' ),
								'80%' 		=> __( 'Four Fifths', 'landtech-extras-for-elementor' ),
								'16.6666%' 	=> __( 'One Sixth', 'landtech-extras-for-elementor' ),
								'83.3333%' 	=> __( 'Five Sixths', 'landtech-extras-for-elementor' ),
							],
							'selectors' => [
								'(mobile){{WRAPPER}} {{CURRENT_ITEM}}.ee-grid__item--custom-size' => 'width: {{VALUE}};',
							],
							'condition' => [
								'custom_size_mobile!' => ''
							],
						]
					);

					$gallery_items->add_control(
						'height_ratio_mobile',
						[
							'label' 	=> __( 'Image Size Ratio', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::SLIDER,
							'default'	=> [
								'size'	=> '',
							],
							'range' 	=> [
								'px' 	=> [
									'min'	=> 10,
									'max' 	=> 200,
								],
							],
							'selectors' => [
								'(mobile){{WRAPPER}} {{CURRENT_ITEM}} .ee-media--stretch:before' => 'padding-bottom: {{SIZE}}%;',
							],
							'condition' => [
								'custom_size_mobile!' => ''
							],
						]
					);

				$gallery_items->end_controls_tab();

			$gallery_items->end_controls_tab();

			$this->add_control(
				'gallery',
				[
					'label' 	=> __( 'Images', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[],
						[],
						[],
						[],
						[],
						[],
					],
					'fields' 		=> $gallery_items->get_controls(),
					'condition'		=> [
						'gallery_type' => 'manual',
					]
				]
			);

			$this->add_control(
				'images_heading',
				[
					'label' 	=> __( 'Images', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'	=> [
						'gallery_type' => [ 'wordpress', 'acf_gallery' ],
					],
				]
			);

			$this->add_control(
				'wp_gallery',
				[
					'label' 	=> __( 'Add Images', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::GALLERY,
					'dynamic'	=> [
						'active' => true,
					],
					'condition'	=> [
						'gallery_type' => 'wordpress',
					],
				]
			);

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
							'gallery_type' => 'acf_gallery',
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
							'gallery_type' => 'acf_gallery',
						],
					]
				);
			}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_settings',
			[
				'label' => __( 'Settings', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' 		=> 'thumbnail',
					'default'	=> 'full',
					'condition'	=> [
						'gallery_type!'	 => 'instagram',
					],
				]
			);

			$this->add_control(
				'insta_image_size',
				[
					'label'   => __( 'Image Size', 'landtech-extras-for-elementor' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'standard',
					'options' => [
						'thumbnail' => __( 'Thumbnail (150x150)', 'landtech-extras-for-elementor' ),
						'low'       => __( 'Low (320x320)', 'landtech-extras-for-elementor' ),
						'standard'  => __( 'Standard (640x640)', 'landtech-extras-for-elementor' ),
						'high'      => __( 'High (original)', 'landtech-extras-for-elementor' ),
					],
					'condition'	=> [
						'gallery_type'	 => 'instagram',
					],
				]
			);

			$this->add_responsive_control(
				'columns',
				[
					'label' 	=> __( 'Columns', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '3',
					'tablet_default' 	=> '2',
					'mobile_default' 	=> '1',
					'options' 			=> [
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					],
					'prefix_class'	=> 'ee-grid-columns%s-',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'columns_notice',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'If you are specifying the widths for each image individually, set this to correspond to the lowest width in your gallery.', 'landtech-extras-for-elementor' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
					'condition'			=> [
						'gallery_type'	 => 'manual',
					]
				]
			);

			$this->add_control(
				'gallery_link',
				[
					'label' 	=> __( 'Link to', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'file',
					'options' 	=> [
						'file' 			=> __( 'Media File', 'landtech-extras-for-elementor' ),
						'attachment' 	=> __( 'Attachment Page', 'landtech-extras-for-elementor' ),
						'' 				=> __( 'None', 'landtech-extras-for-elementor' ),
					],
					'condition'	=> [
						'gallery_type'	=> [ 'wordpress', 'instagram', 'acf_gallery' ],
					],
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
						'gallery_link' => 'file',
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
						'gallery_link' => 'file',
						'open_lightbox' => ['default', 'yes'],
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
				]
			);

			$this->add_control(
				'gallery_display_caption',
				[
					'label' 	=> __( 'Caption', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 		=> __( 'Show', 'landtech-extras-for-elementor' ),
						'none' 	=> __( 'Hide', 'landtech-extras-for-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__media__caption' => 'display: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'gallery_caption',
				[
					'label' 	=> __( 'Caption Type', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'caption',
					'options' 	=> [
						'title' 		=> __( 'Title', 'landtech-extras-for-elementor' ),
						'caption' 		=> __( 'Caption', 'landtech-extras-for-elementor' ),
						'description' 	=> __( 'Description', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						'gallery_display_caption' 	=> '',
						'gallery_type!' 			=> 'instagram',
					],
				]
			);

			$this->add_control(
				'view',
				[
					'label' 	=> __( 'View', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HIDDEN,
					'default' 	=> 'traditional',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_instagram',
			[
				'label' 	=> __( 'Instagram', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_CONTENT,
				'condition'	=> [
					'gallery_type' => 'instagram',
					'gallery_display_caption' => '',
				],
			]
		);

			$this->add_control(
				'insta_counter_comments',
				[
					'label'			=> __( 'Show Comments', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Show', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'Hide', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
					'condition'		=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'insta_counter_likes',
				[
					'label'			=> __( 'Show Likes', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Show', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'Hide', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
					'condition'		=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'insta_counter_caption',
				[
					'label'			=> __( 'Show Caption', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Show', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'Hide', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
					'condition'		=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'insta_caption_length',
				[
					'label' 			=> __( 'Caption Length', 'landtech-extras-for-elementor' ),
					'type'  			=> Controls_Manager::NUMBER,
					'default'			=> 30,
					'condition' 		=> [
						'gallery_type' 	=> 'instagram',
					],
					'dynamic' 			=> [
						'active' 		=> true,
					],
				]
			);

			$this->add_control(
				'insta_posts_counter',
				[
					'label' 			=> __( 'Number of Posts', 'landtech-extras-for-elementor' ),
					'type'  			=> Controls_Manager::NUMBER,
					'default'			=> 10,
					'condition' 		=> [
						'gallery_type' 	=> 'instagram',
					],
					'dynamic' 			=> [
						'active' 		=> true,
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_parallax',
			[
				'label' 	=> __( 'Parallax', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'parallax_enable',
				[
					'label'			=> __( 'Parallax', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'yes',
					'separator'		=> 'before',
					'frontend_available' => true,
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
					'condition' => [
						'parallax_enable' => 'yes',
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
						'size'	=> 0.5
					],
					'tablet_default' => [
						'size'	=> 0.5
					],
					'mobile_default' => [
						'size'	=> 0.5
					],
					'range' 	=> [
						'px' 	=> [
							'min'	=> 0.05,
							'max' 	=> 1,
							'step'	=> 0.01,
						],
					],
					'condition' => [
						'parallax_enable' => 'yes',
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'image_distance',
				[
					'label' 	=> __( 'Parallax Distance (%)', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' 	=> [
						'size' 	=> '10',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__item.is--3d .ee-gallery__media' => 'margin-left: calc({{SIZE}}%/2); margin-right: calc({{SIZE}}%/2);',
					],
					'condition' => [
						'parallax_enable' 		=> 'yes',
						'image_vertical_align!' => 'stretch',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_masonry',
			[
				'label' 	=> __( 'Masonry', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_CONTENT,
				'condition' 	=> [
					'parallax_enable!' 		=> 'yes',
				],
			]
		);

			$this->add_control(
				'masonry_enable',
				[
					'label'			=> __( 'Enable', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'separator'		=> 'before',
					'condition' 	=> [
						'parallax_enable!' 		=> 'yes',
					],
				]
			);

			$this->add_control(
				'masonry_layout',
				[
					'label' 		=> __( 'Layout', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'columns',
					'options' 		=> [
						'columns'    	=> [
							'title' 	=> __( 'Columns', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'nicon nicon-masonry-columns',
						],
						'mixed' 		=> [
							'title' 	=> __( 'Mixed', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'nicon nicon-masonry-mixed',
						],
					],
					'label_block'	=> false,
					'condition' 	=> [
						'masonry_enable!' 		=> '',
						'parallax_enable!' 		=> 'yes',
					],
					'prefix_class'		=> 'ee-grid-masonry-layout--',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_tilt',
			[
				'label' 	=> __( 'Tilt', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'tilt_enable',
				[
					'label'			=> __( 'Enable', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'tilt_depth',
				[
					'label'			=> __( 'Depth', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'condition'		=> [
						'tilt_enable!' => '',
					],
				]
			);

			$this->add_control(
				'tile_depth_warning',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw'  				=> __( 'Depth disables CSS overflow: hidden which disables border radius for thumbnails.', 'landtech-extras-for-elementor' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-warning',
					'condition' 		=> [
						'tilt_enable!' 	=> '',
						'tilt_depth!' 	=> '',
					],
				]
			);

			$this->add_control(
				'tilt_axis',
				[
					'label'			=> __( 'Axis', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> '',
					'options' 			=> [
						'' 		=> __( 'Both', 'landtech-extras-for-elementor' ),
						'x' 	=> __( 'X Only', 'landtech-extras-for-elementor' ),
						'y' 	=> __( 'Y Only', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
					'condition' => [
						'tilt_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'tilt_amount',
				[
					'label' 	=> __( 'Amount', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 10,
							'max' => 40,
						],
					],
					'default' 	=> [
						'size' 	=> 20,
					],
					'frontend_available' => true,
					'condition' => [
						'tilt_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'tilt_caption_depth',
				[
					'label' 	=> __( 'Depth', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'default' 	=> [
						'size' 	=> 20,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__tilt .ee-gallery__media__content' => 'transform: translateZ({{SIZE}}px);',
					],
					'condition' => [
						'tilt_enable!' => '',
						'tilt_depth!' => '',
					],
				]
			);

			$this->add_control(
				'tilt_scale',
				[
					'label' 	=> __( 'Scale', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 1,
							'max' 	=> 1.5,
							'step'	=> 0.01,
						],
					],
					'default' 		=> [
						'size' 		=> 1.05,
					],
					'frontend_available' => true,
					'condition' => [
						'tilt_enable' => 'yes',
					],
				]
			);

			$this->add_control(
				'tilt_speed',
				[
					'label' 	=> __( 'Speed', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 100,
							'max' 	=> 1000,
							'step'	=> 50,
						],
					],
					'default' 		=> [
						'size' 		=> 800,
					],
					'frontend_available' => true,
					'condition' => [
						'tilt_enable' => 'yes',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_layout',
			[
				'label' 	=> __( 'Layout', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'image_align',
				[
					'label' 		=> __( 'Horizontal Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'left',
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
					'prefix_class'		=> 'ee-grid-halign%s--',
					'condition'			=> [
						'masonry_enable' => '',
					],
				]
			);

			$this->add_responsive_control(
				'image_vertical_align',
				[
					'label' 		=> __( 'Vertical Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'top',
					'options' 		=> [
						'top'    			=> [
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
					'prefix_class'		=> 'ee-grid-align%s--',
					'condition'			=> [
						'masonry_enable' => '',
					],
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
					'condition' => [
						'image_vertical_align' 	=> 'stretch',
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__media:before' => 'padding-bottom: {{SIZE}}%;',
					],
				]
			);

			$columns_horizontal_margin = is_rtl() ? 'margin-left' : 'margin-right';
			$columns_horizontal_padding = is_rtl() ? 'padding-left' : 'padding-right';

			$this->add_control(
				'image_horizontal_space',
				[
					'label' 	=> __( 'Horizontal Spacing', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'custom',
					'options' 	=> [
						'none' 		=> __( 'None', 'landtech-extras-for-elementor' ),
						'custom' 	=> __( 'Custom', 'landtech-extras-for-elementor' ),
						'overlap' 	=> __( 'Overlap', 'landtech-extras-for-elementor' ),
					],
					'condition'		=> [
						'masonry_layout!' => 'mixed',
					],
				]
			);

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
						'{{WRAPPER}} .ee-gallery' 		=> $columns_horizontal_margin . ': -{{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-gallery__item' => $columns_horizontal_padding . ': {{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'image_horizontal_space' => 'custom',
						'masonry_layout!' => 'mixed',
					],
				]
			);

			$this->add_responsive_control(
				'image_overlap',
				[
					'label' 	=> __( 'Horizontal Overlap', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'default' 	=> [
						'size' 	=> 0,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-gallery' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-gallery__item .ee-gallery__media' => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'image_horizontal_space' => 'overlap',
						'masonry_layout!' => 'mixed',
					],
				]
			);

			$this->add_responsive_control(
				'image_vertical_spacing',
				[
					'label' 	=> __( 'Vertical Spacing', 'landtech-extras-for-elementor' ),
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
						'{{WRAPPER}} .ee-gallery__item .ee-gallery__media' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'masonry_layout!' => 'mixed',
					],
				]
			);

			$this->add_responsive_control(
				'image_mixed_masonry_spacing',
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
						'{{WRAPPER}} .ee-gallery__media-wrapper' => 'margin: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-gallery' => 'margin: -{{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'masonry_enable!' => '',
						'masonry_layout' => 'mixed',
					],
				]
			);

			$this->add_responsive_control(
				'overflow',
				[
					'label'			=> __( 'Overflow', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Hiding overflow solves the horizontal scroll issue on mobile devices, but affects shadows and tilt effects which will be hidden outside the grid area.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'separator'		=> 'before',
					'default' 		=> '',
					'tablet_default'=> 'yes',
					'mobile_default'=> 'yes',
					'label_on' 		=> __( 'Hidden', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'Visible', 'landtech-extras-for-elementor' ),
					'prefix_class'	=> 'ee-gallery-overflow%s--',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_gallery_images',
			[
				'label' 	=> __( 'Thumbnails', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
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
				'image_background_color',
				[
					'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-gallery__media__thumbnail' => 'background-color: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			[
				'label' 	=> __( 'Captions', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition' => [
					'gallery_display_caption' => '',
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
						'gallery_display_caption' => '',
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
					'prefix_class'	=> 'ee-media-align--',
					'condition' 	=> [
						'gallery_display_caption' => '',
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
					'prefix_class'	=> 'ee-media-align--',
					'condition' 	=> [
						'gallery_display_caption' => '',
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
						'gallery_display_caption' => '',
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
						'gallery_display_caption' => '',
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
						'gallery_display_caption' => '',
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
					'condition' => [
						'gallery_display_caption' => '',
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
						'gallery_display_caption' => '',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_instagram_style',
			[
				'label' 	=> __( 'Instagram', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'gallery_type' => 'instagram',
					'gallery_display_caption' => '',
				],
			]
		);

			$this->add_control(
				'insta_counters_heading',
				[
					'label' 	=> __( 'Counters', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_responsive_control(
				'insta_counters_align',
				[
					'label' 		=> __( 'Horizontal Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'center',
					'label_block'	=> false,
					'options' 		=> [
						'flex-start'    => [
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
					'selectors'			=> [
						'{{WRAPPER}} .ee-caption__insta' => 'justify-content: {{VALIE}}',
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_responsive_control(
				'insta_counters_distance',
				[
					'label' 		=> __( 'Distance', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-caption__insta:not(:first-child)' => 'padding-top: {{SIZE}}px;',
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_responsive_control(
				'insta_counters_spacing',
				[
					'label' 	=> __( 'Spacing', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-caption__insta__counter:not(:first-child)' => 'margin-left: {{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_control(
				'insta_icons_heading',
				[
					'label' 	=> __( 'Icons', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
					'type' 		=> Controls_Manager::HEADING,
				]
			);

			$this->add_responsive_control(
				'insta_icons_style',
				[
					'label' 		=> __( 'Style', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'center',
					'label_block'	=> false,
					'options' 		=> [
						'solid'    		=> [
							'title' 	=> __( 'Solid', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-comment',
						],
						'outline' 		=> [
							'title' 	=> __( 'Outline', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-comment-o',
						],
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_responsive_control(
				'insta_icons_spacing',
				[
					'label' 	=> __( 'Spacing', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 200,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-caption__insta__icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

			$this->add_responsive_control(
				'insta_icons_size',
				[
					'label' 	=> __( 'Size', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' => 0,
							'max' => 2,
							'step' => 0.1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-caption__insta__icon' => 'font-size: {{SIZE}}em;',
					],
					'condition'	=> [
						'gallery_type' => 'instagram',
						'gallery_display_caption' => '',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hover_effects',
			[
				'label' 	=> __( 'Hover Effects', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'hover_images_heading',
				[
					'label' 	=> __( 'Images', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'image_transition',
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media-wrapper,
									{{WRAPPER}} .ee-gallery__media__thumbnail img',
					'separator'	=> '',
				]
			);

			$this->start_controls_tabs( 'image_style' );

				$this->start_controls_tab(
					'image_style_default',
					[
						'label' => __( 'Default', 'landtech-extras-for-elementor' ),
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
							'condition' 	=> [
								'tilt_enable!' => 'yes',
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

				$this->start_controls_tab(
					'image_style_hover',
					[
						'label' 	=> __( 'Hover', 'landtech-extras-for-elementor' ),
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
							'condition' 	=> [
								'tilt_enable!' => 'yes',
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

			$this->end_controls_tabs();

			$this->add_control(
				'hover_overlay_heading',
				[
					'label' 	=> __( 'Overlay', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 		=> 'overlay_transition',
					'selector' 	=> '{{WRAPPER}} .ee-gallery__media__overlay',
				]
			);

			$this->start_controls_tabs( 'overlay_style' );

				$this->start_controls_tab( 'overlay_style_default', [ 'label' => __( 'Default', 'landtech-extras-for-elementor' ) ] );

					$this->add_control(
						'overlay_background_color',
						[
							'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media__overlay' => 'background-color: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'overlay_blend',
						[
							'label' 		=> __( 'Blend mode', 'landtech-extras-for-elementor' ),
							'description'	=> __( 'Using blend mode removes the impact of depth properties from the tilt effect.', 'landtech-extras-for-elementor' ),
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

					$this->add_control(
						'overlay_background_color_hover',
						[
							'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__overlay' => 'background-color: {{VALUE}};',
							],
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

			$this->end_controls_tabs();

			$this->add_control(
				'hover_captions_heading',
				[
					'label' 	=> __( 'Captions', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator'	=> 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'content',
					'selector' 		=> '{{WRAPPER}} .ee-gallery__media__content,
										{{WRAPPER}} .ee-gallery__media__caption',
					'condition' 	=> [
						'gallery_display_caption' => '',
					],
				]
			);

			$this->update_control( 'content_transition', array(
				'default' => 'custom',
			));

			$this->add_control(
				'content_effect',
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
					'prefix_class'	=> 'ee-media-effect__content--',
					'condition' 	=> [
						'gallery_display_caption' 	=> '',
						'tilt_enable!' 				=> 'yes',
						'content_transition!' 		=> '',
					],
				]
			);

			$this->start_controls_tabs( 'caption_style' );

				$this->start_controls_tab( 'caption_style_default', [
					'label' 	=> __( 'Default', 'landtech-extras-for-elementor' ),
					'condition' => [
						'gallery_display_caption' => '',
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
								'gallery_display_caption' => '',
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
								'gallery_display_caption' => '',
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
								'gallery_display_caption' => '',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab( 'caption_style_hover', [
					'label' 	=> __( 'Hover', 'landtech-extras-for-elementor' ),
					'condition' => [
						'gallery_display_caption' => '',
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
								'gallery_display_caption' => '',
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
								'gallery_display_caption' => '',
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
								'gallery_display_caption' => '',
							],
						]
					);

					$this->add_group_control(
						Group_Control_Text_Shadow::get_type(),
						[
							'name' 		=> 'text_box_shadow_hover',
							'selector' 	=> '{{WRAPPER}} .ee-gallery__media:hover .ee-gallery__media__caption',
							'separator'	=> '',
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
	 * @since  2.1.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings();

		$caption_type_key = $settings['gallery_type'];
		if ( 'acf_gallery' === $caption_type_key ) {
			$caption_type_key = 'wordpress';
		}

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => 'ee-gallery-wrapper',
			],
			'gallery' => [
				'class' => [
					'ee-gallery',
					'ee-grid',
					'ee-grid--gallery',
					'ee-gallery__gallery',
				],
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
					'ee-caption',
					'ee-caption--' . $caption_type_key,
				],
			],
		] );

		if ( 'acf_gallery' === $settings['gallery_type'] && ACF_Gallery_Bridge::is_enabled() ) {
			$this->add_render_attribute( 'wrapper', 'data-ltx-acf-gallery', '1' );
		}

		if (
			$this->_is_edit_mode
			&& isset( $settings['masonry_enable'], $settings['parallax_enable'] )
			&& 'yes' === $settings['masonry_enable']
			&& 'yes' !== $settings['parallax_enable']
		) {
			$this->add_render_attribute( 'gallery', 'data-ee-editor-masonry', '1' );
		}

		if ( 'manual' === $settings['gallery_type'] ) {
			$this->render_gallery();
		} elseif ( 'wordpress' === $settings['gallery_type'] ) {
			$this->render_wp_gallery();
		} elseif ( 'acf_gallery' === $settings['gallery_type'] ) {
			if ( ! ACF_Gallery_Bridge::is_enabled() ) {
				if ( $this->_is_edit_mode ) {
					echo '<div class="ee-gallery--editor-notice">';
					esc_html_e( 'ACF Gallery requires Advanced Custom Fields (ACF) and a valid Gallery field on the target post.', 'landtech-extras-for-elementor' );
					echo '</div>';
				}
				return;
			}
			$acf_post = isset( $settings['acf_post_id'] ) ? absint( $settings['acf_post_id'] ) : 0;
			$dataset  = ACF_Gallery_Bridge::get_items(
				isset( $settings['acf_field_name'] ) ? $settings['acf_field_name'] : '',
				$acf_post
			);
			if ( empty( $dataset ) && $this->_is_edit_mode ) {
				echo '<div class="ee-gallery--editor-notice">';
				esc_html_e( 'No gallery images found. Check the ACF field name, Post ID, and that the field contains images on that post.', 'landtech-extras-for-elementor' );
				echo '</div>';
				return;
			}
			$this->render_wp_gallery_from_dataset( $dataset );
		} elseif ( 'instagram' === $settings['gallery_type'] ) {
			$this->render_instagram_gallery();
		}
	}

	/**
	 * Render Gallery Start
	 * 
	 * Render start tags for gallery wrappers
	 *
	 * @since  2.2.23
	 * @return void
	 */
	protected function render_gallery_start() {
		?><div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div <?php $this->print_render_attribute_string( 'gallery' ); ?>><?php
				$this->render_grid_sizer();
	}

	/**
	 * Render Gallery End
	 * 
	 * Render end tags for gallery wrappers
	 *
	 * @since  2.2.23
	 * @return void
	 */
	protected function render_gallery_end() {
			?></div>
		</div><?php
	}

	/**
	 * Render Gallery
	 * 
	 * Render custom gallery
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_gallery() {

		$settings 			= $this->get_settings();
		$gallery 			= $settings['gallery'];

		if ( ! empty( $settings['gallery_rand'] ) ) {
			shuffle( $gallery );
		}

		$this->render_gallery_start();

		foreach ( $gallery as $index => $item ) {

			$media_tag 			= 'figure';
			$item_key 			= $this->get_repeater_setting_key( 'item', 'gallery', $index );
			$media_key 			= $this->get_repeater_setting_key( 'media', 'gallery', $index );
			$media_wrapper_key 	= $this->get_repeater_setting_key( 'media-wrapper', 'gallery', $index );

			$this->add_render_attribute( [
				$item_key => [
					'class' => [
						'ee-gallery__item',
						'ee-grid__item',
						'elementor-repeater-item-' . $item['_id'],
					],
				],
				$media_key => [
					'class' => [
						'ee-media',
						'ee-gallery__media',
					],
				],
				$media_wrapper_key => [
					'class' => [
						'ee-media__wrapper',
						'ee-gallery__media-wrapper',
					],
				],
			] );

			if ( 'yes' === $item['custom_size'] || 'yes' === $item['custom_size_tablet'] || 'yes' === $item['custom_size_mobile'] ) {
				$this->add_render_attribute( [
					$media_key => [
						'class' => 'ee-media--stretch',
					],
					$item_key => [
						'class' => 'ee-grid__item--custom-size',
					],
				] );
			}

			if ( '' !== $item['link'] ) {
				$media_tag = 'a';

				if ( 'file' === $item['link'] ) {
					$item_link 	= $item['image']['url'];

					if ( $item['image']['id'] ) {
						$item_link 	= wp_get_attachment_image_src( $item['image']['id'], 'full' );
						$item_link	= $item_link[0];
					}

					$slideshow = $settings['lightbox_slideshow'] ? $this->get_id_for_loop() : false;

					$this->add_lightbox_data_attributes( $media_key, $item['image']['id'], $settings['open_lightbox'], $slideshow );

					if ( $this->_is_edit_mode ) {
						$this->add_render_attribute( $media_key, 'class', 'elementor-clickable' );
					}

				} else if ( 'attachment' === $item['link'] ) {

					$item_link 	= get_attachment_link( $item['image']['id'] );

				} else if ( 'custom' === $item['link'] ) {

					if ( ! empty( $item['link_url']['url'] ) ) {

						$item_link = $item['link_url']['url'];

						if ( ! empty( $item['link_url']['is_external'] ) ) {
							$this->add_render_attribute( $media_key, 'target', '_blank' );
						}

						if ( ! empty( $item['link_url']['nofollow'] ) ) {
							$this->add_render_attribute( $media_key, 'rel', 'nofollow' );
						}
					}

				}

				$this->add_render_attribute( $media_key, 'href', $item_link );
			}

			if ( 'yes' === $settings['tilt_enable'] ) {
				$this->add_render_attribute( $media_wrapper_key, 'class', 'ee-gallery__tilt' );

				if ( 'yes' === $settings['tilt_depth'] ) {
					$this->add_render_attribute( $media_wrapper_key, 'class', 'ee-gallery__tilt--depth' );
				}
			}

			?>

			<div <?php $this->print_render_attribute_string( $item_key ); ?>>
				<<?php echo esc_html( $this->get_gallery_media_tag_name( $media_tag ) ); ?> <?php $this->print_render_attribute_string( $media_key ); ?>>
					<div <?php $this->print_render_attribute_string( $media_wrapper_key ); ?>><?php
						$this->render_image_thumbnail( $item, $index );
						$this->render_image_overlay();
						$this->render_image_caption( $item, $index );
					?></div>
				</<?php echo esc_html( $this->get_gallery_media_tag_name( $media_tag ) ); ?>>
			</div>

		<?php }

		$this->render_gallery_end();
	}

	/**
	 * Render WP Gallery
	 * 
	 * Render wordpress gallery
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_wp_gallery() {

		$gallery = $this->get_settings_for_display( 'wp_gallery' );
		if ( ! is_array( $gallery ) ) {
			$gallery = array();
		}

		$this->render_wp_gallery_from_dataset( $gallery );
	}

	/**
	 * Render gallery items from an Elementor-style dataset (attachment id + url per row).
	 *
	 * @since 2.2.54
	 *
	 * @param array<int, array<string, int|string>> $gallery Gallery rows with `id` and optional `url`.
	 * @return void
	 */
	protected function render_wp_gallery_from_dataset( array $gallery ) {

		$settings 	= $this->get_settings();
		$media_tag 	= 'figure';

		if ( ! empty( $settings['gallery_rand'] ) ) {
			shuffle( $gallery );
		}

		if ( '' !== $settings['gallery_link'] ) {
			$media_tag = 'a';
		}

		if ( empty( $gallery ) ) {
			return;
		}

		$this->render_gallery_start();

		foreach ( $gallery as $index => $item ) {

			if ( ! isset( $item['id'] ) || $item['id'] < 1 ) {
				continue;
			}

			$gallery_media_key 			= 'gallery-media' . $index;
			$gallery_media_wrapper_key 	= 'gallery-media-wrapper' . $index;
			$gallery_item_key 			= 'gallery-item' . $index;
			$item_url = isset( $item['url'] ) ? $item['url'] : '';

			$item['image'] = Module::get_image_info( $item['id'], $item_url, $settings['thumbnail_size'] );

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
					'class' => [
						'ee-gallery__item',
						'ee-gallery__item--' . ( $index + 1 ),
						'ee-grid__item',
					],
				],
			] );

			if ( '' !== $settings['gallery_link'] ) {

				if ( 'file' === $settings['gallery_link'] ) {

					$item_link 	= wp_get_attachment_image_src( $item['id'], 'full' );
					$item_link	= $item_link[0];

					$slideshow = $settings['lightbox_slideshow'] ? $this->get_id_for_loop() : false;

					$this->add_lightbox_data_attributes( $gallery_media_key, $item['id'], $settings['open_lightbox'], $slideshow );

					if ( $this->_is_edit_mode ) {
						$this->add_render_attribute( $gallery_media_key, 'class', 'elementor-clickable' );
					}

				} else if ( 'attachment' === $settings['gallery_link'] ) {

					$item_link 	= get_attachment_link( $item['id'] );

				}

				$this->add_render_attribute( $gallery_media_key, 'href', $item_link );
			}

			if ( 'yes' === $settings['tilt_enable'] ) {
				$this->add_render_attribute( $gallery_media_wrapper_key, 'class', 'ee-gallery__tilt' );

				if ( 'yes' === $settings['tilt_depth'] ) {
					$this->add_render_attribute( $gallery_media_wrapper_key, 'class', 'ee-gallery__tilt--depth' );
				}
			}

			?><div <?php $this->print_render_attribute_string( $gallery_item_key ); ?>>
				<<?php echo esc_html( $this->get_gallery_media_tag_name( $media_tag ) ); ?> <?php $this->print_render_attribute_string( $gallery_media_key ); ?>>
					<div <?php $this->print_render_attribute_string( $gallery_media_wrapper_key ); ?>><?php
						$this->render_image_thumbnail( $item, $index );
						$this->render_image_overlay();
						$this->render_image_caption( $item, $index );
					?></div>
				</<?php echo esc_html( $this->get_gallery_media_tag_name( $media_tag ) ); ?>>
			</div><?php
		}

		$this->render_gallery_end();
	}

	/**
	 * Render the instagram gallery 
	 *
	 * @since  2.1.0
	 * @return empty
	 */
	protected function render_instagram_gallery() {

		$settings = $this->get_settings();

		if ( 'tags' === $settings['insta_display'] && empty( $settings['insta_hashtag'] ) ) {
			esc_html_e( 'Please enter a hashtag.', 'landtech-extras-for-elementor' );
			return;
		}

		if ( 'feed' === $settings['insta_display'] && ! $this->get_insta_access_token() ) {
			esc_html_e( 'Please enter your Instagram access token.', 'landtech-extras-for-elementor' );
			return;
		}

		$media_tag 	= 'figure';
		$icon_style = 'outline' === $this->get_settings( 'insta_icons_style' ) ? '-o' : '' ;

		$this->add_render_attribute([
			'caption-text' => [
				'class' => 'ee-caption__text',
			],
			'caption-insta' => [
				'class' => 'ee-caption__insta',
			],
			'insta-counter-comments' => [
				'class' => [
					'ee-caption__insta__counter',
					'ee-caption__insta__counter--comments',
				]
			],
			'insta-counter-comments-icon' => [
				'class' => [
					'fa',
					'fa-comment' . $icon_style,
					'ee-caption__insta__icon',
				]
			],
			'insta-counter-likes' => [
				'class' => [
					'ee-caption__insta__counter',
					'ee-caption__insta__counter--likes',
				]
			],
			'insta-counter-likes-icon' => [
				'class' => [
					'fa',
					'fa-heart' . $icon_style,
					'ee-caption__insta__icon',
				]
			],
		]);

		if ( '' !== $settings['gallery_link'] ) {
			$media_tag = 'a';
		}

		$gallery = $this->get_insta_posts( $settings );

		if ( empty( $gallery ) || is_wp_error( $gallery ) ) {
			if ( is_wp_error( $gallery ) ) {
				echo esc_html( $gallery->get_error_message() );
			} else {
				echo esc_html__( 'No Posts Found', 'landtech-extras-for-elementor' );
			}

			return;
		}

		if ( ! empty( $settings['gallery_rand'] ) ) {
			shuffle( $gallery );
		}

		$this->render_gallery_start();

		foreach ( $gallery as $index => $item ) {

			$item_key 			= $this->get_repeater_setting_key( 'item', 'gallery', $index );
			$media_key 			= $this->get_repeater_setting_key( 'media', 'gallery', $index );
			$image_key 			= $this->get_repeater_setting_key( 'image', 'gallery', $index );
			$media_wrapper_key 	= $this->get_repeater_setting_key( 'wrapper', 'gallery', $index );

			$this->add_render_attribute( [
				$item_key => [
					'class' => [
						'ee-gallery__item',
						'ee-grid__item',
						'elementor-repeater-item-' . $index,
					],
				],
				$media_key => [
					'class' => [
						'ee-media',
						'ee-gallery__media',
					],
				],
				$media_wrapper_key => [
					'class' => [
						'ee-media__wrapper',
						'ee-gallery__media-wrapper',
					],
				],
			] );

			if ( '' !== $settings['gallery_link'] ) {

				if ( 'file' === $settings['gallery_link'] ) {

					$item_link = $this->get_insta_image_url( $item, 'high' );

					$this->add_render_attribute( $media_key, [
						'data-elementor-open-lightbox' 		=> $settings['open_lightbox'],
						'data-elementor-lightbox-title' 	=> $item['caption'],
					] );

					if ( $settings['lightbox_slideshow'] ) {
						$this->add_render_attribute( $media_key, 'data-elementor-lightbox-slideshow', $this->get_id() );
					}

					if ( $this->_is_edit_mode ) {
						$this->add_render_attribute( $media_key, 'class', 'elementor-clickable' );
					}

				} else if ( 'attachment' === $settings['gallery_link'] ) {

					$item_link 	= $item['link'];

					$this->add_render_attribute( $media_key, 'target', '_blank' );
				}

				$this->add_render_attribute( $media_key, 'href', $item_link );
			}

			if ( 'yes' === $settings['tilt_enable'] ) {
				$this->add_render_attribute( $media_wrapper_key, 'class', 'ee-gallery__tilt' );

				if ( 'yes' === $settings['tilt_depth'] ) {
					$this->add_render_attribute( $media_wrapper_key, 'class', 'ee-gallery__tilt--depth' );
				}
			}

			?><div <?php $this->print_render_attribute_string( $item_key ); ?>>
				<<?php echo esc_html( $this->get_gallery_media_tag_name( $media_tag ) ); ?> <?php $this->print_render_attribute_string( $media_key ); ?>>
					<div <?php $this->print_render_attribute_string( $media_wrapper_key ); ?>>
						<?php $this->render_image_thumbnail( $item, $index ); ?>
						<?php $this->render_image_overlay(); ?>
						<?php $this->render_image_caption( $item, $index ); ?>
					</div>
				</<?php echo esc_html( $this->get_gallery_media_tag_name( $media_tag ) ); ?>>				
			</div><?php
		}

		$this->render_gallery_end();

	}

	/**
	 * Render Grid Sizer
	 * 
	 * The sizer for masonry layout mode
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_grid_sizer() {
		$settings = $this->get_settings();

		if ( 'yes' === $settings['masonry_enable'] && 'yes' !== $settings['parallax_enable'] ) {
			?><div class="ee-grid__item ee-grid__item--sizer"></div><?php
		}
	}

	/**
	 * Render Image Thumbnail
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_image_thumbnail( $item, $index ) {

		$settings 			= $this->get_settings();
		$thumbnail_url 		= $this->get_thumbnail_image_url( $item, $settings );
		$thumbnail_alt 		= $this->get_thumbnail_image_alt( $item );
		$thumbnail_title 	= $this->get_thumbnail_image_title( $item );
		$image_key 			= $this->get_repeater_setting_key( 'image', 'gallery', $index );

		$this->add_render_attribute( $image_key, 'src', $thumbnail_url );

		if ( '' !== $thumbnail_alt ) {
			$this->add_render_attribute( $image_key, 'alt', $thumbnail_alt );
		}

		if ( '' !== $thumbnail_title ) {
			$this->add_render_attribute( $image_key, 'title', $thumbnail_title );
		}

		?><div <?php $this->print_render_attribute_string( 'gallery-thumbnail' ); ?>>
			<img <?php $this->print_render_attribute_string( $image_key ); ?> />
		</div><?php
	}

	/**
	 * Render Image Caption
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_image_caption( $item, $index ) {
		$settings = $this->get_settings();
		$caption = $this->get_item_caption( $item );

		if ( ! $caption )
			return;

		?><figcaption <?php $this->print_render_attribute_string( 'gallery-content' ); ?>>
			<div <?php $this->print_render_attribute_string( 'gallery-caption' ); ?>>
				<?php echo wp_kses_post( $caption ); ?>
			</div>
		</figcaption><?php
	}

	/**
	 * Render Image Overlay
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function render_image_overlay() {
		?><div <?php $this->print_render_attribute_string( 'gallery-overlay' ); ?>></div><?php
	}

	/**
	 * Get Thumbnail Image URL
	 *
	 * @since  2.1.0
	 * @return string 	The url of the attachment
	 */
	protected function get_thumbnail_image_url( $item, array $settings ) {

		if ( $this->is_instagram_gallery() ) {
			$image_url = $this->get_insta_image_url( $item, $this->get_settings('insta_image_size') );
		} else {
			$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], 'thumbnail', $settings );
		}

		if ( ! $image_url ) {
			$image_url = $item['image']['url'];
		}

		return $image_url;
	}

	/**
	 * Get Insta Thumbnail Image URL
	 *
	 * @since  2.2.23
	 * @return string 	The url of the instagram post image
	 */
	protected function get_insta_image_url( $item, $size = 'high' ) {
		$thumbnail  = $item['thumbnail'];

		if ( ! empty( $thumbnail[ $size ] ) ) {
			$image_url = $thumbnail[ $size ]['src'];
		} else {
			$image_url = isset( $item['image'] ) ? $item['image'] : '';
		}

		return $image_url;
	}

	/**
	 * Get Thumbnail Image Alt Text
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function get_thumbnail_image_alt( $item ) {
		if ( $this->is_instagram_gallery() )
			return $item['caption'];

		return trim( wp_strip_all_tags( get_post_meta( $item['image']['id'], '_wp_attachment_image_alt', true) ) );
	}

	/**
	 * Get Thumbnail Image Title
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function get_thumbnail_image_title( $item ) {
		if ( $this->is_instagram_gallery() )
			return $item['caption'];

		return trim( wp_strip_all_tags( get_the_title( $item['image']['id'] ) ) );
	}

	/**
	 * Get Item Caption
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function get_item_caption( $item ) {
		if ( $this->is_instagram_gallery() ) {
			return $this->get_insta_caption( $item );
		}

		$attachment = get_post( $item['image']['id'] );

		return ImageModule::get_image_caption( $attachment, $this->get_settings( 'gallery_caption' ) );
	}

	/**
	 * Get Insta Caption
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function get_insta_caption( $item ) {

		$settings = $this->get_settings();

		ob_start();

		if ( '' !== $settings['insta_counter_caption'] ) {
			?><div <?php $this->print_render_attribute_string( 'caption-text' ); ?>><?php echo wp_kses_post( $item['caption'] ); ?></div><?php
		}

		if ( '' !== $settings['insta_counter_comments'] || '' !== $settings['insta_counter_likes'] ) {
			?><div <?php $this->print_render_attribute_string( 'caption-insta' ); ?>><?php

			if ( '' !== $settings['insta_counter_comments'] ) {
				?><span <?php $this->print_render_attribute_string( 'insta-counter-comments' ); ?>>
					<i <?php $this->print_render_attribute_string( 'insta-counter-comments-icon' ); ?>></i><?php echo esc_html( (string) $item['comments'] ); ?>
				</span><?php
			}

			if ( '' !== $settings['insta_counter_likes'] ) {
				?><span <?php $this->print_render_attribute_string( 'insta-counter-likes' ); ?>>
					<i <?php $this->print_render_attribute_string( 'insta-counter-likes-icon' ); ?>></i><?php echo esc_html( (string) $item['likes'] ); ?>
				</span><?php
			}
			
			?></div><?php	
		}

		return ob_get_clean();
	}

	/**
	 * Get Instagram Comments
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function get_insta_comments( $item ) {
		if ( $this->is_instagram_gallery() )
			return $item['comments'];
	}

	/**
	 * Check if gallery source is Instagram
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function is_instagram_gallery() {

		$settings = $this->get_settings();

		if ( 'instagram' === $settings['gallery_type'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Fetches normalized Instagram gallery items for rendering, with transient caching.
	 *
	 * Wraps Instagram Graph/unofficial hashtag HTTP access with `get_insta_remote()` and caches successes
	 * (and benign empty payloads) under `landtech_extras_instagram_posts_*` transient keys.
	 *
	 * @since  2.1.0
	 *
	 * @param array<string,mixed> $settings Widget settings array (expects `insta_display`, hashtag fields).
	 *
	 * @return array<mixed>|(\WP_Error) Normalized gallery items list or WP_Error from the remote layer.
	 */
	public function get_insta_posts( $settings ) {

		$url       = $this->get_fetch_url();
		$cache_key = 'landtech_extras_instagram_posts_' . md5(
			wp_json_encode(
				array(
					'd'          => isset( $settings['insta_display'] ) ? (string) $settings['insta_display'] : '',
					't'          => isset( $settings['insta_hashtag'] ) ? (string) $settings['insta_hashtag'] : '',
					'tkn'        => wp_hash( (string) $this->get_insta_access_token(), 'landtech_extras_ig_posts' ),
					'endpoint_h' => (string) wp_parse_url( (string) $url, PHP_URL_HOST ),
				)
			)
		);

		$cached = get_transient( $cache_key );

		if ( false !== $cached && is_array( $cached ) ) {
			if ( ! empty( $cached['_landtech_extras_wp_error'] ) ) {
				$error_code = isset( $cached['code'] ) ? $cached['code'] : '';

				return new \WP_Error(
					$error_code,
					isset( $cached['message'] ) ? $cached['message'] : '',
					array_key_exists( 'error_data', $cached ) ? $cached['error_data'] : null
				);
			}

			return $cached;
		}

		$response = $this->get_insta_remote( $url );

		if ( is_wp_error( $response ) ) {
			set_transient(
				$cache_key,
				array(
					'_landtech_extras_wp_error' => 1,
					'code'                       => $response->get_error_code(),
					'message'                    => $response->get_error_message(),
					'error_data'                 => $response->get_error_data(),
				),
				MINUTE_IN_SECONDS * 15
			);

			return $response;
		}

		$data = ( 'tags' === $settings['insta_display'] ) ? $this->get_insta_tags_response_data( $response ) : $this->get_insta_feed_response_data( $response );

		if ( empty( $data ) ) {
			set_transient( $cache_key, array(), MINUTE_IN_SECONDS * 60 );
			return array();
		}

		set_transient( $cache_key, $data, DAY_IN_SECONDS );

		return $data;
	}

	/**
	 * Executes a validated HTTP GET to Instagram endpoints and returns JSON-decoded payload.
	 *
	 * Uses WordPress HTTP API (`wp_safe_remote_get`) after normalizing the URL. Non-200 responses and
	 * transport failures return `WP_Error`. Remote error messages from the JSON body are sanitized
	 * before being attached to errors (they may be surfaced in logs or admin UI).
	 *
	 * Documented under readme.txt **External Services → Instagram**.
	 *
	 * @since  2.1.0
	 *
	 * @param string $url Fully qualified Instagram Graph or instagram.com URL.
	 *
	 * @return array<mixed>|(\WP_Error) Decoded associative array or error.
	 */
	public function get_insta_remote( $url ) {

		$url = esc_url_raw( (string) $url );

		if ( '' === $url ) {
			return new \WP_Error(
				'landtech_extras_insta_bad_url',
				__( 'Invalid Instagram request URL.', 'landtech-extras-for-elementor' )
			);
		}

		$response = wp_safe_remote_get(
			$url,
			array(
				'timeout'   => 60,
				'sslverify' => true,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );
		$raw_body      = wp_remote_retrieve_body( $response );
		$result        = json_decode( $raw_body, true );

		$decode_ok = ( JSON_ERROR_NONE === json_last_error() );

		if ( 200 !== $response_code ) {
			$api_message = '';
			if ( $decode_ok && is_array( $result ) && isset( $result['error']['message'] ) ) {
				$api_message = sanitize_text_field( wp_strip_all_tags( (string) $result['error']['message'] ) );
			}
			$user_message = '' !== $api_message ? $api_message : __( 'No posts found', 'landtech-extras-for-elementor' );
			$error_code   = ( 0 !== $response_code ) ? 'http_' . (string) $response_code : 'http_error';

			return new \WP_Error( $error_code, $user_message );
		}

		if ( ! $decode_ok || ! is_array( $result ) ) {
			return new \WP_Error(
				'landtech_extras_insta_json',
				__( 'Data Error', 'landtech-extras-for-elementor' )
			);
		}

		return $result;
	}

	public function get_insta_user_id() {
		$result = $this->get_insta_remote( $this->get_user_url() );
		return $result;
	}

	public function get_insta_user_media( $user_id ) {
		$result = $this->get_insta_remote( $this->get_user_media_url( $user_id ) );

		return $result;
	}

	public function get_insta_media( $media_id ) {
		$result = $this->get_insta_remote( $this->get_media_url( $media_id ) );

		return $result;
	}

	/**
	 * Retrieve a grab URL.
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_fetch_url() {

		$settings = $this->get_settings();

		if ( 'tags' == $settings['insta_display'] ) {
			$url = sprintf( $this->get_tags_endpoint(), $settings['insta_hashtag'] );
			$url = add_query_arg( array( '__a' => 1 ), $url );

		} else if ( 'feed' == $settings['insta_display'] ) {
			$url = $this->get_feed_endpoint();
			$url = add_query_arg( [
				'fields'       => 'id,media_type,media_url,thumbnail_url,permalink,caption,likes_count,likes',
				'access_token' => $this->get_insta_access_token(),
			], $url );
		}

		return $url;
	}

	public function get_user_url() {
		$url = $this->get_user_endpoint();
		$url = add_query_arg( [
			'access_token' => $this->get_insta_access_token(),
			// 'fields' => 'media.limit(10){comments_count,like_count,likes,likes_count,media_url,permalink,caption}',
		], $url );

		return $url;
	}

	public function get_user_media_url( $user_id ) {
		$url = sprintf( $this->get_user_media_endpoint(), $user_id );
		$url = add_query_arg( [
			'access_token' => $this->get_insta_access_token(),
			'fields' => 'id,like_count',
		], $url );

		return $url;
	}

	public function get_media_url( $media_id ) {
		$url = sprintf( $this->get_media_endpoint(), $media_id );
		$url = add_query_arg( [
			'access_token' => $this->get_insta_access_token(),
			'fields' => 'id,media_type,media_url,timestamp,like_count',
		], $url );

		return $url;
	}

	/**
	 * Retrieve a URL for own photos.
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_feed_endpoint() {
		return $this->insta_official_api_url . 'me/media/';
	}

	/**
	 * Retrieve a URL for photos by hashtag.
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_tags_endpoint() {
		return $this->insta_api_url . 'explore/tags/%s/';
	}

	public function get_user_endpoint() {
		return $this->insta_official_api_url . 'me/';
	}

	public function get_user_media_endpoint() {
		return $this->insta_official_api_url . '%s/media/';
	}

	public function get_media_endpoint() {
		return $this->insta_official_api_url . '%s/';
	}

	/**
	 * Normalizes Official Instagram Graph `/me/media` payload for widget rendering.
	 *
	 * Sanitizes all fields copied from the remote JSON before they are echoed in templates or cached.
	 *
	 * @since  2.1.0
	 *
	 * @param array<mixed> $response Decoded Graph API payload.
	 *
	 * @return array<int,array<string,mixed>> List of sanitized post stubs.
	 */
	public function get_insta_feed_response_data( $response ) {

		if ( ! is_array( $response ) || ! isset( $response['data'] ) || ! is_array( $response['data'] ) ) {
			return array();
		}

		$response_posts = $response['data'];

		if ( empty( $response_posts ) ) {
			return array();
		}

		$return_data = array();
		$posts       = array_slice( $response_posts, 0, $this->get_settings( 'insta_posts_counter' ), true );

		foreach ( $posts as $post ) {
			if ( ! is_array( $post ) ) {
				continue;
			}

			$mid          = isset( $post['id'] ) ? sanitize_text_field( (string) $post['id'] ) : '';
			$permalink    = isset( $post['permalink'] ) ? esc_url_raw( (string) $post['permalink'] ) : '';
			$media_type   = isset( $post['media_type'] ) ? sanitize_text_field( (string) $post['media_type'] ) : '';
			$thumb_url    = isset( $post['thumbnail_url'] ) ? esc_url_raw( (string) $post['thumbnail_url'] ) : '';
			$media_url_u  = isset( $post['media_url'] ) ? esc_url_raw( (string) $post['media_url'] ) : '';
			$image_url    = ( 'VIDEO' === $media_type && '' !== $thumb_url ) ? $thumb_url : $media_url_u;

			if ( '' === $mid ) {
				continue;
			}

			$_post               = array();
			$_post['id']         = $mid;
			$_post['link']       = '' !== $permalink ? $permalink : '';
			$_post['caption']    = '';
			$_post['image']      = $image_url;
			$_post['comments']   = isset( $post['comments_count'] ) ? absint( $post['comments_count'] ) : 0;
			$_post['likes']      = isset( $post['likes_count'] ) ? absint( $post['likes_count'] ) : 0;
			$_post['thumbnail'] = $this->get_insta_feed_thumbnail_data( $post );

			if ( ! empty( $post['caption'] ) ) {
				$caption_plain = sanitize_text_field( wp_strip_all_tags( (string) $post['caption'] ) );
				if ( '' !== $caption_plain ) {
					$_post['caption'] = wp_html_excerpt( $caption_plain, $this->get_settings( 'insta_caption_length' ), '&hellip;' );
				}
			}

			$return_data[] = $_post;
		}

		return $return_data;
	}

	/**
	 * Builds a sanitized thumbnail size map from Legacy `images` object (unused by current Graph endpoint but kept for payloads that include it).
	 *
	 * @since 2.1.0
	 *
	 * @param array<string,mixed> $post Raw media item from Instagram API.
	 * @return array<string,mixed|array<string,int|string>> Nested src / dimension records.
	 */
	public function get_insta_feed_thumbnail_data( $post ) {
		$thumbnail = array(
			'thumbnail' => false,
			'low'       => false,
			'standard'  => false,
			'high'      => false,
		);

		if ( empty( $post['images'] ) || ! is_array( $post['images'] ) ) {
			return $thumbnail;
		}

		$data = $post['images'];

		$t = isset( $data['thumbnail']['url'], $data['thumbnail']['width'], $data['thumbnail']['height'] ) && is_array( $data['thumbnail'] )
			? $data['thumbnail']
			: null;
		if ( $t ) {
			$thumbnail['thumbnail'] = array(
				'src'           => esc_url_raw( (string) $t['url'] ),
				'config_width'  => absint( $t['width'] ),
				'config_height' => absint( $t['height'] ),
			);
		}

		$l = isset( $data['low_resolution']['url'], $data['low_resolution']['width'], $data['low_resolution']['height'] ) && is_array( $data['low_resolution'] )
			? $data['low_resolution']
			: null;
		if ( $l ) {
			$thumbnail['low'] = array(
				'src'           => esc_url_raw( (string) $l['url'] ),
				'config_width'  => absint( $l['width'] ),
				'config_height' => absint( $l['height'] ),
			);
		}

		$s = isset( $data['standard_resolution']['url'], $data['standard_resolution']['width'], $data['standard_resolution']['height'] ) && is_array( $data['standard_resolution'] )
			? $data['standard_resolution']
			: null;
		if ( $s ) {
			$thumbnail['standard'] = array(
				'src'           => esc_url_raw( (string) $s['url'] ),
				'config_width'  => absint( $s['width'] ),
				'config_height' => absint( $s['height'] ),
			);

			$thumbnail['high'] = $thumbnail['standard'];
		}

		return $thumbnail;
	}

	/**
	 * Normalizes unofficial hashtag GraphQL payload edges for gallery rendering (legacy scraper compatibility).
	 *
	 * Validates nested keys and sanitizes every field taken from upstream JSON before output or transient cache.
	 *
	 * @since  2.1.0
	 *
	 * @param array<mixed> $response Decoded instagram.com graphql-style payload.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public function get_insta_tags_response_data( $response ) {

		$settings = $this->get_settings();

		$response_posts = array();
		if ( is_array( $response ) && isset( $response['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) && is_array( $response['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
			$response_posts = $response['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
		}
		if ( empty( $response_posts ) && isset( $response['graphql']['hashtag']['edge_hashtag_to_top_posts']['edges'] ) && is_array( $response['graphql']['hashtag']['edge_hashtag_to_top_posts']['edges'] ) ) {
			$response_posts = $response['graphql']['hashtag']['edge_hashtag_to_top_posts']['edges'];
		}

		if ( empty( $response_posts ) || ! is_array( $response_posts ) ) {
			return array();
		}

		$return_data = array();
		$posts       = array_slice( $response_posts, 0, $settings['insta_posts_counter'], true );

		foreach ( $posts as $post ) {
			if ( ! is_array( $post ) || empty( $post['node']['shortcode'] ) ) {
				continue;
			}

			$shortcode = sanitize_text_field( (string) $post['node']['shortcode'] );
			if ( ! preg_match( '/^[A-Za-z0-9_-]+$/', $shortcode ) ) {
				continue;
			}

			$permalink = esc_url_raw(
				sprintf( '%sp/%s/', untrailingslashit( esc_url_raw( $this->insta_api_url ) ), $shortcode )
			);
			if ( '' === $permalink ) {
				continue;
			}

			$node           = isset( $post['node'] ) && is_array( $post['node'] ) ? $post['node'] : array();
			$comments_count = isset( $node['edge_media_to_comment']['count'] ) ? absint( $node['edge_media_to_comment']['count'] ) : 0;
			$likes_count    = isset( $node['edge_liked_by']['count'] ) ? absint( $node['edge_liked_by']['count'] ) : 0;

			$_post               = array();
			$_post['id']         = $shortcode;
			$_post['link']       = $permalink;
			$_post['caption']    = '';
			$_post['comments']   = $comments_count;
			$_post['likes']      = $likes_count;
			$_post['thumbnail'] = $this->get_insta_tags_thumbnail_data( $post );

			$caption_node = isset( $node['edge_media_to_caption']['edges'][0]['node']['text'] ) ? $node['edge_media_to_caption']['edges'][0]['node']['text'] : '';
			if ( $caption_node ) {
				$caption_plain = sanitize_text_field( wp_strip_all_tags( (string) $caption_node ) );
				if ( '' !== $caption_plain ) {
					$_post['caption'] = wp_html_excerpt( $caption_plain, $settings['insta_caption_length'], '&hellip;' );
				}
			}

			// Optional image URL parity with feed items when display_url exists.
			if ( ! empty( $node['display_url'] ) ) {
				$_post['image'] = esc_url_raw( (string) $node['display_url'] );
			}

			$return_data[] = $_post;
		}

		return $return_data;
	}

	/**
	 * Sanitized thumbnail presets from hashtag graphql `thumbnail_resources`.
	 *
	 * @since 2.1.0
	 *
	 * @param array<string,mixed> $post_data Edge wrapping a `node` array.
	 * @return array<string,mixed|array<string,int|string>>
	 */
	public function get_insta_tags_thumbnail_data( $post_data ) {
		$post = isset( $post_data['node'] ) && is_array( $post_data['node'] ) ? $post_data['node'] : array();

		$thumbnail = array(
			'thumbnail' => false,
			'low'       => false,
			'standard'  => false,
			'high'      => false,
		);

		if ( ! empty( $post['thumbnail_resources'] ) && is_array( $post['thumbnail_resources'] ) ) {
			foreach ( $post['thumbnail_resources'] as $resources_data ) {
				if ( ! is_array( $resources_data ) || ! isset( $resources_data['config_width'], $resources_data['src'] ) ) {
					continue;
				}

				$src    = esc_url_raw( (string) $resources_data['src'] );
				$config = absint( $resources_data['config_width'] );
				if ( '' === $src ) {
					continue;
				}

				$row = array(
					'src'           => $src,
					'config_width'  => $config,
					'config_height' => isset( $resources_data['config_height'] ) ? absint( $resources_data['config_height'] ) : 0,
				);

				if ( 150 === $config ) {
					$thumbnail['thumbnail'] = $row;
				}

				if ( 320 === $config ) {
					$thumbnail['low'] = $row;
				}

				if ( 640 === $config ) {
					$thumbnail['standard'] = $row;
				}
			}
		}

		if ( ! empty( $post['display_url'] ) && isset( $post['dimensions']['width'], $post['dimensions']['height'] ) ) {
			$durl          = esc_url_raw( (string) $post['display_url'] );
			$thumbnail['high'] = array(
				'src'           => $durl,
				'config_width'  => absint( $post['dimensions']['width'] ),
				'config_height' => absint( $post['dimensions']['height'] ),
			);
		}

		return $thumbnail;
	}

	/**
	 * Get Instagram access token.
	 *
	 * @since 2.1.0
	 * @return string
	 */
	public function get_insta_access_token() {
		$settings = $this->get_settings_for_display();

		if ( ! $this->insta_access_token ) {
			$custom_access_token = $settings['access_token'];

			if ( '' !== trim( $custom_access_token ) ) {
				$this->insta_access_token = $custom_access_token;
			} else {
				$this->insta_access_token = $this->get_insta_global_access_token();
			}
		}

		return $this->insta_access_token;
	}

	/**
	 * Get Instagram access token from wp options.
	 *
	 * @since 2.2.23
	 * @return string
	 */
	public function get_insta_global_access_token() {
		return \LandTechExtras\LandTechExtrasPlugin::$instance->settings->get_option( 'instagram_access_token', 'landtech_extras_apis', false );
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function content_template() {}
}
