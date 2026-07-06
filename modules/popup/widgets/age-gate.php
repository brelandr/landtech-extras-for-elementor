<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Popup\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\Utils;
use LandTechExtras\Group_Control_Transition;
use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\Popup\Skins;
use LandTechExtras\Modules\Popup\Module as Module;

// Elementor Classes
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Age_Gate
 *
 * @since 2.0.0
 */
class Age_Gate extends Extras_Widget {

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
		return 'ee-age-gate';
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
		return __( 'Age Gate', 'landtech-extras-for-elementor' );
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
		return 'nicon nicon-age-gate';
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
			'landtech-extras-glightbox',
		];
	}

	/**
	 * Get Style Depends
	 * 
	 * A list of css files that the widgets is depended in
	 *
	 * @since  2.0.0
	 * @return array
	 */
	public function get_style_depends() {
		return [
			'landtech-extras-glightbox',
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
			'section_settings',
			[
				'label' => __( 'Settings', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'popup_open',
				[
					'label' 		=> __( 'Show Popup in Editor', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			if ( current_user_can( 'administrator' ) ) {
				$this->add_control(
					'popup_open_admin',
					[
						'label' 		=> __( 'Always Show for Admins', 'landtech-extras-for-elementor' ),
						'description' 	=> __( 'Have the popup open every time you visit the page if you\'re an Admin. This will help you test the functionality on the frontend without actually being granted access and losing the popup once you enter a correct age. Turn it off when you\'re done customising this widget.', 'landtech-extras-for-elementor' ),	
						'type' 			=> Controls_Manager::SWITCHER,
						'default' 		=> 'yes',
						'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
						'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
						'frontend_available' => true,
					]
				);
			}

			$this->add_control(
				'popup_animation',
				[
					'label' 	=> __( 'Animation', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default'	=> 'zoom-in',
					'options' 	=> Module::get_animation_options(),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'age',
				[
					'label'			=> __( 'Required Age', 'landtech-extras-for-elementor' ),
					'type'			=> Controls_Manager::NUMBER,
					'separator' 	=> 'before',
					'default'		=> 18,
					'min'			=> 10,
					'step'			=> 1,
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_form',
			[
				'label' => __( 'Form', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'hide_form_on_denied',
				[
					'label' 		=> __( 'Hide If Denied', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'If access is denied, remove entire form and header and just show the denied message.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'clear_form_on_denied',
				[
					'label' 		=> __( 'Clear On Submit', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
					'condition'		=> [
						'hide_form_on_denied!' => 'yes'
					],
				]
			);

			$this->add_control(
				'denied',
				[
					'label' 		=> __( 'Access Denied', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Override the default Access Denied message.', 'landtech-extras-for-elementor' ),
					'title'			=>
					/* translators: %s: Placeholder for the minimum age in user-facing messages. */
					__( 'Use %s to display the required age.', 'landtech-extras-for-elementor' ),
					'dynamic'		=> [ 'active' => true ],
					'type' 			=> Controls_Manager::TEXT,
					/* translators: %s: Minimum age number placeholder in the access-denied message. */
					'default'		=> __( 'Sorry, you must be at least %s to access this website.', 'landtech-extras-for-elementor' ),
					/* translators: %s: Minimum age number placeholder in the access-denied message. */
					'placeholder'	=> __( 'Sorry, you must be at least %s to access this website.', 'landtech-extras-for-elementor' ),
				]
			);

			$this->add_control(
				'header_heading',
				[
					'label' 	=> __( 'Header', 'landtech-extras-for-elementor' ),
					'separator' => 'before',
					'type' 		=> Controls_Manager::HEADING,
				]
			);

			$this->add_control(
				'title',
				[
					'label' 	=> __( 'Title', 'landtech-extras-for-elementor' ),
					'dynamic'	=> [ 'active' => true ],
					'type' 		=> Controls_Manager::TEXT,
					'default'	=> __( 'What\'s your age?', 'landtech-extras-for-elementor' ),
				]
			);

			$this->add_control(
				'title_tag',
				[
					'label' 	=> __( 'Title HTML Tag', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'options' 	=> [
						'h1' 	=> __( 'H1', 'landtech-extras-for-elementor' ),
						'h2' 	=> __( 'H2', 'landtech-extras-for-elementor' ),
						'h3' 	=> __( 'H3', 'landtech-extras-for-elementor' ),
						'h4' 	=> __( 'H4', 'landtech-extras-for-elementor' ),
						'h5' 	=> __( 'H5', 'landtech-extras-for-elementor' ),
						'h6' 	=> __( 'H6', 'landtech-extras-for-elementor' ),
						'div' 	=> __( 'div', 'landtech-extras-for-elementor' ),
						'span' 	=> __( 'span', 'landtech-extras-for-elementor' ),
						'p' 	=> __( 'p', 'landtech-extras-for-elementor' ),
					],
					'default' => 'h1',
				]
			);

			$this->add_control(
				'description',
				[
					'label' 	=> __( 'Description', 'landtech-extras-for-elementor' ),
					'dynamic'	=> [ 'active' => true ],
					'type' 		=> Controls_Manager::TEXT,
					'default'	=> __( 'Let\'s find out if we can let you in', 'landtech-extras-for-elementor' ),
				]
			);

			$this->add_control(
				'input_heading',
				[
					'label' 	=> __( 'Input', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'input_size',
				[
					'label' => __( 'Input Size', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'xs' => __( 'Extra Small', 'landtech-extras-for-elementor' ),
						'sm' => __( 'Small', 'landtech-extras-for-elementor' ),
						'md' => __( 'Medium', 'landtech-extras-for-elementor' ),
						'lg' => __( 'Large', 'landtech-extras-for-elementor' ),
						'xl' => __( 'Extra Large', 'landtech-extras-for-elementor' ),
					],
					'default' => 'sm',
				]
			);

			$this->add_responsive_control(
				'input_width',
				[
					'label' => __( 'Width', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'' => __( 'Default', 'landtech-extras-for-elementor' ),
						'100' => '100%',
						'80' => '80%',
						'75' => '75%',
						'66' => '66%',
						'60' => '60%',
						'50' => '50%',
						'40' => '40%',
						'33' => '33%',
						'25' => '25%',
						'20' => '20%',
					],
					'default' => '100',
				]
			);

			$this->add_control(
				'button_heading',
				[
					'label' 	=> __( 'Button', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'button_text',
				[
					'label' => __( 'Text', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => __( 'Let me in', 'landtech-extras-for-elementor' ),
				]
			);

			$this->add_control(
				'button_size',
				[
					'label' => __( 'Size', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'sm',
					'options' => Utils::get_button_sizes(),
				]
			);

			$this->add_responsive_control(
				'button_width',
				[
					'label' => __( 'Column Width', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'' => __( 'Default', 'landtech-extras-for-elementor' ),
						'100' => '100%',
						'80' => '80%',
						'75' => '75%',
						'66' => '66%',
						'60' => '60%',
						'50' => '50%',
						'40' => '40%',
						'33' => '33%',
						'25' => '25%',
						'20' => '20%',
					],
					'default' => '100',
				]
			);

			$this->add_control(
				'button_align',
				[
					'label' => __( 'Alignment', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'start' => [
							'title' => __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' => 'fa fa-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' => 'fa fa-align-center',
						],
						'end' => [
							'title' => __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' => 'fa fa-align-right',
						],
						'stretch' => [
							'title' => __( 'Justified', 'landtech-extras-for-elementor' ),
							'icon' => 'fa fa-align-justify',
						],
					],
					'default' => 'stretch',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'selected_button_icon',
				[
					'label' => __( 'Icon', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'button_icon',
				]
			);

			$this->add_control(
				'button_icon_align',
				[
					'label' => __( 'Icon Position', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'left',
					'options' => [
						'left' => __( 'Before', 'landtech-extras-for-elementor' ),
						'right' => __( 'After', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						'selected_button_icon[value]!' => '',
					],
				]
			);

			$this->add_control(
				'button_icon_indent',
				[
					'label' => __( 'Icon Spacing', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'max' => 50,
						],
					],
					'condition' => [
						'selected_button_icon[value]!' => '',
					],
					'selectors' => [
						'.mfp-wrap.ee-mfp-popup-{{ID}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
						'.mfp-wrap.ee-mfp-popup-{{ID}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'button_css_id',
				[
					'label' => __( 'Button ID', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'title' => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'landtech-extras-for-elementor' ),
					'label_block' => false,
					'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'landtech-extras-for-elementor' ),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_popup',
			[
				'label' => __( 'Popup', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'popup_valign',
				[
					'label' 		=> __( 'Vertical Placement', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> 'middle',
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
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'popup_width',
				[
					'label' 		=> __( 'Max. Width', 'landtech-extras-for-elementor' ),
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
						'.mfp-wrap.ee-mfp-popup-{{ID}} .mfp-content' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'popup_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'range' 		=> [
						'px' 		=> [
							'min' => 0,
							'max' => 10,
						],
					],
					'frontend_available' => true,
					'selectors' 	=> [
						'.mfp-wrap.ee-mfp-popup-{{ID}} .ee-age-gate__content' => 'border-radius: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'popup_background',
				[
					'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-age-gate__content' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'popup_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-age-gate__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'popup_border',
					'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-age-gate__content,
									.ee-mfp-popup-{{ID}} .mfp-figure',
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'popup_box_shadow',
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-age-gate__content,
									.ee-mfp-popup-{{ID}} .mfp-figure',
					'separator'	=> '',
				]
			);

			$this->add_control(
				'popup_overlay_heading',
				[
					'label' 	=> __( 'Overlay', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'popup_overlay_background',
				[
					'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.mfp-bg.ee-mfp-popup-{{ID}}' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'popup_overlay_opacity',
				[
					'label' 	=> __( 'Opacity (%)', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> 0.8,
					],
					'range' 	=> [
						'px' 	=> [
							'max' 	=> 1,
							'min' 	=> 0,
							'step' 	=> 0.01,
						],
					],
					'selectors' => [
						'.mfp-bg.ee-mfp-popup.mfp-ready:not(.mfp-removing).ee-mfp-popup-{{ID}}' => 'opacity: {{SIZE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				[
					'name' => 'popup_overlay_filter',
					'selector' => '.mfp-bg.ee-mfp-popup-{{ID}}',
				]
			);

			$this->add_control(
				'popup_overlay_blend',
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
						'.mfp-bg.ee-mfp-popup-{{ID}}' => 'mix-blend-mode: {{VALUE}};',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_header',
			[
				'label' => __( 'Header', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'header_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-age-gate__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'header_background',
				[
					'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-age-gate__header' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'header_border',
					'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-age-gate__header',
				]
			);

			$this->add_control(
				'title_style_heading',
				[
					'label' 	=> __( 'Title', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'title_align',
				[
					'label' 		=> __( 'Text Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'left' 			=> [
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
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-age-gate__header__title' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 		=> 'title_typography',
					'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-age-gate__header__title',
				]
			);

			$this->add_control(
				'title_color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-age-gate__header__title' => 'color: {{VALUE}};',
					],
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
						'.ee-mfp-popup-{{ID}} .ee-age-gate__header__title' => 'margin-bottom: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'description_heading',
				[
					'label' 	=> __( 'Description', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'description_align',
				[
					'label' 		=> __( 'Text Align', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'left' 			=> [
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
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-age-gate__header__description' => 'text-align: {{VALUE}};',
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
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-age-gate__header__description',
				]
			);

			$this->add_control(
				'description_color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-age-gate__header__description' => 'color: {{VALUE}};',
					],
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
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-age-gate__header__description' => 'margin-bottom: {{SIZE}}px;',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_form',
			[
				'label' => __( 'Form', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'content_padding',
				[
					'label' 		=> __( 'Padding', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::DIMENSIONS,
					'size_units' 	=> [ 'px', 'em', '%' ],
					'selectors' 	=> [
						'.ee-mfp-popup-{{ID}} .ee-age-gate__content__body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' 		=> 'content_border',
					'label' 	=> __( 'Border', 'landtech-extras-for-elementor' ),
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-age-gate__content,
									.ee-mfp-popup-{{ID}} .mfp-figure',
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' 		=> 'content_box_shadow',
					'selector' 	=> '.ee-mfp-popup-{{ID}} .ee-age-gate__content,
									.ee-mfp-popup-{{ID}} .mfp-figure',
					'separator'	=> '',
				]
			);

			$this->add_control(
				'column_gap',
				[
					'label' => __( 'Columns Gap', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 10,
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 60,
						],
					],
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-field-group' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
						'.ee-mfp-popup-{{ID}} .elementor-form-fields-wrapper' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
					],
				]
			);

			$this->add_control(
				'row_gap',
				[
					'label' => __( 'Rows Gap', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 10,
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 60,
						],
					],
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-field-group,
						 .ee-mfp-popup-{{ID}} .ee-notification:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						'.ee-mfp-popup-{{ID}} .elementor-form-fields-wrapper' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'form_background',
				[
					'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'default'	=> '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-age-gate__content__body' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'input_style_heading',
				[
					'label' 	=> __( 'Input', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'input_text_color',
				[
					'label' => __( 'Text Color', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-field' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'input_background_color',
				[
					'label' => __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#ffffff',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-field' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'input_border_width',
				[
					'label' => __( 'Border Width', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'placeholder' => '1',
					'size_units' => [ 'px' ],
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-field' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'input_border_radius',
				[
					'label' => __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'input_border_color',
				[
					'label' => __( 'Border Color', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-field' => 'border-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'input_typography',
					'selector' => '.ee-mfp-popup-{{ID}} .elementor-field, .ee-mfp-popup-{{ID}} .elementor-field-subgroup label',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
				]
			);

			$this->add_control(
				'button_style_heading',
				[
					'label' 	=> __( 'Button', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'button_typography',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_TEXT,
					],
					'selector' => '.ee-mfp-popup-{{ID}} .elementor-button',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(), [
					'name' => 'button_border',
					'placeholder' => '1px',
					'default' => '1px',
					'selector' => '.ee-mfp-popup-{{ID}} .elementor-button',
				]
			);

			$this->add_control(
				'button_border_radius',
				[
					'label' => __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'button_text_padding',
				[
					'label' => __( 'Text Padding', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->start_controls_tabs( 'tabs_button_style' );

			$this->start_controls_tab(
				'tab_button_normal',
				[
					'label' => __( 'Normal', 'landtech-extras-for-elementor' ),
				]
			);

			$this->add_control(
				'button_background_color',
				[
					'label' => __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-button' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_text_color',
				[
					'label' => __( 'Text Color', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-button' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_button_hover',
				[
					'label' => __( 'Hover', 'landtech-extras-for-elementor' ),
				]
			);

			$this->add_control(
				'button_background_hover_color',
				[
					'label' => __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-button:hover' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_hover_color',
				[
					'label' => __( 'Text Color', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-button:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_hover_border_color',
				[
					'label' => __( 'Border Color', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .elementor-button:hover' => 'border-color: {{VALUE}};',
					],
					'condition' => [
						'button_border_border!' => '',
					],
				]
			);

			$this->add_control(
				'button_hover_animation',
				[
					'label' => __( 'Animation', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::HOVER_ANIMATION,
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'denied_style_heading',
				[
					'label' 	=> __( 'Denied Message', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'denied_typography',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_ACCENT,
					],
					'selector' => '.ee-mfp-popup-{{ID}} .ee-notification--error',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(), [
					'name' => 'denied_border',
					'placeholder' => '1px',
					'default' => '1px',
					'selector' => '.ee-mfp-popup-{{ID}} .ee-notification--error',
				]
			);

			$this->add_control(
				'denied_border_radius',
				[
					'label' => __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-notification--error' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'denied_text_padding',
				[
					'label' => __( 'Text Padding', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-notification--error' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'denied_background_color',
				[
					'label' => __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-notification--error' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'denied_text_color',
				[
					'label' => __( 'Text Color', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'.ee-mfp-popup-{{ID}} .ee-notification--error' => 'color: {{VALUE}};',
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
	 * @since  2.0.0
	 * @return void
	 */
	public function render() {

		$settings = $this->get_settings_for_display();

		$content_link = '#ee-age-gate__trigger-' . $this->get_id();

		$this->add_render_attribute( [
			'wrapper' 	=> [
				'class' => [
					'ee-age-gate',
					'ee-popup',
				],
				'id'	=> 'popup-' . $this->get_id(),
			],
			'trigger' 	=> [
				'class' => [
					'ee-age-gate__trigger',
					'ee-popup__trigger',
				],
				'href'	=> $content_link,
			],
			'content' 	=> [
				'id'	=> 'ee-age-gate__trigger-' . $this->get_id(),
				'class' => [
					'ee-popup__content',
					'ee-age-gate__content',
					'ee-age-gate-' . $this->get_id(),
					'zoom-anim-dialog',
					'mfp-hide glightbox-hide',
				],
			],
		] );

		if ( '' !== $settings['popup_animation'] ) {
			$this->add_render_attribute( 'content', 'class', 'mfp-with-anim' );
		}

		?><div <?php $this->print_render_attribute_string( 'wrapper' ) ; ?>>

			<?php $this->render_placeholder( [
				'body' => __( 'Make sure you place this widget in an Elementor template used on all pages such as the header or footer.', 'landtech-extras-for-elementor' ),
			] ); ?>

			<a <?php $this->print_render_attribute_string( 'trigger' ) ; ?>></a>

			<div <?php $this->print_render_attribute_string( 'content' ) ; ?>>
				<?php $this->render_header(); ?>
				<?php $this->render_body(); ?>
			</div>
		</div><?php

	}

	/**
	 * Render Header
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_header() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['title'] ) && empty( $settings['description'] ) )
			return;

		$this->add_render_attribute( 'header', 'class', [
			'ee-age-gate__header',
			'ee-popup__header',
		] );

		?><div <?php $this->print_render_attribute_string( 'header' ) ; ?>>
			<?php $this->render_title(); ?>
			<?php $this->render_description(); ?>
		</div><?php
	}

	/**
	 * Render Title
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_title() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['title'] ) )
			return;

		$title_tag = $settings['title_tag'];

		$this->add_render_attribute( 'title', 'class', [
			'ee-age-gate__header__title',
			'ee-popup__header__title',
		] );

		?><<?php echo esc_html( $this->ltxe_sanitize_heading_tag( $title_tag ) ); ?> <?php $this->print_render_attribute_string( 'title' ) ; ?>>
			<?php echo wp_kses_post( $settings['title'] ); ?>
		</<?php echo esc_html( $this->ltxe_sanitize_heading_tag( $title_tag ) ); ?>><?php
	}

	/**
	 * Render Description
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_description() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['description'] ) )
			return;

		$this->add_render_attribute( 'description', 'class', [
			'ee-age-gate__header__description',
			'ee-popup__header__description',
		] );

		?><div <?php $this->print_render_attribute_string( 'description' ) ; ?>>
			<?php echo wp_kses_post( $settings['description'] ); ?>
		</div><?php
	}

	/**
	 * Render Body
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_body() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'content-body' => [
				'class' => [
					'ee-age-gate__content__body',
					'ee-popup__content__body',
				],
			],
			'form' => [
				'class' => [
					'elementor-form',
					'ee-form',
					'ee-form--age-gate',
					'elementor-button-align-' . $settings['button_align'],
				],
			],
			'form-fields' => [
				'class' => [
					'elementor-form-fields-wrapper',
				],
			],
			'form-field-age' => [
				'required' => 'required',
				'class' => [
					'elementor-field-type-number',
					'elementor-field-group',
					'elementor-column',
					'elementor-col-' . $settings['input_width'],
					'elementor-field-group-name',
					'ee-form__field',
				],
			],
			'form-field-button' => [
				'class' => [
					'elementor-field-type-submit',
					'elementor-field-group',
					'elementor-column',
					'elementor-col-' . $settings['button_width'],
					'elementor-field-group-name',
					'ee-form__field',
				],
			],
			'field-age' => [
				'class' => [
					'elementor-field',
					'elementor-size-' . $settings['input_size'],
					'elementor-field-textual',
					'ee-age-gate__form__age',
					'ee-form__field__control',
				],
				'type' => 'number',
				'step' => '1',
				'name' => 'ee-age-gate-age',
			],
			'field-submit' => [
				'class' => [
					'elementor-button',
					'elementor-size-' . $settings['button_size'],
					'ee-form__field__control',
					'ee-age-gate__form__submit',
				],
				'type' => 'submit',
			],
			'field-submit-icon' => [
				'class' => [
					empty( $settings['button_icon_align'] ) ? '' : 'elementor-align-icon-' . $settings['button_icon_align'],
					'ee-icon',
					'ee-icon-support--svg',
					'elementor-button-icon',
				],
			],
			'field-submit-text' => [
				'class' => [
					'elementor-button-text',
				],
			],
		] );

		if ( '' !== $settings['button_css_id'] ) {
			$this->add_render_attribute( 'field-submit', 'id', $settings['button_css_id'] );
		}

		$migrated = isset( $settings['__fa4_migrated']['selected_button_icon'] );
		$is_new = empty( $settings['button_icon'] ) && Icons_Manager::is_migration_allowed();

		?><div <?php $this->print_render_attribute_string( 'content-body' ) ; ?>><?php
			$this->render_denied();
			?><form <?php $this->print_render_attribute_string( 'form' ) ; ?>>
				<div <?php $this->print_render_attribute_string( 'form-fields' ) ; ?>>
					<div <?php $this->print_render_attribute_string( 'form-field-age' ) ; ?>>
						<input <?php $this->print_render_attribute_string( 'field-age' ) ; ?> />
					</div>
					<div <?php $this->print_render_attribute_string( 'form-field-button' ) ; ?>>
						<button <?php $this->print_render_attribute_string( 'field-submit' ) ; ?>>
							<span><?php
								if ( ! empty( $settings['button_icon'] ) || ! empty( $settings['selected_button_icon']['value'] ) ) {
									?><span <?php $this->print_render_attribute_string( 'field-submit-icon' ); ?>><?php
										if ( $is_new || $migrated ) {
											Icons_Manager::render_icon( $settings['selected_button_icon'], [ 'aria-hidden' => 'true' ] );
										} else {
											?><i class="<?php echo esc_attr( $settings['button_icon'] ); ?>" aria-hidden="true"></i><?php
										}
									?></span><?php
								}
								?><span <?php $this->print_render_attribute_string( 'field-submit-text' ) ; ?>><?php
									echo esc_html( $settings['button_text'] );
								?></span>
							</span>
						</button>
					</div>
				</div>
			</form>
		</div><?php
	}

	/**
	 * Render Denied Message
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function render_denied() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'denied', 'class', [
			'ee-notification',
			'ee-notification--error',
		] );

		?><div <?php $this->print_render_attribute_string( 'denied' ) ; ?>>
			<?php echo wp_kses_post( str_replace( '%s', esc_html( (string) $settings['age'] ), $settings['denied'] ) ); ?>
		</div><?php
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