( function () {
	'use strict';

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	}

	function parseConfig( el ) {
		try {
			return JSON.parse( el.getAttribute( 'data-ltxe-progress' ) || '{}' );
		} catch ( e ) {
			return {};
		}
	}

	function fill( el, config ) {
		var bar = el.querySelector( '.ltxe-progress-bar__fill' );
		if ( ! bar ) {
			return;
		}
		var pct = typeof config.pct === 'number' ? config.pct : 0;
		var prop = 'vertical' === config.axis ? 'height' : 'width';
		if ( prefersReducedMotion() || ! config.animate ) {
			bar.style[ prop ] = pct + '%';
			return;
		}
		if ( typeof window.ltxeAnime === 'function' ) {
			var opts = { targets: bar, duration: config.duration || 800, easing: 'easeOutQuad' };
			opts[ prop ] = pct + '%';
			window.ltxeAnime( opts );
			return;
		}
		bar.style[ prop ] = pct + '%';
	}

	function initOne( el ) {
		if ( el.getAttribute( 'data-ltxe-pb-init' ) ) {
			return;
		}
		el.setAttribute( 'data-ltxe-pb-init', '1' );
		var config = parseConfig( el );
		if ( ! config.animate || prefersReducedMotion() ) {
			fill( el, config );
			return;
		}
		if ( 'IntersectionObserver' in window ) {
			var io = new IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						fill( el, config );
						io.disconnect();
					}
				} );
			}, { threshold: 0.3 } );
			io.observe( el );
		} else {
			fill( el, config );
		}
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-progress-bar' ).forEach( initOne );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-progress-bar.default', function ( $scope ) {
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
