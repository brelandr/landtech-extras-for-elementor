/**
 * LandTech widget preset import/export (Elementor editor).
 */
( function ( $ ) {
	'use strict';

	function cfg() {
		return window.ltxWidgetPresetIo || {};
	}

	function i18n() {
		return cfg().i18n || {};
	}

	function getSelectedModel() {
		if ( 'undefined' === typeof elementor || ! elementor.getSelectedElement ) {
			return null;
		}
		var el = elementor.getSelectedElement();
		return el && el.model ? el.model : null;
	}

	function mountPresetIo( model ) {
		if ( ! model || ! model.get ) {
			return;
		}
		var wt = model.get( 'widgetType' ) || '';
		if ( ! wt || 0 !== String( wt ).indexOf( 'ee-' ) && 'posts-extra' !== wt ) {
			return;
		}

		$( '.ltx-widget-preset-io-slot' ).remove();

		var $stack = $( '#elementor-panel-content-wrapper .elementor-controls-stack' ).last();
		if ( ! $stack.length ) {
			return;
		}

		var strings = i18n();
		var wrap = $( '<div class="ltx-widget-preset-io-slot" />' );
		var row = $( '<div class="ltx-widget-preset-io-actions" />' );
		var btnExport = $( '<button type="button" class="elementor-button elementor-button-default" />' ).text( strings.export || 'Export' );
		var btnImport = $( '<button type="button" class="elementor-button elementor-button-default" />' ).text( strings.import || 'Import' );

		btnExport.on( 'click', function ( e ) {
			e.preventDefault();
			var settingsModel = model.get( 'settings' );
			var payload = {
				action: 'landtech_extras_export_widget_preset',
				nonce: cfg().nonce,
				document_id: elementor.config.document.id,
				widget_id: model.get( 'id' ),
				widget_type: wt,
				settings: JSON.stringify( settingsModel && settingsModel.toJSON ? settingsModel.toJSON() : {} )
			};
			btnExport.prop( 'disabled', true ).text( strings.working || '…' );
			$.post( cfg().ajaxUrl, payload )
				.done( function ( res ) {
					if ( ! res || ! res.success || ! res.data || ! res.data.preset ) {
						window.alert( strings.exportFail || '' );
						return;
					}
					var text = JSON.stringify( res.data.preset, null, 2 );
					if ( navigator.clipboard && navigator.clipboard.writeText ) {
						navigator.clipboard.writeText( text ).then(
							function () {
								window.alert( strings.exportOk || '' );
							},
							function () {
								window.prompt( strings.export || '', text );
							}
						);
						return;
					}
					window.prompt( strings.export || '', text );
				} )
				.fail( function () {
					window.alert( strings.exportFail || '' );
				} )
				.always( function () {
					btnExport.prop( 'disabled', false ).text( strings.export || 'Export' );
				} );
		} );

		btnImport.on( 'click', function ( e ) {
			e.preventDefault();
			var raw = window.prompt( strings.pasteJson || '', '' );
			if ( ! raw ) {
				return;
			}
			btnImport.prop( 'disabled', true ).text( strings.working || '…' );
			$.post( cfg().ajaxUrl, {
				action: 'landtech_extras_import_widget_preset',
				nonce: cfg().nonce,
				document_id: elementor.config.document.id,
				widget_type: wt,
				preset_json: raw
			} )
				.done( function ( res ) {
					if ( ! res || ! res.success || ! res.data || ! res.data.settings ) {
						window.alert( strings.importFail || '' );
						return;
					}
					if ( window.$e && window.$e.run ) {
						window.$e.run( 'document/elements/setSettings', {
							container: model.get( 'id' ),
							settings: res.data.settings
						} );
					} else if ( model.set ) {
						model.set( 'settings', res.data.settings );
					}
					window.alert( strings.importOk || '' );
				} )
				.fail( function () {
					window.alert( strings.importFail || '' );
				} )
				.always( function () {
					btnImport.prop( 'disabled', false ).text( strings.import || 'Import' );
				} );
		} );

		row.append( btnExport ).append( btnImport );
		wrap.append( row );
		$stack.append( wrap );
	}

	function bindHooks() {
		if ( 'undefined' === typeof elementor || ! elementor.hooks ) {
			return;
		}
		elementor.hooks.addAction( 'panel/open_editor/widget', function ( panelView, model ) {
			if ( ! model ) {
				return;
			}
			window.requestAnimationFrame( function () {
				mountPresetIo( model );
			} );
		} );
	}

	$( function () {
		bindHooks();
		$( window ).on( 'elementor:init', bindHooks );
	} );
}( jQuery ) );
