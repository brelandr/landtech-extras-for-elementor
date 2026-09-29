<?php
namespace LandTechExtras\Modules\SocialShare\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Privacy-first social share buttons (URL-only, no third-party JS).
 *
 * @since 2.9.0
 */
class Social_Share extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-social-share';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Social Share', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-share';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'share', 'social', 'facebook', 'twitter' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-social-share' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-social-share' );
	}

	/**
	 * @inheritDoc
	 */
	protected function ltxe_container_query_name() {
		return 'ltxe-share';
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_share',
			array(
				'label' => __( 'Share', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'platforms',
			array(
				'label'    => __( 'Platforms', 'landtech-extras-for-elementor' ),
				'type'     => Controls_Manager::SELECT2,
				'multiple' => true,
				'default'  => array( 'facebook', 'x', 'linkedin', 'copy' ),
				'options'  => $this->get_platform_labels(),
			)
		);

		$this->add_control(
			'url_source',
			array(
				'label'   => __( 'Share URL', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'current',
				'options' => array(
					'current' => __( 'Current Page', 'landtech-extras-for-elementor' ),
					'custom'  => __( 'Custom URL', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'custom_url',
			array(
				'label'       => __( 'Custom URL', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array( 'url_source' => 'custom' ),
				'placeholder' => 'https://',
			)
		);

		$this->add_control(
			'button_style',
			array(
				'label'   => __( 'Button Style', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon_label',
				'options' => array(
					'icon'       => __( 'Icon Only', 'landtech-extras-for-elementor' ),
					'icon_label' => __( 'Icon + Label', 'landtech-extras-for-elementor' ),
					'label'      => __( 'Label Only', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'button_shape',
			array(
				'label'   => __( 'Shape', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => array(
					'square'  => __( 'Square', 'landtech-extras-for-elementor' ),
					'rounded' => __( 'Rounded', 'landtech-extras-for-elementor' ),
					'circle'  => __( 'Circle', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'color_style',
			array(
				'label'   => __( 'Colors', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'brand',
				'options' => array(
					'brand'   => __( 'Brand Colors', 'landtech-extras-for-elementor' ),
					'custom'  => __( 'Custom', 'landtech-extras-for-elementor' ),
					'minimal' => __( 'Minimal', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'custom_color',
			array(
				'label'     => __( 'Custom Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array( 'color_style' => 'custom' ),
				'selectors' => array(
					'{{WRAPPER}} .ltxe-share__btn' => 'background-color: {{VALUE}}; color: #fff;',
				),
			)
		);

		$this->add_responsive_control(
			'button_size',
			array(
				'label'      => __( 'Button Size', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array( 'size' => 44 ),
				'range'      => array(
					'px' => array(
						'min' => 44,
						'max' => 72,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-share__btn' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'open_in',
			array(
				'label'   => __( 'Open In', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'popup',
				'options' => array(
					'popup' => __( 'Popup Window', 'landtech-extras-for-elementor' ),
					'tab'   => __( 'New Tab', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings  = $this->get_settings_for_display();
		$platforms = isset( $settings['platforms'] ) && is_array( $settings['platforms'] ) ? $settings['platforms'] : array();
		$labels    = $this->get_platform_labels();
		$share_url = $this->resolve_share_url( $settings );
		$title     = wp_strip_all_tags( get_the_title() );
		$image     = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		$style     = isset( $settings['button_style'] ) ? sanitize_key( (string) $settings['button_style'] ) : 'icon_label';
		$shape     = isset( $settings['button_shape'] ) ? sanitize_key( (string) $settings['button_shape'] ) : 'rounded';
		$colors    = isset( $settings['color_style'] ) ? sanitize_key( (string) $settings['color_style'] ) : 'brand';
		$open      = isset( $settings['open_in'] ) ? sanitize_key( (string) $settings['open_in'] ) : 'popup';

		if ( ! in_array( $style, array( 'icon', 'icon_label', 'label' ), true ) ) {
			$style = 'icon_label';
		}
		if ( ! in_array( $shape, array( 'square', 'rounded', 'circle' ), true ) ) {
			$shape = 'rounded';
		}
		if ( ! in_array( $colors, array( 'brand', 'custom', 'minimal' ), true ) ) {
			$colors = 'brand';
		}
		if ( 'tab' !== $open ) {
			$open = 'popup';
		}

		$this->ltxe_open_container_query();
		echo '<div class="ltxe-share ltxe-share--' . esc_attr( $style ) . ' ltxe-share--' . esc_attr( $shape ) . ' ltxe-share--' . esc_attr( $colors ) . '" data-ltxe-open="' . esc_attr( $open ) . '">';
		foreach ( $platforms as $platform ) {
			$platform = sanitize_key( (string) $platform );
			if ( ! isset( $labels[ $platform ] ) ) {
				continue;
			}
			$this->render_platform( $platform, $labels[ $platform ], $share_url, $title, $image, $style, $open );
		}
		echo '</div>';
		$this->ltxe_close_container_query();
	}

	/**
	 * @return array<string,string>
	 */
	private function get_platform_labels() {
		return array(
			'facebook'  => __( 'Facebook', 'landtech-extras-for-elementor' ),
			'x'         => __( 'X', 'landtech-extras-for-elementor' ),
			'linkedin'  => __( 'LinkedIn', 'landtech-extras-for-elementor' ),
			'whatsapp'  => __( 'WhatsApp', 'landtech-extras-for-elementor' ),
			'telegram'  => __( 'Telegram', 'landtech-extras-for-elementor' ),
			'pinterest' => __( 'Pinterest', 'landtech-extras-for-elementor' ),
			'reddit'    => __( 'Reddit', 'landtech-extras-for-elementor' ),
			'email'     => __( 'Email', 'landtech-extras-for-elementor' ),
			'copy'      => __( 'Copy Link', 'landtech-extras-for-elementor' ),
			'print'     => __( 'Print', 'landtech-extras-for-elementor' ),
		);
	}

	/**
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	private function resolve_share_url( $settings ) {
		if ( isset( $settings['url_source'] ) && 'custom' === $settings['url_source'] && ! empty( $settings['custom_url']['url'] ) ) {
			return esc_url_raw( (string) $settings['custom_url']['url'] );
		}
		return esc_url_raw( get_permalink() ? (string) get_permalink() : home_url( '/' ) );
	}

	/**
	 * @param string $platform Platform key.
	 * @param string $label    Label.
	 * @param string $url      Share URL.
	 * @param string $title    Title.
	 * @param string $image    Image URL.
	 * @param string $style    Button style.
	 * @param string $open     popup|tab.
	 * @return void
	 */
	private function render_platform( $platform, $label, $url, $title, $image, $style, $open ) {
		$href    = $this->build_share_href( $platform, $url, $title, $image );
		$show_icon  = 'label' !== $style;
		$show_label = 'icon' !== $style;
		$is_btn     = in_array( $platform, array( 'copy', 'print' ), true );

		$classes = 'ltxe-share__btn ltxe-share__btn--' . $platform;
		if ( $is_btn ) {
			echo '<button type="button" class="' . esc_attr( $classes ) . '" data-ltxe-action="' . esc_attr( 'copy' === $platform ? 'copy-link' : 'print' ) . '" data-ltxe-url="' . esc_attr( $url ) . '">';
		} else {
			$action = 'popup' === $open ? 'share-popup' : '';
			echo '<a class="' . esc_attr( $classes ) . '" href="' . esc_url( $href ) . '"';
			if ( 'email' !== $platform ) {
				echo ' target="_blank" rel="noopener noreferrer"';
			}
			if ( '' !== $action ) {
				echo ' data-ltxe-action="' . esc_attr( $action ) . '"';
			}
			echo '>';
		}
		if ( $show_icon ) {
			echo '<span class="ltxe-share__icon" aria-hidden="true">' . esc_html( $this->icon_mark( $platform ) ) . '</span>';
		}
		if ( $show_label ) {
			echo '<span class="ltxe-share__label">' . esc_html( $label ) . '</span>';
		} else {
			echo '<span class="screen-reader-text">' . esc_html( $label ) . '</span>';
		}
		if ( $is_btn ) {
			echo '</button>';
		} else {
			echo '</a>';
		}
	}

	/**
	 * @param string $platform Platform.
	 * @return string
	 */
	private function icon_mark( $platform ) {
		$map = array(
			'facebook'  => 'f',
			'x'         => 'X',
			'linkedin'  => 'in',
			'whatsapp'  => 'W',
			'telegram'  => 'T',
			'pinterest' => 'P',
			'reddit'    => 'R',
			'email'     => '@',
			'copy'      => '⧉',
			'print'     => '⎙',
		);
		return isset( $map[ $platform ] ) ? $map[ $platform ] : $platform;
	}

	/**
	 * @param string $platform Platform.
	 * @param string $url      URL.
	 * @param string $title    Title.
	 * @param string $image    Image.
	 * @return string
	 */
	private function build_share_href( $platform, $url, $title, $image ) {
		$u = rawurlencode( $url );
		$t = rawurlencode( $title );
		$m = rawurlencode( (string) $image );
		switch ( $platform ) {
			case 'facebook':
				return 'https://www.facebook.com/sharer/sharer.php?u=' . $u;
			case 'x':
				return 'https://twitter.com/intent/tweet?url=' . $u . '&text=' . $t;
			case 'linkedin':
				return 'https://www.linkedin.com/sharing/share-offsite/?url=' . $u;
			case 'whatsapp':
				return 'https://api.whatsapp.com/send?text=' . $t . '%20' . $u;
			case 'telegram':
				return 'https://t.me/share/url?url=' . $u . '&text=' . $t;
			case 'pinterest':
				return 'https://pinterest.com/pin/create/button/?url=' . $u . '&media=' . $m;
			case 'reddit':
				return 'https://reddit.com/submit?url=' . $u . '&title=' . $t;
			case 'email':
				return 'mailto:?subject=' . $t . '&body=' . $u;
			default:
				return $url;
		}
	}
}
