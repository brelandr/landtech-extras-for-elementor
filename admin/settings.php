<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Settings
 *
 * @since 1.8.0
 */
class Settings extends Settings_Page {

	const PAGE_ID = 'landtech-extras';

	// Tabs
	const TAB_WIDGETS 		= 'widgets';
	const TAB_EXTENSIONS 	= 'extensions';
	const TAB_ADVANCED 		= 'advanced';
	const TAB_APIS 			= 'apis';
	const TAB_DOCUMENTATION	= 'documentation';
	const TAB_SUPPORT		= 'support';

	private $_tabs;

	private $_widgets_count;

	private $_extensions_count;

	/**
	 * menu
	 *
	 * Adds the item to the menu
	 *
	 * @since 1.8.0
	 *
	 * @access public
	*/

	public function menu() {
		$slug       = 'landtech-extras';
		$capability = 'manage_options';

		// SVG icon: "LE" monogram. WordPress masks it with the correct theme colour.
		$icon_svg = 'data:image/svg+xml;base64,' . base64_encode(
			'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">'
			. '<rect x="2" y="2" width="16" height="16" rx="2" fill="none" stroke="black" stroke-width="1.5"/>'
			. '<text x="4" y="14" font-family="Arial,sans-serif" font-size="9" font-weight="bold" fill="black">LE</text>'
			. '</svg>'
		);

		// Register "Elementor Extras" as a top-level sidebar item at position 26
		// (immediately after Elementor). This means the menu appears even when the
		// premium add-on is not active — the premium plugin adds its own submenus
		// under the same 'landtech-extras' parent slug.
		add_menu_page(
			$this->get_page_title(),
			__( 'Elementor Extras', 'landtech-extras-for-elementor' ),
			$capability,
			$slug,
			[ $this, 'render_page' ],
			$icon_svg,
			26
		);

		// Explicit first submenu entry labelled "Settings" so the sidebar shows
		// a meaningful child label rather than the parent title repeated.
		add_submenu_page(
			$slug,
			$this->get_page_title(),
			__( 'Settings', 'landtech-extras-for-elementor' ),
			$capability,
			$slug,
			[ $this, 'render_page' ]
		);
	}

	/**
	* enqueue_scripts
	*
	* Enqueue styles and scripts
	*
	* @since 1.8.0
	*
	* @access public
	*/
	
	public function enqueue_scripts() {}

	/**
	* Hooked into admin_init action
	*
	* @since 1.8.0
	*
	* @access public
	*/

	public function init() {
		parent::init();

		// Refresh Instagram Access Token
		$this->refresh_instagram_access_token();
	}

	/**
	* Creates the tabs object
	*
	* @since 1.8.0
	*
	* @access protected
	*/

	protected function create_page_tabs() {
		return $this->_tabs;
	}

	/**
	 * Gets the settings sections
	 *
	 * @since 1.8.0
	 *
	 * @access public
	*/

	public function get_settings_sections() {

		$this->_widgets_count = $this->get_widgets_count( true );
		$this->_extensions_count = $this->get_extensions_count( true );

		$sections = array(
			array(
				'id'    => $this->settings_prefix . self::TAB_WIDGETS,
				'title' => __( 'Widgets', 'landtech-extras-for-elementor' ),
				'count'	=> $this->_widgets_count,
				'label' => $this->_widgets_count > 0 ? '' : 'error',
				'desc'	=> __( 'Disable widgets from LandTech Extras. If disabled, a widget will no longer be available in the Elementor editor panel. We strongly recommend disabling the widgets you don\'t plan on using to improve the load time of the Elementor editor.', 'landtech-extras-for-elementor' ),
			),
			array(
				'id'    => $this->settings_prefix . self::TAB_EXTENSIONS,
				'title' => __( 'Extensions', 'landtech-extras-for-elementor' ),
				'count' => $this->_extensions_count,
				'label' => $this->_extensions_count > 0 ? '' : 'error',
				'desc'	=> __( 'LandTech Extras extensions are features added to the default Elementor elements. They display additional controls that can be found usually under the Advanced tab of each element. Below you can disable any or all these extensions. If disabled, these additional controls will no longer be available in the Elementor editor panel.', 'landtech-extras-for-elementor' ),
			),
		);

		$premium_section_id = 'landtech_extras_features';
		if ( class_exists( '\LandTechExtras\Feature_Flags_Settings', false ) ) {
			$premium_section_id = \LandTechExtras\Feature_Flags_Settings::OPTION_KEY;
		}

		$bulk_base    = admin_url( 'admin-post.php' );
		$enable_all   = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'landtech_extras_features_bulk',
					'bulk'   => 'enable',
				),
				$bulk_base
			),
			'landtech_extras_features_bulk'
		);
		$disable_all  = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'landtech_extras_features_bulk',
					'bulk'   => 'disable',
				),
				$bulk_base
			),
			'landtech_extras_features_bulk'
		);
		$premium_howto = '<p class="description"><strong>' . esc_html__( 'How to use this tab', 'landtech-extras-for-elementor' ) . '</strong> — ' . esc_html__( 'In the tab row at the top of this screen, open the Add-on features tab (between Extensions and APIs). Each row below is one optional capability: check or uncheck Enable for that row only, then click Save Changes. A checked box means the feature is allowed on this site when the optional LandTech Extras add-on is active and other requirements are met.', 'landtech-extras-for-elementor' ) . '</p>';

		if ( class_exists( '\LandTechExtras\Feature_Flags_Settings', false ) ) {
			$addon_version = defined( 'LANDTECH_EXTRAS_PREMIUM_PACKAGE_VERSION' )
				? (string) constant( 'LANDTECH_EXTRAS_PREMIUM_PACKAGE_VERSION' )
				: '';
			$registry_count = count( \LandTechExtras\Feature_Flags_Settings::get_registry() );
			$addon_meta     = '';

			if ( '' !== $addon_version ) {
				$addon_meta = '<p class="description">' . sprintf(
					/* translators: 1: Premium add-on semver. 2: Number of feature toggles in the registry. */
					esc_html__( 'LandTech Extras add-on version %1$s — %2$d feature toggles loaded. If expected capabilities are missing, upload the latest Premium package from your LandTech account and confirm both plugins are active.', 'landtech-extras-for-elementor' ),
					esc_html( $addon_version ),
					(int) $registry_count
				) . '</p>';
			}

			$sections[] = array(
				'id'    => $premium_section_id,
				'title' => __( 'Add-on features', 'landtech-extras-for-elementor' ),
				'desc'  => $premium_howto . $addon_meta . sprintf(
					wp_kses(
						/* translators: %1$s: Opening anchor tag for the bulk-enable link. %2$s: Closing anchor tag for bulk-enable. %3$s: Opening anchor tag for the bulk-disable link. %4$s: Closing anchor tag for bulk-disable. */
						__( 'Optional capability lanes when the add-on is active (AJAX loops & faceted filtering, AI workspace, advanced loop query, WooCommerce lanes, platform toggles). Shortcuts: %1$sEnable all%2$s · %3$sDisable all%4$s.', 'landtech-extras-for-elementor' ),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					'<a href="' . esc_url( $enable_all ) . '">',
					'</a>',
					'<a href="' . esc_url( $disable_all ) . '">',
					'</a>'
				),
			);
		} else {
			$diag_html = '';
			if ( function_exists( 'landtech_extras_premium_addon_bootstrap_diagnosis_html' ) ) {
				$diag_html = landtech_extras_premium_addon_bootstrap_diagnosis_html();
			}

			$sections[] = array(
				'id'    => $premium_section_id,
				'title' => __( 'Add-on features', 'landtech-extras-for-elementor' ),
				'desc'  => $diag_html . '<p class="description">' . wp_kses_post(
					sprintf(
						/* translators: %s: URL to the Plugins admin screen. */
						__( 'Install and activate <strong>LandTech Extras for Elementor Premium</strong> (the add-on — not a third plugin) under <a href="%s">Plugins</a>, then reload this tab. Both plugins must stay active.', 'landtech-extras-for-elementor' ),
						esc_url( admin_url( 'plugins.php' ) )
					)
				) . '</p>',
			);
		}

		$sections = array_merge(
			$sections,
			array(
			array(
				'id'    => $this->settings_prefix . self::TAB_APIS,
				'title' => __( 'APIs', 'landtech-extras-for-elementor' ),
			),
			array(
				'id'    => $this->settings_prefix . self::TAB_ADVANCED,
				'title' => __( 'Advanced', 'landtech-extras-for-elementor' ),
			),
			array(
				'id'    => $this->settings_prefix . self::TAB_SUPPORT,
				'title' => __( 'Get Support', 'landtech-extras-for-elementor' ),
				'target'=> '_blank',
				'link'	=> LandTechExtrasPlugin::$instance->get_link('support'),
				'icon'	=> 'dashicons dashicons-external',
			),
			array(
				'id'    => $this->settings_prefix . self::TAB_DOCUMENTATION,
				'title' => __( 'Documentation', 'landtech-extras-for-elementor' ),
				'target'=> '_blank',
				'link'	=> LandTechExtrasPlugin::$instance->get_link('docs'),
				'icon'	=> 'dashicons dashicons-external',
			),
			)
		);

		return $sections;
	}

	/**
	 * Gets the settings fields
	 *
	 * @since 1.8.0
	 *
	 * @access public
	*/

	public function get_settings_fields() {
		$fields = [];

		$sections = $this->get_settings_sections();

		foreach( $sections as $section ) {
			if ( $this->settings_api->is_tab_linked( $section ) )
				continue;

			$fields[ $section['id'] ] = call_user_func( array( $this, 'get_' . str_replace( $this->settings_prefix, '', $section['id'] ) . '_fields' ) );
		}

		return $fields;
	}

	/**
	* Returns the number of Extras widgets
	*
	* @since 2.0.0
	*
	* @access protected
	*/
	protected function get_widgets_count( $enabled_only = false ) {
		$modules = LandTechExtrasPlugin::$instance->modules_manager->get_modules();
		$count = 0;

		foreach( $modules as $module ) {
			$widgets = $module->get_widgets();
			foreach( $widgets as $widget ) {
				if ( ! $enabled_only ) {
					$count ++;
				} else {
					if ( ! $module->is_widget_disabled( strtolower( $widget ) ) ) {
						$count++;
					}
				}
			}
		}

		return $count;
	}

	/**
	* Returns the fields for the widgets section
	*
	* @since 1.8.0
	*
	* @access protected
	*/
	protected function get_widgets_fields() {

		$fields = [];

		$modules = LandTechExtrasPlugin::$instance->modules_manager->get_modules();

		foreach( $modules as $module ) {

			$module_name = $module->get_name();

			$module_class_name = str_replace( '-', ' ', $module_name );
			$module_class_name = str_replace( ' ', '', ucwords( $module_class_name ) );

			$widgets = $module->get_widgets();

			foreach( $widgets as $widget ) {

				$class_name = 'LandTechExtras\Modules\\' . $module_class_name . '\Widgets\\' . $widget;

				$widget_title 	= str_replace( '_', ' ', ucwords( $widget ) );
				$widget_slug 	= strtolower( $widget );

				$field = [
					'name'		=> 'enable_' . $widget_slug,
					'label' 	=> $widget_title,
					'desc' 		=> __( 'Enable', 'landtech-extras-for-elementor' ),
					'type' 		=> 'checkbox',
					'default' 	=> 'on',
				];

				if ( $class_name::requires_elementor_pro() && ! landtech_extras_is_elementor_pro_active() ) {
					$field['type'] = 'html';
					$field['note'] = __( 'You need Elementor Pro installed and activated for this widget to be available.', 'landtech-extras-for-elementor' );

					unset( $field['desc'] );
				}

				$fields[] = $field;

			}
		}

		return $fields;
	}

	/**
	* Returns the number of Extras extensions
	*
	* @since 2.0.0
	*
	* @access protected
	*/
	protected function get_extensions_count( $enabled_only = false ) {
		$extensions = LandTechExtrasPlugin::$instance->extensions_manager->available_extensions;
		$count = 0;

		foreach( $extensions as $extension_id ) {
			$extension_name = str_replace( '-', '_', $extension_id );
			if ( ! $enabled_only ) {
				$count ++;
			} else {
				if ( ! LandTechExtrasPlugin::$instance->extensions_manager->is_disabled( $extension_name ) ) {
					$count++;
				}
			}
		}

		return $count;
	}

	/**
	* Returns the fields for the extensions section
	*
	* @since 1.8.0
	*
	* @access protected
	*/
	protected function get_extensions_fields() {

		$fields = [];

		$extensions = LandTechExtrasPlugin::$instance->extensions_manager->available_extensions;

		foreach( $extensions as $extension_id ) {

			$extension_name = str_replace( '-', '_', $extension_id );
			$class_name = 'LandTechExtras\Extensions\Extension_' . ucwords( $extension_name );

			$extension_title = str_replace( '-', ' ', $extension_id );
			$extension_title = ucwords( $extension_title );

			$description = $class_name::get_description();

			$fields[] = [
				'name'		=> 'enable_' . $extension_name,
				'label' 	=> $extension_title,
				'desc' 		=> __( 'Enable', 'landtech-extras-for-elementor' ),
				'type' 		=> 'checkbox',
				'default' 	=> $class_name::is_default_disabled() ? 'off' : 'on',
				'note'		=> $description,
			];
		}

		return $fields;
	}

	/**
	 * Fields for optional add-on feature flags (option landtech_extras_features; add-on provides registry).
	 *
	 * @since 2.2.70
	 *
	 * @access protected
	 *
	 * @return array<int, array<string, mixed>>
	 */
	protected function get_features_fields() {

		if ( ! class_exists( '\LandTechExtras\Feature_Flags_Settings', false ) ) {
			return apply_filters( 'landtech_extras/settings/features_fields', array() );
		}

		$fields = array();

		foreach ( Feature_Flags_Settings::get_registry() as $feature_id => $meta ) {
			$default = 'on';
			if ( isset( $meta['enabled_by_default'] ) && true !== $meta['enabled_by_default'] ) {
				$default = 'off';
			}

			$bool_default = ( 'on' === $default );
			$runtime_on   = \function_exists( 'landtech_extras_feature_enabled' ) && landtech_extras_feature_enabled( $feature_id, $bool_default );

			$desc_note = isset( $meta['description'] ) ? (string) $meta['description'] : '';

			$runtime_html  = '<strong class="landtech-extras-runtime-status">' . ( $runtime_on
				? esc_html__( 'Runtime status: On', 'landtech-extras-for-elementor' )
				: esc_html__( 'Runtime status: Off', 'landtech-extras-for-elementor' ) ) . '</strong>';
			$runtime_html .= '<br /><span class="description">' . esc_html__( 'Reflects this checkbox plus add-on and other gates. If this stays Off with the box checked, verify the add-on is active and that its feature gates allow this capability.', 'landtech-extras-for-elementor' ) . '</span>';

			$note_combined = $desc_note;
			if ( '' !== $note_combined ) {
				$note_combined .= '<br /><br />';
			}
			$note_combined .= $runtime_html;

			$fields[] = array(
				'name'              => $feature_id,
				'label'             => isset( $meta['label'] ) ? $meta['label'] : $feature_id,
				'desc'              => __( 'Enable this feature', 'landtech-extras-for-elementor' ),
				'type'              => 'checkbox',
				'default'           => $default,
				'note'              => $note_combined,
				'no_note_p'         => true,
				'sanitize_callback' => array( Feature_Flags_Settings::class, 'sanitize_on_off_checkbox' ),
			);
		}

		/**
		 * Allow the add-on (or other packages) to append read-only tools to the Add-on features section.
		 *
		 * @since 2.2.57
		 *
		 * @param array<int, array<string, mixed>> $fields Features fields.
		 */
		return apply_filters( 'landtech_extras/settings/features_fields', $fields );
	}

	/**
	* Return the fields for the advanced section
	*
	* @since 2.0.0
	*
	* @access protected
	*/
	protected function get_advanced_fields() {

		$fields = [
			[
				'name'		=> 'load_google_maps_api',
				'label'		=> __( 'Load Google Maps API', 'landtech-extras-for-elementor' ),
				'desc' 		=> __( 'You can disable loading the Google Maps API script if it\'s already added from a theme or plugin.', 'landtech-extras-for-elementor' ),
				'type'		=> 'radio',
				'default'	=> 'yes',
				'options'	=> [
					'yes' 	=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'no' 	=> __( 'No', 'landtech-extras-for-elementor' ),
				]
			],
			[
				'name'		=> 'enable_svg_uploads',
				'label'		=> __( 'Allow SVG uploads', 'landtech-extras-for-elementor' ),
				'desc' 		=> __( 'Lets users with upload permission add .svg files to the Media Library (sanitized on upload). Required for the Inline SVG widget when using the Media Library source. You can also paste an SVG URL in the widget without enabling this.', 'landtech-extras-for-elementor' ),
				'type'		=> 'radio',
				'default'	=> 'no',
				'options'	=> [
					'yes' 	=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'no' 	=> __( 'No', 'landtech-extras-for-elementor' ),
				]
			],
		];

		return $fields;

	}

	/**
	* Returns the fields for the API section
	*
	* @since 2.0.0
	*
	* @access protected
	*/
	protected function get_apis_fields() {

		$ai_intro = sprintf(
			/* translators: %s: Tab title "Add-on features" (same screen). */
			__( 'Bring-your-own-key LLM access when the LandTech Extras add-on (AI Studio) is in use. Enable %s → AI Studio workspace (BYOK), choose a provider below, and save your key. Keys are sent only to that provider when you use AI features.', 'landtech-extras-for-elementor' ),
			'<strong>' . esc_html__( 'Add-on features', 'landtech-extras-for-elementor' ) . '</strong>'
		);

		$gmap_description = sprintf(
			/* translators: 1–2: link markup to Google Maps API key documentation. */
			__( 'You can get your API key %1$shere%2$s', 'landtech-extras-for-elementor' ),
			'<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">',
			'</a>'
		);

		$snazzy_description = sprintf(
			/* translators: 1–2: link markup to Snazzy Maps developer account. */
			__( 'You can get your API key %1$shere%2$s after you create an account on Snazzy Maps.', 'landtech-extras-for-elementor' ),
			'<a target="_blank" href="https://snazzymaps.com/account/developer">',
			'</a>'
		);

		$insta_token_description = sprintf(
			/* translators: 1–2: link markup to Instagram token documentation. */
			__( 'Find out %1$show to get your instagram access token%2$s.', 'landtech-extras-for-elementor' ),
			'<a target="_blank" href="' . LandTechExtrasPlugin::$instance->get_link('docs_ig_token') . '">',
			'</a>'
		);

		$fields = [
			[
				'name'		=> 'ai_workspace_api_heading',
				'label'		=> __( 'AI / LLM (BYOK)', 'landtech-extras-for-elementor' ),
				'desc' 		=> $ai_intro,
				'type'		=> 'html',
				'no_desc_p'	=> true,
			],
			[
				'name'				=> 'ai_llm_provider',
				'label'				=> __( 'LLM provider', 'landtech-extras-for-elementor' ),
				'desc'				=> __( 'Select which API credentials to use. Leave disabled if you do not use add-on AI features.', 'landtech-extras-for-elementor' ),
				'type'				=> 'select',
				'default'			=> '',
				'sanitize_callback'	=> array( __CLASS__, 'sanitize_ai_llm_provider_setting' ),
				'options'			=> [
					''          => __( '— Disabled —', 'landtech-extras-for-elementor' ),
					'openai'    => __( 'OpenAI', 'landtech-extras-for-elementor' ),
					'anthropic' => __( 'Anthropic', 'landtech-extras-for-elementor' ),
					'gemini'    => __( 'Google Gemini', 'landtech-extras-for-elementor' ),
				],
			],
			[
				'name'		=> 'openai_api_key',
				'label'		=> __( 'OpenAI API key', 'landtech-extras-for-elementor' ),
				'desc' 		=> __( 'Used when the provider is OpenAI.', 'landtech-extras-for-elementor' ),
				'type'		=> 'password',
				'size'		=> 'large',
				'sanitize_callback' => array( __CLASS__, 'sanitize_api_secret_setting' ),
			],
			[
				'name'		=> 'anthropic_api_key',
				'label'		=> __( 'Anthropic API key', 'landtech-extras-for-elementor' ),
				'desc' 		=> __( 'Used when the provider is Anthropic.', 'landtech-extras-for-elementor' ),
				'type'		=> 'password',
				'size'		=> 'large',
				'sanitize_callback' => array( __CLASS__, 'sanitize_api_secret_setting' ),
			],
			[
				'name'		=> 'gemini_api_key',
				'label'		=> __( 'Google Gemini API key', 'landtech-extras-for-elementor' ),
				'desc' 		=> __( 'Used when the provider is Google Gemini.', 'landtech-extras-for-elementor' ),
				'type'		=> 'password',
				'size'		=> 'large',
				'sanitize_callback' => array( __CLASS__, 'sanitize_api_secret_setting' ),
			],
			[
				'name'		=> 'google_maps_api_key',
				'label'		=> __( 'Google Maps API Key', 'landtech-extras-for-elementor' ),
				'desc' 		=> $gmap_description,
				'type'		=> 'text',
				'sanitize_callback' => array( __CLASS__, 'sanitize_api_secret_setting' ),
			],
			[
				'name'		=> 'snazzy_maps_api_key',
				'label'		=> __( 'Snazzy Maps API Key', 'landtech-extras-for-elementor' ),
				'desc' 		=> $snazzy_description,
				'type'		=> 'text',
				'sanitize_callback' => array( __CLASS__, 'sanitize_api_secret_setting' ),
			],			
			[
				'name'		=> 'snazzy_maps_endpoint',
				'label'		=> __( 'Snazzy Maps Endpoint', 'landtech-extras-for-elementor' ),
				'desc' 		=> __( 'Select where to search for map styles. "Explore" searches all public map styles, "My Styles" search the styles you created on Snazzy Maps and "Favorites" fetches styles from the ones you added to your favorites.', 'landtech-extras-for-elementor' ),
				'type'		=> 'select',
				'options'	=> [
					'explore' 	=> __( 'Explore', 'landtech-extras-for-elementor' ),
					'my-styles' => __( 'My Styles', 'landtech-extras-for-elementor' ),
					'favorites' => __( 'Favorites', 'landtech-extras-for-elementor' ),
				],
			],
			[
				'name'		=> 'instagram_access_token',
				'label'		=> __( 'Instagram Access Token', 'landtech-extras-for-elementor' ),
				'desc' 		=> $insta_token_description,
				'type'		=> 'text',
				'sanitize_callback' => array( __CLASS__, 'sanitize_api_secret_setting' ),
			],
		];

		/**
		 * Allow Premium add-on (or other packages) to append APIs tab fields.
		 *
		 * @since 2.2.101
		 *
		 * @param array<int, array<string, mixed>> $fields API settings fields.
		 */
		return apply_filters( 'landtech_extras/settings/apis_fields', $fields );

	}

	/**
	 * Refresh long-lived Instagram access token via the Graph token endpoint.
	 *
	 * Performs at most one successful refresh per throttle window (stored in transient
	 * `UPDATED_INSTA_ACCESS_TOKEN`). Failures cache a short backoff to reduce repeated calls.
	 *
	 * Documented under readme.txt **External Services → Instagram** (`graph.instagram.com`).
	 *
	 * @since 2.2.23
	 * @access public
	 *
	 * @return void
	 */
	public function refresh_instagram_access_token() {
		$update_token_key = self::UPDATED_INSTA_ACCESS_TOKEN;
		$api_endpoint     = self::REFRESH_INSTA_ACCESS_TOKEN_ENDPOINT;
		$access_token     = trim( (string) $this->settings_api->get_option( 'instagram_access_token', 'landtech_extras_apis', false ) );

		if ( '' === $access_token ) {
			return;
		}

		$updated = get_transient( $update_token_key );

		if ( ! empty( $updated ) ) {
			return;
		}

		$endpoint_url = add_query_arg(
			array(
				'access_token' => $access_token,
				'grant_type'   => 'ig_refresh_token',
			),
			$api_endpoint
		);

		$endpoint_url = esc_url_raw( $endpoint_url );
		if ( '' === $endpoint_url ) {
			set_transient( $update_token_key, 'error', HOUR_IN_SECONDS );
			return;
		}

		$response = wp_safe_remote_get(
			$endpoint_url,
			array(
				'timeout'   => 20,
				'sslverify' => true,
			)
		);

		if ( is_wp_error( $response ) ) {
			set_transient( $update_token_key, 'error', HOUR_IN_SECONDS );
			return;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			set_transient( $update_token_key, 'error', HOUR_IN_SECONDS );
			return;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( '' === $body ) {
			set_transient( $update_token_key, 'error', HOUR_IN_SECONDS );
			return;
		}

		$decoded = json_decode( $body, true );

		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
			set_transient( $update_token_key, 'error', HOUR_IN_SECONDS );
			return;
		}

		if ( empty( $decoded['access_token'] ) || ! isset( $decoded['expires_in'] ) ) {
			set_transient( $update_token_key, 'error', DAY_IN_SECONDS );
			return;
		}

		set_transient( $update_token_key, 'updated', 30 * DAY_IN_SECONDS );
	}

	/**
	 * Preserve API keys, tokens, and other secrets without sanitize_text_field() stripping.
	 *
	 * WordPress.org review: password-like values must not use sanitize_text_field().
	 *
	 * @since 2.2.71
	 *
	 * @param mixed $value Raw setting.
	 * @return string
	 */
	public static function sanitize_api_secret_setting( $value ) {

		if ( ! is_scalar( $value ) ) {
			return '';
		}

		return (string) $value;
	}

	/**
	 * Sanitize LLM provider slug for the APIs tab.
	 *
	 * @since 2.2.70
	 *
	 * @param mixed $value Raw setting.
	 * @return string
	 */
	public static function sanitize_ai_llm_provider_setting( $value ) {

		$v = sanitize_key( (string) $value );

		if ( '' === $v ) {
			return '';
		}

		$allowed = array( 'openai', 'anthropic', 'gemini' );

		return in_array( $v, $allowed, true ) ? $v : '';
	}

	/**
	* Returns current page title
	*
	* @since 1.8.0
	*
	* @access protected
	*/
	protected function get_page_title() {
		return __( 'LandTech Extras for Elementor', 'landtech-extras-for-elementor' );
	}

}

// initialize
new Settings();

?>