<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Tabs\Widgets;

use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\TemplatesControl\Module as TemplatesControl;

use Elementor\Controls_Manager;
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
		return array( 'landtech-extras-tabs' );
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
				'description' => __( 'Optional anchor id (#tab-slug). Also honored as ?tab=slug.', 'landtech-extras-for-elementor' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

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

		/**
		 * Fires while the per-tab repeater is still open for new controls.
		 *
		 * This has to be an action on the repeater itself: `get_controls()` below freezes the
		 * field list, and Elementor offers no hook that can reach into another plugin's repeater
		 * after the fact.
		 *
		 * @since 2.5.9
		 *
		 * @param Repeater $repeater Repeater still accepting `add_control()` calls.
		 * @param Tabs     $widget   Widget instance registering the repeater.
		 */
		do_action( 'landtech_extras/tabs/register_repeater_controls', $repeater, $this );

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
			'horizontal'      => __( 'Horizontal', 'landtech-extras-for-elementor' ),
			'vertical'        => __( 'Vertical (tabs on left)', 'landtech-extras-for-elementor' ),
			'vertical-right'  => __( 'Vertical (tabs on right)', 'landtech-extras-for-elementor' ),
		);

		$this->add_responsive_control(
			'tabs_orientation',
			array(
				'label'        => __( 'Orientation', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'horizontal',
				'options'      => $orient_options,
				'prefix_class' => 'ltxe-tabs--orientation-',
			)
		);

		$this->add_control(
			'orientation',
			array(
				'label'       => __( 'Orientation (legacy)', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::HIDDEN,
				'default'     => 'horizontal',
			)
		);

		$this->add_control(
			'mobile_accordion',
			array(
				'label'        => __( 'Accordion on Mobile', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Below the accordion breakpoint, tabs collapse into a vertical accordion.', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'accordion_breakpoint',
			array(
				'label'     => __( 'Accordion Breakpoint', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'mobile',
				'options'   => array(
					'mobile' => __( 'Mobile (< 767px)', 'landtech-extras-for-elementor' ),
					'tablet' => __( 'Tablet (< 1024px)', 'landtech-extras-for-elementor' ),
				),
				'condition' => array(
					'mobile_accordion' => 'yes',
				),
			)
		);

		$this->add_control(
			'deep_link',
			array(
				'label'        => __( 'URL Deep Linking', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Updates the URL hash when a tab is clicked. Share a link that opens a specific tab directly.', 'landtech-extras-for-elementor' ),
			)
		);

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
	 * Tags permitted in a tab label.
	 *
	 * Post tags cover the markup an add-on needs for a badge or a font icon. Inline SVG is
	 * added on top because `wp_kses_post()` drops `<svg>` outright, and Elementor renders an
	 * uploaded SVG icon inline rather than as an `<img>`.
	 *
	 * @since 2.5.9
	 *
	 * @access protected
	 * @return array<string,array<string,bool>>
	 */
	protected function get_allowed_label_html() {

		$svg_globals = array(
			'class'           => true,
			'style'           => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'aria-hidden'     => true,
			'role'            => true,
			'focusable'       => true,
		);

		$svg = array(
			'svg'      => array_merge(
				$svg_globals,
				array(
					'xmlns'   => true,
					'viewbox' => true,
					'width'   => true,
					'height'  => true,
				)
			),
			'g'        => $svg_globals,
			'defs'     => $svg_globals,
			'title'    => $svg_globals,
			'path'     => array_merge( $svg_globals, array( 'd' => true ) ),
			'circle'   => array_merge( $svg_globals, array( 'cx' => true, 'cy' => true, 'r' => true ) ),
			'ellipse'  => array_merge( $svg_globals, array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ) ),
			'rect'     => array_merge( $svg_globals, array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true ) ),
			'line'     => array_merge( $svg_globals, array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ) ),
			'polygon'  => array_merge( $svg_globals, array( 'points' => true ) ),
			'polyline' => array_merge( $svg_globals, array( 'points' => true ) ),
			'use'      => array_merge( $svg_globals, array( 'href' => true, 'xlink:href' => true ) ),
		);

		return array_merge( wp_kses_allowed_html( 'post' ), $svg );
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

		$orientation = isset( $settings['tabs_orientation'] ) ? (string) $settings['tabs_orientation'] : 'horizontal';
		if ( '' === $orientation && ! empty( $settings['orientation'] ) ) {
			$orientation = (string) $settings['orientation'];
		}
		if ( ! in_array( $orientation, array( 'horizontal', 'vertical', 'vertical-right' ), true ) ) {
			$orientation = 'horizontal';
		}

		$wrapper_atts = array(
			'class' => array(
				'ltxe-tabs',
				'ltxe-tabs--' . $orientation,
			),
			'data-deep-link' => ( isset( $settings['deep_link'] ) && 'yes' === $settings['deep_link'] ) ? 'yes' : 'no',
			'data-mobile-accordion' => ( isset( $settings['mobile_accordion'] ) && 'yes' === $settings['mobile_accordion'] ) ? 'yes' : 'no',
			'data-accordion-breakpoint' => isset( $settings['accordion_breakpoint'] ) ? sanitize_key( $settings['accordion_breakpoint'] ) : 'mobile',
		);

		if ( isset( $settings['mobile_accordion'] ) && 'yes' === $settings['mobile_accordion'] ) {
			$bp = isset( $settings['accordion_breakpoint'] ) ? sanitize_key( $settings['accordion_breakpoint'] ) : 'mobile';
			$wrapper_atts['class'][] = 'ltxe-tabs--accordion-' . $bp;
		}

		/**
		 * Filters the outer wrapper attributes before they are rendered.
		 *
		 * Add-ons use this to swap the orientation class or add their own data attributes. The
		 * horizontal class above is the fallback, so a layout an add-on is no longer around to
		 * style degrades to the built-in one rather than a class with no stylesheet behind it.
		 *
		 * @since 2.5.9
		 *
		 * @param array<string,mixed> $wrapper_atts Render attributes keyed as Elementor expects.
		 * @param array<string,mixed> $settings     Resolved widget settings.
		 * @param Tabs                $widget       Widget instance being rendered.
		 */
		$wrapper_atts = apply_filters( 'landtech_extras/tabs/wrapper_attributes', $wrapper_atts, $settings, $this );

		if ( ! is_array( $wrapper_atts ) ) {
			$wrapper_atts = array( 'class' => array( 'ltxe-tabs', 'ltxe-tabs--horizontal' ) );
		}

		$this->add_render_attribute( 'wrapper', $wrapper_atts );

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
						/**
						 * Filters the inner HTML of a single tab button.
						 *
						 * Add-ons use this to wrap the title with an icon or trailing badge. The
						 * default is already escaped; the result is run through `wp_kses()` at
						 * the print site below because anything a filter returns is untrusted no
						 * matter what it started as.
						 *
						 * @since 2.5.9
						 *
						 * @param string              $label_html Escaped tab title.
						 * @param array<string,mixed> $item       Repeater row for this tab.
						 * @param int                 $index      Zero-based tab index.
						 * @param Tabs                $widget     Widget instance being rendered.
						 */
						$label_html = apply_filters(
							'landtech_extras/tabs/tab_label_html',
							esc_html( (string) ( $item['tab_title'] ?? '' ) ),
							$item,
							(int) $index,
							$this
						);

						echo wp_kses( $label_html, $this->get_allowed_label_html() );
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
					?>
					<button
						type="button"
						class="ltxe-tabs__accordion-title"
						id="<?php echo esc_attr( $tab_id ); ?>-accordion"
						aria-expanded="<?php echo esc_attr( 0 === (int) $index ? 'true' : 'false' ); ?>"
						aria-controls="<?php echo esc_attr( $tab_id ); ?>-panel"
						data-tab-index="<?php echo esc_attr( (string) $index ); ?>"
					>
						<?php echo esc_html( (string) ( $item['tab_title'] ?? '' ) ); ?>
					</button>
					<div
						class="ltxe-tabs__panel"
						id="<?php echo esc_attr( $tab_id ); ?>-panel"
						role="tabpanel"
						aria-labelledby="<?php echo esc_attr( $tab_id ); ?>-label"
						<?php echo 0 === (int) $index ? '' : 'hidden'; ?>
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
