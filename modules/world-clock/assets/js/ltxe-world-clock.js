(function () {
	'use strict';
	function tick(root) {
		var cfg; try { cfg = JSON.parse(root.getAttribute('data-ltxe-wclock') || '{}'); } catch (e) { cfg = {}; }
		root.querySelectorAll('.ltxe-wclock__item').forEach(function (item) {
			var tz = item.getAttribute('data-tz') || 'UTC';
			var now = new Date();
			var opts = { timeZone: tz, hour: '2-digit', minute: '2-digit', hour12: !!cfg.hour12 };
			if (item.getAttribute('data-seconds') === '1') { opts.second = '2-digit'; }
			var digital = item.querySelector('.ltxe-wclock__digital');
			if (digital) {
				try { digital.textContent = new Intl.DateTimeFormat(undefined, opts).format(now); } catch (err) { digital.textContent = now.toUTCString(); }
			}
			var dateEl = item.querySelector('.ltxe-wclock__date');
			if (dateEl && item.getAttribute('data-date') === '1') {
				try { dateEl.textContent = new Intl.DateTimeFormat(undefined, { timeZone: tz, weekday: 'short', month: 'short', day: 'numeric' }).format(now); } catch (err2) { dateEl.textContent = ''; }
			}
			var analog = item.querySelector('.ltxe-wclock__analog');
			if (analog) {
				var parts = {};
				try {
					new Intl.DateTimeFormat('en-GB', { timeZone: tz, hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: false }).formatToParts(now).forEach(function (p) { parts[p.type] = p.value; });
				} catch (err3) { return; }
				var h = parseInt(parts.hour, 10) % 12;
				var m = parseInt(parts.minute, 10);
				var s = parseInt(parts.second, 10);
				var hh = analog.querySelector('.ltxe-wclock__hand--h');
				var mm = analog.querySelector('.ltxe-wclock__hand--m');
				var ss = analog.querySelector('.ltxe-wclock__hand--s');
				if (hh) { hh.style.transform = 'rotate(' + ((h * 30) + (m * 0.5)) + 'deg)'; }
				if (mm) { mm.style.transform = 'rotate(' + (m * 6) + 'deg)'; }
				if (ss) { ss.style.transform = 'rotate(' + (s * 6) + 'deg)'; }
			}
		});
	}
	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-wclock]').forEach(function (el) {
			if (el.getAttribute('data-ready') === '1') { return; }
			el.setAttribute('data-ready', '1');
			tick(el);
			window.setInterval(function () { tick(el); }, 1000);
		});
	}
	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-world-clock.default', function ($s) { init($s[0]); });
			}
		});
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', function () { init(document); }); } else { init(document); }
})();
