<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras;

use \Elementor\Base_Data_Control;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Snazzy Maps control
 *
 * A control for displaying results using the snazzy maps API.
 *
 * @since 2.0.0
 */
class Control_Snazzy extends Base_Data_Control {

	/**
	 * Get control type.
	 *
	 * Retrieve the control type, in this case `ee-snazzy`.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'ee-snazzy';
	}

	/**
	 * Get control default settings.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		$plugin = \LandTechExtras\LandTechExtrasPlugin::$instance;

		return [
			'snazzy_options' => [
				'key' 		=> $plugin->settings->get_option( 'snazzy_maps_api_key', 'landtech_extras_apis', false ),
				'endpoint'	=> $plugin->settings->get_option( 'snazzy_maps_endpoint', 'landtech_extras_apis', false ) || 'explore',
				'term'		=> 'color',
			],
			'label_block'	=> true,
		];
	}

	/**
	 * Render select2 control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<# if ( data.snazzy_options.key ) { #>
			<div class="elementor-control-field ee-control-field">
				<label for="<?php echo esc_attr( $control_uid ); ?>" class="elementor-control-title ee-control-field__title">{{{ data.label }}}</label>
				<div class="elementor-control-input-wrapper ee-control-field__input-wrapper">
					<select id="<?php echo esc_attr( $control_uid ); ?>" class="elementor-select2  ee-control ee-control--select2" type="select2" data-setting="{{ data.name }}">
					<# if ( data.controlValue ) {
						var value = JSON.parse( data.controlValue ); #>
						<option selected value="{{ value.id }}">{{{ value.name }}}</option>
					<# } #>
					</select>
				</div>
			</div>
			<# if ( data.description ) { #>
				<div class="elementor-control-field-description">{{{ data.description }}}</div>
			<# } #>
		<# } else { #>
			<div class="elementor-control-field-description">
				<div class="elementor-panel-alert elementor-panel-alert-warning">
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: 1–2: opening and closing link markup to plugin API settings. */
							__( 'Looks like you haven\'t added your Snazzy Maps API key. Click %1$shere%2$s to set it up.', 'landtech-extras-for-elementor' ),
							'<a target="_blank" href="' . esc_url( admin_url( 'admin.php?page=landtech-extras#landtech_extras_apis' ) ) . '">',
							'</a>'
						)
					);
					?>
				</div>
			</div>
		<# } #>
		<?php
	}

}