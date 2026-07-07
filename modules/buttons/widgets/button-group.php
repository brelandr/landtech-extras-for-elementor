<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Buttons\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\Utils;
use LandTechExtras\Group_Control_Button_Effect;
use LandTechExtras\Base\Extras_Widget;

// Elementor Classes
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Button_Group
 *
 * @since 0.1.0
 */
class Button_Group extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  0.1.0
	 * @return string
	 */
	public function get_name() {
		return 'button-group';
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
		return __( 'Buttons', 'landtech-extras-for-elementor' );
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
		return 'nicon nicon-button-group';
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
			'landtech-extras-hotips',
			'resize',
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
			'section_buttons',
			[
				'label' => __( 'Buttons', 'landtech-extras-for-elementor' ),
			]
		);

			$repeater = new Repeater();

			$repeater->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'selector' 	=> '{{WRAPPER}} {{CURRENT_ITEM}} .ee-button-text',
					'separator'	=> 'after',
					'condition' => [
						'button_custom_style!' => ''
					]
				]
			);

			$repeater->start_controls_tabs( 'buttons_repeater' );

			$repeater->start_controls_tab( 'tab_content', [ 'label' => __( 'Content', 'landtech-extras-for-elementor' ) ] );

				$repeater->add_control(
					'text',
					[
						'label' 		=> __( 'Text', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::TEXT,
						'default' 		=> __( 'Click me', 'landtech-extras-for-elementor' ),
						'placeholder' 	=> __( 'Click me', 'landtech-extras-for-elementor' ),
						'dynamic'		=> [ 'active' => true ],
					]
				);

				$repeater->add_control(
					'tooltip',
					[
						'label' 		=> __( 'Enable Tooltip', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> '',
						'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
						'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
						'return_value' 	=> 'tooltip',
					]
				);

				$repeater->add_control(
					'tooltip_position',
					[
						'label'		=> __( 'Show tooltip at', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> '',
						'options' 	=> [
							'' 			=> __( 'Global', 'landtech-extras-for-elementor' ),
							'bottom' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
							'left' 		=> __( 'Left', 'landtech-extras-for-elementor' ),
							'top' 		=> __( 'Top', 'landtech-extras-for-elementor' ),
							'right' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
						],
						'condition'		=> [
							'tooltip!'	=> ''
						]
					]
				);

				$repeater->add_control(
					'tooltip_arrow_position_h',
					[
						'label'		=> __( 'Tooltip Show at', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> '',
						'options' 	=> [
							'' 			=> __( 'Default', 'landtech-extras-for-elementor' ),
							'center' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
							'left' 		=> __( 'Left', 'landtech-extras-for-elementor' ),
							'right' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
						],
						'condition'	=> [
							'tooltip_position' => [ 'top', 'bottom' ],
						],
					]
				);

				$repeater->add_control(
					'tooltip_arrow_position_v',
					[
						'label'		=> __( 'Tooltip Show at', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> '',
						'options' 	=> [
							'' 			=> __( 'Default', 'landtech-extras-for-elementor' ),
							'center' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
							'bottom' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
							'top' 		=> __( 'Top', 'landtech-extras-for-elementor' ),
						],
						'condition'	=> [
							'tooltip_position' => [ 'left', 'right' ],
						],
					]
				);

				$repeater->add_control(
					'tooltip_content',
					[
						'label' 		=> __( 'Tooltip Content', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::TEXTAREA,
						'default' 		=> __( 'I am a tooltip for a button', 'landtech-extras-for-elementor' ),
						'placeholder' 	=> __( 'I am a tooltip for a button', 'landtech-extras-for-elementor' ),
						'title' 		=> __( 'Tooltip Content', 'landtech-extras-for-elementor' ),
						'rows' 			=> 5,
						'dynamic'		=> [ 'active' => true ],
						'condition'		=> [
							'tooltip!'	=> ''
						]
					]
				);

				$repeater->add_control(
					'link',
					[
						'label' 		=> __( 'Link', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::URL,
						'placeholder' 	=> esc_url( home_url( '/' ) ),
						'dynamic'		=> [ 'active' => true ],
						'label_block' 	=> false,
					]
				);

				$repeater->add_control(
					'selected_icon',
					[
						'label' 		=> __( 'Icon', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::ICONS,
						'fa4compatibility' => 'icon',
					]
				);

				$repeater->add_control(
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

				$repeater->add_control(
					'icon_indent',
					[
						'label' 	=> __( 'Icon Spacing', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::SLIDER,
						'range' 	=> [
							'px' 	=> [
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

				$repeater->add_control(
					'view',
					[
						'label' 	=> __( 'View', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::HIDDEN,
						'default' 	=> 'traditional',
					]
				);

				$repeater->add_control(
					'_element_id',
					[
						'label' 		=> __( 'CSS ID', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::TEXT,
						'dynamic'		=> [ 'active' => true ],
						'default' 		=> '',
						'label_block' 	=> false,
						'title' 		=> __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'landtech-extras-for-elementor' ),
					]
				);

				$repeater->add_control(
					'css_classes',
					[
						'label' 		=> __( 'CSS Classes', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::TEXT,
						'dynamic'		=> [ 'active' => true ],
						'default' 		=> '',
						'label_block' 	=> false,
						'title' 		=> __( 'Add your custom class WITHOUT the dot. e.g: my-class', 'landtech-extras-for-elementor' ),
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab( 'tab_layout', [ 'label' => __( 'Layout', 'landtech-extras-for-elementor' ) ] );

				$repeater->add_control(
					'size',
					[
						'label' 		=> __( 'Size', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SELECT,
						'default' 		=> 'sm',
						'options' 		=> Utils::get_button_sizes(),
					]
				);

				$repeater->add_responsive_control(
					'label_min_width',
					[
						'label' 		=> __( 'Label Min Width', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 10,
								'max' 	=> 1000,
								'step'	=> 1,
							],
						],
						'selectors'		=> [
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-button-text' => 'min-width: {{SIZE}}px;',
						]
					]
				);

				$repeater->add_responsive_control(
					'min_width',
					[
						'label' 		=> __( 'Button Min Width', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SLIDER,
						'range' 	=> [
							'px' 	=> [
								'min' 	=> 10,
								'max' 	=> 1000,
								'step'	=> 1,
							],
						],
						'selectors'		=> [
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-button' => 'min-width: {{SIZE}}px;',
						]
					]
				);

				$repeater->add_control(
					'text_align',
					[
						'label' 		=> __( 'Align Text', 'landtech-extras-for-elementor' ),
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
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-button-text' => 'text-align: {{VALUE}};'
						]
					]
				);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab( 'tab_style', [ 'label' => __( 'Style', 'landtech-extras-for-elementor' ) ] );

				$repeater->add_control(
					'button_custom_style',
					[
						'label' 		=> __( 'Custom', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::SWITCHER,
						'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
						'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
						'return_value' 	=> 'yes',
						'description'   => __( 'Set custom styles that will only affect this specific button.', 'landtech-extras-for-elementor' ),
					]
				);

				$repeater->add_group_control(
					Group_Control_Button_Effect::get_type(),
					[
						'name' 		=> 'button_effect',
						'label' 	=> __( 'Effect', 'landtech-extras-for-elementor' ),
						'selector' 	=> '{{WRAPPER}} {{CURRENT_ITEM}} .ee-button-wrapper',
						'condition' => [
							'button_custom_style!' => ''
						],
					]
				);

				$repeater->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' 		=> 'button_border',
						'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
						'selector' 	=> '{{WRAPPER}} {{CURRENT_ITEM}} .ee-button',
						'condition' => [
							'button_custom_style!' => ''
						]
					]
				);

				$repeater->add_control(
					'border_radius',
					[
						'type' 			=> Controls_Manager::DIMENSIONS,
						'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
						'size_units' 	=> [ 'px', '%' ],
						'selectors' 	=> [
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-button,
							 {{WRAPPER}} {{CURRENT_ITEM}} .ee-effect--radius .ee-button:before,
							 {{WRAPPER}} {{CURRENT_ITEM}} .ee-effect--radius .ee-button:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'button_custom_style!' => '',
							'button_effect_type!' => '3d',
						]
					]
				);

				$repeater->add_control(
					'text_padding',
					[
						'label' 		=> __( 'Text Padding', 'landtech-extras-for-elementor' ),
						'type' 			=> Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', 'em', '%' ],
						'selectors' 	=> [
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-button-content-wrapper,
							{{WRAPPER}} {{CURRENT_ITEM}} .ee-button:after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'button_custom_style!' => ''
						],
						'separator' => 'before',
					]
				);

				$repeater->add_control(
					'heading_style',
					[
						'type'		=> Controls_Manager::HEADING,
						'label' 	=> __( 'Default', 'landtech-extras-for-elementor' ),
						'separator' => 'before',
						'condition' => [
							'button_custom_style!' => ''
						]
					]
				);

				$repeater->add_control(
					'button_text_color',
					[
						'label' 	=> __( 'Text Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-button' => 'color: {{VALUE}};',
						],
						'condition' => [
							'button_custom_style!' => ''
						]
					]
				);

				$repeater->add_control(
					'background_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-button' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'button_custom_style!' => ''
						]
					]
				);

				$repeater->add_control(
					'heading_hover_style',
					[
						'type'		=> Controls_Manager::HEADING,
						'label' 	=> __( 'Hover', 'landtech-extras-for-elementor' ),
						'separator' => 'before',
						'condition' => [
							'button_custom_style!' => ''
						]
					]
				);

				$repeater->add_control(
					'hover_color',
					[
						'label' 	=> __( 'Text Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default' 	=> '',
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-button-wrapper:hover .ee-button' => 'color: {{VALUE}};',
						],
						'condition' => [
							'button_custom_style!' => ''
						]
					]
				);

				$repeater->add_control(
					'button_background_hover_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-button-wrapper:hover .ee-button' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'button_custom_style!' => ''
						]
					]
				);

				$repeater->add_control(
					'button_hover_border_color',
					[
						'label' 	=> __( 'Border Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'condition' => [
							'button_border_border!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-button-wrapper:hover .ee-button' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'button_custom_style!' => ''
						]
					]
				);

				$repeater->add_control(
					'hover_animation',
					[
						'label' 	=> __( 'Animation', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::HOVER_ANIMATION,
						'condition' => [
							'button_custom_style!' => ''
						]
					]
				);

			$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'buttons',
				[
					'label' 	=> __( 'Buttons', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[
							'text' 	=> __( 'Button #1', 'landtech-extras-for-elementor' )
						],
						[
							'text' 	=> __( 'Button #2', 'landtech-extras-for-elementor' )
						],
					],
					'fields' 		=> $repeater->get_controls(),
					'title_field' 	=> '{{{ text }}}',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tooltips',
			[
				'label' => __( 'Tooltips', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_responsive_control(
				'trigger',
				[
					'label'		=> __( 'Trigger', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'mouseenter',
					'tablet_default' 	=> 'click_target',
					'mobile_default' 	=> 'click_target',
					'options' 	=> [
						'mouseenter' 	=> __( 'Mouse Over', 'landtech-extras-for-elementor' ),
						'click_target' 	=> __( 'Click Target', 'landtech-extras-for-elementor' ),
						'load' 			=> __( 'Page Load', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_responsive_control(
				'_hide',
				[
					'label'		=> __( 'Hide on', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 			=> 'mouseleave',
					'tablet_default' 	=> 'click_out',
					'mobile_default' 	=> 'click_out',
					'options' 	=> [
						'mouseleave' 	=> __( 'Mouse Leave', 'landtech-extras-for-elementor' ),
						'click_out' 	=> __( 'Click Outside', 'landtech-extras-for-elementor' ),
						'click_target' 	=> __( 'Click Target', 'landtech-extras-for-elementor' ),
						'click_any' 	=> __( 'Click Anywhere', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'position',
				[
					'label'		=> __( 'Show to', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'bottom',
					'options' 	=> [
						'bottom' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
						'left' 		=> __( 'Left', 'landtech-extras-for-elementor' ),
						'top' 		=> __( 'Top', 'landtech-extras-for-elementor' ),
						'right' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'arrow_position_h',
				[
					'label'		=> __( 'Show at', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 			=> __( 'Center', 'landtech-extras-for-elementor' ),
						'left' 		=> __( 'Left', 'landtech-extras-for-elementor' ),
						'right' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
					],
					'condition'		=> [
						'position' => [ 'top', 'bottom' ],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'arrow_position_v',
				[
					'label'		=> __( 'Show at', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 			=> __( 'Center', 'landtech-extras-for-elementor' ),
						'top' 		=> __( 'Top', 'landtech-extras-for-elementor' ),
						'bottom' 	=> __( 'Bottom', 'landtech-extras-for-elementor' ),
					],
					'condition'		=> [
						'position' => [ 'left', 'right' ],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'css_position',
				[
					'label' 		=> __( 'CSS Position', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> '',
					'options'		=> [
						'' 			=> 'Absolute',
						'fixed'		=> 'Fixed',
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'disable',
				[
					'label'		=> __( 'Disable On', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						'' 			=> __( 'None', 'landtech-extras-for-elementor' ),
						'tablet' 	=> __( 'Tablet & Mobile', 'landtech-extras-for-elementor' ),
						'mobile' 	=> __( 'Mobile', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'tooltips_arrow',
				[
					'label'		=> __( 'Arrow', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '""',
					'options' 	=> [
						'""' 	=> __( 'Show', 'landtech-extras-for-elementor' ),
						'none' 	=> __( 'Hide', 'landtech-extras-for-elementor' ),
					],
					'selectors' => [
						'.ee-tooltip.ee-tooltip-{{ID}}:after' => 'content: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'delay_in',
				[
					'label' 		=> __( 'Delay in (s)', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Time until tooltips appear.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 1,
							'step'	=> 0.1,
						],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'delay_out',
				[
					'label' 		=> __( 'Delay out (s)', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Time until tooltips dissapear.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 1,
							'step'	=> 0.1,
						],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'duration',
				[
					'label' 		=> __( 'Duration', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 2,
							'step'	=> 0.1,
						],
					],
					'frontend_available' => true
				]
			);

			$this->add_control(
				'tooltips_distance',
				[
					'label' 		=> __( 'Distance', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'The distance between the tooltip and the hotspot. Defaults to 6px', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 0,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
						],
					],
					'selectors'		=> [
						'.ee-tooltip.ee-tooltip-{{ID}}.to--top' 	=> 'transform: translateY(-{{SIZE}}{{UNIT}});',
						'.ee-tooltip.ee-tooltip-{{ID}}.to--bottom' 	=> 'transform: translateY({{SIZE}}{{UNIT}});',
						'.ee-tooltip.ee-tooltip-{{ID}}.to--left' 	=> 'transform: translateX(-{{SIZE}}{{UNIT}});',
						'.ee-tooltip.ee-tooltip-{{ID}}.to--right' 	=> 'transform: translateX({{SIZE}}{{UNIT}});',
					]
				]
			);

			$this->add_control(
				'tooltips_offset',
				[
					'label' 		=> __( 'Offset', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Adjust offset to align arrow with target.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 0,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> -100,
							'max' 	=> 100,
						],
					],
					'selectors'	=> [
						'.ee-tooltip.ee-tooltip-{{ID}}.to--top,
						 .ee-tooltip.ee-tooltip-{{ID}}.to--bottom' => 'margin-left: {{SIZE}}{{UNIT}};',
						'.ee-tooltip.ee-tooltip-{{ID}}.to--left,
						 .ee-tooltip.ee-tooltip-{{ID}}.to--right' => 'margin-top: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'tooltips_width',
				[
					'label' 		=> __( 'Maximum Width', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 350,
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 500,
						],
					],
					'selectors'		=> [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'max-width: {{SIZE}}{{UNIT}};',
					]
				]
			);

			$this->add_control(
				'tooltips_zindex',
				[
					'label'			=> __( 'zIndex', 'landtech-extras-for-elementor' ),
					'description'   => __( 'Adjust the z-index of the tooltips. Defaults to 999', 'landtech-extras-for-elementor' ),
					'type'			=> Controls_Manager::NUMBER,
					'default'		=> '999',
					'min'			=> -9999999,
					'step'			=> 1,
					'selectors'		=> [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'z-index: {{SIZE}};',
					]
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' 	=> __( 'Buttons', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'vertical_align',
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
					'prefix_class'		=> 'ee-button-group%s-valign-',
				]
			);

			$this->add_responsive_control(
				'align',
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
						'justify' 		=> [
							'title' 	=> __( 'Stretch', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-stretch',
						],
					],
					'prefix_class'		=> 'ee-button-group%s-halign-'
				]
			);

			$this->add_control(
				'content_align',
				[
					'label' 		=> __( 'Align Content', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'justify',
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
						'justify' 		=> [
							'title' 	=> __( 'Stretch', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-stretch',
						],
					],
					'condition' => [
						'align' => 'justify',
					],
					'prefix_class'		=> 'ee-button-group-content-halign-'
				]
			);

			$this->add_control(
				'text_align',
				[
					'label' 		=> __( 'Align Text', 'landtech-extras-for-elementor' ),
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
						'align' 		=> 'justify',
						'content_align' => 'justify',
					],
					'selectors'		=> [
						'{{WRAPPER}} .ee-button-text' => 'text-align: {{VALUE}};',
					]
				]
			);

			$this->add_control(
				'gap',
				[
					'label' 		=> __( 'Buttons Gap', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Select Custom to be able to specify a different gap for each breakpoint.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'default',
					'options' 		=> [
						'default' 	=> __( 'Default', 'landtech-extras-for-elementor' ),
						'no' 		=> __( 'No Gap', 'landtech-extras-for-elementor' ),
						'narrow' 	=> __( 'Narrow', 'landtech-extras-for-elementor' ),
						'extended' 	=> __( 'Extended', 'landtech-extras-for-elementor' ),
						'wide' 		=> __( 'Wide', 'landtech-extras-for-elementor' ),
						'wider' 	=> __( 'Wider', 'landtech-extras-for-elementor' ),
						'custom' 	=> __( 'Custom', 'landtech-extras-for-elementor' ),
					],
					'prefix_class'	=> 'ee-button-group-gap-',
				]
			);

			$this->add_responsive_control(
				'custom_gap',
				[
					'label' 	=> __( 'Custom Gap', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min'	=> 1,
							'max' 	=> 100,
						],
					],
					'condition' => [
						'gap' => 'custom',
					],
					'selectors' => [
						// No stacking
						'{{WRAPPER}} .ee-button-group' 	=> 'margin-left: -{{SIZE}}{{UNIT}}; margin-bottom: -{{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-button-gap' => 'margin-left: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',

						// Stacked
						'(desktop){{WRAPPER}}.ee-button-group-stack-desktop .ee-button-gap:not(:last-child)' 	=> 'margin-bottom: {{SIZE}}{{UNIT}};',
						'(tablet){{WRAPPER}}.ee-button-group-stack-tablet .ee-button-gap:not(:last-child)' 		=> 'margin-bottom: {{SIZE}}{{UNIT}};',
						'(mobile){{WRAPPER}}.ee-button-group-stack-mobile .ee-button-gap:not(:last-child)' 		=> 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
					'separator' => 'after',
				]
			);

			$this->add_control(
				'text_padding',
				[
					'label' 		=> __( 'Text Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-button-content-wrapper,
						 {{WRAPPER}} .ee-button:after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'buttons_border_radius',
				[
					'type' 			=> Controls_Manager::DIMENSIONS,
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-button,
						 {{WRAPPER}} .ee-effect--radius .ee-button:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'stack',
				[
					'label' 		=> __( 'Stack', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Choose on what breakpoint should the buttons begin to stack.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> '',
					'options' 	=> [
						'' 			=> __( 'None', 'landtech-extras-for-elementor' ),
						'desktop' 	=> __( 'Desktop', 'landtech-extras-for-elementor' ),
						'tablet' 	=> __( 'Tablet', 'landtech-extras-for-elementor' ),
						'mobile' 	=> __( 'Mobile', 'landtech-extras-for-elementor' ),
					],
					'prefix_class'	=> 'ee-button-group-stack-',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'typography',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					],
					'selector' 	=> '{{WRAPPER}} .ee-button',
				]
			);

			$this->start_controls_tabs( 'buttons_style' );

			$this->start_controls_tab( 'buttons_style_default', [ 'label' => __( 'Default', 'landtech-extras-for-elementor' ) ] );

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' 		=> 'buttons_border',
						'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
						'selector' 	=> '{{WRAPPER}} .ee-button',
					]
				);

				$this->add_control(
					'buttons_text_color',
					[
						'label' 	=> __( 'Text Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default' 	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-button' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'buttons_background_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'global'	=> [
							'default' => Global_Colors::COLOR_ACCENT,
						],
						'selectors' => [
							'{{WRAPPER}} .ee-button' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'buttons_box_shadow',
						'selector' 	=> '{{WRAPPER}} .ee-button',
						'separator'	=> '',
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'buttons_style_hover', [ 'label' => __( 'Hover', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'buttons_hover_color',
					[
						'label' 	=> __( 'Text Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default' 	=> '',
						'selectors' => [
							'{{WRAPPER}} .ee-button-wrapper:hover .ee-button' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'buttons_background_hover_color',
					[
						'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-button-wrapper:hover .ee-button' => 'background-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'buttons_hover_border_color',
					[
						'label' 	=> __( 'Border Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'condition' => [
							'button_border_border!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .ee-button-wrapper:hover .ee-button' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'buttons_hover_box_shadow',
						'selector' 	=> '{{WRAPPER}} .ee-button-wrapper:hover .ee-button',
						'separator'	=> '',
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tooltips_style',
			[
				'label' 	=> __( 'Tooltips', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'tooltips_align',
				[
					'label' 	=> __( 'Text Align', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::CHOOSE,
					'options' 	=> [
						'left' 	=> [
							'title' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 	=> [
							'title' => __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 	=> 'fa fa-align-center',
						],
						'right' 	=> [
							'title' => __( 'Right', 'landtech-extras-for-elementor' ),
							'icon'	=> 'fa fa-align-right',
						],
					],
					'selectors' => [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'tooltips_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'tooltips_border',
					'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '.ee-tooltip.ee-tooltip-{{ID}}',
				]
			);

			$this->add_control(
				'tooltips_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'tooltips_typography',
					'selector' 	=> '.ee-tooltip.ee-tooltip-{{ID}}',
					'separator' => 'after',
				]
			);

			$this->add_control(
				'tooltips_background_color',
				[
					'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => Utils::get_tooltip_background_selectors(),
				]
			);

			$this->add_control(
				'tooltips_color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'.ee-tooltip.ee-tooltip-{{ID}}' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'tooltips_box_shadow',
					'selector' => '.ee-tooltip.ee-tooltip-{{ID}}',
					'separator'	=> '',
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

		if ( ! $settings['buttons'] ) {
			return;
		}

		$this->add_render_attribute( 'group', 'class', 'ee-button-group' );

		?><ul <?php $this->print_render_attribute_string( 'group' ); ?>><?php

			foreach ( $settings['buttons'] as $index => $item ) {

				$_has_tooltip = false;
				$has_icon = false;

				$button_text_clone = $item['text'];

				$gap_key 		= $this->get_repeater_setting_key( 'item', 'buttons', $index );
				$wrapper_key 	= $this->get_repeater_setting_key( 'wrapper', 'buttons', $index );
				$button_key 	= $this->get_repeater_setting_key( 'button', 'buttons', $index );
				$icon_key 		= $this->get_repeater_setting_key( 'icon', 'buttons', $index );
				$content_key 	= $this->get_repeater_setting_key( 'content', 'buttons', $index );
				$tooltip_key 	= $this->get_repeater_setting_key( 'tooltip', 'buttons', $index );
				$text_key		= $this->get_repeater_setting_key( 'text', 'buttons', $index );
				$content_id 	= $this->get_id() . '_' . $item['_id'];

				$this->add_render_attribute( [
					$gap_key => [
						'class' => [
							'ee-button-gap',
							'elementor-repeater-item-' . $item['_id'],
						],
					],
					$wrapper_key => [
						'class' => [
							'ee-button-wrapper',
						],
					],
					$button_key => [
						'class' => [
							'ee-button',
						],
					],
					$content_key => [
						'class' => [
							'ee-button-content-wrapper',
						],
					],
					$text_key => [
						'class' => [
							'ee-button-text',
						],
					],
					$tooltip_key => [
						'class' => 'hotip-content',
						'id' => 'hotip-content-' . $content_id,
					],
				] );

				$this->add_inline_editing_attributes( $text_key, 'none' );

				$migrated = isset( $item['__fa4_migrated']['selected_icon'] );
				$is_new = empty( $item['icon'] ) && Icons_Manager::is_migration_allowed();

				if ( ! empty( $item['icon'] ) || ! empty( $item['selected_icon']['value'] ) ) {

					$this->add_render_attribute( $icon_key, 'class', [
						'ee-button-icon',
						'ee-icon',
						'ee-icon-support--svg',
						'ee-icon--' . $item['icon_align'],
					] );

					if ( '' === $item['text'] ) {
						$this->add_render_attribute( $icon_key, 'class', [
							'ee-icon--flush',
						] );
					}

					$has_icon = true;
				}

				if ( 'tooltip' === $item['tooltip'] && ! empty( $item['tooltip_content'] ) ) {
					$this->add_render_attribute( $wrapper_key, [
						'class' 				=> 'hotip',
						'data-hotips-content' 	=> '#hotip-content-' . $content_id,
						'data-hotips-class' 	=> [
							'ee-global',
							'ee-tooltip',
							'ee-tooltip-' . $this->get_id() ],
						'data-hotips-position' 			=> $item['tooltip_position'],
						'data-hotips-arrow-position-h' 	=> $item['tooltip_arrow_position_h'],
						'data-hotips-arrow-position-v' 	=> $item['tooltip_arrow_position_v'],

					] );

					$_has_tooltip = true;
				}

				if ( ! empty( $item['link']['url'] ) ) {

					$this->add_render_attribute( $button_key, 'class', 'ee-button-link' );
					$this->add_render_attribute( $wrapper_key, 'href', $item['link']['url'] );

					if ( ! empty( $item['link']['is_external'] ) ) {
						$this->add_render_attribute( $wrapper_key, 'target', '_blank' );
					}

					if ( ! empty( $item['link']['nofollow'] ) ) {
						$this->add_render_attribute( $wrapper_key, 'rel', 'nofollow' );
					}
				} else {
					$this->add_render_attribute( $wrapper_key, 'href', '#' );
					$this->add_render_attribute( $wrapper_key, 'class', 'ee-button-wrapper--no-link' );
				}

				if ( ! empty( $item['size'] ) ) {
					$this->add_render_attribute( $button_key, 'class', 'ee-size-' . $item['size'] );
				}

				if ( $item['hover_animation'] ) {
					$this->add_render_attribute( $wrapper_key, 'class', 'elementor-animation-' . $item['hover_animation'] );
				}

				if ( $item['css_classes'] ) {
					$this->add_render_attribute( $wrapper_key, 'class', $item['css_classes'] );
				}

				if ( $item['_element_id'] ) {
					$this->add_render_attribute( $wrapper_key, 'id', $item['_element_id'] );
				}

				if ( 'yes' === $item['button_custom_style'] && ! empty( $item['button_effect_type'] ) ) {

					$effect_direction   = isset( $item['button_effect_direction'] ) ? $item['button_effect_direction'] : '';
					$effect_entrance    = ! empty( $item['button_effect_entrance'] ) ? $item['button_effect_entrance'] : 'cover';
					$effect_orientation = ! empty( $item['button_effect_orientation'] ) ? $item['button_effect_orientation'] : 'horizontal';

					$this->add_render_attribute( $wrapper_key, 'class', [
						'ee-effect',
						'ee-effect-type--' . $item['button_effect_type'],
					] );

					if ( in_array( $item['button_effect_type'], array( 'clone', 'back', '3d', 'flip', 'cube' ), true ) ) {
						if ( '' !== $effect_direction ) {
							$this->add_render_attribute( $wrapper_key, 'class', [
								'ee-effect-direction--' . $effect_direction,
							] );
						} elseif ( 'back' === $item['button_effect_type'] ) {
							$this->add_render_attribute( $wrapper_key, 'class', [
								'ee-effect-orientation--' . $effect_orientation,
							] );
						} else {
							$this->add_render_attribute( $wrapper_key, 'class', [
								'ee-effect-direction--down',
							] );
						}
					}

					if ( in_array( $item['button_effect_type'], array( 'flip' )) ) {
						$this->add_render_attribute( $wrapper_key, 'class', [
							'ee-effect--radius'
						] );
					}

					if ( in_array( $item['button_effect_type'], array( 'clone', 'back', 'flip', '3d', 'cube' )) ) {
						$this->add_render_attribute( $wrapper_key, 'class', [
							'ee-effect--background'
						] );
					}

					if ( in_array( $item['button_effect_type'], array( 'clone', 'flip', 'cube' )) ) {
						$this->add_render_attribute( $wrapper_key, 'class', [
							'ee-effect--foreground'
						] );
					}

					if ( in_array( $item['button_effect_type'], array( 'back' )) ) {
						$this->add_render_attribute( $wrapper_key, 'class', [
							'ee-effect--double-background'
						] );
					}

					if ( in_array( $item['button_effect_type'], array( 'back' )) && '' !== $item['button_effect_double'] ) {
						$this->add_render_attribute( $wrapper_key, 'class', [
							'ee-effect--double'
						] );
					}

					if ( in_array( $item['button_effect_type'], array( 'clone' ), true ) ) {
						$this->add_render_attribute( $wrapper_key, 'class', [
							'ee-effect-entrance--' . $effect_entrance,
						] );
					}

					if ( in_array( $item['button_effect_type'], array( 'clone', '3d', 'flip', 'cube' ), true ) && ! empty( $item['button_effect_zoom'] ) ) {
						$this->add_render_attribute( $wrapper_key, 'class', [
							'ee-effect-zoom--' . $item['button_effect_zoom']
						] );
					}

					if ( in_array( $item['button_effect_type'], array( 'clone', 'back' ), true ) && ! empty( $item['button_effect_shape'] ) ) {
						$this->add_render_attribute( $wrapper_key, 'class', [
							'ee-effect-shape--' . $item['button_effect_shape']
						] );
					}

					if ( in_array( $item['button_effect_type'], array( '3d', 'flip', 'cube' )) ) {
						$this->add_render_attribute( $wrapper_key, 'class', [
							'ee-effect--perspective'
						] );
					}

					if ( in_array( $item['button_effect_type'], array( 'clone', 'flip', 'cube' )) && '' !== $item['button_effect_text'] ) {
						$button_text_clone = $item['button_effect_text'];
					}
				}

				$this->add_render_attribute( $button_key, 'data-label', $button_text_clone );

				if ( ( ! $this->_is_edit_mode && $item['text'] ) || $this->_is_edit_mode ) {

				?><li <?php $this->print_render_attribute_string( $gap_key ); ?>>
					<a <?php $this->print_render_attribute_string( $wrapper_key ); ?>>

						<span <?php $this->print_render_attribute_string( $button_key ); ?>>
							<span <?php $this->print_render_attribute_string( $content_key ); ?>><?php

								if ( $has_icon ) {
									?><span <?php $this->print_render_attribute_string( $icon_key ); ?>><?php
										if ( $is_new || $migrated ) {
											Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] );
										} else {
											?><i class="<?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></i><?php
										}
									?></span><?php
								}

								?><span <?php $this->print_render_attribute_string( $text_key ); ?>>
									<?php echo esc_html( $item['text'] ); ?>
								</span>

								<?php if ( $_has_tooltip ) { ?>
								<span <?php $this->print_render_attribute_string( $tooltip_key ); ?>>
									<?php echo wp_kses_post( $this->parse_text_editor( $item['tooltip_content'] ) ); ?>
								</span>
								<?php } ?>

							</span>
						</span>

					</a>
				</li><?php
				}
			}
			?></ul>
		<?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering.
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function content_template() { ?><#

		var widgetId = view.$el.data('id');

		view.addRenderAttribute( 'group', 'class', 'ee-button-group' )

		if ( settings.buttons ) {

		#><ul {{{ view.getRenderAttributeString( 'group' ) }}}><#

			_.each( settings.buttons, function( item, index ) {

				var button_text_clone 	= item.text;

				var wrapperKey 		= view.getRepeaterSettingKey( 'wrapper', 'buttons', index ),
					gapKey 			= view.getRepeaterSettingKey( 'item', 'buttons', index ),
					buttonKey 		= view.getRepeaterSettingKey( 'button', 'buttons', index ),
					iconKey 		= view.getRepeaterSettingKey( 'icon', 'buttons', index ),
					contentKey 		= view.getRepeaterSettingKey( 'content', 'buttons', index ),
					tooltipKey 		= view.getRepeaterSettingKey( 'tooltip', 'buttons', index ),
					textKey			= view.getRepeaterSettingKey( 'text', 'buttons', index ),

					has_icon 		= false,
					_has_tooltip 	= false;

				if ( item.tooltip == 'tooltip' && item.tooltip_content ) {
					view.addRenderAttribute( wrapperKey, 'class', 'hotip' );
				}

				view.addRenderAttribute( gapKey, 'class', [
					'ee-button-gap',
					'elementor-repeater-item-' + item._id
				] );

				view.addRenderAttribute( wrapperKey, 'class', 'ee-button-wrapper' );
				view.addRenderAttribute( buttonKey, 'class', 'ee-button' );
				view.addRenderAttribute( contentKey, 'class', 'ee-button-content-wrapper' );

				view.addRenderAttribute( textKey, 'class', 'ee-button-text' );
				view.addInlineEditingAttributes( textKey, 'none' );

				view.addRenderAttribute( tooltipKey, 'class', 'hotip-content' );
				view.addRenderAttribute( tooltipKey, 'id', 'hotip-content-' + widgetId + '_' + item._id );

				if ( item.icon || item.selected_icon.value ) {
					view.addRenderAttribute( iconKey, 'class', [
						'ee-button-icon',
						'ee-icon',
						'ee-icon-support--svg',
						'ee-icon--' + item.icon_align,
					] );

					if ( '' === item.text ) {
						view.addRenderAttribute( iconKey, 'class', [
							'ee-icon--flush',
						] );
					}

					var iconHTML = elementor.helpers.renderIcon( view, item.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
						migrated = elementor.helpers.isIconMigrated( item, 'selected_icon' );

					has_icon = true;
				}

				if ( 'tooltip' === item.tooltip && '' !== item.tooltip_content ) {

					view.addRenderAttribute( wrapperKey, 'class', 'hotip' );
					view.addRenderAttribute( wrapperKey, 'data-hotips-content', '#hotip-content-' + widgetId + '_' + item._id );
					view.addRenderAttribute( wrapperKey, 'data-hotips-class', [
						'ee-global',
						'ee-tooltip'
					] );

					view.addRenderAttribute( wrapperKey, 'data-hotips-position', item.tooltip_position );
					view.addRenderAttribute( wrapperKey, 'data-hotips-arrow-position-h', item.tooltip_arrow_position_h );
					view.addRenderAttribute( wrapperKey, 'data-hotips-arrow-position-v', item.tooltip_arrow_position_v );

					_has_tooltip = true;
				}

				if ( '' !== item.link.url ) {
					view.addRenderAttribute( buttonKey, 'class', 'ee-button-link' );
					view.addRenderAttribute( wrapperKey, 'href', item.link.url );
				}

				if ( '' !== item.size ) {
					view.addRenderAttribute( buttonKey, 'class', 'ee-size-' + item.size );
				}

				if ( item.hover_animation ) {
					view.addRenderAttribute( wrapperKey, 'class', 'elementor-animation-' + item.hover_animation );
				}

				if ( item.css_classes ) {
					view.addRenderAttribute( wrapperKey, 'class', item.css_classes );
				}

				if ( item._element_id ) {
					view.addRenderAttribute( wrapperKey, 'id', item._element_id );
				}

				if ( 'yes' === item.button_custom_style ) {

					view.addRenderAttribute( wrapperKey, 'class', [
						'ee-effect',
						'ee-effect-type--' + item.button_effect_type,
					] );

					if ( [ 'clone', 'back', '3d', 'flip', 'cube' ].indexOf( item.button_effect_type ) > -1 && '' !== item.button_effect_direction ) {
						view.addRenderAttribute( wrapperKey, 'class', 'ee-effect-direction--' + item.button_effect_direction );
					}

					if ( [ 'back' ].indexOf( item.button_effect_type ) > -1 && '' !== item.button_effect_orientation && '' === item.button_effect_direction  ) {
						view.addRenderAttribute( wrapperKey, 'class', 'ee-effect-orientation--' + item.button_effect_orientation );
					}

					if ( [ 'flip' ].indexOf( item.button_effect_type ) > -1 ) {
						view.addRenderAttribute( wrapperKey, 'class', 'ee-effect--radius' );
					}

					if ( [ 'clone', 'back', 'flip', '3d', 'cube' ].indexOf( item.button_effect_type ) > -1 ) {
						view.addRenderAttribute( wrapperKey, 'class', 'ee-effect--background' );
					}

					if ( [ 'clone', 'flip', 'cube' ].indexOf( item.button_effect_type ) > -1 ) {
						view.addRenderAttribute( wrapperKey, 'class', 'ee-effect--foreground' );
					}

					if ( [ 'back' ].indexOf( item.button_effect_type ) > -1 ) {
						view.addRenderAttribute( wrapperKey, 'class', 'ee-effect--double-background' );
					}

					if ( [ 'back' ].indexOf( item.button_effect_type ) > -1 && '' !== item.button_effect_double ) {
						view.addRenderAttribute( wrapperKey, 'class', 'ee-effect--double' );
					}

					if ( [ 'clone' ].indexOf( item.button_effect_type ) > -1 ) {
						view.addRenderAttribute( wrapperKey, 'class', 'ee-effect-entrance--' + item.button_effect_entrance );
					}

					if ( [ 'clone', '3d', 'flip', 'cube' ].indexOf( item.button_effect_type ) > -1 ) {
						view.addRenderAttribute( wrapperKey, 'class', 'ee-effect-zoom--' + item.button_effect_zoom );
					}

					if ( [ 'clone', 'back' ].indexOf( item.button_effect_type ) > -1 ) {
						view.addRenderAttribute( wrapperKey, 'class', 'ee-effect-shape--' + item.button_effect_shape );
					}

					if ( [ '3d', 'flip', 'cube' ].indexOf( item.button_effect_type ) > -1 ) {
						view.addRenderAttribute( wrapperKey, 'class', 'ee-effect--perspective' );
					}

					if ( [ 'clone', 'flip', 'cube' ].indexOf( item.button_effect_type ) > -1 && '' !== item.button_effect_text ) {
						button_text_clone = item.button_effect_text;
					}

					view.addRenderAttribute( buttonKey, 'data-label', button_text_clone );

				} #>

				<li {{{ view.getRenderAttributeString( gapKey ) }}}>
					<a {{{ view.getRenderAttributeString( wrapperKey ) }}}>

						<span {{{ view.getRenderAttributeString( buttonKey ) }}}>
							<span {{{ view.getRenderAttributeString( contentKey ) }}}>

								<# if ( has_icon ) { #>
								<span {{{ view.getRenderAttributeString( iconKey ) }}}>
									<# if ( ( migrated || ! item.icon ) && iconHTML.rendered ) { #>
										{{{ iconHTML.value }}}
									<# } else { #>
										<i class="{{ item.icon }}" aria-hidden="true"></i>
									<# } #>
								</span>
								<# } #>

								<span {{{ view.getRenderAttributeString( textKey ) }}}>
									{{{ item.text }}}
								</span>

								<# if ( _has_tooltip ) { #>
								<span {{{ view.getRenderAttributeString( tooltipKey ) }}}>
									{{{ item.tooltip_content }}}
								</span>
								<# } #>

							</span>
						</span>

					</a>
				</li>
			<# }); #>
		</ul><# } #><?php
	}
}
