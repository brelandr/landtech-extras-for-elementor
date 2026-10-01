<?php
/**
 * Tags cloud sphere widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\TagsCloudSphere\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.1.0
 */
class Tags_Cloud_Sphere extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-tags-cloud-sphere';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Tags Cloud Sphere', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-tags';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'tags', 'cloud', 'sphere', 'taxonomy' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-tags-cloud-sphere' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-tags-cloud-sphere' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_tags',
			array(
				'label' => __( 'Tags', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'taxonomy',
			array(
				'label'   => __( 'Taxonomy', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post_tag',
				'options' => array(
					'post_tag' => __( 'Tags', 'landtech-extras-for-elementor' ),
					'category' => __( 'Categories', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->add_control(
			'max',
			array(
				'label'   => __( 'Max tags', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 24,
				'min'     => 4,
				'max'     => 80,
			)
		);
		$this->add_control(
			'radius',
			array(
				'label'   => __( 'Sphere radius (px)', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 140,
			)
		);
		$this->add_control(
			'auto_rotate',
			array(
				'label'        => __( 'Auto-rotate', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'pause_hover',
			array(
				'label'        => __( 'Pause on hover', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$s    = $this->get_settings_for_display();
		$tax  = isset( $s['taxonomy'] ) ? sanitize_key( (string) $s['taxonomy'] ) : 'post_tag';
		$max  = isset( $s['max'] ) ? max( 4, absint( $s['max'] ) ) : 24;
		$terms = get_terms(
			array(
				'taxonomy'   => $tax,
				'hide_empty' => true,
				'number'     => $max,
			)
		);
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			$terms = array();
		}
		$cfg = array(
			'radius'     => isset( $s['radius'] ) ? absint( $s['radius'] ) : 140,
			'auto'       => isset( $s['auto_rotate'] ) && 'yes' === $s['auto_rotate'],
			'pauseHover' => isset( $s['pause_hover'] ) && 'yes' === $s['pause_hover'],
		);
		echo '<div class="ltxe-sphere" data-ltxe-sphere="' . esc_attr( wp_json_encode( $cfg ) ) . '">';
		if ( empty( $terms ) ) {
			$fallback = array( 'Elementor', 'Widgets', 'Gallery', 'Forms', 'Blog', 'Design', 'Layout', 'Motion' );
			foreach ( $fallback as $label ) {
				echo '<a class="ltxe-sphere__tag" href="#">' . esc_html( $label ) . '</a>';
			}
		} else {
			foreach ( $terms as $term ) {
				echo '<a class="ltxe-sphere__tag" href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
			}
		}
		echo '</div>';
	}
}
