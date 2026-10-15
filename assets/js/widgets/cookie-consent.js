( function () {
	'use strict';

	var STORE = 'ltxe_consent';

	function parseConfig( el ) {
		try {
			return JSON.parse( el.getAttribute( 'data-ltxe-consent' ) || '{}' );
		} catch ( e ) {
			return {};
		}
	}

	function readRaw() {
		try {
			return window.localStorage.getItem( STORE );
		} catch ( e ) {
			return null;
		}
	}

	function writeRaw( record ) {
		try {
			window.localStorage.setItem( STORE, JSON.stringify( record ) );
		} catch ( e ) {
			return;
		}
	}

	function normalize( record, version, days ) {
		if ( ! record || 'object' !== typeof record ) {
			return null;
		}
		var choice = record.choice;
		if ( 'accepted' !== choice && 'declined' !== choice ) {
			return null;
		}
		var storedVersion = parseInt( record.version, 10 ) || 0;
		if ( storedVersion !== version ) {
			return null;
		}
		var ts = parseInt( record.ts, 10 ) || 0;
		if ( ! ts || Date.now() > ts + days * 864e5 ) {
			return null;
		}
		var categories = Array.isArray( record.categories ) ? record.categories : [];
		return {
			choice: choice,
			categories: categories,
			ts: ts,
			version: storedVersion,
		};
	}

	function readLegacy( version, days, categories ) {
		var key = 'ltxe_consent_v' + version;
		var choice;
		var expires;
		try {
			choice = window.localStorage.getItem( key );
			expires = window.localStorage.getItem( key + '_expires' );
		} catch ( e ) {
			return null;
		}
		if ( 'accepted' !== choice && 'declined' !== choice ) {
			return null;
		}
		if ( expires ) {
			var end = Date.parse( expires );
			if ( ! isNaN( end ) && Date.now() > end ) {
				return null;
			}
		}
		return {
			choice: choice,
			categories: 'accepted' === choice ? ( categories || [] ) : [],
			ts: Date.now(),
			version: version,
		};
	}

	function publish( record ) {
		window.ltxeConsent = window.ltxeConsent || {};
		window.ltxeConsent.record = record;
		window.ltxeConsent.allows = function ( category ) {
			if ( ! record || 'accepted' !== record.choice ) {
				return false;
			}
			if ( 'functional' === category ) {
				return true;
			}
			return record.categories.indexOf( category ) !== -1;
		};
		document.dispatchEvent( new CustomEvent( 'ltxe_consent_changed', { detail: record } ) );
		if ( record ) {
			document.dispatchEvent( new CustomEvent( 'ltxe_consent_' + record.choice, { detail: { categories: record.categories } } ) );
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
		var manage = banner.parentNode ? banner.parentNode.querySelector( '.ltxe-consent-manage' ) : null;
		if ( ! manage ) {
			manage = document.querySelector( '.ltxe-consent-manage' );
		}

		var record = normalize( ( function () {
			try {
				return JSON.parse( readRaw() || 'null' );
			} catch ( e ) {
				return null;
			}
		}() ), version, days );

		if ( ! record ) {
			record = readLegacy( version, days, config.categories );
			if ( record ) {
				writeRaw( record );
			}
		}

		function showManage( on ) {
			if ( ! manage ) {
				return;
			}
			if ( on ) {
				manage.removeAttribute( 'hidden' );
			} else {
				manage.setAttribute( 'hidden', '' );
			}
		}

		function applyBoxes( categories ) {
			banner.querySelectorAll( '.ltxe-consent-cat' ).forEach( function ( box ) {
				box.checked = categories && categories.indexOf( box.value ) !== -1;
			} );
		}

		function openBanner() {
			banner.removeAttribute( 'hidden' );
			showManage( false );
			if ( record ) {
				applyBoxes( record.categories );
			}
		}

		if ( record ) {
			publish( record );
			showManage( true );
		} else {
			publish( null );
			openBanner();
		}

		function decide( choice ) {
			var cats = [];
			if ( 'accepted' === choice ) {
				var boxes = banner.querySelectorAll( '.ltxe-consent-cat' );
				if ( boxes.length ) {
					boxes.forEach( function ( box ) {
						if ( box.checked ) {
							cats.push( box.value );
						}
					} );
				} else if ( config.categories ) {
					cats = config.categories.slice();
				}
			}
			record = {
				choice: choice,
				categories: cats,
				ts: Date.now(),
				version: version,
			};
			writeRaw( record );
			banner.setAttribute( 'hidden', '' );
			showManage( true );
			publish( record );
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
		if ( manage ) {
			manage.addEventListener( 'click', openBanner );
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
