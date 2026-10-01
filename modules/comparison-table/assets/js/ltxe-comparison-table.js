(function () {
	'use strict';
	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-cmp]').forEach(function (el) {
			if (el.getAttribute('data-ready') === '1') { return; }
			el.setAttribute('data-ready', '1');
			if (el.getAttribute('data-sticky') === '1') { el.classList.add('is-sticky'); }
			var heads = el.querySelectorAll('thead .ltxe-cmp__col');
			var tabs = el.querySelector('.ltxe-cmp__tabs');
			if (el.getAttribute('data-mobile') === 'tabs' && tabs) {
				tabs.hidden = false;
				heads.forEach(function (th, i) {
					var b = document.createElement('button');
					b.type = 'button';
					b.className = 'ltxe-cmp__tab' + (0 === i ? ' is-active' : '');
					b.textContent = th.querySelector('.ltxe-cmp__title') ? th.querySelector('.ltxe-cmp__title').textContent : String(i + 1);
					b.addEventListener('click', function () {
						el.querySelectorAll('.ltxe-cmp__tab').forEach(function (t) { t.classList.remove('is-active'); });
						b.classList.add('is-active');
						el.querySelectorAll('[data-col]').forEach(function (cell) {
							cell.classList.toggle('is-active-col', cell.getAttribute('data-col') === String(i));
						});
					});
					tabs.appendChild(b);
				});
				el.querySelectorAll('[data-col="0"]').forEach(function (cell) { cell.classList.add('is-active-col'); });
			}
		});
	}
	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-comparison-table.default', function ($s) { init($s[0]); });
			}
		});
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', function () { init(document); }); } else { init(document); }
	window.ltxeCmpInit = init;
})();
