( function ( window ) {
	'use strict';

	window.ltxeActions = window.ltxeActions || {};

	document.addEventListener( 'click', function ( e ) {
		var node = e.target.closest ? e.target.closest( '[data-ltxe-action]' ) : null;
		var action = node && node.dataset ? node.dataset.ltxeAction : '';
		if ( action && window.ltxeActions[ action ] ) {
			window.ltxeActions[ action ]( e, node );
		}
	} );

	window.ltxeActions['copy-link'] = function ( e, node ) {
		e.preventDefault();
		node.setAttribute( 'data-ltxe-copied', '1' );
	};

	window.ltxeActions.print = function ( e, node ) {
		e.preventDefault();
		node.setAttribute( 'data-ltxe-printed', '1' );
	};

	window.ltxeActions['share-popup'] = function ( e, node ) {
		e.preventDefault();
		node.setAttribute( 'data-ltxe-shared', '1' );
	};

	document.querySelectorAll( '.ltxe-cq-wrapper[data-ltxe-cq]' ).forEach( function ( el ) {
		el.style.setProperty( 'container-type', 'inline-size' );
		el.style.setProperty( 'container-name', el.getAttribute( 'data-ltxe-cq' ) );
	} );
}( window ) );
