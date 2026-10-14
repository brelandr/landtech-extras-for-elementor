<?php
/**
 * SVG upload sanitizer.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Tests\Unit;

use PHPUnit\Framework\TestCase;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WordPress core constant stub for standalone unit tests.
}

/**
 * @covers \Landtech_Extras_Svg_Sanitizer
 */
class Svg_Sanitizer_Test extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		if ( ! class_exists( 'Landtech_Extras_Svg_Sanitizer' ) ) {
			require_once dirname( __DIR__, 2 ) . '/includes/svg/landtech-extras-svg-sanitizer.php';
		}
	}

	public function test_strips_namespaced_script() {
		$raw = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg"><svg:script>alert(1)</svg:script><path d="M0 0"/></svg>';
		$clean = $this->sanitize( $raw );

		$this->assertIsString( $clean );
		$this->assertStringNotContainsString( 'script', strtolower( $clean ) );
		$this->assertStringNotContainsString( 'alert', $clean );
		$this->assertStringContainsString( '<path', strtolower( $clean ) );
	}

	public function test_strips_style_element() {
		$raw = '<svg xmlns="http://www.w3.org/2000/svg"><style>circle{fill:url("javascript:alert(1)")}</style><circle r="5"/></svg>';
		$clean = $this->sanitize( $raw );

		$this->assertIsString( $clean );
		$this->assertStringNotContainsString( '<style', strtolower( $clean ) );
		$this->assertStringNotContainsString( 'javascript', strtolower( $clean ) );
		$this->assertStringContainsString( '<circle', strtolower( $clean ) );
	}

	public function test_strips_foreign_namespace_element() {
		$raw = '<svg xmlns="http://www.w3.org/2000/svg"><foo xmlns="http://www.w3.org/1999/xhtml"><script>alert(1)</script></foo><rect width="1" height="1"/></svg>';
		$clean = $this->sanitize( $raw );

		$this->assertIsString( $clean );
		$this->assertStringNotContainsString( 'script', strtolower( $clean ) );
		$this->assertStringNotContainsString( 'alert', $clean );
		$this->assertStringContainsString( '<rect', strtolower( $clean ) );
	}

	public function test_keeps_plain_svg() {
		$raw = '<svg xmlns="http://www.w3.org/2000/svg"><path d="M1 1" fill="#fff"/></svg>';
		$clean = $this->sanitize( $raw );

		$this->assertIsString( $clean );
		$this->assertStringContainsString( '<path', strtolower( $clean ) );
		$this->assertStringContainsString( 'fill="#fff"', strtolower( $clean ) );
	}

	/**
	 * @param string $raw Raw SVG.
	 * @return string|false
	 */
	private function sanitize( $raw ) {
		$sanitizer = new \Landtech_Extras_Svg_Sanitizer();
		return $sanitizer->sanitize( $raw );
	}
}
