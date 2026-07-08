<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Heading\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Group_Control_Long_Shadow;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Heading
 *
 * @since 0.1.0
 */
class Heading extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  0.1.0
	 * @return string
	 */
	public function get_name() {
		return 'heading-extended';
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
		return __( 'Heading Extra', 'landtech-extras-for-elementor' );
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
		return 'nicon nicon-heading-extended';
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
			'landtech-extras-jquery-long-shadow',
			'landtech-extras-anime',
			'landtech-extras-anime-helpers',
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
			'section_title',
			[
				'label' => __( 'Title', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'title',
				[
					'label' 		=> __( 'Title', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::TEXTAREA,
					'placeholder' 	=> __( 'Enter your title', 'landtech-extras-for-elementor' ),
					'default' 		=> __( 'This is heading element', 'landtech-extras-for-elementor' ),
					'dynamic' 		=> [
						'active' 	=> true,
					],
				]
			);

			$this->add_control(
				'link',
				[
					'label' 		=> __( 'Link', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::URL,
					'placeholder' 	=> esc_url( home_url( '/' ) ),
					'default' 		=> [
						'url' 		=> '',
					],
					'dynamic' => [
						'active' => true,
					],
					'separator'		=> 'before',
				]
			);

			$this->add_control(
				'size',
				[
					'label' 		=> __( 'Size', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SELECT,
					'default' 		=> 'default',
					'options' 		=> [
						'default' 	=> __( 'Default', 'landtech-extras-for-elementor' ),
						'small' 	=> __( 'Small', 'landtech-extras-for-elementor' ),
						'medium' 	=> __( 'Medium', 'landtech-extras-for-elementor' ),
						'large' 	=> __( 'Large', 'landtech-extras-for-elementor' ),
						'xl' 		=> __( 'XL', 'landtech-extras-for-elementor' ),
						'xxl' 		=> __( 'XXL', 'landtech-extras-for-elementor' ),
					],
				]
			);

			$this->add_control(
				'header_size',
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
						'p' 	=> __( 'p', 'landtech-extras-for-elementor' ),
					],
					'default' => 'h1',
				]
			);

			$this->add_responsive_control(
				'align',
				[
					'label' 		=> __( 'Alignment', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'options' 		=> [
						'left' 		=> [
							'title' 	=> __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 	=> [
							'title' => __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' 	=> 'fa fa-align-center',
						],
						'right' 	=> [
							'title' => __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' 	=> 'fa fa-align-right',
						],
						'justify' 	=> [
							'title' => __( 'Justified', 'landtech-extras-for-elementor' ),
							'icon' 	=> 'fa fa-align-justify',
						],
					],
					'default' 		=> '',
					'selectors' 	=> [
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
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
			'section_title_animation',
			[
				'label' => __( 'Entrance animation', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'title_animation_preset',
				[
					'label'              => __( 'Preset', 'landtech-extras-for-elementor' ),
					'type'               => Controls_Manager::SELECT,
					'default'            => 'none',
					'options'            => [
						'none'            => __( 'None', 'landtech-extras-for-elementor' ),
						'fade_up'         => __( 'Fade up', 'landtech-extras-for-elementor' ),
						'typewriter'      => __( 'Typewriter', 'landtech-extras-for-elementor' ),
						'split_words'     => __( 'Split words', 'landtech-extras-for-elementor' ),
						'highlight_sweep' => __( 'Highlight sweep', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_fill',
			[
				'label' 	=> __( 'Fill', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_control(
				'title_fill',
				[
					'label' 	=> __( 'Fill', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'options' 	=> [
						'solid' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
						'gradient' 	=> __( 'Background', 'landtech-extras-for-elementor' ),
					],
					'default' 		=> 'solid',
					'prefix_class'	=> 'ee-heading--'
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' 		=> 'gradient',
					'types' 	=> [ 'gradient', 'classic' ],
					'selector' 	=> '{{WRAPPER}} .ee-heading__text',
					'default'	=> 'gradient',
					'condition'	=> [
						'title_fill'	=> 'gradient'
					]
				]
			);

			$this->add_control(
				'title_color',
				[
					'label' 	=> __( 'Text Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-heading__text' => 'color: {{VALUE}};',
					],
					'condition' => [
						'title_fill' => 'solid'
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_type',
			[
				'label' 	=> __( 'Typography', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' 		=> 'typography',
					'global' => [
						'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
					],
					'selector' 	=> '{{WRAPPER}} .ee-heading',
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_shadow',
			[
				'label' 	=> __( 'Shadow', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name' 		=> 'title_classic_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-heading__text-shadow',
				]
			);

			$this->add_group_control(
				Group_Control_Long_Shadow::get_type(), [
					'name' 		=> 'title_long_shadow',
					'selector' 	=> '{{WRAPPER}} .ee-heading__long-shadow',
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

		if ( empty( $settings['title'] ) )
			return;

		$this->add_render_attribute( [
			'heading' => [
				'class' => 'ee-heading',
				'data-title' => $settings['title'],
			],
		] );

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'heading', 'class', 'elementor-size-' . $settings['size'] );
		}

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'link', 'href', $settings['link']['url'] );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}

			if ( ! empty( $settings['link']['nofollow'] ) ) {
				$this->add_render_attribute( 'link', 'rel', 'nofollow' );
			}
		}

		if ( ! empty( $settings['link']['url'] ) ) { 
			?><a <?php $this->print_render_attribute_string( 'link' ); ?>><?php
		} ?>

			<<?php echo esc_html( $this->ltxe_sanitize_heading_tag( $settings['header_size'] ) ); ?> <?php $this->print_render_attribute_string('heading'); ?>><?php
				$this->render_heading_text();
				$this->render_heading_text_shadow();
				$this->render_heading_long_shadow();
			?></<?php echo esc_html( $this->ltxe_sanitize_heading_tag( $settings['header_size'] ) ); ?>>

		<?php if ( ! empty( $settings['link']['url'] ) ) {
			?></a><?php
		}
	}

	/**
	 * Render Heading Text
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function render_heading_text() {
		$this->add_render_attribute( 'heading-text', 'class', 'ee-heading__text' );

		?><span <?php $this->print_render_attribute_string( 'heading-text' ); ?>>
			<?php echo wp_kses_post( $this->parse_text_editor( $this->get_settings_for_display('title') ) ); ?>
		</span><?php
	}

	/**
	 * Render Heading Text Shadow
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function render_heading_text_shadow() {
		if ( '' === $this->get_settings('title_classic_shadow_text_shadow_type') )
			return;

		$this->add_render_attribute( 'heading-text-shadow', 'class', 'ee-heading__text-shadow' );

		?><span <?php $this->print_render_attribute_string( 'heading-text-shadow' ); ?>>
			<?php echo wp_kses_post( $this->parse_text_editor( $this->get_settings_for_display('title') ) ); ?>
		</span><?php
	}

	/**
	 * Render Heading Long Shadow
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function render_heading_long_shadow() {

		if ( 'yes' !== $this->get_settings_for_display('title_long_shadow_enable') )
			return;

		$this->add_render_attribute( 'heading-long-shadow', 'class', 'ee-heading__long-shadow' );

		?><span <?php $this->print_render_attribute_string( 'heading-long-shadow' ); ?>>
			<?php echo wp_kses_post( $this->parse_text_editor( $this->get_settings_for_display('title') ) ); ?>
		</span><?php
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering
	 *
	 * @since  0.1.0
	 * @return void
	 */
	protected function content_template() { ?><#

		view.addRenderAttribute( {
			'heading' : {
				'class' : [
					'ee-heading'
				],
				'data-title' : settings.title,
			},
			'heading-text' : {
				'class' : [
					'ee-heading__text',
				],
			},
			'heading-text-shadow' : {
				'class' : [
					'ee-heading__text-shadow',
				],
			},
			'heading-long-shadow' : {
				'class' : [
					'ee-heading__long-shadow',
				],
			},
		} );

		if ( '' !== settings.size ) {
			view.addRenderAttribute( 'heading', 'class', 'elementor-size-' + settings.size );
		}

		if ( '' !== settings.link.url ) {
			#><a href="{{ settings.link.url }}"><#
		} #>

			<{{ settings.header_size }} {{{ view.getRenderAttributeString( 'heading' ) }}}>
				<span {{{ view.getRenderAttributeString( 'heading-text' ) }}}>{{{ settings.title }}}</span>
				<span {{{ view.getRenderAttributeString( 'heading-text-shadow' ) }}}>{{{ settings.title }}}</span>

				<# if ( '' !== settings.title_long_shadow_enable ) {
					#><span {{{ view.getRenderAttributeString( 'heading-long-shadow' ) }}}>{{{ settings.title }}}</span><#
				}

			#></{{ settings.header_size }}>

		<# if ( '' !== settings.link.url ) {
			#></a><#

		} #><?php
	}
}
