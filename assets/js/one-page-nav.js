/**
 * One page nav — IntersectionObserver + smooth scroll.
 */
(function () {
	'use strict';

	function parseCfg(el) {
		try {
			return JSON.parse(el.getAttribute('data-ltxe-opn') || '{}');
		} catch (e) {
			return {};
		}
	}

	function sectionTitle(section) {
		if (section.getAttribute('data-ltxe-opn-title')) {
			return section.getAttribute('data-ltxe-opn-title');
		}
		var heading = section.querySelector('h1, h2, h3');
		if (heading && heading.textContent) {
			return heading.textContent.trim();
		}
		return section.id || 'Section';
	}

	function ensureId(section, index) {
		if (section.id) {
			return section.id;
		}
		var id = 'ltxe-opn-sec-' + index;
		section.id = id;
		return id;
	}

	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-opn]').forEach(function (nav) {
			if (nav.getAttribute('data-ltxe-opn-ready') === '1') {
				return;
			}
			nav.setAttribute('data-ltxe-opn-ready', '1');
			var cfg = parseCfg(nav);
			var selector = cfg.selector || '.elementor-section';
			var list = nav.querySelector('.ltxe-opn__list');
			if (!list) {
				return;
			}
			var sections = [];
			try {
				sections = Array.prototype.slice.call(document.querySelectorAll(selector));
			} catch (e) {
				sections = [];
			}
			if (cfg.tooltip) {
				nav.classList.add('ltxe-opn--tips');
			}
			list.innerHTML = '';
			sections.forEach(function (section, index) {
				var id = ensureId(section, index);
				var title = sectionTitle(section);
				var li = document.createElement('li');
				var btn = document.createElement('button');
				btn.type = 'button';
				btn.className = 'ltxe-opn__btn';
				btn.setAttribute('data-target', id);
				btn.setAttribute('aria-label', title);
				var dot = document.createElement('span');
				dot.className = 'ltxe-opn__dot';
				btn.appendChild(dot);
				if (cfg.tooltip) {
					var tip = document.createElement('span');
					tip.className = 'ltxe-opn__tip';
					tip.textContent = title;
					btn.appendChild(tip);
				}
				btn.addEventListener('mouseenter', function () {
					btn.classList.add('is-tip');
				});
				btn.addEventListener('mouseleave', function () {
					btn.classList.remove('is-tip');
				});
				btn.addEventListener('focus', function () {
					btn.classList.add('is-tip');
				});
				btn.addEventListener('blur', function () {
					btn.classList.remove('is-tip');
				});
				btn.addEventListener('click', function () {
					var target = document.getElementById(id);
					if (!target) {
						return;
					}
					var top = target.getBoundingClientRect().top + window.pageYOffset - (parseInt(cfg.offset, 10) || 0);
					window.scrollTo({
						top: top,
						behavior: cfg.smooth && !(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) ? 'smooth' : 'auto',
					});
				});
				li.appendChild(btn);
				list.appendChild(li);
			});

			if (!('IntersectionObserver' in window) || !sections.length) {
				var first = nav.querySelector('.ltxe-opn__btn');
				if (first) {
					first.classList.add('is-active');
				}
				return;
			}

			var observer = new IntersectionObserver(
				function (entries) {
					entries.forEach(function (entry) {
						if (!entry.isIntersecting) {
							return;
						}
						var id = entry.target.id;
						nav.querySelectorAll('.ltxe-opn__btn').forEach(function (btn) {
							btn.classList.toggle('is-active', btn.getAttribute('data-target') === id);
						});
					});
				},
				{ rootMargin: '-40% 0px -40% 0px', threshold: 0 }
			);
			sections.forEach(function (section) {
				observer.observe(section);
			});
		});
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-one-page-nav.default', function ($scope) {
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
