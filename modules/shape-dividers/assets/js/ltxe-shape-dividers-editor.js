(function () {
	'use strict';
	function inject(el) {
		if (!el || el.querySelector('.ltxe-shape-divider')) { return; }
		var raw = el.getAttribute('data-ltxe-sd');
		if (!raw) { return; }
		var cfg; try { cfg = JSON.parse(raw); } catch (e) { return; }
		['top', 'bottom'].forEach(function (side) {
			var item = cfg[side];
			if (!item || !item.svg) { return; }
			var wrap = document.createElement('div');
			wrap.className = 'ltxe-shape-divider ltxe-shape-divider--' + side + (item.flip ? ' is-flip' : '') + (item.invert ? ' is-invert' : '');
			wrap.setAttribute('data-ltxe-shape', item.slug || '');
			wrap.style.zIndex = String(item.z || 1);
			wrap.style.height = (item.h || 80) + 'px';
			wrap.style.width = (item.w || 100) + '%';
			wrap.innerHTML = item.svg;
			el.appendChild(wrap);
		});
	}
	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('.ltxe-has-shape-divider').forEach(inject);
		if (root.classList && root.classList.contains('ltxe-has-shape-divider')) { inject(root); }
	}
	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($s) { init($s[0]); });
				elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($s) { init($s[0]); });
			}
		});
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', function () { init(document); }); } else { init(document); }
})();
