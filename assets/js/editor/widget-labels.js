( function ( $ ) {
	'use strict';

	if ( ! window.elementor ) {
		return;
	}

	var cfg = window.landtechExtrasWidgetLabels || {};
	var ltxeLabelTimer;

	function ltxeSuggestLabel( type, content ) {
		var lower = String( content || '' ).toLowerCase();
		var map = {
			service: 'Services',
			contact: 'Contact',
			about: 'About',
			faq: 'FAQ',
			team: 'Team',
			pricing: 'Pricing',
			testimonial: 'Testimonials',
			welcome: 'Welcome',
			hero: 'Hero',
			blog: 'Blog',
			news: 'News',
		};
		var keyword;
		for ( keyword in map ) {
			if ( Object.prototype.hasOwnProperty.call( map, keyword ) && lower.indexOf( keyword ) !== -1 ) {
				return map[ keyword ] + ( type === 'heading' || type === 'heading-extended' ? ' Heading' : ' Section' );
			}
		}
		var typeMap = {
			'ltxe-cta': 'Call to Action',
			'ltxe-testimonials': 'Testimonials',
			'ltxe-team-members': 'Team Members',
			'ltxe-faq': 'FAQ Section',
			'ltxe-pricing-table': 'Pricing Table',
			'ltxe-icon-box': 'Feature Box',
			'ltxe-social-share': 'Social Share',
			'ltxe-cookie-consent': 'Cookie Consent',
			'ltxe-dark-mode': 'Dark Mode',
		};
		return typeMap[ type ] || null;
	}

	function collectContent( settings ) {
		if ( ! settings || ! settings.get ) {
			return '';
		}
		var keys = [ 'title', 'heading', 'text', 'editor', 'content', 'description', 'label', 'name', 'question', 'tab_title' ];
		var parts = [];
		var i;
		for ( i = 0; i < keys.length; i++ ) {
			var val = settings.get( keys[ i ] );
			if ( 'string' === typeof val && val ) {
				parts.push( val );
			}
		}
		return parts.join( ' ' ).replace( /<[^>]+>/g, ' ' ).replace( /\s+/g, ' ' ).trim();
	}

	function applyCssId( model, panel, slug ) {
		if ( ! slug ) {
			return;
		}
		model.setSetting( '_element_id', slug );
		if ( panel && panel.$el ) {
			panel.$el.find( '[data-setting="_element_id"]' ).val( slug ).trigger( 'input' ).trigger( 'change' );
		}
		if ( elementor.saver && elementor.saver.setFlagEditorChange ) {
			elementor.saver.setFlagEditorChange();
		}
	}

	function requestCssId( model, panel, button ) {
		var settings = model.get( 'settings' );
		var payload = {
			widget_type: model.get( 'widgetType' ) || '',
			content: collectContent( settings ),
		};
		var labels = cfg.i18n || {};
		button.disabled = true;
		button.textContent = labels.working || 'Generating…';

		window.fetch( cfg.restUrl, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': cfg.restNonce || '',
			},
			body: JSON.stringify( payload ),
		} ).then( function ( res ) {
			return res.json().then( function ( body ) {
				return { ok: res.ok, body: body };
			} );
		} ).then( function ( result ) {
			if ( result.ok && result.body && result.body.slug ) {
				applyCssId( model, panel, result.body.slug );
				button.textContent = labels.done || 'CSS ID applied';
				return;
			}
			button.textContent = ( result.body && result.body.message ) ? result.body.message : ( labels.error || 'Could not generate a CSS ID.' );
		} ).catch( function () {
			button.textContent = labels.error || 'Could not generate a CSS ID.';
		} ).then( function () {
			window.setTimeout( function () {
				button.disabled = false;
				button.textContent = labels.button || 'Generate CSS ID';
			}, 2500 );
		} );
	}

	function injectHeaderButton( panel, model ) {
		if ( ! panel || ! panel.$el ) {
			return;
		}
		var heading = panel.$el.find( '.elementor-panel-heading' ).first();
		if ( ! heading.length ) {
			heading = panel.$el.find( '#elementor-panel-header-title' ).closest( '.elementor-panel-heading, .elementor-panel-menu-item, header' );
		}
		if ( ! heading.length ) {
			heading = panel.$el.find( '.elementor-controls-stack' ).first();
		}
		if ( ! heading.length || heading.find( '.ltxe-ai-widget-label' ).length ) {
			return;
		}
		var labels = cfg.i18n || {};
		var btn = $( '<button type="button" class="elementor-button ltxe-ai-widget-label">' + ( labels.button || 'Generate CSS ID' ) + '</button>' );
		btn.on( 'click', function () {
			requestCssId( model, panel, btn[ 0 ] );
		} );
		heading.append( btn );
	}

	elementor.hooks.addAction( 'panel/open_editor/widget', function ( panel, model ) {
		if ( ! model || ! model.get ) {
			return;
		}

		window.setTimeout( function () {
			injectHeaderButton( panel, model );
		}, 120 );

		var settings = model.get( 'settings' );
		var label = collectContent( settings );
		var type = model.get( 'widgetType' );
		if ( model.get( '_title' ) || ! label ) {
			return;
		}
		window.clearTimeout( ltxeLabelTimer );
		ltxeLabelTimer = window.setTimeout( function () {
			var suggestion = ltxeSuggestLabel( type, label );
			if ( ! suggestion || ! panel.$el ) {
				return;
			}
			if ( panel.$el.find( '.ltxe-label-suggest' ).length ) {
				return;
			}
			var row = $( '<div class="ltxe-label-suggest"><button type="button" class="elementor-button">' + suggestion + '</button></div>' );
			row.find( 'button' ).on( 'click', function () {
				model.setSetting( '_title', suggestion );
				if ( elementor.saver && elementor.saver.setFlagEditorChange ) {
					elementor.saver.setFlagEditorChange();
				}
				row.remove();
			} );
			panel.$el.find( '.elementor-panel-heading' ).first().after( row );
		}, 800 );
	} );
}( window.jQuery ) );
