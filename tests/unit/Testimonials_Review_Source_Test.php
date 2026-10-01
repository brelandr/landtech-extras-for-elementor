<?php
/**
 * Testimonials review normaliser.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Tests\Unit;

use LandTechExtras\Modules\Testimonials\Review_Source;
use PHPUnit\Framework\TestCase;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WordPress core constant stub for standalone unit tests.
}

/**
 * @covers \LandTechExtras\Modules\Testimonials\Review_Source
 */
class Testimonials_Review_Source_Test extends TestCase {

	public function test_aggregate_averages_ratings() {
		if ( ! class_exists( Review_Source::class ) ) {
			require_once dirname( __DIR__, 2 ) . '/modules/testimonials/review-source.php';
		}
		$agg = Review_Source::aggregate(
			array(
				array( 'rating' => 5 ),
				array( 'rating' => 3 ),
			)
		);
		$this->assertSame( 2, $agg['count'] );
		$this->assertSame( 4.0, $agg['average'] );
	}

	public function test_normalize_clamps_rating() {
		if ( ! class_exists( Review_Source::class ) ) {
			require_once dirname( __DIR__, 2 ) . '/modules/testimonials/review-source.php';
		}
		if ( ! function_exists( 'sanitize_text_field' ) ) {
			$this->markTestSkipped( 'WordPress is not loaded.' );
		}
		$row = Review_Source::normalize(
			array(
				'author'  => 'Test',
				'rating'  => 9,
				'content' => '<b>Hi</b>',
				'source'  => 'manual',
			)
		);
		$this->assertSame( 5.0, $row['rating'] );
		$this->assertSame( 'manual', $row['source'] );
	}
}
