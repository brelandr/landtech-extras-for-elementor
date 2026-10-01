( function () {
	'use strict';

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	}

	function initPagination( wrapper ) {
		var type = wrapper.getAttribute( 'data-pagination' ) || 'none';
		if ( 'none' === type ) {
			return;
		}

		var perPage = parseInt( wrapper.getAttribute( 'data-per-page' ), 10 ) || 12;
		var items = Array.prototype.slice.call( wrapper.querySelectorAll( '.ee-gallery__item' ) );
		if ( items.length <= perPage ) {
			return;
		}

		var shown = 0;

		function hideFrom( start ) {
			items.forEach( function ( item, i ) {
				item.style.display = i < start ? '' : 'none';
			} );
		}

		function showNext() {
			shown = Math.min( items.length, shown + perPage );
			hideFrom( shown );
			var btn = wrapper.querySelector( '.ee-gallery__load-more' );
			if ( btn && shown >= items.length ) {
				btn.setAttribute( 'hidden', 'hidden' );
			}
		}

		function showPage( page ) {
			var start = page * perPage;
			var end = Math.min( items.length, start + perPage );
			items.forEach( function ( item, i ) {
				item.style.display = ( i >= start && i < end ) ? '' : 'none';
			} );
			var nav = wrapper.querySelector( '.ee-gallery__pagination--numbers' );
			if ( ! nav ) {
				return;
			}
			var buttons = nav.querySelectorAll( 'button' );
			buttons.forEach( function ( b, i ) {
				b.setAttribute( 'aria-current', i === page ? 'page' : 'false' );
			} );
		}

		if ( 'numbers' === type ) {
			var pages = Math.ceil( items.length / perPage );
			var nav = wrapper.querySelector( '.ee-gallery__pagination--numbers' );
			if ( nav ) {
				nav.innerHTML = '';
				for ( var p = 0; p < pages; p++ ) {
					var b = document.createElement( 'button' );
					b.type = 'button';
					b.className = 'ee-gallery__page';
					b.textContent = String( p + 1 );
					b.setAttribute( 'aria-label', 'Page ' + ( p + 1 ) );
					( function ( pageIndex ) {
						b.addEventListener( 'click', function () {
							showPage( pageIndex );
						} );
					}( p ) );
					nav.appendChild( b );
				}
			}
			showPage( 0 );
			return;
		}

		shown = perPage;
		hideFrom( shown );

		if ( 'load_more' === type ) {
			var more = wrapper.querySelector( '.ee-gallery__load-more' );
			if ( more ) {
				more.addEventListener( 'click', showNext );
			}
		}

		if ( 'infinite_scroll' === type ) {
			var ticking = false;
			window.addEventListener( 'scroll', function () {
				if ( ticking || shown >= items.length ) {
					return;
				}
				ticking = true;
				window.requestAnimationFrame( function () {
					ticking = false;
					var rect = wrapper.getBoundingClientRect();
					if ( rect.bottom - window.innerHeight < 200 ) {
						showNext();
					}
				} );
			}, { passive: true } );
		}
	}

	function initVideoLightbox( wrapper ) {
		if ( typeof window.GLightbox !== 'function' ) {
			return;
		}
		var videos = wrapper.querySelectorAll( '.ee-gallery__media--video' );
		if ( ! videos.length ) {
			return;
		}
		if ( wrapper.getAttribute( 'data-ltxe-glightbox' ) ) {
			return;
		}
		wrapper.setAttribute( 'data-ltxe-glightbox', '1' );
		window.GLightbox( {
			selector: '.elementor-element-' + ( wrapper.closest( '.elementor-element' ) ? wrapper.closest( '.elementor-element' ).getAttribute( 'data-id' ) : '' ) + ' .ee-gallery__media--video',
			touchNavigation: true,
			loop: false,
		} );
	}

	function init( scope ) {
		var root = scope && scope.nodeType ? scope : document.body;
		var wrappers = root.querySelectorAll( '.ee-gallery-wrapper' );
		wrappers.forEach( function ( wrapper ) {
			if ( wrapper.getAttribute( 'data-ltxe-gallery-init' ) ) {
				return;
			}
			wrapper.setAttribute( 'data-ltxe-gallery-init', '1' );
			initPagination( wrapper );
			initVideoLightbox( wrapper );
		} );
		if ( prefersReducedMotion() ) {
			return;
		}
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/gallery-extra.default', function ( $scope ) {
				init( $scope[ 0 ] );
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			init( document.body );
		} );
	} else {
		init( document.body );
	}
}() );
