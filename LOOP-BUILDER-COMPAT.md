# Loop Builder compatibility (U29)

Widgets that appear inside Elementor Loop Builder must initialize **per instance**.

Correct pattern:

```js
elementorFrontend.hooks.addAction( 'frontend/element_ready/WIDGET.default', function ( $scope ) {
	init( $scope[ 0 ] );
} );
```

| Widget | Status | Notes |
|---|---|---|
| Lottie (`ee-lottie`) | ✅ compatible | Per-instance `element_ready` |
| Calendar (`ee-calendar`) | ✅ compatible | Schedule-X boots per wrapper |
| Popup (`ee-popup`) | ⚠️ fixed | Separate GLightbox instance per widget (2.8.2) |
| Tabs (`ee-tabs`) | ✅ compatible | Scoped to `$scope` |
| Gallery (`gallery-extra`) | ✅ compatible | Instance map, not a global |
| Inline SVG (`ee-inline-svg`) | ✅ compatible | `data-url` per widget |
| Image Comparison | ✅ compatible | `element_ready` + scoped query |
| Hotspots | ✅ compatible | Tooltips bound per `$scope` |
| Progress Bar / Countdown / Reading Progress | ✅ compatible | `element_ready` |
| Pricing Table / Toggle | ✅ compatible | Event scoped to widget |
| Star Rating | ✅ compatible | Static output |
| Video Playlist | ✅ compatible | Player map by element |
| Testimonials | ✅ compatible | `element_ready` |
| FAQ Accordion | ✅ compatible | `element_ready` |
| Team Members | ✅ compatible | Flip buttons per card |
| Icon Box | ✅ compatible | CSS only |
| CTA | ✅ compatible | Video pause per `$scope` |
| Social Share | ✅ compatible | `element_ready` |
| Back to Top | ✅ compatible | `element_ready` |
| Cookie Consent | ✅ compatible | `element_ready` |
| Sticky Wrapper | ✅ compatible | CSS only |
| Dark Mode Toggle | ✅ compatible | `element_ready` |

PHP render uses `get_the_ID()` for current-loop post data. Do not rely on a stale `global $post` when outputting permalinks or titles inside a loop item.
