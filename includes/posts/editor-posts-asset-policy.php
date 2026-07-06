<?php
/**
 * Elementor editor: when Posts loop Isotope / filtery assets are needed for the open document.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether saved Element settings for a Posts Extra instance require the editor Isotope bundle.
 *
 * Mirrors {@see landtech_extras_posts_extra_widget_needs_isotope_assets()} plus the filter-only path
 * from {@see assets/js/posts-loop-isotope-editor.js} (filtery when layout is default).
 *
 * @since 2.2.57
 *
 * @param array $settings Elementor widget settings array.
 * @return bool
 */
function landtech_extras_posts_extra_element_settings_need_loop_isotope_editor( $settings ) {
	if ( ! is_array( $settings ) ) {
		return false;
	}

	$skin = isset( $settings['_skin'] ) ? sanitize_key( (string) $settings['_skin'] ) : 'classic';

	if ( 'carousel' === $skin ) {
		return false;
	}

	$filters_key = $skin . '_filters';
	if ( isset( $settings[ $filters_key ] ) && 'yes' === (string) $settings[ $filters_key ] ) {
		return true;
	}

	$layout_key = landtech_extras_posts_extra_widget_layout_setting_key( $skin );
	if ( '' === $layout_key ) {
		return false;
	}

	$max_cols = 1;
	foreach ( array( 'columns', 'columns_tablet', 'columns_mobile' ) as $ck ) {
		if ( isset( $settings[ $ck ] ) && '' !== $settings[ $ck ] ) {
			$max_cols = max( $max_cols, (int) $settings[ $ck ] );
		}
	}

	if ( $max_cols < 2 ) {
		return false;
	}

	$layout = isset( $settings[ $layout_key ] ) ? (string) $settings[ $layout_key ] : 'default';

	return ( 'default' !== $layout && '' !== $layout );
}

/**
 * Walk Elementor elements JSON for any Posts Extra that needs the loop Isotope editor script.
 *
 * @since 2.2.57
 *
 * @param array $elements Elementor elements tree.
 * @return bool
 */
function landtech_extras_elementor_elements_data_need_posts_loop_isotope_editor( $elements ) {
	if ( ! is_array( $elements ) ) {
		return false;
	}

	foreach ( $elements as $el ) {
		if ( ! is_array( $el ) ) {
			continue;
		}

		if ( ! empty( $el['widgetType'] ) && 'posts-extra' === $el['widgetType'] ) {
			$settings = isset( $el['settings'] ) && is_array( $el['settings'] ) ? $el['settings'] : array();

			if ( landtech_extras_posts_extra_element_settings_need_loop_isotope_editor( $settings ) ) {
				return true;
			}
		}

		if ( ! empty( $el['elements'] ) && landtech_extras_elementor_elements_data_need_posts_loop_isotope_editor( $el['elements'] ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Whether saved settings use the Packery-based grid layout.
 *
 * @since 2.2.60
 *
 * @param array $settings Elementor widget settings array.
 * @return bool
 */
function landtech_extras_posts_extra_element_settings_use_packery_layout( $settings ) {
	if ( ! is_array( $settings ) ) {
		return false;
	}

	$skin = isset( $settings['_skin'] ) ? sanitize_key( (string) $settings['_skin'] ) : 'classic';

	if ( 'carousel' === $skin ) {
		return false;
	}

	$layout_key = landtech_extras_posts_extra_widget_layout_setting_key( $skin );
	if ( '' === $layout_key ) {
		return false;
	}

	$layout = isset( $settings[ $layout_key ] ) ? (string) $settings[ $layout_key ] : 'default';

	return 'packery' === sanitize_key( $layout );
}

/**
 * Walk Elementor elements JSON for Posts Extra instances using Packery layout (editor script deps).
 *
 * @since 2.2.60
 *
 * @param array $elements Elementor elements tree.
 * @return bool
 */
function landtech_extras_elementor_elements_data_include_packery_posts_layout( $elements ) {
	if ( ! is_array( $elements ) ) {
		return false;
	}

	foreach ( $elements as $el ) {
		if ( ! is_array( $el ) ) {
			continue;
		}

		if ( ! empty( $el['widgetType'] ) && 'posts-extra' === $el['widgetType'] ) {
			$settings = isset( $el['settings'] ) && is_array( $el['settings'] ) ? $el['settings'] : array();

			if ( landtech_extras_posts_extra_element_settings_use_packery_layout( $settings ) ) {
				return true;
			}
		}

		if ( ! empty( $el['elements'] ) && landtech_extras_elementor_elements_data_include_packery_posts_layout( $el['elements'] ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Whether the open Elementor document includes Packery Posts Extra layout (for editor Isotope extensions).
 *
 * Fail-open (true) when the document cannot be resolved safely.
 *
 * @since 2.2.60
 *
 * @return bool
 */
function landtech_extras_editor_document_includes_packery_posts_layout() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return true;
	}

	$document = null;

	if ( method_exists( \Elementor\Plugin::$instance->documents, 'get_current' ) ) {
		$document = \Elementor\Plugin::$instance->documents->get_current();
	}

	if ( ! $document || ! method_exists( $document, 'get_elements_data' ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Editor screen context only; read-only document lookup.
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

	return landtech_extras_elementor_elements_data_include_packery_posts_layout( $elements );
}

/**
 * Whether the editor should enqueue `ee-posts-loop-isotope-editor` for the current document.
 *
 * Fail-open (true) when the document cannot be resolved safely.
 *
 * @since 2.2.57
 *
 * @return bool
 */
function landtech_extras_editor_should_enqueue_posts_loop_isotope_editor_script() {
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

	return landtech_extras_elementor_elements_data_need_posts_loop_isotope_editor( $elements );
}
