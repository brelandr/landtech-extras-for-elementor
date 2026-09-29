( function () {
	'use strict';

	if ( ! window.elementor ) {
		return;
	}

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

	elementor.hooks.addAction( 'panel/open_editor/widget', function ( panel, model ) {
		if ( ! model || ! model.get ) {
			return;
		}
		var settings = model.get( 'settings' );
		var label = '';
		if ( settings && settings.get ) {
			label = settings.get( 'title' ) || settings.get( 'text' ) || settings.get( 'heading' ) || '';
		}
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
			var row = window.jQuery( '<div class="ltxe-label-suggest"><button type="button" class="elementor-button">' + suggestion + '</button></div>' );
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
}() );
