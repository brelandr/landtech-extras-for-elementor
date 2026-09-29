<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Svg\Widgets;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Group_Control_Transition;

// Elementor Classes
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Inline_SVG
 *
 * @since 1.7.0
 */
class Inline_Svg extends Extras_Widget {

	/**
	 * Get Name
	 * 
	 * Get the name of the widget
	 *
	 * @since  1.7.0
	 * @return string
	 */
	public function get_name() {
		return 'ee-inline-svg';
	}

	/**
	 * Get Title
	 * 
	 * Get the title of the widget
	 *
	 * @since  1.7.0
	 * @return string
	 */
	public function get_title() {
		return __( 'Inline SVG', 'landtech-extras-for-elementor' );
	}

	/**
	 * Get Icon
	 * 
	 * Get the icon of the widget
	 *
	 * @since  1.7.0
	 * @return string
	 */
	public function get_icon() {
		return 'nicon nicon-svg';
	}

	/**
	 * Get Script Depends
	 * 
	 * A list of scripts that the widgets is depended in
	 *
	 * @since  1.7.0
	 * @return array
	 */
	public function get_script_depends() {
		return [ 'landtech-extras-frontend', 'landtech-extras-inline-svg', 'landtech-extras-anime-helpers' ];
	}

	/**
	 * Wrapper HTML (data-url shell) is static at render time; SVG fetch/injection runs via frontend.js.
	 *
	 * @inheritDoc
	 */
	protected static function ltxe_allows_element_html_cache(): bool {
		return true;
	}

	/**
	 * Register Widget Controls
	 *
	 * @since  1.7.0
	 * @return void
	 */
	protected function _register_controls() {
		
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Graphic', 'landtech-extras-for-elementor' ),
			]
		);

			$this->add_control(
				'svg_source',
				[
					'label'   => __( 'Source', 'landtech-extras-for-elementor' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'media',
					'options' => [
						'media' => __( 'Media Library', 'landtech-extras-for-elementor' ),
						'url'   => __( 'Custom URL', 'landtech-extras-for-elementor' ),
					],
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'svg',
				[
					'label' 	=> __( 'Choose file', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::MEDIA,
					'dynamic' 	=> [ 'active' => true ],
					'frontend_available' => true,
					'condition' => [
						'svg_source' => 'media',
					],
				]
			);

			$this->add_control(
				'svg_custom_url',
				[
					'label'       => __( 'SVG URL', 'landtech-extras-for-elementor' ),
					'description' => __( 'Direct link to an .svg file (same site or CORS-enabled). Enable Allow SVG uploads under Elementor → LandTech Extras → Advanced to use the Media Library instead.', 'landtech-extras-for-elementor' ),
					'type'        => Controls_Manager::URL,
					'dynamic'     => [ 'active' => true ],
					'placeholder' => 'https://example.com/icon.svg',
					'frontend_available' => true,
					'condition'   => [
						'svg_source' => 'url',
					],
				]
			);

			$this->add_control(
				'link',
				[
					'label' 		=> __( 'Link', 'landtech-extras-for-elementor' ),
					'description' 	=> __( 'Active only when tolltips\' Trigger is set to Hover', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::URL,
					'dynamic' 		=> [ 'active' => true ],
					'placeholder' 	=> 'http://your-link.com',
					'default' 		=> [
						'url' 		=> '',
					],
					'separator' 	=> 'after',
					'frontend_available' => true,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Graphic', 'landtech-extras-for-elementor' ),
				'tab' 		=> Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'align',
				[
					'label' => __( 'Alignment', 'landtech-extras-for-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'landtech-extras-for-elementor' ),
							'icon' => 'fa fa-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'landtech-extras-for-elementor' ),
							'icon' => 'fa fa-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'landtech-extras-for-elementor' ),
							'icon' => 'fa fa-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'sizing',
				[
					'label' 		=> __( 'Sizing', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Makes contents responsive and allows you to change maximum width and aspect ratio', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'yes',
				]
			);

			$this->add_control(
				'maintain_ratio',
				[
					'label' 		=> __( 'Keep aspect ratio', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Maintains width / height ratio intact. Note: Use this feature carefully as it might distort elements inside the SVG.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'yes',
					'condition'		=> [
						'sizing'	=> 'yes'
					],
					'frontend_available' => true,
				]
			);

			$this->add_responsive_control(
				'width',
				[
					'label' 		=> __( 'Width', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Set the maximum width', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> '',
					],
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 1920,
							'step' 	=> 10,
						],
						'%' => [
							'min' 	=> 0,
							'max' 	=> 100,
						],
					],
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-inline-svg' => 'width: 100%; max-width: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .ee-inline-svg > svg' => 'width: 100%; height: auto; min-width: auto;',
					],
					'condition'		=> [
						'sizing'	=> 'yes'
					],
				]
			);

			$this->add_responsive_control(
				'height',
				[
					'label' 		=> __( 'Height', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SLIDER,
					'default' 		=> [
						'size' 		=> '',
					],
					'range' 		=> [
						'px' 		=> [
							'min' 	=> 0,
							'max' 	=> 1920,
							'step' 	=> 10,
						],
						'%' => [
							'min' 	=> 0,
							'max' 	=> 100,
						],
					],
					'size_units' 	=> [ 'px', '%' ],
					'selectors' 	=> [
						'{{WRAPPER}} .ee-inline-svg > svg' => 'height: {{SIZE}}{{UNIT}};',
					],
					'condition'			 	=> [
						'sizing'		=> 'yes',
						'maintain_ratio!' 	=> 'yes'
					],
				]
			);

			$this->add_control(
				'remove_inline_css',
				[
					'label' 		=> __( 'Convert CSS to attributes', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Sometimes a SVG might have internal or inline CSS styling preventing you to set a custom color. This happens usually when exporting artwork from Illustrator. Keeping this option on will prevent strange color behaviour when multiple Inline SVG widgets are on the same page.', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> 'yes',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$this->add_control(
				'override_colors',
				[
					'label' 		=> __( 'Override Color', 'landtech-extras-for-elementor' ),
					'description'	=> __( 'Specify the color for all svg elements that have a fill or stroke color set.', 'landtech-extras-for-elementor' ),
					'frontend_available' => true,
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
				]
			);

			$this->add_group_control(
				Group_Control_Transition::get_type(),
				[
					'name' 			=> 'svg',
					'selector' 		=> '{{WRAPPER}} .ee-inline-svg',
				]
			);

			$this->update_control( 'svg_transition', array(
				'default' => 'custom',
			));

			$this->add_control(
				'color',
				[
					'label' 	=> __( 'Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-inline-svg' => 'color: {{VALUE}} !important',
					],
					'condition'	=> [
						'override_colors!' => '',
					],
				]
			);

			$this->add_control(
				'color_hover',
				[
					'label' 	=> __( 'Hover Color', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::COLOR,
					'global' => [
						'default' => Global_Colors::COLOR_SECONDARY,
					],
					'selectors' => [
						'{{WRAPPER}} .ee-inline-svg:hover' => 'color: {{VALUE}} !important',
					],
					'condition'	=> [
						'override_colors!' => '',
					]
				]
			);

			$path_repeater = new Repeater();

			$path_repeater->add_control(
				'css_selector',
				[
					'label'       => __( 'CSS Selector', 'landtech-extras-for-elementor' ),
					'type'        => Controls_Manager::TEXT,
					'placeholder' => '#path1, .cls-2',
					'description' => __( 'Target specific SVG paths, groups, or classes.', 'landtech-extras-for-elementor' ),
				]
			);

			$path_repeater->add_control(
				'fill_color',
				[
					'label' => __( 'Fill Color', 'landtech-extras-for-elementor' ),
					'type'  => Controls_Manager::COLOR,
				]
			);

			$path_repeater->add_control(
				'stroke_color',
				[
					'label' => __( 'Stroke Color', 'landtech-extras-for-elementor' ),
					'type'  => Controls_Manager::COLOR,
				]
			);

			$path_repeater->add_control(
				'hover_fill',
				[
					'label' => __( 'Hover Fill', 'landtech-extras-for-elementor' ),
					'type'  => Controls_Manager::COLOR,
				]
			);

			$this->add_control(
				'path_colors',
				[
					'label'       => __( 'Per-Path Colors', 'landtech-extras-for-elementor' ),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $path_repeater->get_controls(),
					'title_field' => '{{{ css_selector }}}',
				]
			);

			$this->add_control(
				'svg_animation',
				[
					'label'   => __( 'Animation', 'landtech-extras-for-elementor' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'none',
					'options' => [
						'none'    => __( 'None', 'landtech-extras-for-elementor' ),
						'draw'    => __( 'Draw (stroke path reveal)', 'landtech-extras-for-elementor' ),
						'fade_in' => __( 'Fade In', 'landtech-extras-for-elementor' ),
						'scale_up'=> __( 'Scale Up', 'landtech-extras-for-elementor' ),
						'rotate'  => __( 'Rotate', 'landtech-extras-for-elementor' ),
					],
				]
			);

			$this->add_control(
				'svg_anim_trigger',
				[
					'label'     => __( 'Trigger', 'landtech-extras-for-elementor' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'scroll',
					'options'   => [
						'scroll' => __( 'On scroll into view', 'landtech-extras-for-elementor' ),
						'hover'  => __( 'On hover', 'landtech-extras-for-elementor' ),
						'load'   => __( 'On page load', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						'svg_animation!' => 'none',
					],
				]
			);

			$this->add_control(
				'svg_anim_duration',
				[
					'label'     => __( 'Duration (ms)', 'landtech-extras-for-elementor' ),
					'type'      => Controls_Manager::NUMBER,
					'default'   => 1200,
					'condition' => [
						'svg_animation!' => 'none',
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
	 * @since  1.7.0
	 * @return void
	 */
	protected function render() {
		$settings 	= $this->get_settings_for_display();
		$tag 		= 'div';
		$svg_url    = $this->get_svg_file_url( $settings );

		if ( '' === $svg_url ) {
			$this->render_placeholder( [ 'body' => __( 'Select your SVG file or enter an SVG URL.', 'landtech-extras-for-elementor' ) ] );
			return;
		}

		// Add main class to wrapper
		$this->add_render_attribute( [
			'wrapper' => [
				'class' 	=> 'ee-inline-svg-wrapper',
			],
			'svg' => [
				'class' 	=> 'ee-inline-svg',
				'data-url' 	=> $svg_url,
				'data-svg-animation' => isset( $settings['svg_animation'] ) ? sanitize_key( $settings['svg_animation'] ) : 'none',
				'data-svg-trigger'   => isset( $settings['svg_anim_trigger'] ) ? sanitize_key( $settings['svg_anim_trigger'] ) : 'scroll',
				'data-svg-duration'  => isset( $settings['svg_anim_duration'] ) ? (string) absint( $settings['svg_anim_duration'] ) : '1200',
			],
		] );

		$this->print_path_color_styles( $settings );

		if ( ! empty( $settings['link']['url'] ) ) {

			$tag = 'a';

			$this->add_render_attribute( 'svg', 'href', $settings['link']['url'] );

			if ( $settings['link']['is_external'] ) {
				$this->add_render_attribute( 'svg', 'target', '_blank' );
			}

			if ( ! empty( $settings['link']['nofollow'] ) ) {
				$this->add_render_attribute( 'svg', 'rel', 'nofollow' );
			}
		}

		?><div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<<?php echo esc_html( $tag ); ?> <?php $this->print_render_attribute_string( 'svg' ); ?>></<?php echo esc_html( $tag ); ?>>
		</div><?php
	}

	/**
	 * Scoped per-path fill/stroke CSS for this widget instance.
	 *
	 * @since 2.7.0
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return void
	 */
	protected function print_path_color_styles( $settings ) {
		$rows = isset( $settings['path_colors'] ) && is_array( $settings['path_colors'] ) ? $settings['path_colors'] : array();
		if ( empty( $rows ) ) {
			return;
		}

		$uid = $this->get_id();
		$css = '';
		foreach ( $rows as $row ) {
			$selector = isset( $row['css_selector'] ) ? trim( (string) $row['css_selector'] ) : '';
			if ( '' === $selector ) {
				continue;
			}
			$parts = array_filter( array_map( 'trim', explode( ',', $selector ) ) );
			$scoped = array();
			foreach ( $parts as $part ) {
				if ( '' === $part || false !== strpos( $part, '{' ) || false !== strpos( $part, '}' ) ) {
					continue;
				}
				$scoped[] = '.elementor-element-' . $uid . ' .ee-inline-svg ' . $part;
			}
			if ( empty( $scoped ) ) {
				continue;
			}
			$joined = implode( ',', $scoped );
			$decls  = '';
			if ( ! empty( $row['fill_color'] ) ) {
				$decls .= 'fill:' . sanitize_hex_color( $row['fill_color'] ) . ';';
			}
			if ( ! empty( $row['stroke_color'] ) ) {
				$decls .= 'stroke:' . sanitize_hex_color( $row['stroke_color'] ) . ';';
			}
			if ( '' !== $decls ) {
				$css .= $joined . '{' . $decls . '}';
			}
			if ( ! empty( $row['hover_fill'] ) ) {
				$hover_sel = implode( ',', array_map(
					static function( $s ) {
						return $s . ':hover';
					},
					$scoped
				) );
				$css .= $hover_sel . '{fill:' . sanitize_hex_color( $row['hover_fill'] ) . ';}';
			}
		}

		if ( '' === $css ) {
			return;
		}

		wp_register_style( 'landtech-extras-inline-svg-path-' . $uid, false, array(), LANDTECH_EXTRAS_VERSION );
		wp_enqueue_style( 'landtech-extras-inline-svg-path-' . $uid );
		wp_add_inline_style( 'landtech-extras-inline-svg-path-' . $uid, $css );
	}

	/**
	 * Resolve the SVG file URL from widget settings.
	 *
	 * @since 2.2.96
	 *
	 * @param array<string,mixed> $settings Widget settings.
	 * @return string
	 */
	protected function get_svg_file_url( $settings ) {
		$source = isset( $settings['svg_source'] ) ? $settings['svg_source'] : 'media';

		if ( 'url' === $source && ! empty( $settings['svg_custom_url']['url'] ) ) {
			return esc_url_raw( $settings['svg_custom_url']['url'] );
		}

		if ( ! empty( $settings['svg']['url'] ) ) {
			return esc_url_raw( $settings['svg']['url'] );
		}

		return '';
	}

	/**
	 * Content Template
	 * 
	 * Javascript content template for quick rendering. None in this case
	 *
	 * @since  1.7.0
	 * @return void
	 */
	protected function content_template() {}
}