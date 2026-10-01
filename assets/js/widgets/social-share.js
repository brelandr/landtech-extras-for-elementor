( function () {
	'use strict';

	function popup( url ) {
		var w = 600;
		var h = 400;
		var left = Math.max( 0, ( window.screen.width - w ) / 2 );
		var top = Math.max( 0, ( window.screen.height - h ) / 2 );
		window.open( url, '_blank', 'width=' + w + ',height=' + h + ',left=' + left + ',top=' + top );
	}

	function copiedTip( btn ) {
		var tip = document.createElement( 'span' );
		tip.className = 'ltxe-share__copied';
		tip.textContent = 'Copied!';
		btn.appendChild( tip );
		window.setTimeout( function () {
			if ( tip.parentNode ) {
				tip.parentNode.removeChild( tip );
			}
		}, 2000 );
	}

	window.ltxeActions = window.ltxeActions || {};

	window.ltxeActions['share-popup'] = function ( ev, btn ) {
		if ( ! btn || ! btn.closest( '.ltxe-share' ) ) {
			return;
		}
		ev.preventDefault();
		popup( btn.href );
	};

	window.ltxeActions['copy-link'] = function ( ev, btn ) {
		if ( ! btn || ! btn.closest( '.ltxe-share' ) ) {
			return;
		}
		ev.preventDefault();
		var url = btn.getAttribute( 'data-ltxe-url' ) || window.location.href;
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( url ).then( function () {
				copiedTip( btn );
			} );
		}
	};

	window.ltxeActions.print = function ( ev, btn ) {
		if ( ! btn || ! btn.closest( '.ltxe-share' ) ) {
			return;
		}
		ev.preventDefault();
		window.print();
	};

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-share' ).forEach( function ( el ) {
			el.setAttribute( 'data-ltxe-share-init', '1' );
		} );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-social-share.default', function ( $scope ) {
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
