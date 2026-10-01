<?php
/**
 * PDF embed module.
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Modules\PdfEmbed;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 3.1.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'pdf-embed';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array( 'Pdf_Embed' );
	}
}
