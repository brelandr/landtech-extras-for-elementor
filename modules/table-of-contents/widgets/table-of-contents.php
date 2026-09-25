<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\TableOfContents\Widgets;

use LandTechExtras\Base\Extras_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EE Table of Contents widget.
 *
 * @since 3.0.0
 */
class Table_Of_Contents extends Extras_Widget {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'ee-table-of-contents';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Table of Contents', 'landtech-extras-for-elementor' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	/**
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-table-of-contents' );
	}

	/**
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-table-of-contents' );
	}

	/**
	 * @return void
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'scope',
			array(
				'label'   => __( 'Scan scope', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'page',
				'options' => array(
					'page'    => __( 'Current page content', 'landtech-extras-for-elementor' ),
					'wrapper' => __( 'Nearest Elementor container', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'heading_levels',
			array(
				'label'       => __( 'Heading levels', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'default'     => array( 'h2', 'h3', 'h4' ),
				'options'     => array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
			)
		);

		$this->add_control(
			'exclude_selector',
			array(
				'label'       => __( 'Exclude selector', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'CSS selector for headings to ignore (e.g. .no-toc).', 'landtech-extras-for-elementor' ),
			)
		);

		$premium = function_exists( 'landtech_extras_feature_enabled' ) && landtech_extras_feature_enabled( 'premium_toc_widget', false );

		if ( $premium ) {
			$this->add_control(
				'layout_mode',
				array(
					'label'   => __( 'Layout', 'landtech-extras-for-elementor' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'inline',
					'options' => array(
						'inline'       => __( 'Inline', 'landtech-extras-for-elementor' ),
						'sticky-left'  => __( 'Sticky left', 'landtech-extras-for-elementor' ),
						'sticky-right' => __( 'Sticky right', 'landtech-extras-for-elementor' ),
					),
				)
			);

			$this->add_control(
				'show_progress',
				array(
					'label'        => __( 'Scroll progress bar', 'landtech-extras-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
				)
			);

			$this->add_control(
				'collapsible',
				array(
					'label'        => __( 'Collapsible nested sections', 'landtech-extras-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'highlight_active',
				array(
					'label'        => __( 'Highlight active heading', 'landtech-extras-for-elementor' ),
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'List', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'list_typography',
				'selector' => '{{WRAPPER}} .ltxe-toc__list a',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$premium  = function_exists( 'landtech_extras_feature_enabled' ) && landtech_extras_feature_enabled( 'premium_toc_widget', false );

		$levels = isset( $settings['heading_levels'] ) && is_array( $settings['heading_levels'] )
			? array_map( 'sanitize_key', $settings['heading_levels'] )
			: array( 'h2', 'h3', 'h4' );

		$this->add_render_attribute(
			'wrapper',
			array(
				'class' => array(
					'ltxe-toc',
					$premium && ! empty( $settings['layout_mode'] ) ? 'ltxe-toc--' . sanitize_html_class( (string) $settings['layout_mode'] ) : 'ltxe-toc--inline',
				),
				'data-scope'            => esc_attr( sanitize_key( (string) ( $settings['scope'] ?? 'page' ) ) ),
				'data-levels'           => esc_attr( wp_json_encode( array_values( $levels ) ) ),
				'data-exclude'          => esc_attr( (string) ( $settings['exclude_selector'] ?? '' ) ),
				'data-progress'         => ( $premium && ! empty( $settings['show_progress'] ) && 'yes' === $settings['show_progress'] ) ? '1' : '0',
				'data-collapsible'      => ( $premium && ! empty( $settings['collapsible'] ) && 'yes' === $settings['collapsible'] ) ? '1' : '0',
				'data-highlight-active' => ( $premium && ! empty( $settings['highlight_active'] ) && 'yes' === $settings['highlight_active'] ) ? '1' : '0',
			)
		);
		?>
		<nav <?php $this->print_render_attribute_string( 'wrapper' ); ?> aria-label="<?php esc_attr_e( 'Table of contents', 'landtech-extras-for-elementor' ); ?>">
			<?php if ( $premium && ! empty( $settings['show_progress'] ) && 'yes' === $settings['show_progress'] ) : ?>
				<div class="ltxe-toc__progress" aria-hidden="true"><span class="ltxe-toc__progress-bar"></span></div>
			<?php endif; ?>
			<ol class="ltxe-toc__list"></ol>
		</nav>
		<?php
	}
}
