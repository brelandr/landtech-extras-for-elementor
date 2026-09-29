( function () {
	'use strict';

	function parseConfig( el ) {
		try {
			return JSON.parse( el.getAttribute( 'data-ltxe-consent' ) || '{}' );
		} catch ( e ) {
			return {};
		}
	}

	function bind( banner ) {
		if ( banner.getAttribute( 'data-ltxe-consent-init' ) ) {
			return;
		}
		banner.setAttribute( 'data-ltxe-consent-init', '1' );
		var config = parseConfig( banner );
		var version = parseInt( config.version, 10 ) || 1;
		var days = parseInt( config.expiry_days, 10 ) || 365;
		var key = 'ltxe_consent_v' + version;
		var stored = window.localStorage.getItem( key );
		if ( stored ) {
			document.dispatchEvent( new CustomEvent( 'ltxe_consent_' + stored ) );
			return;
		}

		banner.removeAttribute( 'hidden' );

		function decide( choice ) {
			var cats = [];
			if ( 'accepted' === choice ) {
				banner.querySelectorAll( '.ltxe-consent-cat:checked' ).forEach( function ( box ) {
					cats.push( box.value );
				} );
				if ( ! cats.length && config.categories ) {
					cats = config.categories;
				}
			}
			window.localStorage.setItem( key, choice );
			window.localStorage.setItem( key + '_expires', new Date( Date.now() + days * 864e5 ).toISOString() );
			banner.setAttribute( 'hidden', '' );
			document.dispatchEvent( new CustomEvent( 'ltxe_consent_' + choice, { detail: { categories: cats } } ) );
		}

		var accept = banner.querySelector( '.ltxe-consent-accept' );
		var decline = banner.querySelector( '.ltxe-consent-decline' );
		if ( accept ) {
			accept.addEventListener( 'click', function () {
				decide( 'accepted' );
			} );
		}
		if ( decline ) {
			decline.addEventListener( 'click', function () {
				decide( 'declined' );
			} );
		}
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-cookie-consent' ).forEach( bind );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-cookie-consent.default', function ( $scope ) {
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
