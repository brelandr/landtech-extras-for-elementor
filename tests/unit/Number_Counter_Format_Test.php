<?php
/**
 * Number counter formatter.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Tests\Unit;

use LandTechExtras\Modules\NumberCounter\Number_Counter_Format;
use PHPUnit\Framework\TestCase;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WordPress core constant stub for standalone unit tests.
}

/**
 * @covers \LandTechExtras\Modules\NumberCounter\Number_Counter_Format
 */
class Number_Counter_Format_Test extends TestCase {

	protected function setUp(): void {
		if ( ! class_exists( Number_Counter_Format::class ) ) {
			require_once dirname( __DIR__, 2 ) . '/modules/number-counter/class-number-counter-format.php';
		}
	}

	public function test_comma_thousands() {
		$this->assertSame( '1,250', Number_Counter_Format::format( 1250, 0, 'comma' ) );
	}

	public function test_period_thousands() {
		$this->assertSame( '1.250', Number_Counter_Format::format( 1250, 0, 'period' ) );
	}

	public function test_decimals() {
		$this->assertSame( '2.50', Number_Counter_Format::format( 2.5, 2, 'none' ) );
	}

	public function test_clamps_decimals() {
		$this->assertSame( '3.1416', Number_Counter_Format::format( 3.14159, 8, 'none' ) );
	}
}
