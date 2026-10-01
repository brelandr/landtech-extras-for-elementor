/**
 * Logo carousel — pause marquee when prefers-reduced-motion is set.
 */
(function () {
	'use strict';

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('.ltxe-logo-carousel').forEach(function (el) {
			if (prefersReducedMotion()) {
				el.classList.add('ltxe-logo-carousel--reduced');
				el.querySelectorAll('.ltxe-logo-carousel__track').forEach(function (track) {
					track.style.animation = 'none';
				});
			}
		});
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-logo-carousel.default', function ($scope) {
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
