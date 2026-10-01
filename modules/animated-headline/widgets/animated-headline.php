<?php
/**
 * Animated headline widget (typewriter + word cycle).
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\AnimatedHeadline\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.10.0
 */
class Animated_Headline extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-animated-headline';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Animated Headline', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-animated-headline';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'headline', 'typewriter', 'animated', 'rotating' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-animated-headline' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-animated-headline' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_headline',
			array(
				'label' => __( 'Headline', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'before_text',
			array(
				'label'   => __( 'Before Text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'We build', 'landtech-extras-for-elementor' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'word',
			array(
				'label'   => __( 'Word', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'words',
			array(
				'label'       => __( 'Animated Words', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array( 'word' => __( 'websites', 'landtech-extras-for-elementor' ) ),
					array( 'word' => __( 'apps', 'landtech-extras-for-elementor' ) ),
					array( 'word' => __( 'brands', 'landtech-extras-for-elementor' ) ),
				),
				'title_field' => '{{{ word }}}',
			)
		);

		$this->add_control(
			'after_text',
			array(
				'label'   => __( 'After Text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'for you', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'effect',
			array(
				'label'   => __( 'Effect', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'typewriter',
				'options' => array(
					'typewriter'        => __( 'Typewriter', 'landtech-extras-for-elementor' ),
					'word-cycle-fade'   => __( 'Word Cycle Fade', 'landtech-extras-for-elementor' ),
					'word-cycle-slide'  => __( 'Word Cycle Slide', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'premium_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<p class="ltxe-ah-upsell">' . esc_html__( 'Unlock 15+ effects including Split Text, Gradient Reveal, and Scramble — upgrade to LandTech Extras Premium.', 'landtech-extras-for-elementor' ) . '</p>',
			)
		);

		$this->add_control(
			'typing_speed',
			array(
				'label'     => __( 'Typing Speed (ms)', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 80,
				'min'       => 20,
				'max'       => 400,
				'condition' => array(
					'effect' => 'typewriter',
				),
			)
		);

		$this->add_control(
			'hold_duration',
			array(
				'label'   => __( 'Hold Duration (ms)', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2000,
				'min'     => 200,
				'max'     => 10000,
			)
		);

		$this->add_control(
			'show_cursor',
			array(
				'label'        => __( 'Cursor', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'effect' => 'typewriter',
				),
			)
		);

		$this->add_control(
			'cursor_char',
			array(
				'label'     => __( 'Cursor Character', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '|',
				'condition' => array(
					'effect'      => 'typewriter',
					'show_cursor' => 'yes',
				),
			)
		);

		$this->add_control(
			'html_tag',
			array(
				'label'   => __( 'HTML Tag', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'div',
					'p'   => 'p',
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'landtech-extras-for-elementor' ),
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
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-ah' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Style', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'headline_typo',
				'selector' => '{{WRAPPER}} .ltxe-ah',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-ah' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'word_color',
			array(
				'label'     => __( 'Animated Word Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e94560',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-ah__word' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$tag      = isset( $settings['html_tag'] ) ? sanitize_key( (string) $settings['html_tag'] ) : 'h2';
		$allowed  = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p' );
		if ( ! in_array( $tag, $allowed, true ) ) {
			$tag = 'h2';
		}
		$effect = isset( $settings['effect'] ) ? sanitize_key( (string) $settings['effect'] ) : 'typewriter';
		$words  = array();
		if ( ! empty( $settings['words'] ) && is_array( $settings['words'] ) ) {
			foreach ( $settings['words'] as $row ) {
				if ( ! empty( $row['word'] ) ) {
					$words[] = (string) $row['word'];
				}
			}
		}
		if ( empty( $words ) ) {
			$words[] = '';
		}

		$cfg = array(
			'effect' => $effect,
			'words'  => $words,
			'speed'  => isset( $settings['typing_speed'] ) ? absint( $settings['typing_speed'] ) : 80,
			'hold'   => isset( $settings['hold_duration'] ) ? absint( $settings['hold_duration'] ) : 2000,
			'cursor' => ( isset( $settings['show_cursor'] ) && 'yes' === $settings['show_cursor'] ),
			'char'   => isset( $settings['cursor_char'] ) ? (string) $settings['cursor_char'] : '|',
		);

		echo '<' . tag_escape( $tag ) . ' class="ltxe-ah ltxe-ah--' . esc_attr( $effect ) . '" data-ltxe-ah="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
		if ( ! empty( $settings['before_text'] ) ) {
			echo '<span class="ltxe-ah__before">' . esc_html( (string) $settings['before_text'] ) . '</span> ';
		}
		echo '<span class="ltxe-ah__word" data-ltxe-ah-word>' . esc_html( $words[0] ) . '</span>';
		if ( ! empty( $cfg['cursor'] ) && 'typewriter' === $effect ) {
			echo '<span class="ltxe-ah__cursor" aria-hidden="true">' . esc_html( $cfg['char'] ) . '</span>';
		}
		if ( ! empty( $settings['after_text'] ) ) {
			echo ' <span class="ltxe-ah__after">' . esc_html( (string) $settings['after_text'] ) . '</span>';
		}
		echo '</' . tag_escape( $tag ) . '>';
	}
}
