( function ( $ ) {
	'use strict';

	function activateTab( $root, index, updateHash ) {
		var $tabs = $root.find( '.ltxe-tabs__tab' );
		var $panels = $root.find( '.ltxe-tabs__panel' );
		var $accordion = $root.find( '.ltxe-tabs__accordion-title' );

		$tabs.attr( 'aria-selected', 'false' ).removeClass( 'is-active' );
		$panels.attr( 'hidden', 'true' ).removeClass( 'is-active' );
		$accordion.attr( 'aria-expanded', 'false' ).removeClass( 'is-active' );

		var $tab = $tabs.filter( '[data-tab-index="' + index + '"]' );
		var $panel = $panels.filter( '[data-tab-index="' + index + '"]' );
		var $acc = $accordion.filter( '[data-tab-index="' + index + '"]' );

		$tab.attr( 'aria-selected', 'true' ).addClass( 'is-active' );
		$panel.removeAttr( 'hidden' ).addClass( 'is-active' );
		$acc.attr( 'aria-expanded', 'true' ).addClass( 'is-active' );

		$root.trigger( 'ltxe:tabs:activated', [ index, $panel, $tab ] );

		if ( updateHash && 'yes' === $root.attr( 'data-deep-link' ) ) {
			var slug = $tab.attr( 'data-tab-slug' );
			if ( slug && window.history && window.history.replaceState ) {
				window.history.replaceState( null, '', '#' + slug );
			}
		}
	}

	function resolveDeepLink( $root ) {
		var params = new URLSearchParams( window.location.search );
		var qTab = params.get( 'tab' );
		var hash = window.location.hash ? window.location.hash.replace( '#', '' ) : '';
		var slug = qTab || hash;
		if ( ! slug ) {
			return;
		}
		var $match = $root.find( '.ltxe-tabs__tab[data-tab-slug="' + slug + '"]' );
		if ( $match.length ) {
			activateTab( $root, $match.data( 'tab-index' ), false );
		}
	}

	function initTabs( $scope ) {
		$scope.find( '.ltxe-tabs' ).each( function () {
			var $root = $( this );
			if ( $root.data( 'ltxeTabsInit' ) ) {
				return;
			}
			$root.data( 'ltxeTabsInit', true );

			$root.on( 'click', '.ltxe-tabs__tab, .ltxe-tabs__accordion-title', function ( e ) {
				e.preventDefault();
				activateTab( $root, $( this ).data( 'tab-index' ), true );
			} );

			$root.on( 'keydown', '.ltxe-tabs__tab', function ( e ) {
				var $tabs = $root.find( '.ltxe-tabs__tab' );
				var idx = $tabs.index( this );
				if ( 37 === e.which || 38 === e.which ) {
					e.preventDefault();
					activateTab( $root, idx > 0 ? idx - 1 : $tabs.length - 1, true );
				}
				if ( 39 === e.which || 40 === e.which ) {
					e.preventDefault();
					activateTab( $root, idx < $tabs.length - 1 ? idx + 1 : 0, true );
				}
			} );

			resolveDeepLink( $root );

			if ( ! $root.find( '.ltxe-tabs__panel:not([hidden])' ).length ) {
				activateTab( $root, 0, false );
			}
		} );
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-tabs.default', initTabs );
	} );

	$( function () {
		initTabs( $( document.body ) );
	} );
}( jQuery ) );
