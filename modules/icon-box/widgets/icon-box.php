<?php
namespace LandTechExtras\Modules\IconBox\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Icon / feature box.
 *
 * @since 2.9.0
 */
class Icon_Box extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-icon-box';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Icon Box', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-icon-box';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'icon', 'feature', 'box', 'service' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-icon-box' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_icon',
			array(
				'label' => __( 'Icon', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'   => __( 'Icon Type', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => array(
					'icon'   => __( 'Icon', 'landtech-extras-for-elementor' ),
					'image'  => __( 'Image', 'landtech-extras-for-elementor' ),
					'number' => __( 'Number', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label'     => __( 'Icon', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'icon_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'icon_image',
			array(
				'label'     => __( 'Image', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					'icon_type' => 'image',
				),
			)
		);

		$this->add_control(
			'icon_number',
			array(
				'label'     => __( 'Number', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '01',
				'condition' => array(
					'icon_type' => 'number',
				),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'   => __( 'Icon Position', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'   => __( 'Top', 'landtech-extras-for-elementor' ),
					'left'  => __( 'Left', 'landtech-extras-for-elementor' ),
					'right' => __( 'Right', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'icon_shape',
			array(
				'label'   => __( 'Icon Shape', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'rounded',
				'options' => array(
					'none'    => __( 'None', 'landtech-extras-for-elementor' ),
					'circle'  => __( 'Circle', 'landtech-extras-for-elementor' ),
					'square'  => __( 'Square', 'landtech-extras-for-elementor' ),
					'rounded' => __( 'Rounded', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 16,
						'max' => 120,
					),
				),
				'default'    => array(
					'size' => 32,
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-icon-box__mark' => 'font-size: {{SIZE}}{{UNIT}}; width: calc({{SIZE}}{{UNIT}} * 2); height: calc({{SIZE}}{{UNIT}} * 2);',
				),
			)
		);

		$this->add_control(
			'hover_anim',
			array(
				'label'   => __( 'Hover Animation', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'   => __( 'None', 'landtech-extras-for-elementor' ),
					'float'  => __( 'Float', 'landtech-extras-for-elementor' ),
					'grow'   => __( 'Grow', 'landtech-extras-for-elementor' ),
					'spin'   => __( 'Icon spin', 'landtech-extras-for-elementor' ),
					'bounce' => __( 'Icon bounce', 'landtech-extras-for-elementor' ),
					'glow'   => __( 'Border glow', 'landtech-extras-for-elementor' ),
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
			'title',
			array(
				'label'   => __( 'Title', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Feature title', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'A short note about this feature or service.', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => __( 'Button Text', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
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
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-icon-box__mark' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_bg',
			array(
				'label'     => __( 'Icon Background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-icon-box__mark' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typo',
				'selector' => '{{WRAPPER}} .ltxe-icon-box__title',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .ltxe-icon-box',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$type     = isset( $settings['icon_type'] ) ? sanitize_key( (string) $settings['icon_type'] ) : 'icon';
		$pos      = isset( $settings['icon_position'] ) ? sanitize_key( (string) $settings['icon_position'] ) : 'top';
		$shape    = isset( $settings['icon_shape'] ) ? sanitize_key( (string) $settings['icon_shape'] ) : 'rounded';
		$anim     = isset( $settings['hover_anim'] ) ? sanitize_key( (string) $settings['hover_anim'] ) : 'none';
		$title    = isset( $settings['title'] ) ? (string) $settings['title'] : '';
		$desc     = isset( $settings['description'] ) ? (string) $settings['description'] : '';
		$btn      = isset( $settings['button_text'] ) ? (string) $settings['button_text'] : '';
		$url      = $this->get_link_url( isset( $settings['link'] ) ? $settings['link'] : array() );

		if ( ! in_array( $type, array( 'icon', 'image', 'number' ), true ) ) {
			$type = 'icon';
		}
		if ( ! in_array( $pos, array( 'top', 'left', 'right' ), true ) ) {
			$pos = 'top';
		}
		if ( ! in_array( $shape, array( 'none', 'circle', 'square', 'rounded' ), true ) ) {
			$shape = 'rounded';
		}
		if ( ! in_array( $anim, array( 'none', 'float', 'grow', 'spin', 'bounce', 'glow' ), true ) ) {
			$anim = 'none';
		}

		$classes = array(
			'ltxe-icon-box',
			'ltxe-icon-box--' . $pos,
			'ltxe-icon-box--shape-' . $shape,
			'ltxe-icon-box--' . $anim,
		);

		$class_names = implode( ' ', $classes );
		if ( '' !== $url ) {
			echo '<a class="' . esc_attr( $class_names ) . '" href="' . esc_url( $url ) . '"';
			if ( ! empty( $settings['link']['is_external'] ) ) {
				echo ' target="_blank" rel="noopener noreferrer"';
			}
			echo '>';
		} else {
			echo '<div class="' . esc_attr( $class_names ) . '">';
		}
		echo '<div class="ltxe-icon-box__mark" aria-hidden="true">';
		$this->render_mark( $settings, $type );
		echo '</div>';
		echo '<div class="ltxe-icon-box__body">';
		if ( '' !== $title ) {
			echo '<h3 class="ltxe-icon-box__title">' . esc_html( $title ) . '</h3>';
		}
		if ( '' !== $desc ) {
			echo '<p class="ltxe-icon-box__desc">' . esc_html( $desc ) . '</p>';
		}
		if ( '' !== $btn ) {
			echo '<span class="ltxe-icon-box__btn">' . esc_html( $btn ) . '</span>';
		}
		echo '</div>';
		if ( '' !== $url ) {
			echo '</a>';
		} else {
			echo '</div>';
		}
	}

	/**
	 * @param array<string,mixed> $settings Settings.
	 * @param string              $type     Mark type.
	 * @return void
	 */
	private function render_mark( $settings, $type ) {
		if ( 'image' === $type ) {
			if ( ! empty( $settings['icon_image']['id'] ) ) {
				echo wp_kses_post( wp_get_attachment_image( absint( $settings['icon_image']['id'] ), 'thumbnail', false, array( 'alt' => '' ) ) );
				return;
			}
			if ( ! empty( $settings['icon_image']['url'] ) ) {
				echo '<img src="' . esc_url( $settings['icon_image']['url'] ) . '" alt="" />';
			}
			return;
		}
		if ( 'number' === $type ) {
			$num = isset( $settings['icon_number'] ) ? (string) $settings['icon_number'] : '';
			echo '<span class="ltxe-icon-box__number">' . esc_html( $num ) . '</span>';
			return;
		}
		if ( ! empty( $settings['selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) );
			return;
		}
		echo '<span>★</span>';
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
