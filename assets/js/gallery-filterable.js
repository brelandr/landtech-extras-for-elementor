/**
 * Filterable gallery — Isotope when present, class fallback otherwise.
 */
(function ($) {
	'use strict';

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function parseConfig(el) {
		try {
			return JSON.parse(el.getAttribute('data-ltxe-gf') || '{}');
		} catch (e) {
			return {};
		}
	}

	function bind(el) {
		if (el.getAttribute('data-ltxe-gf-init')) {
			return;
		}
		el.setAttribute('data-ltxe-gf-init', '1');
		var config = parseConfig(el);
		var grid = el.querySelector('.ltxe-gf__grid');
		var iso = null;
		var duration = prefersReducedMotion() ? 0 : 400;

		if (grid && $ && $.fn && $.fn.isotope) {
			iso = $(grid).isotope({
				itemSelector: '.ltxe-gf__item',
				layoutMode: config.layout === 'fitRows' ? 'fitRows' : 'masonry',
				transitionDuration: duration,
				percentPosition: true,
			});
		}

		el.querySelectorAll('.ltxe-gf__btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				el.querySelectorAll('.ltxe-gf__btn').forEach(function (b) {
					b.classList.remove('is-active');
					b.setAttribute('aria-pressed', 'false');
				});
				btn.classList.add('is-active');
				btn.setAttribute('aria-pressed', 'true');
				var filter = btn.getAttribute('data-filter') || '*';
				if (iso) {
					iso.isotope({ filter: filter });
					return;
				}
				grid.querySelectorAll('.ltxe-gf__item').forEach(function (item) {
					var show = '*' === filter || item.matches(filter);
					item.classList.toggle('is-hidden', !show);
				});
			});
		});
	}

	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('.ltxe-gf').forEach(bind);
	}

	if ($) {
		$(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-gallery-filterable.default', function ($scope) {
					init($scope[0]);
				});
			}
		});
		$(function () {
			init(document);
		});
	} else if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			init(document);
		});
	} else {
		init(document);
	}
})(window.jQuery);
