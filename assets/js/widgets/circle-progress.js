( function ( $ ) {
	'use strict';

	function countDecimals( value ) {
		if ( Math.floor( value ) === value ) {
			return 0;
		}
		return value.toString().split( '.' )[ 1 ].length || 0;
	}

	function parseValue( value, fallback ) {
		var parsed = value;

		if ( 'string' === typeof parsed ) {
			parsed = parsed.replace( /\s/g, '' );
			parsed = parsed.replace( ',', '.' );

			if ( parsed.indexOf( '/' ) > -1 ) {
				var parts = parsed.split( '/' );
				if ( ! isNaN( parts[ 0 ] ) && ! isNaN( parts[ 1 ] ) ) {
					parsed = ( parseInt( parts[ 0 ], 10 ) / parts[ 1 ] ) * 100;
					parsed = parsed.toFixed( 0 );
				}
			}
		}

		if ( ! isNaN( parsed ) ) {
			parsed = Math.abs( parseFloat( parsed ) );
		} else {
			parsed = fallback;
		}

		return parsed;
	}

	function moveDecimal( n, x ) {
		var v = n / Math.pow( 10, x );
		v = ( v > 1 ) ? Math.round( v ) : Math.round( v * Math.pow( 10, x + 1 ) ) / Math.pow( 10, x + 1 );
		return v;
	}

	function initCircle( $scope ) {
		var elementSettings = window.ee.getElementSettings( $scope );
		var $value = $scope.find( '.ee-circle-progress__value .value' );
		var progressValue = parseValue( $scope.find( '.ee-circle-progress__value' ).data( 'cp-value' ), 75 );
		var move = 0;
		var absolute = progressValue;
		var maxValue = 100;
		var decimals = countDecimals( progressValue );
		var $circle = null;
		var cpArgs = {
			value: 0.75,
			reverse: 'yes' === elementSettings.reverse,
			lineCap: elementSettings.lineCap,
			startAngle: -Math.PI,
			animation: {
				easing: elementSettings.easing,
			},
		};

		function stepValue( current, ratio, absoluteValue, shift ) {
			if ( ! current ) {
				return 0;
			}

			var next = ( 'percentage' === elementSettings.value_progress ) ? current * 100 : absoluteValue * current / ratio;
			next = next.toFixed( decimals );

			if ( shift ) {
				next = moveDecimal( next, shift );
			}

			return next;
		}

		$circle = $scope.find( '.ee-circle-progress' );
		if ( ! $circle.length ) {
			return;
		}

		if ( elementSettings.value_max ) {
			maxValue = elementSettings.value_max;
		}

		if ( 'undefined' !== typeof progressValue ) {
			if ( 'percentage' === elementSettings.value_progress ) {
				progressValue = progressValue / 100;
			} else if ( 'absolute' === elementSettings.value_progress ) {
				progressValue = progressValue / maxValue;
			}
			cpArgs.value = progressValue;
		}

		if ( 'undefined' !== typeof elementSettings.value_decimal_move ) {
			move = elementSettings.value_decimal_move.size * -1;
		}

		if ( elementSettings.size.size ) {
			cpArgs.size = elementSettings.size.size;
		}

		if ( elementSettings.thickness.size ) {
			if ( elementSettings.thickness.size > ( elementSettings.size.size / 2 ) ) {
				cpArgs.thickness = elementSettings.size.size / 2;
			} else {
				cpArgs.thickness = elementSettings.thickness.size;
			}
		}

		if ( elementSettings.angle.size ) {
			cpArgs.startAngle = cpArgs.startAngle + elementSettings.angle.size;
		}

		if ( elementSettings.emptyFill ) {
			cpArgs.emptyFill = elementSettings.emptyFill;
		}

		if ( ! elementSettings.animate ) {
			cpArgs.animation = false;
			$circle.circleProgress( cpArgs );
			$value.text( stepValue( cpArgs.value, progressValue, absolute, move ) );
		} else {
			if ( elementSettings.duration ) {
				cpArgs.animation.duration = elementSettings.duration.size;
			}

			$circle.circleProgress( cpArgs );
			$circle.off( 'circle-animation-progress' ).on( 'circle-animation-progress', function ( event, progress, current ) {
				$value.text( stepValue( current, progressValue, absolute, move ) );
			} );
		}

		if ( ! elementorFrontend.isEditMode() ) {
			var canvas = $( $circle.circleProgress( 'widget' ) );
			canvas.stop();
			$._appear( $circle.get( 0 ), { force_process: true } );
			$circle.on( '_appear', function () {
				if ( ! $circle.data( 'animated' ) ) {
					$circle.circleProgress( 'value', progressValue );
					$circle.data( 'animated', true );
				}
			} );
		}
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/circle-progress.default', initCircle );
	} );
}( jQuery ) );
