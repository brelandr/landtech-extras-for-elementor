<?php
/**
 * Media Gallery URL helpers.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\MediaGallery;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * YouTube and Vimeo ID extraction. No remote calls.
 *
 * @since 3.0.0
 */
class Media_Gallery_Urls {

	/**
	 * YouTube video ID, or an empty string.
	 *
	 * @since 3.0.0
	 * @param string $url Video URL.
	 * @return string
	 */
	public static function youtube_id( $url ) {
		$url = trim( (string) $url );
		if ( '' === $url ) {
			return '';
		}
		if ( preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $url, $match ) ) {
			return $match[1];
		}
		return '';
	}

	/**
	 * YouTube poster image.
	 *
	 * hqdefault exists for every public video. maxresdefault 404s on older
	 * uploads, including the gallery demo video.
	 *
	 * @since 3.0.0
	 * @param string $url Video URL.
	 * @return string
	 */
	public static function youtube_thumb( $url ) {
		$id = self::youtube_id( $url );
		if ( '' === $id ) {
			return '';
		}
		return 'https://img.youtube.com/vi/' . $id . '/hqdefault.jpg';
	}

	/**
	 * Vimeo numeric ID, or an empty string.
	 *
	 * @since 3.0.0
	 * @param string $url Video URL.
	 * @return string
	 */
	public static function vimeo_id( $url ) {
		$url = trim( (string) $url );
		if ( preg_match( '/vimeo\.com\/(?:video\/)?(\d+)/', $url, $match ) ) {
			return $match[1];
		}
		return '';
	}
}
