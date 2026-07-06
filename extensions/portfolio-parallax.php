<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.

namespace LandTechExtras\Extensions;

use LandTechExtras\Base\Extension_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Portfolio Extension
 *
 * Extends the options and design of the default
 * portfolio widget
 *
 * @since 0.1.0
 */
class Extension_Portfolio_Parallax extends Extension_Base {
	/**
	 * A list of scripts that the widgets is depended in
	 *
	 * @since 1.8.0
	 **/
	public function get_script_depends() {
		return [
			'landtech-extras-parallax-gallery',
		];
	}

	/**
	 * The description of the current extension
	 *
	 * @since 1.8.0
	 **/
	public static function get_description() {
		return __( 'Adds options to parallax gallery items for the Elementor Pro Portfolio widget. Can be found under Content &rarr; LandTech Extras &rarr; Parallax.', 'landtech-extras-for-elementor' );
	}

	/**
	 * Add Actions
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	private function add_controls( $element, $args ) {

		$element->start_controls_section(
			'section_landtech_extras',
			[
				'label' => __( 'LandTech Extras', 'landtech-extras-for-elementor' ),
			]
		);

			// Make sure columns are available for our plugin
			$element->update_control( 'columns', [ 'frontend_available' => true ] );

			$element->add_control(
				'parallax_enable',
				[
					'label'			=> __( 'Parallax', 'landtech-extras-for-elementor' ),
					'type' 			=> Controls_Manager::SWITCHER,
					'default' 		=> '',
					'label_on' 		=> __( 'Yes', 'landtech-extras-for-elementor' ),
					'label_off' 	=> __( 'No', 'landtech-extras-for-elementor' ),
					'return_value' 	=> 'yes',
					'frontend_available' => true,
				]
			);

			$element->add_control(
				'parallax_disable_on',
				[
					'label' 	=> __( 'Disable for', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SELECT,
					'default' 	=> 'mobile',
					'options' 			=> [
						'none' 		=> __( 'None', 'landtech-extras-for-elementor' ),
						'mobile' 	=> __( 'Mobile only', 'landtech-extras-for-elementor' ),
						'tablet' 	=> __( 'Mobile and tablet', 'landtech-extras-for-elementor' ),
					],
					'condition' => [
						'parallax_enable' => 'yes',
					],
					'frontend_available' => true,
				]
			);

			$element->add_responsive_control(
				'parallax_speed',
				[
					'label' 	=> __( 'Parallax speed', 'landtech-extras-for-elementor' ),
					'type' 		=> Controls_Manager::SLIDER,
					'default'	=> [
						'size'	=> 0.5
					],
					'tablet_default' => [
						'size'	=> 0.5
					],
					'mobile_default' => [
						'size'	=> 0.5
					],
					'range' 	=> [
						'px' 	=> [
							'min'	=> 0.05,
							'max' 	=> 1,
							'step'	=> 0.01,
						],
					],
					'condition' => [
						'parallax_enable' => 'yes',
					],
					'frontend_available' => true,
				]
			);

		$element->end_controls_section();

	}

	/**
	 * Add Actions
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	protected function add_actions() {


		// ——— CUSTOM CONTROLS

		add_action( 'elementor/element/portfolio/section_layout/after_section_end', function( $element, $args ) {

			$this->add_controls( $element, $args );

		}, 10, 2 );

	}

	/**
	 * Method for setting extension dependancy on Elementor Pro plugin
	 *
	 * When returning true it doesn't allow the extension to be registered
	 *
	 * @access public
	 * @since 1.8.0
	 * @return bool
	 */
	public static function requires_elementor_pro() {
		return true;
	}

}