<?php
/**
 * Seeds WordPress Playground demo pages for LandTech Extras for Elementor.
 *
 * Invoked from assets/blueprints/blueprint.json (runPHP step) after Elementor and
 * this plugin are installed. Not loaded on normal site requests.
 *
 * @package LandTechExtras
 * @since   2.2.78
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Entry point for Playground blueprint seeding.
 *
 * @return int Homepage page ID, or 0 on failure.
 */
function landtech_extras_seed_playground_demos() {
	if ( ! function_exists( 'wp_insert_post' ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	update_option( 'elementor_onboarded', true );
	update_option( 'elementor_experiment-container', 'active' );

	$images = landtech_extras_playground_sideload_images();
	landtech_extras_playground_seed_sample_posts( $images );

	$demos  = landtech_extras_playground_get_demo_definitions( $images );
	$pages  = array();
	$groups = array();

	foreach ( $demos as $demo ) {
		$page_id = landtech_extras_playground_create_elementor_page(
			$demo['title'],
			$demo['slug'],
			$demo['intro'],
			$demo['widgets']
		);

		if ( $page_id <= 0 ) {
			continue;
		}

		$pages[ $demo['slug'] ] = $page_id;
		$group                  = isset( $demo['group'] ) ? $demo['group'] : 'Demos';

		if ( ! isset( $groups[ $group ] ) ) {
			$groups[ $group ] = array();
		}

		$groups[ $group ][] = array(
			'title'   => $demo['title'],
			'slug'    => $demo['slug'],
			'page_id' => $page_id,
		);
	}

	$home_id = landtech_extras_playground_create_homepage( $groups, $pages );
	landtech_extras_playground_assign_nav_menu( $home_id, $groups, $pages );

	if ( $home_id > 0 ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}

	// Pretty permalinks for /demo-*/ URLs used by the nav menu and homepage links.
	flush_rewrite_rules( false );

	return $home_id;
}

/**
 * @return string Random Elementor element ID.
 */
function landtech_extras_playground_element_id() {
	return substr( md5( uniqid( 'ltxe', true ) ), 0, 7 );
}

/**
 * @param array<int,array<string,mixed>> $widgets Widget element trees.
 * @return array<string,mixed>
 */
function landtech_extras_playground_section( array $widgets ) {
	return array(
		'id'       => landtech_extras_playground_element_id(),
		'elType'   => 'section',
		'isInner'  => false,
		'settings' => array(),
		'elements' => array(
			array(
				'id'       => landtech_extras_playground_element_id(),
				'elType'   => 'column',
				'isInner'  => false,
				'settings' => array(
					'_column_size' => 100,
				),
				'elements' => $widgets,
			),
		),
	);
}

/**
 * @param string               $widget_type Elementor widget slug.
 * @param array<string,mixed>  $settings    Widget settings.
 * @return array<string,mixed>
 */
function landtech_extras_playground_widget( $widget_type, array $settings = array() ) {
	return array(
		'id'         => landtech_extras_playground_element_id(),
		'elType'     => 'widget',
		'widgetType' => $widget_type,
		'settings'   => $settings,
		'elements'   => array(),
	);
}

/**
 * @param int $attachment_id Attachment ID.
 * @return array<string,mixed>
 */
function landtech_extras_playground_media( $attachment_id ) {
	$url = wp_get_attachment_url( $attachment_id );

	return array(
		'id'  => (int) $attachment_id,
		'url' => $url ? $url : '',
	);
}

/**
 * @param array<int,int> $attachment_ids Attachment IDs.
 * @return array<int,array<string,mixed>>
 */
function landtech_extras_playground_gallery( array $attachment_ids ) {
	$gallery = array();

	foreach ( $attachment_ids as $attachment_id ) {
		$gallery[] = landtech_extras_playground_media( (int) $attachment_id );
	}

	return $gallery;
}

/**
 * Sideload placeholder images for gallery and media widgets.
 *
 * @return array<int,int> Attachment IDs.
 */
function landtech_extras_playground_sideload_images() {
	$seeds  = array( 'gallery1', 'gallery2', 'gallery3', 'gallery4', 'gallery5', 'gallery6', 'hotspot', 'device' );
	$images = array();

	foreach ( $seeds as $index => $seed ) {
		$url = 'https://picsum.photos/seed/' . rawurlencode( 'landtech-' . $seed ) . '/1200/800';
		$id  = landtech_extras_playground_sideload_image(
			$url,
			sprintf(
				/* translators: %d: image number in Playground demo set. */
				__( 'LandTech Extras demo image %d', 'landtech-extras-for-elementor' ),
				$index + 1
			)
		);

		if ( $id > 0 ) {
			$images[] = $id;
		}
	}

	if ( empty( $images ) ) {
		$images[] = (int) get_option( 'site_icon', 0 );
	}

	return array_values( array_filter( array_map( 'absint', $images ) ) );
}

/**
 * @param string $url         Remote image URL.
 * @param string $description Attachment description.
 * @return int Attachment ID or 0.
 */
function landtech_extras_playground_sideload_image( $url, $description ) {
	$tmp = download_url( $url );

	if ( is_wp_error( $tmp ) ) {
		return 0;
	}

	$file_array = array(
		'name'     => 'landtech-demo-' . wp_generate_password( 8, false ) . '.jpg',
		'tmp_name' => $tmp,
	);

	$attachment_id = media_handle_sideload( $file_array, 0, $description );

	if ( is_wp_error( $attachment_id ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink -- temp file cleanup after failed sideload.
		@unlink( $tmp );
		return 0;
	}

	return (int) $attachment_id;
}

/**
 * @param array<int,int> $images Attachment IDs for featured images.
 * @return void
 */
function landtech_extras_playground_seed_sample_posts( array $images ) {
	$titles = array(
		'Launching a New Elementor Site',
		'Design Tips for Modern Layouts',
		'Building Galleries with LandTech Extras',
		'Posts Grid and Timeline Demos',
		'Interactive Widgets Showcase',
		'Navigation Patterns for Elementor',
	);

	foreach ( $titles as $index => $title ) {
		$post_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_content' => '<p>' . esc_html__( 'Sample blog post used by LandTech Extras Posts and Timeline demos in WordPress Playground.', 'landtech-extras-for-elementor' ) . '</p>',
				'post_status'  => 'publish',
				'post_type'    => 'post',
			),
			true
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			continue;
		}

		if ( ! empty( $images[ $index ] ) ) {
			set_post_thumbnail( $post_id, (int) $images[ $index ] );
		}
	}
}

/**
 * @param array<int,int> $images Attachment IDs.
 * @return array<int,array<string,mixed>>
 */
function landtech_extras_playground_get_demo_definitions( array $images ) {
	$gallery_ids = array_slice( $images, 0, 6 );
	$hero_image  = ! empty( $images[0] ) ? landtech_extras_playground_media( $images[0] ) : array( 'url' => '' );
	$alt_image   = ! empty( $images[1] ) ? landtech_extras_playground_media( $images[1] ) : $hero_image;
	$hotspot_img = ! empty( $images[6] ) ? landtech_extras_playground_media( $images[6] ) : $hero_image;
	$wp_gallery  = landtech_extras_playground_gallery( $gallery_ids );

	return array(
		array(
			'title'   => 'Gallery Demo',
			'slug'    => 'demo-gallery',
			'group'   => 'Media & Gallery',
			'intro'   => __( 'Masonry-style gallery with lightbox-ready images from the WordPress media library.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'gallery-extra',
					array(
						'gallery_type' => 'wordpress',
						'wp_gallery'   => $wp_gallery,
						'columns'      => '3',
					)
				),
			),
		),
		array(
			'title'   => 'Gallery Slider Demo',
			'slug'    => 'demo-gallery-slider',
			'group'   => 'Media & Gallery',
			'intro'   => __( 'Carousel gallery slider with LandTech Extras transitions.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'gallery-slider',
					array(
						'gallery_type' => 'wordpress',
						'wp_gallery'   => $wp_gallery,
					)
				),
			),
		),
		array(
			'title'   => 'Image Comparison Demo',
			'slug'    => 'demo-image-comparison',
			'group'   => 'Media & Gallery',
			'intro'   => __( 'Drag a slider to compare two images before and after.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'image-comparison',
					array(
						'original_image' => $hero_image,
						'modified_image' => $alt_image,
					)
				),
			),
		),
		array(
			'title'   => 'Random Image Demo',
			'slug'    => 'demo-random-image',
			'group'   => 'Media & Gallery',
			'intro'   => __( 'Displays a random image from a selected gallery on each page load.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-random-image',
					array(
						'wp_gallery' => $wp_gallery,
					)
				),
			),
		),
		array(
			'title'   => 'Hotspots Demo',
			'slug'    => 'demo-hotspots',
			'group'   => 'Media & Gallery',
			'intro'   => __( 'Interactive hotspots with tooltips on a single image.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'hotspots',
					array(
						'image' => $hotspot_img,
					)
				),
			),
		),
		array(
			'title'   => 'HTML5 Video Demo',
			'slug'    => 'demo-html5-video',
			'group'   => 'Media & Gallery',
			'intro'   => __( 'Self-hosted HTML5 video player widget.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'html5-video',
					array(
						'video_source' => 'url',
						'video_url'    => 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4',
					)
				),
			),
		),
		array(
			'title'   => 'Audio Player Demo',
			'slug'    => 'demo-audio-player',
			'group'   => 'Media & Gallery',
			'intro'   => __( 'Custom audio playlist player for podcasts or music samples.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-audio-player',
					array(
						'playlist' => array(
							array(
								'_id'              => landtech_extras_playground_element_id(),
								'title'            => __( 'Demo Track', 'landtech-extras-for-elementor' ),
								'audio_source'     => 'url',
								'source_mpeg_url'  => 'https://interactive-examples.mdn.mozilla.net/media/cc0-audio/t-rex-roar.mp3',
							),
						),
					)
				),
			),
		),
		array(
			'title'   => 'Search Form Demo',
			'slug'    => 'demo-search-form',
			'group'   => 'Forms & Search',
			'intro'   => __( 'Advanced search form with classic skin and live results styling.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-search-form',
					array(
						'_skin' => 'classic',
					)
				),
			),
		),
		array(
			'title'   => 'Posts Extra Demo',
			'slug'    => 'demo-posts-extra',
			'group'   => 'Content & Posts',
			'intro'   => __( 'Posts grid powered by the sample blog posts seeded in this Playground.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'posts-extra',
					array(
						'_skin'          => 'classic',
						'posts_per_page' => 6,
					)
				),
			),
		),
		array(
			'title'   => 'Timeline Demo',
			'slug'    => 'demo-timeline',
			'group'   => 'Content & Posts',
			'intro'   => __( 'Vertical timeline layout for chronological content.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'timeline',
					array(
						'posts_per_page' => 4,
					)
				),
			),
		),
		array(
			'title'   => 'Calendar Demo',
			'slug'    => 'demo-calendar',
			'group'   => 'Content & Posts',
			'intro'   => __( 'Event calendar widget with month navigation.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget( 'ee-calendar', array() ),
			),
		),
		array(
			'title'   => 'Heading Extra Demo',
			'slug'    => 'demo-heading-extra',
			'group'   => 'Typography & Buttons',
			'intro'   => __( 'Extended heading with long-shadow and typography controls.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'heading-extended',
					array(
						'title' => __( 'LandTech Extras Heading', 'landtech-extras-for-elementor' ),
						'size'  => 'large',
					)
				),
			),
		),
		array(
			'title'   => 'Text Divider Demo',
			'slug'    => 'demo-text-divider',
			'group'   => 'Typography & Buttons',
			'intro'   => __( 'Decorative divider with centered label text.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'text-divider',
					array(
						'text' => __( 'Featured Section', 'landtech-extras-for-elementor' ),
					)
				),
			),
		),
		array(
			'title'   => 'Button Group Demo',
			'slug'    => 'demo-button-group',
			'group'   => 'Typography & Buttons',
			'intro'   => __( 'Group of styled buttons with hover effects.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget( 'button-group', array() ),
			),
		),
		array(
			'title'   => 'Table Demo',
			'slug'    => 'demo-table',
			'group'   => 'Content & Posts',
			'intro'   => __( 'Responsive data table with sortable styling options.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'table',
					array(
						'header_cells' => array(
							array( '_id' => landtech_extras_playground_element_id(), 'cell_text' => __( 'Feature', 'landtech-extras-for-elementor' ) ),
							array( '_id' => landtech_extras_playground_element_id(), 'cell_text' => __( 'Description', 'landtech-extras-for-elementor' ) ),
							array( '_id' => landtech_extras_playground_element_id(), 'cell_text' => __( 'Status', 'landtech-extras-for-elementor' ) ),
						),
					)
				),
			),
		),
		array(
			'title'   => 'Circle Progress Demo',
			'slug'    => 'demo-circle-progress',
			'group'   => 'Utilities',
			'intro'   => __( 'Animated circular progress indicator.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'circle-progress',
					array(
						'value' => '78',
					)
				),
			),
		),
		array(
			'title'   => 'Google Map Demo',
			'slug'    => 'demo-google-map',
			'group'   => 'Maps & Location',
			'intro'   => __( 'Google Map widget (add a Maps API key under Elementor → LandTech Extras → APIs for live maps).', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-google-map',
					array(
						'lat' => '48.8583736',
						'lng' => '2.2922873',
					)
				),
			),
		),
		array(
			'title'   => 'Breadcrumbs Demo',
			'slug'    => 'demo-breadcrumbs',
			'group'   => 'Navigation & UI',
			'intro'   => __( 'Breadcrumb trail for the current page hierarchy.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget( 'ee-breadcrumbs', array() ),
			),
		),
		array(
			'title'   => 'Switcher Demo',
			'slug'    => 'demo-switcher',
			'group'   => 'Navigation & UI',
			'intro'   => __( 'Tabbed switcher for toggling between content panels.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-switcher',
					landtech_extras_playground_switcher_settings( $images )
				),
			),
		),
		array(
			'title'   => 'Toggle Element Demo',
			'slug'    => 'demo-toggle-element',
			'group'   => 'Navigation & UI',
			'intro'   => __( 'Expand/collapse toggle panels for FAQs and accordions.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-toggle-element',
					landtech_extras_playground_toggle_settings()
				),
			),
		),
		array(
			'title'   => 'Unfold Demo',
			'slug'    => 'demo-unfold',
			'group'   => 'Navigation & UI',
			'intro'   => __( 'Reveal hidden content with an unfold animation.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget( 'unfold', array() ),
			),
		),
		array(
			'title'   => 'Popup Demo',
			'slug'    => 'demo-popup',
			'group'   => 'Navigation & UI',
			'intro'   => __( 'Modal popup triggered by a button or link.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget( 'ee-popup', array() ),
			),
		),
		array(
			'title'   => 'Age Gate Demo',
			'slug'    => 'demo-age-gate',
			'group'   => 'Navigation & UI',
			'intro'   => __( 'Age verification overlay for restricted content.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget( 'ee-age-gate', array() ),
			),
		),
		array(
			'title'   => 'Offcanvas Demo',
			'slug'    => 'demo-offcanvas',
			'group'   => 'Navigation & UI',
			'intro'   => __( 'Off-canvas panel for mobile menus or side content.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget( 'ee-offcanvas', array() ),
			),
		),
		array(
			'title'   => 'Slide Menu Demo',
			'slug'    => 'demo-slide-menu',
			'group'   => 'Navigation & UI',
			'intro'   => __( 'Multi-level slide menu navigation pattern.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget( 'ee-slide-menu', array() ),
			),
		),
		array(
			'title'   => 'Scroll Indicator Demo',
			'slug'    => 'demo-scroll-indicator',
			'group'   => 'Navigation & UI',
			'intro'   => __( 'Scroll progress indicator — scroll this page to see it in action.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-scroll-indicator',
					array(
						'_skin' => 'bar',
					)
				),
				landtech_extras_playground_widget(
					'text-editor',
					array(
						'editor' => str_repeat( '<p>' . esc_html__( 'Scroll down to preview the scroll indicator widget.', 'landtech-extras-for-elementor' ) . '</p>', 12 ),
					)
				),
			),
		),
		array(
			'title'   => 'Devices Demo',
			'slug'    => 'demo-devices',
			'group'   => 'Utilities',
			'intro'   => __( 'Device mockups for showcasing responsive designs.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget( 'devices-extended', array() ),
			),
		),
		array(
			'title'   => 'Inline SVG Demo',
			'slug'    => 'demo-inline-svg',
			'group'   => 'Utilities',
			'intro'   => __( 'Inline SVG icon with color and stroke controls.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget( 'ee-inline-svg', array() ),
			),
		),
	);
}

/**
 * Default Switcher widget settings for Playground demos.
 *
 * @param array<int,int> $images Attachment IDs.
 * @return array<string,mixed>
 */
function landtech_extras_playground_switcher_settings( array $images ) {
	$panels = array(
		array(
			'label'       => __( 'Design', 'landtech-extras-for-elementor' ),
			'title'       => __( 'Design your layout', 'landtech-extras-for-elementor' ),
			'description' => __( 'Plan sections, typography, and imagery before you publish.', 'landtech-extras-for-elementor' ),
		),
		array(
			'label'       => __( 'Build', 'landtech-extras-for-elementor' ),
			'title'       => __( 'Build with Elementor', 'landtech-extras-for-elementor' ),
			'description' => __( 'Combine LandTech Extras widgets with Elementor controls on the canvas.', 'landtech-extras-for-elementor' ),
		),
		array(
			'label'       => __( 'Launch', 'landtech-extras-for-elementor' ),
			'title'       => __( 'Launch your site', 'landtech-extras-for-elementor' ),
			'description' => __( 'Preview interactions like this switcher before going live.', 'landtech-extras-for-elementor' ),
		),
	);

	$items = array();

	foreach ( $panels as $index => $panel ) {
		$image = ! empty( $images[ $index ] ) ? landtech_extras_playground_media( (int) $images[ $index ] ) : array( 'url' => '' );

		$items[] = array(
			'_id'         => landtech_extras_playground_element_id(),
			'title'       => $panel['title'],
			'label'       => $panel['label'],
			'description' => $panel['description'],
			'image'       => $image,
		);
	}

	return array(
		'description' => 'yes',
		'items'       => $items,
	);
}

/**
 * Default Toggle Element widget settings for Playground demos.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_toggle_settings() {
	return array(
		'elements' => array(
			array(
				'_id'     => landtech_extras_playground_element_id(),
				'text'    => __( 'Getting started', 'landtech-extras-for-elementor' ),
				'content' => __( 'Use the toggle control to reveal this panel on the frontend.', 'landtech-extras-for-elementor' ),
			),
			array(
				'_id'     => landtech_extras_playground_element_id(),
				'text'    => __( 'Widget settings', 'landtech-extras-for-elementor' ),
				'content' => __( 'Each toggle item can contain rich text, links, and styled content.', 'landtech-extras-for-elementor' ),
			),
		),
	);
}

/**
 * Persist Elementor canvas JSON using the document API when available.
 *
 * @param int   $page_id  Page ID.
 * @param array $elements Elementor element tree.
 * @return void
 */
function landtech_extras_playground_save_elementor_data( $page_id, array $elements ) {
	$page_id = absint( $page_id );

	if ( $page_id <= 0 ) {
		return;
	}

	update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );

	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
	}

	if ( class_exists( '\Elementor\Plugin' ) ) {
		$document = \Elementor\Plugin::$instance->documents->get( $page_id, false );

		if ( $document ) {
			$document->save(
				array(
					'elements' => $elements,
					'settings' => array(),
				)
			);
			return;
		}
	}

	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
	delete_post_meta( $page_id, '_elementor_css' );

	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->db->save_plain_text( $page_id );
	}
}

/**
 * @param string $title   Page title.
 * @param string $slug    Page slug.
 * @param string $intro   Intro paragraph for the demo page.
 * @param array  $widgets Widget element trees for the demo section.
 * @return int Page ID or 0.
 */
function landtech_extras_playground_create_elementor_page( $title, $slug, $intro, array $widgets ) {
	$page_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_name'    => sanitize_title( $slug ),
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		),
		true
	);

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return 0;
	}

	$intro_widget = landtech_extras_playground_widget(
		'text-editor',
		array(
			'editor' => '<h2>' . esc_html( $title ) . '</h2><p>' . esc_html( $intro ) . '</p>',
		)
	);

	$data = array(
		landtech_extras_playground_section( array( $intro_widget ) ),
		landtech_extras_playground_section( $widgets ),
	);

	landtech_extras_playground_save_elementor_data( $page_id, $data );

	return (int) $page_id;
}

/**
 * @param array<string,array<int,array<string,mixed>>> $groups Demo groups for the homepage grid.
 * @param array<string,int>                            $pages  Slug => page ID map.
 * @return int Homepage ID.
 */
function landtech_extras_playground_create_homepage( array $groups, array $pages ) {
	$sections_html = '';

	foreach ( $groups as $group_name => $items ) {
		$sections_html .= '<h3>' . esc_html( $group_name ) . '</h3><ul>';

		foreach ( $items as $item ) {
			$url = get_permalink( $item['page_id'] );
			if ( ! $url ) {
				continue;
			}
			$sections_html .= '<li><a href="' . esc_url( $url ) . '">' . esc_html( $item['title'] ) . '</a></li>';
		}

		$sections_html .= '</ul>';
	}

	$editor = landtech_extras_playground_widget(
		'text-editor',
		array(
			'editor' => '<h1>' . esc_html__( 'LandTech Extras for Elementor', 'landtech-extras-for-elementor' ) . '</h1>'
				. '<p>' . esc_html__( 'Welcome to the live Playground demo. Each link below opens a dedicated page showcasing one widget or extension from the free WordPress.org plugin. Log in as admin / password to edit any page with Elementor or manage widgets under Elementor → LandTech Extras.', 'landtech-extras-for-elementor' ) . '</p>'
				. '<p><a href="' . esc_url( admin_url( 'admin.php?page=landtech-extras' ) ) . '">' . esc_html__( 'Open LandTech Extras settings', 'landtech-extras-for-elementor' ) . '</a></p>'
				. $sections_html,
		)
	);

	$hero = landtech_extras_playground_widget(
		'heading-extended',
		array(
			'title'       => __( 'Explore every LandTech Extras widget', 'landtech-extras-for-elementor' ),
			'header_size' => 'h1',
			'size'        => 'xl',
		)
	);

	$data = array(
		landtech_extras_playground_section( array( $hero ) ),
		landtech_extras_playground_section( array( $editor ) ),
	);

	$page_id = wp_insert_post(
		array(
			'post_title'   => __( 'LandTech Extras Demos', 'landtech-extras-for-elementor' ),
			'post_name'    => 'landtech-extras-demos',
			'post_status'  => 'publish',
			'post_type'    => 'page',
		),
		true
	);

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return 0;
	}

	landtech_extras_playground_save_elementor_data( $page_id, $data );

	return (int) $page_id;
}

/**
 * @param int                                            $home_id Homepage page ID.
 * @param array<string,array<int,array<string,mixed>>>   $groups  Grouped demo pages.
 * @param array<string,int>                              $pages   Slug => page ID map.
 * @return void
 */
function landtech_extras_playground_assign_nav_menu( $home_id, array $groups, array $pages ) {
	$menu_name = __( 'LandTech Extras Demos', 'landtech-extras-for-elementor' );
	$menu_id   = wp_create_nav_menu( $menu_name );

	if ( is_wp_error( $menu_id ) ) {
		return;
	}

	$position = 1;

	if ( $home_id > 0 ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => __( 'Demo Home', 'landtech-extras-for-elementor' ),
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $home_id,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => $position++,
			)
		);
	}

	foreach ( $groups as $group_name => $items ) {
		$parent_id = wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'    => $group_name,
				'menu-item-url'      => '#',
				'menu-item-type'     => 'custom',
				'menu-item-status'   => 'publish',
				'menu-item-position' => $position++,
			)
		);

		if ( is_wp_error( $parent_id ) ) {
			continue;
		}

		foreach ( $items as $item ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $item['title'],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => (int) $item['page_id'],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => (int) $parent_id,
					'menu-item-position'  => $position++,
				)
			);
		}
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( ! is_array( $locations ) ) {
		$locations = array();
	}

	$locations['primary'] = (int) $menu_id;

	$registered = get_registered_nav_menus();

	if ( is_array( $registered ) ) {
		foreach ( array_keys( $registered ) as $location ) {
			if ( empty( $locations[ $location ] ) ) {
				$locations[ $location ] = (int) $menu_id;
			}
		}
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}
