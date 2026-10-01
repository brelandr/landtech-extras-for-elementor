/**
 * Number counter — IntersectionObserver + easing, respects prefers-reduced-motion.
 */
(function () {
	'use strict';

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function formatNumber(value, decimals, delimiter) {
		var fixed = Number(value).toFixed(decimals);
		var parts = fixed.split('.');
		var int = parts[0];
		var frac = parts[1] || '';
		if ('comma' === delimiter) {
			int = int.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
		} else if ('period' === delimiter) {
			int = int.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
		}
		return frac ? int + '.' + frac : int;
	}

	function ease(t, type) {
		if ('linear' === type) {
			return t;
		}
		if ('ease-in-out' === type) {
			return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
		}
		return 1 - Math.pow(1 - t, 3);
	}

	function parseConfig(el) {
		try {
			return JSON.parse(el.getAttribute('data-ltxe-counter') || '{}');
		} catch (e) {
			return {};
		}
	}

	function setDigits(el, value, config) {
		var digits = el.querySelector('.ltxe-number-counter__digits');
		if (digits) {
			digits.textContent = formatNumber(value, config.decimals || 0, config.delimiter || 'none');
		}
	}

	function play(el, config) {
		var target = typeof config.target === 'number' ? config.target : 0;
		var duration = typeof config.duration === 'number' ? config.duration : 2000;
		var easing = config.easing || 'ease-out';

		if (el._ltxeCounterRaf) {
			window.cancelAnimationFrame(el._ltxeCounterRaf);
			el._ltxeCounterRaf = 0;
		}

		if (prefersReducedMotion() || duration <= 0) {
			setDigits(el, target, config);
			el.setAttribute('data-ltxe-counter-done', '1');
			return;
		}

		var start = performance.now();
		setDigits(el, 0, config);

		function tick(now) {
			var t = Math.min(1, (now - start) / duration);
			setDigits(el, target * ease(t, easing), config);
			if (t < 1) {
				el._ltxeCounterRaf = window.requestAnimationFrame(tick);
			} else {
				setDigits(el, target, config);
				el.setAttribute('data-ltxe-counter-done', '1');
			}
		}

		el._ltxeCounterRaf = window.requestAnimationFrame(tick);
	}

	function initOne(el) {
		if (el.getAttribute('data-ltxe-nc-init')) {
			return;
		}
		el.setAttribute('data-ltxe-nc-init', '1');
		var config = parseConfig(el);
		var once = false !== config.once;

		if (prefersReducedMotion()) {
			play(el, config);
			return;
		}

		if (!('IntersectionObserver' in window)) {
			play(el, config);
			return;
		}

		var io = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						play(el, config);
						if (once) {
							io.disconnect();
						}
					} else if (!once) {
						el.removeAttribute('data-ltxe-counter-done');
						setDigits(el, 0, config);
					}
				});
			},
			{ threshold: 0.35 }
		);
		io.observe(el);
		el._ltxeCounterIo = io;
	}

	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('.ltxe-number-counter').forEach(initOne);
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-number-counter.default', function ($scope) {
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
