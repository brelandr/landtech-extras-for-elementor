( function () {
	'use strict';

	var STORAGE_KEY = 'ltxe_dark_mode';

	function parseConfig( el ) {
		try {
			return JSON.parse( el.getAttribute( 'data-ltxe-dark' ) || '{}' );
		} catch ( e ) {
			return {};
		}
	}

	function applyTokens( config ) {
		var root = document.documentElement;
		if ( config.bg ) {
			root.style.setProperty( '--ltxe-dark-bg', config.bg );
		}
		if ( config.text ) {
			root.style.setProperty( '--ltxe-dark-text', config.text );
		}
		if ( config.accent ) {
			root.style.setProperty( '--ltxe-dark-accent', config.accent );
		}
		if ( config.surface ) {
			root.style.setProperty( '--ltxe-dark-surface', config.surface );
		}
	}

	function applyMode( mode, config ) {
		var root = document.documentElement;
		root.setAttribute( 'data-ltxe-theme', mode );
		document.body.classList.toggle( 'ltxe-theme-dark', 'dark' === mode );
		if ( config ) {
			applyTokens( config );
			if ( 'dark' === mode ) {
				root.style.setProperty( '--ltxe-surface', config.surface || '#1e1e1e' );
				root.style.setProperty( '--ltxe-ink', config.text || '#f5f5f5' );
			} else {
				root.style.setProperty( '--ltxe-surface', '#f4f1ea' );
				root.style.setProperty( '--ltxe-ink', '#1b1913' );
			}
		}
		window.localStorage.setItem( STORAGE_KEY, mode );
		document.querySelectorAll( '.ltxe-dark-toggle' ).forEach( function ( btn ) {
			btn.setAttribute( 'aria-pressed', 'dark' === mode ? 'true' : 'false' );
		} );
	}

	function resolveInitial( config ) {
		var stored = window.localStorage.getItem( STORAGE_KEY );
		if ( stored ) {
			return stored;
		}
		if ( config.default_mode === 'system' ) {
			return window.matchMedia && window.matchMedia( '(prefers-color-scheme: dark)' ).matches ? 'dark' : 'light';
		}
		return config.default_mode === 'dark' ? 'dark' : 'light';
	}

	function bind( btn ) {
		if ( btn.getAttribute( 'data-ltxe-dark-init' ) ) {
			return;
		}
		btn.setAttribute( 'data-ltxe-dark-init', '1' );
		var config = parseConfig( btn );
		applyMode( resolveInitial( config ), config );
		btn.addEventListener( 'click', function () {
			var next = document.documentElement.getAttribute( 'data-ltxe-theme' ) === 'dark' ? 'light' : 'dark';
			applyMode( next, config );
		} );
		if ( window.matchMedia ) {
			window.matchMedia( '(prefers-color-scheme: dark)' ).addEventListener( 'change', function ( ev ) {
				if ( ! window.localStorage.getItem( STORAGE_KEY ) ) {
					applyMode( ev.matches ? 'dark' : 'light', config );
				}
			} );
		}
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-dark-toggle' ).forEach( bind );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-dark-mode.default', function ( $scope ) {
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
