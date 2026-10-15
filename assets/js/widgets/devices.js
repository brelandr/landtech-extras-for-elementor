( function ( $ ) {
	'use strict';

	function initDevices( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		var $shape = $scope.find( '.ee-device__shape' );
		var url = null;

		if ( ! settings.device_type ) {
			settings.device_type = 'phone';
		}

		url = landtechExtrasFrontendConfig.urls.assets + 'shapes/' + settings.device_type + '.svg';

		jQuery.get( url, function ( data ) {
			$shape.html( data.childNodes[ 0 ] );
		} );

		if ( 'yes' === settings.device_orientation_control ) {
			$scope.find( '.ee-device__orientation' ).on( 'click', function () {
				$scope.toggleClass( 'ee-device-orientation-landscape' );
			} );
		}

		if ( window.ee.initHtml5Video ) {
			window.ee.initHtml5Video( $scope );
		}
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/devices-extended.default', initDevices );
	} );
}( jQuery ) );
