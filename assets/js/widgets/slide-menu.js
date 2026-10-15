( function ( $ ) {
	'use strict';

	function initSlideMenu( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		var $menu = $scope.find( '.ee-slide-menu__menu' );
		var slideMenuArgs = {
			linkNavigation: 'yes' === settings.link_navigation,
			backLabel: settings.back_text,
		};

		function destroy() {
			if ( $menu.data( 'slideMenu' ) ) {
				$menu.data( 'slideMenu' ).destroy();
			}
		}

		destroy();
		$menu.slideMenu( slideMenuArgs );

		window.ee.onElementRemove( $scope, destroy );
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-slide-menu.classic', initSlideMenu );
	} );
}( jQuery ) );
