( function () {
	'use strict';

	function initOne( root ) {
		if ( root.getAttribute( 'data-ltxe-team-init' ) ) {
			return;
		}
		root.setAttribute( 'data-ltxe-team-init', '1' );

		root.querySelectorAll( '.ltxe-team-card' ).forEach( function ( card ) {
			var flipBtn = card.querySelector( '.ltxe-team-card__flip-btn' );
			function setFlipped( on ) {
				card.classList.toggle( 'is-flipped', on );
				if ( flipBtn ) {
					flipBtn.textContent = on ? ( flipBtn.getAttribute( 'data-hide' ) || '' ) : ( flipBtn.getAttribute( 'data-show' ) || '' );
				}
			}
			if ( flipBtn ) {
				flipBtn.addEventListener( 'click', function () {
					setFlipped( ! card.classList.contains( 'is-flipped' ) );
				} );
			}
			if ( root.classList.contains( 'ltxe-team--overlay' ) ) {
				card.addEventListener( 'click', function ( e ) {
					if ( e.target.closest( 'a' ) ) {
						return;
					}
					card.classList.toggle( 'is-open' );
				} );
			}
		} );
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-team' ).forEach( initOne );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-team-members.default', function ( $scope ) {
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
