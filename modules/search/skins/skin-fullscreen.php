<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Search\Skins;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\Search\Skins
 *
 * @since  2.1.0
 */
class Skin_Fullscreen extends Skin_Base {

	/**
	 * Get ID
	 * 
	 * Gets the current skin ID
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_id() {
		return 'fullscreen';
	}

	/**
	 * Get Title
	 * 
	 * Gets the current skin title
	 *
	 * @since  2.1.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Full Screen', 'landtech-extras-for-elementor' );
	}

	/**
	 * Register Controls Actions
	 * 
	 * Registers controls at specific points in the Controls Stack
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function _register_controls_actions() {
		parent::_register_controls_actions();

		add_action( 'elementor/element/ee-search-form/section_button/before_section_end', [ $this, 'register_content_controls' ] );
		add_action( 'elementor/element/ee-search-form/section_input_style/before_section_end', [ $this, 'register_style_controls' ] );

		add_action( 'elementor/element/ee-search-form/section_form_style/after_section_end', [ $this, 'register_overlay_style_controls' ] );
	}

	/**
	 * Register Content Controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_content_controls() {
		$this->parent->start_injection( [
			'at' => 'after',
			'of' => '_skin',
		] );

			$this->add_control(
				'toggle_effect',
				[
					'label' 	=> __( 'Toggle Effect', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'slide-down',
					'options' 	=> [
						'zoom' 			=> __( 'Zoom', 'landtech-extras-for-elementor' ),
						'slide-down' 	=> __( 'Slide Down', 'landtech-extras-for-elementor' ),
						'slide-left' 	=> __( 'Slide Left', 'landtech-extras-for-elementor' ),
						'slide-up' 		=> __( 'Slide Up', 'landtech-extras-for-elementor' ),
						'slide-right' 	=> __( 'Slide Right', 'landtech-extras-for-elementor' ),
					],
					'prefix_class' => 'ee-search-form-toggle-effect--',
				]
			);

		$this->parent->end_injection();

		$this->register_button_content_controls();

		parent::register_content_controls();
	}

	/**
	 * Register Button Content Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_button_content_controls() {

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => 'heading_icon_content',
		] );

			$this->add_control(
				'icon',
				[
					'label' => __( 'Icon', 'landtech-extras-for-elementor' ),
					'type' => \Elementor\Controls_Manager::HIDDEN,
					'default' => 'search',
				]
			);

		$this->parent->end_injection();

	}

	/**
	 * Register Style Controls
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function register_style_controls() {
		$this->register_form_style_controls();
		$this->register_input_style_controls();
		$this->register_button_style_controls();
	}

	/**
	 * Register Form Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	protected function register_form_style_controls() {

		$this->parent->start_injection( [
			'at' => 'after',
			'of' => 'form_border_color',
		] );

			$this->add_responsive_control(
				'form_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'For perfectly rounded corners set this to half of the height', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 50,
						],
					],
					'selectors'	=> [
						'{{WRAPPER}} .ee-search-form__container,
						 {{WRAPPER}} .ee-search-form__filters .ee-form__field__control--text,
						 {{WRAPPER}} .ee-search-form__submit' => 'border-radius: {{SIZE}}px;',
						 '{{WRAPPER}} .ee-search-form__filters .select2-container--open.select2-container--below .ee-form__field__control--select2,
						.ee-select2__dropdown--{{ID}}.select2-dropdown--above' => 'border-radius: {{SIZE}}px {{SIZE}}px 0 0',
						'{{WRAPPER}} .ee-search-form__filters .select2-container--open.select2-container--above .ee-form__field__control--select2,
						.ee-select2__dropdown--{{ID}}.select2-dropdown--below' => 'border-radius: 0 0 {{SIZE}}px {{SIZE}}px',
					],
					'condition' => [
						'collapse_spacing!' => '',
					],
				]
			);

		$this->parent->end_injection();
	}

	/**
	 * Register Button Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_overlay_style_controls() {

		$this->start_controls_section(
			'section_overlay_style',
			[
				'label' => __( 'Overlay', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'overlay_distance',
				[
					'label' 		=> __( 'Distance', 'landtech-extras-for-elementor' ),
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
					'selectors'	=> [
						'{{WRAPPER}} .ee-search-form__overlay' => 'top: {{SIZE}}px; right: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px;',
					],
				]
			);

			$this->add_control(
				'overlay_background_color',
				[
					'label' 	=> __( 'Background Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ee-search-form__overlay' => 'background-color: {{VALUE}};',
					],
				]
			);

		$this->parent->end_controls_section();

	}

	/**
	 * Register Input Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_input_style_controls() {}

	/**
	 * Register Button Style Controls
	 *
	 * @since  2.1.0
	 * @return void
	 */
	public function register_button_style_controls() {

		$this->parent->start_injection( [
			'type' => 'section',
			'at' => 'start',
			'of' => 'section_button_style',
		] );

			$this->add_responsive_control(
				'button_size', 
				[
					'label' 		=> __( 'Size', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 1,
							'max' 	=> 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ee-search-form__submit' => 'min-width: {{SIZE}}px; min-height: {{SIZE}}px;',
					],
				]
			);

			$this->add_responsive_control(
				'button_border_radius',
				[
					'label' 		=> __( 'Border Radius', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 	=> [
						'size' 	=> '',
					],
					'range' 	=> [
						'px' 	=> [
							'min' 	=> 0,
							'max' 	=> 100,
						],
					],
					'selectors'	=> [
						'{{WRAPPER}} .ee-search-form__submit' => 'border-radius: {{SIZE}}px;',
					],
				]
			);

		$this->parent->end_injection();

	}

	/**
	 * Render widget content
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render() {
		parent::render();

		/**
		 * Add Skin Actions
		 */
		add_action( 'landtech_extras/search-form/fields/before_end', [ $this->parent, 'render_inline_filters' ], 10 );

		$this->render_form();
	}

	/**
	 * After Fields
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function after_fields() {
		$this->parent->render_inline_filters();
	}

	/**
	 * Render Form Container
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_form_container() {

		$this->parent->add_render_attribute( [
			'overlay' => [
				'class' => [
					'ee-search-form__overlay',
				],
			],
		] );

		?><div <?php $this->parent->print_render_attribute_string( 'overlay' ); ?>><?php
			parent::render_form_container();
			parent::render_filters();
		?></div><?php

		parent::render_button();
	}

	/**
	 * Render Button
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_button_content() {
		$this->render_icon();
	}

	/**
	 * Render Close Icon
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function render_close_icon() {
		$settings = $this->parent->get_settings();

		$this->parent->add_render_attribute( [
			'close' => [
				'class' => [
					'ee-search-form__overlay__close',
				],
			],
		] );

		?><div <?php $this->parent->print_render_attribute_string( 'close' ); ?>><i class="eicon-close"></i></div><?php
	}
}