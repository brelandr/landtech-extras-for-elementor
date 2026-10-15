( function ( $ ) {
	'use strict';

	function markPressed( $wrapper ) {
		$wrapper.find( '.ee-toggle-element__controls__item' ).each( function () {
			var $item = $( this );
			window.ee.makeFocusableControl( $item, $.trim( $item.text() ) || 'Toggle' );
			$item.attr( 'aria-pressed', $item.hasClass( 'ee--is-active' ) ? 'true' : 'false' );
		} );
	}

	function initToggle( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		var $wrapper = $scope.find( '.ee-toggle-element' );
		var toggleElementArgs = {
			active: settings.toggle_active_index || 1,
			inactive: 'yes' == settings.toggle_inactive,
			hashToggle: 'yes' == settings.toggle_hash,
			hashLoad: 'yes' == settings.toggle_hash_load,
			onAfterToggle: function ( $currentElement ) {
				if ( 'yes' === settings.refresh_widgets ) {
					window.ee.refreshElements( $currentElement, false );
				}

				window.ee.fixSwipers( $currentElement );
				markPressed( $wrapper );
			},
		};

		if ( '' !== settings.indicator_color ) {
			toggleElementArgs.indicatorColor = settings.indicator_color;
		}

		if ( settings.indicator_speed.size ) {
			toggleElementArgs.speed = settings.indicator_speed.size;
		}

		if ( elementorFrontend.isEditMode() ) {
			toggleElementArgs.watchControls = true;
		}

		$wrapper.toggleElement( toggleElementArgs );
		markPressed( $wrapper );
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-toggle-element.classic', initToggle );
	} );
}( jQuery ) );
