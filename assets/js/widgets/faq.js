( function () {
	'use strict';

	function setOpen( item, open ) {
		var btn = item.querySelector( '.ltxe-faq__question' );
		var panel = item.querySelector( '.ltxe-faq__answer' );
		if ( ! btn || ! panel ) {
			return;
		}
		item.classList.toggle( 'is-open', open );
		btn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		if ( open ) {
			panel.removeAttribute( 'hidden' );
		} else {
			panel.setAttribute( 'hidden', '' );
		}
	}

	function initOne( root ) {
		if ( root.getAttribute( 'data-ltxe-faq-init' ) ) {
			return;
		}
		root.setAttribute( 'data-ltxe-faq-init', '1' );
		var accordion = '1' === root.getAttribute( 'data-accordion' );
		var items = root.querySelectorAll( '.ltxe-faq__item' );

		items.forEach( function ( item ) {
			var btn = item.querySelector( '.ltxe-faq__question' );
			if ( ! btn ) {
				return;
			}
			btn.addEventListener( 'click', function () {
				var willOpen = 'true' !== btn.getAttribute( 'aria-expanded' );
				if ( accordion && willOpen ) {
					items.forEach( function ( other ) {
						if ( other !== item ) {
							setOpen( other, false );
						}
					} );
				}
				setOpen( item, willOpen );
			} );
			btn.addEventListener( 'keydown', function ( e ) {
				var list = Array.prototype.slice.call( root.querySelectorAll( '.ltxe-faq__question' ) );
				var i = list.indexOf( btn );
				if ( 'ArrowDown' === e.key && list[ i + 1 ] ) {
					e.preventDefault();
					list[ i + 1 ].focus();
				}
				if ( 'ArrowUp' === e.key && list[ i - 1 ] ) {
					e.preventDefault();
					list[ i - 1 ].focus();
				}
				if ( 'Home' === e.key && list[ 0 ] ) {
					e.preventDefault();
					list[ 0 ].focus();
				}
				if ( 'End' === e.key && list[ list.length - 1 ] ) {
					e.preventDefault();
					list[ list.length - 1 ].focus();
				}
			} );
		} );
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-faq' ).forEach( initOne );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-faq.default', function ( $scope ) {
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
