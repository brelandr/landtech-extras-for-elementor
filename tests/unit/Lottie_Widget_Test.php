<?php
/**
 * Lottie src + back-compat helpers.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Tests\Unit;

use LandTechExtras\Modules\Lottie\Widgets\Lottie;
use PHPUnit\Framework\TestCase;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/**
 * @covers \LandTechExtras\Modules\Lottie\Widgets\Lottie
 */
class Lottie_Widget_Test extends TestCase {

	/**
	 * @return Lottie
	 */
	protected function widget() {
		if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
			$this->markTestSkipped( 'Elementor is not loaded.' );
		}
		return new Lottie();
	}

	public function test_simple_autoplay_uses_lottie_player() {
		$widget = $this->widget();
		$this->assertFalse(
			$widget->uses_lottie_web(
				array(
					'trigger'   => 'autoplay',
					'direction' => 'forward',
					'speed'     => array( 'size' => 1 ),
				)
			)
		);
	}

	public function test_scroll_trigger_uses_lottie_web() {
		$widget = $this->widget();
		$this->assertTrue(
			$widget->uses_lottie_web(
				array(
					'trigger' => 'scroll_scrub',
				)
			)
		);
	}

	public function test_resolve_src_prefers_upload() {
		$widget = $this->widget();
		$src    = $widget->resolve_src(
			array(
				'source'         => 'upload',
				'animation_file' => array( 'url' => 'https://example.com/a.json' ),
				'animation_url'  => array( 'url' => 'https://example.com/b.json' ),
			)
		);
		$this->assertSame( 'https://example.com/a.json', $src );
	}
}
