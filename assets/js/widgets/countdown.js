( function () {
	'use strict';

	function pad( n, usePad ) {
		var s = String( n );
		if ( usePad && s.length < 2 ) {
			return '0' + s;
		}
		return s;
	}

	function diffParts( seconds ) {
		var days = Math.floor( seconds / 86400 );
		var hours = Math.floor( ( seconds % 86400 ) / 3600 );
		var minutes = Math.floor( ( seconds % 3600 ) / 60 );
		var secs = Math.floor( seconds % 60 );
		return { days: days, hours: hours, minutes: minutes, seconds: secs };
	}

	function initOne( el ) {
		if ( el.getAttribute( 'data-ltxe-cd-init' ) ) {
			return;
		}
		el.setAttribute( 'data-ltxe-cd-init', '1' );

		var config;
		try {
			config = JSON.parse( el.getAttribute( 'data-ltxe-countdown' ) || '{}' );
		} catch ( e ) {
			return;
		}

		var expiredEl = el.querySelector( '.ltxe-countdown__expired' );
		var unitsEl = el.querySelector( '.ltxe-countdown__units' );
		var timer;

		function expire() {
			if ( timer ) {
				window.clearInterval( timer );
			}
			if ( 'hide' === config.expire ) {
				el.setAttribute( 'hidden', 'hidden' );
				return;
			}
			if ( 'redirect' === config.expire && config.url ) {
				window.location.assign( config.url );
				return;
			}
			if ( unitsEl ) {
				unitsEl.setAttribute( 'hidden', 'hidden' );
			}
			if ( expiredEl ) {
				expiredEl.removeAttribute( 'hidden' );
				expiredEl.textContent = config.message || '';
			}
		}

		function tick() {
			var now = Math.floor( Date.now() / 1000 );
			var left = ( config.end || 0 ) - now;
			if ( left <= 0 ) {
				expire();
				return;
			}
			var parts = diffParts( left );
			[ 'days', 'hours', 'minutes', 'seconds' ].forEach( function ( key ) {
				var node = el.querySelector( '[data-digit="' + key + '"]' );
				if ( node ) {
					node.textContent = pad( parts[ key ], !! config.pad );
				}
			} );
		}

		tick();
		timer = window.setInterval( tick, 1000 );
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-countdown' ).forEach( initOne );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-countdown.default', function ( $scope ) {
				init( $scope[ 0 ] );
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			init( document );
		} );
	} else {
		init( document );
	}
}() );
