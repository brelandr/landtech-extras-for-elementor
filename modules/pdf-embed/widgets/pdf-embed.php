<?php
/**
 * PDF embed widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\PdfEmbed\Widgets;

use LandTechExtras\Base\Extras_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.1.0
 */
class Pdf_Embed extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-pdf-embed';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'PDF Embed', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-document-file';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'pdf', 'document', 'embed', 'viewer' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-pdf-embed' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-pdf-embed' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_pdf',
			array(
				'label' => __( 'PDF', 'landtech-extras-for-elementor' ),
			)
		);
		$this->add_control(
			'pdf_file',
			array(
				'label' => __( 'PDF file', 'landtech-extras-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$this->add_control(
			'pdf_url',
			array(
				'label'       => __( 'PDF URL', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'https://',
			)
		);
		$this->add_control(
			'viewer',
			array(
				'label'   => __( 'Viewer', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'native',
				'options' => array(
					'native' => __( 'Browser native (iframe)', 'landtech-extras-for-elementor' ),
					'pdfjs'  => __( 'Custom toolbar + iframe', 'landtech-extras-for-elementor' ),
				),
			)
		);
		$this->add_responsive_control(
			'height',
			array(
				'label'      => __( 'Height', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'default'    => array(
					'size' => 560,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-pdf__frame' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->add_control(
			'show_nav',
			array(
				'label'        => __( 'Page navigation', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'show_zoom',
			array(
				'label'        => __( 'Zoom controls', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'show_download',
			array(
				'label'        => __( 'Download button', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->add_control(
			'show_print',
			array(
				'label'        => __( 'Print button', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);
		$this->add_control(
			'initial_page',
			array(
				'label'   => __( 'Initial page', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 1,
				'min'     => 1,
			)
		);
		$this->end_controls_section();
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$s    = $this->get_settings_for_display();
		$url  = ( isset( $s['pdf_file']['url'] ) && is_string( $s['pdf_file']['url'] ) ) ? $s['pdf_file']['url'] : '';
		if ( '' === $url && ! empty( $s['pdf_url'] ) ) {
			$url = esc_url_raw( (string) $s['pdf_url'] );
		}
		if ( '' === $url ) {
			$url = 'https://mozilla.github.io/pdf.js/web/compressed.tracemonkey-pldi-09.pdf';
		}
		$page = isset( $s['initial_page'] ) ? max( 1, absint( $s['initial_page'] ) ) : 1;
		$src  = $url . '#page=' . $page;
		echo '<div class="ltxe-pdf" data-ltxe-pdf="' . esc_attr( wp_json_encode( array( 'url' => $url, 'page' => $page ) ) ) . '">';
		echo '<div class="ltxe-pdf__toolbar">';
		if ( isset( $s['show_nav'] ) && 'yes' === $s['show_nav'] ) {
			echo '<button type="button" class="ltxe-pdf__btn" data-pdf-act="prev">' . esc_html__( 'Previous', 'landtech-extras-for-elementor' ) . '</button>';
			echo '<span class="ltxe-pdf__page" data-pdf-page>' . esc_html( (string) $page ) . '</span>';
			echo '<button type="button" class="ltxe-pdf__btn" data-pdf-act="next">' . esc_html__( 'Next', 'landtech-extras-for-elementor' ) . '</button>';
		}
		if ( isset( $s['show_zoom'] ) && 'yes' === $s['show_zoom'] ) {
			echo '<button type="button" class="ltxe-pdf__btn" data-pdf-act="zin">' . esc_html__( 'Zoom in', 'landtech-extras-for-elementor' ) . '</button>';
			echo '<button type="button" class="ltxe-pdf__btn" data-pdf-act="zout">' . esc_html__( 'Zoom out', 'landtech-extras-for-elementor' ) . '</button>';
		}
		if ( isset( $s['show_download'] ) && 'yes' === $s['show_download'] ) {
			echo '<a class="ltxe-pdf__btn" download href="' . esc_url( $url ) . '">' . esc_html__( 'Download', 'landtech-extras-for-elementor' ) . '</a>';
		}
		if ( isset( $s['show_print'] ) && 'yes' === $s['show_print'] ) {
			echo '<button type="button" class="ltxe-pdf__btn" data-pdf-act="print">' . esc_html__( 'Print', 'landtech-extras-for-elementor' ) . '</button>';
		}
		echo '</div>';
		echo '<iframe class="ltxe-pdf__frame" src="' . esc_url( $src ) . '" title="' . esc_attr__( 'PDF document', 'landtech-extras-for-elementor' ) . '"></iframe>';
		echo '</div>';
	}
}
