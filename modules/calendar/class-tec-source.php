<?php
namespace LandTechExtras\Modules\Calendar;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Events Calendar data source.
 *
 * @since 2.6.0
 */
class Tec_Source {

	/**
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( 'Tribe__Events__Main' );
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

		$args = array(
			'post_type'      => \Tribe__Events__Main::POSTTYPE,
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'meta_key'       => '_EventStartDate', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_EventStartDate',
					'value'   => current_time( 'mysql' ),
					'compare' => '>=',
					'type'    => 'DATETIME',
				),
			),
		);

		$posts  = get_posts( $args );
		$events = array();
		foreach ( $posts as $post ) {
			$start = get_post_meta( $post->ID, '_EventStartDate', true );
			$end   = get_post_meta( $post->ID, '_EventEndDate', true );
			$venue = '';
			if ( function_exists( 'tribe_get_venue' ) ) {
				$venue = (string) tribe_get_venue( $post->ID );
			}
			$events[] = array(
				'id'          => (string) $post->ID,
				'title'       => get_the_title( $post ),
				'start'       => $start ? gmdate( 'Y-m-d H:i', strtotime( $start ) ) : '',
				'end'         => $end ? gmdate( 'Y-m-d H:i', strtotime( $end ) ) : $start,
				'link'        => get_permalink( $post ),
				'target'      => '_self',
				'rel'         => '',
				'archive'     => false,
				'description' => wp_trim_words( wp_strip_all_tags( $post->post_content ), 20 ),
				'location'    => $venue,
			);
		}

		return $events;
	}
}
