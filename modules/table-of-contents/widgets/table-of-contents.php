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

		$levels = isset( $settings['heading_levels'] ) && is_array( $settings['heading_levels'] )
			? array_map( 'sanitize_key', $settings['heading_levels'] )
			: array( 'h2', 'h3', 'h4' );

		$wrapper_atts = array(
			'class'        => array(
				'ltxe-toc',
				'ltxe-toc--inline',
			),
			'data-scope'   => esc_attr( sanitize_key( (string) ( $settings['scope'] ?? 'page' ) ) ),
			'data-levels'  => esc_attr( wp_json_encode( array_values( $levels ) ) ),
			'data-exclude' => esc_attr( (string) ( $settings['exclude_selector'] ?? '' ) ),
		);

		/**
		 * Filters the `<nav>` attributes before they are rendered.
		 *
		 * Add-ons use this to swap the layout class or add their own data attributes. The inline
		 * class above is the fallback, so a layout an add-on is no longer around to style degrades
		 * to the built-in one rather than a class with no stylesheet behind it.
		 *
		 * @since 2.5.9
		 *
		 * @param array<string,mixed> $wrapper_atts Render attributes keyed as Elementor expects.
		 * @param array<string,mixed> $settings     Resolved widget settings.
		 * @param Table_Of_Contents   $widget       Widget instance being rendered.
		 */
		$wrapper_atts = apply_filters( 'landtech_extras/toc/wrapper_attributes', $wrapper_atts, $settings, $this );

		if ( ! is_array( $wrapper_atts ) ) {
			$wrapper_atts = array( 'class' => array( 'ltxe-toc', 'ltxe-toc--inline' ) );
		}

		$this->add_render_attribute( 'wrapper', $wrapper_atts );
		?>
		<nav <?php $this->print_render_attribute_string( 'wrapper' ); ?> aria-label="<?php esc_attr_e( 'Table of contents', 'landtech-extras-for-elementor' ); ?>">
			<?php
			/**
			 * Fires inside the nav, immediately before the (empty) list the script fills in.
			 *
			 * Add-ons print their own chrome here — a scroll progress bar, for instance. Anything
			 * echoed by a callback is that callback's responsibility to escape.
			 *
			 * @since 2.5.9
			 *
			 * @param array<string,mixed> $settings Resolved widget settings.
			 * @param Table_Of_Contents   $widget   Widget instance being rendered.
			 */
			do_action( 'landtech_extras/toc/before_list', $settings, $this );
			?>
			<ol class="ltxe-toc__list"></ol>
		</nav>
		<?php
	}
}
