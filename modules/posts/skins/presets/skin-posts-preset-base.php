<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras\Modules\Posts\Skins\Presets;

use LandTechExtras\Modules\Posts\Skins\Skin_Classic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base for Posts Extra "preset" skins: same behaviors as Classic, distinct wrapper CSS class.
 *
 * @since  2.2.54
 */
abstract class Skin_Posts_Preset_Base extends Skin_Classic {

	/**
	 * Skin `_skin` values that pair with frontend classes `ee-posts-extra-skin--{id}`.
	 *
	 * @since 2.2.54
	 * @return string[]
	 */
	public static function get_all_preset_skin_ids(): array {
		return [
			'editorial',
			'studio',
			'brutalist',
			'minimal',
			'glass',
			'magazine',
			'lift',
			'capsule',
			'cinema',
			'soft',
		];
	}

	/**
	 * @since  2.2.54
	 * @return string
	 */
	abstract protected function get_preset_slug();

	/**
	 * @since  2.2.54
	 * @return string
	 */
	abstract protected function get_preset_title();

	/**
	 * @since 2.2.54
	 * @inheritDoc
	 */
	public function get_id() {
		return $this->get_preset_slug();
	}

	/**
	 * @since 2.2.54
	 * @inheritDoc
	 */
	public function get_title() {
		return $this->get_preset_title();
	}
}
