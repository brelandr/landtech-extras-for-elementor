/**
 * News ticker — pause + reduced-motion.
 */
(function () {
	'use strict';

	function prefersReduced() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-nt]').forEach(function (el) {
			if (prefersReduced()) {
				el.classList.add('ltxe-nt--reduced');
				el.querySelectorAll('.ltxe-nt__track').forEach(function (track) {
					track.style.animation = 'none';
				});
			}
		});
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-news-ticker.default', function ($scope) {
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
