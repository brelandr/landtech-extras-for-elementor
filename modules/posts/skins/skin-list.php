<?php
namespace LandTechExtras\Modules\Posts\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Horizontal list layout skin.
 *
 * @since 2.2.102
 */
class Skin_List extends Skin_Classic {

	/**
	 * @inheritDoc
	 */
	public function get_id() {
		return 'list';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'List', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	protected function render_post() {
		$this->render_horizontal_post();
	}

	/**
	 * @inheritDoc
	 */
	protected function render_post_start() {
		parent::render_post_start();
		$grid_item_key = 'grid-item-' . get_the_ID();
		$this->parent->add_render_attribute( $grid_item_key, 'class', 'ee-posts-layout-skin--list' );
	}
}
