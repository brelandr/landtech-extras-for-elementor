( function () {
	'use strict';

	function parseConfig( el ) {
		try {
			return JSON.parse( el.getAttribute( 'data-ltxe-btt' ) || '{}' );
		} catch ( e ) {
			return {};
		}
	}

	function progress( el ) {
		var ring = el.querySelector( '.ltxe-btt__progress' );
		if ( ! ring ) {
			return;
		}
		var doc = document.documentElement;
		var max = doc.scrollHeight - window.innerHeight;
		var pct = max > 0 ? ( window.scrollY / max ) * 100 : 0;
		ring.style.strokeDashoffset = String( 100 - pct );
	}

	function bind( el ) {
		if ( el.getAttribute( 'data-ltxe-btt-init' ) ) {
			return;
		}
		el.setAttribute( 'data-ltxe-btt-init', '1' );
		var cfg = parseConfig( el );
		var after = parseInt( cfg.after, 10 ) || 300;
		var behavior = ( 'instant' === cfg.behavior ) ? 'instant' : 'smooth';
		if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
			behavior = 'instant';
		}

		function onScroll() {
			if ( window.scrollY >= after ) {
				el.classList.add( 'is-visible' );
			} else {
				el.classList.remove( 'is-visible' );
			}
			progress( el );
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();

		el.addEventListener( 'click', function () {
			window.scrollTo( { top: 0, behavior: behavior } );
		} );
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-btt' ).forEach( bind );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-back-to-top.default', function ( $scope ) {
				init( $scope[ 0 ] );
			} );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			init( document );
		} );
	} else {
		init( document );
	}
}() );
