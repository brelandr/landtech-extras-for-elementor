# Loop Builder compatibility (U29)

Widgets that appear inside Elementor Loop Builder must initialize **per instance**.

Correct pattern:

```js
elementorFrontend.hooks.addAction( 'frontend/element_ready/WIDGET.default', function ( $scope ) {
	init( $scope[ 0 ] );
} );
```

Do **not** use a single `document.querySelector()` for init. Store Isotope / Swiper / Infinite Scroll on a `Map` keyed by `ee.getUniqueLoopScopeId( $scope )`, not on a shared function property.

PHP render of the current loop item must use `get_the_ID()` (via `Skin_Base::ltxe_loop_post_id()` / `ltxe_loop_post()`), not a stale `global $post`.

## Repeating-content widgets (this audit)

| Widget | Status | Notes |
|---|---|---|
| Posts Extra (`posts-extra` + skins) | ⚠️ fixed | `element_ready` per skin; Isotope / Infinite Scroll / Swiper stored in `ee.ltxeSetInstance`; render uses `get_the_ID()` |
| Gallery (`gallery-extra`) | ⚠️ fixed | `element_ready` in `frontend.js` + `assets/js/widgets/gallery.js`; per-wrapper `data-ltxe-gallery-init`; masonry Isotope keyed by scope |
| Gallery Slider (`gallery-slider`) | ✅ compatible | `element_ready` + unique loop scope id |
| Testimonials (`ltxe-testimonials`) | ✅ compatible | `element_ready` + `data-ltxe-tm-init` |
| Team Members (`ltxe-team-members`) | ✅ compatible | `element_ready` + `data-ltxe-team-init` |

## Other widgets

| Widget | Status | Notes |
|---|---|---|
| Lottie (`ee-lottie`) | ✅ compatible | Per-instance `element_ready` |
| Calendar (`ee-calendar`) | ✅ compatible | Schedule-X boots per wrapper |
| Popup (`ee-popup`) | ⚠️ fixed | Separate GLightbox instance per widget (2.8.2) |
| Tabs (`ee-tabs`) | ✅ compatible | Scoped to `$scope` |
| Inline SVG (`ee-inline-svg`) | ✅ compatible | `data-url` per widget |
| Image Comparison | ✅ compatible | `element_ready` + scoped query |
| Hotspots | ✅ compatible | Tooltips bound per `$scope` |
| Progress Bar / Countdown / Reading Progress | ✅ compatible | `element_ready` |
| Pricing Table / Toggle | ✅ compatible | Event scoped to widget |
| Star Rating | ✅ compatible | Static output |
| Video Playlist | ✅ compatible | Player map by element |
| FAQ Accordion | ✅ compatible | `element_ready` |
| Icon Box | ✅ compatible | CSS only |
| CTA | ✅ compatible | Video pause per `$scope` |
| Social Share | ✅ compatible | `element_ready` |
| Back to Top | ✅ compatible | `element_ready` |
| Cookie Consent | ✅ compatible | `element_ready` |
| Sticky Wrapper | ✅ compatible | CSS only |
| Dark Mode Toggle | ✅ compatible | `element_ready` |

## Playwright (U29)

See `e2e/tests/upgrade-u29-loop-builder.spec.ts`.

| Editor verify | Frontend verify | Key assertions |
|---|---|---|
| Loop Builder template | Posts loop / `/demo-loop-builder/` | All widgets render correctly per post, no JS instance collision |

Demo URL: `/demos/loop-builder/` (Playground slug: `/demo-loop-builder/`).
