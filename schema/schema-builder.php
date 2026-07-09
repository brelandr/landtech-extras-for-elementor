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
	 * Build Organization graph from widget settings.
	 *
	 * @param array<string, mixed> $settings Widget settings.
	 * @return array<string, mixed>
	 */
	public static function build_organization( $settings ) {
		$builder = ( new self() )->start( 'Organization' );
		$name    = isset( $settings['name'] ) ? sanitize_text_field( (string) $settings['name'] ) : '';

		if ( '' !== $name ) {
			$builder->set( 'name', $name );
		}

		if ( ! empty( $settings['description'] ) ) {
			$builder->set( 'description', sanitize_textarea_field( (string) $settings['description'] ) );
		}

		if ( ! empty( $settings['url']['url'] ) ) {
			$builder->set( 'url', esc_url_raw( (string) $settings['url']['url'] ) );
		}

		if ( ! empty( $settings['logo']['url'] ) ) {
			$builder->set( 'logo', esc_url_raw( (string) $settings['logo']['url'] ) );
		}

		if ( ! empty( $settings['telephone'] ) ) {
			$builder->set( 'telephone', sanitize_text_field( (string) $settings['telephone'] ) );
		}

		if ( ! empty( $settings['email'] ) ) {
			$builder->set( 'email', sanitize_email( (string) $settings['email'] ) );
		}

		$same_as = array();
		foreach ( (array) ( $settings['social_profiles'] ?? array() ) as $row ) {
			if ( ! is_array( $row ) || empty( $row['profile_url']['url'] ) ) {
				continue;
			}
			$url = esc_url_raw( (string) $row['profile_url']['url'] );
			if ( '' !== $url ) {
				$same_as[] = $url;
			}
		}
		if ( ! empty( $same_as ) ) {
			$builder->set( 'sameAs', $same_as );
		}

		$address = self::build_postal_address_from_settings( $settings );
		if ( ! empty( $address ) ) {
			$builder->set( 'address', $address );
		}

		return $builder->get_data();
	}

	/**
	 * Build Event graph from widget settings.
	 *
	 * @param array<string, mixed> $settings Widget settings.
	 * @return array<string, mixed>
	 */
	public static function build_event( $settings ) {
		$builder = ( new self() )->start( 'Event' );
		$name    = isset( $settings['name'] ) ? sanitize_text_field( (string) $settings['name'] ) : '';

		if ( '' !== $name ) {
			$builder->set( 'name', $name );
		}

		if ( ! empty( $settings['description'] ) ) {
			$builder->set( 'description', sanitize_textarea_field( (string) $settings['description'] ) );
		}

		$start = self::format_schema_datetime( $settings['start_date'] ?? '' );
		if ( '' !== $start ) {
			$builder->set( 'startDate', $start );
		}

		$end = self::format_schema_datetime( $settings['end_date'] ?? '' );
		if ( '' !== $end ) {
			$builder->set( 'endDate', $end );
		}

		if ( ! empty( $settings['image']['url'] ) ) {
			$builder->set( 'image', esc_url_raw( (string) $settings['image']['url'] ) );
		}

		if ( ! empty( $settings['event_url']['url'] ) ) {
			$builder->set( 'url', esc_url_raw( (string) $settings['event_url']['url'] ) );
		}

		if ( ! empty( $settings['event_status'] ) ) {
			$builder->set( 'eventStatus', sanitize_text_field( (string) $settings['event_status'] ) );
		}

		if ( ! empty( $settings['event_attendance_mode'] ) ) {
			$builder->set( 'eventAttendanceMode', sanitize_text_field( (string) $settings['event_attendance_mode'] ) );
		}

		$location = self::build_event_location( $settings );
		if ( ! empty( $location ) ) {
			$builder->set( 'location', $location );
		}

		if ( ! empty( $settings['organizer_name'] ) ) {
			$organizer = array(
				'@type' => 'Organization',
				'name'  => sanitize_text_field( (string) $settings['organizer_name'] ),
			);
			if ( ! empty( $settings['organizer_url']['url'] ) ) {
				$organizer['url'] = esc_url_raw( (string) $settings['organizer_url']['url'] );
			}
			$builder->set( 'organizer', $organizer );
		}

		if ( '' !== (string) ( $settings['offer_price'] ?? '' ) || ! empty( $settings['offer_url']['url'] ) ) {
			$offer = array(
				'@type' => 'Offer',
				'url'   => ! empty( $settings['offer_url']['url'] ) ? esc_url_raw( (string) $settings['offer_url']['url'] ) : '',
			);
			if ( '' !== (string) ( $settings['offer_price'] ?? '' ) ) {
				$offer['price']         = sanitize_text_field( (string) $settings['offer_price'] );
				$offer['priceCurrency'] = sanitize_text_field( (string) ( $settings['offer_currency'] ?? 'USD' ) );
			}
			$offer = array_filter( $offer );
			if ( ! empty( $offer ) ) {
				$builder->set( 'offers', $offer );
			}
		}

		return $builder->get_data();
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 * @return array<string, mixed>
	 */
	private static function build_postal_address_from_settings( $settings ) {
		$address = array_filter(
			array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => sanitize_text_field( (string) ( $settings['street'] ?? '' ) ),
				'addressLocality' => sanitize_text_field( (string) ( $settings['city'] ?? '' ) ),
				'addressRegion'   => sanitize_text_field( (string) ( $settings['region'] ?? '' ) ),
				'postalCode'      => sanitize_text_field( (string) ( $settings['postal_code'] ?? '' ) ),
				'addressCountry'  => sanitize_text_field( (string) ( $settings['country'] ?? '' ) ),
			)
		);

		if ( count( $address ) <= 1 ) {
			return array();
		}

		return $address;
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 * @return array<string, mixed>
	 */
	private static function build_event_location( $settings ) {
		$mode = ! empty( $settings['event_attendance_mode'] ) ? (string) $settings['event_attendance_mode'] : 'OfflineEventAttendanceMode';

		if ( 'OnlineEventAttendanceMode' === $mode && ! empty( $settings['location_url']['url'] ) ) {
			return array(
				'@type' => 'VirtualLocation',
				'url'   => esc_url_raw( (string) $settings['location_url']['url'] ),
			);
		}

		$location_name = sanitize_text_field( (string) ( $settings['location_name'] ?? '' ) );
		$address       = self::build_postal_address_from_settings( $settings );

		if ( '' === $location_name && empty( $address ) ) {
			return array();
		}

		$location = array(
			'@type' => 'Place',
		);

		if ( '' !== $location_name ) {
			$location['name'] = $location_name;
		}

		if ( ! empty( $address ) ) {
			$location['address'] = $address;
		}

		return $location;
	}

	/**
	 * @param string $value Raw date/time from Elementor control.
	 * @return string ISO 8601 datetime or empty string.
	 */
	private static function format_schema_datetime( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}

		$timestamp = strtotime( $value );
		if ( false === $timestamp ) {
			return sanitize_text_field( $value );
		}

		return wp_date( DATE_ATOM, $timestamp );
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
