<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Posts\Skins;

// LandTech Extras for Elementor Classes
use LandTechExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Posts\Skins
 *
 * @since  1.6.0
 */
class Skin_Carousel extends Skin_Base {

	/**
	 * Get ID
	 * 
	 * Gets the current skin ID
	 *
	 * @since  1.6.0
	 * @return string
	 */
	public function get_id() {
		return 'carousel';
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
		return __( 'Carousel', 'landtech-extras-for-elementor' );
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

		add_action( 'elementor/element/posts-extra/section_query/after_section_end', [ $this, 'register_carousel_controls' ] );
		add_action( 'elementor/element/posts-extra/section_layout/before_section_end', [ $this, 'inject_layout_content_controls' ] );
		add_action( 'elementor/element/posts-extra/section_style_posts/after_section_end', [ $this, 'register_carousel_style_controls' ] );
	}

	/**
	 * Register Layout Content Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_layout_content_controls() {
		$slides_per_column = range( 1, 6 );
		$slides_per_column = array_combine( $slides_per_column, $slides_per_column );

		$this->add_control(
			'carousel_heading',
			[
				'label' 	=> __( 'Carousel', 'landtech-extras-for-elementor' ),
				'type' 		=> Controls_Manager::HEADING,
				'separator'	=> 'before',
			]
		);

		$this->add_responsive_control(
			'direction',
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
				'frontend_available' 	=> true,
			]
		);

		$this->add_responsive_control(
			'slides_per_view',
			[
				'label' 			=> __( 'Slides Per View', 'landtech-extras-for-elementor' ),
				'type' 				=> Controls_Manager::SELECT,
				'default' 			=> '',
				'tablet_default' 	=> '',
				'mobile_default' 	=> '',
				'options' => [
					''	=> __( 'Default', 'landtech-extras-for-elementor' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'slides_per_column',
			[
				'type' 					=> Controls_Manager::SELECT,
				'label' 				=> __( 'Slides Per Column', 'landtech-extras-for-elementor' ),
				'options' 				=> [ '' => __( 'Default', 'landtech-extras-for-elementor' ) ] + $slides_per_column,
				'condition'				=> [
					$this->get_control_id( 'direction' ) => 'horizontal',
				],
				'frontend_available' 	=> true,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __( 'Slides to Scroll', 'landtech-extras-for-elementor' ),
				'options' 		=> [ '' => __( 'Default', 'landtech-extras-for-elementor' ) ] + $slides_per_column,
				'frontend_available' => true,
			]
		);

		parent::register_layout_content_controls();

		$this->update_control( 'grid_columns_spacing', [
			'label' => __( 'Grid Spacing', 'landtech-extras-for-elementor' ),
		] );

		$this->remove_control( 'grid_rows_spacing' );
	}

	/**
	 * Inject Layout Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function inject_layout_content_controls() {

		$this->parent->start_injection( [
			'at' => 'before',
			'of' => 'carousel_grid_columns_spacing',
		] );

			$this->add_control(
				'height',
				[
					'label' 		=> __( 'Height', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'The carousel needs to have a fixed defined height to work in vertical mode.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%', 'vh' ],
					'default' => [
						'size' => 500,
						'unit' => 'px',
					],
					'range' 		=> [
						'px' 		=> [
							'min' => 200,
							'max' => 2000,
						],
						'%' 		=> [
							'min' => 0,
							'max' => 100,
						],
						'vh' 		=> [
							'min' => 0,
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-swiper__container' => 'height: {{SIZE}}{{UNIT}};',
					],
					'condition'		=> [
						$this->get_control_id( 'direction' ) => 'vertical',
					],
				]
			);

		$this->parent->end_injection();

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => 'slides_to_scroll',
		] );

			$this->add_responsive_control(
				'slides_align',
				[
					'label' 		=> __( 'Vertical Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'stretch',
					'options' 		=> [
						'top' 			=> [
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
					'prefix_class' 	=> 'ee-grid-align%s--',
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Register Carousel Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_carousel_controls() {

		$this->start_controls_section(
			'section_carousel',
			[
				'label' => __( 'Carousel', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'effect',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Effect', 'landtech-extras-for-elementor' ),
					'default' 		=> 'slide',
					'options' 		=> [
						'slide' 	=> __( 'Slide', 'landtech-extras-for-elementor' ),
						'fade' 		=> __( 'Fade', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'effect_fade_warning',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'The Fade effect ignores the Slides per View and Slides per Column settings', 'landtech-extras-for-elementor' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
					'condition' 		=> [
						$this->get_control_id('effect') => 'fade',
					],
				]
			);

			$this->add_control(
				'speed',
				[
					'label' 	=> __( 'Duration (ms)', 'landtech-extras-for-elementor' ),
					'description' => __( 'Duration of the effect transition.', 'landtech-extras-for-elementor' ) ,
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 300,
						'unit' 	=> 'px',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 2000,
							'step'	=> 100,
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'resistance_ratio',
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
				'loop',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Loop', 'landtech-extras-for-elementor' ),
					'default' 		=> '',
					'separator'		=> 'before',
					'frontend_available' 	=> true,
				]
			);

			$this->add_control(
				'autoheight',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Auto Height', 'landtech-extras-for-elementor' ),
					'default' 		=> '',
					'frontend_available' 	=> true,
					'conditions'=> [
						'relation' 	=> 'or',
						'terms' 	=> [
							[
								'name' 		=> $this->get_control_id('slides_per_column'),
								'operator' 	=> '==',
								'value' 	=> '1',
							],
							[
								'name' 		=> $this->get_control_id('slides_per_column'),
								'operator' 	=> '==',
								'value' 	=> '',
							],
						]
					]
				]
			);

			$this->add_control(
				'slide_change_resize',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Trigger Resize on Slide', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Some widgets inside post skins templates might require triggering a window resize event when changing slides to display correctly.', 'landtech-extras-for-elementor' ),
					'default' 		=> '',
					'frontend_available' => true,
					'condition' 	=> [
						'skin_source' => 'template',
					],
				]
			);

			$this->add_control(
				'arrows',
				[
					'label' 		=> __( 'Arrows', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::POPOVER_TOGGLE,
					'default' 		=> 'on',
					'label_on' 		=> __( 'On', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'Off', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'on',
					'frontend_available' => true,
				]
			);

			$this->parent->start_popover();

			$this->add_control(
				'arrows_placement',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Placement', 'landtech-extras-for-elementor' ),
					'default'		=> 'inside',
					'options' 		=> [
						'inside' 	=> __( 'Inside', 'landtech-extras-for-elementor' ),
						'outside' 	=> __( 'Outside', 'landtech-extras-for-elementor' ),
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
					]
				]
			);

			$this->parent->end_popover();

			$this->add_control(
				'free_mode',
				[
					'type' 			=> Controls_Manager::POPOVER_TOGGLE,
					'label' 		=> __( 'Free Mode', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Disable fixed positions for slides.', 'landtech-extras-for-elementor' ),
					'default' 		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' 	=> true,
				]
			);

			$this->parent->start_popover();

			$this->add_control(
				'free_mode_sticky',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Snap to position', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Enable to snap slides to positions in free mode.', 'landtech-extras-for-elementor' ),
					'default' 		=> '',
					'return_value' 	=> 'yes',
					'frontend_available' 	=> true,
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
					],
				]
			);

			$this->add_control(
				'free_mode_momentum',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Momentum', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Enable to keep slide moving for a while after you release it.', 'landtech-extras-for-elementor' ),
					'default' 		=> 'yes',
					'return_value' 	=> 'yes',
					'separator'		=> 'before',
					'frontend_available' => true,
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
					],
				]
			);

			$this->add_control(
				'free_mode_momentum_ratio',
				[
					'label' 		=> __( 'Ratio', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Higher value produces larger momentum distance after you release slider.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 5,
							'step'	=> 0.1,
						],
					],
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
						$this->get_control_id( 'free_mode_momentum!' ) => '',
					],	
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'free_mode_momentum_velocity',
				[
					'label' 		=> __( 'Velocity', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Higher value produces larger momentum velocity after you release slider.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 5,
							'step'	=> 0.1,
						],
					],
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
						$this->get_control_id( 'free_mode_momentum!' ) => '',
					],	
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'free_mode_momentum_bounce',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Bounce', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Set to No if you want to disable momentum bounce in free mode.', 'landtech-extras-for-elementor' ),
					'default' 		=> 'yes',
					'return_value' 	=> 'yes',
					'frontend_available' => true,
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
						$this->get_control_id( 'free_mode_momentum!' ) => '',
					],
				]
			);

			$this->add_control(
				'free_mode_momentum_bounce_ratio',
				[
					'label' 		=> __( 'Bounce Ratio', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Higher value produces larger momentum bounce effect.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 5,
							'step'	=> 0.1,
						],
					],
					'condition' => [
						$this->get_control_id( 'free_mode!' ) => '',
						$this->get_control_id( 'free_mode_momentum!' ) => '',
						$this->get_control_id( 'free_mode_momentum_bounce!' ) => '',
					],	
					'frontend_available' => true,
				]
			);

			$this->parent->end_popover();

			$this->add_control(
				'autoplay',
				[
					'label' 	=> __( 'Autoplay', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::POPOVER_TOGGLE,
					'default' 	=> '',
					'frontend_available' => true,
				]
			);

			$this->parent->start_popover();

			$this->add_control(
				'autoplay_speed',
				[
					'label' 	=> __( 'Autoplay Speed', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::NUMBER,
					'default' 	=> 5000,
					'condition' => [
						$this->get_control_id( 'autoplay!' ) => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'pause_on_interaction',
				[
					'label' 		=> __( 'Disable on Interaction', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Removes autoplay completely on the first interaction with the carousel.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'condition' 	=> [
						$this->get_control_id( 'autoplay!' ) => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'stop_on_hover',
				[
					'label' 	=> __( 'Pause on Hover', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SWITCHER,
					'default' 	=> '',
					'frontend_available' => true,
					'condition'	=> [
						$this->get_control_id( 'autoplay!' ) => '',
					],
				]
			);

			$this->parent->end_popover();

			$this->add_control(
				'pagination',
				[
					'label' 		=> __( 'Pagination', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::POPOVER_TOGGLE,
					'default' 		=> 'on',
					'label_on' 		=> __( 'On', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'Off', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'on',
					'frontend_available' => true,
				]
			);

			$this->parent->start_popover();

			$this->add_control(
				'pagination_position',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Position', 'landtech-extras-for-elementor' ),
					'default'		=> 'inside',
					'options' 		=> [
						'inside' 		=> __( 'Inside', 'landtech-extras-for-elementor' ),
						'outside' 		=> __( 'Outside', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' 	=> true,
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->add_control(
				'pagination_type',
				[
					'type' 			=> Controls_Manager::SELECT,
					'label' 		=> __( 'Type', 'landtech-extras-for-elementor' ),
					'default'		=> 'bullets',
					'options' 		=> [
						'bullets' 		=> __( 'Bullets', 'landtech-extras-for-elementor' ),
						'fraction' 		=> __( 'Fraction', 'landtech-extras-for-elementor' ),
					],
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'pagination_clickable',
				[
					'type' 			=> Controls_Manager::SWITCHER,
					'label' 		=> __( 'Clickable', 'landtech-extras-for-elementor' ),
					'default' 		=> 'yes',
					'return_value' 	=> 'yes',
					'condition' => [
						$this->get_control_id( 'pagination!' ) => '',
						$this->get_control_id( 'pagination_type' ) => 'bullets',
					],
					'frontend_available' 	=> true,
				]
			);

			$this->parent->end_popover();

		$this->end_controls_section();

	}

	/**
	 * Register Carousel Style Controls
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function register_carousel_style_controls() {

		$this->start_controls_section(
			'section_style_carousel',
			[
				'label' => __( 'Carousel', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions'=> [
					'relation' 	=> 'and',
					'terms' 	=> [
						[
							'name' => '_skin',
							'operator' => '==',
							'value' => 'carousel',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'name' 		=> $this->get_control_id('arrows'),
									'operator' 	=> '!=',
									'value' 	=> '',
								],
								[
									'name' 		=> $this->get_control_id('pagination'),
									'operator' 	=> '!=',
									'value' 	=> '',
								],
							]
						]
					]
				]
			]
		);

			$this->add_control(
				'arrows_style_heading',
				[
					'label' 	=> __( 'Arrows', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
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
						$this->get_control_id('arrows!') => '',
						$this->get_control_id('direction') => 'horizontal',
					]
				]
			);

			$this->add_control(
				'arrows_position_vertical',
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
						$this->get_control_id('arrows!') => '',
						$this->get_control_id('direction') => 'vertical',
					]
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
						'{{WRAPPER}} .ee-swiper__button' => 'font-size: {{SIZE}}px;',
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
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
						'{{WRAPPER}} .ee-swiper__button' => 'padding: {{SIZE}}em;',
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
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
							'max' => 200,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-swiper__navigation--inside.ee-swiper__navigation--middle.ee-arrows--horizontal .ee-swiper__button' => 'margin-left: {{SIZE}}px; margin-right: {{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--inside:not(.ee-swiper__navigation--middle).ee-arrows--horizontal .ee-swiper__button' => 'margin: {{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--outside.ee-arrows--horizontal .ee-swiper__button--prev' => 'left: -{{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--outside.ee-arrows--horizontal .ee-swiper__button--next' => 'right: -{{SIZE}}px;',

						'{{WRAPPER}} .ee-swiper__navigation--inside.ee-swiper__navigation--center.ee-arrows--vertical .ee-swiper__button' => 'margin-top: {{SIZE}}px; margin-bottom: {{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--inside:not(.ee-swiper__navigation--center).ee-arrows--vertical .ee-swiper__button' => 'margin: {{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--outside.ee-arrows--vertical .ee-swiper__button--prev' => 'top: -{{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__navigation--outside.ee-arrows--vertical .ee-swiper__button--next' => 'bottom: -{{SIZE}}px;',
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
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
						'{{WRAPPER}} .ee-swiper__button' => 'border-radius: {{SIZE}}%;',
					],
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
					],
					'separator'		=> 'after',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'arrows',
					'selector' 		=> '{{WRAPPER}} .ee-swiper__button',
					'condition'		=> [
						$this->get_control_id('arrows!') => '',
					]
				]
			);

			$this->start_controls_tabs( 'arrows_tabs_hover' );

			$this->start_controls_tab( 'arrows_tab_default', [
				'label' => __( 'Default', 'landtech-extras-for-elementor' ),
				'condition'	=> [
					$this->get_control_id('arrows!') => '',
				]
			] );

				$this->add_control(
					'arrows_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-swiper__button i:before' => 'color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id('arrows!') => '',
						]
					]
				);

				$this->add_control(
					'arrows_background_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-swiper__button' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id('arrows!') => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'arrows_tab_hover', [
				'label' => __( 'Hover', 'landtech-extras-for-elementor' ),
				'condition'	=> [
					$this->get_control_id('arrows!') => '',
				]
			] );

				$this->add_control(
					'arrows_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-swiper__button:not(.ee-swiper__button--disabled):hover i:before' => 'color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id('arrows!') => '',
						]
					]
				);

				$this->add_control(
					'arrows_background_color_hover',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-swiper__button:not(.ee-swiper__button--disabled):hover' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id('arrows!') => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'arrows_tab_disabled', [
				'label' => __( 'Disabled', 'landtech-extras-for-elementor' ),
				'condition'	=> [
					$this->get_control_id('arrows!') => '',
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
							'{{WRAPPER}} .ee-swiper__button--disabled' => 'opacity: {{SIZE}};',
						],
						'condition'		=> [
							$this->get_control_id('arrows!') => '',
						]
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'pagination_style_heading',
				[
					'separator'	=> 'before',
					'label' 	=> __( 'Pagination', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_align',
				[
					'label' 		=> __( 'Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'center',
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
						'{{WRAPPER}} .ee-swiper__pagination.ee-swiper__pagination--horizontal' => 'text-align: {{VALUE}};',
					],
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
						$this->get_control_id( 'direction' ) => 'horizontal',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_align_vertical',
				[
					'label' 		=> __( 'Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'middle',
					'options' 		=> [
						'flex-start'    => [
							'title' 	=> __( 'Top', 'landtech-extras-for-elementor' ),
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
					'selectors'		=> [
						'{{WRAPPER}} .ee-swiper__pagination.ee-swiper__pagination--vertical' => 'justify-content: {{VALUE}};',
					],
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
						$this->get_control_id( 'direction' ) => 'vertical',
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
							'max' => 100,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-swiper__pagination--inside.ee-swiper__pagination--horizontal' => 'padding: 0 {{SIZE}}px {{SIZE}}px {{SIZE}}px;',
						'{{WRAPPER}} .ee-swiper__pagination--outside.ee-swiper__pagination--horizontal' => 'padding: {{SIZE}}px 0 0 0;',
						'{{WRAPPER}} .ee-swiper__pagination--inside.ee-swiper__pagination--vertical' => 'padding: {{SIZE}}px {{SIZE}}px {{SIZE}}px 0;',
						'{{WRAPPER}} .ee-swiper__pagination--outside.ee-swiper__pagination--vertical' => 'padding: 0 0 0 {{SIZE}}px;',
					],
					'condition'		=> [
						$this->get_control_id('pagination!') => '',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_bullets_spacing',
				[
					'label' 		=> __( 'Spacing', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 20,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-swiper__pagination--horizontal .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}px',
						'{{WRAPPER}} .ee-swiper__pagination--vertical .swiper-pagination-bullet' => 'margin: {{SIZE}}px 0',
					],
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
						$this->get_control_id( 'pagination_type' ) => 'bullets',
					]
				]
			);

			$this->add_responsive_control(
				'pagination_bullets_border_radius',
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
						'{{WRAPPER}} .swiper-pagination-bullet' => 'border-radius: {{SIZE}}px;',
					],
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
						$this->get_control_id( 'pagination_type' ) => 'bullets',
					],
					'separator'		=> 'after',
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'pagination',
					'selector' 		=> '{{WRAPPER}} .swiper-pagination-bullet',
					'condition'		=> [
						$this->get_control_id( 'pagination!' ) => '',
					]
				]
			);

			$this->start_controls_tabs( 'pagination_bullets_tabs_hover' );

			$this->start_controls_tab( 'pagination_bullets_tab_default', [
				'label' 		=> __( 'Default', 'landtech-extras-for-elementor' ),
				'condition'		=> [
					$this->get_control_id( 'pagination!' ) => '',
					$this->get_control_id( 'pagination_type' ) => 'bullets',
				]
			] );

				$this->add_responsive_control(
					'pagination_bullets_size',
					[
						'label' 		=> __( 'Size', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 0,
								'max' => 12,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_control(
					'pagination_bullets_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_responsive_control(
					'pagination_bullets_opacity',
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
							'{{WRAPPER}} .swiper-pagination-bullet' => 'opacity: {{SIZE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'pagination_bullets_tab_hover',[
				'label' 		=> __( 'Hover', 'landtech-extras-for-elementor' ),
				'condition'		=> [
					$this->get_control_id( 'pagination!' ) => '',
					$this->get_control_id( 'pagination_type' ) => 'bullets',
				]
			] );

				$this->add_responsive_control(
					'pagination_bullets_size_hover',
					[
						'label' 		=> __( 'Size', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 1,
								'max' => 1.5,
								'step'=> 0.1,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'transform: scale({{SIZE}});',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_control(
					'pagination_bullets_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_responsive_control(
					'pagination_bullets_opacity_hover',
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
							'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'opacity: {{SIZE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'pagination_bullets_tab_active', [
				'label' => __( 'Active', 'landtech-extras-for-elementor' ),
				'condition'	=> [
					$this->get_control_id( 'pagination!' ) => '',
					$this->get_control_id( 'pagination_type' ) => 'bullets',
				]
			] );

				$this->add_responsive_control(
					'pagination_bullets_size_active',
					[
						'label' 		=> __( 'Size', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 		=> [
							'px' 		=> [
								'min' => 1,
								'max' => 1.5,
								'step'=> 0.1,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .swiper-pagination-bullet-active' => 'transform: scale({{SIZE}});',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_control(
					'pagination_bullets_color_active',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
					]
				);

				$this->add_responsive_control(
					'pagination_bullets_opacity_active',
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
							'{{WRAPPER}} .swiper-pagination-bullet-active' => 'opacity: {{SIZE}};',
						],
						'condition'		=> [
							$this->get_control_id( 'pagination!' ) => '',
							$this->get_control_id( 'pagination_type' ) => 'bullets',
						]
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
	 * @since  2.2.28
	 * @return void
	 */
	public function before_loop() {
		parent::before_loop();

		add_filter( 'landtech_extras/widgets/posts/item_classes', [ $this, 'filter_item_classes' ], 10, 3 );
	}

	/**
	 * Before Loop
	 *
	 * Executes after the loop has ended
	 *
	 * @since  2.2.28
	 * @return void
	 */
	public function after_loop() {
		remove_filter( 'landtech_extras/widgets/posts/item_classes', [ $this, 'filter_item_classes' ] );

		parent::after_loop();
	}

	/**
	 * Filter for item classes
	 *
	 * @since  2.2.28
	 * @return array
	 */
	public function filter_item_classes( $classes, $post, $settings ) {
		$classes[] = 'ee-swiper__slide';
		$classes[] = 'swiper-slide';

		return $classes;
	}

	/**
	 * Render Loop Start
	 * 
	 * Function to render markup before the posts loop starts
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_loop_start() {
		$settings = $this->parent->get_settings();

		$this->parent->add_render_attribute( [
			'metas-separator' => [
				'class' => 'ee-post__meta__separator',
			],
			'terms-separator' => [
				'class' => [
					'ee-post__terms__separator',
				],
			],
			'swiper' => [
				'class' => [
					'ee-swiper',
				],
				'role' => 'region',
				'aria-roledescription' => 'carousel',
				'aria-label' => __( 'Posts carousel', 'landtech-extras-for-elementor' ),
			],
			'swiper-container' => [
				'class' => [
					'swiper',
					'ee-swiper__container',
				],
				'tabindex' => '0',
			],
			'swiper-wrapper' => [
				'class' => [
					'ee-grid',
					'ee-swiper__wrapper',
					'swiper-wrapper',
				],
			],
		] );

		?>
		<div <?php $this->parent->print_render_attribute_string( 'swiper' ); ?>>
			<div <?php $this->parent->print_render_attribute_string( 'swiper-container' ); ?>>
				<div <?php $this->parent->print_render_attribute_string( 'swiper-wrapper' ); ?>>
		<?php
	}

	/**
	 * Render Loop End
	 *
	 * Outputs the markup for the end of the loop
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_loop_end() {
				?></div><!-- .ee-swiper__wrapper -->
				<?php
					
					// if ( 'outside' !== $this->parent->get_settings( $this->get_control_id( 'arrows_placement' ) ) )
					// 	$this->render_swiper_navigation();

					if ( 'outside' !== $this->parent->get_settings( $this->get_control_id( 'pagination_position' ) ) )
						$this->render_swiper_pagination();

				?>
			</div><!-- .ee-swiper__container -->
		</div><!-- .ee-swiper --><?php

		// if ( 'outside' === $this->parent->get_settings( $this->get_control_id( 'arrows_placement' ) ) ) {
			$this->render_swiper_navigation();
		// }

		if ( 'outside' === $this->parent->get_settings( $this->get_control_id( 'pagination_position' ) ) ) {
			$this->render_swiper_pagination();
		}
	}

	/**
	 * Render Swiper Navigation
	 *
	 * Outputs markup for the swiper navigation
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_swiper_navigation() {
		$this->parent->add_render_attribute( [
			'navigation' => [
				'class' => [
					'ee-arrows',
					'ee-arrows--' . $this->parent->get_settings( $this->get_control_id( 'direction' ) ),
					'ee-swiper__navigation',
					'ee-swiper__navigation--' . $this->parent->get_settings( $this->get_control_id( 'arrows_placement' ) ),
					'ee-swiper__navigation--' . $this->parent->get_settings( $this->get_control_id( 'arrows_position' ) ),
					'ee-swiper__navigation--' . $this->parent->get_settings( $this->get_control_id( 'arrows_position_vertical' ) ),
				],
				'role' => 'group',
				'aria-label' => __( 'Carousel controls', 'landtech-extras-for-elementor' ),
			],
		] );

		// if ( '' !== $settings[ $this->get_control_id('arrows') ] ) {
		// 	$this->parent->add_render_attribute( 'swiper', [
		// 		'class' => [
		// 				'ee-swiper-arrows-placement--' . $settings[ $this->get_control_id('arrows_placement') ],
		// 				'ee-swiper-arrows-position--' . $settings[ $this->get_control_id('arrows_position') ],
		// 			],
		// 		],
		// 	);
		// }

		?><div <?php $this->parent->print_render_attribute_string( 'navigation' ); ?>><?php
			$this->render_swiper_arrows();
		?></div><?php
	}

	/**
	 * Render Swiper Pagination
	 *
	 * Outputs markup for the swiper bullets pagination
	 *
	 * @since  1.6.0
	 * @return void
	 */
	public function render_swiper_pagination() {
		if ( '' === $this->parent->get_settings( $this->get_control_id( 'pagination' ) ) )
			return;

		$this->parent->add_render_attribute( 'pagination', 'class', [
			'ee-swiper__pagination',
			'ee-swiper__pagination--' . $this->parent->get_settings( $this->get_control_id( 'direction' ) ),
			'ee-swiper__pagination--' . $this->parent->get_settings( $this->get_control_id( 'pagination_position' ) ),
			'ee-swiper__pagination-' . $this->parent->get_id(),
			'swiper-pagination',
		] );

		?><div <?php $this->parent->print_render_attribute_string( 'pagination' ); ?>></div><?php
	}

	/**
	 * Render Swiper Arrows
	 *
	 * Outputs markup for the swiper arrows navigation
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_swiper_arrows() {
		if ( '' === $this->parent->get_settings( $this->get_control_id( 'arrows' ) ) )
			return;

		$prev = is_rtl() ? 'right' : 'left';
		$next = is_rtl() ? 'left' : 'right';

		$this->parent->add_render_attribute( [
			'button-prev' => [
				'class' => [
					'ee-swiper__button',
					'ee-swiper__button--prev',
					'ee-arrow',
					'ee-arrow--prev',
					'ee-swiper__button--prev-' . $this->parent->get_id(),
				],
				'role' => 'button',
				'tabindex' => '0',
				'aria-label' => __( 'Previous slide', 'landtech-extras-for-elementor' ),
			],
			'button-prev-icon' => [
				'class' => 'eicon-chevron-' . $prev,
				'aria-hidden' => 'true',
			],
			'button-next' => [
				'class' => [
					'ee-swiper__button',
					'ee-swiper__button--next',
					'ee-arrow',
					'ee-arrow--next',
					'ee-swiper__button--next-' . $this->parent->get_id(),
				],
				'role' => 'button',
				'tabindex' => '0',
				'aria-label' => __( 'Next slide', 'landtech-extras-for-elementor' ),
			],
			'button-next-icon' => [
				'class' => 'eicon-chevron-' . $next,
				'aria-hidden' => 'true',
			],
		] );

		?><div <?php $this->parent->print_render_attribute_string( 'button-prev' ); ?>>
			<i <?php $this->parent->print_render_attribute_string( 'button-prev-icon' ); ?>></i>
		</div>
		<div <?php $this->parent->print_render_attribute_string( 'button-next' ); ?>>
			<i <?php $this->parent->print_render_attribute_string( 'button-next-icon' ); ?>></i>
		</div><?php
	}

	/**
	 * Render Sizer
	 * 
	 * Stop rendering markup for masonry sizer
	 *
	 * @since  1.6.0
	 * @return void
	 */
	protected function render_sizer() {}
}