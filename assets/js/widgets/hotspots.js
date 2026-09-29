( function ( $ ) {
	'use strict';

	function readSettings( $scope ) {
		var settings = {};
		if ( window.elementorFrontend && typeof elementorFrontend.getElementSettings === 'function' && $scope.data( 'id' ) ) {
			try {
				settings = elementorFrontend.getElementSettings( $scope ) || {};
			} catch ( e ) {
				settings = $scope.data( 'settings' ) || {};
			}
		} else {
			settings = $scope.data( 'settings' ) || {};
		}
		return settings || {};
	}

	function alreadyBound( $hotspots ) {
		var first = $hotspots.get( 0 );
		if ( ! first ) {
			return false;
		}
		if ( $hotspots.first().data( 'hotips' ) ) {
			return true;
		}
		var events = $._data( first, 'events' );
		return ! ! ( events && ( events.click || events.mouseenter || events.touchstart || events.hotip ) );
	}

	function initScope( $scope ) {
		if ( ! $scope || ! $scope.length || ! $.fn.hotips ) {
			return;
		}
		var $hotspots = $scope.find( '.hotip' );
		if ( ! $hotspots.length || alreadyBound( $hotspots ) ) {
			return;
		}

		var settings = readSettings( $scope );
		var trigger = settings.trigger || 'click_target';
		var hide = settings._hide || 'click_out';

		$hotspots.hotips( {
			id: $scope.data( 'id' ),
			position: settings.position || 'bottom',
			arrowPositionH: settings.arrow_position_h || 'center',
			arrowPositionV: settings.arrow_position_v || 'center',
			trigger: {
				desktop: trigger,
				tablet: settings.trigger_tablet || trigger,
				mobile: settings.trigger_mobile || trigger,
			},
			hide: {
				desktop: hide,
				tablet: settings._hide_tablet || hide,
				mobile: settings._hide_mobile || hide,
			},
		} );
	}

	function initAll() {
		$( '.elementor-widget-hotspots, .elementor-element[data-widget_type="hotspots.default"]' ).each( function () {
			initScope( $( this ) );
		} );
	}

	$( window ).on( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && elementorFrontend.hooks ) {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/hotspots.default', function ( $scope ) {
				initScope( $scope );
			} );
		}
		window.setTimeout( initAll, 50 );
	} );

	$( function () {
		window.setTimeout( initAll, 150 );
	} );

	$( window ).on( 'load', function () {
		window.setTimeout( initAll, 50 );
	} );
}( jQuery ) );
