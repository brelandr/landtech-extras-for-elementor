(function () {
	'use strict';
	function reduced() { return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches; }
	function parse(el) { try { return JSON.parse(el.getAttribute('data-ltxe-icard') || '{}'); } catch (e) { return {}; } }
	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-icard]').forEach(function (el) {
			if (el.getAttribute('data-ready') === '1') { return; }
			el.setAttribute('data-ready', '1');
			var cfg = parse(el);
			if (cfg.effect !== 'tilt' || reduced()) { return; }
			var max = cfg.max || 15, persp = cfg.perspective || 1000;
			el.addEventListener('mousemove', function (ev) {
				var r = el.getBoundingClientRect();
				var x = (ev.clientX - r.left) / r.width - 0.5;
				var y = (ev.clientY - r.top) / r.height - 0.5;
				window.requestAnimationFrame(function () {
					el.style.transform = 'perspective(' + persp + 'px) rotateX(' + (-y * max) + 'deg) rotateY(' + (x * max) + 'deg)';
					el.style.setProperty('--ltxe-glare-x', ((x + 0.5) * 100) + '%');
					el.style.setProperty('--ltxe-glare-y', ((y + 0.5) * 100) + '%');
					el.classList.add('is-tilting');
				});
			});
			el.addEventListener('mouseleave', function () {
				if (window.ltxeAnimeV4Params && window.anime && window.anime.animate) {
					window.anime.animate(el, window.ltxeAnimeV4Params({ rotateX: 0, rotateY: 0, duration: cfg.speed || 180, easing: 'easeOutQuad' }));
				} else {
					el.style.transform = '';
				}
				el.classList.remove('is-tilting');
			});
		});
	}
	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-interactive-card.default', function ($s) { init($s[0]); });
			}
		});
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', function () { init(document); }); } else { init(document); }
})();
