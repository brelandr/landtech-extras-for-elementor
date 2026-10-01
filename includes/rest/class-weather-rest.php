<?php
/**
 * Public weather REST proxy (Open-Meteo, no API key).
 *
 * @package LandTechExtras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether anonymous clients may call the weather proxy.
 *
 * Public on purpose: this route only forwards city/lat/lon to the keyless
 * Open-Meteo forecast and geocoding APIs. Responses are cached for five
 * minutes. No site credentials, user data, or write operations are involved.
 *
 * @return bool
 */
function landtech_extras_weather_rest_permission() {
	return true;
}

/**
 * Register GET landtech-extras/v1/weather.
 *
 * @return void
 */
function landtech_extras_register_weather_rest() {
	register_rest_route(
		'landtech-extras/v1',
		'/weather',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'landtech_extras_weather_rest_callback',
			'permission_callback' => 'landtech_extras_weather_rest_permission',
			'args'                => array(
				'city'  => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'lat'   => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'lon'   => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'units' => array(
					'required'          => false,
					'type'              => 'string',
					'default'           => 'celsius',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'landtech_extras_register_weather_rest' );

/**
 * REST callback.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function landtech_extras_weather_rest_callback( $request ) {
	$city  = $request->get_param( 'city' );
	$lat   = $request->get_param( 'lat' );
	$lon   = $request->get_param( 'lon' );
	$units = $request->get_param( 'units' );

	$payload = landtech_extras_weather_get_payload( is_string( $city ) ? $city : '', is_string( $lat ) ? $lat : '', is_string( $lon ) ? $lon : '', is_string( $units ) ? $units : 'celsius' );
	if ( is_wp_error( $payload ) ) {
		return $payload;
	}

	return rest_ensure_response( $payload );
}

/**
 * Fetch (or cache) a weather payload for a city or coordinates.
 *
 * @param string $city  City name.
 * @param string $lat   Latitude.
 * @param string $lon   Longitude.
 * @param string $units Units.
 * @return array|WP_Error
 */
function landtech_extras_weather_get_payload( $city, $lat, $lon, $units ) {
	if ( ! class_exists( '\LandTechExtras\Modules\Weather\Weather_Client' ) ) {
		$client = LANDTECH_EXTRAS_PATH . 'modules/weather/weather-client.php';
		if ( is_readable( $client ) ) {
			require_once $client;
		}
	}

	if ( ! class_exists( '\LandTechExtras\Modules\Weather\Weather_Client' ) ) {
		return new WP_Error( 'ltxe_weather_missing', __( 'Weather helper is unavailable.', 'landtech-extras-for-elementor' ), array( 'status' => 500 ) );
	}

	$city  = sanitize_text_field( (string) $city );
	$lat   = sanitize_text_field( (string) $lat );
	$lon   = sanitize_text_field( (string) $lon );
	$units = \LandTechExtras\Modules\Weather\Weather_Client::sanitize_units( $units );

	if ( '' === $lat || '' === $lon ) {
		if ( '' === $city ) {
			return new WP_Error( 'ltxe_weather_location', __( 'Provide a city name or latitude and longitude.', 'landtech-extras-for-elementor' ), array( 'status' => 400 ) );
		}
	} else {
		if ( ! is_numeric( $lat ) || ! is_numeric( $lon ) ) {
			return new WP_Error( 'ltxe_weather_coords', __( 'Latitude and longitude must be numeric.', 'landtech-extras-for-elementor' ), array( 'status' => 400 ) );
		}
		$lat_f = (float) $lat;
		$lon_f = (float) $lon;
		if ( $lat_f < -90 || $lat_f > 90 || $lon_f < -180 || $lon_f > 180 ) {
			return new WP_Error( 'ltxe_weather_coords', __( 'Latitude or longitude is out of range.', 'landtech-extras-for-elementor' ), array( 'status' => 400 ) );
		}
		$lat = (string) $lat_f;
		$lon = (string) $lon_f;
	}

	$cache_key = \LandTechExtras\Modules\Weather\Weather_Client::cache_key( $city, $lat, $lon, $units );
	$cached    = get_transient( $cache_key );
	if ( is_array( $cached ) && isset( $cached['condition'] ) ) {
		return $cached;
	}

	$label = $city;
	if ( '' === $lat || '' === $lon ) {
		$geo = landtech_extras_weather_geocode( $city );
		if ( is_wp_error( $geo ) ) {
			return $geo;
		}
		$lat   = $geo['lat'];
		$lon   = $geo['lon'];
		$label = $geo['name'];
	}

	$temp_unit = ( 'fahrenheit' === $units ) ? 'fahrenheit' : 'celsius';
	$wind_unit = ( 'fahrenheit' === $units ) ? 'mph' : 'kmh';
	$url       = add_query_arg(
		array(
			'latitude'          => $lat,
			'longitude'         => $lon,
			'current'           => 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m,is_day,uv_index',
			'daily'             => 'weather_code,temperature_2m_max,temperature_2m_min',
			'forecast_days'     => 5,
			'temperature_unit'  => $temp_unit,
			'wind_speed_unit'   => $wind_unit,
			'timezone'          => 'auto',
		),
		'https://api.open-meteo.com/v1/forecast'
	);

	$response = wp_remote_get(
		esc_url_raw( $url ),
		array(
			'timeout' => 8,
			'headers' => array(
				'Accept' => 'application/json',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'ltxe_weather_http', __( 'Weather request failed.', 'landtech-extras-for-elementor' ), array( 'status' => 502 ) );
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	if ( $code < 200 || $code >= 300 ) {
		return new WP_Error( 'ltxe_weather_http', __( 'Weather service returned an error.', 'landtech-extras-for-elementor' ), array( 'status' => 502 ) );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $body ) ) {
		return new WP_Error( 'ltxe_weather_parse', __( 'Weather response was invalid.', 'landtech-extras-for-elementor' ), array( 'status' => 502 ) );
	}

	$payload = \LandTechExtras\Modules\Weather\Weather_Client::parse_forecast( $body, $units, $label );
	set_transient( $cache_key, $payload, 5 * MINUTE_IN_SECONDS );

	return $payload;
}

/**
 * Resolve a city name via Open-Meteo geocoding.
 *
 * @param string $city City.
 * @return array{lat:string,lon:string,name:string}|WP_Error
 */
function landtech_extras_weather_geocode( $city ) {
	$url = add_query_arg(
		array(
			'name'  => $city,
			'count' => 1,
		),
		'https://geocoding-api.open-meteo.com/v1/search'
	);

	$response = wp_remote_get(
		esc_url_raw( $url ),
		array(
			'timeout' => 8,
			'headers' => array(
				'Accept' => 'application/json',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'ltxe_weather_geo', __( 'Could not look up that city.', 'landtech-extras-for-elementor' ), array( 'status' => 502 ) );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $body ) || empty( $body['results'][0] ) || ! is_array( $body['results'][0] ) ) {
		return new WP_Error( 'ltxe_weather_geo', __( 'City not found.', 'landtech-extras-for-elementor' ), array( 'status' => 404 ) );
	}

	$row  = $body['results'][0];
	$name = isset( $row['name'] ) ? (string) $row['name'] : $city;
	if ( ! empty( $row['country'] ) ) {
		$name .= ', ' . (string) $row['country'];
	}

	return array(
		'lat'  => isset( $row['latitude'] ) ? (string) $row['latitude'] : '',
		'lon'  => isset( $row['longitude'] ) ? (string) $row['longitude'] : '',
		'name' => $name,
	);
}
