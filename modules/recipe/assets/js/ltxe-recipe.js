/**
 * Recipe print helper.
 */
(function () {
	'use strict';

	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-recipe]').forEach(function (el) {
			if (el.getAttribute('data-ltxe-recipe-ready') === '1') {
				return;
			}
			el.setAttribute('data-ltxe-recipe-ready', '1');
			var btn = el.querySelector('.ltxe-recipe__print');
			if (!btn) {
				return;
			}
			btn.addEventListener('click', function () {
				window.print();
			});
		});
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-recipe.default', function ($scope) {
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

	window.ltxeRecipeInit = init;
})();
