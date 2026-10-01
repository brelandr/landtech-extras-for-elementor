<?php
/**
 * Form styler shortcode sanitizer.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Tests\Unit;

use LandTechExtras\Modules\FormStyler\Form_Styler_Sanitize;
use PHPUnit\Framework\TestCase;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WordPress core constant stub for standalone unit tests.
}

/**
 * @covers \LandTechExtras\Modules\FormStyler\Form_Styler_Sanitize
 */
class Form_Styler_Sanitize_Test extends TestCase {

	protected function setUp(): void {
		if ( ! class_exists( Form_Styler_Sanitize::class ) ) {
			require_once dirname( __DIR__, 2 ) . '/modules/form-styler/class-form-styler-sanitize.php';
		}
	}

	public function test_numeric_id_becomes_cf7_shortcode() {
		$this->assertSame(
			'[contact-form-7 id="42"]',
			Form_Styler_Sanitize::shortcode( '42', 'contact-form-7' )
		);
	}

	public function test_strips_non_id_attributes() {
		$raw = '[contact-form-7 id="7" title="Contact" html_class="evil"]';
		$this->assertSame(
			'[contact-form-7 id="7"]',
			Form_Styler_Sanitize::shortcode( $raw, 'contact-form-7' )
		);
	}

	public function test_rejects_unknown_tag() {
		$this->assertSame( '', Form_Styler_Sanitize::shortcode( '[contact-form-7 id="1"]', 'evil-tag' ) );
	}

	public function test_wpforms_shortcode() {
		$this->assertSame(
			'[wpforms id="9"]',
			Form_Styler_Sanitize::shortcode( '[wpforms id="9" title="true"]', 'wpforms' )
		);
	}

	public function test_empty_input() {
		$this->assertSame( '', Form_Styler_Sanitize::shortcode( '', 'contact-form-7' ) );
	}
}
