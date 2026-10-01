(function () {
	'use strict';
	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-pdf]').forEach(function (el) {
			if (el.getAttribute('data-ready') === '1') { return; }
			el.setAttribute('data-ready', '1');
			var cfg; try { cfg = JSON.parse(el.getAttribute('data-ltxe-pdf') || '{}'); } catch (e) { cfg = {}; }
			var page = Math.max(1, parseInt(cfg.page, 10) || 1);
			var zoom = 100;
			var frame = el.querySelector('.ltxe-pdf__frame');
			var label = el.querySelector('[data-pdf-page]');
			function apply() {
				if (!frame || !cfg.url) { return; }
				var base = String(cfg.url).split('#')[0];
				var join = base.indexOf('?') === -1 ? '?' : '&';
				var next = base + join + 'ltxe_pdf=' + page + 'z' + zoom + '#page=' + page + '&zoom=' + zoom;
				if (label) { label.textContent = String(page); }
				frame.src = 'about:blank';
				window.setTimeout(function () { frame.src = next; }, 30);
			}
			el.querySelectorAll('[data-pdf-act]').forEach(function (btn) {
				btn.addEventListener('click', function (event) {
					event.preventDefault();
					var act = btn.getAttribute('data-pdf-act');
					if (act === 'prev' && page > 1) { page -= 1; apply(); }
					if (act === 'next') { page += 1; apply(); }
					if (act === 'zin' && zoom < 200) { zoom = Math.min(200, zoom + 25); apply(); }
					if (act === 'zout' && zoom > 50) { zoom = Math.max(50, zoom - 25); apply(); }
					if (act === 'print') {
						try {
							if (frame && frame.contentWindow) { frame.contentWindow.print(); return; }
						} catch (err) { /* Cross-origin PDF viewers cannot be printed from the parent. */ }
						window.print();
					}
				});
			});
		});
	}
	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-pdf-embed.default', function ($s) { init($s[0]); });
			}
		});
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', function () { init(document); }); } else { init(document); }
})();
