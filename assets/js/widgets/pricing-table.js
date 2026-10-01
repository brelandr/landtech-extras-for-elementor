( function () {
	'use strict';

	function applyPeriod( period ) {
		document.querySelectorAll( '.ltxe-pricing-table' ).forEach( function ( table ) {
			var monthEl = table.querySelector( '.ltxe-pricing-table__price--monthly' );
			var annualEl = table.querySelector( '.ltxe-pricing-table__price--annual' );
			if ( ! monthEl || ! annualEl ) {
				return;
			}
			if ( 'annual' === period ) {
				monthEl.setAttribute( 'hidden', 'hidden' );
				annualEl.removeAttribute( 'hidden' );
			} else {
				annualEl.setAttribute( 'hidden', 'hidden' );
				monthEl.removeAttribute( 'hidden' );
			}
		} );
	}

	function initToggle( root ) {
		root.querySelectorAll( '.ltxe-pricing-toggle' ).forEach( function ( toggle ) {
			if ( toggle.getAttribute( 'data-ltxe-pt-init' ) ) {
				return;
			}
			toggle.setAttribute( 'data-ltxe-pt-init', '1' );
			toggle.addEventListener( 'click', function ( ev ) {
				var btn = ev.target.closest( '[data-period]' );
				if ( ! btn || ! toggle.contains( btn ) ) {
					return;
				}
				var period = btn.getAttribute( 'data-period' );
				toggle.querySelectorAll( '.ltxe-pricing-toggle__btn' ).forEach( function ( b ) {
					var on = b === btn;
					b.classList.toggle( 'is-active', on );
					b.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
				} );
				toggle.setAttribute( 'data-period', period );
				document.dispatchEvent( new CustomEvent( 'ltxe_pricing_toggle_changed', { detail: { period: period } } ) );
			} );
		} );
	}

	document.addEventListener( 'ltxe_pricing_toggle_changed', function ( ev ) {
		if ( ev.detail && ev.detail.period ) {
			applyPeriod( ev.detail.period );
		}
	} );

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		initToggle( root );
		var first = document.querySelector( '.ltxe-pricing-toggle' );
		if ( first ) {
			applyPeriod( first.getAttribute( 'data-period' ) || 'monthly' );
		}
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-pricing-table.default', function ( $scope ) {
				init( $scope[ 0 ] );
			} );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-pricing-toggle.default', function ( $scope ) {
				init( $scope[ 0 ] );
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			init( document );
		} );
	} else {
		init( document );
	}
}() );
