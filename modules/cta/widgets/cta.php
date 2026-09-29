<?php
namespace LandTechExtras\Modules\Cta\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Call to action banner.
 *
 * @since 2.9.0
 */
class Cta extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-cta';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Call to Action', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-call-to-action';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'cta', 'banner', 'call to action', 'hero' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-cta' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-cta' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_bg',
			array(
				'label' => __( 'Background', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'bg_type',
			array(
				'label'   => __( 'Background', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'color',
				'options' => array(
					'color'     => __( 'Color / Gradient', 'landtech-extras-for-elementor' ),
					'image'     => __( 'Image', 'landtech-extras-for-elementor' ),
					'video'     => __( 'Video URL', 'landtech-extras-for-elementor' ),
					'particles' => __( 'Particles', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'bg_fill',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .ltxe-cta',
				'condition' => array(
					'bg_type' => array( 'color', 'particles' ),
				),
			)
		);

		$this->add_control(
			'bg_image',
			array(
				'label'     => __( 'Image', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'bg_type' => 'image',
				),
			)
		);

		$this->add_control(
			'bg_video',
			array(
				'label'       => __( 'MP4 URL', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
				'condition'   => array(
					'bg_type' => 'video',
				),
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => __( 'Overlay Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0f172a',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-cta__overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'overlay_opacity',
			array(
				'label'     => __( 'Overlay Opacity', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.05,
					),
				),
				'default'   => array(
					'size' => 0.45,
				),
				'selectors' => array(
					'{{WRAPPER}} .ltxe-cta__overlay' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'   => __( 'Eyebrow', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Ready when you are', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Heading', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Build the next page faster.', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'subtext',
			array(
				'label'   => __( 'Subtext', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'A full-width call to action with two buttons and an optional video background.', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'btn1_text',
			array(
				'label'   => __( 'Primary Button', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Get started', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'btn1_url',
			array(
				'label' => __( 'Primary URL', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'btn1_style',
			array(
				'label'   => __( 'Primary Style', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'primary',
				'options' => $this->button_style_options(),
			)
		);

		$this->add_control(
			'btn2_text',
			array(
				'label'   => __( 'Secondary Button', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'See features', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'btn2_url',
			array(
				'label' => __( 'Secondary URL', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::URL,
			)
		);

		$this->add_control(
			'btn2_style',
			array(
				'label'   => __( 'Secondary Style', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'outline',
				'options' => $this->button_style_options(),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Text Align', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'landtech-extras-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'landtech-extras-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'landtech-extras-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-cta__inner' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'min_height',
			array(
				'label'      => __( 'Min Height', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 160,
						'max' => 800,
					),
				),
				'default'    => array(
					'size' => 320,
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-cta' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typo',
				'selector' => '{{WRAPPER}} .ltxe-cta__heading',
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => __( 'Heading Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-cta__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return array<string,string>
	 */
	private function button_style_options() {
		return array(
			'primary'   => __( 'Primary', 'landtech-extras-for-elementor' ),
			'secondary' => __( 'Secondary', 'landtech-extras-for-elementor' ),
			'outline'   => __( 'Outline', 'landtech-extras-for-elementor' ),
			'ghost'     => __( 'Ghost', 'landtech-extras-for-elementor' ),
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$bg       = isset( $settings['bg_type'] ) ? sanitize_key( (string) $settings['bg_type'] ) : 'color';
		if ( ! in_array( $bg, array( 'color', 'image', 'video', 'particles' ), true ) ) {
			$bg = 'color';
		}

		echo '<section class="ltxe-cta ltxe-cta--' . esc_attr( $bg ) . '">';
		if ( 'image' === $bg && ! empty( $settings['bg_image']['url'] ) ) {
			echo '<img class="ltxe-cta__img" src="' . esc_url( $settings['bg_image']['url'] ) . '" alt="" />';
		}
		if ( 'video' === $bg ) {
			$video = $this->get_link_url( isset( $settings['bg_video'] ) ? $settings['bg_video'] : array() );
			if ( '' !== $video ) {
				echo '<video class="ltxe-cta__video" autoplay muted loop playsinline preload="metadata">';
				echo '<source src="' . esc_url( $video ) . '" type="video/mp4" />';
				echo '</video>';
			}
		}
		if ( 'particles' === $bg ) {
			echo '<div class="ltxe-cta__particles" aria-hidden="true">';
			for ( $i = 0; $i < 10; $i++ ) {
				echo '<span class="ltxe-cta__dot"></span>';
			}
			echo '</div>';
		}
		echo '<div class="ltxe-cta__overlay" aria-hidden="true"></div>';
		echo '<div class="ltxe-cta__inner">';
		if ( ! empty( $settings['eyebrow'] ) ) {
			echo '<p class="ltxe-cta__eyebrow">' . esc_html( (string) $settings['eyebrow'] ) . '</p>';
		}
		if ( ! empty( $settings['heading'] ) ) {
			echo '<h2 class="ltxe-cta__heading">' . esc_html( (string) $settings['heading'] ) . '</h2>';
		}
		if ( ! empty( $settings['subtext'] ) ) {
			echo '<p class="ltxe-cta__sub">' . esc_html( (string) $settings['subtext'] ) . '</p>';
		}
		echo '<div class="ltxe-cta__actions">';
		$this->render_button( $settings, 'btn1' );
		$this->render_button( $settings, 'btn2' );
		echo '</div>';
		echo '</div>';
		echo '</section>';
	}

	/**
	 * @param array<string,mixed> $settings Settings.
	 * @param string              $prefix   btn1|btn2.
	 * @return void
	 */
	private function render_button( $settings, $prefix ) {
		$text = isset( $settings[ $prefix . '_text' ] ) ? (string) $settings[ $prefix . '_text' ] : '';
		$url  = $this->get_link_url( isset( $settings[ $prefix . '_url' ] ) ? $settings[ $prefix . '_url' ] : array() );
		if ( '' === $text || '' === $url ) {
			return;
		}
		$style = isset( $settings[ $prefix . '_style' ] ) ? sanitize_key( (string) $settings[ $prefix . '_style' ] ) : 'primary';
		if ( ! in_array( $style, array( 'primary', 'secondary', 'outline', 'ghost' ), true ) ) {
			$style = 'primary';
		}
		echo '<a class="ltxe-cta__btn ltxe-cta__btn--' . esc_attr( $style ) . '" href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a>';
	}

	/**
	 * @param mixed $link URL control.
	 * @return string
	 */
	private function get_link_url( $link ) {
		if ( is_array( $link ) && ! empty( $link['url'] ) ) {
			return esc_url_raw( (string) $link['url'] );
		}
		return '';
	}
}
