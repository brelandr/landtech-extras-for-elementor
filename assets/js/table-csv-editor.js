( function( $ ) {
	'use strict';

	function parseCsv( text ) {
		var lines = String( text || '' ).split( /\r?\n/ ).filter( function( line ) {
			return '' !== $.trim( line );
		} );

		return lines.map( function( line ) {
			if ( line.indexOf( '\t' ) !== -1 ) {
				return line.split( '\t' );
			}
			return line.split( ',' ).map( function( cell ) {
				return $.trim( cell.replace( /^"|"$/g, '' ) );
			} );
		} );
	}

	function buildRowsFromMatrix( matrix, skipHeader ) {
		var rows = [];
		var start = skipHeader ? 1 : 0;
		var r;

		for ( r = start; r < matrix.length; r++ ) {
			rows.push( { type: 'row' } );
			matrix[ r ].forEach( function( value ) {
				rows.push( {
					type: 'cell',
					cell_type: 'td',
					cell_content: 'text',
					cell_text: value,
				} );
			} );
		}

		return rows;
	}

	function onCsvImport( panel, model, view ) {
		var settings = model.get( 'settings' );
		var csv = settings.get( 'csv_import_data' );

		if ( ! csv || ! $.trim( csv ) ) {
			return;
		}

		var matrix = parseCsv( csv );
		if ( ! matrix.length ) {
			return;
		}

		var skipHeader = matrix.length > 1;
		var bodyRows = buildRowsFromMatrix( matrix, skipHeader );
		var headerCells = [];

		if ( skipHeader ) {
			headerCells = matrix[ 0 ].map( function( label ) {
				return {
					cell_content: 'text',
					cell_text: label,
				};
			} );
		}

		settings.set( 'rows', bodyRows );
		if ( headerCells.length ) {
			settings.set( 'header_cells', headerCells );
		}
		settings.set( 'csv_import_data', '' );
	}

	$( window ).on( 'elementor:init', function() {
		elementor.hooks.addAction( 'panel/open_editor/widget/table', function( panel, model, view ) {
			panel.$el.on( 'click.ltxeTableCsv', '[data-event="landtech_extras:table:csv_import"]', function( event ) {
				event.preventDefault();
				onCsvImport( panel, model, view );
			} );
		} );
	} );
}( jQuery ) );
