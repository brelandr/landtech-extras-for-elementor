# Contributing to LandTech Extras for Elementor

## WCAG 2.2 ARIA checklist (U30)

Every new interactive widget must pass this list before merge:

- Interactive controls are real `<button>` or `<a href>` elements (no click-only `<div>`).
- Touch targets are at least **44×44px**.
- `prefers-reduced-motion` disables or simplifies animation.
- Dialogs use `role="dialog"`, `aria-modal="true"`, Escape to close, and a focus trap.
- Tab interfaces use `role="tablist"`, `role="tab"`, `role="tabpanel"`, `aria-selected`, and arrow-key navigation.
- Carousels expose `aria-roledescription="carousel"`, a pause control, and a live status.
- Dynamic counters (countdown) use `aria-live="polite"` and `aria-atomic="true"`.
- Drag handles (image comparison) use `<input type="range" class="ltxe-ic__handle">` so arrow keys work natively; JS listens to the `input` event.
- No inline `onclick`, `onmouseover`, or `javascript:` hrefs (CSP-safe).
- Loop Builder: init with `elementorFrontend.hooks.addAction('frontend/element_ready/WIDGET.default', …)` — never a single `document.querySelector()`.

Run axe DevTools on the widget demo page and keep **0 critical** violations.

## CSP checklist (U32)

First-party PHP and JS must stay compatible with a strict policy (`script-src 'self'; style-src 'self'` — no `unsafe-inline`):

- Never emit `onclick`, `onmouseover`, or other inline event attributes.
- Never emit `href="javascript:..."`.
- Never use `eval()` or `new Function()` in `assets/js/`.
- Interactive markup uses `data-ltxe-action="…"`; handle it from `window.ltxeActions` (delegated in `assets/js/frontend-core.js`).
- Do not put widget styles in HTML `style=""`. Use a stylesheet class, or set values via CSSOM / `data-ltxe-*` (see `LtxeUtils.applyCspSafeStyles`).
- Runtime animation may set `element.style.property` from JS (CSSOM is not blocked by `style-src`).

Run `npm run test:u32-csp` before merge.
