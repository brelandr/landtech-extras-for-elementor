<?php
/**
 * Recipe schema tests.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Tests\Unit;

use LandTechExtras\Modules\Recipe\Recipe_Schema;
use PHPUnit\Framework\TestCase;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WordPress core constant stub for standalone unit tests.
}

/**
 * @covers \LandTechExtras\Modules\Recipe\Recipe_Schema
 */
class Recipe_Schema_Test extends TestCase {

	protected function setUp(): void {
		if ( ! class_exists( Recipe_Schema::class ) ) {
			require_once dirname( __DIR__, 2 ) . '/modules/recipe/recipe-schema.php';
		}
	}

	public function test_iso_minutes() {
		$this->assertSame( 'PT30M', Recipe_Schema::iso_duration( 30, 'minutes' ) );
	}

	public function test_iso_hours() {
		$this->assertSame( 'PT1H', Recipe_Schema::iso_duration( 1, 'hours' ) );
	}

	public function test_build_type() {
		$graph = Recipe_Schema::build(
			array(
				'name'             => 'Cookies',
				'recipeIngredient' => array( '1 cup flour' ),
			)
		);
		$this->assertSame( 'Recipe', $graph['@type'] );
		$this->assertSame( 'Cookies', $graph['name'] );
	}
}
