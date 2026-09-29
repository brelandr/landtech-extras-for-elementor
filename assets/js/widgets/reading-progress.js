( function () {
	'use strict';

	function parseConfig( el ) {
		try {
			return JSON.parse( el.getAttribute( 'data-ltxe-reading' ) || '{}' );
		} catch ( e ) {
			return {};
		}
	}

	function initOne( el ) {
		if ( el.getAttribute( 'data-ltxe-rp-init' ) ) {
			return;
		}
		el.setAttribute( 'data-ltxe-rp-init', '1' );

		var config = parseConfig( el );
		var bar = el.querySelector( '.ltxe-reading-progress__bar' );
		if ( ! bar ) {
			return;
		}

		var selector = ( config.selector || '' ).trim();
		var target = null;
		if ( selector && /^[.#a-zA-Z][\w\-#.[\]= ]*$/.test( selector ) ) {
			try {
				target = document.querySelector( selector );
			} catch ( e ) {
				target = null;
			}
		}

		function update() {
			var doc = document.documentElement;
			var top;
			var height;
			if ( target ) {
				var rect = target.getBoundingClientRect();
				top = rect.top + window.scrollY;
				height = target.scrollHeight - window.innerHeight;
			} else {
				top = 0;
				height = doc.scrollHeight - window.innerHeight;
			}
			if ( height <= 0 ) {
				height = 1;
			}
			var pct = Math.min( 100, Math.max( 0, ( ( window.scrollY - top ) / height ) * 100 ) );
			if ( 'vertical' === config.axis ) {
				bar.style.height = pct + '%';
				bar.style.width = '100%';
			} else {
				bar.style.width = pct + '%';
				bar.style.height = '100%';
			}
			el.setAttribute( 'aria-valuenow', String( Math.round( pct ) ) );
		}

		window.addEventListener( 'scroll', update, { passive: true } );
		window.addEventListener( 'resize', update );
		update();
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-reading-progress' ).forEach( initOne );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-reading-progress.default', function ( $scope ) {
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
