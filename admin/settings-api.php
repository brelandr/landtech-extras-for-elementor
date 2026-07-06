<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Settings_API
 *
 * @since 1.8.0
 */
class Settings_API {

	/**
	 * Settings sections array
	 *
	 * @since 1.8.0
	 * @var array
	 */
	protected $settings_sections = array();

	/**
	 * Settings prefix
	 *
	 * @since 1.8.0
	 * @var array
	 */
	protected $settings_prefix = 'landtech_extras_';

	/**
	 * Settings fields array
	 *
	 * @since 1.8.0
	 * @var array
	 */
	protected $settings_fields = array();

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts and styles — only on the LandTech Extras settings pages.
	 *
	 * Limiting to the plugin's own screen avoids loading wp_enqueue_media() (which
	 * pulls in the full media-upload stack) and wp-color-picker on every admin page.
	 *
	 * @since 1.8.0
	 */
	function admin_enqueue_scripts() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		// Only load on Elementor → LandTech Extras sub-pages (screen id contains our page slug).
		if ( ! $screen || false === strpos( $screen->id, 'landtech-extras' ) ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_media();
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Set settings sections
	 *
	 * @since 1.8.0
	 * @param array   $sections setting sections array
	 */
	function set_sections( $sections ) {
		$this->settings_sections = $sections;

		return $this;
	}

	/**
	 * Add a single section
	 *
	 * @since 1.8.0
	 * @param array   $section
	 */
	function add_section( $section ) {
		$this->settings_sections[] = $section;

		return $this;
	}

	/**
	 * Set settings fields
	 *
	 * @since 1.8.0
	 * @param array   $fields settings fields array
	 */
	function set_fields( $fields ) {
		$this->settings_fields = $fields;

		return $this;
	}

	/**
	 * Add settings fields
	 *
	 * @since 1.8.0
	 * @param string  $section settings section
	 * @param array   $fields field args
	 */
	function add_field( $section, $field ) {
		$defaults = array(
			'name'  => '',
			'label' => '',
			'desc'  => '',
			'desc_paragraph'  => '',
			'note'	=> '',
			'note_paragraph'  => '',
			'type'  => 'text',
			'disabled' => false,
		);

		$arg = wp_parse_args( $field, $defaults );
		$this->settings_fields[$section][] = $arg;

		return $this;
	}

	/**
	 * Initialize and registers the settings sections and fileds to WordPress
	 *
	 * Usually this should be called at `admin_init` hook.
	 *
	 * This function gets the initiated settings sections and fields. Then
	 * registers them to WordPress and ready for use.
	 *
	 * @since 1.8.0
	 */
	function admin_init() {

		// Register settings sections
		foreach ( $this->settings_sections as $section ) {

			if ( $this->is_tab_linked( $section ) )
				continue;

			if ( false == get_option( $section['id'] ) ) {
				add_option( $section['id'] );
			}

			if ( isset( $section['desc'] ) && ! empty( $section['desc'] ) ) {
				$section['desc'] = $section['desc'];
				$callback = function() use ( $section ) {
					echo wp_kses_post( $section['desc'] );
				};
			} else if ( isset( $section['callback'] ) ) {
				$callback = $section['callback'];
			} else {
				$callback = null;
			}

			add_settings_section( $section['id'], $section['title'], $callback, $section['id'] );
		}

		// Register settings fields
		foreach ( $this->settings_fields as $section => $field ) {
			foreach ( $field as $option ) {

				$name 		= $option['name'];
				$type 		= isset( $option['type'] ) ? $option['type'] : 'text';
				$label 		= isset( $option['label'] ) ? $option['label'] : '';
				$callback 	= isset( $option['callback'] ) ? $option['callback'] : array( $this, 'callback_' . $type );

				$args = array(
					'id'                => $name,
					'class'             => isset( $option['class'] ) ? $option['class'] : $name,
					'label_for'         => "{$section}[{$name}]",
					'desc'              => isset( $option['desc'] ) ? $option['desc'] : '',
					'no_desc_p'			=> isset( $option['no_desc_p'] ) ? $option['no_desc_p'] : false,
					'note'              => isset( $option['note'] ) ? $option['note'] : '',
					'no_note_p'			=> isset( $option['no_note_p'] ) ? $option['no_note_p'] : false,
					'name'              => $label,
					'section'           => $section,
					'size'              => isset( $option['size'] ) ? $option['size'] : null,
					'options'           => isset( $option['options'] ) ? $option['options'] : '',
					'std'               => isset( $option['default'] ) ? $option['default'] : '',
					'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
					'type'              => $type,
					'placeholder'       => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
					'min'               => isset( $option['min'] ) ? $option['min'] : '',
					'max'               => isset( $option['max'] ) ? $option['max'] : '',
					'step'              => isset( $option['step'] ) ? $option['step'] : '',
					'disabled'			=> isset( $option['disabled'] ) ? 'disabled' : '',
				);

				add_settings_field( "{$section}[{$name}]", $label, $callback, $section, $section, $args );
			}
		}

		// Creates our settings in the options table.
		// Use the array-argument form required by WordPress Plugin Check (WP 4.7+).
		foreach ( $this->settings_sections as $section ) {
			register_setting(
				$section['id'],
				$section['id'],
				array(
					'type'              => 'array',
					'sanitize_callback' => array( $this, 'sanitize_options' ),
					'description'       => isset( $section['title'] ) ? $section['title'] : '',
				)
			);
		}
	}

	/**
	 * Returns the field description markup
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	public function get_field_description( $args ) {
		if ( ! empty( $args['desc'] ) ) {
			$before = '';
			$after = '';

			if ( false === $args['no_desc_p'] ) {
				$before = '<p class="description ee-description">';
				$after = '</p>';
			}

			return $before . wp_kses_post( $args['desc'] ) . $after;
		}

		return '';
	}

	/**
	 * Returns the field notice markup
	 *
	 * @since 1.8.0
	 */
	function get_field_note( $args ) {

		if ( ! empty( $args['note'] ) ) {
			$before = '';
			$after = '';

			if ( false === $args['no_note_p'] ) {
				$before = '<p class="note ee-note">';
				$after = '</p>';
			}

			return $before . wp_kses_post( $args['note'] ) . $after;
		}

		return '';
	}

	/**
	 * Displays a text field for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_text( $args ) {

		$value       = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$type        = isset( $args['type'] ) ? $args['type'] : 'text';
		$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		$id_name     = $args['section'] . '[' . $args['id'] . ']';

		printf(
			'<input type="%1$s" class="%2$s-text" id="%3$s" name="%3$s" value="%4$s"%5$s/>',
			esc_attr( (string) $type ),
			esc_attr( (string) $size ),
			esc_attr( $id_name ),
			esc_attr( (string) $value ),
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute fragment built only from esc_attr( placeholder ).
			$placeholder
		);
		echo wp_kses_post( $this->get_field_description( $args ) );
		echo wp_kses_post( $this->get_field_note( $args ) );
	}

	/**
	 * Displays a url field for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_url( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Displays a number field for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_number( $args ) {
		$value       = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$type        = isset( $args['type'] ) ? $args['type'] : 'number';
		$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		$min         = empty( $args['min'] ) ? '' : ' min="' . esc_attr( (string) $args['min'] ) . '"';
		$max         = empty( $args['max'] ) ? '' : ' max="' . esc_attr( (string) $args['max'] ) . '"';
		$step        = empty( $args['step'] ) ? '' : ' step="' . esc_attr( (string) $args['step'] ) . '"';
		$id_name     = $args['section'] . '[' . $args['id'] . ']';

		printf(
			'<input type="%1$s" class="%2$s-number" id="%3$s" name="%3$s" value="%4$s"%5$s%6$s%7$s%8$s/>',
			esc_attr( (string) $type ),
			esc_attr( (string) $size ),
			esc_attr( $id_name ),
			esc_attr( (string) $value ),
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute fragments (placeholder, min, max, step) built with esc_attr above.
			$placeholder,
			$min,
			$max,
			$step
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		echo wp_kses_post( $this->get_field_description( $args ) );
		echo wp_kses_post( $this->get_field_note( $args ) );
	}

	/**
	 * Displays a checkbox for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_checkbox( $args ) {

		$value   = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$id_name = $args['section'] . '[' . $args['id'] . ']';

		echo '<fieldset>';
		printf( '<label for="%1$s">', esc_attr( $id_name ) );
		printf( '<input type="hidden" name="%1$s" value="off" />', esc_attr( $id_name ) );
		printf(
			'<input type="checkbox" class="checkbox" id="%1$s" name="%1$s" value="on"%2$s%3$s />',
			esc_attr( $id_name ),
			checked( $value, 'on', false ),
			disabled( ! empty( $args['disabled'] ), true, false )
		);
		echo wp_kses_post( $args['desc'] );
		echo '</label>';
		echo wp_kses_post( $this->get_field_note( $args ) );
		echo '</fieldset>';
	}

	/**
	 * Displays a multicheckbox for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_multicheck( $args ) {

		$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$base  = $args['section'] . '[' . $args['id'] . ']';

		echo '<fieldset>';
		printf( '<input type="hidden" name="%s" value="" />', esc_attr( $base ) );

		foreach ( $args['options'] as $key => $label ) {
			$checked   = isset( $value[ $key ] ) ? $value[ $key ] : '0';
			$field_id  = $args['section'] . '[' . $args['id'] . '][' . $key . ']';
			printf( '<label for="%s">', esc_attr( $field_id ) );
			printf(
				'<input type="checkbox" class="checkbox" id="%1$s" name="%1$s" value="%2$s" %3$s />',
				esc_attr( $field_id ),
				esc_attr( (string) $key ),
				checked( $checked, $key, false )
			);
			echo esc_html( $label );
			echo '</label><br>';
		}

		echo wp_kses_post( $this->get_field_description( $args ) );
		echo wp_kses_post( $this->get_field_note( $args ) );
		echo '</fieldset>';
	}

	/**
	 * Displays a radio button for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_radio( $args ) {

		$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$base  = $args['section'] . '[' . $args['id'] . ']';

		echo '<fieldset>';

		foreach ( $args['options'] as $key => $label ) {
			$field_id = $args['section'] . '[' . $args['id'] . '][' . $key . ']';
			printf( '<label for="%s">', esc_attr( $field_id ) );
			printf(
				'<input type="radio" class="radio" id="%1$s" name="%2$s" value="%3$s" %4$s />',
				esc_attr( $field_id ),
				esc_attr( $base ),
				esc_attr( (string) $key ),
				checked( $value, $key, false )
			);
			echo esc_html( $label );
			echo '</label><br>';
		}

		echo wp_kses_post( $this->get_field_description( $args ) );
		echo wp_kses_post( $this->get_field_note( $args ) );
		echo '</fieldset>';
	}

	/**
	 * Displays a selectbox for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_select( $args ) {

		$value   = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$size    = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$id_name = $args['section'] . '[' . $args['id'] . ']';

		printf(
			'<select class="%1$s" name="%2$s" id="%2$s">',
			esc_attr( (string) $size ),
			esc_attr( $id_name )
		);

		foreach ( $args['options'] as $key => $label ) {
			printf(
				'<option value="%1$s"%2$s>%3$s</option>',
				esc_attr( (string) $key ),
				selected( (string) $value, (string) $key, false ),
				esc_html( $label )
			);
		}

		echo '</select>';
		echo wp_kses_post( $this->get_field_description( $args ) );
		echo wp_kses_post( $this->get_field_note( $args ) );
	}

	/**
	 * Displays a textarea for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_textarea( $args ) {

		$value       = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		$id_name     = $args['section'] . '[' . $args['id'] . ']';

		printf(
			'<textarea rows="5" cols="55" class="%1$s-text" id="%2$s" name="%2$s"%3$s>%4$s</textarea>',
			esc_attr( (string) $size ),
			esc_attr( $id_name ),
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute fragment built with esc_attr( placeholder ).
			$placeholder,
			esc_textarea( (string) $value )
		);
		echo wp_kses_post( $this->get_field_description( $args ) );
		echo wp_kses_post( $this->get_field_note( $args ) );
	}

	/**
	 * Displays the html for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 * @return string
	 */
	function callback_html( $args ) {
		echo wp_kses_post( $this->get_field_description( $args ) );
		echo wp_kses_post( $this->get_field_note( $args ) );
	}

	/**
	 * Displays a rich text textarea for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_wysiwyg( $args ) {

		$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '500px';

		printf( '<div style="max-width: %s;">', esc_attr( (string) $size ) );

		$editor_settings = array(
			'teeny'         => true,
			'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
			'textarea_rows' => 10
		);

		if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
			$editor_settings = array_merge( $editor_settings, $args['options'] );
		}

		wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );

		echo '</div>';

		echo wp_kses_post( $this->get_field_description( $args ) );
		echo wp_kses_post( $this->get_field_note( $args ) );
	}

	/**
	 * Displays a file upload field for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_file( $args ) {

		$value = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$label = isset( $args['options']['button_label'] ) ? $args['options']['button_label'] : __( 'Choose File', 'landtech-extras-for-elementor' );
		$id_name = $args['section'] . '[' . $args['id'] . ']';

		printf(
			'<input type="text" class="%1$s-text wpsa-url" id="%2$s" name="%2$s" value="%3$s"/>',
			esc_attr( (string) $size ),
			esc_attr( $id_name ),
			esc_attr( (string) $value )
		);
		printf( '<input type="button" class="button wpsa-browse" value="%s" />', esc_attr( $label ) );
		echo wp_kses_post( $this->get_field_description( $args ) );
		echo wp_kses_post( $this->get_field_note( $args ) );
	}

	/**
	 * Displays a password field for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_password( $args ) {

		$value   = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$size    = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$id_name = $args['section'] . '[' . $args['id'] . ']';

		printf(
			'<input type="password" class="%1$s-text" id="%2$s" name="%2$s" value="%3$s"/>',
			esc_attr( (string) $size ),
			esc_attr( $id_name ),
			esc_attr( (string) $value )
		);
		echo wp_kses_post( $this->get_field_description( $args ) );
		echo wp_kses_post( $this->get_field_note( $args ) );
	}

	/**
	 * Displays a color picker field for a settings field
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_color( $args ) {

		$value   = $this->get_option( $args['id'], $args['section'], $args['std'] );
		$size    = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		$std     = isset( $args['std'] ) ? $args['std'] : '';
		$id_name = $args['section'] . '[' . $args['id'] . ']';

		printf(
			'<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s" name="%2$s" value="%3$s" data-default-color="%4$s" />',
			esc_attr( (string) $size ),
			esc_attr( $id_name ),
			esc_attr( (string) $value ),
			esc_attr( (string) $std )
		);
		echo wp_kses_post( $this->get_field_description( $args ) );
		echo wp_kses_post( $this->get_field_note( $args ) );
	}


	/**
	 * Displays a select box for creating the pages select box
	 *
	 * @since 1.8.0
	 * @param array   $args settings field args
	 */
	function callback_pages( $args ) {

		$dropdown_args = array(
			'selected' => (int) $this->get_option( $args['id'], $args['section'], $args['std'] ),
			'name'     => $args['section'] . '[' . $args['id'] . ']',
			'id'       => $args['section'] . '[' . $args['id'] . ']',
			'echo'     => 0
		);
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Args only; markup returned below is core-generated by wp_dropdown_pages.
		$html = wp_dropdown_pages( $dropdown_args );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup generated by WordPress core (wp_dropdown_pages).
		echo $html;
	}

	/**
	 * Sanitize callback for Settings API
	 *
	 * @since 1.8.0
	 * @return mixed
	 */
	function sanitize_options( $options ) {

		if ( ! $options ) {
			return $options;
		}

		foreach ( $options as $option_slug => $option_value ) {
			$sanitize_callback = $this->get_sanitize_callback( $option_slug );

			// If callback is set, call it
			if ( $sanitize_callback ) {
				$options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
				continue;
			}

			// Default: no raw request values persisted (Settings API does not require per-field callbacks).
			if ( is_array( $option_value ) ) {
				$options[ $option_slug ] = map_deep( wp_unslash( $option_value ), 'sanitize_text_field' );
			} else {
				$options[ $option_slug ] = sanitize_text_field( wp_unslash( (string) $option_value ) );
			}
		}

		return $options;
	}

	/**
	 * Get sanitization callback for given option slug
	 *
	 * @since 1.8.0
	 * @param string $slug option slug
	 *
	 * @return mixed string or bool false
	 */
	function get_sanitize_callback( $slug = '' ) {
		if ( empty( $slug ) ) {
			return false;
		}

		// Iterate over registered fields and see if we can find proper callback
		foreach( $this->settings_fields as $section => $options ) {
			foreach ( $options as $option ) {
				if ( $option['name'] != $slug ) {
					continue;
				}

				// Return the callback name
				return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
			}
		}

		return false;
	}

	/**
	 * Get the value of a settings field
	 *
	 * @since 1.8.0
	 * @param string  $option  settings field name
	 * @param string  $section the section name this field belongs to
	 * @param string  $default default text if it's not found
	 * @return string
	 */
	function get_option( $option, $section, $default = '' ) {

		$options = get_option( $section );

		if ( isset( $options[$option] ) ) {
			return $options[$option];
		}

		return $default;
	}

	/**
	 * Determines if a tab should be treated as a link
	 *
	 * @since 1.8.0
	 * Shows all the settings section labels as tab
	 */
	function is_tab_linked( $tab ) {
		if ( array_key_exists( 'link', $tab ) )
			return true;
		
		return false;
	}

	/**
	 * Show navigations as tab
	 *
	 * @since 1.8.0
	 * Shows all the settings section labels as tab
	 */
	function render_navigation() {
		$count = count( $this->settings_sections );

		// don't show the navigation if only one section exists
		if ( 1 === $count ) {
			return;
		}

		echo '<h2 class="nav-tab-wrapper ee-nav-tabs">';

		foreach ( $this->settings_sections as $tab ) {

			$link          = '#' . $tab['id'];
			$count_display = isset( $tab['count'] ) ? $tab['count'] : '';
			$icon_class    = isset( $tab['icon'] ) ? $tab['icon'] : '';

			$classes_item = 'nav-tab ee-nav-tabs__tab';

			$count_markup = '';
			if ( ( is_numeric( $count_display ) && $count_display > 0 ) || '' !== $count_display ) {
				$count_class = '';
				if ( isset( $tab['label'] ) ) {
					$count_class = 'ee-count--' . sanitize_html_class( (string) $tab['label'] );
				}
				$count_markup = '<span class="ee-count ' . esc_attr( $count_class ) . '">' . esc_html( (string) $count_display ) . '</span>';
			}

			$icon_markup = '';
			if ( '' !== $icon_class ) {
				$icon_markup = '<span class="' . esc_attr( $icon_class ) . '"></span>';
			}

			if ( $this->is_tab_linked( $tab ) ) {
				$classes_item .= ' ee-nav-tabs__link';
				$link = $tab['link'];
			}

			printf(
				'<a href="%1$s" target="%2$s" class="%3$s" id="%4$s-tab">%5$s%6$s%7$s</a>',
				esc_url( $link ),
				esc_attr( isset( $tab['target'] ) ? (string) $tab['target'] : '' ),
				esc_attr( $classes_item ),
				esc_attr( (string) $tab['id'] ),
				esc_html( (string) $tab['title'] ),
				wp_kses_post( $count_markup ),
				wp_kses_post( $icon_markup )
			);
		}

		echo '</h2>';
	}

	/**
	 * Show the section settings forms
	 *
	 * @since 1.8.0
	 * This function displays every sections in a different form
	 */
	function render_forms() {
		?>
		<div class="metabox-holder ee-metabox-holder ee-settings">
			<?php foreach ( $this->settings_sections as $form ) {

				if ( $this->is_tab_linked( $form ) )
					continue;

				?>
				<div id="<?php echo esc_attr( (string) $form['id'] ); ?>" class="ee-settings__group" style="display: none;">
					<form method="post" action="options.php" class="ee-settings__form">
						<?php

						settings_fields( $form['id'] );

						do_settings_sections( $form['id'] );

						if ( ! empty( $this->settings_fields[ $form['id'] ] ) ) {
							submit_button();
						} ?>
					</form>
				</div>
			<?php } ?>
		</div>
		<?php
	}
}