( function () {
	'use strict';

	function setVerticalPosition( el, pct ) {
		var image = el.querySelector( '.ee-image-comparison__image' );
		var handle = el.querySelector( '.ltxe-ic__handle, .ee-image-comparison__handle' );
		if ( ! image ) {
			return;
		}
		var clipped = Math.max( 10, Math.min( 90, pct ) );
		image.style.clipPath = 'inset(0 0 ' + ( 100 - clipped ) + '% 0)';
		image.style.height = '100%';
		image.style.width = '100%';
		if ( handle && 'INPUT' === handle.tagName ) {
			handle.value = String( Math.round( clipped ) );
			return;
		}
		if ( ! handle ) {
			return;
		}
		handle.style.top = clipped + '%';
		handle.style.left = '50%';
		handle.style.transform = 'translate(-50%, -50%)';
	}

	function initVertical( el ) {
		if ( el.getAttribute( 'data-ltxe-vertical-init' ) ) {
			return;
		}
		el.setAttribute( 'data-ltxe-vertical-init', '1' );
		el.classList.add( 'is--visible' );

		var start = parseFloat( el.getAttribute( 'data-start' ) || '50' );
		setVerticalPosition( el, start );

		var dragging = false;

		function fromEvent( ev ) {
			var rect = el.getBoundingClientRect();
			var y = ( ev.touches && ev.touches[ 0 ] ) ? ev.touches[ 0 ].clientY : ev.clientY;
			return ( ( y - rect.top ) / rect.height ) * 100;
		}

		function onMove( ev ) {
			if ( ! dragging ) {
				return;
			}
			ev.preventDefault();
			setVerticalPosition( el, fromEvent( ev ) );
		}

		function onUp() {
			dragging = false;
		}

		el.addEventListener( 'pointerdown', function ( ev ) {
			dragging = true;
			el.setPointerCapture( ev.pointerId );
			setVerticalPosition( el, fromEvent( ev ) );
		} );
		el.addEventListener( 'pointermove', onMove );
		el.addEventListener( 'pointerup', onUp );
		el.addEventListener( 'pointercancel', onUp );
	}

	function applyPosition( el, pct ) {
		var now = Math.max( 0, Math.min( 100, pct ) );
		var handle = el.querySelector( '.ltxe-ic__handle, .ee-image-comparison__handle' );
		if ( handle && 'INPUT' === handle.tagName ) {
			handle.value = String( Math.round( now ) );
		}
		if ( el.classList.contains( 'ltxe-img-comparison--vertical' ) ) {
			setVerticalPosition( el, now );
			return;
		}
		var image = el.querySelector( '.ee-image-comparison__image' );
		if ( image ) {
			image.style.width = now + '%';
		}
		if ( handle && 'INPUT' !== handle.tagName ) {
			handle.style.left = now + '%';
		}
	}

	function applyStart( el ) {
		if ( el.classList.contains( 'ltxe-img-comparison--vertical' ) ) {
			initVertical( el );
		}
		var start = parseFloat( el.getAttribute( 'data-start' ) || '' );
		if ( isNaN( start ) ) {
			start = 50;
		}
		applyPosition( el, start );
	}

	function bindKeyboard( el ) {
		var handle = el.querySelector( '.ltxe-ic__handle, .ee-image-comparison__handle' );
		if ( ! handle || handle.getAttribute( 'data-ltxe-keys' ) ) {
			return;
		}
		handle.setAttribute( 'data-ltxe-keys', '1' );
		handle.addEventListener( 'input', function () {
			applyPosition( el, parseFloat( handle.value ) );
		} );
		handle.addEventListener( 'keydown', function ( ev ) {
			if ( 'INPUT' === handle.tagName && 'range' === handle.type ) {
				return;
			}
			var now = parseFloat( handle.getAttribute( 'aria-valuenow' ) || handle.value || el.getAttribute( 'data-start' ) || '50' );
			var step = 5;
			if ( 'ArrowLeft' === ev.key || 'ArrowDown' === ev.key ) {
				now -= step;
			} else if ( 'ArrowRight' === ev.key || 'ArrowUp' === ev.key ) {
				now += step;
			} else {
				return;
			}
			ev.preventDefault();
			applyPosition( el, Math.max( 10, Math.min( 90, now ) ) );
		} );
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ee-image-comparison' ).forEach( function ( el ) {
			applyStart( el );
			bindKeyboard( el );
		} );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/image-comparison.default', function ( $scope ) {
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
