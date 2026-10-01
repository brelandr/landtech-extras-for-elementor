/**
 * Posts carousel — standalone fixture init + hover class helper.
 * Production Posts Extra carousel uses Elementor's bundled Swiper via frontend.js.
 */
(function () {
	'use strict';

	function parseCfg(el) {
		var raw = el.getAttribute('data-ltxe-pc');
		var cfg = { slides: 3, tablet: 2, mobile: 1, delay: 3000, loop: true, autoplay: true };
		if (!raw) {
			return cfg;
		}
		try {
			var parsed = JSON.parse(raw);
			if (parsed && typeof parsed === 'object') {
				Object.keys(cfg).forEach(function (key) {
					if (typeof parsed[key] !== 'undefined') {
						cfg[key] = parsed[key];
					}
				});
			}
		} catch (e) {
			return cfg;
		}
		return cfg;
	}

	function slidesPerView(cfg) {
		var w = window.innerWidth;
		if (w < 768) {
			return parseInt(cfg.mobile, 10) || 1;
		}
		if (w < 1024) {
			return parseInt(cfg.tablet, 10) || 2;
		}
		return parseInt(cfg.slides, 10) || 3;
	}

	function apply(root) {
		var cfg = parseCfg(root);
		var track = root.querySelector('.ltxe-pc__track');
		var slides = root.querySelectorAll('.ltxe-pc__slide');
		if (!track || !slides.length) {
			return;
		}
		var spv = slidesPerView(cfg);
		root.style.setProperty('--ltxe-pc-slides', String(spv));
		var max = cfg.loop ? slides.length : Math.max(0, slides.length - spv);
		var index = parseInt(root.getAttribute('data-index') || '0', 10);
		if (cfg.loop) {
			index = ((index % slides.length) + slides.length) % slides.length;
		} else {
			index = Math.max(0, Math.min(max, index));
		}
		root.setAttribute('data-index', String(index));
		root.setAttribute('data-active-title', slides[index] ? slides[index].getAttribute('data-title') || '' : '');
		var pct = (100 / spv) * index;
		track.style.transform = 'translateX(-' + pct + '%)';
	}

	function go(root, delta) {
		var index = parseInt(root.getAttribute('data-index') || '0', 10) + delta;
		root.setAttribute('data-index', String(index));
		apply(root);
	}

	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-pc]').forEach(function (el) {
			if (el.getAttribute('data-ltxe-pc-ready') === '1') {
				return;
			}
			el.setAttribute('data-ltxe-pc-ready', '1');
			el.setAttribute('data-index', '0');
			apply(el);
			var next = el.querySelector('.ltxe-pc__next');
			var prev = el.querySelector('.ltxe-pc__prev');
			if (next) {
				next.addEventListener('click', function () {
					go(el, 1);
				});
			}
			if (prev) {
				prev.addEventListener('click', function () {
					go(el, -1);
				});
			}
			var cfg = parseCfg(el);
			var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			if (cfg.autoplay && !reduce) {
				var timer = window.setInterval(function () {
					go(el, 1);
				}, parseInt(cfg.delay, 10) || 3000);
				el.addEventListener('mouseenter', function () {
					window.clearInterval(timer);
					timer = 0;
				});
				el.addEventListener('mouseleave', function () {
					if (!timer) {
						timer = window.setInterval(function () {
							go(el, 1);
						}, parseInt(cfg.delay, 10) || 3000);
					}
				});
			}
			window.addEventListener('resize', function () {
				apply(el);
			});
		});
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/posts-extra.carousel', function ($scope) {
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
