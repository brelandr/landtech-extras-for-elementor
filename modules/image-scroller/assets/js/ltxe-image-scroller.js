(function () {
	'use strict';
	function reduced() { return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches; }
	function cfg(el) { try { return JSON.parse(el.getAttribute('data-ltxe-iscroll') || '{}'); } catch (e) { return {}; } }
	function offset(el, c, pct) {
		var img = el.querySelector('.ltxe-iscroll__img');
		if (!img) { return; }
		var max = c.direction === 'horizontal' ? (img.offsetWidth - el.clientWidth) : (img.offsetHeight - el.clientHeight);
		if (max < 0) { max = 0; }
		var v = -max * pct * (c.speed || 1);
		img.style.transform = c.direction === 'horizontal' ? 'translateX(' + v + 'px)' : 'translateY(' + v + 'px)';
	}
	function init(scope) {
		if (reduced()) { return; }
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-iscroll]').forEach(function (el) {
			if (el.getAttribute('data-ready') === '1') { return; }
			el.setAttribute('data-ready', '1');
			var c = cfg(el);
			if (c.trigger === 'hover') {
				el.addEventListener('mousemove', function (ev) {
					var r = el.getBoundingClientRect();
					var pct = c.direction === 'horizontal' ? (ev.clientX - r.left) / r.width : (ev.clientY - r.top) / r.height;
					window.requestAnimationFrame(function () { offset(el, c, Math.min(1, Math.max(0, pct))); });
				});
			} else if (c.trigger === 'click') {
				var on = false;
				el.addEventListener('click', function () {
					on = !on;
					offset(el, c, on ? 1 : 0);
				});
			} else {
				var io = new IntersectionObserver(function (entries) {
					entries.forEach(function (en) {
						if (!en.isIntersecting) { return; }
						var r = el.getBoundingClientRect();
						var pct = 1 - Math.min(1, Math.max(0, r.top / window.innerHeight));
						offset(el, c, pct);
					});
				}, { threshold: [0, 0.25, 0.5, 0.75, 1] });
				io.observe(el);
			}
		});
	}
	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-image-scroller.default', function ($s) { init($s[0]); });
			}
		});
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', function () { init(document); }); } else { init(document); }
})();
