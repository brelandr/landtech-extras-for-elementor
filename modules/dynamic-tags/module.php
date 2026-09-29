<?php
namespace LandTechExtras\Modules\DynamicTags;

use LandTechExtras\Base\Module_Base;
use LandTechExtras\Modules\DynamicTags\Tags\AcfFree\Acf_Text;
use LandTechExtras\Modules\DynamicTags\Tags\AcfFree\Acf_Textarea;
use LandTechExtras\Modules\DynamicTags\Tags\AcfFree\Acf_Number;
use LandTechExtras\Modules\DynamicTags\Tags\AcfFree\Acf_Email;
use LandTechExtras\Modules\DynamicTags\Tags\AcfFree\Acf_Url;
use LandTechExtras\Modules\DynamicTags\Tags\AcfFree\Acf_Image;
use LandTechExtras\Modules\DynamicTags\Tags\AcfFree\Acf_True_False;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF Free dynamic tags.
 *
 * @since 2.9.0
 */
class Module extends Module_Base {

	/**
	 * Hook tag registration after the parent widget hook.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_tags' ) );
	}

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'dynamic-tags';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array();
	}

	/**
	 * Register ACF Free tags when ACF is available.
	 *
	 * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags Elementor manager.
	 * @return void
	 */
	public function register_tags( $dynamic_tags ) {
		if ( ! function_exists( 'get_field' ) ) {
			return;
		}
		if ( ! is_object( $dynamic_tags ) || ! method_exists( $dynamic_tags, 'register' ) ) {
			return;
		}
		if ( method_exists( $dynamic_tags, 'register_group' ) ) {
			$dynamic_tags->register_group(
				'ltxe-acf',
				array(
					'title' => __( 'LandTech ACF', 'landtech-extras-for-elementor' ),
				)
			);
		}
		$dynamic_tags->register( new Acf_Text() );
		$dynamic_tags->register( new Acf_Textarea() );
		$dynamic_tags->register( new Acf_Number() );
		$dynamic_tags->register( new Acf_Email() );
		$dynamic_tags->register( new Acf_Url() );
		$dynamic_tags->register( new Acf_Image() );
		$dynamic_tags->register( new Acf_True_False() );
	}
}
