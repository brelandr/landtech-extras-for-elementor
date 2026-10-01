(function () {
	'use strict';
	function fib(n, i) {
		var offset = 2 / n;
		var y = i * offset - 1 + (offset / 2);
		var r = Math.sqrt(1 - y * y);
		var phi = i * Math.PI * (3 - Math.sqrt(5));
		return { x: Math.cos(phi) * r, y: y, z: Math.sin(phi) * r };
	}
	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-sphere]').forEach(function (el) {
			if (el.getAttribute('data-ready') === '1') { return; }
			el.setAttribute('data-ready', '1');
			if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
			var cfg; try { cfg = JSON.parse(el.getAttribute('data-ltxe-sphere') || '{}'); } catch (e) { cfg = {}; }
			var tags = Array.prototype.slice.call(el.querySelectorAll('.ltxe-sphere__tag'));
			var pts = tags.map(function (_, i) { return fib(tags.length, i); });
			var rot = 0;
			var paused = false;
			function draw() {
				var r = cfg.radius || 140;
				tags.forEach(function (tag, i) {
					var p = pts[i];
					var c = Math.cos(rot);
					var s = Math.sin(rot);
					var x = p.x * c - p.z * s;
					var z = p.x * s + p.z * c;
					tag.style.transform = 'translate(-50%,-50%) translate3d(' + (x * r) + 'px,' + (p.y * r) + 'px,' + (z * r) + 'px) scale(' + (0.7 + (z + 1) / 3) + ')';
					tag.style.opacity = String(0.4 + (z + 1) / 3);
					tag.style.zIndex = String(Math.round((z + 1) * 50));
				});
			}
			function loop() {
				if (!paused && cfg.auto !== false) { rot += 0.008; }
				draw();
				window.requestAnimationFrame(loop);
			}
			if (cfg.pauseHover) {
				el.addEventListener('mouseenter', function () { paused = true; });
				el.addEventListener('mouseleave', function () { paused = false; });
			}
			loop();
		});
	}
	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-tags-cloud-sphere.default', function ($s) { init($s[0]); });
			}
		});
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', function () { init(document); }); } else { init(document); }
})();
