<?php
namespace LandTechExtras\Modules\Testimonials;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalise review rows from manual, WooCommerce, or comments.
 *
 * @since 2.9.0
 */
class Review_Source {

	const MAX_ITEMS = 50;

	/**
	 * @param array<string,mixed> $settings Widget settings.
	 * @return array<int,array<string,mixed>>
	 */
	public static function collect( $settings ) {
		$settings = is_array( $settings ) ? $settings : array();
		$source   = isset( $settings['source'] ) ? sanitize_key( (string) $settings['source'] ) : 'manual';
		$limit    = isset( $settings['limit'] ) ? absint( $settings['limit'] ) : 12;
		if ( $limit < 1 ) {
			$limit = 12;
		}
		if ( $limit > self::MAX_ITEMS ) {
			$limit = self::MAX_ITEMS;
		}

		if ( 'woocommerce' === $source ) {
			return self::from_woocommerce( $settings, $limit );
		}
		if ( 'comments' === $source ) {
			return self::from_comments( $settings, $limit );
		}

		return self::from_manual( $settings, $limit );
	}

	/**
	 * @param array<string,mixed> $row Raw row.
	 * @return array<string,mixed>
	 */
	public static function normalize( $row ) {
		$row = is_array( $row ) ? $row : array();

		$rating = isset( $row['rating'] ) ? (float) $row['rating'] : 0;
		if ( $rating < 0 ) {
			$rating = 0;
		}
		if ( $rating > 5 ) {
			$rating = 5;
		}

		$source = isset( $row['source'] ) ? sanitize_key( (string) $row['source'] ) : 'manual';
		if ( ! in_array( $source, array( 'manual', 'woocommerce', 'comments' ), true ) ) {
			$source = 'manual';
		}

		return array(
			'author'     => isset( $row['author'] ) ? sanitize_text_field( (string) $row['author'] ) : '',
			'role'       => isset( $row['role'] ) ? sanitize_text_field( (string) $row['role'] ) : '',
			'avatar'     => isset( $row['avatar'] ) ? esc_url_raw( (string) $row['avatar'] ) : '',
			'rating'     => $rating,
			'content'    => isset( $row['content'] ) ? wp_kses_post( (string) $row['content'] ) : '',
			'date'       => isset( $row['date'] ) ? sanitize_text_field( (string) $row['date'] ) : '',
			'source'     => $source,
			'source_url' => isset( $row['source_url'] ) ? esc_url_raw( (string) $row['source_url'] ) : '',
		);
	}

	/**
	 * @param array<int,array<string,mixed>> $items Items.
	 * @return array{count:int,average:float}
	 */
	public static function aggregate( $items ) {
		$items = is_array( $items ) ? $items : array();
		$sum   = 0.0;
		$count = 0;
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$sum  += isset( $item['rating'] ) ? (float) $item['rating'] : 0;
			$count++;
		}
		return array(
			'count'   => $count,
			'average' => $count ? round( $sum / $count, 1 ) : 0.0,
		);
	}

	/**
	 * @param array<string,mixed> $settings Settings.
	 * @param int                 $limit    Cap.
	 * @return array<int,array<string,mixed>>
	 */
	private static function from_manual( $settings, $limit ) {
		$raw = isset( $settings['reviews'] ) && is_array( $settings['reviews'] ) ? $settings['reviews'] : array();
		$out = array();
		foreach ( array_slice( $raw, 0, $limit ) as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$avatar = '';
			if ( ! empty( $row['author_image']['url'] ) ) {
				$avatar = esc_url_raw( (string) $row['author_image']['url'] );
			} elseif ( ! empty( $row['author_image']['id'] ) ) {
				$url = wp_get_attachment_image_url( absint( $row['author_image']['id'] ), 'thumbnail' );
				if ( is_string( $url ) ) {
					$avatar = esc_url_raw( $url );
				}
			}
			$out[] = self::normalize(
				array(
					'author'  => isset( $row['author_name'] ) ? $row['author_name'] : '',
					'role'    => isset( $row['author_role'] ) ? $row['author_role'] : '',
					'avatar'  => $avatar,
					'rating'  => isset( $row['rating'] ) ? $row['rating'] : 5,
					'content' => isset( $row['content'] ) ? $row['content'] : '',
					'date'    => isset( $row['date'] ) ? $row['date'] : '',
					'source'  => 'manual',
				)
			);
		}
		return $out;
	}

	/**
	 * @param array<string,mixed> $settings Settings.
	 * @param int                 $limit    Cap.
	 * @return array<int,array<string,mixed>>
	 */
	private static function from_woocommerce( $settings, $limit ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return array();
		}

		$args = array(
			'status'  => 'approve',
			'type'    => 'review',
			'number'  => $limit,
			'orderby' => 'comment_date_gmt',
			'order'   => 'DESC',
		);

		$product_id = isset( $settings['wc_product_id'] ) ? absint( $settings['wc_product_id'] ) : 0;
		if ( $product_id ) {
			$args['post_id'] = $product_id;
		} else {
			$args['post_type'] = 'product';
		}

		return self::from_wp_comments( $args, $settings, 'woocommerce' );
	}

	/**
	 * @param array<string,mixed> $settings Settings.
	 * @param int                 $limit    Cap.
	 * @return array<int,array<string,mixed>>
	 */
	private static function from_comments( $settings, $limit ) {
		$post_id = isset( $settings['comment_post_id'] ) ? absint( $settings['comment_post_id'] ) : 0;
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$args = array(
			'status'  => 'approve',
			'type'    => 'comment',
			'number'  => $limit,
			'orderby' => 'comment_date_gmt',
			'order'   => 'DESC',
		);
		if ( $post_id ) {
			$args['post_id'] = $post_id;
		}

		return self::from_wp_comments( $args, $settings, 'comments' );
	}

	/**
	 * @param array<string,mixed> $args     get_comments args.
	 * @param array<string,mixed> $settings Widget settings.
	 * @param string              $source   Source key.
	 * @return array<int,array<string,mixed>>
	 */
	private static function from_wp_comments( $args, $settings, $source ) {
		$min = isset( $settings['min_rating'] ) ? (float) $settings['min_rating'] : 0;
		$out = array();
		$comments = get_comments( $args );
		if ( ! is_array( $comments ) ) {
			return $out;
		}

		foreach ( $comments as $comment ) {
			if ( ! $comment instanceof \WP_Comment ) {
				continue;
			}
			$rating = (float) get_comment_meta( (int) $comment->comment_ID, 'rating', true );
			if ( $min > 0 && $rating < $min ) {
				continue;
			}
			$avatar = get_avatar_url( $comment, array( 'size' => 96 ) );
			$out[]  = self::normalize(
				array(
					'author'     => $comment->comment_author,
					'role'       => '',
					'avatar'     => is_string( $avatar ) ? $avatar : '',
					'rating'     => $rating,
					'content'    => $comment->comment_content,
					'date'       => wp_date( get_option( 'date_format' ), strtotime( $comment->comment_date_gmt . ' UTC' ) ),
					'source'     => $source,
					'source_url' => get_comment_link( $comment ),
				)
			);
		}

		return $out;
	}
}
