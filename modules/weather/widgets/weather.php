<?php
/**
 * Weather widget.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\Weather\Widgets;

use LandTechExtras\Base\Extras_Widget;
use LandTechExtras\Modules\Weather\Weather_Client;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname( __DIR__ ) . '/weather-client.php';

/**
 * @since 3.1.0
 */
class Weather extends Extras_Widget {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'ltxe-weather';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Weather', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon() {
		return 'eicon-flash';
	}

	/**
	 * @inheritDoc
	 */
	public function get_keywords() {
		return array( 'weather', 'forecast', 'temperature', 'open-meteo' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_style_depends() {
		return array( 'landtech-extras-weather' );
	}

	/**
	 * @inheritDoc
	 */
	public function get_script_depends() {
		return array( 'landtech-extras-weather' );
	}

	/**
	 * @inheritDoc
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_location',
			array(
				'label' => __( 'Location', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'location',
			array(
				'label'       => __( 'City', 'landtech-extras-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'London',
				'placeholder' => __( 'City name or lat,lon', 'landtech-extras-for-elementor' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'geolocate',
			array(
				'label'        => __( 'Show geolocate button', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'units',
			array(
				'label'   => __( 'Units', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'celsius',
				'options' => array(
					'celsius'    => __( 'Celsius', 'landtech-extras-for-elementor' ),
					'fahrenheit' => __( 'Fahrenheit', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'refresh',
			array(
				'label'   => __( 'Refresh interval', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '15',
				'options' => array(
					'5'  => __( '5 minutes', 'landtech-extras-for-elementor' ),
					'15' => __( '15 minutes', 'landtech-extras-for-elementor' ),
					'30' => __( '30 minutes', 'landtech-extras-for-elementor' ),
					'60' => __( '60 minutes', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'card',
				'options' => array(
					'compact' => __( 'Compact', 'landtech-extras-for-elementor' ),
					'card'    => __( 'Card', 'landtech-extras-for-elementor' ),
					'hero'    => __( 'Hero banner', 'landtech-extras-for-elementor' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_fields',
			array(
				'label' => __( 'Fields', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'show_temperature',
			array(
				'label'        => __( 'Temperature', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_feels_like',
			array(
				'label'        => __( 'Feels like', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_humidity',
			array(
				'label'        => __( 'Humidity', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_wind',
			array(
				'label'        => __( 'Wind speed', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_uv',
			array(
				'label'        => __( 'UV index', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'        => __( 'Condition icon', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_forecast',
			array(
				'label'        => __( '5-day forecast', 'landtech-extras-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => __( 'Uses the keyless Open-Meteo daily forecast.', 'landtech-extras-for-elementor' ),
			)
		);

		$premium_hourly = function_exists( 'landtech_extras_premium_addon_runtime_entitled' ) && landtech_extras_premium_addon_runtime_entitled();
		if ( ! $premium_hourly ) {
			$this->add_control(
				'forecast_premium_note',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => '<p>' . esc_html__( 'LandTech Extras Premium can add hourly OpenWeather forecasts when you save an API key.', 'landtech-extras-for-elementor' ) . '</p>',
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_background',
			array(
				'label' => __( 'Condition background', 'landtech-extras-for-elementor' ),
			)
		);

		$this->add_control(
			'condition_bg',
			array(
				'label'   => __( 'Background', 'landtech-extras-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'  => __( 'None', 'landtech-extras-for-elementor' ),
					'image' => __( 'Image per condition', 'landtech-extras-for-elementor' ),
					'video' => __( 'Video per condition', 'landtech-extras-for-elementor' ),
				),
			)
		);

		foreach ( array( 'sunny', 'cloudy', 'rain', 'snow', 'night' ) as $cond ) {
			$this->add_control(
				'bg_image_' . $cond,
				array(
					'label'     => sprintf(
						/* translators: %s: weather condition */
						__( '%s image', 'landtech-extras-for-elementor' ),
						ucfirst( $cond )
					),
					'type'      => Controls_Manager::MEDIA,
					'condition' => array( 'condition_bg' => 'image' ),
				)
			);
			$this->add_control(
				'bg_video_' . $cond,
				array(
					'label'      => sprintf(
						/* translators: %s: weather condition */
						__( '%s video', 'landtech-extras-for-elementor' ),
						ucfirst( $cond )
					),
					'type'       => Controls_Manager::MEDIA,
					'media_types' => array( 'video' ),
					'condition'  => array( 'condition_bg' => 'video' ),
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_card',
			array(
				'label' => __( 'Card', 'landtech-extras-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_color',
			array(
				'label'     => __( 'Text color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-weather' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'     => __( 'Background color', 'landtech-extras-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ltxe-weather' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => __( 'Padding', 'landtech-extras-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .ltxe-weather' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'city_typo',
				'label'    => __( 'City', 'landtech-extras-for-elementor' ),
				'selector' => '{{WRAPPER}} .ltxe-weather__city',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'temp_typo',
				'label'    => __( 'Temperature', 'landtech-extras-for-elementor' ),
				'selector' => '{{WRAPPER}} .ltxe-weather__temp',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Media URL from an Elementor MEDIA control.
	 *
	 * @param mixed $media Control value.
	 * @return string
	 */
	private function media_url( $media ) {
		if ( is_array( $media ) && ! empty( $media['url'] ) && is_string( $media['url'] ) ) {
			return $media['url'];
		}
		return '';
	}

	/**
	 * @inheritDoc
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$location = isset( $settings['location'] ) ? sanitize_text_field( (string) $settings['location'] ) : 'London';
		$units    = Weather_Client::sanitize_units( isset( $settings['units'] ) ? (string) $settings['units'] : 'celsius' );
		$layout   = isset( $settings['layout'] ) ? sanitize_key( (string) $settings['layout'] ) : 'card';
		if ( ! in_array( $layout, array( 'compact', 'card', 'hero' ), true ) ) {
			$layout = 'card';
		}
		$refresh = isset( $settings['refresh'] ) ? absint( $settings['refresh'] ) : 15;
		if ( ! in_array( $refresh, array( 5, 15, 30, 60 ), true ) ) {
			$refresh = 15;
		}

		$lat = '';
		$lon = '';
		if ( preg_match( '/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/', $location, $m ) ) {
			$lat      = $m[1];
			$lon      = $m[2];
			$location = '';
		}

		$payload = null;
		if ( function_exists( 'landtech_extras_weather_get_payload' ) ) {
			$fetched = landtech_extras_weather_get_payload( $location, $lat, $lon, $units );
			if ( ! is_wp_error( $fetched ) && is_array( $fetched ) ) {
				$payload = $fetched;
			}
		}

		$condition = ( $payload && ! empty( $payload['condition'] ) ) ? sanitize_key( (string) $payload['condition'] ) : 'cloudy';
		$city_label = $payload && ! empty( $payload['city'] ) ? (string) $payload['city'] : ( '' !== $location ? $location : __( 'Your location', 'landtech-extras-for-elementor' ) );
		$unit_sym   = ( 'fahrenheit' === $units ) ? '°F' : '°C';
		$wind_unit  = ( 'fahrenheit' === $units ) ? 'mph' : 'km/h';

		$bg_mode = isset( $settings['condition_bg'] ) ? sanitize_key( (string) $settings['condition_bg'] ) : 'none';
		$conds   = array( 'sunny', 'cloudy', 'rain', 'snow', 'night' );
		$images  = array();
		$videos  = array();
		foreach ( $conds as $cond ) {
			$images[ $cond ] = $this->media_url( isset( $settings[ 'bg_image_' . $cond ] ) ? $settings[ 'bg_image_' . $cond ] : array() );
			$videos[ $cond ] = $this->media_url( isset( $settings[ 'bg_video_' . $cond ] ) ? $settings[ 'bg_video_' . $cond ] : array() );
		}

		$config = array(
			'city'      => $location,
			'lat'       => $lat,
			'lon'       => $lon,
			'units'     => $units,
			'refresh'   => $refresh * 60 * 1000,
			'geolocate' => isset( $settings['geolocate'] ) && 'yes' === $settings['geolocate'],
			'forecast'  => isset( $settings['show_forecast'] ) && 'yes' === $settings['show_forecast'],
			'bgMode'    => $bg_mode,
		);

		$style = '';
		if ( 'image' === $bg_mode ) {
			foreach ( $images as $cond => $url ) {
				if ( '' !== $url ) {
					$style .= '--ltxe-weather-bg-' . $cond . ':url(' . esc_url( $url ) . ');';
				}
			}
		}

		$classes = 'ltxe-weather ltxe-weather--' . $layout;
		$rest    = rest_url( 'landtech-extras/v1/weather' );

		echo '<div class="' . esc_attr( $classes ) . '" data-ltxe-condition="' . esc_attr( $condition ) . '" data-ltxe-weather="' . esc_attr( wp_json_encode( $config ) ) . '" data-ltxe-weather-rest="' . esc_url( $rest ) . '"';
		if ( '' !== $style ) {
			echo ' style="' . esc_attr( $style ) . '"';
		}
		echo '>';

		echo '<div class="ltxe-weather__bg" aria-hidden="true">';
		if ( 'video' === $bg_mode ) {
			foreach ( $videos as $cond => $url ) {
				if ( '' === $url ) {
					continue;
				}
				$active = ( $cond === $condition ) ? ' is-active' : '';
				$video_class = 'ltxe-weather__video ltxe-weather__video--' . $cond . $active;
				echo '<video class="' . esc_attr( $video_class ) . '" data-ltxe-cond="' . esc_attr( $cond ) . '" src="' . esc_url( $url ) . '" muted loop playsinline></video>';
			}
		}
		echo '</div>';

		echo '<div class="ltxe-weather__inner">';
		echo '<div class="ltxe-weather__head">';
		echo '<p class="ltxe-weather__city">' . esc_html( $city_label ) . '</p>';
		if ( isset( $settings['geolocate'] ) && 'yes' === $settings['geolocate'] ) {
			echo '<button type="button" class="ltxe-weather__geo" aria-label="' . esc_attr__( 'Use my location', 'landtech-extras-for-elementor' ) . '">' . esc_html__( 'Use my location', 'landtech-extras-for-elementor' ) . '</button>';
		}
		echo '</div>';

		echo '<div class="ltxe-weather__now">';
		if ( isset( $settings['show_icon'] ) && 'yes' === $settings['show_icon'] ) {
			echo '<span class="ltxe-weather__icon" data-ltxe-icon="' . esc_attr( $condition ) . '" aria-hidden="true"></span>';
		}
		if ( isset( $settings['show_temperature'] ) && 'yes' === $settings['show_temperature'] ) {
			$temp = ( $payload && null !== $payload['temperature'] ) ? $payload['temperature'] . $unit_sym : '—';
			echo '<span class="ltxe-weather__temp">' . esc_html( (string) $temp ) . '</span>';
		}
		$desc = $payload && ! empty( $payload['description'] ) ? (string) $payload['description'] : __( 'Loading weather…', 'landtech-extras-for-elementor' );
		echo '<span class="ltxe-weather__desc">' . esc_html( $desc ) . '</span>';
		echo '</div>';

		echo '<ul class="ltxe-weather__meta">';
		if ( isset( $settings['show_feels_like'] ) && 'yes' === $settings['show_feels_like'] ) {
			$feel = ( $payload && null !== $payload['feels_like'] ) ? $payload['feels_like'] . $unit_sym : '—';
			echo '<li class="ltxe-weather__meta-item" data-field="feels_like"><span>' . esc_html__( 'Feels like', 'landtech-extras-for-elementor' ) . '</span> <strong>' . esc_html( (string) $feel ) . '</strong></li>';
		}
		if ( isset( $settings['show_humidity'] ) && 'yes' === $settings['show_humidity'] ) {
			$hum = ( $payload && null !== $payload['humidity'] ) ? $payload['humidity'] . '%' : '—';
			echo '<li class="ltxe-weather__meta-item" data-field="humidity"><span>' . esc_html__( 'Humidity', 'landtech-extras-for-elementor' ) . '</span> <strong>' . esc_html( (string) $hum ) . '</strong></li>';
		}
		if ( isset( $settings['show_wind'] ) && 'yes' === $settings['show_wind'] ) {
			$wind = ( $payload && null !== $payload['wind'] ) ? $payload['wind'] . ' ' . $wind_unit : '—';
			echo '<li class="ltxe-weather__meta-item" data-field="wind"><span>' . esc_html__( 'Wind', 'landtech-extras-for-elementor' ) . '</span> <strong>' . esc_html( (string) $wind ) . '</strong></li>';
		}
		if ( isset( $settings['show_uv'] ) && 'yes' === $settings['show_uv'] ) {
			$uv = ( $payload && null !== $payload['uv'] ) ? (string) $payload['uv'] : '—';
			echo '<li class="ltxe-weather__meta-item" data-field="uv"><span>' . esc_html__( 'UV', 'landtech-extras-for-elementor' ) . '</span> <strong>' . esc_html( $uv ) . '</strong></li>';
		}
		echo '</ul>';

		if ( isset( $settings['show_forecast'] ) && 'yes' === $settings['show_forecast'] ) {
			echo '<ol class="ltxe-weather__forecast">';
			$days = ( $payload && ! empty( $payload['forecast'] ) && is_array( $payload['forecast'] ) ) ? $payload['forecast'] : array();
			if ( empty( $days ) ) {
				echo '<li class="ltxe-weather__day ltxe-weather__day--empty">' . esc_html__( 'Forecast loading…', 'landtech-extras-for-elementor' ) . '</li>';
			} else {
				foreach ( $days as $day ) {
					if ( ! is_array( $day ) ) {
						continue;
					}
					$d_cond = isset( $day['condition'] ) ? sanitize_key( (string) $day['condition'] ) : 'cloudy';
					$d_max  = isset( $day['temp_max'] ) && null !== $day['temp_max'] ? $day['temp_max'] . $unit_sym : '—';
					$d_min  = isset( $day['temp_min'] ) && null !== $day['temp_min'] ? $day['temp_min'] . $unit_sym : '—';
					$d_date = isset( $day['date'] ) ? (string) $day['date'] : '';
					echo '<li class="ltxe-weather__day" data-ltxe-condition="' . esc_attr( $d_cond ) . '">';
					echo '<span class="ltxe-weather__day-date">' . esc_html( $d_date ) . '</span>';
					echo '<span class="ltxe-weather__day-range">' . esc_html( $d_min . ' / ' . $d_max ) . '</span>';
					echo '</li>';
				}
			}
			echo '</ol>';
		}

		echo '</div></div>';
	}
}
