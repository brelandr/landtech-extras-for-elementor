<?php
/**
 * Shared CF7 / WPForms styler widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\FormStyler\Widgets;

use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\FormStyler\Form_Styler_Sanitize;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __DIR__ ) . '/class-form-styler-sanitize.php';

/**
 * @since 2.10.0
 */
abstract class Form_Styler_Widget extends Extras_Widget {

	/**
	 * @return string contact-form-7|wpforms
	 */
	abstract protected function ltxe_form_tag();

	/**
	 * @return bool
	 */
	abstract protected function ltxe_form_plugin_active();

	/**
	 * @return string
	 */
	abstract protected function ltxe_missing_plugin_notice();

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-form-styler' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-form-styler' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_form',
			array(
				'label' => __( 'Form', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'shortcode',
			array(
				'label'       => __( 'Shortcode', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '[' . $this->ltxe_form_tag() . ' id="123"]',
				'description' => __( 'Paste the form shortcode or a numeric form ID. Only the id attribute is used.', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'full',
				'options' => array(
					'full'     => __( 'Full width', 'landtech-extras-for-elementor' ),
					'inline'   => __( 'Inline labels', 'landtech-extras-for-elementor' ),
					'floating' => __( 'Floating labels', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'show_preview',
			array(
				'label'        => __( 'Preview form (no plugin)', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'description'  => __( 'Renders a sample form so you can style it when the form plugin is not active.', 'landtech-extras-for-elementor' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			array(
				'label' => __( 'Inputs', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'input_background',
			array(
				'label'     => __( 'Background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-input-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'input_border_color',
			array(
				'label'     => __( 'Border Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-input-border: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'input_radius',
			array(
				'label'      => __( 'Border Radius', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-input-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'input_padding',
			array(
				'label'      => __( 'Padding', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-input-pad: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'input_font_size',
			array(
				'label'      => __( 'Font Size', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-input-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'focus_ring',
			array(
				'label'     => __( 'Focus Ring Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-focus: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_label',
			array(
				'label' => __( 'Labels', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => __( 'Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-label-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_font_size',
			array(
				'label'      => __( 'Font Size', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-label-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'label_weight',
			array(
				'label'     => __( 'Font Weight', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''    => __( 'Default', 'landtech-extras-for-elementor' ),
					'400' => '400',
					'500' => '500',
					'600' => '600',
					'700' => '700',
				),
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-label-weight: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_margin',
			array(
				'label'      => __( 'Margin Bottom', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-label-mb: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => __( 'Button', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => __( 'Background Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e94560',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-btn-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => __( 'Text Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-btn-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_background',
			array(
				'label'     => __( 'Hover Background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-btn-hover: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => __( 'Border Radius', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-btn-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-btn-pad: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_full',
			array(
				'label'        => __( 'Full Width', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_messages',
			array(
				'label' => __( 'Messages', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'error_color',
			array(
				'label'     => __( 'Error Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#b91c1c',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-error-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'error_background',
			array(
				'label'     => __( 'Error Background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-error-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'error_border',
			array(
				'label'     => __( 'Error Border', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-error-border: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'success_color',
			array(
				'label'     => __( 'Success Color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#166534',
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-success-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'success_background',
			array(
				'label'     => __( 'Success Background', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-form-styler' => '--ltxe-fs-success-bg: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'message_typo',
				'selector' => '{{WRAPPER}} .ltxe-form-styler__error, {{WRAPPER}} .ltxe-form-styler__success, {{WRAPPER}} .wpcf7-not-valid-tip, {{WRAPPER}} .wpforms-error',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$layout   = isset( $settings['layout'] ) ? sanitize_key( (string) $settings['layout'] ) : 'full';
		$full_btn = isset( $settings['button_full'] ) && 'yes' === $settings['button_full'];
		$preview  = isset( $settings['show_preview'] ) && 'yes' === $settings['show_preview'];
		$active   = $this->ltxe_form_plugin_active();
		$tag      = $this->ltxe_form_tag();
		$code     = Form_Styler_Sanitize::shortcode( isset( $settings['shortcode'] ) ? $settings['shortcode'] : '', $tag );

		$classes = array(
			'ltxe-form-styler',
			'ltxe-form-styler--' . $layout,
		);
		if ( $full_btn ) {
			$classes[] = 'ltxe-form-styler--btn-full';
		}

		echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';

		if ( ! $active ) {
			echo '<p class="ltxe-form-styler__notice" role="status">' . esc_html( $this->ltxe_missing_plugin_notice() ) . '</p>';
		}

		$in_editor = false;
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->editor ) ) {
			$in_editor = (bool) \Elementor\Plugin::$instance->editor->is_edit_mode();
		}

		if ( $active && '' !== $code ) {
			echo wp_kses( do_shortcode( $code ), $this->ltxe_form_kses() );
		} elseif ( $preview || ( ! $active && $in_editor ) ) {
			$this->render_preview_form();
		}

		echo '</div>';
	}

	/**
	 * Allowed tags for CF7 / WPForms markup.
	 *
	 * @return array<string,array<string,bool>>
	 */
	private function ltxe_form_kses() {
		$global = array(
			'id'          => true,
			'class'       => true,
			'role'        => true,
			'aria-hidden' => true,
			'aria-label'  => true,
			'style'       => true,
			'data-*'      => true,
		);

		return array(
			'form'     => array_merge(
				$global,
				array(
					'action'       => true,
					'method'       => true,
					'novalidate'   => true,
					'enctype'      => true,
					'name'         => true,
					'target'       => true,
					'autocomplete' => true,
				)
			),
			'input'    => array_merge(
				$global,
				array(
					'type'         => true,
					'name'         => true,
					'value'        => true,
					'placeholder'  => true,
					'required'     => true,
					'disabled'     => true,
					'checked'      => true,
					'maxlength'    => true,
					'size'         => true,
					'min'          => true,
					'max'          => true,
					'step'         => true,
					'autocomplete' => true,
					'aria-invalid' => true,
					'aria-required'=> true,
				)
			),
			'textarea' => array_merge(
				$global,
				array(
					'name'         => true,
					'placeholder'  => true,
					'required'     => true,
					'rows'         => true,
					'cols'         => true,
					'maxlength'    => true,
					'aria-invalid' => true,
					'aria-required'=> true,
				)
			),
			'select'   => array_merge(
				$global,
				array(
					'name'     => true,
					'required' => true,
					'multiple' => true,
				)
			),
			'option'   => array(
				'value'    => true,
				'selected' => true,
				'disabled' => true,
			),
			'button'   => array_merge(
				$global,
				array(
					'type'     => true,
					'name'     => true,
					'value'    => true,
					'disabled' => true,
				)
			),
			'label'    => array_merge( $global, array( 'for' => true ) ),
			'span'     => $global,
			'div'      => $global,
			'p'        => $global,
			'br'       => array(),
			'strong'   => $global,
			'em'       => $global,
			'a'        => array_merge( $global, array( 'href' => true, 'target' => true, 'rel' => true ) ),
			'fieldset' => $global,
			'legend'   => $global,
			'ul'       => $global,
			'ol'       => $global,
			'li'       => $global,
		);
	}

	/**
	 * Sample form for styling / e2e when the form plugin is not active.
	 *
	 * @return void
	 */
	private function render_preview_form() {
		$id      = $this->get_id();
		$is_cf7  = ( 'ltxe-cf7-styler' === $this->get_name() );
		$email   = $is_cf7 ? 'not-an-email' : 'jordan@studiolane.com';
		$message = __( 'We would like this form to match the rest of the site.', 'landtech-extras-for-elementor' );
		?>
		<form class="ltxe-form-styler__preview" novalidate>
			<p class="ltxe-form-styler__field">
				<label for="ltxe-fs-name-<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Name', 'landtech-extras-for-elementor' ); ?></label>
				<input id="ltxe-fs-name-<?php echo esc_attr( $id ); ?>" type="text" name="ltxe-fs-name" value="<?php echo esc_attr__( 'Jordan Lee', 'landtech-extras-for-elementor' ); ?>" required />
			</p>
			<p class="ltxe-form-styler__field<?php echo $is_cf7 ? ' ltxe-form-styler__field--invalid' : ''; ?>">
				<label for="ltxe-fs-email-<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Email', 'landtech-extras-for-elementor' ); ?></label>
				<input id="ltxe-fs-email-<?php echo esc_attr( $id ); ?>" type="text" name="ltxe-fs-email" value="<?php echo esc_attr( $email ); ?>" required />
			</p>
			<p class="ltxe-form-styler__field">
				<label for="ltxe-fs-msg-<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Message', 'landtech-extras-for-elementor' ); ?></label>
				<textarea id="ltxe-fs-msg-<?php echo esc_attr( $id ); ?>" name="ltxe-fs-message" required><?php echo esc_textarea( $message ); ?></textarea>
			</p>
			<p class="ltxe-form-styler__error"<?php echo $is_cf7 ? '' : ' hidden'; ?>><?php esc_html_e( 'Enter a valid email address.', 'landtech-extras-for-elementor' ); ?></p>
			<p class="ltxe-form-styler__success"<?php echo $is_cf7 ? ' hidden' : ''; ?>><?php esc_html_e( 'Thanks — your message is ready to send.', 'landtech-extras-for-elementor' ); ?></p>
			<p>
				<button type="submit" class="ltxe-form-styler__submit"><?php esc_html_e( 'Send', 'landtech-extras-for-elementor' ); ?></button>
			</p>
		</form>
		<?php
	}
}
