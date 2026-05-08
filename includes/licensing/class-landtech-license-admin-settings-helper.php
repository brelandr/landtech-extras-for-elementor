<?php
/**
 * Reusable helper to register License Server settings on General Settings.
 *
 * @package Wedding_Party_RSVP_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Landtech_License_Admin_Settings_Helper' ) ) {
	class Landtech_License_Admin_Settings_Helper {
		public static function register_general_settings( $args ) {
			$defaults = array(
				'section_id'            => 'landtech_license_server_section',
				'section_title'         => __( 'License Server', 'wedding-party-rsvp-pro' ),
				'option_store_url'      => 'landtech_license_store_url',
				'option_product_id'     => 'landtech_license_product_id',
				'option_shared_secret'  => 'landtech_license_shared_secret',
				'default_store_url'     => '',
				'default_product_id'    => '',
				'store_url_label'       => __( 'License Server URL', 'wedding-party-rsvp-pro' ),
				'product_id_label'      => __( 'License Product ID', 'wedding-party-rsvp-pro' ),
				'shared_secret_label'   => __( 'License Shared Secret', 'wedding-party-rsvp-pro' ),
				'store_url_placeholder' => 'https://your-license-server.com',
			);

			$config = wp_parse_args( $args, $defaults );

			register_setting( 'general', $config['option_store_url'], array( 'type' => 'string', 'sanitize_callback' => 'esc_url_raw' ) );
			register_setting( 'general', $config['option_product_id'], array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );
			register_setting( 'general', $config['option_shared_secret'], array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ) );

			add_settings_section( $config['section_id'], $config['section_title'], '__return_false', 'general' );

			add_settings_field(
				$config['option_store_url'],
				$config['store_url_label'],
				array( __CLASS__, 'render_store_url_field' ),
				'general',
				$config['section_id'],
				array(
					'option_name' => $config['option_store_url'],
					'default'     => $config['default_store_url'],
					'placeholder' => $config['store_url_placeholder'],
				)
			);

			add_settings_field(
				$config['option_product_id'],
				$config['product_id_label'],
				array( __CLASS__, 'render_product_id_field' ),
				'general',
				$config['section_id'],
				array(
					'option_name' => $config['option_product_id'],
					'default'     => $config['default_product_id'],
				)
			);

			add_settings_field(
				$config['option_shared_secret'],
				$config['shared_secret_label'],
				array( __CLASS__, 'render_shared_secret_field' ),
				'general',
				$config['section_id'],
				array(
					'option_name' => $config['option_shared_secret'],
					'default'     => '',
				)
			);
		}

		public static function render_store_url_field( $args ) {
			$option_name = isset( $args['option_name'] ) ? (string) $args['option_name'] : '';
			$default     = isset( $args['default'] ) ? (string) $args['default'] : '';
			$placeholder = isset( $args['placeholder'] ) ? (string) $args['placeholder'] : 'https://your-license-server.com';
			$value       = get_option( $option_name, $default );
			echo '<input type="url" name="' . esc_attr( $option_name ) . '" class="regular-text" value="' . esc_attr( (string) $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" />';
		}

		public static function render_product_id_field( $args ) {
			$option_name = isset( $args['option_name'] ) ? (string) $args['option_name'] : '';
			$default     = isset( $args['default'] ) ? (string) $args['default'] : '';
			$value       = get_option( $option_name, $default );
			echo '<input type="text" name="' . esc_attr( $option_name ) . '" class="regular-text" value="' . esc_attr( (string) $value ) . '" />';
		}

		public static function render_shared_secret_field( $args ) {
			$option_name = isset( $args['option_name'] ) ? (string) $args['option_name'] : '';
			$default     = isset( $args['default'] ) ? (string) $args['default'] : '';
			$value       = get_option( $option_name, $default );
			echo '<input type="text" name="' . esc_attr( $option_name ) . '" class="regular-text" value="' . esc_attr( (string) $value ) . '" autocomplete="off" />';
		}
	}
}
