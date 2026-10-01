<?php
/**
 * AI widget CSS ID (semantic slug) via the WordPress AI Client, then a local slug.
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the WordPress AI Client can run.
 *
 * @return bool
 */
function landtech_extras_ai_widget_label_client_available() {
	return function_exists( 'wp_ai_client_prompt' ) && function_exists( 'wp_supports_ai' ) && wp_supports_ai();
}

/**
 * BYOK OpenAI key: ltxe_openai_key, then APIs-tab openai_api_key, then wp-config constants.
 *
 * @return string
 */
function landtech_extras_get_openai_byok_key() {
	if ( defined( 'LTXE_OPENAI_KEY' ) && is_string( LTXE_OPENAI_KEY ) && '' !== LTXE_OPENAI_KEY ) {
		return LTXE_OPENAI_KEY;
	}
	if ( defined( 'LANDTECH_EXTRAS_OPENAI_KEY' ) && is_string( LANDTECH_EXTRAS_OPENAI_KEY ) && '' !== LANDTECH_EXTRAS_OPENAI_KEY ) {
		return LANDTECH_EXTRAS_OPENAI_KEY;
	}

	$stored = get_option( 'ltxe_openai_key', '' );
	if ( is_string( $stored ) && '' !== trim( $stored ) ) {
		return $stored;
	}

	$apis = get_option( 'landtech_extras_apis', array() );
	if ( is_array( $apis ) && ! empty( $apis['openai_api_key'] ) && is_string( $apis['openai_api_key'] ) ) {
		return $apis['openai_api_key'];
	}

	return '';
}

/**
 * Keep ltxe_openai_key in sync when the APIs tab OpenAI key is saved.
 *
 * @param mixed $old_value Previous option.
 * @param mixed $value     New option.
 * @return void
 */
function landtech_extras_sync_ltxe_openai_key( $old_value, $value ) {
	unset( $old_value );
	if ( ! is_array( $value ) || empty( $value['openai_api_key'] ) || ! is_string( $value['openai_api_key'] ) ) {
		return;
	}
	update_option( 'ltxe_openai_key', $value['openai_api_key'], false );
}
add_action( 'update_option_landtech_extras_apis', 'landtech_extras_sync_ltxe_openai_key', 10, 2 );
add_action( 'add_option_landtech_extras_apis', 'landtech_extras_sync_ltxe_openai_key', 10, 2 );

/**
 * Register landtech-extras/v1/ai/widget-label.
 *
 * @return void
 */
function landtech_extras_register_ai_widget_label_rest() {
	register_rest_route(
		'landtech-extras/v1',
		'/ai/widget-label',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'landtech_extras_ai_widget_label_callback',
			'permission_callback' => 'landtech_extras_ai_widget_label_permission',
			'args'                => array(
				'widget_type' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_key',
				),
				'content'     => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_textarea_field',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'landtech_extras_register_ai_widget_label_rest' );

/**
 * Editors only.
 *
 * @return bool
 */
function landtech_extras_ai_widget_label_permission() {
	return current_user_can( 'edit_posts' );
}

/**
 * Build a CSS-safe slug from text.
 *
 * @param string $raw Raw suggestion.
 * @return string
 */
function landtech_extras_ai_widget_label_slugify( $raw ) {
	$slug = sanitize_title( (string) $raw );
	$slug = preg_replace( '/[^a-z0-9\-]/', '', $slug );
	$slug = trim( (string) $slug, '-' );
	if ( strlen( $slug ) > 40 ) {
		$slug = substr( $slug, 0, 40 );
		$slug = trim( $slug, '-' );
	}
	if ( '' === $slug ) {
		return '';
	}
	if ( preg_match( '/^[0-9]/', $slug ) ) {
		$slug = 'ltxe-' . $slug;
	}
	return $slug;
}

/**
 * Local heuristic slug when no AI provider is configured.
 *
 * @param string $widget_type Widget type.
 * @param string $content     Visible content.
 * @return string
 */
function landtech_extras_ai_widget_label_local_slug( $widget_type, $content ) {
	$from_content = landtech_extras_ai_widget_label_slugify( $content );
	if ( '' !== $from_content ) {
		return $from_content;
	}
	$from_type = landtech_extras_ai_widget_label_slugify( str_replace( array( 'ltxe-', 'ee-' ), '', $widget_type ) );
	if ( '' !== $from_type ) {
		return $from_type;
	}
	return 'ltxe-widget';
}

/**
 * Ask the WordPress AI Client for a CSS id slug.
 *
 * @param string $widget_type Widget type.
 * @param string $content     Visible content.
 * @return string|WP_Error
 */
function landtech_extras_ai_widget_label_wp_client( $widget_type, $content ) {
	$prompt = sprintf(
		'Return only a CSS id slug (lowercase letters, numbers, hyphens, max 40 characters) for this Elementor widget. No quotes or explanation. Widget type: %1$s. Visible content: %2$s',
		$widget_type,
		$content
	);

	$result = wp_ai_client_prompt(
		array(
			'prompt' => $prompt,
		)
	);

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	$text = '';
	if ( is_string( $result ) ) {
		$text = $result;
	} elseif ( is_array( $result ) ) {
		if ( isset( $result['text'] ) ) {
			$text = (string) $result['text'];
		} elseif ( isset( $result['content'] ) ) {
			$text = (string) $result['content'];
		}
	} elseif ( is_object( $result ) && isset( $result->text ) ) {
		$text = (string) $result->text;
	}

	return landtech_extras_ai_widget_label_slugify( $text );
}

/**
 * Generate a semantic CSS ID slug from widget type + visible content.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function landtech_extras_ai_widget_label_callback( WP_REST_Request $request ) {
	$widget_type = sanitize_key( (string) $request->get_param( 'widget_type' ) );
	$content     = sanitize_textarea_field( (string) $request->get_param( 'content' ) );
	$content     = wp_strip_all_tags( $content );
	if ( strlen( $content ) > 500 ) {
		$content = substr( $content, 0, 500 );
	}

	$source = 'local';
	$slug   = '';

	if ( landtech_extras_ai_widget_label_client_available() ) {
		$client = landtech_extras_ai_widget_label_wp_client( $widget_type, $content );
		if ( ! is_wp_error( $client ) && '' !== $client ) {
			$slug   = $client;
			$source = 'wp_ai';
		}
	}

	if ( '' === $slug ) {
		$slug   = landtech_extras_ai_widget_label_local_slug( $widget_type, $content );
		$source = 'local';
	}

	return rest_ensure_response(
		array(
			'slug'   => $slug,
			'source' => $source,
		)
	);
}
