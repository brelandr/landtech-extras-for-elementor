/**
 * Elementor editor notice: Gravity Forms Styler is in Premium.
 * Enqueued only from the editor hook. Never loaded on the frontend.
 */
( function () {
	'use strict';

	var cfg = window.landtechExtrasGfUpsell;
	if ( ! cfg || ! cfg.message || ! cfg.url ) {
		return;
	}

	var dismissed = false;

	function mount() {
		if ( dismissed ) {
			return true;
		}
		var cat = document.getElementById( 'elementor-panel-category-landtech-extras' );
		if ( ! cat || cat.querySelector( '.ltxe-gf-upsell' ) ) {
			return !! cat && !! cat.querySelector( '.ltxe-gf-upsell' );
		}
		var items = cat.querySelector( '.elementor-panel-category-items' ) || cat;
		var box = document.createElement( 'div' );
		box.className = 'ltxe-gf-upsell';
		box.setAttribute( 'role', 'status' );

		var text = document.createElement( 'p' );
		text.appendChild( document.createTextNode( cfg.message + ' ' ) );
		var link = document.createElement( 'a' );
		link.href = cfg.url;
		link.target = '_blank';
		link.rel = 'noopener noreferrer';
		link.textContent = cfg.linkLabel || 'LandTech Extras Premium';
		text.appendChild( link );
		text.appendChild( document.createTextNode( '.' ) );

		var button = document.createElement( 'button' );
		button.type = 'button';
		button.className = 'ltxe-gf-upsell__dismiss';
		button.textContent = cfg.dismiss || 'Dismiss';
		button.addEventListener( 'click', function () {
			button.disabled = true;
			var body = new URLSearchParams();
			body.set( 'action', cfg.action || 'landtech_extras_dismiss_gf_upsell' );
			body.set( 'nonce', cfg.nonce || '' );
			window.fetch( cfg.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: body.toString()
			} ).then( function ( response ) {
				if ( ! response.ok ) {
					button.disabled = false;
					return;
				}
				dismissed = true;
				box.remove();
			} ).catch( function () {
				button.disabled = false;
			} );
		} );

		box.appendChild( text );
		box.appendChild( button );
		items.insertBefore( box, items.firstChild );
		return true;
	}

	function boot() {
		if ( mount() ) {
			return;
		}
		var tries = 0;
		var timer = window.setInterval( function () {
			tries += 1;
			if ( mount() || tries > 40 ) {
				window.clearInterval( timer );
			}
		}, 250 );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
