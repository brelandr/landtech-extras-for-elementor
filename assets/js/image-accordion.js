/**
 * Image accordion — aria-expanded + click/keyboard.
 */
(function () {
	'use strict';

	function prefersReduced() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function parseCfg(el) {
		try {
			return JSON.parse(el.getAttribute('data-ltxe-ia') || '{}');
		} catch (e) {
			return {};
		}
	}

	function setOpen(root, panel) {
		root.querySelectorAll('.ltxe-ia__panel').forEach(function (item) {
			var open = item === panel;
			item.classList.toggle('is-open', open);
			item.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
	}

	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-ia]').forEach(function (el) {
			if (el.getAttribute('data-ltxe-ia-ready') === '1') {
				return;
			}
			el.setAttribute('data-ltxe-ia-ready', '1');
			if (prefersReduced()) {
				el.classList.add('ltxe-ia--reduced');
				el.querySelectorAll('.ltxe-ia__panel').forEach(function (panel) {
					panel.classList.add('is-open');
					panel.setAttribute('aria-expanded', 'true');
				});
				return;
			}
			var cfg = parseCfg(el);
			var panels = el.querySelectorAll('.ltxe-ia__panel');
			if (cfg.trigger === 'click') {
				panels.forEach(function (panel) {
					panel.addEventListener('click', function (event) {
						if (event.target && event.target.closest && event.target.closest('.ltxe-ia__btn')) {
							return;
						}
						setOpen(el, panel);
					});
					panel.addEventListener('keydown', function (event) {
						if (event.key === 'Enter' || event.key === ' ') {
							event.preventDefault();
							setOpen(el, panel);
						}
					});
				});
			} else {
				panels.forEach(function (panel) {
					panel.addEventListener('mouseenter', function () {
						setOpen(el, panel);
					});
				});
			}
		});
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-image-accordion.default', function ($scope) {
					init($scope[0]);
				});
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			init(document);
		});
	} else {
		init(document);
	}
})();
