( function ( $ ) {
	'use strict';

	var ee = {
		getElementSettings: function ( $scope ) {
			return window.ee.getElementSettings( $scope );
		},
		getElementType: function ( $element ) {
			if ( 'section' === $element.data( 'element_type' ) || 'column' === $element.data( 'element_type' ) ) {
				return $element.data( 'element_type' );
			}
			return 'widget';
		},
		getWidgetContentRoot: function ( $widgetScope ) {
			return window.ee.getWidgetContentRoot( $widgetScope );
		},
		getWindow: function () {
			return window.ee.getWindow();
		},
		onElementRemove: function ( $element, callback ) {
			window.ee.onElementRemove( $element, callback );
		},
		getGlobalSettings: function ( section ) {
			return window.ee.getGlobalSettings( section );
		},
		isAdminBar: function () {
			return $( 'body' ).is( '.admin-bar' );
		}
	};

	function initParallaxBackground( $scope ) {

			if ( 'section' !== ee.getElementType( $scope ) )
				return;

			ee.ParallaxBackground.elementSettings 	= ee.getElementSettings( $scope );

			var instance = $scope.data( 'parallaxBackground' ),
				parallaxBackgroundArgs = {
					parallaxResizeWatch : $scope.find('.elementor-container'),
				};

			ee.ParallaxBackground.maybeDestroy = function() {
				if ( instance )
					instance.destroy();
			};

			ee.ParallaxBackground.init = function() {

				// Reinit parallax background
				ee.ParallaxBackground.maybeDestroy();

				// Bail if not enabled
				if ( ! ee.ParallaxBackground.elementSettings.parallax_background_enable || '' === ee.ParallaxBackground.elementSettings.parallax_background_enable )
					return;

				// Check for background images for the section
				if ( ! elementorFrontend.isEditMode() && ( $scope.css('background-image') === '' || $scope.css('background-image') === 'none' ) )
					return;

				parallaxBackgroundArgs.parallaxBgImage = ee.ParallaxBackground.elementSettings.background_image['url'];

				if ( "" !== ee.ParallaxBackground.elementSettings.parallax_background_speed.size ) {
					parallaxBackgroundArgs.parallaxSpeed = ee.ParallaxBackground.elementSettings.parallax_background_speed.size;
				}

				if ( "" !== ee.ParallaxBackground.elementSettings.parallax_background_speed_tablet.size ) {
					parallaxBackgroundArgs.parallaxSpeedTablet = ee.ParallaxBackground.elementSettings.parallax_background_speed_tablet.size;
				}

				if ( "" !== ee.ParallaxBackground.elementSettings.parallax_background_speed_mobile.size ) {
					parallaxBackgroundArgs.parallaxSpeedMobile = ee.ParallaxBackground.elementSettings.parallax_background_speed_mobile.size;
				}

				if ( ee.ParallaxBackground.elementSettings.parallax_background_direction ) {
					parallaxBackgroundArgs.parallaxDirection = ee.ParallaxBackground.elementSettings.parallax_background_direction;
				}

				// Editor mode
				if ( elementorFrontend.isEditMode() ) {
					parallaxBackgroundArgs.win = window.elementor.$previewContents;
				}

				$scope.parallaxBackground( parallaxBackgroundArgs );

				ee.onElementRemove( $scope, function() {
					ee.ParallaxBackground.maybeDestroy();
				});
			};

			ee.ParallaxBackground.init();
	}


	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/section', function ( $scope ) {
			initParallaxBackground( $scope );
		} );
	} );
}( jQuery ) );
