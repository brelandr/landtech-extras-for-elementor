<?php
namespace LandTechExtras\Modules\Calendar;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Bookings source (gated on the Bookings plugin).
 *
 * @since 2.6.0
 */
class Woo_Bookings_Source {

	/**
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( 'WC_Bookings' ) || class_exists( 'WC_Booking' );
	}

	/**
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<int,array<string,mixed>>
	 */
	public function get_events( $settings ) {
		if ( ! self::is_active() ) {
			return array();
		}

		$limit = isset( $settings['posts_count'] ) ? absint( $settings['posts_count'] ) : 50;
		if ( $limit < 1 ) {
			$limit = 50;
		}
		if ( $limit > 100 ) {
			$limit = 100;
		}

		$posts  = get_posts(
			array(
				'post_type'      => 'wc_booking',
				'posts_per_page' => $limit,
				'post_status'    => array( 'paid', 'confirmed', 'complete', 'publish' ),
			)
		);
		$events = array();

		foreach ( $posts as $post ) {
			$start = get_post_meta( $post->ID, '_booking_start', true );
			$end   = get_post_meta( $post->ID, '_booking_end', true );
			if ( is_numeric( $start ) ) {
				$start = gmdate( 'Y-m-d H:i', (int) $start );
			}
			if ( is_numeric( $end ) ) {
				$end = gmdate( 'Y-m-d H:i', (int) $end );
			}
			$events[] = array(
				'id'      => (string) $post->ID,
				'title'   => get_the_title( $post ),
				'start'   => $start ? (string) $start : '',
				'end'     => $end ? (string) $end : (string) $start,
				'link'    => get_permalink( $post ),
				'target'  => '_self',
				'rel'     => '',
				'archive' => false,
			);
		}

		return $events;
	}
}
