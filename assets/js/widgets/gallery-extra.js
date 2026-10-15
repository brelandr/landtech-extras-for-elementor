( function ( $ ) {
	'use strict';

	var stores = {};

	var ee = {
		getElementSettings: function ( $scope ) {
			return window.ee.getElementSettings( $scope );
		},
		getUniqueLoopScopeId: function ( $scope ) {
			if ( $scope.data( 'ee-template-widget-id' ) ) {
				return $scope.data( 'ee-template-widget-id' );
			}
			return $scope.data( 'id' );
		},
		ltxeInstanceStore: function ( kind ) {
			if ( ! stores[ kind ] ) {
				stores[ kind ] = ( typeof Map === 'function' ) ? new Map() : {};
			}
			return stores[ kind ];
		},
		ltxeSetInstance: function ( kind, id, instance ) {
			var store = ee.ltxeInstanceStore( kind );
			var key = String( id );
			if ( store.set ) {
				store.set( key, instance );
			} else {
				store[ key ] = instance;
			}
		}
	};

	function initGalleryExtra( $scope ) {

			var elementSettings = ee.getElementSettings( $scope );
			var uniqueId = ee.getUniqueLoopScopeId( $scope );

			var $gallery = $scope.find( '.ee-gallery' ),
				parallaxGalleryArgs = {
					columns : elementSettings.columns,
				};

			var runGalleryParallax = function() {
				if ( 'none' !== elementSettings.parallax_disable_on ) {
					parallaxGalleryArgs.responsive = elementSettings.parallax_disable_on;
				}

				if ( elementSettings.columns_tablet ) {
					parallaxGalleryArgs.columnsTablet = elementSettings.columns_tablet;
				}

				if ( elementSettings.columns_mobile ) {
					parallaxGalleryArgs.columnsMobile = elementSettings.columns_mobile;
				}

				if ( elementSettings.parallax_speed.size ) {
					parallaxGalleryArgs.speed = elementSettings.parallax_speed.size;
				}

				if ( elementSettings.parallax_speed_tablet.size ) {
					parallaxGalleryArgs.speedTablet = elementSettings.parallax_speed_tablet.size;
				}

				if ( elementSettings.parallax_speed_mobile.size ) {
					parallaxGalleryArgs.speedMobile = elementSettings.parallax_speed_mobile.size;
				}

				if ( elementorFrontend.isEditMode() ) {
					parallaxGalleryArgs.scope = window.elementor.$previewContents;
				}

				$gallery.parallaxGallery( parallaxGalleryArgs );
			};

			var runGalleryMasonry = function() {
				$gallery.imagesLoaded( function() {
					var $isotope = $gallery.isotope({
							itemSelector	: '.ee-gallery__item',
							percentPosition : true,
							hiddenStyle 	: {
								opacity 	: 0,
							},
						});

					$isotope.masonry();
					ee.ltxeSetInstance( 'gallery-isotope', uniqueId, $gallery.data( 'isotope' ) );
				});
			};

			var runGalleryTilt = function() {
				$gallery.find( '.ee-gallery__tilt' ).tilt({
					maxTilt 		: elementSettings.tilt_amount.size,
					scale 			: elementSettings.tilt_scale.size,
					speed 			: elementSettings.tilt_speed.size,
					axis 			: elementSettings.tilt_axis,
					perspective 	: 1000,
				});
			};

			if ( 'yes' === elementSettings.parallax_enable ) {
				runGalleryParallax();
			} else if ( 'yes' === elementSettings.masonry_enable && ! elementorFrontend.isEditMode() ) {
				runGalleryMasonry();
			}

			if ( 'yes' === elementSettings.tilt_enable ) {
				runGalleryTilt();
			}
	}


	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/gallery-extra.default', function ( $scope ) {
			if ( $scope.data( 'ltxeOnce_gallery-extra.default' ) ) {
				return;
			}
			$scope.data( 'ltxeOnce_gallery-extra.default', true );
			initGalleryExtra( $scope );
		} );
	} );
}( jQuery ) );
