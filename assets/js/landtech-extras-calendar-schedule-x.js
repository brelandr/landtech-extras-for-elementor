( function( $ ) {
	'use strict';

	window.ltxeInitScheduleXCalendar = function( $scope, settings ) {
		if ( 'undefined' === typeof window.SXCalendar || 'function' !== typeof window.SXCalendar.createCalendar ) {
			return;
		}

		if ( 'undefined' === typeof window.Temporal || 'undefined' === typeof window.Temporal.PlainDate ) {
			return;
		}

		var $calendar = $scope.find( '.ee-calendar' );
		if ( ! $calendar.length ) {
			return;
		}

		function ltxePlainDateFromSetting( value ) {
			if ( ! value ) {
				return null;
			}
			var datePart = String( value ).substring( 0, 10 );
			try {
				return window.Temporal.PlainDate.from( datePart );
			} catch ( e ) {
				return null;
			}
		}

		function ltxePlainDateFromMonthSetting( value ) {
			if ( ! value ) {
				return null;
			}
			return ltxePlainDateFromSetting( String( value ).substring( 0, 7 ) + '-01' );
		}

		function ltxeScheduleXFirstDayOfWeek( firstDay ) {
			var day = parseInt( firstDay, 10 );
			if ( isNaN( day ) || day < 0 || day > 6 ) {
				return 1;
			}
			return ( ( day + 6 ) % 7 ) + 1;
		}

		var events = [];
		$calendar.find( '.ee-calendar-event' ).each( function( index ) {
			var $event = $( this );
			var start = ltxePlainDateFromSetting( $event.data( 'start' ) );
			if ( ! start ) {
				return;
			}
			var end = ltxePlainDateFromSetting( $event.data( 'end' ) ) || start;

			events.push( {
				id: 'ltxe-' + index,
				title: $.trim( $event.text() ) || $.trim( $event.html() ),
				start: start,
				end: end,
			} );
		} );

		var $mount = $calendar.find( '.ee-calendar__mount' );
		if ( ! $mount.length ) {
			$mount = $( '<div class="ee-calendar__mount"></div>' );
			$calendar.prepend( $mount );
		}

		var views = [ window.SXCalendar.viewMonthGrid ];
		if ( 'compact' === settings._skin || 'compact' === settings.skin ) {
			views.push( window.SXCalendar.viewWeek );
		}

		var config = {
			views: views,
			events: events,
			defaultView: 'month-grid',
			firstDayOfWeek: ltxeScheduleXFirstDayOfWeek( settings.first_day ),
		};

		if ( '' === settings.default_current_month && settings.default_month ) {
			var selectedDate = ltxePlainDateFromMonthSetting( settings.default_month );
			if ( selectedDate ) {
				config.selectedDate = selectedDate;
			}
		}

		var minDate = ltxePlainDateFromMonthSetting( settings.constrain_start );
		if ( minDate ) {
			config.minDate = minDate;
		}

		var maxDate = ltxePlainDateFromMonthSetting( settings.constrain_end );
		if ( maxDate ) {
			config.maxDate = maxDate;
		}

		try {
			var app = window.SXCalendar.createCalendar( config );
			if ( app && 'function' === typeof app.render ) {
				app.render( $mount.get( 0 ) );
			}
		} catch ( error ) {
			// Schedule-X could not mount; event source nodes remain hidden.
		}
	};
}( jQuery ) );
