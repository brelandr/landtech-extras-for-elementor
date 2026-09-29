( function ( $ ) {
	'use strict';

	if ( ! window.elementor ) {
		return;
	}

	var cfg = window.landtechExtrasAiAlt || {};

	function findImageId( model ) {
		if ( ! model || ! model.get ) {
			return 0;
		}
		var settings = model.get( 'settings' );
		if ( ! settings || ! settings.get ) {
			return 0;
		}
		var keys = [ 'image', 'original_image', 'modified_image', 'selected_image', 'icon_image' ];
		for ( var i = 0; i < keys.length; i++ ) {
			var val = settings.get( keys[ i ] );
			if ( val && val.id ) {
				return parseInt( val.id, 10 ) || 0;
			}
		}
		return 0;
	}

	function requestAlt( imageId, button ) {
		button.disabled = true;
		button.textContent = 'Generating…';
		window.fetch( cfg.restUrl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': cfg.restNonce || '',
			},
			body: JSON.stringify( { image_id: imageId } ),
		} ).then( function ( res ) {
			return res.json().then( function ( body ) {
				return { ok: res.ok, body: body };
			} );
		} ).then( function ( result ) {
			if ( result.ok && result.body && result.body.alt_text ) {
				button.textContent = 'Alt text saved';
				return;
			}
			button.textContent = ( result.body && result.body.message ) ? result.body.message : 'Unavailable';
		} ).catch( function () {
			button.textContent = 'Unavailable';
		} ).then( function () {
			window.setTimeout( function () {
				button.disabled = false;
				button.textContent = 'Generate alt text';
			}, 2500 );
		} );
	}

	elementor.hooks.addAction( 'panel/open_editor/widget', function ( panel, model ) {
		window.setTimeout( function () {
			var imageId = findImageId( model );
			if ( ! imageId ) {
				return;
			}
			var wrap = panel.$el && panel.$el.find ? panel.$el.find( '.elementor-controls-stack' ).first() : null;
			if ( ! wrap || ! wrap.length || wrap.find( '.ltxe-ai-alt-btn' ).length ) {
				return;
			}
			var btn = $( '<button type="button" class="elementor-button elementor-button-success ltxe-ai-alt-btn">Generate alt text</button>' );
			btn.on( 'click', function () {
				requestAlt( imageId, btn[ 0 ] );
			} );
			wrap.prepend( btn );
		}, 200 );
	} );
}( window.jQuery ) );
