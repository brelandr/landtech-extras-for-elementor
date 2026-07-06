<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\CircleProgress\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Extras_Widget;

// Elementor Classes
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Circle_Progress
 *
 * @since 0.1.0
 */
class Circle_Progress extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  0.1.0
	 * @return string
	 */
	public function get_name() {
		return 'circle-progress';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  0.1.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Circle Progress', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  0.1.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-circle-progress';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  0.1.0
	 * @return array
	 */
	public function get_script_depends() {
		return [
			'landtech-extras-circle-progress',
			'landtech-extras-jquery-appear',
			'landtech-extras-jquery-easing',
		];
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_circle',
			[
				'label' => __( 'Circle', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'value_heading',
				[
					'label'			=> __( 'Value', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::HEADING,
					'separator'		=> 'before',
				]
			);

			$this->add_control(
				'value_progress',
				[
					'label' 		=> __( 'Progress Value', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Choose absolute if you want to manually define the maximum value and display the entered value instead of the percentage.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'percentage',
					'frontend_available' => true,
					'options' 		=> [
						'percentage'	=> __( 'Percentage', 'landtech-extras-for-elementor' ),
						'absolute' 		=> __( 'Absolute', 'landtech-extras-for-elementor' ),
					],
				]
			);

			$this->add_control(
				'value',
				[
					'label' 	=> __( 'Value', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::TEXT,
					'title'		=> __( 'Accepted value formats are: 50, 0.50, 0,50, 50/100', 'landtech-extras-for-elementor' ),
					'default' 	=> '75',
					'frontend_available' => true,
					'dynamic'	=> [
						'active'		=> true,
						'categories' 	=> [ TagsModule::POST_META_CATEGORY ],
					],
				]
			);

			$this->add_control(
				'value_decimal_move',
				[
					'label' 		=> __( 'Move Decimal', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Move the decimal point of the number shown, keeping the progress to the default value.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 0,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> -4,
							'max' 	=> 4,
							'step'	=> 1,
						],
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'value_max',
				[
					'label' 	=> __( 'Max. Value', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::NUMBER,
					'default' 	=> 100,
					'min'		=> 0,
					'step'		=> 1,
					'frontend_available' => true,
					'condition' => [
						'value_progress' => 'absolute',
					],
				]
			);

			$this->add_control(
				'value_position',
				[
					'label'			=> __( 'Value Position', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Position of the value relative to circle.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'inside',
					'options' 		=> [
						'inside' 	=> __( 'Inside', 'landtech-extras-for-elementor' ),
						'below' 	=> __( 'Below', 'landtech-extras-for-elementor' ),
						'hide' 		=> __( 'Hide', 'landtech-extras-for-elementor' ),
					],
				]
			);

			$this->add_control(
				'selected_icon',
				[
					'label' => __( 'Icon', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'separator'		=> 'before',
					'condition'		=> [
						'value_position!' => 'inside',
					],
				]
			);

			$this->add_control(
				'suffix_heading',
				[
					'label'			=> __( 'Suffix', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::HEADING,
					'separator'		=> 'before',
				]
			);

			$this->add_control(
				'suffix',
				[
					'type'		=> Controls_Manager::TEXT,
					'label' 	=> __( 'Text', 'landtech-extras-for-elementor' ),
					'default'	=> '%',
					'separator' => 'none'
				]
			);

			$this->add_control(
				'suffix_position',
				[
					'label'		=> __( 'Position', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'after',
					'options' 	=> [
						'after' 	=> __( 'After', 'landtech-extras-for-elementor' ),
						'before' 	=> __( 'Before', 'landtech-extras-for-elementor' ),
					],
					'prefix_class'	=> 'ee-circle-progress-suffix--'
				]
			);

			$this->add_responsive_control(
				'suffix_vertical_align',
				[
					'label' 		=> __( 'Alignment', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'top',
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
					'prefix_class'		=> 'ee-circle-progress-suffix--'
				]
			);

			$this->add_control(
				'suffix_top_adjustment',
				[
					'label' 		=> __( 'Top Offset', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '0.5',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 3,
							'step'	=> 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-circle-progress__value .suffix' => 'margin-top: {{SIZE}}em;',
					],
					'condition'	=> [
						'suffix_vertical_align' => 'top',
					]
				]
			);

			$this->add_control(
				'animation_heading',
				[
					'label'			=> __( 'Settings', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::HEADING,
					'separator'		=> 'before',
				]
			);

			$this->add_control(
				'animate',
				[
					'label' 		=> __( 'Animate', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true
				]
			);

			$this->add_control(
				'easing',
				[
					'label'		=> __( 'Easing', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'easeInOutCubic',
					'options' 	=> [
						'easeInQuad' 			=> __( 'easeInQuad', 'landtech-extras-for-elementor' ),
						'easeOutQuad' 			=> __( 'easeOutQuad', 'landtech-extras-for-elementor' ),
						'easeInOutQuad' 		=> __( 'easeInOutQuad', 'landtech-extras-for-elementor' ),
						'easeInCubic' 			=> __( 'easeInCubic', 'landtech-extras-for-elementor' ),
						'easeOutCubic' 			=> __( 'easeOutCubic', 'landtech-extras-for-elementor' ),
						'easeInOutCubic'		=> __( 'easeInOutCubic', 'landtech-extras-for-elementor' ),
						'easeInQuart' 			=> __( 'easeInQuart', 'landtech-extras-for-elementor' ),
						'easeOutQuart' 			=> __( 'easeOutQuart', 'landtech-extras-for-elementor' ),
						'easeInOutQuart' 		=> __( 'easeInOutQuart', 'landtech-extras-for-elementor' ),
						'easeInQuint' 			=> __( 'easeInQuint', 'landtech-extras-for-elementor' ),
						'easeOutQuint' 			=> __( 'easeOutQuint', 'landtech-extras-for-elementor' ),
						'easeInOutQuint' 		=> __( 'easeInOutQuint', 'landtech-extras-for-elementor' ),
						'easeInSine' 			=> __( 'easeInSine', 'landtech-extras-for-elementor' ),
						'easeOutSine' 			=> __( 'easeOutSine', 'landtech-extras-for-elementor' ),
						'easeInOutSine' 		=> __( 'easeInOutSine', 'landtech-extras-for-elementor' ),
						'easeInExpo' 			=> __( 'easeInExpo', 'landtech-extras-for-elementor' ),
						'easeOutExpo' 			=> __( 'easeOutExpo', 'landtech-extras-for-elementor' ),
						'easeInOutExpo' 		=> __( 'easeInOutExpo', 'landtech-extras-for-elementor' ),
						'easeInCirc' 			=> __( 'easeInCirc', 'landtech-extras-for-elementor' ),
						'easeOutCirc' 			=> __( 'easeOutCirc', 'landtech-extras-for-elementor' ),
						'easeInOutCirc' 		=> __( 'easeInOutCirc', 'landtech-extras-for-elementor' ),
						'easeInElastic' 		=> __( 'easeInElastic', 'landtech-extras-for-elementor' ),
						'easeOutElastic' 		=> __( 'easeOutElastic', 'landtech-extras-for-elementor' ),
						'easeInOutElastic' 		=> __( 'easeInOutElastic', 'landtech-extras-for-elementor' ),
						'easeInBack' 			=> __( 'easeInBack', 'landtech-extras-for-elementor' ),
						'easeOutBack' 			=> __( 'easeOutBack', 'landtech-extras-for-elementor' ),
						'easeInOutBack' 		=> __( 'easeInOutBack', 'landtech-extras-for-elementor' ),
						'easeInBounce' 			=> __( 'easeInBounce', 'landtech-extras-for-elementor' ),
						'easeOutBounce' 		=> __( 'easeOutBounce', 'landtech-extras-for-elementor' ),
						'easeInOutBounce' 		=> __( 'easeInOutBounce', 'landtech-extras-for-elementor' ),
					],
					'condition' 	=> [
						'animate!'	=> '',
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'reverse',
				[
					'label' 		=> __( 'Reverse', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'duration',
				[
					'label' 		=> __( 'Duration (ms)', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 1,
							'max' 	=> 3000,
							'step'	=> 100,
						],
					],
					'condition' 	=> [
						'animate!'	=> '',
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'appear_offset',
				[
					'label' 		=> __( 'Appear Offset', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Specifies the offset, relative to when the widget enteres the viewport, after which the animation starts', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 10,
							'max' 	=> 1000,
						],
					],
					'condition' 	=> [
						'animate!'	=> '',
					],
				]
			);

			$this->add_control(
				'angle',
				[
					'label' 		=> __( 'Start Angle', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 2 * M_PI,
							'step'	=> 0.001,
						],
					],
					'frontend_available' => true
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text',
			[
				'label' => __( 'Text', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
			'text',
				[
					'label' => '',
					'type' => Controls_Manager::WYSIWYG,
					'default' => __( 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'landtech-extras-for-elementor' ),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_circle_style',
			[
				'label' => __( 'Circle', 'landtech-extras-for-elementor' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'size',
				[
					'label' 		=> __( 'Size', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 100,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 10,
							'max' 	=> 1000,
						],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'thickness',
				[
					'label' 		=> __( 'Thickness (%)', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 10,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 1,
							'max' 	=> 100,
						],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'lineCap',
				[
					'label'		=> __( 'Line Cap', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'butt',
					'options' 	=> [
						'butt' 		=> __( 'Butt', 'landtech-extras-for-elementor' ),
						'round' 	=> __( 'Round', 'landtech-extras-for-elementor' ),
						'square' 	=> __( 'Square', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true
				]
			);

			$gradient = new Repeater();

			$gradient->add_control(
				'color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
				]
			);

			$this->add_control(
				'fill',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::REPEATER,
					'fields' 		=> $gradient->get_controls(),
					'title_field' 	=> '{{{ color }}}'
				]
			);

			$this->add_control(
				'gradient_angle',
				[
					'label'		=> __( 'Gradient Angle (&deg;)', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '2',
					'options' 	=> [
						'2' 	=> __( '0', 'landtech-extras-for-elementor' ),
						'4' 	=> __( '45', 'landtech-extras-for-elementor' ),
						'0.5' 	=> __( '90', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'emptyFill',
				[
					'label' 	=> __( 'Empty Fill', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'frontend_available' => true
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_value_style',
			[
				'label' => __( 'Value', 'landtech-extras-for-elementor' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'value_color',
				[
					'label' 	=> __( 'Value Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'default' 	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-circle-progress__value' => 'color: {{VALUE}};',
					],
					'global' => [
						'default' => Global_Colors::COLOR_TEXT,
					],
				]
			);

			$this->add_control(
				'suffix_color',
				[
					'label' 	=> __( 'Suffix Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'default' 	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-circle-progress__value .suffix' => 'color: {{VALUE}};',
					],
					'global' => [
						'default' => Global_Colors::COLOR_TEXT,
					],
				]
			);

			$this->add_control(
				'value_spacing',
				[
					'label' 		=> __( 'Value Spacing', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 200,
						],
					],
					'condition'	=> [
						'value_position!'	=> 'inside'
					],
					'selectors'	=> [
						'{{WRAPPER}}.ee-circle-progress-position--below .ee-circle-progress__value' => 'margin-top: {{SIZE}}{{UNIT}}',
					]
				]
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name' 		=> 'value_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-circle-progress__value',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'value_typography',
					'selector' 	=> '{{WRAPPER}} .ee-circle-progress__value',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			[
				'label' => __( 'Icon', 'landtech-extras-for-elementor' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'value_position!' 	=> 'inside',
					'icon!'				=> '',
				],
			]
		);

			$this->add_control(
				'icon_color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'default' 	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-circle-progress__icon' => 'color: {{VALUE}};',
					],
					'global' => [
						'default' => Global_Colors::COLOR_TEXT,
					],
					'condition'		=> [
						'value_position!' 	=> 'inside',
						'icon!'				=> '',
					],
				]
			);

			$this->add_control(
				'icon_size',
				[
					'label' 		=> __( 'Size', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 9,
							'max' 	=> 100,
						],
					],
					'range' 	=> [
						'em' 	=> [
							'min' 	=> 1,
							'max' 	=> 10,
							'step'	=> 0.1,
						],
					],
					'range' 	=> [
						'rem' 	=> [
							'min' 	=> 1,
							'max' 	=> 10,
							'step'	=> 0.1,
						],
					],
					'size_units' 	=> [ 'px', 'em', 'rem' ],
					'condition'		=> [
						'value_position!' 	=> 'inside',
						'icon!'				=> '',
					],
					'selectors'	=> [
						'{{WRAPPER}} .ee-circle-progress__icon' => 'font-size: {{SIZE}}{{UNIT}}',
					]
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			[
				'label' => __( 'Text', 'landtech-extras-for-elementor' ),
				'tab' 	=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'text_color',
				[
					'label' 	=> __( 'Text Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'default' 	=> '',
					'selectors' => [
						'{{WRAPPER}} .ee-circle-progress__text' => 'color: {{VALUE}};',
					],
					'global' => [
						'default' => Global_Colors::COLOR_TEXT,
					],
				]
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name' 		=> 'text_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-circle-progress__text',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'text_typography',
					'selector' 	=> '{{WRAPPER}} .ee-circle-progress__text',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
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
	 * @since  0.1.0
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$circle_progress_fill = array();

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => [
					'ee-circle-progress',
					'ee-circle-progress-position--' . $settings['value_position'],
				],
			],
		] );

		if( ! empty( $settings['suffix'] ) ) {
			$this->add_render_attribute( 'wrapper', 'data-suffix', $settings['suffix'] );
		}

		if ( $settings['appear_offset']['size'] ) {
			$this->add_render_attribute( 'wrapper', 'data-appear-top-offset', $settings['appear_offset']['size'] );
		}

		if ( count( $settings['fill'] ) > 0 ) {
			if ( count( $settings['fill'] ) === 1 ) {
				if ( ! empty( $settings['fill'][0]['color'] ) ) {
					$circle_progress_fill['color'] = $settings['fill'][0]['color'];
				}
			} else { // Gradient
				$circle_progress_fill['gradient'] = array();
				foreach (  $settings['fill'] as $fill ) {
					if ( ! empty( $fill['color'] ) ) {
						$circle_progress_fill['gradient'][] = $fill['color'];
					}
				}

				$gradient_angle = ( (int)$settings['gradient_angle'] > 0 ) ? (int)$settings['gradient_angle'] : 4;

				$circle_progress_fill['gradientAngle'] = M_PI / $gradient_angle;
			}

			if ( count( $circle_progress_fill ) > 0 ) {
				$circle_progress_settings['fill'] = wp_json_encode(
					$circle_progress_fill,
					JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
				);
				$this->add_render_attribute( 'wrapper', 'data-fill', $circle_progress_settings['fill'] );
			}
		}

		?><div <?php $this->print_render_attribute_string( 'wrapper' ); ?>><?php
			if ( ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon']['value'] ) ) && 'inside' !== $settings['value_position'] ) { $this->render_icon(); }
			if ( 'inside' === $settings['value_position'] ) { $this->render_value( $settings ); }
		?></div><?php

		if ( 'inside' !== $settings['value_position'] ) { $this->render_value( $settings ); }
		if ( $settings['text'] ) { $this->render_text( $settings ); }
	}

	/**
	 * Render Icon
	 * 
	 * Markup for the icon
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function render_icon() {
		$settings = $this->get_settings_for_display();

		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
		$is_new = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

		$this->add_render_attribute( [
			'icon-wrapper' => [
				'class' => [
					'ee-circle-progress__icon',
					'ee-icon-support--svg',
					'ee-icon',
				],
			],
			'icon' => [
				'class' => esc_attr( $settings['icon'] ),
				'aria-hidden' => 'true',
			],
		] );

		?><span <?php $this->print_render_attribute_string( 'icon-wrapper' ); ?>><?php
			if ( $is_new || $migrated ) {
				Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
			} else {
				?><i <?php $this->print_render_attribute_string( 'icon' ); ?>></i><?php
			}
		?></span><?php
	}

	/**
	 * Render Value
	 * 
	 * Renders the template for the value
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function render_value( $settings ) {

		$this->add_render_attribute( [
			'value-wrapper' => [
				'class' => [
					'ee-circle-progress__value',
				],
			],
			'value' => [
				'class' => 'value',
			],
			'suffix' => [
				'class' => 'suffix',
			],
		] );

		$this->add_inline_editing_attributes( 'suffix', 'basic' );

		if ( 'hide' === $settings['value_position'] ) {
			$this->add_render_attribute( 'value-wrapper', 'class', 'is--hidden' );
		}

		if ( '' !== $settings['value'] ) {
			$this->add_render_attribute( 'value-wrapper', 'data-cp-value', $settings['value'] );
		}

		?><div <?php $this->print_render_attribute_string( 'value-wrapper' ); ?>>
			<span <?php $this->print_render_attribute_string( 'value' ); ?>></span><?php
			if ( $settings['suffix'] ) {
				?><span <?php $this->print_render_attribute_string( 'suffix' ); ?>>
					<?php echo esc_html( $settings['suffix'] ); ?>
				</span><?php
			}
		?></div><?php
	}

	/**
	 * Render Text
	 * 
	 * Renders the template for the text
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function render_text( $settings ) {

		$this->add_inline_editing_attributes( 'text', 'advanced' );
		$this->add_render_attribute( 'text', 'class', 'ee-circle-progress__text' );

		?><div <?php $this->print_render_attribute_string( 'text' ); ?>>
			<?php echo wp_kses_post( $this->parse_text_editor( $settings['text'] ) ); ?>
		</div><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function content_template() {
		?><#

		var circle_progress_fill = {},
			entityMap = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#39;',
				'/': '&#x2F;',
				'`': '&#x60;',
				'=': '&#x3D;'
			};

		view.addRenderAttribute( {
			'wrapper' : {
				'class' : [
					'ee-circle-progress',
					'ee-circle-progress-position--' + settings.value_position,
				],
			},
		} );

		if ( settings.suffix ) {
			view.addRenderAttribute( 'wrapper', 'data-suffix', settings.suffix );
		}

		if ( settings.appear_offset ) {
			view.addRenderAttribute( 'wrapper', 'data-appear-top-offset', settings.appear_offset.size );
		}

		if ( settings.fill.length > 0 ) {
			if ( settings.fill.length === 1 ) {

				if ( settings.fill[0].color != '' ) {
					circle_progress_fill.color = settings.fill[0].color;
				}

			} else {

				circle_progress_fill.gradient = [];
				var gradient_angle = ( settings.gradient_angle > 0 ) ? parseInt(settings.gradient_angle) : 4;

				_.each( settings.fill, function( fill ) {
					if ( fill.color != '' ) circle_progress_fill.gradient.push( fill.color );
				});
				circle_progress_fill.gradientAngle = Math.PI / gradient_angle;
			}
		}

		if ( ! jQuery.isEmptyObject( circle_progress_fill ) ) {

			circle_progress_fill = JSON.stringify( circle_progress_fill );
			circle_progress_fill = circle_progress_fill.replace( /[&<>"'`=\/]/g, function (s) {
				return entityMap[s];
			});

			circle_progress_fill = jQuery('<textarea />').html( circle_progress_fill ).text();

			view.addRenderAttribute( 'wrapper', 'data-fill', circle_progress_fill );
		}

		#><div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>

			<# if ( ( settings.icon || settings.selected_icon ) && 'inside' !== settings.value_position ) { #>
				<?php $this->_icon_template(); ?>
			<# } #>

			<# if ( 'inside' === settings.value_position ) { #>
				<?php $this->_value_template(); ?>
			<# } #>

		</div>

		<# if ( 'inside' !== settings.value_position ) { #>
			<?php $this->_value_template(); ?>
		<# } #>

		<# if ( settings.text ) { #>
			<?php $this->_text_template(); ?>
		<# } #>

		<?php
	}

	/**
	 * Icon Template
	 * 
	 * JS template for the icon
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function _icon_template() {
		?><#

		var iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
			migrated = elementor.helpers.isIconMigrated( settings, 'selected_icon' );

		view.addRenderAttribute( {
			'icon-wrapper' : {
				'class' : [
					'ee-circle-progress__icon',
					'ee-icon-support--svg',
					'ee-icon',
				],
			},
			'icon' : {
				'class' : settings.icon,
				'aria-hidden' : 'true',
			},
		} );

		#><div {{{ view.getRenderAttributeString( 'icon-wrapper' ) }}}><#
			if ( ( migrated || ! settings.icon ) && iconHTML.rendered ) {
				#>{{{ iconHTML.value }}}<#
			} else {
				#><i {{{ view.getRenderAttributeString( 'icon' ) }}}></i><#
			}
		#></div><?php
	}

	/**
	 * Value Template
	 * 
	 * JS template for the value
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function _value_template() {
		?><#

		view.addRenderAttribute( {
			'value-wrapper' : {
				'class' : [
					'ee-circle-progress__value',
				],
			},
			'value' : {
				'class' : 'value',
			},
			'suffix' : {
				'class' : 'suffix',
			},
		} );

		if ( 'hide' === settings.value_position ) {
			view.addRenderAttribute( 'value-wrapper', 'classs', 'is--hidden' );
		}

		if ( '' !== settings.value ) {
			view.addRenderAttribute( 'value-wrapper', 'data-cp-value', settings.value );
		}

		view.addInlineEditingAttributes( 'suffix', 'basic' );

		#><div {{{ view.getRenderAttributeString( 'value-wrapper' ) }}}>

			<span {{{ view.getRenderAttributeString( 'value' ) }}}></span>

			<# if ( settings.suffix ) { #>
				<span {{{ view.getRenderAttributeString( 'suffix' ) }}}>{{{ settings.suffix }}}</span>
			<# } #>

		</div><?php
	}

	/**
	 * Text Template
	 * 
	 * JS template for the text
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function _text_template() {
		?><#

		view.addRenderAttribute( 'text', 'class', 'ee-circle-progress__text' );
		view.addInlineEditingAttributes( 'text', 'advanced' );

		#><div {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text }}}</div><?php
	}
}
