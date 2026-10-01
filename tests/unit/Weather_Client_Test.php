<?php
/**
 * Weather client mapping tests.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Tests\Unit;

use LandTechExtras\Modules\Weather\Weather_Client;
use PHPUnit\Framework\TestCase;

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WordPress core constant stub for standalone unit tests.
}

/**
 * @covers \LandTechExtras\Modules\Weather\Weather_Client
 */
class Weather_Client_Test extends TestCase {

	protected function setUp(): void {
		if ( ! class_exists( Weather_Client::class ) ) {
			require_once dirname( __DIR__, 2 ) . '/modules/weather/weather-client.php';
		}
	}

	public function test_sanitize_units() {
		$this->assertSame( 'fahrenheit', Weather_Client::sanitize_units( 'F' ) );
		$this->assertSame( 'celsius', Weather_Client::sanitize_units( 'celsius' ) );
	}

	public function test_cache_key_city() {
		$this->assertSame( 'ltxe_weather_london_celsius', Weather_Client::cache_key( 'London', '', '', 'celsius' ) );
	}

	public function test_map_sunny_day() {
		$this->assertSame( 'sunny', Weather_Client::map_condition( 0, 1 ) );
	}

	public function test_map_night() {
		$this->assertSame( 'night', Weather_Client::map_condition( 0, 0 ) );
	}

	public function test_map_rain() {
		$this->assertSame( 'rain', Weather_Client::map_condition( 61, 1 ) );
	}

	public function test_map_snow() {
		$this->assertSame( 'snow', Weather_Client::map_condition( 71, 1 ) );
	}

	public function test_map_cloudy() {
		$this->assertSame( 'cloudy', Weather_Client::map_condition( 3, 1 ) );
	}

	public function test_parse_forecast() {
		$data = array(
			'current' => array(
				'temperature_2m'         => 12.4,
				'apparent_temperature'   => 11.0,
				'relative_humidity_2m'   => 80,
				'weather_code'           => 61,
				'wind_speed_10m'         => 14.2,
				'is_day'                 => 1,
				'uv_index'               => 2.1,
			),
			'daily'   => array(
				'time'               => array( '2026-09-30' ),
				'temperature_2m_max' => array( 14.0 ),
				'temperature_2m_min' => array( 8.0 ),
				'weather_code'       => array( 61 ),
			),
		);
		$out = Weather_Client::parse_forecast( $data, 'celsius', 'Seattle' );
		$this->assertSame( 'rain', $out['condition'] );
		$this->assertSame( 12.4, $out['temperature'] );
		$this->assertSame( 'Seattle', $out['city'] );
		$this->assertCount( 1, $out['forecast'] );
		$this->assertSame( 'rain', $out['forecast'][0]['condition'] );
	}
}
