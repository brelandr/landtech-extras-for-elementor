( function ( $ ) {
	'use strict';

	var ee = {
		ParallaxElement: {},
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

	function initParallaxElement( $scope ) {

			ee.ParallaxElement.elementSettings 	= ee.getElementSettings( $scope );

			var $element = $scope,
				instance = $scope.data( 'parallaxElement' ),
				parallaxElementArgs = {
					type 			: ee.ParallaxElement.elementSettings.parallax_element_type,
					invert 			: 'yes' === ee.ParallaxElement.elementSettings.parallax_element_invert,
					moveOutside 	: 'yes' === ee.ParallaxElement.elementSettings.parallax_off_viewport,
					scroll 			: {
						relative 	: ee.ParallaxElement.elementSettings.parallax_element_relative,
					},
					mouse 			: {
						relative 	: ee.ParallaxElement.elementSettings.parallax_element_pan_relative,
						axis 	 	: ee.ParallaxElement.elementSettings.parallax_element_pan_axis,
					},
					speed 			: {},
				};;

			ee.ParallaxElement.maybeDestroy = function() {
				if ( instance )
					instance.destroy();
			};

			ee.ParallaxElement.init = function() {

				if ( 'column' === ee.getElementType( $scope ) ) $element = $scope.find( '> .elementor-column-wrap' );
				if ( 'widget'=== ee.getElementType( $scope ) ) $element = ee.getWidgetContentRoot( $scope );

				ee.ParallaxElement.maybeDestroy();

				if ( 'yes' !== ee.ParallaxElement.elementSettings.parallax_element_enable )
					return;

				if ( 'scroll' === ee.ParallaxElement.elementSettings.parallax_element_type ) {
					if ( 'none' !== ee.ParallaxElement.elementSettings.parallax_element_disable_on ) {
						parallaxElementArgs.scroll.responsive = ee.ParallaxElement.elementSettings.parallax_element_disable_on;
					}
				} else if ( 'mouse' === ee.ParallaxElement.elementSettings.parallax_element_type ) {
					if ( undefined !== typeof ee.ParallaxElement.elementSettings.parallax_element_pan_distance && 'element' === ee.ParallaxElement.elementSettings.parallax_element_pan_relative ) {
						parallaxElementArgs.mouse.distance = ee.ParallaxElement.elementSettings.parallax_element_pan_distance.size;
					}
				}

				if ( ee.ParallaxElement.elementSettings.parallax_element_speed.size ) {
					parallaxElementArgs.speed.desktop = ee.ParallaxElement.elementSettings.parallax_element_speed.size;
				}

				if ( ee.ParallaxElement.elementSettings.parallax_element_speed_tablet.size ) {
					parallaxElementArgs.speed.tablet = ee.ParallaxElement.elementSettings.parallax_element_speed_tablet.size;
				}

				if ( ee.ParallaxElement.elementSettings.parallax_element_speed_mobile.size ) {
					parallaxElementArgs.speed.mobile = ee.ParallaxElement.elementSettings.parallax_element_speed_mobile.size;
				}

				if ( elementorFrontend.isEditMode() ) {
					parallaxElementArgs.scope = window.elementor.$previewContents;
				}

				$element.parallaxElement( parallaxElementArgs );
			};

			ee.ParallaxElement.init();

			ee.onElementRemove( $scope, function() {
				ee.ParallaxElement.maybeDestroy();
			});
	}


	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) {
			initParallaxElement( $scope );
		} );
	} );
}( jQuery ) );
