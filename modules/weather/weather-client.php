<?php
/**
 * Open-Meteo weather mapping helpers (no HTTP).
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\Weather;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Maps WMO codes and normalizes weather payloads.
 *
 * @since 3.1.0
 */
class Weather_Client {

	/**
	 * Normalize units to celsius or fahrenheit.
	 *
	 * @param string $units Raw units.
	 * @return string
	 */
	public static function sanitize_units( $units ) {
		$units = strtolower( (string) $units );
		if ( in_array( $units, array( 'f', 'fahrenheit', 'imperial' ), true ) ) {
			return 'fahrenheit';
		}
		return 'celsius';
	}

	/**
	 * Transient / cache key for a location.
	 *
	 * @param string $city  City name.
	 * @param string $lat   Latitude.
	 * @param string $lon   Longitude.
	 * @param string $units Units.
	 * @return string
	 */
	public static function cache_key( $city, $lat, $lon, $units ) {
		$units = self::sanitize_units( $units );
		$city  = strtolower( trim( (string) $city ) );
		$lat   = trim( (string) $lat );
		$lon   = trim( (string) $lon );
		if ( '' !== $lat && '' !== $lon ) {
			$slug = $lat . '_' . $lon;
		} else {
			$slug = $city;
		}
		$slug = preg_replace( '/[^a-z0-9._-]+/i', '_', $slug );
		$slug = trim( (string) $slug, '_' );
		if ( '' === $slug ) {
			$slug = 'unknown';
		}
		return 'ltxe_weather_' . $slug . '_' . $units;
	}

	/**
	 * Map a WMO weather code + day flag to a condition slug.
	 *
	 * @param int $code   WMO weather code.
	 * @param int $is_day 1 = day, 0 = night.
	 * @return string sunny|cloudy|rain|snow|night
	 */
	public static function map_condition( $code, $is_day ) {
		$code   = (int) $code;
		$is_day = (int) $is_day;

		if ( 0 === $is_day ) {
			return 'night';
		}

		if ( ( $code >= 71 && $code <= 77 ) || ( $code >= 85 && $code <= 86 ) ) {
			return 'snow';
		}

		if ( ( $code >= 51 && $code <= 67 ) || ( $code >= 80 && $code <= 82 ) || ( $code >= 95 && $code <= 99 ) ) {
			return 'rain';
		}

		if ( 0 === $code ) {
			return 'sunny';
		}

		return 'cloudy';
	}

	/**
	 * Human-readable WMO description.
	 *
	 * @param int $code WMO weather code.
	 * @return string
	 */
	public static function wmo_description( $code ) {
		$code = (int) $code;
		$map  = array(
			0  => 'Clear sky',
			1  => 'Mainly clear',
			2  => 'Partly cloudy',
			3  => 'Overcast',
			45 => 'Fog',
			48 => 'Depositing rime fog',
			51 => 'Light drizzle',
			53 => 'Moderate drizzle',
			55 => 'Dense drizzle',
			61 => 'Slight rain',
			63 => 'Moderate rain',
			65 => 'Heavy rain',
			71 => 'Slight snow',
			73 => 'Moderate snow',
			75 => 'Heavy snow',
			80 => 'Slight rain showers',
			81 => 'Moderate rain showers',
			82 => 'Violent rain showers',
			85 => 'Slight snow showers',
			86 => 'Heavy snow showers',
			95 => 'Thunderstorm',
			96 => 'Thunderstorm with hail',
			99 => 'Thunderstorm with heavy hail',
		);
		if ( isset( $map[ $code ] ) ) {
			return $map[ $code ];
		}
		if ( $code >= 71 && $code <= 77 ) {
			return 'Snow';
		}
		if ( $code >= 51 && $code <= 67 ) {
			return 'Rain';
		}
		return 'Cloudy';
	}

	/**
	 * Parse an Open-Meteo forecast JSON object into a widget payload.
	 *
	 * @param array  $data  Decoded API body.
	 * @param string $units Units.
	 * @param string $city  Resolved city label.
	 * @return array
	 */
	public static function parse_forecast( $data, $units, $city ) {
		$units   = self::sanitize_units( $units );
		$current = array();
		if ( isset( $data['current'] ) && is_array( $data['current'] ) ) {
			$current = $data['current'];
		} elseif ( isset( $data['current_weather'] ) && is_array( $data['current_weather'] ) ) {
			$legacy                        = $data['current_weather'];
			$current['temperature_2m']     = isset( $legacy['temperature'] ) ? $legacy['temperature'] : null;
			$current['wind_speed_10m']     = isset( $legacy['windspeed'] ) ? $legacy['windspeed'] : null;
			$current['weather_code']       = isset( $legacy['weathercode'] ) ? $legacy['weathercode'] : 0;
			$current['is_day']             = isset( $legacy['is_day'] ) ? $legacy['is_day'] : 1;
			$current['apparent_temperature'] = isset( $legacy['temperature'] ) ? $legacy['temperature'] : null;
		}

		$code   = isset( $current['weather_code'] ) ? (int) $current['weather_code'] : 0;
		$is_day = isset( $current['is_day'] ) ? (int) $current['is_day'] : 1;

		$daily = array();
		if ( isset( $data['daily'] ) && is_array( $data['daily'] ) && ! empty( $data['daily']['time'] ) && is_array( $data['daily']['time'] ) ) {
			$times = $data['daily']['time'];
			$maxs  = isset( $data['daily']['temperature_2m_max'] ) && is_array( $data['daily']['temperature_2m_max'] ) ? $data['daily']['temperature_2m_max'] : array();
			$mins  = isset( $data['daily']['temperature_2m_min'] ) && is_array( $data['daily']['temperature_2m_min'] ) ? $data['daily']['temperature_2m_min'] : array();
			$codes = isset( $data['daily']['weather_code'] ) && is_array( $data['daily']['weather_code'] ) ? $data['daily']['weather_code'] : array();
			foreach ( $times as $i => $day ) {
				$d_code = isset( $codes[ $i ] ) ? (int) $codes[ $i ] : 0;
				$daily[] = array(
					'date'        => (string) $day,
					'temp_max'    => isset( $maxs[ $i ] ) ? round( (float) $maxs[ $i ], 1 ) : null,
					'temp_min'    => isset( $mins[ $i ] ) ? round( (float) $mins[ $i ], 1 ) : null,
					'condition'   => self::map_condition( $d_code, 1 ),
					'description' => self::wmo_description( $d_code ),
				);
			}
		}

		return array(
			'city'        => (string) $city,
			'units'       => $units,
			'condition'   => self::map_condition( $code, $is_day ),
			'description' => self::wmo_description( $code ),
			'temperature' => isset( $current['temperature_2m'] ) ? round( (float) $current['temperature_2m'], 1 ) : null,
			'feels_like'  => isset( $current['apparent_temperature'] ) ? round( (float) $current['apparent_temperature'], 1 ) : null,
			'humidity'    => isset( $current['relative_humidity_2m'] ) ? (int) $current['relative_humidity_2m'] : null,
			'wind'        => isset( $current['wind_speed_10m'] ) ? round( (float) $current['wind_speed_10m'], 1 ) : null,
			'uv'          => isset( $current['uv_index'] ) ? round( (float) $current['uv_index'], 1 ) : null,
			'is_day'      => $is_day,
			'forecast'    => $daily,
		);
	}
}
