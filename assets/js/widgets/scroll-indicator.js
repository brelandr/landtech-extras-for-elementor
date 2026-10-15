( function ( $ ) {
	'use strict';

	function bindIndicator( $scope, args ) {
		var $widget = $scope.find( '.ee-scroll-indicator' );

		function destroy() {
			if ( $widget.data( 'scrollIndicator' ) ) {
				$widget.data( 'scrollIndicator' ).destroy();
			}
		}

		destroy();
		$widget.scrollIndicator( args );
		window.ee.onElementRemove( $scope, destroy );
	}

	function scrollOffset( settings ) {
		return settings.scroll_offset ? settings.scroll_offset.size : 0;
	}

	function initList( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		bindIndicator( $scope, {
			scope: window.ee.getWindow(),
			progress: 'circle',
			click: 'yes' === settings.click,
			scrollOffset: scrollOffset( settings ),
		} );
	}

	function initBar( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		bindIndicator( $scope, {
			scope: window.ee.getWindow(),
			mode: 'anchor',
			progress: 'background',
			click: 'yes' === settings.click,
			scrollOffset: scrollOffset( settings ),
		} );
	}

	function initBullets( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		var skin = window.ee.getElementSkin( $scope );
		skin = ( '' !== skin && 'default' !== skin ) ? skin + '_' : '';

		if ( 'yes' === settings[ skin + 'tooltips' ] && window.ee.initTooltips ) {
			window.ee.initTooltips( $scope );
		}

		bindIndicator( $scope, {
			scope: window.ee.getWindow(),
			autoHover: 'yes' === settings[ skin + 'show_on_focus' ],
			progress: 'background',
			click: 'yes' === settings.click,
			property: 'height',
			scrollOffset: scrollOffset( settings ),
		} );
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-scroll-indicator.list', initList );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-scroll-indicator.bar', initBar );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-scroll-indicator.bullets', initBullets );
	} );
}( jQuery ) );
