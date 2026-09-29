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
	document.addEventListener( 'click', function ( e ) {
		var node = e.target.closest ? e.target.closest( '[data-ltxe-action]' ) : null;
		var action = node ? node.getAttribute( 'data-ltxe-action' ) : '';
		if ( action && window.ltxeActions[ action ] ) {
			window.ltxeActions[ action ]( e, node );
		}
	} );
}( jQuery, window ) );
