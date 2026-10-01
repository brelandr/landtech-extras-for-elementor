/**
 * Animated headline — typewriter and word-cycle.
 */
(function () {
	'use strict';

	function prefersReduced() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function parseCfg(el) {
		try {
			return JSON.parse(el.getAttribute('data-ltxe-ah') || '{}');
		} catch (e) {
			return {};
		}
	}

	function showReduced(el, words) {
		el.classList.add('ltxe-ah--reduced');
		var all = el.querySelector('.ltxe-ah__all');
		if (!all) {
			all = document.createElement('span');
			all.className = 'ltxe-ah__all';
			var word = el.querySelector('[data-ltxe-ah-word]');
			if (word && word.parentNode) {
				word.parentNode.insertBefore(all, word);
			}
		}
		all.textContent = (words || []).join(', ');
	}

	function typewriter(el, cfg) {
		var wordEl = el.querySelector('[data-ltxe-ah-word]');
		var words = cfg.words || [];
		if (!wordEl || !words.length) {
			return;
		}
		var speed = parseInt(cfg.speed, 10) || 80;
		var hold = parseInt(cfg.hold, 10) || 2000;
		var i = 0;
		var pos = 0;
		var deleting = false;

		function tick() {
			var current = words[i] || '';
			if (!deleting) {
				pos += 1;
				wordEl.textContent = current.slice(0, pos);
				if (pos >= current.length) {
					deleting = true;
					window.setTimeout(tick, hold);
					return;
				}
			} else {
				pos -= 1;
				wordEl.textContent = current.slice(0, Math.max(0, pos));
				if (pos <= 0) {
					deleting = false;
					i = (i + 1) % words.length;
				}
			}
			window.setTimeout(tick, deleting ? Math.max(30, speed / 2) : speed);
		}
		wordEl.textContent = '';
		tick();
	}

	function cycle(el, cfg) {
		var wordEl = el.querySelector('[data-ltxe-ah-word]');
		var words = cfg.words || [];
		if (!wordEl || words.length < 2) {
			return;
		}
		var hold = parseInt(cfg.hold, 10) || 2000;
		var i = 0;
		window.setInterval(function () {
			wordEl.classList.add('is-out');
			window.setTimeout(function () {
				i = (i + 1) % words.length;
				wordEl.textContent = words[i];
				wordEl.classList.remove('is-out');
			}, 250);
		}, hold);
	}

	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-ah]').forEach(function (el) {
			if (el.getAttribute('data-ltxe-ah-ready') === '1') {
				return;
			}
			el.setAttribute('data-ltxe-ah-ready', '1');
			var cfg = parseCfg(el);
			if (prefersReduced()) {
				showReduced(el, cfg.words || []);
				return;
			}
			if (cfg.effect === 'typewriter') {
				typewriter(el, cfg);
			} else {
				cycle(el, cfg);
			}
		});
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-animated-headline.default', function ($scope) {
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
