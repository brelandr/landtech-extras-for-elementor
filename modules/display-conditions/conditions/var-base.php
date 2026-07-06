<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\DisplayConditions\Conditions;

// LandTech Extras for Elementor Classes
use LandTechExtras\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\DisplayConditions\Conditions
 *
 * @since  2.2.0
 */
class Var_Base extends Condition {

	/**
	 * Get Group
	 * 
	 * Get the group of the condition
	 *
	 * @since  2.2.0
	 * @return string
	 */
	public function get_group() {
		return 'var';
	}

	/**
	 * Get Name Control
	 * 
	 * Get the settings for the name control
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_name_control() {
		return [
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'placeholder'	=> __( 'Name', 'landtech-extras-for-elementor' ),
			'label_block' 	=> true,
		];
	}

	/**
	 * Get Value Control
	 * 
	 * Get the settings for the value control
	 *
	 * @since  2.2.0
	 * @return array
	 */
	public function get_value_control() {
		return [
			'type' 			=> Controls_Manager::TEXT,
			'default' 		=> '',
			'description'	=> __( 'Leave blank to accept any value.', 'landtech-extras-for-elementor' ),
			'placeholder'	=> __( 'Value', 'landtech-extras-for-elementor' ),
			'label_block' 	=> true,
		];
	}

	/**
	 * Sanitize a REQUEST variable name configured in the widget (prevent arbitrary superglobal traversal).
	 *
	 * @param mixed $name Control value from the editor.
	 * @return string Safe key or empty string.
	 */
	protected function sanitize_request_var_key( $name ) {
		if ( null === $name ) {
			return '';
		}
		$key = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $name );
		return strlen( $key ) > 64 ? '' : $key;
	}
}
