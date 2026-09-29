<?php
namespace LandTechExtras\Modules\Faq\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FAQ accordion with FAQPage schema.
 *
 * @since 2.9.0
 */
class Faq extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-faq';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'FAQ Accordion', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-accordion';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'faq', 'accordion', 'schema', 'question' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-faq' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-faq' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_faq',
			array(
				'label' => __( 'FAQ', 'landtech-extras-for-elementor' ),
			)
		);

		$repeater = new Repeater();
		$repeater->add_control(
			'question',
			array(
				'label'   => __( 'Question', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);
		$repeater->add_control(
			'answer',
			array(
				'label'   => __( 'Answer', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::WYSIWYG,
			)
		);
		$repeater->add_control(
			'open_by_default',
			array(
				'label'        => __( 'Open by Default', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => __( 'Items', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->get_default_items(),
				'title_field' => '{{{ question }}}',
			)
		);

		$this->add_control(
			'accordion_mode',
			array(
				'label'        => __( 'Accordion Mode', 'landtech-extras-for-elementor' ),
				'description'  => __( 'Only one item open at a time.', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'faq_schema',
			array(
				'label'        => __( 'FAQPage Schema', 'landtech-extras-for-elementor' ),
				'description'  => __( 'Outputs FAQPage structured data for Google rich results.', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'   => __( 'Icon Position', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'right' => __( 'Right', 'landtech-extras-for-elementor' ),
					'left'  => __( 'Left', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'icon_closed',
			array(
				'label'   => __( 'Closed Icon', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-plus',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'icon_open',
			array(
				'label'   => __( 'Open Icon', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-minus',
					'library' => 'fa-solid',
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

		$this->add_control(
			'question_color',
			array(
				'label'     => __( 'Question Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-faq__question' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'answer_color',
			array(
				'label'     => __( 'Answer Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-faq__answer' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'question_typo',
				'selector' => '{{WRAPPER}} .ltxe-faq__question',
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => __( 'Item Gap', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-faq' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return array<int,array<string,string>>
	 */
	private function get_default_items() {
		return array(
			array(
				'question'        => __( 'What is included?', 'landtech-extras-for-elementor' ),
				'answer'          => __( 'The FAQ Accordion widget ships with keyboard support and optional FAQPage schema.', 'landtech-extras-for-elementor' ),
				'open_by_default' => 'yes',
			),
			array(
				'question'        => __( 'Does this replace FAQ Schema?', 'landtech-extras-for-elementor' ),
				'answer'          => __( 'No. The existing FAQ Schema widget stays available. Use this accordion when you want a styled, interactive list.', 'landtech-extras-for-elementor' ),
				'open_by_default' => '',
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = isset( $settings['items'] ) && is_array( $settings['items'] ) ? $settings['items'] : array();
		if ( empty( $items ) ) {
			echo '<p class="ltxe-faq__empty">' . esc_html__( 'Add questions to the FAQ Accordion widget.', 'landtech-extras-for-elementor' ) . '</p>';
			return;
		}

		$accordion = isset( $settings['accordion_mode'] ) && 'yes' === $settings['accordion_mode'];
		$schema    = isset( $settings['faq_schema'] ) && 'yes' === $settings['faq_schema'];
		$icon_pos  = isset( $settings['icon_position'] ) && 'left' === $settings['icon_position'] ? 'left' : 'right';
		$uid       = $this->get_id();

		echo '<div class="ltxe-faq ltxe-faq--icon-' . esc_attr( $icon_pos ) . '" data-accordion="' . ( $accordion ? '1' : '0' ) . '">';

		$schema_items = array();
		$i            = 0;
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$question = isset( $item['question'] ) ? (string) $item['question'] : '';
			$answer   = isset( $item['answer'] ) ? (string) $item['answer'] : '';
			if ( '' === $question ) {
				continue;
			}
			$open    = isset( $item['open_by_default'] ) && 'yes' === $item['open_by_default'];
			$panel   = 'ltxe-faq-' . sanitize_html_class( $uid ) . '-' . $i;
			$button  = $panel . '-btn';

			echo '<div class="ltxe-faq__item' . ( $open ? ' is-open' : '' ) . '">';
			echo '<h3 class="ltxe-faq__heading">';
			echo '<button type="button" class="ltxe-faq__question" id="' . esc_attr( $button ) . '" aria-expanded="' . ( $open ? 'true' : 'false' ) . '" aria-controls="' . esc_attr( $panel ) . '">';
			echo '<span class="ltxe-faq__icon ltxe-faq__icon--closed" aria-hidden="true">';
			$this->render_icon_or_fallback( isset( $settings['icon_closed'] ) ? $settings['icon_closed'] : array(), '+' );
			echo '</span>';
			echo '<span class="ltxe-faq__icon ltxe-faq__icon--open" aria-hidden="true">';
			$this->render_icon_or_fallback( isset( $settings['icon_open'] ) ? $settings['icon_open'] : array(), '−' );
			echo '</span>';
			echo '<span class="ltxe-faq__label">' . esc_html( $question ) . '</span>';
			echo '</button>';
			echo '</h3>';
			echo '<div class="ltxe-faq__answer" id="' . esc_attr( $panel ) . '" role="region" aria-labelledby="' . esc_attr( $button ) . '"' . ( $open ? '' : ' hidden' ) . '>';
			echo wp_kses_post( $answer );
			echo '</div>';
			echo '</div>';

			$schema_items[] = array(
				'question' => $question,
				'answer'   => $answer,
			);
			$i++;
		}
		echo '</div>';

		if ( $schema && ! empty( $schema_items ) ) {
			$this->render_schema( $schema_items );
		}
	}

	/**
	 * @param array<string,mixed> $icon     Elementor icon.
	 * @param string              $fallback Text fallback.
	 * @return void
	 */
	private function render_icon_or_fallback( $icon, $fallback ) {
		if ( is_array( $icon ) && ! empty( $icon['value'] ) ) {
			Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
			return;
		}
		echo esc_html( $fallback );
	}

	/**
	 * @param array<int,array<string,string>> $items Items.
	 * @return void
	 */
	private function render_schema( $items ) {
		$entities = array();
		foreach ( $items as $item ) {
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $item['question'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $item['answer'] ),
				),
			);
		}
		$schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $entities,
		);
		$json = wp_json_encode( $schema );
		if ( ! is_string( $json ) || '' === $json ) {
			return;
		}
		wp_print_inline_script_tag( $json, array( 'type' => 'application/ld+json' ) );
	}
}
