<?php
// Modified and maintained by Land Tech Web Designs (2026) under the GPLv3 license.
namespace LandTechExtras;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main Plugin Class
 *
 * Register elementor widget.
 *
 * @since 0.1.0
 */
class LandTechExtrasPlugin {

	/**
	 * @var Settings
	 */
	public $settings;

	/**
	 * @var Modules_Manager
	 */
	public $modules_manager;

	/**
	 * @var Extensions_Manager
	 */
	public $extensions_manager;

	/**
	 * @var WPML
	 */
	public $wpml_compatibility;

	/**
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * @var string Anime.js version bundled for animations.
	 */
	public $anime_version = '4.5.0';

	/**
	 * @var Links
	 */
	public $links = [
		'shop'              => 'https://landtechwebdesigns.com/', // Base for relative paths; trailing slash required.
		'support'           => 'mailto:sales@landtechwebdesigns.com',
		'docs'              => 'https://extrasforelementor.com/docs/',
		'docs_ig_token'     => 'https://extrasforelementor.com/docs/apis/#instagram',
		'generate_ig_token' => 'https://extrasforelementor.com/docs/apis/#instagram',
	];

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		add_action( 'init', [ $this, 'on_init' ] );
		add_action( 'elementor/init', [ $this, 'on_elementor_init' ], 0 );
	}

	/**
	 * On Init
	 *
	 * @since 2.2.38
	 * @access public
	 */
	public function on_init() {
		if ( ! $this->get_current_version() || version_compare( LANDTECH_EXTRAS_VERSION, $this->get_current_version(), '!=' ) ) {

			// Set new version
			$this->set_current_version();
		}
	}

	/**
	 * On Elementor Init
	 *
	 * @since 0.1.0
	 * @access public
	 */
	public function on_elementor_init() {

		// Elementor hooks
		$this->add_actions();

		/* INCLUDES */

		// Include extensions
		$this->includes();

		// Components
		$this->init_components();

		// Add editor panel widgets section
		$this->init_panel_section();

		/**
		 * LandTech Extras init action.
		 *
		 * @since 1.0.0
		 */
		do_action( 'landtech_extras/init' );
	}

	/**
	 * Plugin instance
	 * 
	 * @since 0.1.0
	 * @return Plugin
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add Actions
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	private function add_actions() {

		// Register widgets hook

		add_action( 'elementor/controls/register', [ $this, 'register_controls' ] );
		add_action( 'elementor/controls/register', [ $this, 'register_group_controls' ] );

		// ——— SCRIPTS ——— //

			// Editor Scripts
			add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );

			// Front-end Scripts
			add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ] );
			add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
			// Elementor Improved Asset Loading enqueues `_elementor_page_assets` on wp_enqueue_scripts.
			// Register widget handles before that so page-asset scripts (calendar, progress bar, etc.) print.
			add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ], 1 );
			add_action( 'wp_footer', [ $this, 'print_document_landtech_assets' ], 25 );

			// Preview
			// add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_scripts' ] );
			// add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] ); 

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

		// ——— STYLES ——— //

			// Admin Styles
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );

			// Editor Styles
			add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );

			// Editor Preview Styles (after other preview listeners register handles).
			add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_editor_preview_styles' ], PHP_INT_MAX );

			// Shared handles after Elementor registers `elementor-frontend` (WP 6.9.1+ validates deps).
			add_action( 'elementor/frontend/before_register_styles', [ $this, 'register_landtech_extras_frontend_style_handles' ], 20 );

			// Front-end Styles
			add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_styles' ] );

			/*
			 * Preview iframe only: dequeue default frontend handle; enqueue preview-tail handle late on wp_enqueue_scripts
			 * ({@see dequeue_extras_frontend_for_preview_tail_style()}) so cascade matches former wp_head tail behavior.
			 */
			add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_extras_frontend_for_preview_tail_style' ], PHP_INT_MAX );
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since 1.8.0
	 *
	 * @access public
	 */
	public function enqueue_admin_scripts() {

		global $pagenow;

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$page_slug = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin query arg for script enqueue; sanitized immediately.

		if ( 'admin.php' === $pagenow && 'landtech-extras' === $page_slug ) {
			
			// Register scripts
			wp_register_script(
				'landtech-extras-admin',
				plugins_url( '/assets/js/admin' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
				[],
				LANDTECH_EXTRAS_VERSION,
				true );

			// Enqueue scripts
			wp_enqueue_script( 'landtech-extras-admin' );
		}
	}

	/**
	 * Enqueue admin styles
	 *
	 * @since 1.1.3
	 *
	 * @access public
	 */
	public function enqueue_admin_styles() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register styles
		wp_register_style(
			'landtech-extras-admin',
			plugins_url( '/assets/css/admin' . $suffix . '.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_style(
			'landtech-extras-nicons',
			LANDTECH_EXTRAS_ASSETS_URL . 'lib/nicons/css/nicons.css',
			[],
			LANDTECH_EXTRAS_VERSION
		);

		// Enqueue styles
		wp_enqueue_style( 'landtech-extras-nicons' );
		wp_enqueue_style( 'landtech-extras-admin' );

		global $pagenow;
		$page_slug = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin query arg for style enqueue; sanitized immediately.
		if ( 'admin.php' === $pagenow && 'landtech-extras' === $page_slug && ! \LandTechExtras\Admin\Addon_Catalog::add_on_is_active() ) {
			wp_enqueue_style(
				'landtech-extras-addon-catalog',
				plugins_url( '/assets/css/addon-catalog.css', LANDTECH_EXTRAS__FILE__ ),
				array( 'landtech-extras-admin' ),
				LANDTECH_EXTRAS_VERSION
			);
		}
	}

	/**
	 * Enqueue editor scripts
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function enqueue_editor_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Widget dependency handles (registered on elementor/frontend/after_register_scripts) may not exist yet in pure editor bootstrap.
		$this->register_scripts();

		$posts_editor_deps = [ 'jquery', 'landtech-extras-jquery-resize', 'landtech-extras-isotope', 'landtech-extras-filtery' ];
		if ( function_exists( 'landtech_extras_editor_document_includes_packery_posts_layout' ) && landtech_extras_editor_document_includes_packery_posts_layout() ) {
			$posts_editor_deps = [ 'jquery', 'landtech-extras-jquery-resize', 'landtech-extras-isotope', 'landtech-extras-packery', 'landtech-extras-isotope-packery-mode', 'landtech-extras-filtery' ];
		}
		if ( wp_script_is( 'imagesloaded', 'registered' ) ) {
			$posts_editor_deps[] = 'imagesloaded';
		} elseif ( wp_script_is( 'imagesLoaded', 'registered' ) ) {
			$posts_editor_deps[] = 'imagesLoaded';
		}

		wp_register_script(
			'landtech-extras-gallery-masonry-editor',
			plugins_url( '/assets/js/gallery-masonry-editor.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-isotope', 'landtech-extras-jquery-resize' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_script(
			'landtech-extras-posts-loop-isotope-editor',
			plugins_url( '/assets/js/posts-loop-isotope-editor.js', LANDTECH_EXTRAS__FILE__ ),
			$posts_editor_deps,
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_enqueue_script( 'landtech-extras-gallery-masonry-editor' );

		if ( function_exists( 'landtech_extras_editor_should_enqueue_posts_loop_isotope_editor_script' ) && landtech_extras_editor_should_enqueue_posts_loop_isotope_editor_script() ) {
			wp_enqueue_script( 'landtech-extras-posts-loop-isotope-editor' );
		}

		if ( function_exists( 'landtech_extras_editor_should_enqueue_table_csv_editor_script' ) && landtech_extras_editor_should_enqueue_table_csv_editor_script() ) {
			wp_enqueue_script( 'landtech-extras-table-csv-editor' );
		}

		$landtech_extras_frontend_config = [
			'urls' => [
				'assets'       => LANDTECH_EXTRAS_ASSETS_URL,
				'leafletIcons' => LANDTECH_EXTRAS_ASSETS_URL . 'lib/leaflet/images/',
			],
			'restSearchUrl' => rest_url( 'landtech-extras/v1/search' ),
		];

		// Register scripts
		wp_register_script(
			'landtech-extras-editor',
			plugins_url( '/assets/js/editor' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION,
			true );

		wp_localize_script( 'landtech-extras-editor', 'landtechExtrasFrontendConfig', $landtech_extras_frontend_config );

		wp_register_script(
			'landtech-extras-search-form-editor',
			plugins_url( '/assets/js/search-form-editor.js', LANDTECH_EXTRAS__FILE__ ),
			array( 'jquery', 'elementor-editor' ),
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_localize_script(
			'landtech-extras-search-form-editor',
			'landtechExtrasSearchFormEditor',
			array(
				'restUrl'   => rest_url( 'landtech-extras/v1/taxonomy-terms' ),
				'restNonce' => wp_create_nonce( 'wp_rest' ),
			)
		);

		wp_register_script(
			'landtech-extras-ai-alt-text',
			plugins_url( '/assets/js/editor/ai-alt-text.js', LANDTECH_EXTRAS__FILE__ ),
			array( 'jquery', 'elementor-editor' ),
			LANDTECH_EXTRAS_VERSION,
			true
		);
		wp_localize_script(
			'landtech-extras-ai-alt-text',
			'landtechExtrasAiAlt',
			array(
				'restUrl'   => rest_url( 'landtech-extras/v1/ai/alt-text' ),
				'restNonce' => wp_create_nonce( 'wp_rest' ),
			)
		);

		wp_register_script(
			'landtech-extras-widget-labels',
			plugins_url( '/assets/js/editor/widget-labels.js', LANDTECH_EXTRAS__FILE__ ),
			array( 'elementor-editor' ),
			LANDTECH_EXTRAS_VERSION,
			true
		);

		// Enqueue scripts
		wp_enqueue_script( 'landtech-extras-editor' );
		wp_enqueue_script( 'landtech-extras-search-form-editor' );
		wp_enqueue_script( 'landtech-extras-ai-alt-text' );
		wp_enqueue_script( 'landtech-extras-widget-labels' );
	}

	/**
	 * Register scripts
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function register_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts

		// Non-widget scripts
		wp_register_script(
			'landtech-extras-audio-player',
			plugins_url( '/assets/lib/audio-player/audio-player' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'landtech-extras-wavesurfer',
			plugins_url( '/assets/lib/wavesurfer/wavesurfer' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'7.9.9',
			true
		);

		wp_register_script(
			'landtech-extras-hc-sticky',
			plugins_url( '/assets/lib/hc-sticky/hc-sticky' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'2.2.3',
			true );

		wp_register_script(
			'landtech-extras-parallax-gallery',
			plugins_url( '/assets/lib/parallax-gallery/parallax-gallery' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-anime-helpers' ],
			'1.0.0',
			true );

		wp_register_script(
			'landtech-extras-parallax-element',
			plugins_url( '/assets/lib/parallax-element/parallax-element' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-anime-helpers' ],
			'1.0.0',
			true );

		wp_register_script(
			'landtech-extras-parallax-background',
			plugins_url( '/assets/lib/parallax-background/parallax-background' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.2.0',
			true );

		// Custom widget scripts
		wp_register_script(
			'landtech-extras-image-comparison',
			plugins_url( '/assets/lib/image-comparison/image-comparison' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-anime-helpers' ],
			'1.0.0',
			true );

		wp_register_script(
			'landtech-extras-hotips',
			plugins_url( '/assets/lib/hotips/hotips' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-anime-helpers' ],
			'1.1.0',
			true );

		wp_register_script(
			'landtech-extras-unfold',
			plugins_url( '/assets/lib/unfold/unfold' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-anime-helpers' ],
			'1.0.0',
			true );

		wp_register_script(
			'landtech-extras-temporal-polyfill',
			plugins_url( '/assets/lib/temporal-polyfill/js-temporal-schedule-x.min.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'0.3.0',
			true
		);

		wp_register_script(
			'landtech-extras-preact',
			plugins_url( '/assets/lib/preact/preact' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'10.26.5',
			true
		);

		wp_register_script(
			'landtech-extras-preact-hooks',
			plugins_url( '/assets/lib/preact/hooks.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-preact' ],
			'10.26.5',
			true
		);

		wp_register_script(
			'landtech-extras-preact-jsx-runtime',
			plugins_url( '/assets/lib/preact/jsx-runtime.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-preact' ],
			'10.26.5',
			true
		);

		wp_register_script(
			'landtech-extras-preact-signals-core',
			plugins_url( '/assets/lib/preact/signals-core.min.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'1.14.4',
			true
		);

		wp_register_script(
			'landtech-extras-preact-signals',
			plugins_url( '/assets/lib/preact/signals.min.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-preact', 'landtech-extras-preact-hooks', 'landtech-extras-preact-signals-core' ],
			'2.0.4',
			true
		);

		wp_register_script(
			'landtech-extras-preact-compat',
			plugins_url( '/assets/lib/preact/compat.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-preact', 'landtech-extras-preact-hooks' ],
			'10.26.5',
			true
		);

		wp_register_script(
			'landtech-extras-schedule-x-calendar',
			plugins_url( '/assets/lib/schedule-x/schedule-x-calendar.umd.js', LANDTECH_EXTRAS__FILE__ ),
			[
				'landtech-extras-temporal-polyfill',
				'landtech-extras-preact',
				'landtech-extras-preact-jsx-runtime',
				'landtech-extras-preact-hooks',
				'landtech-extras-preact-compat',
				'landtech-extras-preact-signals-core',
				'landtech-extras-preact-signals',
			],
			'4.6.1',
			true
		);

		wp_register_style(
			'landtech-extras-schedule-x-theme',
			plugins_url( '/assets/lib/schedule-x/schedule-x-theme-default.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			'4.6.1'
		);

		wp_register_script(
			'landtech-extras-calendar-schedule-x',
			plugins_url( '/assets/js/landtech-extras-calendar-schedule-x.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-schedule-x-calendar' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_script(
			'landtech-extras-lottie-player',
			plugins_url( '/assets/lib/lottie-player/lottie-player.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'2.0.12',
			true
		);

		wp_register_script(
			'landtech-extras-lottie-web',
			plugins_url( '/assets/lib/lottie-web/lottie.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'5.13.0',
			true
		);

		wp_register_script(
			'landtech-extras-lottie',
			plugins_url( '/assets/js/widgets/lottie.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-lottie-web' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_script(
			'landtech-extras-popup',
			plugins_url( '/assets/js/widgets/popup.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_script(
			'landtech-extras-hotspots',
			plugins_url( '/assets/js/widgets/hotspots.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-hotips' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_script(
			'landtech-extras-table-csv-editor',
			plugins_url( '/assets/js/table-csv-editor.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_script(
			'landtech-extras-circle-progress',
			plugins_url( '/assets/lib/circle-progress/circle-progress' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'1.2.2',
			true );

		wp_register_script(
			'landtech-extras-scroll-indicator',
			plugins_url( '/assets/lib/scroll-indicator/scroll-indicator' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'1.0.0',
			true );

		if ( 'no' !== $this->settings->get_option( 'load_google_maps_api', 'landtech_extras_apis', false ) ) {
		wp_register_script(
			'landtech-extras-google-maps',
				add_query_arg(
					array(
						'key' => $this->settings->get_option( 'google_maps_api_key', 'landtech_extras_apis', false ),
					),
					'https://maps.googleapis.com/maps/api/js'
				),
				[],
				LANDTECH_EXTRAS_VERSION,
				true
			);
		}

		wp_register_script(
			'landtech-extras-anime',
			plugins_url( '/assets/lib/anime/anime' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			$this->anime_version,
			true
		);

		wp_register_script(
			'landtech-extras-anime-helpers',
			plugins_url( '/assets/lib/landtech-extras-anime-helpers.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-anime' ],
			$this->anime_version . '-shim-3',
			true
		);

		wp_register_script(
			'landtech-extras-splitting',
			plugins_url( '/assets/lib/splitting/splitting' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'1.0.6',
			true
		);

		wp_register_script(
			'landtech-extras-glightbox',
			plugins_url( '/assets/lib/glightbox/js/glightbox' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'3.3.1',
			true
		);

		wp_register_script(
			'landtech-extras-gmap3',
			plugins_url( '/assets/lib/gmap3/gmap3' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'7.2',
			true );

		wp_register_style(
			'landtech-extras-leaflet',
			plugins_url( '/assets/lib/leaflet/leaflet.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			'1.9.4'
		);

		wp_register_script(
			'landtech-extras-leaflet',
			plugins_url( '/assets/lib/leaflet/leaflet.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'1.9.4',
			true
		);

		wp_register_script(
			'landtech-extras-timeline',
			plugins_url( '/assets/lib/timeline/timeline' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'landtech-extras-switcher',
			plugins_url( '/assets/lib/switcher/switcher' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[
				'jquery',
				'landtech-extras-anime-helpers',
				'landtech-extras-splitting',
				'landtech-extras-jquery-appear',
				'landtech-extras-jquery-visible',
				'landtech-extras-jquery-resize',
			],
			'1.0.1',
			true );

		wp_register_script(
			'landtech-extras-toggle-element',
			plugins_url( '/assets/lib/toggle-element/toggle-element' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-anime-helpers' ],
			'1.2.0',
			true );

		wp_register_style(
			'landtech-extras-tabs',
			plugins_url( '/assets/css/tabs.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-tabs',
			plugins_url( '/assets/js/tabs.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_script(
			'landtech-extras-gallery',
			plugins_url( '/assets/js/widgets/gallery.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core', 'landtech-extras-glightbox' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_script(
			'landtech-extras-image-comparison-widget',
			plugins_url( '/assets/js/widgets/image-comparison.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_script(
			'landtech-extras-inline-svg',
			plugins_url( '/assets/js/widgets/inline-svg.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core', 'landtech-extras-anime-helpers' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-progress-bar',
			plugins_url( '/assets/css/progress-bar.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-progress-bar',
			plugins_url( '/assets/js/widgets/progress-bar.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core', 'landtech-extras-anime-helpers' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-countdown',
			plugins_url( '/assets/css/countdown.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-countdown',
			plugins_url( '/assets/js/widgets/countdown.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-reading-progress',
			plugins_url( '/assets/css/reading-progress.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-reading-progress',
			plugins_url( '/assets/js/widgets/reading-progress.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-pricing-table',
			plugins_url( '/assets/css/pricing-table.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-pricing-table',
			plugins_url( '/assets/js/widgets/pricing-table.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-star-rating',
			plugins_url( '/assets/css/star-rating.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_style(
			'landtech-extras-video-playlist',
			plugins_url( '/assets/css/video-playlist.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-video-playlist',
			plugins_url( '/assets/js/widgets/video-playlist.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-testimonials',
			plugins_url( '/assets/css/testimonials.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-testimonials',
			plugins_url( '/assets/js/widgets/testimonials.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-faq',
			plugins_url( '/assets/css/faq.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-faq',
			plugins_url( '/assets/js/widgets/faq.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-team-members',
			plugins_url( '/assets/css/team-members.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-team-members',
			plugins_url( '/assets/js/widgets/team-members.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-icon-box',
			plugins_url( '/assets/css/icon-box.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_style(
			'landtech-extras-cta',
			plugins_url( '/assets/css/cta.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-cta',
			plugins_url( '/assets/js/widgets/cta.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-social-share',
			plugins_url( '/assets/css/social-share.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-social-share',
			plugins_url( '/assets/js/widgets/social-share.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-back-to-top',
			plugins_url( '/assets/css/back-to-top.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-back-to-top',
			plugins_url( '/assets/js/widgets/back-to-top.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-cookie-consent',
			plugins_url( '/assets/css/cookie-consent.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-cookie-consent',
			plugins_url( '/assets/js/widgets/cookie-consent.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-sticky-wrapper',
			plugins_url( '/assets/css/sticky-wrapper.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-sticky-wrapper',
			plugins_url( '/assets/js/widgets/sticky-wrapper.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-dark-mode',
			plugins_url( '/assets/css/dark-mode.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-dark-mode',
			plugins_url( '/assets/js/widgets/dark-mode.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_style(
			'landtech-extras-table-of-contents',
			plugins_url( '/assets/css/table-of-contents.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_script(
			'landtech-extras-table-of-contents',
			plugins_url( '/assets/js/table-of-contents.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery' ],
			LANDTECH_EXTRAS_VERSION,
			true
		);

		wp_register_script(
			'landtech-extras-slidebars',
			plugins_url( '/assets/lib/slidebars/js/slidebars' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.2.7',
			true );

		wp_register_script(
			'landtech-extras-slide-menu',
			plugins_url( '/assets/lib/slide-menu/slide-menu' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.1',
			true );

		wp_register_script(
			'landtech-extras-tilt',
			plugins_url( '/assets/lib/tilt/tilt.jquery' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'landtech-extras-jquery-appear',
			plugins_url( '/assets/lib/jquery-appear/jquery.appear' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'0.3.6',
			true );

		wp_register_script(
			'landtech-extras-jquery-visible',
			plugins_url( '/assets/lib/jquery-visible/jquery.visible' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'landtech-extras-jquery-easing',
			plugins_url( '/assets/lib/jquery-easing/jquery-easing' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.3.2',
			true );

		wp_register_script(
			'landtech-extras-jquery-mobile',
			plugins_url( '/assets/lib/jquery-mobile/jquery-mobile' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.4.3',
			true );

		wp_register_script(
			'landtech-extras-jquery-long-shadow',
			plugins_url( '/assets/lib/jquery-long-shadow/jquery.longShadow' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.1.0',
			true );

		wp_register_script(
			'landtech-extras-video-player',
			plugins_url( '/assets/lib/video-player/video-player' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'landtech-extras-iphone-inline-video',
			plugins_url( '/assets/lib/iphone-inline-video/iphone-inline-video' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'2.2.2',
			true );

		wp_register_script(
			'landtech-extras-tablesorter',
			plugins_url( '/assets/lib/tablesorter/jquery.tablesorter' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'2.32.0',
			true );

		wp_register_script(
			'landtech-extras-ev-emitter',
			plugins_url( '/assets/lib/ev-emitter/ev-emitter.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'2.1.1',
			true );

		wp_register_script(
			'landtech-extras-get-size',
			plugins_url( '/assets/lib/get-size/get-size.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'2.0.3',
			true );

		wp_register_script(
			'landtech-extras-matches-selector',
			plugins_url( '/assets/lib/matches-selector/matches-selector.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'2.0.2',
			true );

		wp_register_script(
			'landtech-extras-fizzy-ui-utils',
			plugins_url( '/assets/lib/fizzy-ui-utils/utils.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-matches-selector' ],
			'2.0.7',
			true );

		wp_register_script(
			'landtech-extras-jquery-bridget',
			plugins_url( '/assets/lib/jquery-bridget/jquery-bridget.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'2.0.1',
			true );

		wp_register_script(
			'landtech-extras-outlayer-item',
			plugins_url( '/assets/lib/outlayer/item.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-ev-emitter', 'landtech-extras-get-size' ],
			'2.1.1',
			true );

		wp_register_script(
			'landtech-extras-outlayer',
			plugins_url( '/assets/lib/outlayer/outlayer.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-jquery-bridget', 'landtech-extras-outlayer-item', 'landtech-extras-fizzy-ui-utils', 'landtech-extras-ev-emitter', 'landtech-extras-get-size' ],
			'2.1.1',
			true );

		wp_register_script(
			'landtech-extras-masonry-layout',
			plugins_url( '/assets/lib/masonry-layout/masonry.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-outlayer', 'landtech-extras-get-size' ],
			'4.2.2',
			true );

		wp_register_script(
			'landtech-extras-isotope-item',
			plugins_url( '/assets/lib/isotope/item.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-outlayer' ],
			'3.0.6',
			true );

		wp_register_script(
			'landtech-extras-isotope-layout-mode',
			plugins_url( '/assets/lib/isotope/layout-mode.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-get-size', 'landtech-extras-outlayer' ],
			'3.0.6',
			true );

		wp_register_script(
			'landtech-extras-isotope-masonry-mode',
			plugins_url( '/assets/lib/isotope/js/layout-modes/masonry.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-isotope-layout-mode', 'landtech-extras-masonry-layout' ],
			'3.0.6',
			true );

		wp_register_script(
			'landtech-extras-isotope-fit-rows-mode',
			plugins_url( '/assets/lib/isotope/js/layout-modes/fit-rows.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-isotope-layout-mode' ],
			'3.0.6',
			true );

		wp_register_script(
			'landtech-extras-isotope-vertical-mode',
			plugins_url( '/assets/lib/isotope/js/layout-modes/vertical.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-isotope-layout-mode' ],
			'3.0.6',
			true );

		wp_register_script(
			'landtech-extras-isotope',
			plugins_url( '/assets/lib/isotope/isotope.js', LANDTECH_EXTRAS__FILE__ ),
			[
				'jquery',
				'landtech-extras-jquery-bridget',
				'landtech-extras-outlayer',
				'landtech-extras-get-size',
				'landtech-extras-matches-selector',
				'landtech-extras-fizzy-ui-utils',
				'landtech-extras-isotope-item',
				'landtech-extras-isotope-layout-mode',
				'landtech-extras-isotope-masonry-mode',
				'landtech-extras-isotope-fit-rows-mode',
				'landtech-extras-isotope-vertical-mode',
			],
			'3.0.6',
			true );

		wp_register_script(
			'landtech-extras-packery-rect',
			plugins_url( '/assets/lib/packery/rect.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'2.1.2',
			true );

		wp_register_script(
			'landtech-extras-packery-packer',
			plugins_url( '/assets/lib/packery/packer.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-packery-rect' ],
			'2.1.2',
			true );

		wp_register_script(
			'landtech-extras-packery-item',
			plugins_url( '/assets/lib/packery/item.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-outlayer', 'landtech-extras-packery-rect' ],
			'2.1.2',
			true );

		wp_register_script(
			'landtech-extras-packery',
			plugins_url( '/assets/lib/packery/packery.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-jquery-bridget', 'landtech-extras-get-size', 'landtech-extras-outlayer', 'landtech-extras-packery-rect', 'landtech-extras-packery-packer', 'landtech-extras-packery-item' ],
			'2.1.2',
			true );

		wp_register_script(
			'landtech-extras-isotope-packery-mode',
			plugins_url( '/assets/lib/isotope-packery/packery-mode.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'landtech-extras-isotope', 'landtech-extras-packery' ],
			'2.0.1',
			true );

		wp_register_script(
			'landtech-extras-filtery',
			plugins_url( '/assets/lib/filtery/filtery' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', ],
			'1.0.0',
			true );

		wp_register_script(
			'landtech-extras-infinite-scroll',
			plugins_url( '/assets/lib/infinite-scroll/infinite-scroll' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			'4.0.1',
			true );

		wp_register_script(
			'landtech-extras-jquery-resize',
			plugins_url( '/assets/lib/jquery-resize/jquery.resize' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery' ],
			'3.0.2',
			true );

		wp_register_script(
			'landtech-extras-frontend-core',
			plugins_url( '/assets/js/frontend-core.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery' ],
			LANDTECH_EXTRAS_VERSION,
			true );

		wp_register_script(
			'landtech-extras-frontend',
			plugins_url( '/assets/js/frontend' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[ 'jquery', 'landtech-extras-frontend-core' ],
			LANDTECH_EXTRAS_VERSION,
			true );

		wp_register_script(
			'landtech-extras-elementor-select2',
			ELEMENTOR_ASSETS_URL . 'lib/e-select2/js/e-select2.full' . $suffix . '.js',
			[
				'jquery',
			],
			'4.0.6-rc.1',
			true
		);

	}

	/**
	 * Late enqueue so widget scripts survive Elementor page-asset queue resets.
	 *
	 * @return void
	 */
	public function print_document_landtech_assets() {
		$this->register_scripts();
		$this->enqueue_document_landtech_assets();
	}

	/**
	 * Enqueue scripts for frontend
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function enqueue_frontend_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$this->register_scripts();
		$this->enqueue_document_landtech_assets();

		$landtech_extras_frontend_config = [
			'urls' => [
				'assets'       => LANDTECH_EXTRAS_ASSETS_URL,
				'leafletIcons' => LANDTECH_EXTRAS_ASSETS_URL . 'lib/leaflet/images/',
			],
			'restSearchUrl' => rest_url( 'landtech-extras/v1/search' ),
			'refreshableWidgets' => apply_filters( 'landtech_extras/widgets/refreshable', [
				'ee-offcanvas.classic',
				'ee-popup.classic',
				'gallery-slider.default',
				'media-carousel.default',
				'image-carousel.default',
				'slides.default',
			] ),
		];

		wp_localize_script( 'landtech-extras-frontend-core', 'landtechExtrasFrontendConfig', $landtech_extras_frontend_config );
		wp_localize_script( 'landtech-extras-frontend', 'landtechExtrasFrontendConfig', $landtech_extras_frontend_config );
		wp_enqueue_script( 'landtech-extras-frontend-core' );
		if ( ! defined( 'LTXE_FRONTEND_SPLIT' ) || ! LTXE_FRONTEND_SPLIT ) {
			wp_enqueue_script( 'landtech-extras-frontend' );
		}
	}

	/**
	 * Re-enqueue LandTech handles listed on the current Elementor document.
	 *
	 * Elementor Improved Asset Loading calls wp_enqueue_* on page-asset handles
	 * before some widget dependencies are registered. Re-enqueue after register_scripts().
	 *
	 * @return void
	 */
	protected function enqueue_document_landtech_assets() {
		if ( ! is_singular() ) {
			return;
		}

		$post_id = get_queried_object_id();
		if ( $post_id <= 0 ) {
			$post_id = get_the_ID();
		}
		if ( $post_id <= 0 ) {
			return;
		}

		$handles = array();
		$styles  = array();

		$assets = get_post_meta( $post_id, '_elementor_page_assets', true );
		if ( is_array( $assets ) ) {
			if ( ! empty( $assets['scripts'] ) && is_array( $assets['scripts'] ) ) {
				$handles = array_merge( $handles, $assets['scripts'] );
			}
			if ( ! empty( $assets['styles'] ) && is_array( $assets['styles'] ) ) {
				$styles = array_merge( $styles, $assets['styles'] );
			}
		}

		$raw = get_post_meta( $post_id, '_elementor_data', true );
		if ( is_string( $raw ) && '' !== $raw ) {
			$map = array(
				'ee-lottie'                     => array( 'landtech-extras-lottie-web', 'landtech-extras-lottie', 'landtech-extras-lottie-player' ),
				'ee-calendar'                   => array( 'landtech-extras-schedule-x-calendar', 'landtech-extras-calendar-schedule-x' ),
				'ee-popup'                      => array( 'landtech-extras-frontend', 'landtech-extras-glightbox', 'landtech-extras-popup' ),
				'ee-tabs'                       => array( 'landtech-extras-tabs' ),
				'gallery-extra'                 => array( 'landtech-extras-gallery', 'landtech-extras-glightbox' ),
				'ee-inline-svg'                 => array( 'landtech-extras-inline-svg' ),
				'image-comparison'              => array( 'landtech-extras-image-comparison-widget' ),
				'hotspots'                      => array( 'landtech-extras-frontend', 'landtech-extras-hotips', 'landtech-extras-hotspots' ),
				'ltxe-progress-bar'             => array( 'landtech-extras-progress-bar' ),
				'ltxe-countdown'                => array( 'landtech-extras-countdown' ),
				'ltxe-reading-progress'         => array( 'landtech-extras-reading-progress' ),
				'ltxe-pricing-table'            => array( 'landtech-extras-pricing-table' ),
				'ltxe-pricing-toggle'           => array( 'landtech-extras-pricing-table' ),
				'ltxe-star-rating'              => array( 'landtech-extras-star-rating' ),
				'ltxe-video-playlist'           => array( 'landtech-extras-video-playlist' ),
				'ltxe-testimonials'             => array( 'landtech-extras-testimonials' ),
				'ltxe-faq'                      => array( 'landtech-extras-faq' ),
				'ltxe-team-members'             => array( 'landtech-extras-team-members' ),
				'ltxe-cta'                      => array( 'landtech-extras-cta' ),
				'ltxe-social-share'             => array( 'landtech-extras-social-share' ),
				'ltxe-back-to-top'              => array( 'landtech-extras-back-to-top' ),
				'ltxe-cookie-consent'           => array( 'landtech-extras-cookie-consent' ),
				'ltxe-sticky-wrapper'           => array( 'landtech-extras-sticky-wrapper' ),
				'ltxe-dark-mode'                => array( 'landtech-extras-dark-mode' ),
			);
			$style_map = array(
				'ee-calendar'           => array( 'landtech-extras-schedule-x-theme' ),
				'ee-tabs'               => array( 'landtech-extras-tabs' ),
				'ltxe-progress-bar'     => array( 'landtech-extras-progress-bar' ),
				'ltxe-countdown'        => array( 'landtech-extras-countdown' ),
				'ltxe-reading-progress' => array( 'landtech-extras-reading-progress' ),
				'ltxe-pricing-table'    => array( 'landtech-extras-pricing-table' ),
				'ltxe-pricing-toggle'   => array( 'landtech-extras-pricing-table' ),
				'ltxe-star-rating'      => array( 'landtech-extras-star-rating' ),
				'ltxe-video-playlist'   => array( 'landtech-extras-video-playlist' ),
				'ltxe-testimonials'     => array( 'landtech-extras-testimonials' ),
				'ltxe-faq'              => array( 'landtech-extras-faq' ),
				'ltxe-team-members'     => array( 'landtech-extras-team-members' ),
				'ltxe-icon-box'         => array( 'landtech-extras-icon-box' ),
				'ltxe-cta'              => array( 'landtech-extras-cta' ),
				'ltxe-social-share'     => array( 'landtech-extras-social-share' ),
				'ltxe-back-to-top'      => array( 'landtech-extras-back-to-top' ),
				'ltxe-cookie-consent'   => array( 'landtech-extras-cookie-consent' ),
				'ltxe-sticky-wrapper'   => array( 'landtech-extras-sticky-wrapper' ),
				'ltxe-dark-mode'        => array( 'landtech-extras-dark-mode' ),
			);
			foreach ( $map as $widget => $widget_handles ) {
				if ( false !== strpos( $raw, $widget ) ) {
					$handles = array_merge( $handles, $widget_handles );
				}
			}
			foreach ( $style_map as $widget => $widget_styles ) {
				if ( false !== strpos( $raw, $widget ) ) {
					$styles = array_merge( $styles, $widget_styles );
				}
			}
		}

		foreach ( array_unique( $handles ) as $handle ) {
			$handle = sanitize_key( (string) $handle );
			if ( 0 === strpos( $handle, 'landtech-extras-' ) ) {
				wp_enqueue_script( $handle );
			}
		}

		$style_handles  = array();
		foreach ( array_unique( $styles ) as $handle ) {
			$handle = sanitize_key( (string) $handle );
			if ( 0 === strpos( $handle, 'landtech-extras-' ) ) {
				wp_enqueue_style( $handle );
				$style_handles[] = $handle;
			}
		}

		if ( doing_action( 'wp_footer' ) ) {
			$print_scripts = array();
			foreach ( array_unique( $handles ) as $handle ) {
				$handle = sanitize_key( (string) $handle );
				if ( 0 === strpos( $handle, 'landtech-extras-' ) ) {
					$print_scripts[] = $handle;
				}
			}
			if ( ! empty( $print_scripts ) ) {
				wp_print_scripts( $print_scripts );
			}
			if ( ! empty( $style_handles ) ) {
				wp_print_styles( $style_handles );
			}
		}
	}

	/**
	 * Enqueue preview scripts
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function enqueue_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts
		wp_register_script(
			'landtech-extras-preview',
			plugins_url( '/assets/js/preview' . $suffix . '.js', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION,
			true );

		wp_enqueue_script( 'landtech-extras-preview' );
	}

	/**
	 * Dependencies for frontend stylesheet handles when Elementor has registered its bundle.
	 *
	 * @since 2.3.1
	 * @return string[]
	 */
	private function get_landtech_extras_frontend_style_dependencies() {
		if ( wp_style_is( 'elementor-frontend', 'registered' ) ) {
			return array( 'elementor-frontend' );
		}

		return array();
	}

	/**
	 * Register shared stylesheet handles (frontend + previews + widget style dependencies).
	 *
	 * @since 2.2.54
	 * @return void
	 */
	public function register_landtech_extras_frontend_style_handles() {

		$suffix            = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$direction_suffix  = is_rtl() ? '-rtl' : '';

		wp_register_style(
			'landtech-extras-glightbox',
			plugins_url( '/assets/lib/glightbox/css/glightbox' . $suffix . '.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			'3.3.1'
		);

		wp_register_style(
			'landtech-extras-splitting',
			plugins_url( '/assets/lib/splitting/splitting.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			'1.0.6'
		);

		// After Elementor core `elementor-frontend` so defaults do not win the cascade; filemtime busts iframe cache.
		$frontend_css_rel = 'assets/css/frontend' . $direction_suffix . $suffix . '.css';
		$frontend_css_abs = LANDTECH_EXTRAS_PATH . $frontend_css_rel;
		$frontend_css_ver = ( is_readable( $frontend_css_abs ) ? (string) filemtime( $frontend_css_abs ) : LANDTECH_EXTRAS_VERSION );
		$frontend_css_deps = $this->get_landtech_extras_frontend_style_dependencies();

		if ( ! wp_style_is( 'landtech-extras-frontend', 'registered' ) ) {
			wp_register_style(
				'landtech-extras-frontend',
				plugins_url( '/' . $frontend_css_rel, LANDTECH_EXTRAS__FILE__ ),
				$frontend_css_deps,
				$frontend_css_ver
			);
		}

		if ( ! wp_style_is( 'landtech-extras-frontend-preview-tail', 'registered' ) ) {
			wp_register_style(
				'landtech-extras-frontend-preview-tail',
				plugins_url( '/' . $frontend_css_rel, LANDTECH_EXTRAS__FILE__ ),
				$frontend_css_deps,
				$frontend_css_ver
			);
		}

		wp_register_style(
			'landtech-extras-nicons',
			LANDTECH_EXTRAS_ASSETS_URL . 'lib/nicons/css/nicons.css',
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_register_style(
			'elementor-select2',
			ELEMENTOR_ASSETS_URL . 'lib/e-select2/css/e-select2' . $suffix . '.css',
			[],
			'4.0.6-rc.1'
		);
	}

	/**
	 * Enqueue preview styles
	 *
	 * @since 1.6.0
	 *
	 * @access public
	 */
	public function enqueue_editor_preview_styles() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$this->register_landtech_extras_frontend_style_handles();

		wp_enqueue_style( 'landtech-extras-nicons' );

		// Register styles
		wp_register_style(
			'landtech-extras-editor-preview',
			plugins_url( '/assets/css/editor-preview' . $suffix . '.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_enqueue_style( 'landtech-extras-glightbox' );
		wp_enqueue_style( 'landtech-extras-editor-preview' );
	}

	/**
	 * Enqueue frontend styles
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function enqueue_frontend_styles() {

		if ( $this->is_elementor_preview_iframe_request() ) {
			/*
			 * Canvas iframe: dequeue default handle then enqueue preview-tail handle last on this hook
			 * so Elementor's preview stylesheet order cannot erase Posts Extra presets.
			 */
			return;
		}

		$this->register_landtech_extras_frontend_style_handles();

		// Enqueue styles
		wp_enqueue_style( 'landtech-extras-nicons' );
		wp_enqueue_style( 'landtech-extras-frontend' );
	}

	/**
	 * Whether this request is the Elementor editor preview iframe (?elementor-preview=).
	 *
	 * @since 2.2.54
	 * @return bool
	 */
	private function is_elementor_preview_iframe_request() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}
		$preview = \Elementor\Plugin::instance()->preview;
		return $preview && method_exists( $preview, 'is_preview_mode' ) && $preview->is_preview_mode();
	}

	/**
	 * Remove queued frontend stylesheet on preview; actual output happens in wp_head tail.
	 *
	 * @since 2.2.54
	 * @return void
	 */
	public function dequeue_extras_frontend_for_preview_tail_style() {
		if ( ! $this->is_elementor_preview_iframe_request() ) {
			return;
		}
		$this->register_landtech_extras_frontend_style_handles();
		wp_dequeue_style( 'landtech-extras-frontend' );
		wp_enqueue_style( 'landtech-extras-frontend-preview-tail' );
	}

	/**
	 * Enqueue editor styles
	 *
	 * Loads `editor.css` for the panel and `frontend.css` so Posts Extra preset skins (and other
	 * widget rules) match the live canvas, including editor modes where preview markup is in-document.
	 *
	 * @since 0.1.0
	 *
	 * @access public
	 */
	public function enqueue_editor_styles() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$this->register_landtech_extras_frontend_style_handles();

		wp_register_style(
			'landtech-extras-editor',
			plugins_url( '/assets/css/editor' . $suffix . '.css', LANDTECH_EXTRAS__FILE__ ),
			[],
			LANDTECH_EXTRAS_VERSION
		);

		wp_enqueue_style( 'landtech-extras-nicons' );
		wp_enqueue_style( 'landtech-extras-frontend' );
		wp_enqueue_style( 'landtech-extras-editor' );
	}

	/**
	 * Include components
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	private function includes() {

		// Utils
		landtech_extras_include( 'includes/utils.php' );
		landtech_extras_include( 'includes/svg/landtech-extras-svg-sanitizer.php' );
		landtech_extras_include( 'includes/svg/landtech-extras-svg-upload.php' );
		landtech_extras_include( 'includes/posts/phase4-layout-policy.php' );
		landtech_extras_include( 'includes/posts/editor-posts-asset-policy.php' );
		landtech_extras_include( 'includes/table/editor-table-asset-policy.php' );

		// ACF → Elementor gallery bridge (requires ACF; see includes/gallery/acf-gallery-bridge.php).
		landtech_extras_include( 'includes/gallery/acf-gallery-bridge.php' );

		// Widget integrations
		landtech_extras_include( 'includes/compatibility/wpml/compatibility.php' );

		// Managers
		landtech_extras_include( 'includes/managers/extensions.php' );
		landtech_extras_include( 'includes/managers/modules.php' );
	}

	/**
	 * Sections init
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	public function init_panel_section() {
		\Elementor\Plugin::instance()->elements_manager->add_category(
			'landtech-extras',
			array( 'title'  => esc_html__( 'LandTech Extras for Elementor', 'landtech-extras-for-elementor' ), ),
			1
		);
	}

	/**
	 * Components init
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	private function init_components() {
		$this->settings 				= new Settings_API();
		$this->modules_manager 			= new Modules_Manager();
		$this->extensions_manager 		= new Extensions_Manager();
		$this->wpml_compatibility 		= new Compatibility\WPML();
	}

	/**
	 * Register Group Controls
	 *
	 * @since 1.1.4
	 *
	 * @access private
	 */
	public function register_group_controls() {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;

		// Include Control Groups
		landtech_extras_include( 'includes/controls/groups/long-shadow.php' );
		landtech_extras_include( 'includes/controls/groups/button-effect.php' );
		landtech_extras_include( 'includes/controls/groups/transition.php' );
		landtech_extras_include( 'includes/controls/groups/tooltip.php' );

		// Add Control Groups
		$controls_manager->add_group_control( 'long-shadow', new Group_Control_Long_Shadow() );
		$controls_manager->add_group_control( 'effect', new Group_Control_Button_Effect() );
		$controls_manager->add_group_control( 'ee-transition', new Group_Control_Transition() );
		$controls_manager->add_group_control( 'ee-tooltip', new Group_Control_Tooltip() );
	}

	/**
	 * Register Controls
	 *
	 * @since 2.0.0
	 *
	 * @access private
	 */
	public function register_controls() {

		// Include Controls
		landtech_extras_include( 'includes/controls/query.php' );
		landtech_extras_include( 'includes/controls/snazzy.php' );

		// Register Controls
		\Elementor\Plugin::instance()->controls_manager->register_control( 'ee-snazzy', new Control_Snazzy() );
		\Elementor\Plugin::instance()->controls_manager->register_control( 'ee-query', new Control_Query() );
	}

	/**
	 * Autoload Classes
	 *
	 * @since 1.6.0
	 */
	public function autoload( $class ) {

		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$has_class_alias = isset( $this->classes_aliases[ $class ] );

		// Backward Compatibility: Save old class name for set an alias after the new class is loaded
		if ( $has_class_alias ) {
			$class_alias_name = $this->classes_aliases[ $class ];
			$class_to_load = $class_alias_name;
		} else {
			$class_to_load = $class;
		}

		if ( ! class_exists( $class_to_load, false ) ) {

			$filename = strtolower(
				preg_replace(
					[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
					[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
					$class_to_load
				)
			);

			$filename = LANDTECH_EXTRAS_PATH . $filename . '.php';

			if ( is_readable( $filename ) ) {
				require_once $filename;
			}
		}

		if ( $has_class_alias ) {
			class_alias( $class_alias_name, $class );
		}
	}

	/**
	 * Check if Woocommerce is installed and active
	 *
	 * @since 1.1.0
	 *
	 * @access public
	 */
	public function is_woocommerce_active() {
		landtech_extras_require_plugin_api();

		return is_plugin_active( 'woocommerce/woocommerce.php' );
	}

	/**
	 * Get a specific link from the links property
	 *
	 * @since 2.2.23
	 *
	 * @access public
	 */
	public function get_link( $name ) {
		if ( ! isset( $this->links[ $name ] ) ) {
			return $this->links['shop'];
		}
		$url = $this->links[ $name ];
		if ( preg_match( '#^(https?://|mailto:)#i', $url ) ) {
			return $url;
		}

		return $this->links['shop'] . ltrim( $url, '/' );
	}

	/**
	 * Get the current plungin version from options
	 *
	 * @since 2.2.38
	 *
	 * @access public
	 */
	public function get_current_version() {
		return get_option('landtech_extras_version', false);
	}

	/**
	 * Get the current plungin version from options
	 *
	 * @since 2.2.38
	 *
	 * @access public
	 */
	public function set_current_version() {
		if ( function_exists( 'landtech_extras_update_option_no_autoload' ) ) {
			landtech_extras_update_option_no_autoload( 'landtech_extras_version', LANDTECH_EXTRAS_VERSION );
			return;
		}

		update_option( 'landtech_extras_version', LANDTECH_EXTRAS_VERSION );
	}
}

if ( ! defined( 'LANDTECH_EXTRAS_TESTS' ) ) {
	// In tests we run the instance manually.
	LandTechExtrasPlugin::instance();
}

