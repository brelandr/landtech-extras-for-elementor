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

	// Blueprint runPHP may not inherit the login step's user context.
	if ( function_exists( 'wp_set_current_user' ) ) {
		wp_set_current_user( 1 );
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
		$page_settings = isset( $demo['page_settings'] ) && is_array( $demo['page_settings'] ) ? $demo['page_settings'] : array();

		$page_id = landtech_extras_playground_create_elementor_page(
			$demo['title'],
			$demo['slug'],
			$demo['intro'],
			$demo['widgets'],
			$page_settings
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

	$home_id = landtech_extras_playground_create_homepage( $groups, $pages, $demos, $images );
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
						'wp_gallery'   => $wp_gallery,
						'link_to'      => 'file',
						'open_lightbox' => 'yes',
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
			'intro'   => __( 'Advanced search form with classic skin and live AJAX results (type at least two characters).', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-search-form',
					array(
						'_skin'              => 'classic',
						'live_ajax_search'   => 'yes',
						'live_ajax_post_type'=> 'post',
					)
				),
			),
		),
		array(
			'title'   => 'Posts List Layout Demo',
			'slug'    => 'demo-posts-list',
			'group'   => 'Content & Posts',
			'intro'   => __( 'Posts Extra list skin — thumbnail beside title and excerpt.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'posts-extra',
					landtech_extras_playground_posts_extra_list_settings()
				),
			),
		),
		array(
			'title'   => 'Posts Featured Grid Demo',
			'slug'    => 'demo-posts-featured-grid',
			'group'   => 'Content & Posts',
			'intro'   => __( 'Posts Extra featured-grid skin — hero post with a secondary grid below.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'posts-extra',
					landtech_extras_playground_posts_extra_featured_grid_settings()
				),
			),
		),
		array(
			'title'   => 'Posts Timeline Layout Demo',
			'slug'    => 'demo-posts-timeline',
			'group'   => 'Content & Posts',
			'intro'   => __( 'Posts Extra timeline skin — vertical line with dated blog entries.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'posts-extra',
					landtech_extras_playground_posts_extra_timeline_settings()
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
					landtech_extras_playground_posts_extra_settings()
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
					landtech_extras_playground_timeline_settings( $images )
				),
			),
		),
		array(
			'title'   => 'Calendar Demo',
			'slug'    => 'demo-calendar',
			'group'   => 'Content & Posts',
			'intro'   => __( 'Event calendar powered by Schedule-X (month grid with compact skin event list).', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-calendar',
					landtech_extras_playground_calendar_settings()
				),
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
			'intro'   => __( 'Hover each button to preview Clone, Flip, Background, 3D, and Cube effects.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'button-group',
					landtech_extras_playground_button_group_settings()
				),
			),
			'page_settings' => array(
				'custom_css' => landtech_extras_playground_button_group_custom_css(),
			),
		),
		array(
			'title'   => 'Table Demo',
			'slug'    => 'demo-table',
			'group'   => 'Content & Posts',
			'intro'   => __( 'Responsive data table with frontend pagination (5 rows per page).', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'table',
					landtech_extras_playground_table_settings( true )
				),
			),
		),
		array(
			'title'   => 'Lottie Demo',
			'slug'    => 'demo-lottie',
			'group'   => 'Utilities',
			'intro'   => __( 'Vector animation via the bundled lottie-player web component.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-lottie',
					landtech_extras_playground_lottie_settings()
				),
			),
		),
		array(
			'title'   => 'FAQ Schema Demo',
			'slug'    => 'demo-faq-schema',
			'group'   => 'Utilities',
			'intro'   => __( 'Accessible FAQ accordion with optional FAQPage JSON-LD structured data.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-faq-schema',
					landtech_extras_playground_faq_schema_settings()
				),
			),
		),
		array(
			'title'   => 'OpenStreetMap Demo',
			'slug'    => 'demo-openstreetmap',
			'group'   => 'Maps & Location',
			'intro'   => __( 'Leaflet map using OpenStreetMap tiles (no Google Maps API key required).', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-google-map',
					array(
						'map_provider' => 'openstreetmap',
						'lat'          => '48.8583736',
						'lng'          => '2.2922873',
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
			'intro'   => __( 'Click Design, Build, or Launch to switch panels — images and copy animate on the frontend.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-switcher',
					landtech_extras_playground_switcher_settings( $images )
				),
			),
			'page_settings' => array(
				'custom_css' => landtech_extras_playground_switcher_custom_css(),
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
				landtech_extras_playground_widget(
					'unfold',
					landtech_extras_playground_unfold_settings()
				),
			),
		),
		array(
			'title'   => 'Popup Demo',
			'slug'    => 'demo-popup',
			'group'   => 'Navigation & UI',
			'intro'   => __( 'Modal popup triggered by a button or link.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-popup',
					landtech_extras_playground_popup_settings()
				),
			),
		),
		array(
			'title'   => 'Age Gate Demo',
			'slug'    => 'demo-age-gate',
			'group'   => 'Navigation & UI',
			'intro'   => __( 'Age verification overlay for restricted content.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-age-gate',
					landtech_extras_playground_age_gate_settings()
				),
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
					landtech_extras_playground_scroll_indicator_settings()
				),
				landtech_extras_playground_widget(
					'heading',
					array(
						'title'       => __( 'Part 1 — Start', 'landtech-extras-for-elementor' ),
						'_element_id' => 'ltxe-scroll-part-1',
					)
				),
				landtech_extras_playground_widget(
					'text-editor',
					array(
						'editor' => str_repeat( '<p>' . esc_html__( 'Scroll to see the indicator track this section.', 'landtech-extras-for-elementor' ) . '</p>', 8 ),
					)
				),
				landtech_extras_playground_widget(
					'heading',
					array(
						'title'       => __( 'Part 2 — Middle', 'landtech-extras-for-elementor' ),
						'_element_id' => 'ltxe-scroll-part-2',
					)
				),
				landtech_extras_playground_widget(
					'text-editor',
					array(
						'editor' => str_repeat( '<p>' . esc_html__( 'Keep scrolling — the active item and progress ring should update.', 'landtech-extras-for-elementor' ) . '</p>', 8 ),
					)
				),
				landtech_extras_playground_widget(
					'heading',
					array(
						'title'       => __( 'Part 3 — End', 'landtech-extras-for-elementor' ),
						'_element_id' => 'ltxe-scroll-part-3',
					)
				),
				landtech_extras_playground_widget(
					'text-editor',
					array(
						'editor' => str_repeat( '<p>' . esc_html__( 'Final section — indicator should mark this block as read when you reach the bottom.', 'landtech-extras-for-elementor' ) . '</p>', 8 ),
					)
				),
			),
		),
		array(
			'title'   => 'Devices Demo',
			'slug'    => 'demo-devices',
			'group'   => 'Utilities',
			'intro'   => __( 'Phone mockup with a scrollable screenshot — click the rotate icon to switch orientation.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'devices-extended',
					landtech_extras_playground_devices_settings( $images )
				),
			),
		),
		array(
			'title'   => 'Inline SVG Demo',
			'slug'    => 'demo-inline-svg',
			'group'   => 'Utilities',
			'intro'   => __( 'Inline SVG loaded from a file — hover to see the secondary color; edit Color and Hover Color in the widget.', 'landtech-extras-for-elementor' ),
			'widgets' => array(
				landtech_extras_playground_widget(
					'ee-inline-svg',
					landtech_extras_playground_inline_svg_settings()
				),
			),
		),
	);
}

/**
 * Default Calendar widget settings for Playground demos.
 *
 * Uses compact skin so clicking an event day slides open the events panel.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_calendar_settings() {
	$events = array(
		array(
			'title' => __( 'Team standup', 'landtech-extras-for-elementor' ),
			'start' => wp_date( 'Y-m-d 09:00' ),
			'end'   => wp_date( 'Y-m-d 09:30' ),
		),
		array(
			'title' => __( 'Client review', 'landtech-extras-for-elementor' ),
			'start' => wp_date( 'Y-m-d 14:00', strtotime( '+3 days' ) ),
			'end'   => wp_date( 'Y-m-d 15:00', strtotime( '+3 days' ) ),
		),
		array(
			'title' => __( 'Launch day', 'landtech-extras-for-elementor' ),
			'start' => wp_date( 'Y-m-d 10:00', strtotime( '+10 days' ) ),
			'end'   => wp_date( 'Y-m-d 18:00', strtotime( '+10 days' ) ),
		),
	);

	$repeater = array();

	foreach ( $events as $event ) {
		$repeater[] = array(
			'_id'   => landtech_extras_playground_element_id(),
			'title' => $event['title'],
			'start' => $event['start'],
			'end'   => $event['end'],
			'link'  => array(
				'url'         => '',
				'is_external' => '',
				'nofollow'    => '',
			),
		);
	}

	return array(
		'source'             => 'manual',
		'skin'               => 'compact',
		'event_list_heading' => __( 'Events this month', 'landtech-extras-for-elementor' ),
		'events'             => $repeater,
	);
}

/**
 * Default Timeline widget settings for Playground demos.
 *
 * Uses custom repeater items so the demo works without Elementor Pro.
 *
 * @param array<int,int> $images Attachment IDs.
 * @return array<string,mixed>
 */
function landtech_extras_playground_timeline_settings( array $images = array() ) {
	$milestones = array(
		array(
			'date'    => __( 'January 2020', 'landtech-extras-for-elementor' ),
			'content' => '<h3>' . esc_html__( 'Project kickoff', 'landtech-extras-for-elementor' ) . '</h3><p>' . esc_html__( 'Define goals, wireframes, and the first Elementor layout.', 'landtech-extras-for-elementor' ) . '</p>',
		),
		array(
			'date'    => __( 'June 2021', 'landtech-extras-for-elementor' ),
			'content' => '<h3>' . esc_html__( 'First launch', 'landtech-extras-for-elementor' ) . '</h3><p>' . esc_html__( 'Ship the homepage and core content with LandTech Extras widgets.', 'landtech-extras-for-elementor' ) . '</p>',
		),
		array(
			'date'    => __( 'March 2023', 'landtech-extras-for-elementor' ),
			'content' => '<h3>' . esc_html__( 'Growth phase', 'landtech-extras-for-elementor' ) . '</h3><p>' . esc_html__( 'Add galleries, timelines, and interactive navigation patterns.', 'landtech-extras-for-elementor' ) . '</p>',
		),
		array(
			'date'    => __( 'Today', 'landtech-extras-for-elementor' ),
			'content' => '<h3>' . esc_html__( 'Live preview', 'landtech-extras-for-elementor' ) . '</h3><p>' . esc_html__( 'You are viewing this timeline in WordPress Playground.', 'landtech-extras-for-elementor' ) . '</p>',
		),
	);

	$items = array();

	foreach ( $milestones as $index => $milestone ) {
		$item = array(
			'_id'     => landtech_extras_playground_element_id(),
			'date'    => $milestone['date'],
			'content' => $milestone['content'],
		);

		if ( ! empty( $images[ $index ] ) ) {
			$item['image'] = landtech_extras_playground_media( (int) $images[ $index ] );
		}

		$items[] = $item;
	}

	return array(
		'source' => 'custom',
		'items'  => $items,
	);
}

/**
 * Default Posts Extra widget settings for Playground demos.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_posts_extra_settings() {
	return array(
		'_skin'               => 'classic',
		'posts_per_page'      => 6,
		'posts_post_type'     => 'post',
		'posts_orderby'       => 'date',
		'posts_order'         => 'desc',
		'columns'             => '3',
		'columns_tablet'      => '2',
		'columns_mobile'      => '1',
		'layout'              => 'default',
		'post_media'          => 'yes',
		'post_title_position' => 'body',
	);
}

/**
 * Posts Extra list skin settings for Playground demos.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_posts_extra_list_settings() {
	$settings = landtech_extras_playground_posts_extra_settings();
	$settings['_skin'] = 'list';

	return $settings;
}

/**
 * Posts Extra featured-grid skin settings for Playground demos.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_posts_extra_featured_grid_settings() {
	$settings = landtech_extras_playground_posts_extra_settings();
	$settings['_skin']  = 'featured-grid';
	$settings['columns'] = '2';

	return $settings;
}

/**
 * Posts Extra timeline skin settings for Playground demos.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_posts_extra_timeline_settings() {
	$settings = landtech_extras_playground_posts_extra_settings();
	$settings['_skin']         = 'timeline';
	$settings['posts_per_page'] = 5;

	return $settings;
}

/**
 * Table widget settings for Playground demos.
 *
 * @param bool $paginate Enable frontend pagination controls.
 * @return array<string,mixed>
 */
function landtech_extras_playground_table_settings( $paginate = false ) {
	$rows = array();

	for ( $index = 1; $index <= 12; $index++ ) {
		$rows[] = array(
			'type' => 'row',
		);
		$rows[] = array(
			'type'      => 'cell',
			'cell_text' => sprintf(
				/* translators: %d: row number */
				__( 'Feature %d', 'landtech-extras-for-elementor' ),
				$index
			),
			'cell_type' => 'td',
		);
		$rows[] = array(
			'type'      => 'cell',
			'cell_text' => __( 'Sample description for this row.', 'landtech-extras-for-elementor' ),
			'cell_type' => 'td',
		);
		$rows[] = array(
			'type'      => 'cell',
			'cell_text' => __( 'Active', 'landtech-extras-for-elementor' ),
			'cell_type' => 'td',
		);
	}

	$settings = array(
		'header_cells' => array(
			array(
				'_id'       => landtech_extras_playground_element_id(),
				'cell_text' => __( 'Feature', 'landtech-extras-for-elementor' ),
			),
			array(
				'_id'       => landtech_extras_playground_element_id(),
				'cell_text' => __( 'Description', 'landtech-extras-for-elementor' ),
			),
			array(
				'_id'       => landtech_extras_playground_element_id(),
				'cell_text' => __( 'Status', 'landtech-extras-for-elementor' ),
			),
		),
		'rows'         => $rows,
	);

	if ( $paginate ) {
		$settings['pagination']      = 'yes';
		$settings['pagination_rows'] = 5;
	}

	return $settings;
}

/**
 * Default Lottie widget settings for Playground demos.
 *
 * Uses a public sample animation URL (visitor browser fetch when the demo page loads).
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_lottie_settings() {
	return array(
		'source'        => 'url',
		'animation_url' => array(
			'url'         => 'https://assets10.lottiefiles.com/packages/lf20_kyu7ypfb.json',
			'is_external' => 'on',
			'nofollow'    => '',
		),
		'loop'          => 'yes',
		'autoplay'      => 'yes',
	);
}

/**
 * Default FAQ Schema widget settings for Playground demos.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_faq_schema_settings() {
	return array(
		'faq_items' => array(
			array(
				'_id'      => landtech_extras_playground_element_id(),
				'question' => __( 'What is LandTech Extras for Elementor?', 'landtech-extras-for-elementor' ),
				'answer'   => __( 'A GPL fork of Elementor Extras with creative widgets and editor extensions maintained for WordPress.org.', 'landtech-extras-for-elementor' ),
			),
			array(
				'_id'      => landtech_extras_playground_element_id(),
				'question' => __( 'Does this demo output structured data?', 'landtech-extras-for-elementor' ),
				'answer'   => __( 'Yes — this widget can print FAQPage JSON-LD when enabled in the widget settings.', 'landtech-extras-for-elementor' ),
			),
		),
		'output_json_ld' => 'yes',
	);
}

/**
 * Default Inline SVG widget settings for Playground demos.
 *
 * The widget fetches an SVG by URL on the frontend; empty settings render nothing.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_inline_svg_settings() {
	$svg_url = trailingslashit( LANDTECH_EXTRAS_URL ) . 'assets/demo/landtech-inline-svg-demo.svg';

	return array(
		'svg_source'        => 'url',
		'svg_custom_url'    => array(
			'url'         => $svg_url,
			'is_external' => '',
			'nofollow'    => '',
		),
		'svg'               => array(
			'id'  => 0,
			'url' => $svg_url,
		),
		'sizing'            => 'yes',
		'width'             => array(
			'unit' => 'px',
			'size' => 180,
		),
		'maintain_ratio'    => 'yes',
		'remove_inline_css' => 'yes',
		'override_colors'   => 'yes',
		'color'             => '#2563eb',
		'color_hover'       => '#7c3aed',
		'align'             => 'center',
	);
}

/**
 * Default Button Group widget settings for Playground demos.
 *
 * LandTech button effects require per-button Custom style plus an effect type.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_button_group_settings() {
	$demo_ids = landtech_extras_playground_button_group_demo_ids();

	$demos = array(
		array(
			'text'                      => __( 'Clone', 'landtech-extras-for-elementor' ),
			'button_effect_type'        => 'clone',
			'button_effect_direction'   => 'up',
			'button_effect_entrance'    => 'cover',
			'background_color'          => '#2563eb',
			'button_effect_background_color' => '#1e3a8a',
		),
		array(
			'text'                      => __( 'Flip', 'landtech-extras-for-elementor' ),
			'button_effect_type'        => 'flip',
			'button_effect_direction'   => 'down',
			'button_effect_text'        => __( 'Flipped!', 'landtech-extras-for-elementor' ),
			'background_color'          => '#7c3aed',
			'button_effect_background_color' => '#5b21b6',
		),
		array(
			'text'                      => __( 'Background', 'landtech-extras-for-elementor' ),
			'button_effect_type'        => 'back',
			'button_effect_direction'   => 'left',
			'background_color'          => '#0f766e',
			'button_effect_background_color' => '#134e4a',
		),
		array(
			'text'                      => __( '3D', 'landtech-extras-for-elementor' ),
			'button_effect_type'        => '3d',
			'button_effect_direction'   => 'down',
			'background_color'          => '#ea580c',
			'button_effect_background_color' => '#9a3412',
		),
		array(
			'text'                      => __( 'Cube', 'landtech-extras-for-elementor' ),
			'button_effect_type'        => 'cube',
			'button_effect_direction'   => 'right',
			'button_effect_text'        => __( 'Rotate', 'landtech-extras-for-elementor' ),
			'background_color'          => '#db2777',
			'button_effect_background_color' => '#9d174d',
		),
	);

	$buttons = array();

	foreach ( $demos as $index => $demo ) {
		$buttons[] = array_merge(
			array(
				'_id'                         => isset( $demo_ids[ $index ] ) ? $demo_ids[ $index ] : landtech_extras_playground_element_id(),
				'button_custom_style'         => 'yes',
				'size'                        => 'md',
				'button_text_color'           => '#ffffff',
				'button_effect_color'         => '#ffffff',
				'button_effect_duration'      => 0.35,
				'button_effect_easing'        => 'ease-in-out',
			),
			$demo
		);
	}

	return array(
		'buttons' => $buttons,
		'gap'     => 'wide',
		'align'   => 'center',
	);
}

/**
 * Stable repeater IDs for the Playground Button Group demo (used by fallback CSS).
 *
 * @return array<int,string>
 */
function landtech_extras_playground_button_group_demo_ids() {
	return array( 'ltxeb01', 'ltxeb02', 'ltxeb03', 'ltxeb04', 'ltxeb05' );
}

/**
 * Fallback Elementor page custom CSS for Button Group Playground demos.
 *
 * Ensures padding, colors, and effect backgrounds apply when post CSS is missing.
 *
 * @return string
 */
function landtech_extras_playground_button_group_custom_css() {
	$styles = array(
		'ltxeb01' => array(
			'bg'        => '#2563eb',
			'effect_bg' => '#1e3a8a',
			'color'     => '#ffffff',
		),
		'ltxeb02' => array(
			'bg'        => '#7c3aed',
			'effect_bg' => '#5b21b6',
			'color'     => '#ffffff',
		),
		'ltxeb03' => array(
			'bg'        => '#0f766e',
			'effect_bg' => '#134e4a',
			'color'     => '#ffffff',
		),
		'ltxeb04' => array(
			'bg'        => '#ea580c',
			'effect_bg' => '#9a3412',
			'color'     => '#ffffff',
		),
		'ltxeb05' => array(
			'bg'        => '#db2777',
			'effect_bg' => '#9d174d',
			'color'     => '#ffffff',
		),
	);

	$css = '';

	foreach ( $styles as $id => $colors ) {
		$selector = '.elementor-widget-button-group .elementor-repeater-item-' . $id;

		$css .= $selector . ' .ee-button-content-wrapper,' . $selector . ' .ee-button:after{padding:12px 28px;}';
		$css .= $selector . ' .ee-button{background-color:' . $colors['bg'] . ';color:' . $colors['color'] . ';}';
		$css .= $selector . '.ee-effect--background .ee-button:before{background-color:' . $colors['effect_bg'] . ';}';
		$css .= $selector . '.ee-effect--foreground .ee-button:after{color:' . $colors['color'] . ';}';
		$css .= $selector . ' .ee-button:before,' . $selector . ' .ee-button:after,' . $selector . ' .ee-button,' . $selector . ' .ee-button-content-wrapper{transition-duration:.35s;transition-timing-function:ease-in-out;}';
	}

	return $css;
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
			'image_seed'  => 'switcher-design',
		),
		array(
			'label'       => __( 'Build', 'landtech-extras-for-elementor' ),
			'title'       => __( 'Build with Elementor', 'landtech-extras-for-elementor' ),
			'description' => __( 'Combine LandTech Extras widgets with Elementor controls on the canvas.', 'landtech-extras-for-elementor' ),
			'image_seed'  => 'switcher-build',
		),
		array(
			'label'       => __( 'Launch', 'landtech-extras-for-elementor' ),
			'title'       => __( 'Launch your site', 'landtech-extras-for-elementor' ),
			'description' => __( 'Preview interactions like this switcher before going live.', 'landtech-extras-for-elementor' ),
			'image_seed'  => 'switcher-launch',
		),
	);

	$panel_ids = array( 'ltxes01', 'ltxes02', 'ltxes03' );
	$items     = array();

	foreach ( $panels as $index => $panel ) {
		if ( ! empty( $images[ $index ] ) ) {
			$image = landtech_extras_playground_media( (int) $images[ $index ] );
		} else {
			$image = array(
				'id'  => 0,
				'url' => 'https://picsum.photos/seed/' . rawurlencode( 'landtech-' . $panel['image_seed'] ) . '/900/600',
			);
		}

		$items[] = array(
			'_id'         => isset( $panel_ids[ $index ] ) ? $panel_ids[ $index ] : landtech_extras_playground_element_id(),
			'title'       => $panel['title'],
			'label'       => $panel['label'],
			'description' => $panel['description'],
			'image'       => $image,
		);
	}

	return array(
		'_skin'           => 'classic',
		'description'     => 'yes',
		'effect_entrance' => '',
		'menu'            => 'show',
		'menu_tablet'     => 'show',
		'menu_mobile'     => 'show',
		'speed'           => array(
			'size' => 0.8,
			'unit' => 'px',
		),
		'items'           => $items,
	);
}

/**
 * Fallback Elementor page custom CSS for Switcher Playground demos.
 *
 * @return string
 */
function landtech_extras_playground_switcher_custom_css() {
	return '.elementor-widget-ee-switcher .ee-switcher__nav__item{color:#0f172a;}'
		. '.elementor-widget-ee-switcher .ee-switcher__nav__item.is--active{color:#2563eb;}'
		. '.elementor-widget-ee-switcher .ee-switcher__title{color:#0f172a;}'
		. '.elementor-widget-ee-switcher .ee-switcher__descriptions__description{color:#475569;}'
		. '.elementor-widget-ee-switcher .ee-switcher__wrapper{min-height:420px;}';
}

/**
 * Default Devices widget settings for Playground demos.
 *
 * @param array<int,int> $images Attachment IDs.
 * @return array<string,mixed>
 */
function landtech_extras_playground_devices_settings( array $images ) {
	$portrait_id = landtech_extras_playground_sideload_image(
		'https://picsum.photos/seed/landtech-device-portrait/800/2400',
		__( 'LandTech Extras device demo portrait screenshot', 'landtech-extras-for-elementor' )
	);

	if ( $portrait_id <= 0 && ! empty( $images[7] ) ) {
		$portrait_id = (int) $images[7];
	} elseif ( $portrait_id <= 0 && ! empty( $images[0] ) ) {
		$portrait_id = (int) $images[0];
	}

	$landscape_id = landtech_extras_playground_sideload_image(
		'https://picsum.photos/seed/landtech-device-landscape/1200/800',
		__( 'LandTech Extras device demo landscape screenshot', 'landtech-extras-for-elementor' )
	);

	$settings = array(
		'device_type'                          => 'phone',
		'device_media_type'                    => 'image',
		'device_orientation_control'           => 'yes',
		'media_portrait_screenshot_scrollable' => 'scrollable',
		'device_width'                         => array(
			'size' => 300,
			'unit' => 'px',
		),
	);

	if ( $portrait_id > 0 ) {
		$settings['media_portrait_screenshot'] = landtech_extras_playground_media( $portrait_id );
	} else {
		$settings['media_portrait_screenshot'] = array(
			'id'  => 0,
			'url' => 'https://picsum.photos/seed/landtech-device-portrait/800/2400',
		);
	}

	if ( $landscape_id > 0 ) {
		$settings['media_landscape_screenshot'] = landtech_extras_playground_media( $landscape_id );
	} else {
		$settings['media_landscape_screenshot'] = array(
			'id'  => 0,
			'url' => 'https://picsum.photos/seed/landtech-device-landscape/1200/800',
		);
	}

	return $settings;
}

/**
 * Default Scroll Indicator widget settings for Playground demos.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_scroll_indicator_settings() {
	return array(
		'_skin'    => 'list',
		'sections' => array(
			array(
				'_id'                 => landtech_extras_playground_element_id(),
				'selector'            => 'ltxe-scroll-part-1',
				'title'               => __( 'Part 1', 'landtech-extras-for-elementor' ),
				'subtitle'            => __( 'Start of page', 'landtech-extras-for-elementor' ),
				'progress_start'      => 'top-top',
				'progress_end'        => 'top-bottom',
				'progress_start_offset' => array(
					'size' => 0,
					'unit' => 'px',
				),
				'progress_end_offset' => array(
					'size' => 0,
					'unit' => 'px',
				),
			),
			array(
				'_id'                 => landtech_extras_playground_element_id(),
				'selector'            => 'ltxe-scroll-part-2',
				'title'               => __( 'Part 2', 'landtech-extras-for-elementor' ),
				'subtitle'            => __( 'Middle section', 'landtech-extras-for-elementor' ),
				'progress_start'      => 'top-top',
				'progress_end'        => 'top-bottom',
				'progress_start_offset' => array(
					'size' => 0,
					'unit' => 'px',
				),
				'progress_end_offset' => array(
					'size' => 0,
					'unit' => 'px',
				),
			),
			array(
				'_id'                 => landtech_extras_playground_element_id(),
				'selector'            => 'ltxe-scroll-part-3',
				'title'               => __( 'Part 3', 'landtech-extras-for-elementor' ),
				'subtitle'            => __( 'End of page', 'landtech-extras-for-elementor' ),
				'progress_start'      => 'top-top',
				'progress_end'        => 'top-bottom',
				'progress_start_offset' => array(
					'size' => 0,
					'unit' => 'px',
				),
				'progress_end_offset' => array(
					'size' => 0,
					'unit' => 'px',
				),
			),
		),
	);
}

/**
 * Default Age Gate widget settings for Playground demos.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_age_gate_settings() {
	return array(
		'_skin'           => 'classic',
		'age'             => 18,
		'title'           => __( 'Age verification', 'landtech-extras-for-elementor' ),
		'description'     => __( 'Please confirm your age to continue browsing this demo site.', 'landtech-extras-for-elementor' ),
		'button_text'     => __( 'Let me in', 'landtech-extras-for-elementor' ),
		'popup_open_admin' => 'yes',
	);
}

/**
 * Default Popup widget settings for Playground demos.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_popup_settings() {
	return array(
		'_skin'              => 'classic',
		'popup_type'         => 'text',
		'popup_trigger'      => 'click',
		'popup_click_target' => 'text',
		'popup_trigger_text' => __( 'Open modal', 'landtech-extras-for-elementor' ),
		'popup_title'        => __( 'LandTech Extras Popup', 'landtech-extras-for-elementor' ),
		'popup_content'      => '<p>' . esc_html__( 'This modal is powered by the Popup widget and GLightbox. Use it for announcements, newsletter signups, terms, or any message that should not take over the whole page.', 'landtech-extras-for-elementor' ) . '</p><p>' . esc_html__( 'Configure trigger type (click, scroll, exit intent), animation, overlay, and close behavior under the widget settings.', 'landtech-extras-for-elementor' ) . '</p>',
		'popup_close_on_bg'  => 'yes',
	);
}

/**
 * Default Unfold widget settings for Playground demos.
 *
 * Requires non-empty WYSIWYG content and a visible percentage below 100 or the trigger is hidden.
 *
 * @return array<string,mixed>
 */
function landtech_extras_playground_unfold_settings() {
	$content = '<p>' . esc_html__( 'LandTech Extras for Elementor adds creative widgets on top of Elementor — gallery sliders, interactive navigation, posts layouts, maps, and more.', 'landtech-extras-for-elementor' ) . '</p>';
	$content .= '<p>' . esc_html__( 'The Unfold widget truncates long copy with a gradient fade and animates the rest into view when visitors click Read more. Use it for bios, product details, legal text, or any section where you want a cleaner first impression without losing the full story.', 'landtech-extras-for-elementor' ) . '</p>';
	$content .= '<p>' . esc_html__( 'Adjust the visible percentage, animation speed, and button labels in the widget panel. Click the button below to preview the unfold animation on this demo page.', 'landtech-extras-for-elementor' ) . '</p>';

	return array(
		'content'              => $content,
		'visible_percentage'   => array(
			'size' => 35,
		),
		'text_closed'          => __( 'Read more', 'landtech-extras-for-elementor' ),
		'text_open'            => __( 'Read less', 'landtech-extras-for-elementor' ),
		'duration_unfold'      => array(
			'size' => 0.6,
		),
		'duration_fold'        => array(
			'size' => 0.5,
		),
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
 * @param int                $page_id        Page ID.
 * @param array              $elements       Elementor element tree.
 * @param array<string,mixed> $page_settings Optional document settings.
 * @return void
 */
function landtech_extras_playground_save_elementor_data( $page_id, array $elements, array $page_settings = array() ) {
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
			$save_payload = array(
				'elements' => $elements,
			);

			if ( ! empty( $page_settings ) ) {
				$save_payload['settings'] = $page_settings;
			}

			$saved = $document->save( $save_payload );

			if ( $saved ) {
				landtech_extras_playground_regenerate_elementor_css( $page_id );
				return;
			}
		}
	}

	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
	delete_post_meta( $page_id, '_elementor_css' );

	if ( ! empty( $page_settings ) ) {
		update_post_meta( $page_id, '_elementor_page_settings', $page_settings );

		if ( ! empty( $page_settings['template'] ) ) {
			update_post_meta( $page_id, '_wp_page_template', $page_settings['template'] );
		}
	}

	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->db->save_plain_text( $page_id );
	}

	landtech_extras_playground_regenerate_elementor_css( $page_id );
}

/**
 * Regenerate Elementor post CSS so repeater control styles (e.g. button effects) apply on seeded demos.
 *
 * @param int $page_id Page ID.
 * @return void
 */
function landtech_extras_playground_regenerate_elementor_css( $page_id ) {
	$page_id = absint( $page_id );

	if ( $page_id <= 0 || ! class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
		return;
	}

	$css_file = \Elementor\Core\Files\CSS\Post::create( $page_id );

	if ( $css_file ) {
		$css_file->update();
	}
}

/**
 * @param string $title   Page title.
 * @param string $slug    Page slug.
 * @param string $intro   Intro paragraph for the demo page.
 * @param array  $widgets Widget element trees for the demo section.
 * @param array<string,mixed> $page_settings Optional Elementor document settings merged with defaults.
 * @return int Page ID or 0.
 */
function landtech_extras_playground_create_elementor_page( $title, $slug, $intro, array $widgets, array $page_settings = array() ) {
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

	landtech_extras_playground_save_elementor_data(
		$page_id,
		$data,
		array_merge( landtech_extras_playground_page_settings(), $page_settings )
	);

	return (int) $page_id;
}

/**
 * Elementor document settings shared by Playground demo pages.
 *
 * Must be a PHP array — Elementor expects `_elementor_page_settings` to be serialized array data.
 *
 * @return array<string,string>
 */
function landtech_extras_playground_page_settings() {
	return array(
		'hide_title' => 'yes',
		'template'   => 'elementor_header_footer',
	);
}

/**
 * Assign Elementor page template meta for demo pages.
 *
 * @param int $page_id Page ID.
 * @return void
 */
function landtech_extras_playground_set_elementor_page_template( $page_id ) {
	$page_id = absint( $page_id );

	if ( $page_id <= 0 ) {
		return;
	}

	update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
	update_post_meta( $page_id, '_elementor_page_settings', landtech_extras_playground_page_settings() );
}

/**
 * Build styled homepage markup (CSS + demo card grid) for the Playground landing page.
 *
 * @param array<string,array<int,array<string,mixed>>> $groups Demo groups.
 * @param array<int,array<string,mixed>>             $demos  Full demo definitions (for intros).
 * @param array<int,int>                               $images Attachment IDs for hero art.
 * @return string HTML for Elementor HTML widget.
 */
function landtech_extras_playground_build_homepage_html( array $groups, array $demos, array $images = array() ) {
	$intro_by_slug = array();

	foreach ( $demos as $demo ) {
		if ( empty( $demo['slug'] ) ) {
			continue;
		}

		$intro_by_slug[ $demo['slug'] ] = isset( $demo['intro'] ) ? $demo['intro'] : '';
	}

	$widget_count = 0;

	foreach ( $groups as $items ) {
		$widget_count += count( $items );
	}

	$hero_style = '';

	if ( ! empty( $images[0] ) ) {
		$hero_url = wp_get_attachment_url( (int) $images[0] );

		if ( $hero_url ) {
			$hero_style = ' style="background-image:linear-gradient(135deg,rgba(15,23,42,.88),rgba(30,64,175,.72)),url(' . esc_url( $hero_url ) . ');"';
		}
	}

	$html  = '<style>';
	$html .= '.ltxe-playground-home{--ltxe-ph-accent:#2563eb;--ltxe-ph-accent-soft:#dbeafe;--ltxe-ph-ink:#0f172a;--ltxe-ph-muted:#475569;--ltxe-ph-border:#e2e8f0;--ltxe-ph-card:#fff;--ltxe-ph-radius:16px;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;color:var(--ltxe-ph-ink);line-height:1.6}';
	$html .= '.ltxe-playground-home *,.ltxe-playground-home *::before,.ltxe-playground-home *::after{box-sizing:border-box}';
	$html .= '.ltxe-ph-hero{padding:clamp(2.5rem,6vw,4.5rem) clamp(1.25rem,4vw,2.5rem);background:#0f172a center/cover no-repeat;color:#fff;text-align:center;border-radius:0 0 var(--ltxe-ph-radius) var(--ltxe-ph-radius);margin:0 0 2rem}';
	$html .= '.ltxe-ph-hero__inner{max-width:760px;margin:0 auto}';
	$html .= '.ltxe-ph-badge{display:inline-block;margin:0 0 1rem;padding:.35rem .85rem;border-radius:999px;background:rgba(255,255,255,.14);font-size:.78rem;font-weight:600;letter-spacing:.04em;text-transform:uppercase}';
	$html .= '.ltxe-ph-hero h1{margin:0 0 .75rem;font-size:clamp(1.85rem,4vw,2.75rem);line-height:1.15;font-weight:800}';
	$html .= '.ltxe-ph-lead{margin:0 auto 1.5rem;max-width:640px;font-size:clamp(1rem,2vw,1.125rem);color:rgba(255,255,255,.92)}';
	$html .= '.ltxe-ph-stats{display:flex;flex-wrap:wrap;justify-content:center;gap:.75rem}';
	$html .= '.ltxe-ph-stat{min-width:7.5rem;padding:.75rem 1rem;border-radius:12px;background:rgba(255,255,255,.12);backdrop-filter:blur(6px)}';
	$html .= '.ltxe-ph-stat strong{display:block;font-size:1.35rem;line-height:1.2}';
	$html .= '.ltxe-ph-stat span{font-size:.82rem;opacity:.9}';
	$html .= '.ltxe-ph-main{max-width:1120px;margin:0 auto;padding:0 clamp(1rem,3vw,1.5rem) 3rem}';
	$html .= '.ltxe-ph-actions{display:flex;flex-wrap:wrap;gap:.75rem;justify-content:center;margin:0 0 2.5rem}';
	$html .= '.ltxe-ph-btn{display:inline-flex;align-items:center;gap:.35rem;padding:.7rem 1.15rem;border-radius:999px;font-weight:600;text-decoration:none;transition:transform .15s ease,box-shadow .15s ease}';
	$html .= '.ltxe-ph-btn:hover{transform:translateY(-1px)}';
	$html .= '.ltxe-ph-btn--primary{background:var(--ltxe-ph-accent);color:#fff;box-shadow:0 10px 24px rgba(37,99,235,.25)}';
	$html .= '.ltxe-ph-btn--ghost{background:#fff;color:var(--ltxe-ph-accent);border:1px solid var(--ltxe-ph-border)}';
	$html .= '.ltxe-ph-group{margin:0 0 2.5rem}';
	$html .= '.ltxe-ph-group__head{display:flex;align-items:center;gap:.75rem;margin:0 0 1rem;padding-bottom:.65rem;border-bottom:2px solid var(--ltxe-ph-border)}';
	$html .= '.ltxe-ph-group__icon{width:2.5rem;height:2.5rem;border-radius:10px;display:grid;place-items:center;font-size:1.1rem;background:var(--ltxe-ph-accent-soft);color:var(--ltxe-ph-accent);flex-shrink:0}';
	$html .= '.ltxe-ph-group h2{margin:0;font-size:1.25rem}';
	$html .= '.ltxe-ph-group p{margin:.15rem 0 0;font-size:.92rem;color:var(--ltxe-ph-muted)}';
	$html .= '.ltxe-ph-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem}';
	$html .= '.ltxe-ph-card{display:flex;flex-direction:column;min-height:100%;padding:1.1rem 1.15rem;border:1px solid var(--ltxe-ph-border);border-radius:14px;background:var(--ltxe-ph-card);text-decoration:none;color:inherit;box-shadow:0 1px 2px rgba(15,23,42,.04);transition:border-color .15s ease,box-shadow .15s ease,transform .15s ease}';
	$html .= '.ltxe-ph-card:hover{border-color:#93c5fd;box-shadow:0 14px 30px rgba(37,99,235,.12);transform:translateY(-2px)}';
	$html .= '.ltxe-ph-card h3{margin:0 0 .45rem;font-size:1rem;line-height:1.35;color:var(--ltxe-ph-ink)}';
	$html .= '.ltxe-ph-card p{margin:0 0 .85rem;flex:1;font-size:.88rem;color:var(--ltxe-ph-muted)}';
	$html .= '.ltxe-ph-card__cta{margin-top:auto;font-size:.82rem;font-weight:700;color:var(--ltxe-ph-accent)}';
	$html .= '.ltxe-ph-footer{margin-top:2rem;padding:1.25rem 1.5rem;border-radius:14px;background:#f8fafc;border:1px solid var(--ltxe-ph-border);text-align:center;font-size:.92rem;color:var(--ltxe-ph-muted)}';
	$html .= '.ltxe-ph-footer a{color:var(--ltxe-ph-accent);font-weight:600;text-decoration:none}';
	$html .= '@media (max-width:640px){.ltxe-ph-stats{flex-direction:column;align-items:stretch}.ltxe-ph-stat{min-width:0}}';
	$html .= '</style>';

	$html .= '<div class="ltxe-playground-home">';
	$html .= '<header class="ltxe-ph-hero"' . $hero_style . '>';
	$html .= '<div class="ltxe-ph-hero__inner">';
	$html .= '<span class="ltxe-ph-badge">' . esc_html__( 'WordPress Playground', 'landtech-extras-for-elementor' ) . '</span>';
	$html .= '<h1>' . esc_html__( 'LandTech Extras for Elementor', 'landtech-extras-for-elementor' ) . '</h1>';
	$html .= '<p class="ltxe-ph-lead">' . esc_html__( 'Explore every free widget in this live preview. Each card opens a dedicated demo page with sample content. Log in as admin / password to edit with Elementor.', 'landtech-extras-for-elementor' ) . '</p>';
	$html .= '<div class="ltxe-ph-stats">';
	$html .= '<div class="ltxe-ph-stat"><strong>' . esc_html( (string) $widget_count ) . '</strong><span>' . esc_html__( 'Widget demos', 'landtech-extras-for-elementor' ) . '</span></div>';
	$html .= '<div class="ltxe-ph-stat"><strong>' . esc_html( (string) count( $groups ) ) . '</strong><span>' . esc_html__( 'Categories', 'landtech-extras-for-elementor' ) . '</span></div>';
	$html .= '<div class="ltxe-ph-stat"><strong>100%</strong><span>' . esc_html__( 'Free plugin', 'landtech-extras-for-elementor' ) . '</span></div>';
	$html .= '</div></div></header>';

	$html .= '<main class="ltxe-ph-main">';
	$html .= '<div class="ltxe-ph-actions">';
	$html .= '<a class="ltxe-ph-btn ltxe-ph-btn--primary" href="' . esc_url( admin_url( 'admin.php?page=elementor' ) ) . '">' . esc_html__( 'Open Elementor', 'landtech-extras-for-elementor' ) . '</a>';
	$html .= '<a class="ltxe-ph-btn ltxe-ph-btn--ghost" href="' . esc_url( admin_url( 'admin.php?page=landtech-extras' ) ) . '">' . esc_html__( 'LandTech Extras settings', 'landtech-extras-for-elementor' ) . '</a>';
	$html .= '</div>';

	$group_icons = array(
		'Media & Gallery'      => '🖼',
		'Forms & Search'       => '🔍',
		'Content & Posts'      => '📝',
		'Typography & Buttons' => '✏️',
		'Maps & Location'      => '📍',
		'Navigation & UI'      => '🧭',
		'Utilities'            => '⚙️',
	);

	foreach ( $groups as $group_name => $items ) {
		$icon = isset( $group_icons[ $group_name ] ) ? $group_icons[ $group_name ] : '✦';

		$html .= '<section class="ltxe-ph-group">';
		$html .= '<div class="ltxe-ph-group__head">';
		$html .= '<span class="ltxe-ph-group__icon" aria-hidden="true">' . esc_html( $icon ) . '</span>';
		$html .= '<div><h2>' . esc_html( $group_name ) . '</h2>';
		$html .= '<p>' . esc_html(
			sprintf(
				/* translators: %d: number of widget demos in this category. */
				_n( '%d interactive demo', '%d interactive demos', count( $items ), 'landtech-extras-for-elementor' ),
				count( $items )
			)
		) . '</p></div></div>';
		$html .= '<div class="ltxe-ph-grid">';

		foreach ( $items as $item ) {
			$url = get_permalink( $item['page_id'] );

			if ( ! $url ) {
				continue;
			}

			$slug  = isset( $item['slug'] ) ? $item['slug'] : '';
			$intro = isset( $intro_by_slug[ $slug ] ) ? $intro_by_slug[ $slug ] : '';

			$html .= '<a class="ltxe-ph-card" href="' . esc_url( $url ) . '">';
			$html .= '<h3>' . esc_html( $item['title'] ) . '</h3>';

			if ( '' !== $intro ) {
				$html .= '<p>' . esc_html( $intro ) . '</p>';
			}

			$html .= '<span class="ltxe-ph-card__cta">' . esc_html__( 'View demo →', 'landtech-extras-for-elementor' ) . '</span>';
			$html .= '</a>';
		}

		$html .= '</div></section>';
	}

	$html .= '<div class="ltxe-ph-footer">';
	$html .= esc_html__( 'This sandbox installs Elementor and LandTech Extras from WordPress.org only — no premium add-on.', 'landtech-extras-for-elementor' );
	$html .= ' <a href="https://wordpress.org/plugins/landtech-extras-for-elementor/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'View on WordPress.org', 'landtech-extras-for-elementor' ) . '</a>';
	$html .= '</div>';
	$html .= '</main></div>';

	return $html;
}

/**
 * @param array<string,array<int,array<string,mixed>>> $groups Demo groups for the homepage grid.
 * @param array<string,int>                            $pages  Slug => page ID map.
 * @param array<int,array<string,mixed>>               $demos  Demo definitions (intros).
 * @param array<int,int>                               $images Attachment IDs for hero styling.
 * @return int Homepage ID.
 */
function landtech_extras_playground_create_homepage( array $groups, array $pages, array $demos = array(), array $images = array() ) {
	$homepage_html = landtech_extras_playground_widget(
		'html',
		array(
			'html' => landtech_extras_playground_build_homepage_html( $groups, $demos, $images ),
		)
	);

	$data = array(
		landtech_extras_playground_section( array( $homepage_html ) ),
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

	landtech_extras_playground_save_elementor_data(
		$page_id,
		$data,
		landtech_extras_playground_page_settings()
	);

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
