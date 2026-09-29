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
- Drag handles (image comparison) are keyboard-reachable (`tabindex="0"`, `role="slider"`, arrow keys).
- No inline `onclick`, `onmouseover`, or `javascript:` hrefs (CSP-safe).
- Loop Builder: init with `elementorFrontend.hooks.addAction('frontend/element_ready/WIDGET.default', …)` — never a single `document.querySelector()`.

Run axe DevTools on the widget demo page and keep **0 critical** violations.
