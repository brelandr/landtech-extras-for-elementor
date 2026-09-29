<?php
namespace LandTechExtras\Modules\DarkMode\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dark mode toggle with localStorage and system preference.
 *
 * @since 2.9.0
 */
class Dark_Mode_Toggle extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-dark-mode';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Dark Mode Toggle', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-dark-mode';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'dark', 'theme', 'toggle', 'night' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-dark-mode' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-dark-mode' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_toggle',
			array(
				'label' => __( 'Toggle', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'toggle_style',
			array(
				'label'   => __( 'Style', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => array(
					'switch' => __( 'Toggle Switch', 'landtech-extras-for-elementor' ),
					'icon'   => __( 'Icon Button', 'landtech-extras-for-elementor' ),
					'text'   => __( 'Text Button', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'light_icon',
			array(
				'label'   => __( 'Light Icon', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-sun',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'dark_icon',
			array(
				'label'   => __( 'Dark Icon', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-moon',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'default_mode',
			array(
				'label'   => __( 'Default Mode', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'system',
				'options' => array(
					'system' => __( 'Follow System', 'landtech-extras-for-elementor' ),
					'light'  => __( 'Always Light', 'landtech-extras-for-elementor' ),
					'dark'   => __( 'Always Dark', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'dark_bg',
			array(
				'label'   => __( 'Dark Page Background', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#121212',
			)
		);

		$this->add_control(
			'dark_text',
			array(
				'label'   => __( 'Dark Body Text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#f5f5f5',
			)
		);

		$this->add_control(
			'dark_accent',
			array(
				'label'   => __( 'Dark Accent', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#6ea8fe',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$style    = isset( $settings['toggle_style'] ) ? sanitize_key( (string) $settings['toggle_style'] ) : 'icon';
		if ( ! in_array( $style, array( 'switch', 'icon', 'text' ), true ) ) {
			$style = 'icon';
		}
		$mode = isset( $settings['default_mode'] ) ? sanitize_key( (string) $settings['default_mode'] ) : 'system';
		if ( ! in_array( $mode, array( 'system', 'light', 'dark' ), true ) ) {
			$mode = 'system';
		}
		$bg     = isset( $settings['dark_bg'] ) ? sanitize_hex_color( (string) $settings['dark_bg'] ) : '#121212';
		$text   = isset( $settings['dark_text'] ) ? sanitize_hex_color( (string) $settings['dark_text'] ) : '#f5f5f5';
		$accent = isset( $settings['dark_accent'] ) ? sanitize_hex_color( (string) $settings['dark_accent'] ) : '#6ea8fe';
		if ( ! $bg ) {
			$bg = '#121212';
		}
		if ( ! $text ) {
			$text = '#f5f5f5';
		}
		if ( ! $accent ) {
			$accent = '#6ea8fe';
		}

		$surface = '#1e1e1e';
		$css     = sprintf(
			':root{--ltxe-dark-bg:%1$s;--ltxe-dark-text:%2$s;--ltxe-dark-accent:%3$s;}html[data-ltxe-theme="dark"]{--ltxe-surface:%4$s;--ltxe-ink:%2$s;--e-global-color-primary:%3$s;}',
			$bg,
			$text,
			$accent,
			$surface
		);
		wp_add_inline_style( 'landtech-extras-dark-mode', $css );

		$config = wp_json_encode(
			array(
				'default_mode' => $mode,
				'bg'           => $bg,
				'text'         => $text,
				'accent'       => $accent,
				'surface'      => $surface,
			)
		);
		echo '<button type="button" class="ltxe-dark-toggle ltxe-dark-toggle--' . esc_attr( $style ) . '" data-ltxe-dark="' . esc_attr( $config ) . '" aria-pressed="false" aria-label="' . esc_attr__( 'Toggle dark mode', 'landtech-extras-for-elementor' ) . '">';
		echo '<span class="ltxe-dark-toggle__light" aria-hidden="true">';
		if ( ! empty( $settings['light_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['light_icon'], array( 'aria-hidden' => 'true' ) );
		} else {
			echo '☀';
		}
		echo '</span>';
		echo '<span class="ltxe-dark-toggle__dark" aria-hidden="true">';
		if ( ! empty( $settings['dark_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['dark_icon'], array( 'aria-hidden' => 'true' ) );
		} else {
			echo '☾';
		}
		echo '</span>';
		if ( 'text' === $style ) {
			echo '<span class="ltxe-dark-toggle__text">' . esc_html__( 'Theme', 'landtech-extras-for-elementor' ) . '</span>';
		}
		echo '</button>';
	}
}
