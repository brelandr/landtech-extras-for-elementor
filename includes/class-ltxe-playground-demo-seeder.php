<?php
/**
 * Imports Playground demo content (WXR or bundled seeder).
 *
 * @package LandTechExtras
 * @since   2.9.1
 */

namespace LandTechExtras;

use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Seeds demo pages when a WordPress Playground blueprint runs.
 *
 * REST: POST landtech-extras/v1/playground/seed
 *
 * @since 2.9.1
 */
class LTXE_Playground_Demo_Seeder {

	/**
	 * REST namespace (house convention).
	 *
	 * @var string
	 */
	const REST_NAMESPACE = 'landtech-extras/v1';

	/**
	 * REST route for demo import.
	 *
	 * @var string
	 */
	const REST_ROUTE = '/playground/seed';

	/**
	 * Canonical WXR URL from U27.
	 *
	 * @var string
	 */
	const WXR_URL = 'https://extrasforelementor.com/playground-demo-content.xml';

	/**
	 * Option set after a successful seed.
	 *
	 * @var string
	 */
	const SEEDED_OPTION = 'landtech_extras_playground_seeded';

	/**
	 * Register REST routes.
	 *
	 * @since 2.9.1
	 *
	 * @return void
	 */
	public static function register_rest_routes(): void {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( self::class, 'rest_seed' ),
				'permission_callback' => array( self::class, 'rest_seed_permission' ),
				'args'                => array(
					'force'  => array(
						'required'          => false,
						'default'           => false,
						'sanitize_callback' => array( self::class, 'sanitize_bool_flag' ),
					),
					'source' => array(
						'required'          => false,
						'default'           => 'auto',
						'sanitize_callback' => 'sanitize_key',
						'validate_callback' => array( self::class, 'is_allowed_source' ),
					),
				),
			)
		);
	}

	/**
	 * Write endpoint: administrators / importers only. Cookie REST nonce is required.
	 *
	 * @since 2.9.1
	 *
	 * @return bool
	 */
	public static function rest_seed_permission(): bool {
		return current_user_can( 'import' ) || current_user_can( 'manage_options' );
	}

	/**
	 * Whether the source token is allowed.
	 *
	 * @since 2.9.1
	 *
	 * @param mixed $source Raw source after sanitize_key.
	 * @return bool
	 */
	public static function is_allowed_source( $source ): bool {
		return in_array( (string) $source, array( 'auto', 'wxr', 'bundled' ), true );
	}

	/**
	 * Sanitize a REST boolean flag.
	 *
	 * @since 2.9.1
	 *
	 * @param mixed $value Raw value.
	 * @return bool
	 */
	public static function sanitize_bool_flag( $value ): bool {
		return rest_sanitize_boolean( $value );
	}

	/**
	 * REST callback: import demo content.
	 *
	 * @since 2.9.1
	 *
	 * @param WP_REST_Request $request Request.
	 * @return \WP_REST_Response|WP_Error
	 */
	public static function rest_seed( WP_REST_Request $request ) {
		$force  = rest_sanitize_boolean( $request->get_param( 'force' ) );
		$source = sanitize_key( (string) $request->get_param( 'source' ) );

		if ( ! self::is_allowed_source( $source ) ) {
			$source = 'auto';
		}

		$result = self::seed( $source, $force );

		if ( isset( $result['error'] ) && $result['error'] instanceof WP_Error ) {
			return $result['error'];
		}

		return rest_ensure_response( $result );
	}

	/**
	 * Import demo content (WXR first, bundled seeder fallback).
	 *
	 * @since 2.9.1
	 *
	 * @param string $source auto|wxr|bundled.
	 * @param bool   $force  Re-import even when already seeded.
	 * @return array<string,mixed>
	 */
	public static function seed( string $source = 'auto', bool $force = false ): array {
		if ( function_exists( 'wp_set_current_user' ) && 0 === (int) get_current_user_id() ) {
			wp_set_current_user( 1 );
		}

		if ( ! $force && (bool) get_option( self::SEEDED_OPTION, false ) ) {
			return array(
				'success' => true,
				'source'  => 'skipped',
				'message' => __( 'Playground demo content is already present.', 'landtech-extras-for-elementor' ),
			);
		}

		if ( in_array( $source, array( 'auto', 'wxr' ), true ) ) {
			$wxr = self::import_wxr();
			if ( ! empty( $wxr['imported'] ) ) {
				self::apply_demo_site_options( $wxr );
				update_option( self::SEEDED_OPTION, 'wxr', false );
				return $wxr;
			}

			if ( 'wxr' === $source ) {
				$error = isset( $wxr['error'] ) && $wxr['error'] instanceof WP_Error
					? $wxr['error']
					: new WP_Error(
						'ltxe_playground_wxr_failed',
						__( 'The Playground WXR could not be imported.', 'landtech-extras-for-elementor' ),
						array( 'status' => 502 )
					);

				return array(
					'success' => false,
					'source'  => 'wxr',
					'error'   => $error,
				);
			}
		}

		$bundled = self::import_bundled_seeder();
		if ( ! empty( $bundled['imported'] ) ) {
			update_option( self::SEEDED_OPTION, 'bundled', false );
		}

		return $bundled;
	}

	/**
	 * Apply U27 site options after a WXR import.
	 *
	 * @since 2.9.1
	 *
	 * @param array<string,mixed> $wxr Import result.
	 * @return void
	 */
	protected static function apply_demo_site_options( array $wxr ): void {
		update_option( 'blogname', 'LandTech Extras Demo' );
		update_option( 'show_on_front', 'page' );

		$front_id = 0;
		if ( isset( $wxr['front_page_id'] ) ) {
			$front_id = absint( $wxr['front_page_id'] );
		}

		if ( $front_id <= 0 ) {
			$front_id = 2;
		}

		update_option( 'page_on_front', $front_id );
	}

	/**
	 * Fetch and import the U27 WXR.
	 *
	 * @since 2.9.1
	 *
	 * @return array<string,mixed>
	 */
	public static function import_wxr(): array {
		$url = self::get_wxr_url();
		if ( '' === $url ) {
			return array(
				'imported' => false,
				'source'   => 'wxr',
				'error'    => new WP_Error(
					'ltxe_playground_wxr_url',
					__( 'Playground WXR URL is empty.', 'landtech-extras-for-elementor' ),
					array( 'status' => 400 )
				),
			);
		}

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 20,
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'imported' => false,
				'source'   => 'wxr',
				'error'    => $response,
			);
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = (string) wp_remote_retrieve_body( $response );

		if ( 200 !== $code || '' === $body ) {
			return array(
				'imported' => false,
				'source'   => 'wxr',
				'error'    => new WP_Error(
					'ltxe_playground_wxr_http',
					__( 'The Playground WXR could not be downloaded.', 'landtech-extras-for-elementor' ),
					array(
						'status' => 502,
						'code'   => $code,
					)
				),
			);
		}

		$parsed = self::import_wxr_string( $body );
		$parsed['source'] = 'wxr';
		$parsed['url']    = $url;

		return $parsed;
	}

	/**
	 * WXR URL (filterable).
	 *
	 * @since 2.9.1
	 *
	 * @return string
	 */
	public static function get_wxr_url(): string {
		$url = self::WXR_URL;

		/**
		 * Filter the Playground demo WXR URL.
		 *
		 * @since 2.9.1
		 *
		 * @param string $url Remote WXR URL.
		 */
		$url = apply_filters( 'landtech_extras_playground_wxr_url', $url );

		return esc_url_raw( (string) $url );
	}

	/**
	 * Parse a WXR string and insert posts/pages.
	 *
	 * @since 2.9.1
	 *
	 * @param string $body WXR XML.
	 * @return array<string,mixed>
	 */
	protected static function import_wxr_string( string $body ): array {
		if ( ! function_exists( 'simplexml_load_string' ) ) {
			return array(
				'imported' => false,
				'error'    => new WP_Error(
					'ltxe_playground_wxr_xml',
					__( 'SimpleXML is required to import the Playground WXR.', 'landtech-extras-for-elementor' ),
					array( 'status' => 500 )
				),
			);
		}

		$use_errors = libxml_use_internal_errors( true );
		$xml        = simplexml_load_string( $body );
		libxml_clear_errors();
		libxml_use_internal_errors( $use_errors );

		if ( false === $xml ) {
			return array(
				'imported' => false,
				'error'    => new WP_Error(
					'ltxe_playground_wxr_parse',
					__( 'The Playground WXR is not valid XML.', 'landtech-extras-for-elementor' ),
					array( 'status' => 400 )
				),
			);
		}

		$xml->registerXPathNamespace( 'wp', 'http://wordpress.org/export/1.2/' );
		$xml->registerXPathNamespace( 'content', 'http://purl.org/rss/1.0/modules/content/' );

		$items = $xml->channel->item ?? array();
		if ( ! $items ) {
			return array(
				'imported' => false,
				'error'    => new WP_Error(
					'ltxe_playground_wxr_empty',
					__( 'The Playground WXR contained no items.', 'landtech-extras-for-elementor' ),
					array( 'status' => 400 )
				),
			);
		}

		$created      = 0;
		$front_page_id = 0;

		foreach ( $items as $item ) {
			$wp = $item->children( 'http://wordpress.org/export/1.2/' );
			if ( ! $wp ) {
				$wp = $item->children( 'http://wordpress.org/export/1.1/' );
			}

			$content_ns = $item->children( 'http://purl.org/rss/1.0/modules/content/' );
			$post_type  = isset( $wp->post_type ) ? sanitize_key( (string) $wp->post_type ) : 'post';
			$status     = isset( $wp->status ) ? sanitize_key( (string) $wp->status ) : 'publish';

			if ( ! in_array( $post_type, array( 'post', 'page' ), true ) ) {
				continue;
			}

			if ( 'auto-draft' === $status || 'trash' === $status ) {
				continue;
			}

			$title   = isset( $item->title ) ? sanitize_text_field( (string) $item->title ) : '';
			$slug    = isset( $wp->post_name ) ? sanitize_title( (string) $wp->post_name ) : '';
			$content = '';
			if ( isset( $content_ns->encoded ) ) {
				$content = wp_kses_post( (string) $content_ns->encoded );
			}

			if ( '' === $title && '' === $content ) {
				continue;
			}

			$post_id = wp_insert_post(
				array(
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_content' => $content,
					'post_status'  => 'publish',
					'post_type'    => $post_type,
				),
				true
			);

			if ( is_wp_error( $post_id ) || $post_id <= 0 ) {
				continue;
			}

			++$created;

			if ( 0 === $front_page_id && 'page' === $post_type ) {
				$front_page_id = (int) $post_id;
			}
		}

		if ( $created <= 0 ) {
			return array(
				'imported' => false,
				'error'    => new WP_Error(
					'ltxe_playground_wxr_none',
					__( 'No posts or pages were created from the Playground WXR.', 'landtech-extras-for-elementor' ),
					array( 'status' => 400 )
				),
			);
		}

		return array(
			'imported'      => true,
			'success'       => true,
			'created'       => $created,
			'front_page_id' => $front_page_id,
			'message'       => __( 'Playground WXR imported.', 'landtech-extras-for-elementor' ),
		);
	}

	/**
	 * Fall back to the existing bundled Playground demo pages.
	 *
	 * @since 2.9.1
	 *
	 * @return array<string,mixed>
	 */
	protected static function import_bundled_seeder(): array {
		$seeder = LANDTECH_EXTRAS_PATH . 'includes/playground-demo-seeder.php';
		if ( ! is_readable( $seeder ) ) {
			return array(
				'imported' => false,
				'source'   => 'bundled',
				'error'    => new WP_Error(
					'ltxe_playground_seeder_missing',
					__( 'The bundled Playground seeder file is missing.', 'landtech-extras-for-elementor' ),
					array( 'status' => 500 )
				),
			);
		}

		require_once $seeder;

		if ( ! function_exists( 'landtech_extras_seed_playground_demos' ) ) {
			return array(
				'imported' => false,
				'source'   => 'bundled',
				'error'    => new WP_Error(
					'ltxe_playground_seeder_fn',
					__( 'The bundled Playground seeder is unavailable.', 'landtech-extras-for-elementor' ),
					array( 'status' => 500 )
				),
			);
		}

		$home_id = (int) landtech_extras_seed_playground_demos();

		return array(
			'imported'      => $home_id > 0,
			'success'       => $home_id > 0,
			'source'        => 'bundled',
			'front_page_id' => $home_id,
			'message'       => __( 'Bundled Playground demo pages were seeded.', 'landtech-extras-for-elementor' ),
		);
	}
}

add_action( 'rest_api_init', array( LTXE_Playground_Demo_Seeder::class, 'register_rest_routes' ) );
