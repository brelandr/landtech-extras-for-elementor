<?php
/**
 * Schema.org JSON-LD builder utilities.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds Schema.org graph nodes for widget JSON-LD output.
 *
 * @since 2.3.0
 */
final class Schema_Builder {

	/**
	 * @var array<string, mixed>
	 */
	private $data = array();

	/**
	 * Start a new schema node.
	 *
	 * @param string $type Schema.org @type value.
	 * @return self
	 */
	public function start( $type ) {
		$this->data = array(
			'@context' => 'https://schema.org',
			'@type'    => sanitize_text_field( (string) $type ),
		);

		return $this;
	}

	/**
	 * @param string               $key   Property name.
	 * @param string|array|int|float|bool|null $value Property value.
	 * @return self
	 */
	public function set( $key, $value ) {
		if ( '' === $key || null === $value ) {
			return $this;
		}

		$this->data[ sanitize_key( $key ) ] = $value;

		return $this;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Build FAQPage mainEntity from question/answer rows.
	 *
	 * @param array<int, array{question?: string, answer?: string}> $rows FAQ rows.
	 * @return array<string, mixed>
	 */
	public static function build_faq_page( $rows ) {
		$builder = ( new self() )->start( 'FAQPage' );
		$entities = array();

		foreach ( (array) $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$question = isset( $row['question'] ) ? wp_strip_all_tags( (string) $row['question'] ) : '';
			$answer   = isset( $row['answer'] ) ? wp_kses_post( (string) $row['answer'] ) : '';

			if ( '' === $question || '' === $answer ) {
				continue;
			}

			$entities[] = array(
				'@type'          => 'Question',
				'name'           => $question,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $answer ),
				),
			);
		}

		$builder->set( 'mainEntity', $entities );

		return $builder->get_data();
	}

	/**
	 * Build BreadcrumbList itemListElement from crumb rows.
	 *
	 * @param array<int, array{content?: string, link?: string}> $crumbs Crumb list.
	 * @return array<string, mixed>
	 */
	public static function build_breadcrumb_list( $crumbs ) {
		$builder = ( new self() )->start( 'BreadcrumbList' );
		$items   = array();
		$pos     = 1;

		foreach ( (array) $crumbs as $crumb ) {
			if ( ! is_array( $crumb ) ) {
				continue;
			}

			$name = isset( $crumb['content'] ) ? wp_strip_all_tags( (string) $crumb['content'] ) : '';
			$url  = isset( $crumb['link'] ) ? esc_url_raw( (string) $crumb['link'] ) : '';

			if ( '' === $name ) {
				continue;
			}

			$item = array(
				'@type'    => 'ListItem',
				'position' => $pos,
				'name'     => $name,
			);

			if ( '' !== $url ) {
				$item['item'] = $url;
			}

			$items[] = $item;
			++$pos;
		}

		$builder->set( 'itemListElement', $items );

		return $builder->get_data();
	}

	/**
	 * Print JSON-LD script tag (WP.org-safe structured data exception).
	 *
	 * @param array<string, mixed> $data Schema graph.
	 * @param string               $id   Optional DOM id.
	 * @return void
	 */
	public static function print_json_ld( $data, $id = '' ) {
		if ( empty( $data ) || ! is_array( $data ) ) {
			return;
		}

		$json = wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		if ( false === $json ) {
			return;
		}

		$attrs = array( 'type' => 'application/ld+json' );

		if ( '' !== $id ) {
			$attrs['id'] = sanitize_html_class( $id );
		}

		if ( function_exists( 'wp_print_inline_script_tag' ) ) {
			wp_print_inline_script_tag( $json, $attrs );
			return;
		}

		printf(
			'<script type="application/ld+json"%1$s>%2$s</script>',
			'' !== $id ? ' id="' . esc_attr( sanitize_html_class( $id ) ) . '"' : '',
			esc_html( $json )
		);
	}
}
