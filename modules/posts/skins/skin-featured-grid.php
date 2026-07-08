<?php
namespace LandTechExtras\Modules\Posts\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Featured + grid magazine layout skin.
 *
 * @since 2.2.102
 */
class Skin_Featured_Grid extends Skin_Classic {

	/**
	 * @inheritDoc
	 */
	public function get_id() {
		return 'featured-grid';
	}

	/**
	 * @inheritDoc
	 */
	public function get_title() {
		return __( 'Featured grid', 'landtech-extras-for-elementor' );
	}

	/**
	 * @inheritDoc
	 */
	protected function render_loop_start() {
		$this->parent->add_render_attribute( 'loop', 'class', 'ee-posts-layout-skin--featured-grid' );
		parent::render_loop_start();
	}

	/**
	 * @inheritDoc
	 */
	protected function render_post_start() {
		parent::render_post_start();
		$grid_item_key = 'grid-item-' . get_the_ID();
		$index         = (int) $this->ltx_loop_item_index;

		if ( 0 === $index ) {
			$this->parent->add_render_attribute( $grid_item_key, 'class', 'ee-posts-layout-skin__featured' );
		} else {
			$this->parent->add_render_attribute( $grid_item_key, 'class', 'ee-posts-layout-skin__secondary' );
		}
	}
}
