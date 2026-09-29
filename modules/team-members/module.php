<?php
namespace LandTechExtras\Modules\TeamMembers;

use LandTechExtras\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Team members grid module.
 *
 * @since 2.9.0
 */
class Module extends Module_Base {

	/**
	 * @inheritDoc
	 */
	public function get_name() {
		return 'team-members';
	}

	/**
	 * @inheritDoc
	 */
	public function get_widgets() {
		return array(
			'Team_Members',
		);
	}
}
