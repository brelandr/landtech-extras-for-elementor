/**
 * Weather widget — fetch via REST proxy, swap condition backgrounds.
 */
(function () {
	'use strict';

	function prefersReduced() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function parseConfig(el) {
		try {
			return JSON.parse(el.getAttribute('data-ltxe-weather') || '{}');
		} catch (e) {
			return {};
		}
	}

	function setText(root, selector, value) {
		var node = root.querySelector(selector);
		if (node) {
			node.textContent = value;
		}
	}

	function unitSym(units) {
		return units === 'fahrenheit' ? '°F' : '°C';
	}

	function windUnit(units) {
		return units === 'fahrenheit' ? 'mph' : 'km/h';
	}

	function applyPayload(el, data, cfg) {
		if (!data || !data.condition) {
			return;
		}
		var next = String(data.condition);
		var prev = el.getAttribute('data-ltxe-condition') || '';
		var units = cfg.units || data.units || 'celsius';
		var sym = unitSym(units);

		if (data.city) {
			setText(el, '.ltxe-weather__city', data.city);
		}
		if (data.temperature != null) {
			setText(el, '.ltxe-weather__temp', data.temperature + sym);
		}
		if (data.description) {
			setText(el, '.ltxe-weather__desc', data.description);
		}

		var feel = el.querySelector('[data-field="feels_like"] strong');
		if (feel && data.feels_like != null) {
			feel.textContent = data.feels_like + sym;
		}
		var hum = el.querySelector('[data-field="humidity"] strong');
		if (hum && data.humidity != null) {
			hum.textContent = data.humidity + '%';
		}
		var wind = el.querySelector('[data-field="wind"] strong');
		if (wind && data.wind != null) {
			wind.textContent = data.wind + ' ' + windUnit(units);
		}
		var uv = el.querySelector('[data-field="uv"] strong');
		if (uv && data.uv != null) {
			uv.textContent = String(data.uv);
		}

		var forecast = el.querySelector('.ltxe-weather__forecast');
		if (forecast && cfg.forecast && data.forecast && data.forecast.length) {
			forecast.innerHTML = '';
			data.forecast.forEach(function (day) {
				var li = document.createElement('li');
				li.className = 'ltxe-weather__day';
				li.setAttribute('data-ltxe-condition', day.condition || 'cloudy');
				var date = document.createElement('span');
				date.className = 'ltxe-weather__day-date';
				date.textContent = day.date || '';
				var range = document.createElement('span');
				range.className = 'ltxe-weather__day-range';
				var max = day.temp_max != null ? day.temp_max + sym : '—';
				var min = day.temp_min != null ? day.temp_min + sym : '—';
				range.textContent = min + ' / ' + max;
				li.appendChild(date);
				li.appendChild(range);
				forecast.appendChild(li);
			});
		}

		var icon = el.querySelector('.ltxe-weather__icon');
		if (icon) {
			icon.setAttribute('data-ltxe-icon', next);
		}

		el.querySelectorAll('.ltxe-weather__video').forEach(function (vid) {
			var on = vid.getAttribute('data-ltxe-cond') === next;
			vid.classList.toggle('is-active', on);
			if (on && typeof vid.play === 'function') {
				vid.play().catch(function () {
					/* Autoplay may be blocked; muted loop is best-effort. */
				});
			}
		});

		if (prev === next) {
			return;
		}

		var bg = el.querySelector('.ltxe-weather__bg');
		if (!prefersReduced() && bg && window.ltxeAnimeV4Params && window.anime && typeof window.anime.animate === 'function') {
			window.anime.animate(bg, window.ltxeAnimeV4Params({
				opacity: [1, 0.2],
				duration: 180,
				easing: 'linear',
				complete: function () {
					el.setAttribute('data-ltxe-condition', next);
					window.anime.animate(bg, window.ltxeAnimeV4Params({
						opacity: [0.2, 1],
						duration: 220,
						easing: 'linear',
					}));
				},
			}));
			return;
		}

		el.setAttribute('data-ltxe-condition', next);
	}

	function fetchWeather(el, extra) {
		var cfg = parseConfig(el);
		var rest = el.getAttribute('data-ltxe-weather-rest') || '';
		if (!rest) {
			return Promise.resolve(null);
		}
		var params = new URLSearchParams();
		var city = extra && extra.city != null ? extra.city : cfg.city;
		var lat = extra && extra.lat != null ? extra.lat : cfg.lat;
		var lon = extra && extra.lon != null ? extra.lon : cfg.lon;
		if (city) {
			params.set('city', city);
		}
		if (lat) {
			params.set('lat', lat);
		}
		if (lon) {
			params.set('lon', lon);
		}
		params.set('units', cfg.units || 'celsius');
		var sep = rest.indexOf('?') === -1 ? '?' : '&';
		return fetch(rest + sep + params.toString(), { credentials: 'same-origin' })
			.then(function (res) {
				if (!res.ok) {
					throw new Error('weather');
				}
				return res.json();
			})
			.then(function (data) {
				applyPayload(el, data, cfg);
				return data;
			})
			.catch(function () {
				return null;
			});
	}

	function initOne(el) {
		if (!el || el.getAttribute('data-ltxe-weather-ready') === '1') {
			return;
		}
		el.setAttribute('data-ltxe-weather-ready', '1');
		var cfg = parseConfig(el);

		fetchWeather(el);

		var refresh = parseInt(el.getAttribute('data-ltxe-refresh'), 10);
		if (!refresh) {
			refresh = parseInt(cfg.refresh, 10) || 0;
		}
		if (refresh > 0) {
			window.setInterval(function () {
				fetchWeather(el);
			}, refresh);
		}

		var geo = el.querySelector('.ltxe-weather__geo');
		if (geo && cfg.geolocate && navigator.geolocation) {
			geo.addEventListener('click', function () {
				navigator.geolocation.getCurrentPosition(function (pos) {
					fetchWeather(el, {
						city: '',
						lat: String(pos.coords.latitude),
						lon: String(pos.coords.longitude),
					});
				});
			});
		}
	}

	function init(scope) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll('.ltxe-weather').forEach(initOne);
	}

	if (window.jQuery) {
		window.jQuery(window).on('elementor/frontend/init', function () {
			if (window.elementorFrontend && elementorFrontend.hooks) {
				elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-weather.default', function ($scope) {
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

	window.ltxeWeatherInit = init;
})();
