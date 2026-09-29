( function () {
	'use strict';

	function pauseVideos( scope ) {
		if ( ! window.matchMedia || ! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
			return;
		}
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-cta__video' ).forEach( function ( video ) {
			video.pause();
			video.removeAttribute( 'autoplay' );
		} );
	}

	function init( scope ) {
		pauseVideos( scope );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-cta.default', function ( $scope ) {
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
