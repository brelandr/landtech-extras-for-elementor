<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Toggle\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\Toggle\Skins;
use LandTechExtras\Modules\TemplatesControl\Module as TemplatesControl;
use LandTechExtras\Group_Control_Transition;
use LandTechExtras\Schema\Schema_Builder;
use LandTechExtras\Schema\Schema_Validator;

// Elementor Classes
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Toggle_Element
 *
 * @since 2.0.0
 */
class Toggle_Element extends Extras_Widget {

	/**
	 * Has template content
	 *
	 * @since  2.0.0
	 * @var    bool
	 */
	protected $_has_template_content = false;

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  2.0.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-toggle-element';
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
		return __( 'Toggle Element', 'landtech-extras-for-elementor' );
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
		return 'nicon nicon-toggle';
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
		return [
			'landtech-extras-toggle-element',
			'landtech-extras-jquery-resize',
		];
	}

	/**
	 * Register Skins
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_skins() {
		$this->add_skin( new Skins\Skin_Classic( $this ) );
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function _register_controls() {
		$this->register_content_controls();
	}

	/**
	 * Register Content Controls
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function register_content_controls() {

		$this->start_controls_section(
			'section_elements',
			[
				'label' => __( 'Elements', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$repeater = new Repeater();

			$repeater->start_controls_tabs( 'elements_repeater' );

			$repeater->start_controls_tab( 'element_content', [ 'label' => __( 'Content', 'landtech-extras-for-elementor' ) ] );

				$repeater->add_control(
					'text',
					[
						'default'	=> '',
						'type'		=> Controls_Manager::TEXT,
						'dynamic'	=> [ 'active' => true ],
						'label' 	=> __( 'Label', 'landtech-extras-for-elementor' ),
						'separator' => 'none',
					]
				);

				$repeater->add_control(
					'hash',
					[
						'label' 	=> __( 'Hash', 'landtech-extras-for-elementor' ),
						'title'   	=> __( 'Add the hashtag name WITHOUT the # characters', 'landtech-extras-for-elementor' ),
						'description' => __('The hashtag is used for automatically activating this element when it\'s present in the URL.', 'landtech-extras-for-elementor'),
						'type'		=> Controls_Manager::TEXT,
						'default'	=> '',
						'dynamic'	=> [ 'active' => true ],
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
						'label_block' => false,
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 'left',
						'options' 	=> [
							'left' 		=> __( 'Before', 'landtech-extras-for-elementor' ),
							'right' 	=> __( 'After', 'landtech-extras-for-elementor' ),
						],
						'condition' => [
							'icon!' => '',
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
							'icon!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-icon--right' => 'margin-left: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} {{CURRENT_ITEM}} .ee-icon--left' => 'margin-right: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$repeater->add_control(
					'content_type',
					[
						'label'		=> __( 'Type', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::SELECT,
						'default' 	=> 'text',
						'options' 	=> [
							'text' 		=> __( 'Text', 'landtech-extras-for-elementor' ),
							'template' 	=> __( 'Template', 'landtech-extras-for-elementor' ),
						],
					]
				);

				$repeater->add_control(
					'content',
					[
						'label' 	=> __( 'Content', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::WYSIWYG,
						'dynamic'	=> [ 'active' => true ],
						'default' 	=> __( 'I am the content ready to be toggled', 'landtech-extras-for-elementor' ),
						'condition'	=> [
							'content_type' => 'text',
						],
					]
				);

				TemplatesControl::add_controls( $repeater, [
					'condition' => [
						'content_type' => 'template',
					],
					'prefix' => 'content_',
				] );

			$repeater->end_controls_tab();

			$repeater->start_controls_tab( 'element_label', [ 'label' => __( 'Style', 'landtech-extras-for-elementor' ) ] );

				$repeater->add_control(
					'text_color',
					[
						'label' 	=> __( 'Label Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}}.ee-toggle-element__controls__item' => 'color: {{VALUE}};',
						],
					]
				);

				$repeater->add_control(
					'text_active_color',
					[
						'label' 	=> __( 'Active Label Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'default'	=> '',
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}}.ee-toggle-element__controls__item.ee--is-active,
							 {{WRAPPER}} {{CURRENT_ITEM}}.ee-toggle-element__controls__item.ee--is-active:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$repeater->add_control(
					'active_color',
					[
						'label' 	=> __( 'Indicator Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'global' => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
					]
				);

			$repeater->end_controls_tab();

			$repeater->end_controls_tabs();

			$this->add_control(
				'elements',
				[
					'label' 	=> __( 'Elements', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::REPEATER,
					'default' 	=> [
						[
							'text' 	=> '',
							'content' => __( 'I am the content ready to be toggled', 'landtech-extras-for-elementor' ),
						],
						[
							'text' 	=> '',
							'content' => __( 'I am the content of another element ready to be toggled', 'landtech-extras-for-elementor' ),
						],
					],
					'fields' 		=> $repeater->get_controls(),
					'title_field' 	=> '{{{ text }}}',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle',
			[
				'label' => __( 'Toggle', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'toggle_inactive',
				[
					'label' 		=> __( 'Start Hidden', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Don\'t show any of the elements initially.', 'landtech-extras-for-elementor' ),
					'default'		=> '',
					'type' 			=> Controls_Manager::SWITCHER,
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'toggle_active_index',
				[
					'label'			=> __( 'Default Index', 'landtech-extras-for-elementor' ),
					'title'   		=> __( 'The index of the default active element.', 'landtech-extras-for-elementor' ),
					'type'			=> Controls_Manager::NUMBER,
					'default'		=> 1,
					'min'			=> 1,
					'step'			=> 1,
					'frontend_available' => true,
					'condition' 	=> [
						'toggle_inactive' => '',
					],
				]
			);

			$this->add_control(
				'toggle_hash_load',
				[
					'label' 		=> __( 'Load Hash', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'When the page loads, if the hash of an element is present in the URL, that element will be activated.', 'landtech-extras-for-elementor' ),
					'default'		=> '',
					'type' 			=> Controls_Manager::SWITCHER,
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			/*
			// TODO: Hash Navigation control

			$this->add_control(
				'toggle_hash_navigation',
				[
					'label' 		=> __( 'Navigation Hash', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'If clicking on a link to containing any of the hashes, the page will scroll to this widget and toggle the element specified by the hash.', 'landtech-extras-for-elementor' ),
					'default'		=> '',
					'type' 			=> Controls_Manager::SWITCHER,
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);
			*/

			$this->add_control(
				'toggle_hash',
				[
					'label' 		=> __( 'Toggle Hash', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'When toggling, a hashtag will be added to the current URL. If turned off, you can still activate an element by including its hash in the URL.', 'landtech-extras-for-elementor' ),
					'default'		=> '',
					'type' 			=> Controls_Manager::SWITCHER,
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'toggle_position',
				[
					'label'		=> __( 'Position', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'before',
					'options' 	=> [
						'before'  	=> __( 'Before', 'landtech-extras-for-elementor' ),
						'after' 	=> __( 'After', 'landtech-extras-for-elementor' ),
					],
				]
			);

			$this->add_control(
				'indicator_speed',
				[
					'label' 	=> __( 'Indicator Speed', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0.1,
							'max' 	=> 2,
							'step'	=> 0.1,
						],
					],
					'default' 	=> [
						'size' => 0.3
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'toggle_hide_empty',
				[
					'label' 		=> __( 'Hide Empty Items', 'landtech-extras-for-elementor' ),
					'default'		=> '',
					'type' 			=> Controls_Manager::SWITCHER,
					'return_value' 	=> 'yes',
				]
			);

			$this->add_control(
				'refresh_widgets',
				[
					'label' 		=> __( 'Refresh Template Widgets', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'If you are using templates as content for the elements, this option will refresh any frontend functionality for all elements inside those template when toggling. Turn this off if you notice strange behaviour or broken elements inside the template.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'output_faq_schema',
				[
					'label'        => __( 'Output FAQPage JSON-LD', 'landtech-extras-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'return_value' => 'yes',
					'description'  => __( 'Outputs FAQ structured data from toggle labels (questions) and text content (answers). Template-based items are skipped.', 'landtech-extras-for-elementor' ),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_toggler',
			[
				'label' => __( 'Toggler', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'toggle_style',
				[
					'label'		=> __( 'Style', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'round',
					'options' 	=> [
						'round'  => __( 'Round', 'landtech-extras-for-elementor' ),
						'square' => __( 'Square', 'landtech-extras-for-elementor' ),
					],
					'prefix_class' => 'ee-toggle-element--',
				]
			);

			$this->add_responsive_control(
				'toggle_align',
				[
					'label' 		=> __( 'Align', 'landtech-extras-for-elementor' ),
					'label_block'	=> false,
					'type' 			=> Controls_Manager::CHOOSE,
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
					'default' 	=> 'center',
					'selectors' => [
						'{{WRAPPER}} .ee-toggle-element__toggle' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'toggle_zoom',
				[
					'label' 	=> __( 'Zoom', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 16,
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 28,
							'min' 	=> 12,
							'step' 	=> 1,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-toggle-element__controls-wrapper' => 'font-size: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'toggle_spacing',
				[
					'label' 	=> __( 'Distance', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 24,
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 100,
							'min' 	=> 0,
							'step' 	=> 1,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-toggle-element__controls-wrapper--before' => 'margin-bottom: {{SIZE}}px;',
						'{{WRAPPER}} .ee-toggle-element__controls-wrapper--after' => 'margin-top: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'toggle_padding',
				[
					'label' 	=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 6,
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 10,
							'min' 	=> 0,
							'step' 	=> 1,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-toggle-element__indicator' => 'margin: {{SIZE}}px;',
						'{{WRAPPER}} .ee-toggle-element__controls-wrapper' => 'padding: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'toggle_width',
				[
					'label' 	=> __( 'Width (%)', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 100,
							'min' 	=> 0,
							'step' 	=> 1,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-toggle-element__controls-wrapper' => 'width: {{SIZE}}%;',
					],
				]
			);

			$this->add_responsive_control(
				'toggle_radius',
				[
					'label' 	=> __( 'Radius', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 4,
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 10,
							'min' 	=> 0,
							'step' 	=> 1,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}}.ee-toggle-element--square .ee-toggle-element__controls-wrapper' => 'border-radius: {{SIZE}}px;',
						'{{WRAPPER}}.ee-toggle-element--square .ee-toggle-element__indicator' => 'border-radius: calc( {{SIZE}}px - 2px );',
					],
					'condition' => [
						'toggle_style' => 'square',
					]
				]
			);

			$this->add_control(
				'toggle_background',
				[
					'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-toggle-element__controls-wrapper' => 'background-color: {{VALUE}};'
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'toggle',
					'selector' 	=> '{{WRAPPER}} .ee-toggle-element__controls-wrapper',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_indicator',
			[
				'label' => __( 'Indicator', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'indicator_color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'active' => false,
					],
					'frontend_available' => true,
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'indicator',
					'selector' 	=> '{{WRAPPER}} .ee-toggle-element__indicator',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_labels',
			[
				'label' => __( 'Labels', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'labels_info',
				[
					'type' 				=> Controls_Manager::RAW_HTML,
					'raw' 				=> __( 'After adjusting some of these settings, interact with the toggler so that the position of the indicator is updated. ', 'landtech-extras-for-elementor' ),
					'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
				]
			);

			$this->add_control(
				'labels_stack',
				[
					'label'		=> __( 'Stack On', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> '',
					'options' 	=> [
						''  		=> __( 'None', 'landtech-extras-for-elementor' ),
						'desktop'  	=> __( 'All', 'landtech-extras-for-elementor' ),
						'tablet'  	=> __( 'Tablet & Mobile', 'landtech-extras-for-elementor' ),
						'mobile' 	=> __( 'Mobile', 'landtech-extras-for-elementor' ),
					],
					'prefix_class' => 'ee-toggle-element--stack-',
				]
			);

			$this->add_responsive_control(
				'labels_align',
				[
					'label' 		=> __( 'Inline Align', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Label alignment only works if you set a custom width for the toggler.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'options' 		=> [
						'start'    => [
							'title' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-center',
						],
						'end' 		=> [
							'title' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-right',
						],
						'stretch' 		=> [
							'title' 	=> __( 'Justify', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-stretch',
						],
					],
					'default' 		=> 'center',
					'prefix_class' 	=> 'ee-labels-align%s--',
				]
			);

			$this->add_responsive_control(
				'stacked_labels_align',
				[
					'label' 		=> __( 'Stacked Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'options' 		=> [
						'start'    => [
							'title' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-center',
						],
						'end' 		=> [
							'title' 	=> __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-right',
						],
						'stretch' 		=> [
							'title' 	=> __( 'Justify', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'eicon-h-align-stretch',
						],
					],
					'default' 		=> 'center',
					'prefix_class' 	=> 'ee-labels-align-stacked%s--',
				]
			);

			$this->add_responsive_control(
				'text_align',
				[
					'label' 		=> __( 'Align Label Text', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Label text alignment only works if your labels have text.', 'landtech-extras-for-elementor' ),
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
						'{{WRAPPER}} .ee-toggle-element__controls__item' => 'text-align: {{VALUE}};',
					]
				]
			);

			$this->add_control(
				'labels_padding',
				[
					'label' 	=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 2,
							'min' 	=> 0,
							'step' 	=> 0.1,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-toggle-element__controls__item' => 'padding: {{SIZE}}em;',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'labels_typography',
					'selector' 	=> '{{WRAPPER}} .ee-toggle-element__controls__item',
					'exclude'	=> ['font_size', 'line_height'], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control schema.
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'labels',
					'selector' 		=> '{{WRAPPER}} .ee-toggle-element__controls__item',
				]
			);

			$this->start_controls_tabs( 'labels_style' );

			$this->start_controls_tab( 'labels_style_default', [ 'label' => __( 'Default', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'labels_color',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-toggle-element__controls__item' => 'color: {{VALUE}};'
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'labels_style_hover', [ 'label' => __( 'Hover', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'labels_color_hover',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-toggle-element__controls__item:hover' => 'color: {{VALUE}};'
						],
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab( 'labels_style_active', [ 'label' => __( 'Active', 'landtech-extras-for-elementor' ) ] );

				$this->add_control(
					'labels_color_active',
					[
						'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ee-toggle-element__controls__item.ee--is-active,
							 {{WRAPPER}} .ee-toggle-element__controls__item.ee--is-active:hover' => 'color: {{VALUE}};'
						],
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'content',
					'selector' 	=> '{{WRAPPER}} .ee-toggle-element__element',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

			$this->add_control(
				'content_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-toggle-element__element' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'content_margin',
				[
					'label' 		=> __( 'Margin', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-toggle-element__element' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'content',
					'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '{{WRAPPER}} .ee-toggle-element__element',
				]
			);

			$this->add_responsive_control(
				'content_border_radius',
				[
					'label' 	=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 10,
							'min' 	=> 0,
							'step' 	=> 1,
						],
					],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-toggle-element__element' => 'border-radius: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'content_foreground',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'separator' => 'before',
					'selectors'	=> [
						'{{WRAPPER}} .ee-toggle-element__element' => 'color: {{VALUE}};'
					]
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' 		=> 'content_background',
					'selector' 	=> '{{WRAPPER}} .ee-toggle-element__element',
					'types' 	=> [ 'classic', 'gradient' ],
					'default'	=> 'classic',
				]
			);

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
	public function render() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => [
					'ee-toggle-element',
				],
			],
			'toggle' => [
				'class' => [
					'ee-toggle-element__toggle',
				],
			],
			'controls-wrapper' => [
				'class' => [
					'ee-toggle-element__controls-wrapper',
					'ee-toggle-element__controls-wrapper--' . $settings['toggle_position'],
				],
			],
			'indicator' => [
				'class' => [
					'ee-toggle-element__indicator',
				],
			],
			'controls' => [
				'class' => [
					'ee-toggle-element__controls',
				],
			],
			'elements' => [
				'class' => [
					'ee-toggle-element__elements',
				],
			],
		] );

		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div <?php $this->print_render_attribute_string( 'toggle' ); ?>>
				<?php if ( 'before' === $settings['toggle_position'] ) $this->render_toggle(); ?>
				<div <?php $this->print_render_attribute_string( 'elements' ); ?>>
					<?php foreach ( $settings['elements'] as $index => $item ) {

						if ( 'yes' === $settings['toggle_hide_empty'] ) {
							if ( ! $this->has_item_content( $item ) ) {
								continue;
							}
						}

						$element_key = $this->get_repeater_setting_key( 'element', 'elements', $index );

						$this->add_render_attribute( $element_key, [
							'class' => [
								'ee-toggle-element__element',
								'elementor-repeater-item-' . $item['_id'],
							]
						] );

						?><div <?php $this->print_render_attribute_string( $element_key ); ?>><?php

						switch ( $item['content_type'] ) {
							case 'text':
								$this->render_text( $index, $item );
								break;
							case 'template':
								$template_key = 'content_' . $item['content_template_type'] . '_template_id';
								if ( array_key_exists( $template_key, $item ) )
									TemplatesControl::render_template_content( $item[ $template_key ], $this );
								break;
							default:
								break;
						}

						?></div><?php
					} ?>
				</div>
				<?php if ( 'after' === $settings['toggle_position'] ) $this->render_toggle(); ?>
			</div>
		</div>
		<?php

		$this->maybe_render_faq_json_ld();

	}

	/**
	 * Output FAQPage JSON-LD when enabled.
	 *
	 * @since 2.2.102
	 * @return void
	 */
	protected function maybe_render_faq_json_ld() {
		$settings = $this->get_settings_for_display();

		if ( 'yes' !== ( $settings['output_faq_schema'] ?? '' ) ) {
			return;
		}

		$elements = isset( $settings['elements'] ) && is_array( $settings['elements'] )
			? $settings['elements']
			: array();

		$faq_rows = array();

		foreach ( $elements as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			if ( 'yes' === ( $settings['toggle_hide_empty'] ?? '' ) && ! $this->has_item_content( $item ) ) {
				continue;
			}

			if ( 'text' !== ( $item['content_type'] ?? 'text' ) ) {
				continue;
			}

			$question = isset( $item['text'] ) ? (string) $item['text'] : '';
			$answer   = isset( $item['content'] ) ? (string) $item['content'] : '';

			if ( '' === trim( $question ) || '' === trim( wp_strip_all_tags( $answer ) ) ) {
				continue;
			}

			$faq_rows[] = array(
				'question' => $question,
				'answer'   => $answer,
			);
		}

		if ( empty( $faq_rows ) ) {
			return;
		}

		$schema = Schema_Builder::build_faq_page( $faq_rows );
		$result = Schema_Validator::validate( 'FAQPage', $schema );

		if ( empty( $result['valid'] ) ) {
			return;
		}

		Schema_Builder::print_json_ld(
			$schema,
			'ee-toggle-element-jsonld-' . sanitize_html_class( (string) $this->get_id() )
		);
	}

	/**
	 * Has Item Content
	 *
	 * @since  2.2.5
	 * @param  $item. array  The item to check
	 * @return void
	 */
	protected function has_item_content( $item ) {
		$settings = $this->get_settings_for_display();
		$has_content = false;

		switch ( $item['content_type'] ) {
			case 'text':
				if ( trim($item['content']) ) {
					$has_content = true;
				}
				break;
			case 'template':
				$template_id = $item[ 'content_' . $item['content_template_type'] . '_template_id' ];
				if ( '0' !== $template_id && false !== get_post_status( $template_id ) ) {
					$has_content = true;
				}
				break;
			default:
				break;
		}

		return $has_content;
	}

	/**
	 * Render Toggle Control
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_toggle() {
		$settings = $this->get_settings_for_display();

		?><div <?php $this->print_render_attribute_string( 'controls-wrapper' ); ?>>
			<div <?php $this->print_render_attribute_string( 'indicator' ); ?>></div><?php

			if ( $settings['elements'] ) {

			?><ul <?php $this->print_render_attribute_string( 'controls' ); ?>><?php
				foreach ( $settings['elements'] as $index => $item ) {
					if ( 'yes' === $settings['toggle_hide_empty'] ) {
						if ( ! $this->has_item_content( $item ) ) {
							continue;
						}
					}

					$control_key = $this->get_repeater_setting_key( 'control', 'elements', $index );
					$control_text_key = $this->get_repeater_setting_key( 'control-text', 'elements', $index );

					$has_icon = ! empty( $item['icon'] ) || ! empty( $item['selected_icon']['value'] );

					$this->add_render_attribute( [
						$control_key => [
							'class' => [
								'ee-toggle-element__controls__item',
								'elementor-repeater-item-' . $item['_id'],
							]
						],
						$control_text_key => [
							'class' => 'ee-toggle-element__controls__text',
							'unselectable' => 'on',
						],
					] );

					if ( '' !== $item['hash'] ) {
						$hash = $item['hash'];
					} else {
						$hash = $item['_id'];
					}

					$this->add_render_attribute( $control_key, 'data-hash', $hash );

					if ( '' !== $item['active_color'] ) {
						$this->add_render_attribute( $control_key, 'data-color', $item['active_color'] ); }

					if ( ! empty( $item['text'] ) ) {
						$this->add_render_attribute( $control_key, 'class', 'ee--is-empty' ); }

					?><li <?php $this->print_render_attribute_string( $control_key ); ?>><?php

						if ( $has_icon ) {
							$this->render_toggle_item_icon( $index, $item ); }

						if ( ! empty( $item['text'] ) && ! $has_icon ) {
							?><span <?php $this->print_render_attribute_string( $control_text_key ); ?>><?php }

						if ( ! empty( $item['text'] ) ) { echo esc_html( $item['text'] ); } else if ( ! $has_icon ) { echo '&nbsp;'; }

						if ( ! empty( $item['text'] ) && ! $has_icon ) {
							?></span><?php }

					?></li><?php
				}
			?></ul><?php
			}
		?></div><?php
	}

	/**
	 * Render Toggle Item Icon
	 *
	 * @since  2.1.5
	 * @return void
	 */
	protected function render_toggle_item_icon( $index, $item ) {

		$icon_key 	= $this->get_repeater_setting_key( 'icon', 'elements', $index );
		$migrated 	= isset( $item['__fa4_migrated']['selected_icon'] );
		$is_new 	= empty( $item['icon'] ) && Icons_Manager::is_migration_allowed();

		$this->add_render_attribute( $icon_key, 'class', [
			'ee-toggle-element__controls__icon',
			'ee-icon',
			'ee-icon-support--svg',
			'ee-icon--' . $item['icon_align'],
		] );

		if ( '' === $item['text'] ) {
			$this->add_render_attribute( $icon_key, 'class', [
				'ee-icon--flush',
			] );
		}

		?><span <?php $this->print_render_attribute_string( $icon_key ); ?>><?php
			if ( $is_new || $migrated ) {
				Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] );
			} else {
				?><i class="<?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></i><?php
			}
		?></span><?php
	}

	/**
	 * Render Text
	 * 
	 * Renders the WYSIWYG content
	 *
	 * @since  2.0.0
	 * @return void
	 */
	protected function render_text( $index, $item ) {
		echo wp_kses_post( $this->parse_text_editor( $item['content'] ) );
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function content_template() {}

}