<?php
/**
 * Elementor editor: load Table CSV import helper only when the document uses the Table widget.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Walk Elementor elements JSON for a widget type slug.
 *
 * @since 2.3.0
 *
 * @param array  $elements     Elementor elements tree.
 * @param string $widget_type  Widget type slug (for example `table`).
 * @return bool
 */
function landtech_extras_elementor_elements_data_include_widget_type( $elements, $widget_type ) {
	if ( ! is_array( $elements ) || '' === (string) $widget_type ) {
		return false;
	}

	foreach ( $elements as $el ) {
		if ( ! is_array( $el ) ) {
			continue;
		}

		if ( ! empty( $el['widgetType'] ) && (string) $widget_type === (string) $el['widgetType'] ) {
			return true;
		}

		if ( ! empty( $el['elements'] ) && landtech_extras_elementor_elements_data_include_widget_type( $el['elements'], $widget_type ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Whether the editor should enqueue the Table CSV paste helper for the current document.
 *
 * Fail-open (true) when the document cannot be resolved safely (matches Posts Isotope editor policy).
 *
 * @since 2.3.0
 *
 * @return bool
 */
function landtech_extras_editor_should_enqueue_table_csv_editor_script() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return true;
	}

	$document = null;

	if ( method_exists( \Elementor\Plugin::$instance->documents, 'get_current' ) ) {
		$document = \Elementor\Plugin::$instance->documents->get_current();
	}

	if ( ! $document || ! method_exists( $document, 'get_elements_data' ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Editor screen context only; value used for read-only document lookup.
		$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

		if ( $post_id ) {
			$document = \Elementor\Plugin::$instance->documents->get( $post_id );
		}
	}

	if ( ! $document || ! method_exists( $document, 'get_elements_data' ) ) {
		return true;
	}

	$elements = $document->get_elements_data();

	if ( ! is_array( $elements ) || array() === $elements ) {
		return false;
	}

	return landtech_extras_elementor_elements_data_include_widget_type( $elements, 'table' );
}
