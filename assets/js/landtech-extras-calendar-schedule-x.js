( function( $ ) {
	'use strict';

	function ltxePatchTemporalZonedFrom() {
		if ( ! window.Temporal || ! window.Temporal.ZonedDateTime || window.Temporal.ZonedDateTime.__ltxePatched ) {
			return;
		}
		var origFrom = window.Temporal.ZonedDateTime.from;
		if ( 'function' !== typeof origFrom ) {
			return;
		}
		window.Temporal.ZonedDateTime.from = function( item, options ) {
			if ( 'string' === typeof item && /^\d{4}-\d{2}-\d{2}$/.test( item ) ) {
				item = item + 'T00:00:00[UTC]';
			}
			return origFrom.call( this, item, options );
		};
		window.Temporal.ZonedDateTime.__ltxePatched = true;
	}

	function ltxeDatePart( value ) {
		if ( ! value ) {
			return '';
		}
		var match = String( value ).match( /(\d{4}-\d{2}-\d{2})/ );
		return match ? match[ 1 ] : '';
	}

	function ltxePaintFallbackMonth( mount, events ) {
		if ( ! mount || mount.querySelector( '.sx__month-grid-day, .ltxe-cal-fallback__day' ) ) {
			return;
		}

		var now = new Date();
		var year = now.getFullYear();
		var month = now.getMonth();
		var first = new Date( year, month, 1 );
		var daysInMonth = new Date( year, month + 1, 0 ).getDate();
		var mondayOffset = ( first.getDay() + 6 ) % 7;
		var labels = [ 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ];
		var eventDates = {};
		( events || [] ).forEach( function( event ) {
			var start = ltxeDatePart( event.start );
			if ( start ) {
				eventDates[ start ] = ( eventDates[ start ] || 0 ) + 1;
			}
		} );

		var grid = document.createElement( 'div' );
		grid.className = 'ltxe-cal-fallback';
		grid.setAttribute( 'role', 'grid' );

		labels.forEach( function( label ) {
			var head = document.createElement( 'div' );
			head.className = 'ltxe-cal-fallback__dow';
			head.textContent = label;
			grid.appendChild( head );
		} );

		var cell;
		var i;
		for ( i = 0; i < mondayOffset; i++ ) {
			cell = document.createElement( 'div' );
			cell.className = 'ltxe-cal-fallback__day is-pad';
			grid.appendChild( cell );
		}

		for ( i = 1; i <= daysInMonth; i++ ) {
			cell = document.createElement( 'div' );
			var iso = year + '-' + String( month + 1 ).padStart( 2, '0' ) + '-' + String( i ).padStart( 2, '0' );
			cell.className = 'ltxe-cal-fallback__day';
			if ( i === now.getDate() ) {
				cell.classList.add( 'is-today' );
			}
			if ( eventDates[ iso ] ) {
				cell.classList.add( 'has-events' );
			}
			cell.textContent = String( i );
			grid.appendChild( cell );
		}

		mount.appendChild( grid );
	}

	window.ltxeInitScheduleXCalendar = function( $scope, settings ) {
		if ( 'undefined' === typeof window.SXCalendar || 'function' !== typeof window.SXCalendar.createCalendar ) {
			return;
		}

		if ( 'undefined' === typeof window.Temporal || 'undefined' === typeof window.Temporal.PlainDate ) {
			return;
		}

		ltxePatchTemporalZonedFrom();

		var $calendar = $scope.hasClass( 'ee-calendar' ) ? $scope : $scope.find( '.ee-calendar' );
		if ( ! $calendar.length ) {
			return;
		}
		if ( $calendar.data( 'ltxeCalInit' ) ) {
			return;
		}
		$calendar.data( 'ltxeCalInit', true );

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
			var startPart = ltxeDatePart( $event.data( 'start' ) );
			var start = ltxePlainDateFromSetting( startPart || $event.data( 'start' ) );
			if ( ! start ) {
				return;
			}
			var end = ltxePlainDateFromSetting( ltxeDatePart( $event.data( 'end' ) ) || $event.data( 'end' ) ) || start;

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

		var monthView = window.SXCalendar.createViewMonthGrid
			? window.SXCalendar.createViewMonthGrid()
			: window.SXCalendar.viewMonthGrid;
		var views = [ monthView ];
		if ( ( 'compact' === settings._skin || 'compact' === settings.skin ) && window.SXCalendar.viewWeek ) {
			views.push( window.SXCalendar.viewWeek );
		}

		var config = {
			views: views,
			events: events,
			defaultView: 'month-grid',
			firstDayOfWeek: ltxeScheduleXFirstDayOfWeek( settings.first_day ),
			timezone: 'UTC',
			isResponsive: false,
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
			window.setTimeout( function() {
				var mountNode = $mount.get( 0 );
				if ( mountNode && ! mountNode.querySelector( '.sx__month-grid-day' ) ) {
					ltxePaintFallbackMonth( mountNode, events );
				}
			}, 250 );
		} catch ( error ) {
			$calendar.data( 'ltxeCalInit', false );
			ltxePaintFallbackMonth( $mount.get( 0 ), events );
		}
	};

	function ltxeReadCalendarSettings( $scope ) {
		if ( window.LtxeUtils && 'function' === typeof window.LtxeUtils.getSettings ) {
			return window.LtxeUtils.getSettings( $scope ) || {};
		}
		var node = $scope && $scope.length ? $scope.get( 0 ) : null;
		if ( ! node ) {
			return {};
		}
		try {
			return JSON.parse( node.getAttribute( 'data-settings' ) || '{}' );
		} catch ( e ) {
			return {};
		}
	}

	function ltxeBootScheduleXCalendars( scope ) {
		var $root = scope && scope.jquery ? scope : $( scope || document );
		var $widgets = $root.find( '.elementor-widget-ee-calendar' ).addBack( '.elementor-widget-ee-calendar' );
		if ( ! $widgets.length && $root.hasClass && $root.hasClass( 'elementor-widget-ee-calendar' ) ) {
			$widgets = $root;
		}
		if ( ! $widgets.length ) {
			$widgets = $root.find( '.ee-calendar' ).closest( '.elementor-widget-ee-calendar' );
		}
		$widgets.each( function() {
			var $scope = $( this );
			window.ltxeInitScheduleXCalendar( $scope, ltxeReadCalendarSettings( $scope ) );
		} );
	}

	$( window ).on( 'elementor/frontend/init', function() {
		if ( window.elementorFrontend && elementorFrontend.hooks ) {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-calendar.default', function( $scope ) {
				window.ltxeInitScheduleXCalendar( $scope, ltxeReadCalendarSettings( $scope ) );
			} );
		}
	} );

	$( function() {
		ltxeBootScheduleXCalendars( document );
	} );
}( jQuery ) );
