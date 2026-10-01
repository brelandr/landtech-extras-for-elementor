/**
 * Media Gallery — filter bar, optional Isotope, GLightbox.
 */
( function () {
	'use strict';

	function init( root ) {
		if ( ! root || root.getAttribute( 'data-ready' ) === '1' ) {
			return;
		}
		root.setAttribute( 'data-ready', '1' );
		var grid = root.querySelector( '.ltxe-mg__grid' );
		var buttons = root.querySelectorAll( '.ltxe-mg__filter' );
		var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		var iso = null;
		function startIsotope() {
			if ( ! grid || ! window.Isotope || iso ) {
				return;
			}
			var proLayout = root.getAttribute( 'data-pro-layout' );
			var layout = root.getAttribute( 'data-layout' );
			/*
			 * CSS grid and column-count own these layouts. Isotope measured each
			 * item at 100% width and stacked Grid and Masonry in a single column.
			 */
			if ( 'mosaic' === proLayout || 'justified' === proLayout || 'grid' === layout || 'masonry' === layout || 'justified' === layout ) {
				return;
			}
			iso = new window.Isotope( grid, {
				itemSelector: '.ltxe-mg__item',
				layoutMode: root.getAttribute( 'data-layout' ) === 'masonry' ? 'masonry' : 'fitRows',
				transitionDuration: reduce ? 0 : '0.35s'
			} );
		}
		if ( grid && window.imagesLoaded ) {
			window.imagesLoaded( grid, startIsotope );
		} else {
			startIsotope();
		}
		if ( root.getAttribute( 'data-lazy' ) === '1' && 'IntersectionObserver' in window ) {
			var observer = new IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( ! entry.isIntersecting ) {
						return;
					}
					var img = entry.target;
					var src = img.getAttribute( 'data-src' );
					if ( src ) {
						img.setAttribute( 'src', src );
						img.removeAttribute( 'data-src' );
					}
					observer.unobserve( img );
					if ( iso ) {
						iso.layout();
					}
				} );
			} );
			root.querySelectorAll( 'img[data-src]' ).forEach( function ( img ) {
				observer.observe( img );
			} );
		}
		buttons.forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var filter = button.getAttribute( 'data-filter' ) || '*';
				buttons.forEach( function ( other ) {
					var on = other === button;
					other.classList.toggle( 'is-active', on );
					other.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
				} );
				if ( iso ) {
					iso.arrange( { filter: filter } );
					return;
				}
				root.querySelectorAll( '.ltxe-mg__item' ).forEach( function ( item ) {
					var show = filter === '*' || item.matches( filter );
					if ( show ) {
						item.removeAttribute( 'hidden' );
					} else {
						item.setAttribute( 'hidden', '' );
					}
				} );
			} );
		} );
		if ( root.getAttribute( 'data-lightbox' ) === '1' && window.GLightbox ) {
			window.GLightbox( {
				selector: '.ltxe-mg__link[data-gallery="' + root.getAttribute( 'data-gallery' ) + '"]',
				loop: root.getAttribute( 'data-loop' ) !== '0',
				touchNavigation: true,
				skin: root.getAttribute( 'data-theme' ) === 'light' ? 'clean' : 'modern'
			} );
		}
	}

	function boot( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-mg' ).forEach( init );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-media-gallery.default', function ( $scope ) {
					boot( $scope[ 0 ] );
				} );
			}
		} );
	}
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () { boot( document ); } );
	} else {
		boot( document );
	}
}() );
