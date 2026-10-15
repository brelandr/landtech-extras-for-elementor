( function ( $ ) {
	'use strict';

	var ee = {
		Portfolio: {},
		getElementSettings: function ( $scope ) {
			return window.ee.getElementSettings( $scope );
		}
	};

	function initPortfolio( $scope ) {

			ee.Portfolio.elementSettings 	= ee.getElementSettings( $scope );

			if ( 'yes' !== ee.Portfolio.elementSettings.parallax_enable )
				return;

			var parallaxGalleryArgs = {
					transformItem 	: 'a.elementor-post__thumbnail__link',
					columns 		: ee.Portfolio.elementSettings.columns,
				};

			ee.Portfolio.init = function() {
				if ( 'none' !== ee.Portfolio.elementSettings.parallax_disable_on ) {
					parallaxGalleryArgs.responsive = ee.Portfolio.elementSettings.parallax_disable_on;
				}

				if ( ee.Portfolio.elementSettings.columns_tablet ) {
					parallaxGalleryArgs.columnsTablet = ee.Portfolio.elementSettings.columns_tablet;
				}

				if ( ee.Portfolio.elementSettings.columns_mobile ) {
					parallaxGalleryArgs.columnsMobile = ee.Portfolio.elementSettings.columns_mobile;
				}

				if ( ee.Portfolio.elementSettings.parallax_speed_tablet.size ) {
					parallaxGalleryArgs.speedTablet = ee.Portfolio.elementSettings.parallax_speed_tablet.size;
				}

				if ( ee.Portfolio.elementSettings.parallax_speed_mobile.size ) {
					parallaxGalleryArgs.speedMobile = ee.Portfolio.elementSettings.parallax_speed_mobile.size;
				}

				if ( ee.Portfolio.elementSettings.parallax_speed.size ) {
					parallaxGalleryArgs.speed = ee.Portfolio.elementSettings.parallax_speed.size;
				}

				if ( elementorFrontend.isEditMode() ) {
					parallaxGalleryArgs.scope = window.elementor.$previewContents;
				}

				$scope.find('.elementor-portfolio').parallaxGallery( parallaxGalleryArgs );
			};

			ee.Portfolio.init();
	}


	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/portfolio.default', function ( $scope ) {
			if ( $scope.data( 'ltxeOnce_portfolio.default' ) ) {
				return;
			}
			$scope.data( 'ltxeOnce_portfolio.default', true );
			initPortfolio( $scope );
		} );
	} );
}( jQuery ) );
