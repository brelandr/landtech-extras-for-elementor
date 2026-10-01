( function () {
	'use strict';

	var MQ = {
		mobile: '(max-width: 767px)',
		tablet: '(min-width: 768px) and (max-width: 1024px)',
		desktop: '(min-width: 1025px)',
	};

	function parseConfig( el ) {
		try {
			return JSON.parse( el.getAttribute( 'data-ltxe-sticky' ) || '{}' );
		} catch ( e ) {
			return {};
		}
	}

	function shouldStick( on ) {
		var list = Array.isArray( on ) && on.length ? on : [ 'desktop', 'tablet' ];
		if ( ! window.matchMedia ) {
			return -1 !== list.indexOf( 'desktop' );
		}
		var i;
		for ( i = 0; i < list.length; i++ ) {
			if ( MQ[ list[ i ] ] && window.matchMedia( MQ[ list[ i ] ] ).matches ) {
				return true;
			}
		}
		return false;
	}

	function adminOffset() {
		var bar = document.getElementById( 'wpadminbar' );
		return bar ? bar.offsetHeight : 0;
	}

	function readTop( el, fallback ) {
		var raw = window.getComputedStyle( el ).getPropertyValue( '--ltxe-sticky-top' );
		var n = parseInt( raw, 10 );
		return isNaN( n ) ? fallback : n;
	}

	function findContainer( el ) {
		return el.closest( '.elementor-top-section' ) ||
			el.closest( '.elementor-section' ) ||
			el.closest( '.e-con' ) ||
			el.closest( '.elementor-widget-wrap' ) ||
			el.closest( '.elementor-column' ) ||
			el.parentElement;
	}

	function bind( el ) {
		if ( el.getAttribute( 'data-ltxe-sticky-init' ) ) {
			return;
		}
		el.setAttribute( 'data-ltxe-sticky-init', '1' );
		el.classList.add( 'ltxe-sticky--js' );

		var cfg = parseConfig( el );
		var on = cfg.on;
		var extraTopFallback = parseInt( cfg.top, 10 );
		if ( isNaN( extraTopFallback ) ) {
			extraTopFallback = 30;
		}
		var z = parseInt( cfg.z, 10 );
		if ( isNaN( z ) ) {
			z = 100;
		}
		el.style.setProperty( '--ltxe-sticky-z', String( z ) );

		var container = findContainer( el );
		if ( ! container ) {
			return;
		}

		var placeholder = document.createElement( 'div' );
		placeholder.className = 'ltxe-sticky__ph';
		placeholder.setAttribute( 'aria-hidden', 'true' );
		el.parentNode.insertBefore( placeholder, el );

		var state = {
			stuck: false,
			naturalTop: 0,
			width: 0,
			left: 0,
		};

		function release() {
			el.style.position = '';
			el.style.top = '';
			el.style.left = '';
			el.style.width = '';
			el.style.zIndex = '';
			placeholder.style.display = 'none';
			placeholder.style.height = '';
			placeholder.style.width = '';
			state.stuck = false;
		}

		function pin( topPx ) {
			el.style.position = 'fixed';
			el.style.top = topPx + 'px';
			el.style.left = state.left + 'px';
			el.style.width = state.width + 'px';
			el.style.zIndex = String( z );
			placeholder.style.display = 'block';
			placeholder.style.height = el.offsetHeight + 'px';
			placeholder.style.width = state.width + 'px';
			state.stuck = true;
		}

		function measure() {
			var wasStuck = state.stuck;
			if ( wasStuck ) {
				release();
			}
			var rect = el.getBoundingClientRect();
			state.naturalTop = rect.top + window.scrollY;
			state.width = el.offsetWidth;
			state.left = rect.left + window.scrollX;
			if ( wasStuck ) {
				onScroll();
			}
		}

		function onScroll() {
			if ( ! shouldStick( on ) ) {
				if ( state.stuck ) {
					release();
				}
				return;
			}

			var extraTop = readTop( el, extraTopFallback );
			var top = extraTop + adminOffset();
			var start = state.naturalTop - top;
			var elH = el.offsetHeight;
			var containerBottom = container.getBoundingClientRect().bottom;
			var maxTop = containerBottom - elH;

			if ( window.scrollY < start ) {
				if ( state.stuck ) {
					release();
				}
				return;
			}

			if ( maxTop < top ) {
				pin( maxTop );
				return;
			}

			pin( top );
		}

		measure();
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', function () {
			measure();
			onScroll();
		} );
		onScroll();
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-sticky' ).forEach( bind );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-sticky-wrapper.default', function ( $scope ) {
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
