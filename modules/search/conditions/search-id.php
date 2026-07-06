<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Search\Conditions;

use Elementor\Controls_Manager;
use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Search_Id extends Condition_Base {

	public static function get_type() {
		return 'archive';
	}

	public static function get_priority() {
		return 71;
	}

	public function get_name() {
		return 'ee-search-id';
	}

	public function get_label() {
		return __( 'Extras Search Results', 'landtech-extras-for-elementor' );
	}

	public function check( $args = null ) {
		return is_search() && get_query_var('ltxe_search_id') === $args['id'];
	}

	protected function _register_controls() {
		$this->add_control(
			'search_form_id',
			[
				'section' 		=> 'settings',
				'type' 			=> Controls_Manager::TEXT,
				'placeholder'	=> __( 'Search ID', 'landtech-extras-for-elementor' )
			]
		);
	}
}
