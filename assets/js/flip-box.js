/**
 * Flip box — click/keyboard toggle, reduced-motion class.
 */
(function () {
	'use strict';

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function bind(el) {
		if (el.getAttribute('data-ltxe-fb-init')) {
			return;
		}
		el.setAttribute('data-ltxe-fb-init', '1');

		if (prefersReducedMotion()) {
			el.classList.add('ltxe-flip-box--reduced');
		}

		if (el.classList.contains('ltxe-flip-box--hover')) {
			el.addEventListener('mouseenter', function () {
				el.classList.add('is-flipped');
			});
			el.addEventListener('mouseleave', function () {
				el.classList.remove('is-flipped');
			});
			return;
		}

		if (!el.classList.contains('ltxe-flip-box--click')) {
			return;
		}

		function toggle(e) {
			if (e.target && e.target.closest && e.target.closest('a')) {
				return;
			}
			el.classList.toggle('is-flipped');
			el.setAttribute('aria-pressed', el.classList.contains('is-flipped') ? 'true' : 'false');
		}

		el.addEventListener('click', toggle);
		el.addEventListener('keydown', function (e) {
			if ('Enter' === e.key || ' ' === e.key) {
				e.preventDefault();
				toggle(e);
			}
		});
	}

	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('.ltxe-flip-box').forEach(bind);
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-flip-box.default', function ($scope) {
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
