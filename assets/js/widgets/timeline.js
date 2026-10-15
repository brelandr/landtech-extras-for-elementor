( function ( $ ) {
	'use strict';

	function initTimeline( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		var $timeline = $scope.find( '.ee-timeline' );
		var timelineArgs = {};

		if ( elementorFrontend.isEditMode() ) {
			timelineArgs.scope = window.elementor.$previewContents;
		}

		if ( 'undefined' !== typeof settings.line_location && settings.line_location.size ) {
			timelineArgs.lineLocation = settings.line_location.size;
		}

		$timeline.eeTimeline( timelineArgs );
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/timeline.default', initTimeline );
	} );
}( jQuery ) );
