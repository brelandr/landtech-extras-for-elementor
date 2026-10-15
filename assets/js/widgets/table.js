( function ( $ ) {
	'use strict';

	function initPagination( $wrap, $tbl ) {
		var perPage = parseInt( $wrap.data( 'rows-per-page' ), 10 ) || 10,
			$rows = $tbl.find( 'tbody > tr' ),
			$nav = $wrap.find( '.ee-table-pagination' ),
			$prev = $nav.find( '.ee-table-pagination__prev' ),
			$next = $nav.find( '.ee-table-pagination__next' ),
			$status = $nav.find( '.ee-table-pagination__status' ),
			page = 0,
			totalPages = Math.max( 1, Math.ceil( $rows.length / perPage ) );

		function renderPage() {
			$rows.hide();
			$rows.slice( page * perPage, ( page + 1 ) * perPage ).show();
			$prev.prop( 'disabled', 0 === page );
			$next.prop( 'disabled', page >= totalPages - 1 );
			if ( $status.length ) {
				$status.text( ( page + 1 ) + ' / ' + totalPages );
			}
		}

		$prev.off( 'click.ltxeTablePager' ).on( 'click.ltxeTablePager', function () {
			if ( page > 0 ) {
				page--;
				renderPage();
			}
		} );

		$next.off( 'click.ltxeTablePager' ).on( 'click.ltxeTablePager', function () {
			if ( page < totalPages - 1 ) {
				page++;
				renderPage();
			}
		} );

		renderPage();
	}

	function initTable( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		var $wrapper = $scope.find( '.ee-table-wrapper' );
		var $table = $scope.find( 'table.ee-table' );

		if ( 'yes' === settings.sortable ) {
			$table.tablesorter( {
				cssHeader: 'ee-table__sort',
				cssAsc: 'ee-table__sort--up',
				cssDesc: 'ee-table__sort--down',
			} );
		} else {
			$table.removeData( 'tablesorter' );
		}

		if ( 'yes' === settings.pagination ) {
			initPagination( $wrapper, $table );
		}
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/table.default', initTable );
	} );
}( jQuery ) );
