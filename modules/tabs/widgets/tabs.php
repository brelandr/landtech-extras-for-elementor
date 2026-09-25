<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Tabs\Widgets;

use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\TemplatesControl\Module as TemplatesControl;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EE Tabs widget.
 *
 * @since 3.0.0
 */
class Tabs extends Extras_Widget {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ee-tabs';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Tabs', 'landtech-extras-for-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-tabs';
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		$deps = array( 'landtech-extras-tabs' );
		if ( function_exists( 'landtech_extras_feature_enabled' ) && landtech_extras_feature_enabled( 'premium_tabs_widget', false ) ) {
			$deps[] = 'landtech-extras-anime-helpers';
		}
		return $deps;
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-tabs' );
	}

	/**
	 * @return void
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_tabs',
			array(
				'label' => __( 'Tabs', 'landtech-extras-for-elementor' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'tab_title',
			array(
				'label'   => __( 'Title', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Tab', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'tab_slug',
			array(
				'label'       => __( 'Slug (deep link)', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Optional anchor id (#tab-slug). Premium deep links also honor ?tab=slug.', 'landtech-extras-for-elementor' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		if ( function_exists( 'landtech_extras_feature_enabled' ) && landtech_extras_feature_enabled( 'premium_tabs_widget', false ) ) {
			$repeater->add_control(
				'tab_icon',
				array(
					'label' => __( 'Icon', 'landtech-extras-for-elementor' ),
					'type'  => Controls_Manager::ICONS,
				)
			);

			$repeater->add_control(
				'tab_badge',
				array(
					'label' => __( 'Badge', 'landtech-extras-for-elementor' ),
					'type'  => Controls_Manager::TEXT,
				)
			);
		}

		$repeater->add_control(
			'tab_content',
			array(
				'label'   => __( 'Content', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => __( 'Tab content goes here.', 'landtech-extras-for-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		if ( class_exists( '\LandTechExtras\Modules\TemplatesControl\Module', false ) ) {
			TemplatesControl::add_controls(
				$repeater,
				array(
					'prefix' => 'content_',
				)
			);
		}

		$this->add_control(
			'tabs',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'tab_title'   => __( 'Tab 1', 'landtech-extras-for-elementor' ),
						'tab_content' => __( 'First tab content.', 'landtech-extras-for-elementor' ),
					),
					array(
						'tab_title'   => __( 'Tab 2', 'landtech-extras-for-elementor' ),
						'tab_content' => __( 'Second tab content.', 'landtech-extras-for-elementor' ),
					),
				),
				'title_field' => '{{{ tab_title }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'landtech-extras-for-elementor' ),
			)
		);

		$orient_options = array(
			'horizontal' => __( 'Horizontal', 'landtech-extras-for-elementor' ),
		);
		if ( function_exists( 'landtech_extras_feature_enabled' ) && landtech_extras_feature_enabled( 'premium_tabs_widget', false ) ) {
			$orient_options['vertical'] = __( 'Vertical', 'landtech-extras-for-elementor' );
		}

		$this->add_control(
			'orientation',
			array(
				'label'   => __( 'Orientation', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => $orient_options,
			)
		);

		if ( function_exists( 'landtech_extras_feature_enabled' ) && landtech_extras_feature_enabled( 'premium_tabs_widget', false ) ) {
			$this->add_control(
				'accordion_mobile',
				array(
					'label'        => __( 'Accordion on mobile', 'landtech-extras-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
				)
			);

			$this->add_control(
				'animate_transitions',
				array(
					'label'        => __( 'Animated transitions', 'landtech-extras-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tabs',
			array(
				'label' => __( 'Tab labels', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tab_typography',
				'selector' => '{{WRAPPER}} .ltxe-tabs__tab',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'tab_border',
				'selector' => '{{WRAPPER}} .ltxe-tabs__tab',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$tabs     = isset( $settings['tabs'] ) && is_array( $settings['tabs'] ) ? $settings['tabs'] : array();

		if ( empty( $tabs ) ) {
			return;
		}

		$premium   = function_exists( 'landtech_extras_feature_enabled' ) && landtech_extras_feature_enabled( 'premium_tabs_widget', false );
		$orient    = ( $premium && 'vertical' === ( $settings['orientation'] ?? '' ) ) ? 'vertical' : 'horizontal';
		$accordion = $premium && ! empty( $settings['accordion_mobile'] ) && 'yes' === $settings['accordion_mobile'];
		$animate   = $premium && ! empty( $settings['animate_transitions'] ) && 'yes' === $settings['animate_transitions'];

		$this->add_render_attribute(
			'wrapper',
			array(
				'class' => array(
					'ltxe-tabs',
					'ltxe-tabs--' . $orient,
				),
				'data-accordion-mobile' => $accordion ? '1' : '0',
				'data-animate'            => $animate ? '1' : '0',
			)
		);

		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="ltxe-tabs__list" role="tablist">
				<?php
				foreach ( $tabs as $index => $item ) {
					$tab_id = 'ltxe-tab-' . $this->get_id() . '-' . $index;
					if ( ! empty( $item['tab_slug'] ) ) {
						$tab_id = sanitize_title( (string) $item['tab_slug'] );
					}
					$selected = 0 === (int) $index ? 'true' : 'false';
					?>
					<button
						type="button"
						class="ltxe-tabs__tab"
						id="<?php echo esc_attr( $tab_id ); ?>-label"
						role="tab"
						aria-selected="<?php echo esc_attr( $selected ); ?>"
						aria-controls="<?php echo esc_attr( $tab_id ); ?>-panel"
						data-tab-index="<?php echo esc_attr( (string) $index ); ?>"
						data-tab-slug="<?php echo esc_attr( sanitize_title( (string) ( $item['tab_slug'] ?? $tab_id ) ) ); ?>"
					>
						<?php
						if ( $premium && ! empty( $item['tab_icon']['value'] ) ) {
							Icons_Manager::render_icon( $item['tab_icon'], array( 'aria-hidden' => 'true' ) );
						}
						echo esc_html( (string) ( $item['tab_title'] ?? '' ) );
						if ( $premium && ! empty( $item['tab_badge'] ) ) {
							echo '<span class="ltxe-tabs__badge">' . esc_html( (string) $item['tab_badge'] ) . '</span>';
						}
						?>
					</button>
					<?php
				}
				?>
			</div>
			<div class="ltxe-tabs__panels">
				<?php
				foreach ( $tabs as $index => $item ) {
					$tab_id   = 'ltxe-tab-' . $this->get_id() . '-' . $index;
					if ( ! empty( $item['tab_slug'] ) ) {
						$tab_id = sanitize_title( (string) $item['tab_slug'] );
					}
					$hidden   = 0 === (int) $index ? 'false' : 'true';
					?>
					<div
						class="ltxe-tabs__panel"
						id="<?php echo esc_attr( $tab_id ); ?>-panel"
						role="tabpanel"
						aria-labelledby="<?php echo esc_attr( $tab_id ); ?>-label"
						hidden="<?php echo esc_attr( $hidden ); ?>"
						data-tab-index="<?php echo esc_attr( (string) $index ); ?>"
					>
						<?php
						$template_key = '';
						if ( ! empty( $item['content_template_type'] ) ) {
							$template_key = 'content_' . $item['content_template_type'] . '_template_id';
						}
						if ( $template_key && ! empty( $item[ $template_key ] ) && class_exists( '\LandTechExtras\Modules\TemplatesControl\Module', false ) ) {
							TemplatesControl::render_template_content( $item[ $template_key ], $this );
						} elseif ( ! empty( $item['tab_content'] ) ) {
							echo wp_kses_post( (string) $item['tab_content'] );
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}
}
