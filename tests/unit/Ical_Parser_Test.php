<?php
/**
 * iCal subset parser smoke tests.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Tests\Unit;

use LandTechExtras\Modules\Calendar\Ical_Parser;
use PHPUnit\Framework\TestCase;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WordPress core constant stub for standalone unit tests.
}

/**
 * @covers \LandTechExtras\Modules\Calendar\Ical_Parser
 */
class Ical_Parser_Test extends TestCase {

	public function test_parses_single_vevent() {
		if ( ! class_exists( Ical_Parser::class ) ) {
			require_once dirname( __DIR__, 2 ) . '/modules/calendar/class-ical-parser.php';
		}
		$parser = new Ical_Parser();
		$ical   = "BEGIN:VCALENDAR\nBEGIN:VEVENT\nDTSTART:20260929T100000Z\nDTEND:20260929T110000Z\nSUMMARY:Standup\nEND:VEVENT\nEND:VCALENDAR";
		$events = $parser->parse( $ical );
		$this->assertNotEmpty( $events );
		$this->assertSame( 'Standup', $events[0]['title'] );
	}

	public function test_caps_rrule_expansions() {
		if ( ! class_exists( Ical_Parser::class ) ) {
			require_once dirname( __DIR__, 2 ) . '/modules/calendar/class-ical-parser.php';
		}
		$parser = new Ical_Parser();
		$ical   = "BEGIN:VCALENDAR\nBEGIN:VEVENT\nDTSTART:20260101T100000Z\nRRULE:FREQ=DAILY;COUNT=400\nSUMMARY:Daily\nEND:VEVENT\nEND:VCALENDAR";
		$events = $parser->parse( $ical );
		$this->assertLessThanOrEqual( Ical_Parser::MAX_INSTANCES, count( $events ) );
	}
}
