=== LandTech Extras for Elementor ===

Contributors: brelandr
Tags: elementor, page-builder, widgets, addons, extensions
Requires at least: 6.2
Tested up to: 7.0
Stable tag: 2.3.0
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Elementor widgets and extensions — GPL fork of Elementor Extras. Gallery, calendar, maps, posts, navigation, and editor tools.

== Description ==

**Disclaimer:** Independent fork of the original **Elementor Extras** plugin (also known as **Extras for Elementor**, by Namogo). Not affiliated with or endorsed by Namogo or Elementor Ltd. Original credits: Namogo (Elementor Extras). Maintained by Land Tech Web Designs.

**LandTech Extras for Elementor** is a free, actively maintained replacement for sites that relied on Elementor Extras / Extras for Elementor. It adds creative **Elementor widgets** (gallery, calendar, Google Map, posts grids, navigation, popups, and more) and **editor extensions** (display conditions, parallax, sticky elements, tooltips) on top of Elementor. Existing Elementor templates that used Elementor Extras widget slugs (for example `ee-calendar`, `posts-extra`, `ee-gallery`) continue to work after you switch to this plugin.

Configure optional API keys (Google Maps, Snazzy Maps, Instagram, and optional bring-your-own-key LLM credentials when using the separate **LandTech Extras add-on** AI workspace) under **Elementor → LandTech Extras → APIs** when needed.

== Widgets and extensions ==

Find widgets in the Elementor panel under the **LandTech Extras for Elementor** category. Widget type slugs match the upstream Elementor Extras fork for easier migration.

**Widgets:** Age Gate (`ee-age-gate`), Audio Player (`ee-audio-player`), Breadcrumbs (`ee-breadcrumbs`, optional **BreadcrumbList** microdata when **Structured data** is enabled), Buttons / Button Group (`button-group`), Calendar (`ee-calendar`), Circle Progress (`circle-progress`), Devices (`devices-extended`), FAQ Schema (`ee-faq-schema`), Gallery (`gallery-extra`), Gallery Slider (`gallery-slider`), Google Map (`ee-google-map`), Heading Extra (`heading-extended`), Hotspots (`hotspots`), HTML5 Video Player (`html5-video`), Image Comparison (`image-comparison`), Inline SVG (`ee-inline-svg`), Lottie (`ee-lottie`), Offcanvas (`ee-offcanvas`), Popup (`ee-popup`), Posts Extra / Portfolio / Carousel layouts (`posts-extra`), Random Image (`ee-random-image`), Scroll Indicator (`ee-scroll-indicator`), Search Form (`ee-search-form`), Slide Menu (`ee-slide-menu`), Switcher (`ee-switcher`), Table (`table`), Text Divider (`text-divider`), Timeline (`timeline`), Toggle Element (`ee-toggle-element`), Unfold (`unfold`).

**Editor extensions** (Advanced tab on Elementor elements): Display Conditions (free: time, day, URL/query vars including UTM presets, cookie, business hours; WooCommerce purchase history and membership rules ship in the separate **LandTech Extras Premium** add-on), Parallax Background, Parallax Elements, Portfolio Parallax, Sticky Elements, Tooltips.

Disable unused widgets under **Elementor → LandTech Extras → Widgets** to speed up the editor.

== Try It Live - Preview This Plugin Instantly ==

Experience LandTech Extras for Elementor without installation: the blueprint installs **Elementor** and this plugin from **WordPress.org**, seeds **sample images and blog posts**, builds a **demo homepage** with links to every widget, creates a **dedicated demo page per feature** (gallery, search form, posts grid, maps, navigation widgets, and more), and registers a **navigation menu** grouping all demos. Log in as **admin** / **password** to edit pages with Elementor or open **Elementor → LandTech Extras** settings.

[Preview on WordPress Playground](https://playground.wordpress.net/?blueprint-url=https://plugins.svn.wordpress.org/landtech-extras-for-elementor/assets/blueprints/blueprint.json)

The same blueprint ships in the plugin package as `blueprint.json` (repository root) and `assets/blueprints/blueprint.json`. WordPress.org serves the public copy from **plugin SVN** at `assets/blueprints/blueprint.json` for directory Live Preview integration.

== External Services ==

This plugin may cause the site, the WordPress admin, or the Elementor editor to contact third-party services when you enable the related features or save API credentials. For each service below, direct links to its **Terms of Service** (or equivalent) and **Privacy Policy** are provided.

**Google Maps Platform (Google LLC)**

When you enable the Google Map widget or turn on **Load Google Maps API** under Elementor → LandTech Extras → APIs, visitors’ browsers may load the Maps JavaScript API from Google (`maps.googleapis.com`, including via bundled `assets/lib/gmap3/gmap3.js`). Requests can include the **Maps API key** you enter in settings, map coordinates or place queries you configure, and typical browser HTTP metadata. [Terms of Service](https://cloud.google.com/maps-platform/terms) · [Privacy Policy](https://policies.google.com/privacy)

**OpenStreetMap tile servers (OpenStreetMap Foundation and community tile providers)**

When you set the Google Map widget **Map provider** to **OpenStreetMap**, visitors’ browsers load bundled **Leaflet** (`assets/lib/leaflet/`) and request raster map tiles from OpenStreetMap’s tile service (`tile.openstreetmap.org`, including subdomains `a`–`c`). Requests can include map tile coordinates (zoom level and x/y indices), the visitor’s IP address, and standard browser HTTP metadata. Marker icon assets are served from your site; no API key is required for the default tile layer. [OpenStreetMap Foundation Terms](https://wiki.osmfoundation.org/wiki/Terms_of_Use) · [OpenStreetMap Foundation Privacy Policy](https://wiki.osmfoundation.org/wiki/Privacy_Policy) · [OpenStreetMap copyright](https://www.openstreetmap.org/copyright)

**Snazzy Maps (snazzymaps.com; operated by Atlist Inc.)**

When you add a Snazzy Maps API key and search for map styles in the Elementor editor, the editor script (`assets/js/editor.js`) requests JSON from `https://snazzymaps.com/{endpoint}.json` (explore, my-styles, or favorites). Those requests can include your **Snazzy Maps API key**, pagination parameters, and standard browser HTTP metadata. Server-side PHP does not proxy these editor requests. Atlist publishes the legal terms that apply to Snazzy Maps / Atlist services: [Terms of Service](https://www.atlist.com/terms) · [Privacy Policy](https://www.atlist.com/privacy)

**Instagram (Meta Platforms, Inc.)**

When you use Instagram-related options (Gallery widget Instagram source or an access token under Elementor → LandTech Extras → APIs), the plugin may request data from **Instagram Graph API** (`graph.instagram.com`, including token refresh in admin) and/or **instagram.com** endpoints used by the Gallery widget. Data sent can include the **access token** you configure, API parameters required by Instagram, and standard HTTP metadata. [Instagram Terms of Service](https://www.instagram.com/legal/terms/) · [Instagram Privacy Policy](https://privacycenter.instagram.com/policy/) · [Meta Privacy Policy](https://www.facebook.com/privacy/policy/) · Meta **Developer Terms** (API use): [Terms](https://developers.facebook.com/terms/)

**OpenAI (OpenAI, LLC) — optional bring-your-own-key (separate LandTech Extras add-on AI workspace)**

This WordPress.org build may store optional API credentials in the `landtech_extras_apis` option when you save settings, but **does not** call OpenAI from this plugin alone. When the separate **LandTech Extras add-on** is active, AI workspace is enabled, and you choose **OpenAI**, that add-on may send HTTPS requests to **OpenAI’s API** (e.g. `api.openai.com`) with the **API key**, **prompt or content**, model parameters, and standard HTTP metadata. [Terms of Service](https://openai.com/policies/terms-of-use/) · [Privacy Policy](https://openai.com/policies/privacy-policy/)

**Anthropic (Anthropic, PBC) — optional bring-your-own-key (add-on AI workspace)**

Same as OpenAI above: credentials may be stored here; outbound **Anthropic API** requests (e.g. `api.anthropic.com`) occur only when the separate add-on AI workspace invokes them with your saved **Anthropic API key**, messages, and parameters. [Commercial Terms of Service](https://www.anthropic.com/legal/commercial-terms) · [Privacy Policy](https://www.anthropic.com/legal/privacy)

**Google Gemini API (Google LLC) — optional bring-your-own-key (add-on AI workspace)**

Same as OpenAI above: credentials may be stored here; outbound **Gemini** requests (e.g. `generativelanguage.googleapis.com`) occur only when the separate add-on AI workspace invokes them with your saved **Gemini API key**, prompts, and parameters. [Google AI / Gemini API Terms](https://ai.google.dev/gemini-api/terms) · [Google Privacy Policy](https://policies.google.com/privacy)

**User-provided Lottie animation URLs (LottieFiles or other hosts)**

When the Lottie widget is set to load an animation from an **External URL** (or from a JSON file in your media library served from another origin), visitors’ browsers request that JSON from the URL you configure. The Playground demo may load a sample file from **LottieFiles** (`assets*.lottiefiles.com`) when you preview the bundled demo page. No animation URL is transmitted to Land Tech servers. Review your animation host’s terms and privacy policy before publishing.

== Screenshots ==

1. Enable or disable individual LandTech Extras widgets from Elementor → LandTech Extras.
2. Editor extensions such as display conditions, sticky elements, parallax, and tooltips.
3. Optional API keys for Google Maps, Snazzy Maps, Instagram, and BYOK LLM credentials.
4. LandTech Extras widgets appear in the Elementor panel under the LandTech Extras category.

== Installation ==

1. Install and activate **Elementor**.
2. Install and activate **LandTech Extras for Elementor** from this directory or from WordPress.org once published.
3. Edit pages with Elementor and use **LandTech Extras for Elementor** widgets and extensions.

== Frequently Asked Questions ==

= Does this plugin require Elementor? =

Yes. LandTech Extras for Elementor requires the free [Elementor](https://wordpress.org/plugins/elementor/) page builder to be installed and active.

= Which Elementor version is required? =

Elementor 3.5.0 or later. Compatibility is documented in the plugin header (`Elementor tested up to`).

= Can I use this without Elementor Pro? =

Yes. All free widgets and extensions work without Elementor Pro. Some widgets note when Elementor Pro is required.

= How do I disable widgets I don't use? =

Go to **Elementor → LandTech Extras** in your WordPress admin and uncheck any widgets or extensions you don't plan to use. This improves Elementor editor load time.

= How do I get an API key for Google Maps? =

Follow the [Google Maps API key guide](https://developers.google.com/maps/documentation/javascript/get-api-key) and enter your key under **Elementor → LandTech Extras → APIs**.

= Is this plugin affiliated with Namogo or Elementor Ltd.? =

No. This is an independent fork of the original Elementor Extras plugin by Namogo, maintained by Land Tech Web Designs. It is not affiliated with or endorsed by Namogo or Elementor Ltd.

= Is this a replacement for Elementor Extras / Extras for Elementor? =

Yes. LandTech Extras for Elementor is an independent, GPLv3-maintained continuation of the Elementor Extras (Extras for Elementor) widget set. It is intended for sites that need those Elementor widgets and extensions without relying on the discontinued original distribution. Widget type slugs (`ee-calendar`, `posts-extra`, and similar) are preserved so existing Elementor JSON templates can load after you deactivate the old plugin and activate LandTech Extras.

= Can I migrate from Elementor Extras or Namogo Extras? =

In most cases, yes. Export your Elementor templates, replace the old plugin with **LandTech Extras for Elementor**, and re-open pages in Elementor. Widgets should resolve under the LandTech Extras category. Re-save pages if Elementor prompts you to update data. Compare your widget list with the **Widgets and extensions** section above; optional API keys (Maps, Instagram) must be re-entered under **Elementor → LandTech Extras → APIs**.

= Does this plugin include Gutenberg blocks? =

No. LandTech Extras adds **Elementor** widgets and Elementor editor extensions only. Search terms like “Elementor blocks” usually refer to Elementor widgets or sections; this plugin does not register WordPress block editor blocks.

= What prefix does this plugin use in code? =

PHP functions, hooks, options, and transients use the **`landtech_extras_`** or **`ltxe_`** prefix (four or more characters). Script/style handles use **`landtech-extras-`**. Namespaced PHP classes live under **`LandTechExtras\`**. Elementor widget type slugs (for example `ee-calendar` or `posts-extra`) are retained from the upstream Elementor Extras fork so existing Elementor templates keep working; they are Elementor element identifiers, not WordPress options or hooks.

== Third-party libraries ==

This plugin bundles or references third-party scripts. Licenses and upstream sources for every bundled file are noted below. Full provenance details are in the matching `README.txt` files under `assets/lib/`.

**anime.js 4.0.2** (MIT) — `assets/lib/anime/anime.js` / `anime.min.js`
Source: https://github.com/juliangarnier/anime — License notes: `assets/lib/anime/README.txt`.

**Splitting.js 1.0.6** (MIT) — `assets/lib/splitting/splitting.js` / `splitting.min.js` / `splitting.css`
Source: https://github.com/shshaw/Splitting — License notes: `assets/lib/splitting/README.txt`.

**GLightbox 3.3.1** (MIT) — `assets/lib/glightbox/js/glightbox.js` / `glightbox.min.js` and CSS under `assets/lib/glightbox/css/`
Source: https://github.com/biati-digital/glightbox — License notes: `assets/lib/glightbox/README.txt`.

**jquery-circle-progress** (MIT) — https://github.com/kottenator/jquery-circle-progress
**jquery.appear** (MIT) — https://github.com/morr/jquery.appear/
**LongShadow jQuery Plugin** (MIT) — https://github.com/dangvanthanh/jquery.longShadow
**HC-Sticky** (MIT) — https://github.com/somewebmedia/hc-sticky
**jQuery Mobile** (MIT/jquery.org) — https://jquerymobile.com/
**jquery-visible** (MIT) — http://teamdf.com/jquery-plugins/license/
**Parallax Background** (MIT) — https://github.com/erensuleymanoglu/parallax-background
**TableSorter** v2.32.0 (MIT/GPL dual, Mottie fork) — https://github.com/Mottie/tablesorter
**Isotope v3.0.6** (GPLv3) — `assets/lib/isotope/` standalone modules (WordPress core jQuery via jquery-bridget)
**Metafizzy / Desandro layout dependencies** (MIT unless noted) — `assets/lib/jquery-bridget/`, `outlayer/`, `ev-emitter/`, `get-size/`, `fizzy-ui-utils/`, `matches-selector/`, `masonry-layout/` (each folder includes `README.txt`)
**Packery v2.1.2** (GPLv3) — `assets/lib/packery/` standalone modules (WordPress core jQuery via jquery-bridget)
**Infinite Scroll 4.0.1** (GPLv3) — `assets/lib/infinite-scroll/infinite-scroll.js` (core build; vanilla DOM API via `new InfiniteScroll()`; no jquery-bridget)
Source: https://infinite-scroll.com — License notes: `assets/lib/infinite-scroll/README.txt`.
**javascript-detect-element-resize** (MIT) — https://github.com/sdecima/javascript-detect-element-resize
**tilt.js** (MIT) — https://github.com/gijsroge/tilt.js
**CLNDR** (MIT) — legacy fallback in `assets/lib/clndr/` (Calendar widget now uses Schedule-X)
**Schedule-X Calendar 4.6.1** (MIT) — `assets/lib/schedule-x/` with Preact under `assets/lib/preact/` and `@js-temporal/polyfill` under `assets/lib/temporal-polyfill/`
Source: https://github.com/schedule-x/schedule-x — License notes: `assets/lib/schedule-x/README.txt`.
**@lottiefiles/lottie-player** (MIT) — `assets/lib/lottie-player/lottie-player.js`
Source: https://github.com/LottieFiles/lottie-player — License notes: `assets/lib/lottie-player/README.txt`.
**WaveSurfer.js 7.9.9** (BSD-3-Clause) — optional Audio Player waveform skin
Source: https://github.com/kwavesurfer/wavesurfer.js — License notes: `assets/lib/wavesurfer/README.txt`.
**GMAP3** (GPL-3.0+) — http://gmap3.net
**Leaflet** v1.9.4 (BSD-2-Clause) — `assets/lib/leaflet/leaflet.js` / `leaflet.css` (OpenStreetMap map provider)
Source: https://github.com/Leaflet/Leaflet — License notes: `assets/lib/leaflet/README.txt`.
**Slidebars** (MIT) — http://www.adchsm.com/slidebars/

Date/time formatting in the Calendar widget uses **Moment.js** registered by WordPress core when available.

== Development ==

Human-readable source for bundled/minified assets (included in this plugin package):

* `assets/css/frontend.min.css` and `assets/css/frontend-rtl.min.css` — built from `assets/css/frontend.css` and `assets/css/frontend-rtl.css`.
* `assets/js/frontend.min.js` — built from `assets/js/frontend.js`.
* `assets/js/landtech-extras-calendar-schedule-x.js` and `assets/js/table-csv-editor.js` — widget helpers (not minified; loaded directly when needed).
* `assets/js/admin.min.js`, `assets/js/editor.min.js`, and `assets/js/notice.min.js` — built from their matching non-minified files in `assets/js/`.
* `assets/css/admin.min.css`, `assets/css/editor.min.css`, and `assets/css/editor-preview.min.css` — built from matching non-minified CSS in `assets/css/`.
* Widget/library `.min.js` / `.min.css` under `assets/lib/` — non-minified counterparts ship in the same folder where applicable; third-party-only bundles are documented under **Third-party libraries** above and in the per-library `README.txt` files under `assets/lib/`.
* `assets/lib/landtech-extras-anime-helpers.js` — TweenMax-compatible animation shim using bundled anime.js (MIT).

From the plugin root, after **`npm install`**, run **`npm run build:assets`** to regenerate the minified frontend CSS/JS files listed above (used by default enqueues when `SCRIPT_DEBUG` is false).

Before publishing a public GitHub mirror, verify any **Repository** or **Source** URL in this readme returns HTTP 200 (WordPress.org reviewers check linked URLs).

== Changelog ==

= 2.3.0 =

* Phase 0: TableSorter v2.32.0; Isotope/Packery non-pkgd builds; Leaflet OpenStreetMap map provider; library metadata alignment.
* Phase 1: Toggle FAQPage JSON-LD; display conditions (cookie, UTM presets, business hours); Table pagination and CSV paste import in the editor.
* Phase 2: Calendar migrated to Schedule-X; anime.js v4 with updated animation helpers; Heading Extra entrance animation presets; Audio Player optional WaveSurfer waveform skin; video player native playback-rate support; audio-player header typo fix.
* Phase 3: Posts Extra list, featured-grid, and timeline layout skins; Search Form live AJAX results via public REST route; new Lottie widget.
* Playground: demo pages for Lottie, FAQ Schema, Posts list skin, OpenStreetMap map provider, and live search.

= 2.2.101 =

* Docs: Breadcrumbs widget documents optional **BreadcrumbList** structured data (microdata) when **Structured data** is enabled in widget settings.

= 2.2.100 =

* Fix: Devices widget scrollable portrait/landscape screens — CSS targeted non-existent class names; scrolling now works when enabled.
* Fix: Devices orientation toggle no longer shows a blank landscape screen when no landscape screenshot is set (falls back to portrait image).
* Fix: Playground Devices demo uses tall portrait and landscape screenshots so scroll and rotate are demonstrable.

= 2.2.99 =

* Fix: Shorten readme short description to meet WordPress.org 150-character limit.

= 2.2.98 =

* Fix: Playground Button Group demo ships fallback page CSS (padding, colors, effect backgrounds) so hover effects are visible when Elementor post CSS is incomplete.
* Fix: Playground Switcher demo shows Design / Build / Launch on the frontend — entrance animation now falls back when the appear plugin does not fire; nav stays visible on all breakpoints.
* Fix: Playground Devices demo seeds a phone screenshot, scrollable screen content, and orientation toggle instead of an empty mockup.

= 2.2.97 =

* Docs: readme SEO — short description, widget catalog, and FAQ entries for Elementor Extras / Extras for Elementor migration and replacement terminology.

= 2.2.96 =

* Add: Optional sanitized SVG uploads — Elementor → LandTech Extras → Advanced → Allow SVG uploads (admin opt-in; files sanitized on upload; works with Elementor media controls).
* Add: Inline SVG widget **Source** control — choose Media Library or **Custom URL** so you can link to an .svg without uploading through WordPress.
* Fix: Playground Inline SVG demo enables Override Color and uses the bundled SVG URL source.

= 2.2.95 =

* Fix: Buttons widget hover effects did not animate because transition CSS targeted the wrong pseudo-elements; base styles now apply transitions to `.ee-button:before` / `:after` with sensible defaults.
* Fix: Buttons without a link now use a no-op `#` href (with preventDefault) so click and hover behave consistently on the frontend.
* Fix: Custom button effect modifier classes (direction, entrance) fall back to control defaults when unset so Clone/Background effects are not stuck in a neutral state.
* Fix: Button Group tooltip init no longer throws when tooltip delay settings are absent (non-tooltip buttons).
* Fix: Playground demo pages regenerate Elementor post CSS after seeding so per-button effect colors and timing apply.

= 2.2.94 =

* Fix: Calendar event clicks did nothing on the Default skin because the events panel stayed `display: none` even when `show-events` was toggled; the panel now overlays the month view when a day with events is clicked.
* Fix: Calendar event title links without a URL now open the events panel instead of doing nothing.
* Fix: Playground Calendar demo seeds compact skin with sample manual events so click-to-reveal behavior is testable.

= 2.2.93 =

* Fix: Switcher widget stayed blank on the published frontend because entrance animation hides all slides until jQuery Appear fires; above-the-fold widgets now trigger appear immediately and nav/arrows opacity animations accept combined jQuery targets.
* Fix: anime.js helper now resolves arrays of jQuery collections (nav + arrows) for TweenMax.set/to calls used by Switcher.

= 2.2.92 =

* Fix: Scroll Indicator progress never updated because jQuery `.data('start-offset')` / `.data('end-offset')` do not read HTML `data-start-offset` attributes (camelCase keys required); offsets are now read reliably and full-page body tracking uses document height.
* Fix: Playground Scroll Indicator demo seeds three anchored sections (List skin) with matching heading CSS IDs and scrollable content between them.

= 2.2.91 =

* Fix: Age Gate and Popup modals showed a blank white box because legacy Magnific Popup animation CSS hides `.mfp-with-anim` until `.mfp-ready` is on the container; GLightbox now adds that class on open.
* Fix: Playground Age Gate demo seeds title, description, required age, button label, and classic skin.

= 2.2.90 =

* Fix: Popup and Age Gate modals opened empty because inline content kept `mfp-hide` / `glightbox-hide` (`display: none !important`) after the GLightbox migration; hide classes are now stripped before inline slides load.
* Fix: Playground Popup demo seeds title, body copy, classic skin, and an Open modal click trigger.

= 2.2.89 =

* Fix: Unfold open/close animation failed because the anime.js timeline shim did not return the timeline from chained `.to()` calls (GSAP-style `.add().to().to()`).
* Fix: Playground Unfold demo now seeds multi-paragraph content, 35% visible height, and Read more / Read less labels.

= 2.2.88 =

* Fix: Switcher widget stayed invisible on the frontend because the anime.js timeline shim did not understand GSAP label positions (`animateAll`, `animateAll+=`, `-=`); Switcher and Unfold animations now resolve correctly.
* Fix: Playground Switcher demo seeds three panels with images, classic skin, and entrance animation off for a reliable Live Preview.

= 2.2.87 =

* Fix: Playground Inline SVG demo now ships a bundled SVG URL plus color, sizing, and hover settings (empty settings previously rendered a blank frontend).

= 2.2.86 =

* Fix: Playground Button Group demo now seeds Clone, Flip, Background, 3D, and Cube hover effects (custom per-button styling was previously unset).

= 2.2.85 =

* Fix: Gallery Slider thumbnail clicks now use explicit slide indexes, Swiper loop/fade/RTL handling, and auto-height updates so the preview image stays visible when navigating.
* Fix: Gallery Slider preview skips broken attachment URLs and falls back to the gallery item URL when needed.
* Fix: Playground Gallery Slider demo enables Elementor lightbox on preview images.

= 2.2.84 =

* Fix: Playground critical error — store `_elementor_page_settings` as a PHP array (not JSON text) so Elementor page rendering does not fatal on PHP 8.
* Fix: Playground seeder sets the admin user during blueprint runPHP so Elementor document saves succeed.

= 2.2.83 =

* Fix: Playground demo homepage rebuilt with styled CSS hero, category sections, and card links to all 28 free widget demos (Elementor HTML widget).
* Fix: Demo pages use the Elementor header/footer template and hide the theme page title for cleaner previews.

= 2.2.82 =

* Fix: Timeline demo works on WordPress Playground — widget registers on free Elementor (custom items); Posts source still requires Elementor Pro.
* Fix: Playground Timeline demo seeds custom milestone items with sample content and images.

= 2.2.81 =

* Fix: Posts Extra demo works on WordPress Playground (free Elementor) via a standard `WP_Query` fallback when Elementor Pro query controls are unavailable; demo seeder now includes full grid/query settings.
* Fix: Posts Extra registers on free Elementor installs for basic post grids; advanced Query panel still requires Elementor Pro.

= 2.2.80 =

* Fix: Playground demo pages (including Switcher Demo) resolve at pretty permalinks after seeding via `flush_rewrite_rules()`.
* Fix: Switcher and Toggle Element demo pages seed with default panel content instead of empty widget settings.
* Fix: Demo pages save Elementor canvas data through the Elementor document API when available.

= 2.2.79 =

* Fix: Gallery Slider — initialize Swiper through Elementor's async loader, correct thumbnail `slideTo` / `slideToLoop` handling, and guard against uninitialized instances.
* Fix: `anime.js` unminified build no longer throws `module is not defined` in the browser when `SCRIPT_DEBUG` is enabled.

= 2.2.78 =

* **Try It Live demos** — Playground blueprint seeds a demo homepage, one Elementor page per free widget (28 demos), sample media/posts, and a grouped navigation menu via `includes/playground-demo-seeder.php`.

= 2.2.77 =

* Fix: WordPress Playground **Try It Live** link uses the public WordPress.org SVN blueprint URL (GitHub source repo is private).

= 2.2.76 =

* **Try It Live** — WordPress Playground blueprint (`blueprint.json`, `assets/blueprints/blueprint.json`), readme **Try It Live - Preview This Plugin Instantly** section, and **Live demo** links on the Plugins screen.

= 2.2.75 =

* WordPress.org soft-warning remediation: standardized plugin prefixes on dismissible notice transients (`landtech-extras-*`), search-form action hooks (`landtech_extras/…`), Posts pagination query var (`ltxe-page`), and Elementor Select2 script handle (`landtech-extras-elementor-select2`); expanded **External Services** readme (browser vs server requests, exact hostnames); fixed search-form dynamic hook registration.

= 2.2.74 =

* WordPress.org review follow-up: removed unnecessary `wp-admin/includes/plugin.php` load from bootstrap guard; all `is_plugin_active()` call sites use guarded `landtech_extras_require_plugin_api()`; Infinite Scroll core build strips jQuery bridge code (vanilla DOM API only).

= 2.2.73 =

* WordPress.org review (T2): ACF Gallery bridge is fully enabled when Advanced Custom Fields is active (removed add-on entitlement gate); fixed PHP syntax error in template loop background-image module; removed unnecessary `wp-admin/includes/plugin.php` include; upgraded GLightbox to 3.3.1; Infinite Scroll build comment updated to avoid bundled-jQuery scanner false positive.

= 2.2.72 =

* WordPress.org review follow-up: Infinite Scroll rebuilt as core-only bundle (no jquery-bridget); Posts Classic uses `new InfiniteScroll()` API; Isotope/Packery register with WordPress core jQuery dependency; all plugin script handles prefixed with `landtech-extras-`.

= 2.2.71 =

* WordPress.org review remediation: replaced unmaintained Magnific Popup with GLightbox (MIT); removed GPL-incompatible GreenSock/GSAP, SplitText, and CustomEase in favor of anime.js + Splitting.js; Infinite Scroll now registers with WordPress core jQuery; template loop background CSS uses `wp_add_inline_style()` instead of inline `<style>` tags; prefixed admin dismiss notice keys; removed Load TweenMax setting.

= 2.2.70 =

* WordPress.org submission readiness: API key/token settings use identity sanitization (not `sanitize_text_field()`); expanded **Development** and third-party library documentation in readme; distribution zip output to `DIST/`; removed dead stub module folders.
* Admin: dismissible notice AJAX hardened (nonce + capability).

= 2.2.69 =

* Posts Extra (Classic): Packery **second item width** control and `.ltxee-packery-second` span class for Metro tiles (Packery); default CSS in `assets/css/frontend.css` and `frontend-rtl.css`.
* Build: `package.json` includes `npm run build:assets` to regenerate minified frontend CSS/JS (see **Development**).

= 2.2.68 =

* Posts Extra (Classic): responsive **Packery: first item width** when layout is Metro tiles (Packery); overrides default width for `.ltxee-packery-featured` per breakpoint.

= 2.2.65 =

* WordPress **6.7+**: when no `landtech-extras-for-elementor-{locale}.mo` exists, assign `NOOP_Translations` for the text domain so gettext on `plugins_loaded` does not hit just-in-time loading before `after_setup_theme`.

= 2.2.64 =

* WordPress **6.7+**: load first readable `.mo` via `load_textdomain()` on `plugins_loaded` (early priority) so add-on/embeddings hooks avoid "translation loading triggered too early".

= 2.2.63 / 2.2.62 =

* Superseded text-domain timing fixes for WordPress 6.7+ — use **2.2.64+**.

= 2.2.61 =

* Gallery widget: removed invalid `packery-mode` script dependency; cleaned redundant vendor stubs (Posts Packery stack unchanged).

= 2.2.60 =

* Metro tiles (**Packery**): Packery + Isotope wiring, `ltxee-packery-featured`, Phase 4 layout policy / grandfather scan updates.

= 2.2.59 =

* Posts Base: `landtech_extras/posts/advanced_query_args`, `query_paged`, `set_query_args` hooks before `set_query()` (add-on REST facets / advanced query).

= 2.2.58 =

* **`metro`** layout (Isotope fitRows), `landtech_extras/posts/grid_layout_mode_choices` filter, resize-safe Isotope relayout, Phase 4 `metro` handling.

= 2.2.57 =

* Posts Extra preset skins use Classic loop handler; editor loads Isotope only when the document needs it; `landtech_extras/settings/features_fields` for add-on settings.

= 2.2.56 =

* Devices widget: landscape/portrait and `Group_Control_Image_Size` guards; correct image URL validation for requested field.

= 2.2.55 =

* Posts Extra: defer Isotope until needed; Phase 4 opt-in filters for add-on masonry grandfather.

= 2.2.54 =

* ACF gallery bridge (**ACF** required): Gallery / Gallery Slider ACF Gallery source (`includes/gallery/acf-gallery-bridge.php`).

= 2.2.53+ (fork) =

* **LandTech Extras for Elementor** on WordPress.org: External Services / BYOK readme disclosure, org–add-on coexistence (`LANDTECH_EXTRAS_RUNTIME_FAMILY`), text domain `landtech-extras-for-elementor`, GPLv3, directory updates only (no custom org updater).

= Earlier releases =

* Releases **2.2.52 and older** inherit the historical **Elementor Extras** changelog (widgets, extensions, Elementor compatibility). The full line-by-line history is omitted here to satisfy WordPress.org's **5,000-character** Changelog limit; consult Git/SVN or shipped release archives for the complete record.


== Support ==

For add-on purchases, feature requests, and bug reports, email **sales@landtechwebdesigns.com** or use the contact options on [Land Tech Web Designs](https://landtechwebdesigns.com/).

== Credits ==

Developed by **Land Tech Web Designs, Corp.**
