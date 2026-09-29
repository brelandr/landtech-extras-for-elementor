<?php
/**
 * Read-only catalog of the optional Premium add-on (informational upsell).
 *
 * @package LandTechExtras
 */

namespace LandTechExtras\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lists capabilities that ship in LandTech Extras Premium — not as disabled free features.
 */
final class Addon_Catalog {

	/**
	 * Whether the Premium add-on plugin is loaded (activated), regardless of license.
	 *
	 * @return bool
	 */
	public static function add_on_is_active() {
		if ( defined( 'LANDTECH_EXTRAS_PREMIUM_FILE' ) || defined( 'LANDTECH_EXTRAS_PREMIUM_PACKAGE_VERSION' ) ) {
			return true;
		}

		return class_exists( '\LandTechExtras\Feature_Flags_Settings', false );
	}

	/**
	 * Grouped catalog. Keys are display groups; items are label + summary + docs slug.
	 *
	 * @return array<string, array{title:string, items:array<int, array{title:string, summary:string, slug:string}>}>
	 */
	public static function groups() {
		return array(
			'woo'        => array(
				'title' => __( 'WooCommerce & conversion', 'landtech-extras-for-elementor' ),
				'items' => array(
					array( 'title' => __( 'WooCommerce extras', 'landtech-extras-for-elementor' ), 'summary' => __( 'Mini-cart, Quick View, sale badges, and related shop widgets.', 'landtech-extras-for-elementor' ), 'slug' => 'woo-extras-widgets' ),
					array( 'title' => __( 'Conversion widgets', 'landtech-extras-for-elementor' ), 'summary' => __( 'Sticky add-to-cart and free-shipping progress.', 'landtech-extras-for-elementor' ), 'slug' => 'woo-conversion-widgets' ),
					array( 'title' => __( 'Product page builder', 'landtech-extras-for-elementor' ), 'summary' => __( 'Swatches, gallery, tabs, sticky cart, and review schema.', 'landtech-extras-for-elementor' ), 'slug' => 'woo-product-page-builder' ),
					array( 'title' => __( 'Product carousel', 'landtech-extras-for-elementor' ), 'summary' => __( 'Swipeable product rows with optional filters and add to cart.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-product-carousel' ),
					array( 'title' => __( 'Dynamic wishlist', 'landtech-extras-for-elementor' ), 'summary' => __( 'Save any widget item; EE Saved Items list for visitors.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-wishlist' ),
					array( 'title' => __( '360 product viewer', 'landtech-extras-for-elementor' ), 'summary' => __( 'Drag an image sequence to spin a product.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-360-viewer' ),
					array( 'title' => __( '3D product viewer', 'landtech-extras-for-elementor' ), 'summary' => __( 'Interactive GLB/GLTF models with hotspots.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-3d-viewer' ),
					array( 'title' => __( 'Smart countdown', 'landtech-extras-for-elementor' ), 'summary' => __( 'Evergreen, stock, sale, and remote timers.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-smart-countdown' ),
				),
			),
			'forms'      => array(
				'title' => __( 'Forms, popups & menus', 'landtech-extras-for-elementor' ),
				'items' => array(
					array( 'title' => __( 'Multi-step forms', 'landtech-extras-for-elementor' ), 'summary' => __( 'Steps, email, webhook, and database submissions.', 'landtech-extras-for-elementor' ), 'slug' => 'forms-pro-multistep' ),
					array( 'title' => __( 'Smart popup triggers', 'landtech-extras-for-elementor' ), 'summary' => __( 'Scroll, time-on-page, cart value, and conditions.', 'landtech-extras-for-elementor' ), 'slug' => 'smart-popup-triggers' ),
					array( 'title' => __( 'Popup A/B testing', 'landtech-extras-for-elementor' ), 'summary' => __( 'Variants and conversion tracking for popups.', 'landtech-extras-for-elementor' ), 'slug' => 'popup-ab-testing' ),
					array( 'title' => __( 'Mega menu builder', 'landtech-extras-for-elementor' ), 'summary' => __( 'Multi-column menus with Elementor template zones.', 'landtech-extras-for-elementor' ), 'slug' => 'mega-menu-builder' ),
					array( 'title' => __( 'Off-canvas suite', 'landtech-extras-for-elementor' ), 'summary' => __( 'Global triggers, focus trap, Woo and search drawers.', 'landtech-extras-for-elementor' ), 'slug' => 'premium-offcanvas-suite' ),
					array( 'title' => __( 'Membership conditions', 'landtech-extras-for-elementor' ), 'summary' => __( 'WooCommerce, MemberPress, PMPro, and Woo Memberships.', 'landtech-extras-for-elementor' ), 'slug' => 'membership-display-conditions' ),
					array( 'title' => __( 'Gravity Forms styler', 'landtech-extras-for-elementor' ), 'summary' => __( 'Style labels, fields, buttons, and confirmations.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-gf-styler' ),
					array( 'title' => __( 'Booking calendar', 'landtech-extras-for-elementor' ), 'summary' => __( 'On-site slots, guest form, and confirmation email.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-booking' ),
					array( 'title' => __( 'Login & register', 'landtech-extras-for-elementor' ), 'summary' => __( 'Frontend forms that stay on the page — no wp-login.php.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-auth-forms' ),
					array( 'title' => __( 'Marketing button', 'landtech-extras-for-elementor' ), 'summary' => __( 'CTA with optional countdown, proof count, and pulse.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-marketing-button' ),
					array( 'title' => __( 'Business hours', 'landtech-extras-for-elementor' ), 'summary' => __( 'Weekly schedule with a live Open / Closed badge.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-business-hours' ),
					array( 'title' => __( 'Calculator builder', 'landtech-extras-for-elementor' ), 'summary' => __( 'Quotes, ROI, and loan math without evaluating PHP.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-calculator' ),
					array( 'title' => __( 'Experience builder', 'landtech-extras-for-elementor' ), 'summary' => __( 'Quizzes and product finders with scoring and results.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-experience-builder' ),
				),
			),
			'dynamic'    => array(
				'title' => __( 'Dynamic data & loops', 'landtech-extras-for-elementor' ),
				'items' => array(
					array( 'title' => __( 'Remote content', 'landtech-extras-for-elementor' ), 'summary' => __( 'JSON from allow-listed hosts, mapped into a widget.', 'landtech-extras-for-elementor' ), 'slug' => 'dynamic-remote-widget' ),
					array( 'title' => __( 'ACF gallery bridge', 'landtech-extras-for-elementor' ), 'summary' => __( 'Feed Gallery widgets from an ACF Gallery field.', 'landtech-extras-for-elementor' ), 'slug' => 'acf-gallery-bridge-widget' ),
					array( 'title' => __( 'AJAX loops & facets', 'landtech-extras-for-elementor' ), 'summary' => __( 'Filter Posts Extra without a full page reload.', 'landtech-extras-for-elementor' ), 'slug' => 'loop-facets-ajax' ),
					array( 'title' => __( 'Advanced loop query', 'landtech-extras-for-elementor' ), 'summary' => __( 'Include/exclude IDs, authors, meta and taxonomy clauses.', 'landtech-extras-for-elementor' ), 'slug' => 'advanced-loop-query' ),
					array( 'title' => __( 'Masonry & metro layouts', 'landtech-extras-for-elementor' ), 'summary' => __( 'Isotope masonry, Metro, and Packery tile loops.', 'landtech-extras-for-elementor' ), 'slug' => 'loop-advanced-layouts' ),
					array( 'title' => __( 'Live data table', 'landtech-extras-for-elementor' ), 'summary' => __( 'Sortable tables via a server-side fetch proxy.', 'landtech-extras-for-elementor' ), 'slug' => 'live-data-table' ),
					array( 'title' => __( 'External grid', 'landtech-extras-for-elementor' ), 'summary' => __( 'Allow-listed JSON endpoints with saved field maps.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-external-grid-connector' ),
					array( 'title' => __( 'Real-time data binding', 'landtech-extras-for-elementor' ), 'summary' => __( 'Live values on any widget from options, Woo, or JSON.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-realtime-binding' ),
					array( 'title' => __( 'Social feeds', 'landtech-extras-for-elementor' ), 'summary' => __( 'Instagram, X, and TikTok feeds cached on your site.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-social-feeds' ),
					array( 'title' => __( 'Interactivity API facets', 'landtech-extras-for-elementor' ), 'summary' => __( 'Marks facet markup for a future core hydration path.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-interactivity-facets' ),
					array( 'title' => __( 'Sync-ready meta', 'landtech-extras-for-elementor' ), 'summary' => __( 'Incremental REST exposure for plugin-owned fields.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-sync-ready-meta' ),
					array( 'title' => __( 'Edge personalization', 'landtech-extras-for-elementor' ), 'summary' => __( 'Signed fragment endpoint for CDN or worker wiring.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-edge-personalization' ),
				),
			),
			'motion'     => array(
				'title' => __( 'Motion & design', 'landtech-extras-for-elementor' ),
				'items' => array(
					array( 'title' => __( 'Motion Lab', 'landtech-extras-for-elementor' ), 'summary' => __( 'Glass and backdrop motion controls.', 'landtech-extras-for-elementor' ), 'slug' => 'premium-motion-lab' ),
					array( 'title' => __( 'Advanced tabs', 'landtech-extras-for-elementor' ), 'summary' => __( 'Vertical tabs, accordion-on-mobile, icons, transitions.', 'landtech-extras-for-elementor' ), 'slug' => 'premium-tabs-widget' ),
					array( 'title' => __( 'Advanced table of contents', 'landtech-extras-for-elementor' ), 'summary' => __( 'Sticky sidebar, scroll progress, active heading.', 'landtech-extras-for-elementor' ), 'slug' => 'premium-toc-widget' ),
					array( 'title' => __( 'Advanced image', 'landtech-extras-for-elementor' ), 'summary' => __( 'Next-gen formats with picture/srcset fallbacks.', 'landtech-extras-for-elementor' ), 'slug' => 'premium-wasm-image-pipeline' ),
					array( 'title' => __( 'Scroll storytelling', 'landtech-extras-for-elementor' ), 'summary' => __( 'Snap, horizontal scroll, reveal, and parallax story.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-scroll-story' ),
					array( 'title' => __( 'V4 atomic widgets', 'landtech-extras-for-elementor' ), 'summary' => __( 'Accordion, gallery, carousel, tabs, and icon list for V4.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-atomic-bridge' ),
					array( 'title' => __( 'Cursor & interaction effects', 'landtech-extras-for-elementor' ), 'summary' => __( 'Custom cursor, magnetic, tilt, spotlight, and reveal.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-cursor-effects' ),
					array( 'title' => __( 'Text animation studio', 'landtech-extras-for-elementor' ), 'summary' => __( 'Animated heading and text reveal presets.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-text-animation' ),
					array( 'title' => __( 'Dot nav', 'landtech-extras-for-elementor' ), 'summary' => __( 'Fixed one-page dots that track section IDs.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-dot-nav' ),
					array( 'title' => __( 'Conditional CSS classes', 'landtech-extras-for-elementor' ), 'summary' => __( 'Add classes when login, role, device, or Woo rules match.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-conditional-classes' ),
					array( 'title' => __( 'Personalization UI', 'landtech-extras-for-elementor' ), 'summary' => __( 'Show or hide a widget by country, device, time, or role.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-personalization-ui' ),
					array( 'title' => __( 'Viewport condition', 'landtech-extras-for-elementor' ), 'summary' => __( 'Display condition aligned with core breakpoints.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-viewport-visibility' ),
				),
			),
			'ai'         => array(
				'title' => __( 'AI, SEO & accessibility', 'landtech-extras-for-elementor' ),
				'items' => array(
					array( 'title' => __( 'AI Studio (BYOK)', 'landtech-extras-for-elementor' ), 'summary' => __( 'Optional provider keys when Connectors are unavailable.', 'landtech-extras-for-elementor' ), 'slug' => 'ai-workspace' ),
					array( 'title' => __( 'AI site guidelines', 'landtech-extras-for-elementor' ), 'summary' => __( 'Inject WordPress site Guidelines into Genius prompts.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-ai-guidelines' ),
					array( 'title' => __( 'Genius assistant', 'landtech-extras-for-elementor' ), 'summary' => __( 'Ask, generate, brand voice, and schema from the editor.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-genius' ),
					array( 'title' => __( 'AI content generator', 'landtech-extras-for-elementor' ), 'summary' => __( 'Write widget field copy from the Elementor editor.', 'landtech-extras-for-elementor' ), 'slug' => 'ai-content-generator' ),
					array( 'title' => __( 'AI section generator', 'landtech-extras-for-elementor' ), 'summary' => __( 'Describe a section and place it on the canvas.', 'landtech-extras-for-elementor' ), 'slug' => 'ai-section-generator' ),
					array( 'title' => __( 'AI experiments', 'landtech-extras-for-elementor' ), 'summary' => __( 'Opt-in loop-editor LLM output for audits.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-ai-experiments' ),
					array( 'title' => __( 'AI safe mode', 'landtech-extras-for-elementor' ), 'summary' => __( 'Stop all LandTech AI completions site-wide.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-ai-safe-mode' ),
					array( 'title' => __( 'Semantic search', 'landtech-extras-for-elementor' ), 'summary' => __( 'Rank posts by embedding similarity, not only keywords.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-semantic-search' ),
					array( 'title' => __( 'Voice search', 'landtech-extras-for-elementor' ), 'summary' => __( 'Speak or type; optional semantic ranking.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-voice-search' ),
					array( 'title' => __( 'Alt text batch', 'landtech-extras-for-elementor' ), 'summary' => __( 'Queue Media Library suggestions; you approve before save.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-alt-text-batch' ),
					array( 'title' => __( 'Schema.org SEO suite', 'landtech-extras-for-elementor' ), 'summary' => __( 'LocalBusiness, Product, Review, HowTo, plus a dashboard.', 'landtech-extras-for-elementor' ), 'slug' => 'schema-suite-premium' ),
					array( 'title' => __( 'WCAG accessibility scanner', 'landtech-extras-for-elementor' ), 'summary' => __( 'Editor panel with one-click fixes and page scores.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-accessibility-scanner' ),
					array( 'title' => __( 'Abilities API registry', 'landtech-extras-for-elementor' ), 'summary' => __( 'Capability-checked abilities for WordPress 6.9+ agents.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-abilities-api' ),
				),
			),
			'agency'     => array(
				'title' => __( 'Agency & platform tools', 'landtech-extras-for-elementor' ),
				'items' => array(
					array( 'title' => __( 'Header / footer builder', 'landtech-extras-for-elementor' ), 'summary' => __( 'Elementor chrome with display rules, no Theme Builder required.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-hfb' ),
					array( 'title' => __( 'White-label agency mode', 'landtech-extras-for-elementor' ), 'summary' => __( 'Rebrand admin UI and hide the license from non-agency users.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-white-label' ),
					array( 'title' => __( 'Design system sync', 'landtech-extras-for-elementor' ), 'summary' => __( 'Move kit colors and typography between sites via tokens.', 'landtech-extras-for-elementor' ), 'slug' => 'design-system-sync' ),
					array( 'title' => __( 'Export package', 'landtech-extras-for-elementor' ), 'summary' => __( 'Pack selected pages, media, and optional kit settings.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-export-package' ),
					array( 'title' => __( 'CPT builder', 'landtech-extras-for-elementor' ), 'summary' => __( 'Create custom post types and taxonomies in admin.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-cpt-builder' ),
					array( 'title' => __( 'Content protection', 'landtech-extras-for-elementor' ), 'summary' => __( 'Password-lock a single widget on an otherwise public page.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-content-protection' ),
					array( 'title' => __( 'Template marketplace', 'landtech-extras-for-elementor' ), 'summary' => __( 'Insert bundled starter sections from the editor.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-template-marketplace' ),
					array( 'title' => __( 'Time machine', 'landtech-extras-for-elementor' ), 'summary' => __( 'Named Elementor page history with restore and compare.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-time-machine' ),
					array( 'title' => __( 'Visual regression guard', 'landtech-extras-for-elementor' ), 'summary' => __( 'Compare the preview after save; optional rollback.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-visual-regression' ),
					array( 'title' => __( 'Smart asset compiler', 'landtech-extras-for-elementor' ), 'summary' => __( 'One CSS and JS file per page from widgets actually used.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-asset-compiler' ),
					array( 'title' => __( 'Client feedback', 'landtech-extras-for-elementor' ), 'summary' => __( 'Share a review link; clients pin comments without logging in.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-client-feedback' ),
					array( 'title' => __( 'Design review', 'landtech-extras-for-elementor' ), 'summary' => __( 'Pinned canvas comments for logged-in editors.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-design-review' ),
					array( 'title' => __( 'Performance intelligence', 'landtech-extras-for-elementor' ), 'summary' => __( 'Widget weight, DOM, and CWV panel — measurement only.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-performance-intelligence' ),
					array( 'title' => __( 'Layout watchdog', 'landtech-extras-for-elementor' ), 'summary' => __( 'CLS and broken-image telemetry in admin.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-layout-watchdog' ),
					array( 'title' => __( 'PWA builder', 'landtech-extras-for-elementor' ), 'summary' => __( 'Installable app, offline page, and optional push.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-pwa-builder' ),
					array( 'title' => __( 'Command palette shortcuts', 'landtech-extras-for-elementor' ), 'summary' => __( 'LandTech commands in the WordPress admin palette.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-admin-command-palette' ),
					array( 'title' => __( 'Onboarding agent', 'landtech-extras-for-elementor' ), 'summary' => __( 'First-run recommendations you apply or undo yourself.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-onboarding-agent' ),
					array( 'title' => __( 'Evolutionary layouts', 'landtech-extras-for-elementor' ), 'summary' => __( 'Cookie A/B assignment with local stats — no auto-winner.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-evolutionary-layouts' ),
					array( 'title' => __( 'DataViews admin', 'landtech-extras-for-elementor' ), 'summary' => __( 'Searchable lanes table on WordPress 7.0+.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-dataviews-admin' ),
					array( 'title' => __( 'Telemetry (opt-in)', 'landtech-extras-for-elementor' ), 'summary' => __( 'Local snapshots of enabled features — default off, no PII.', 'landtech-extras-for-elementor' ), 'slug' => 'platform-telemetry-opt-in' ),
					array( 'title' => __( 'Social proof hub', 'landtech-extras-for-elementor' ), 'summary' => __( 'Google and Trustpilot review widgets (your API keys).', 'landtech-extras-for-elementor' ), 'slug' => 'social-proof-hub' ),
				),
			),
		);
	}

	/**
	 * HTML for the Add-on features tab when the add-on is not active.
	 *
	 * @return string
	 */
	public static function render() {
		if ( self::add_on_is_active() ) {
			return '';
		}

		$docs   = 'https://extrasforelementor.com/docs/premium/';
		$buy    = 'https://landtechwebdesigns.com/product/extras-for-elementor/';
		$guides = 'https://extrasforelementor.com/docs/getting-started/';
		$count  = 0;
		foreach ( self::groups() as $group ) {
			$count += count( $group['items'] );
		}

		$html  = '<div class="ltxe-addon-catalog">';
		$html .= '<div class="ltxe-addon-catalog__hero">';
		$html .= '<p class="ltxe-addon-catalog__badge">' . esc_html__( 'Optional add-on', 'landtech-extras-for-elementor' ) . '</p>';
		$html .= '<h2>' . esc_html__( 'LandTech Extras Premium', 'landtech-extras-for-elementor' ) . '</h2>';
		$html .= '<p class="ltxe-addon-catalog__lead">' . esc_html(
			sprintf(
				/* translators: %d: number of add-on capabilities listed. */
				__( 'This WordPress.org plugin is complete on its own. The optional Premium add-on is a separate plugin that adds %d extra capabilities. Nothing below is included here as a disabled feature.', 'landtech-extras-for-elementor' ),
				$count
			)
		) . '</p>';
		$html .= '<p class="ltxe-addon-catalog__actions">';
		$html .= '<a class="button button-primary" href="' . esc_url( $buy ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'View the Premium add-on', 'landtech-extras-for-elementor' ) . '</a> ';
		$html .= '<a class="button" href="' . esc_url( $docs ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Read add-on documentation', 'landtech-extras-for-elementor' ) . '</a> ';
		$html .= '<a class="button" href="' . esc_url( $guides ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'How to install it', 'landtech-extras-for-elementor' ) . '</a>';
		$html .= '</p>';
		$html .= '</div>';

		foreach ( self::groups() as $group ) {
			$html .= '<div class="ltxe-addon-catalog__group">';
			$html .= '<h3>' . esc_html( $group['title'] ) . '</h3>';
			$html .= '<ul class="ltxe-addon-catalog__grid">';
			foreach ( $group['items'] as $item ) {
				$url   = $docs . rawurlencode( $item['slug'] ) . '/';
				$html .= '<li class="ltxe-addon-catalog__card">';
				$html .= '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">';
				$html .= '<strong>' . esc_html( $item['title'] ) . '</strong>';
				$html .= '<span>' . esc_html( $item['summary'] ) . '</span>';
				$html .= '</a></li>';
			}
			$html .= '</ul></div>';
		}

		$html .= '<p class="ltxe-addon-catalog__foot">' . esc_html__( 'After you install and activate the add-on alongside this plugin, this tab becomes the live feature list with Enable checkboxes. License and updates are handled by Land Tech Web Designs.', 'landtech-extras-for-elementor' ) . '</p>';
		$html .= '</div>';

		return $html;
	}
}
