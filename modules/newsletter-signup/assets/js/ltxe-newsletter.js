(function () {
	'use strict';
	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('[data-ltxe-news]').forEach(function (form) {
			if (form.getAttribute('data-ready') === '1') { return; }
			form.setAttribute('data-ready', '1');
			var cfg; try { cfg = JSON.parse(form.getAttribute('data-ltxe-news') || '{}'); } catch (e) { cfg = {}; }
			var msg = form.querySelector('.ltxe-news__msg');
			var btn = form.querySelector('.ltxe-news__btn');
			form.addEventListener('submit', function (ev) {
				ev.preventDefault();
				if (!cfg.rest) { return; }
				var data = {
					email: (form.querySelector('[name="email"]') || {}).value || '',
					first_name: (form.querySelector('[name="first_name"]') || {}).value || '',
					last_name: (form.querySelector('[name="last_name"]') || {}).value || '',
					phone: (form.querySelector('[name="phone"]') || {}).value || '',
					audience_id: cfg.audience || '',
					gdpr_consent: form.querySelector('[name="gdpr_consent"]') ? !!form.querySelector('[name="gdpr_consent"]').checked : true,
					double_optin: !!cfg.doubleOptin
				};
				if (btn) { btn.disabled = true; }
				if (msg) { msg.hidden = true; msg.className = 'ltxe-news__msg'; }
				fetch(cfg.rest, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.nonce || '' },
					body: JSON.stringify(data)
				}).then(function (r) { return r.json().then(function (j) { return { ok: r.ok, json: j }; }); })
					.then(function (res) {
						if (msg) {
							msg.hidden = false;
							if (res.ok) {
								msg.className = 'ltxe-news__msg is-ok';
								msg.textContent = cfg.success || 'Thanks';
							} else {
								msg.className = 'ltxe-news__msg is-err';
								msg.textContent = (res.json && res.json.message) ? res.json.message : (cfg.error || 'Error');
							}
						}
					})
					.catch(function () {
						if (msg) { msg.hidden = false; msg.className = 'ltxe-news__msg is-err'; msg.textContent = cfg.error || 'Error'; }
					})
					.finally(function () { if (btn) { btn.disabled = false; } });
			});
		});
	}
	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-newsletter-signup.default', function ($s) { init($s[0]); });
			}
		});
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', function () { init(document); }); } else { init(document); }
})();
