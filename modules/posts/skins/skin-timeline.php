<?php
namespace LandTechExtras\Modules\Posts\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Vertical timeline layout skin.
 *
 * @since 2.2.102
 */
class Skin_Timeline extends Skin_Classic {

	/**
	 * @inheritDoc
	 */
	public function get_id() {
		return 'timeline';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Timeline', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	protected function render_loop_start() {
		$this->parent->add_render_attribute( 'loop', 'class', 'ee-posts-layout-skin--timeline' );
		parent::render_loop_start();
	}

	/**
	 * @inheritDoc
	 */
	protected function render_post_start() {
		parent::render_post_start();
		$grid_item_key = 'grid-item-' . get_the_ID();
		$this->parent->add_render_attribute( $grid_item_key, 'class', 'ee-posts-layout-skin__timeline-item' );
	}
}
