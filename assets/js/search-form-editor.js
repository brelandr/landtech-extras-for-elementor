( function( $ ) {
	'use strict';

	/**
	 * Optional REST hydration for saved slugs when options were trimmed (500 cap).
	 * Primary term lists are embedded in control options in PHP for Elementor Select2.
	 */
	var config = window.landtechExtrasSearchFormEditor || {};
	var restUrl = config.restUrl || '';
	var restNonce = config.restNonce || ( window.wpApiSettings && window.wpApiSettings.nonce ) || '';

	function controlNameToTaxonomy( controlName ) {
		if ( ! controlName || typeof controlName !== 'string' ) {
			return '';
		}
		var match = controlName.match( /^filter_(.+)_(include|exclude)$/ );
		if ( ! match ) {
			return '';
		}
		return match[ 1 ].replace( /_/g, '-' );
	}

	function hydrateSelectedSlugs( $select ) {
		var taxonomy = controlNameToTaxonomy( $select.attr( 'data-setting' ) || '' );
		var selected = $select.val();
		if ( ! selected || ! taxonomy || ! restUrl ) {
			return;
		}
		var slugs = Array.isArray( selected ) ? selected.join( ',' ) : String( selected );
		if ( ! slugs ) {
			return;
		}

		$.ajax( {
			url: restUrl,
			dataType: 'json',
			data: { taxonomy: taxonomy, slugs: slugs },
			beforeSend: function( xhr ) {
				if ( restNonce ) {
					xhr.setRequestHeader( 'X-WP-Nonce', restNonce );
				}
			},
		} ).done( function( response ) {
			if ( ! response || ! response.results ) {
				return;
			}
			response.results.forEach( function( row ) {
				if ( $select.find( 'option[value="' + row.id + '"]' ).length ) {
					return;
				}
				$select.append( new Option( row.text, row.id, true, true ) );
			} );
			$select.trigger( 'change' );
		} );
	}

	function hydrateSearchFormSelects( $scope ) {
		$scope.find( 'select.elementor-select2[data-setting*="filter_"][data-setting*="_include"], select.elementor-select2[data-setting*="filter_"][data-setting*="_exclude"]' ).each( function() {
			var $select = $( this );
			if ( $select.data( 'ltxeHydratedSlugs' ) ) {
				return;
			}
			$select.data( 'ltxeHydratedSlugs', true );
			hydrateSelectedSlugs( $select );
		} );
	}

	function bindPanelHooks() {
		if ( ! window.elementor || ! elementor.hooks ) {
			return;
		}

		elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model ) {
			if ( ! model || 'ee-search-form' !== model.get( 'widgetType' ) ) {
				return;
			}
			var $panel = panel.$el ? panel.$el : $( panel );
			setTimeout( function() {
				hydrateSearchFormSelects( $panel );
			}, 300 );
		} );
	}

	$( window ).on( 'elementor:init', bindPanelHooks );
}( jQuery ) );
