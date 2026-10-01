<?php
/**
 * Media Gallery URL helpers.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Tests\Unit;

use LandTechExtras\Modules\MediaGallery\Media_Gallery_Urls;
use PHPUnit\Framework\TestCase;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WordPress core constant stub for standalone unit tests.
}

/**
 * @covers \LandTechExtras\Modules\MediaGallery\Media_Gallery_Urls
 */
class Media_Gallery_Urls_Test extends TestCase {

	protected function setUp(): void {
		if ( ! class_exists( Media_Gallery_Urls::class ) ) {
			require_once dirname( __DIR__, 2 ) . '/modules/media-gallery/class-media-gallery-urls.php';
		}
	}

	public function test_youtube_thumbnail_uses_maxresdefault() {
		$url = 'https://www.youtube.com/watch?v=XHOmBV4js_E';
		$this->assertSame(
			'https://img.youtube.com/vi/XHOmBV4js_E/maxresdefault.jpg',
			Media_Gallery_Urls::youtube_thumb( $url )
		);
	}

	public function test_short_youtube_url() {
		$this->assertSame( 'jNQXAC9IVRw', Media_Gallery_Urls::youtube_id( 'https://youtu.be/jNQXAC9IVRw' ) );
	}

	public function test_vimeo_id() {
		$this->assertSame( '76979871', Media_Gallery_Urls::vimeo_id( 'https://vimeo.com/76979871' ) );
	}
}
