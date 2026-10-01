( function ( $, window ) {
	'use strict';

	window.LtxeUtils = window.LtxeUtils || {
		getSettings: function ( element ) {
			var el = element && element.jquery ? element[ 0 ] : element;
			if ( ! el ) {
				return {};
			}
			var raw = el.getAttribute( 'data-settings' ) || el.getAttribute( 'data-element_type' );
			try {
				return JSON.parse( el.getAttribute( 'data-settings' ) || '{}' );
			} catch ( e ) {
				return {};
			}
		},
		onReady: function ( callback ) {
			if ( 'loading' === document.readyState ) {
				document.addEventListener( 'DOMContentLoaded', callback );
			} else {
				callback();
			}
		},
		prefersReducedMotion: function () {
			return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		},
		debounce: function ( fn, ms ) {
			var t;
			return function () {
				var ctx = this;
				var args = arguments;
				window.clearTimeout( t );
				t = window.setTimeout( function () {
					fn.apply( ctx, args );
				}, ms );
			};
		},
	};

	window.ltxeActions = window.ltxeActions || {};

	/**
	 * U32 — single delegated click listener. Widgets emit data-ltxe-action
	 * instead of onclick / javascript: hrefs so script-src can omit unsafe-inline.
	 */
	document.addEventListener( 'click', function ( e ) {
		var node = e.target.closest ? e.target.closest( '[data-ltxe-action]' ) : null;
		var action = node && node.dataset ? node.dataset.ltxeAction : ( node ? node.getAttribute( 'data-ltxe-action' ) : '' );
		if ( action && window.ltxeActions[ action ] ) {
			window.ltxeActions[ action ]( e, node );
		}
	} );

	/**
	 * Apply values that used to live in HTML style="" via CSSOM (allowed without style-src unsafe-inline).
	 *
	 * @param {ParentNode} root Scope.
	 * @return {void}
	 */
	function applyCspSafeStyles( root ) {
		var scope = root && root.querySelectorAll ? root : document;
		scope.querySelectorAll( '.ltxe-cq-wrapper[data-ltxe-cq]' ).forEach( function ( el ) {
			var name = el.getAttribute( 'data-ltxe-cq' );
			if ( name ) {
				el.style.setProperty( 'container-type', 'inline-size' );
				el.style.setProperty( 'container-name', name );
			}
		} );
		scope.querySelectorAll( '[data-ltxe-style-max-width]' ).forEach( function ( el ) {
			el.style.maxWidth = el.getAttribute( 'data-ltxe-style-max-width' );
		} );
		scope.querySelectorAll( '[data-ltxe-hero-image]' ).forEach( function ( el ) {
			var url = el.getAttribute( 'data-ltxe-hero-image' );
			if ( url ) {
				el.style.backgroundImage = 'linear-gradient(135deg,rgba(15,23,42,.88),rgba(30,64,175,.72)),url("' + url.replace( /"/g, '' ) + '")';
			}
		} );
	}

	window.LtxeUtils.applyCspSafeStyles = applyCspSafeStyles;

	function bootCspStyles() {
		applyCspSafeStyles( document );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', bootCspStyles );
	} else {
		bootCspStyles();
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) {
					applyCspSafeStyles( $scope[ 0 ] );
				} );
			}
		} );
	}
}( jQuery, window ) );
