( function ( $ ) {
	'use strict';

	var ee = {
		GallerySlider: {},
		getElementSettings: function ( $scope ) {
			return window.ee.getElementSettings( $scope );
		},
		getUniqueLoopScopeId: function ( $scope ) {
			if ( $scope.data( 'ee-template-widget-id' ) ) {
				return $scope.data( 'ee-template-widget-id' );
			}
			return $scope.data( 'id' );
		},
		onElementRemove: function ( $element, callback ) {
			window.ee.onElementRemove( $element, callback );
		},
		prefersReducedMotion: function () {
			return window.ee.prefersReducedMotion();
		}
	};

	ee.Carousel = function( $swiper, settings ) {
			var $slides = $swiper.find( '.ee-swiper__slide' ),

				elementorBreakpoints = elementorFrontend.config.breakpoints,

				swiperInstance 	= $swiper.data( 'swiper' ),
				swiperArgs 		= {
					autoHeight				: settings.element.autoHeight || false,
					direction 				: settings.element.direction || settings.default.direction,
					effect 					: settings.element.effect || settings.default.effect,
					slidesPerView 			: settings.default.slidesPerView,
					slidesPerColumn 		: settings.default.slidesPerColumn,
					slidesPerColumnFill 	: 'row',
					slidesPerGroup			: settings.default.slidesPerGroup,
					spaceBetween 			: settings.default.spaceBetween,
					pagination 				: {},
					navigation 				: {},
					autoplay 				: settings.element.autoplay || false,
					grabCursor 				: true,
					watchSlidesProgress 	: true,
					watchSlidesVisibility 	: true,
				};

				if ( settings.default.breakpoints ) {
					swiperArgs.breakpoints = {};
					swiperArgs.breakpoints[ elementorBreakpoints.md ] = settings.default.breakpoints.tablet;
					swiperArgs.breakpoints[ elementorBreakpoints.lg ] = settings.default.breakpoints.desktop;
				}

				if ( ! elementorFrontend.isEditMode() ) {
					// Observer messes with free mode
					if ( ! settings.element.freeMode ) {
						swiperArgs.observer 			= true;
						swiperArgs.observeParents		= true;
						swiperArgs.observeSlideChildren = true;
					}
				} else { // But we're safe in edit mode
					swiperArgs.observer 			= true;
					swiperArgs.observeParents		= true;
					swiperArgs.observeSlideChildren = true;
				}

			ee.Carousel.init = function() {
				if ( swiperInstance ) {
					ee.Carousel.destroy();
					return;
				}

				// Number of columns

				if ( swiperArgs.breakpoints ) {
					if ( settings.element.breakpoints.desktop.slidesPerView ) {
						swiperArgs.breakpoints[ elementorBreakpoints.lg ].slidesPerView = settings.stretch ? Math.min( $slides.length, +settings.element.breakpoints.desktop.slidesPerView || 3 ) : +settings.element.breakpoints.desktop.slidesPerView || 3;
					}

					if ( settings.element.breakpoints.tablet.slidesPerView ) {
						swiperArgs.breakpoints[ elementorBreakpoints.md ].slidesPerView = settings.stretch ? Math.min( $slides.length, +settings.element.breakpoints.tablet.slidesPerView || 2 ) : +settings.element.breakpoints.tablet.slidesPerView || 2;
					}
				}

				if ( settings.element.slidesPerView ) {
					swiperArgs.slidesPerView = settings.stretch ? Math.min( $slides.length, +settings.element.slidesPerView || 1 ) : +settings.element.slidesPerView || 1;
				}

				// Number of slides to scroll

				if ( swiperArgs.breakpoints ) {
					if ( settings.element.breakpoints.desktop.slidesPerGroup ) {
						swiperArgs.breakpoints[ elementorBreakpoints.lg ].slidesPerGroup = Math.min( $slides.length, +settings.element.breakpoints.desktop.slidesPerGroup || 3 );
					}

					if ( settings.element.breakpoints.tablet.slidesPerGroup ) {
						swiperArgs.breakpoints[ elementorBreakpoints.md ].slidesPerGroup = Math.min( $slides.length, +settings.element.breakpoints.tablet.slidesPerGroup || 2 );
					}
				}

				if ( settings.element.slidesPerGroup ) {
					swiperArgs.slidesPerGroup = Math.min( $slides.length, +settings.element.slidesPerGroup || 1 );
				}

				// Rows

				if ( swiperArgs.breakpoints ) {
					if ( settings.element.breakpoints.desktop.slidesPerColumn ) {
						swiperArgs.breakpoints[ elementorBreakpoints.lg ].slidesPerColumn = settings.element.breakpoints.desktop.slidesPerColumn;
					}

					if ( settings.element.breakpoints.tablet.slidesPerColumn ) {
						swiperArgs.breakpoints[ elementorBreakpoints.md ].slidesPerColumn = settings.element.breakpoints.tablet.slidesPerColumn;
					}
				}

				if ( settings.element.slidesPerColumn ) {
					swiperArgs.slidesPerColumn = settings.element.slidesPerColumn;
				}

				// Column spacing

				if ( swiperArgs.breakpoints ) {
					swiperArgs.breakpoints[ elementorBreakpoints.lg ].spaceBetween = settings.element.breakpoints.desktop.spaceBetween || 0;
					swiperArgs.breakpoints[ elementorBreakpoints.md ].spaceBetween = settings.element.breakpoints.tablet.spaceBetween || 0;
				}

				if ( settings.element.spaceBetween ) {
					swiperArgs.spaceBetween = settings.element.spaceBetween || 0;
				}

				if ( settings.element.slidesPerColumnFill ) {
					swiperArgs.slidesPerColumnFill = settings.element.slidesPerColumnFill;
				}

				// Arrows and pagination

				if ( settings.element.arrows ) {
					swiperArgs.navigation.disabledClass = 'ee-swiper__button--disabled';

					var $prevButton = settings.scope.find( settings.element.arrowPrev ),
						$nextButton = settings.scope.find( settings.element.arrowNext );

					if ( $prevButton.length && $nextButton.length ) {

						var arrowPrev = settings.element.arrowPrev + '-' + settings.id,
							arrowNext = settings.element.arrowNext + '-' + settings.id;

						$prevButton.addClass( arrowPrev.replace('.','') );
						$nextButton.addClass( arrowNext.replace('.','') );

						swiperArgs.navigation.prevEl = arrowPrev;
						swiperArgs.navigation.nextEl = arrowNext;
					}
				}

				if ( settings.element.pagination ) {
					swiperArgs.pagination.el = '.ee-swiper__pagination-' + settings.id;
					swiperArgs.pagination.type = settings.element.paginationType;

					if ( settings.element.paginationClickable ) {
						swiperArgs.pagination.clickable = true;
					}
				}

				// Loop

				if ( settings.element.loop ) {
					swiperArgs.loop = true;

					if ( $slides.length ) {
						swiperArgs.loopedSlides = $slides.length;
					}
				}

				if ( settings.element.rtl ) {
					swiperArgs.rtl = true;
				}

				if ( 'fade' === swiperArgs.effect ) {
					swiperArgs.fadeEffect = {
						crossFade: true,
					};
				}

				// Autplay

				if ( swiperArgs.autoplay && ( settings.element.autoplaySpeed || settings.element.disableOnInteraction ) ) {
					swiperArgs.autoplay = {};

					if ( settings.element.autoplaySpeed ) {
						swiperArgs.autoplay.delay = settings.element.autoplaySpeed;
					}

					if ( settings.element.autoplaySpeed ) {
						swiperArgs.autoplay.disableOnInteraction = settings.element.disableOnInteraction;
					}
				} else {

				}

				// Speed 

				if ( settings.element.speed ) {
					swiperArgs.speed = settings.element.speed;
				}

				// Resistance 

				if ( settings.element.resistance ) {
					swiperArgs.resistanceRatio = 1 - settings.element.resistance;
				}

				// Free Mode

				if ( settings.element.freeMode ) {
					swiperArgs.freeMode = true;
					swiperArgs.freeModeSticky = settings.element.freeModeSticky;
					swiperArgs.freeModeMomentum = settings.element.freeModeMomentum;
					swiperArgs.freeModeMomentumBounce = settings.element.freeModeMomentumBounce;

					if ( settings.element.freeModeMomentumRatio ) {
						swiperArgs.freeModeMomentumRatio = settings.element.freeModeMomentumRatio;
					}

					if ( settings.element.freeModeMomentumVelocityRatio ) {
						swiperArgs.freeModeMomentumVelocityRatio = settings.element.freeModeMomentumVelocityRatio;
					}

					if ( settings.element.freeModeMomentumBounceRatio ) {
						swiperArgs.freeModeMomentumBounceRatio = settings.element.freeModeMomentumBounceRatio;
					}
				}

				return swiperArgs;
			};

			ee.Carousel.onAfterInit = function( $swiper, swiper, settings ) {
				if ( 'undefined' == typeof settings || 'undefined' == typeof swiper ) {
					return;
				}

				if ( ee.prefersReducedMotion() ) {
					if ( swiper.params.autoplay && swiper.autoplay ) {
						swiper.autoplay.stop();
					}
					if ( swiper.params.speed ) {
						swiper.params.speed = 0;
					}
				}

				$swiper.on( 'keydown.ltxeCarousel', function( e ) {
					if ( 37 === e.which ) {
						swiper.slidePrev();
					} else if ( 39 === e.which ) {
						swiper.slideNext();
					} else if ( 27 === e.which && settings.scope ) {
						settings.scope.find( '.ee-swiper__button--prev' ).focus();
					}
				} );

				if ( settings.element.stopOnHover ) {
					$swiper.on( 'mouseover', function() {
						swiper.autoplay.stop();
					});

					$swiper.on( 'mouseout', function() {
						swiper.autoplay.start();
					});
				}

				if ( settings.element.slideChangeTriggerResize ) {
					swiper.on('slideChange', function () {
						$( window ).trigger('resize');
					});
				}

				$swiper.data( 'swiper', swiper );
			};

			return ee.Carousel.init();
	};
	function initGallerySlider( $scope ) {

			var elementSettings = ee.getElementSettings( $scope ),
				uniqueId 		= ee.getUniqueLoopScopeId( $scope ),
				scopeId 		= $scope.data('id'),
				$swiperSlider 	= $scope.find('.ee-gallery-slider__slider'),
				$swiperCarousel = $scope.find('.ee-gallery-slider__carousel'),

				$thumbs 		= $scope.find('.ee-gallery__item'),

				hasCarousel 	= $swiperCarousel.length;

			var swiperSlider = null,
				swiperCarousel = null,
				sliderSettings = {
					key 		: 'slider',
					scope 		: $scope,
					id 			: uniqueId,
					element : {
						autoHeight 				: 'yes' === elementSettings.adaptive_height,
						autoplay 				: 'yes' === elementSettings.autoplay,
						autoplaySpeed 			: 'yes' === elementSettings.autoplay && elementSettings.autoplay_speed ? elementSettings.autoplay_speed.size : false,
						disableOnInteraction 	: '' !== elementSettings.autoplay_disable_on_interaction,
						stopOnHover 			: 'yes' === elementSettings.pause_on_hover,
						loop 					: 'yes' === elementSettings.infinite,
						rtl 					: 'rtl' === elementSettings.direction,
						arrows 					: '' !== elementSettings.show_arrows,
						arrowPrev 				: '.ee-swiper__button--prev-slider',
						arrowNext 				: '.ee-swiper__button--next-slider',
						effect 					: elementSettings.effect,
						speed 					: elementSettings.speed ? elementSettings.speed.size : 500,
						resistance 				: elementSettings.resistance ? elementSettings.resistance.size : 0.25,
					},
					default : {
						effect 			: 'slide',
						direction 		: 'horizontal',
						slidesPerView 	: 1,
						slidesPerGroup 	: 1,
						slidesPerColumn : 1,
						spaceBetween 	: 0,
					},
				};

			if ( hasCarousel ) {
				var carouselSettings = {
						key 		: 'carousel',
						scope 		: $scope,
						id 			: uniqueId,
						stretch 	: 'yes' === elementSettings.thumbnails_stretch,
						element : {
							direction 			: elementSettings.carousel_orientation,
							arrows 				: '' !== elementSettings.carousel_arrows,
							arrowPrev 			: '.ee-swiper__button--prev-carousel',
							arrowNext 			: '.ee-swiper__button--next-carousel',
							autoHeight 			: false,
							speed 				: elementSettings.carousel_speed ? elementSettings.carousel_speed.size : 500,
							slidesPerView 		: elementSettings.carousel_slides_per_view_mobile,
							slidesPerColumn 	: 'vertical' === elementSettings.carousel_orientation ? 1 : elementSettings.carousel_slides_per_column_mobile,
							slidesPerGroup 		: elementSettings.carousel_slides_to_scroll_mobile,
							resistance 			: elementSettings.carousel_resistance ? elementSettings.carousel_resistance.size : 0.15,
							spaceBetween 		: elementSettings.carousel_spacing_mobile ? elementSettings.carousel_spacing_mobile.size : 0,
							breakpoints 		: {
								tablet : {
									slidesPerView 	: elementSettings.carousel_slides_per_view_tablet,
									slidesPerColumn : 'vertical' === elementSettings.carousel_orientation ? 1 : elementSettings.carousel_slides_per_column_tablet,
									slidesPerGroup 	: elementSettings.carousel_slides_to_scroll_tablet,
									spaceBetween 	: elementSettings.carousel_spacing_tablet ? elementSettings.carousel_spacing_tablet.size : 0,
								},
								desktop : {
									slidesPerView 	: elementSettings.carousel_slides_per_view,
									slidesPerColumn : 'vertical' === elementSettings.carousel_orientation ? 1 : elementSettings.carousel_slides_per_column,
									slidesPerGroup 	: elementSettings.carousel_slides_to_scroll,
									spaceBetween 	: elementSettings.carousel_spacing ? elementSettings.carousel_spacing.size : 0,
								},
							},
						},
						default : {
							effect 			: 'slide',
							slidesPerView 	: 1,
							slidesPerGroup 	: 1,
							slidesPerColumn : 1,
							spaceBetween 	: 6,
							breakpoints 	: {
								tablet : {
									slidesPerView 	: 2,
									slidesPerGroup 	: 1,
									slidesPerColumn : 2,
									spaceBetween 	: 12,
								},
								desktop : {
									slidesPerView 	: 3,
									slidesPerGroup 	: 1,
									slidesPerColumn : 3,
									spaceBetween 	: 24,
								},
							},
						},
					};
			}

			ee.GallerySlider.init = function() {
				var sliderArgs = ee.Carousel( $swiperSlider, sliderSettings ),
					carouselArgs,
					sliderEl = $swiperSlider[0];

				if ( ! sliderEl || ! sliderArgs ) {
					return;
				}

				if ( hasCarousel ) {
					carouselArgs = ee.Carousel( $swiperCarousel, carouselSettings );
				}

				var finishInit = function( sliderInstance, carouselInstance ) {
					ee.GallerySlider.initSliders( $scope, sliderInstance, carouselInstance || false );
					ee.Carousel.onAfterInit( $swiperSlider, sliderInstance, sliderSettings );

					if ( sliderInstance && 'function' === typeof sliderInstance.update ) {
						sliderInstance.update();
					}

					if ( hasCarousel && carouselInstance ) {
						ee.Carousel.onAfterInit( $swiperCarousel, carouselInstance, carouselSettings );

						if ( 'function' === typeof carouselInstance.update ) {
							carouselInstance.update();
						}
					}
				};

				if ( elementorFrontend.utils && elementorFrontend.utils.swiper ) {
					var asyncSwiper = elementorFrontend.utils.swiper;

					new asyncSwiper( sliderEl, sliderArgs ).then( function( sliderSwiperInstance ) {
						if ( ! hasCarousel ) {
							finishInit( sliderSwiperInstance, false );
							return;
						}

						var carouselEl = $swiperCarousel[0];

						if ( ! carouselEl || ! carouselArgs ) {
							finishInit( sliderSwiperInstance, false );
							return;
						}

						new asyncSwiper( carouselEl, carouselArgs ).then( function( carouselSwiperInstance ) {
							finishInit( sliderSwiperInstance, carouselSwiperInstance );
						} );
					} );

				} else if ( 'undefined' !== typeof Swiper ) {
					swiperSlider = new Swiper( sliderEl, sliderArgs );

					if ( hasCarousel && $swiperCarousel[0] && carouselArgs ) {
						swiperCarousel = new Swiper( $swiperCarousel[0], carouselArgs );
					}

					finishInit( swiperSlider, swiperCarousel );
				}
			};

			ee.GallerySlider.getSlider = function() {
				return $scope.find('.ee-gallery-slider__slider');
			};

			ee.GallerySlider.getCarousel = function() {
				return $scope.find('.ee-gallery-slider__carousel');
			};

			ee.GallerySlider.initSliders = function( $scope, swiperSlider, swiperCarousel ) {
				var data = {
					scope 		: $scope,
					slider 		: swiperSlider,
					carousel 	: swiperCarousel,
				};

				ee.GallerySlider.onSlideChange( data );
				ee.GallerySlider.events( data );
			};

			ee.GallerySlider.getThumbIndex = function( $thumb, $thumbs ) {
				var index = $thumb.data( 'galleryIndex' );

				if ( 'undefined' === typeof index ) {
					index = parseInt( $thumb.attr( 'data-gallery-index' ), 10 );
				}

				if ( ! isNaN( index ) && index >= 0 ) {
					return index;
				}

				return $thumbs.index( $thumb[0] );
			};

			ee.GallerySlider.slideToIndex = function( swiper, index, useLoop ) {
				if ( ! swiper || 'function' !== typeof swiper.slideTo ) {
					return;
				}

				index = parseInt( index, 10 );

				if ( isNaN( index ) || index < 0 ) {
					return;
				}

				var loopEnabled = useLoop && swiper.params && swiper.params.loop;

				if ( loopEnabled && 'function' === typeof swiper.slideToLoop ) {
					swiper.slideToLoop( index );
				} else {
					swiper.slideTo( index );
				}

				if ( swiper.params && swiper.params.autoHeight && 'function' === typeof swiper.updateAutoHeight ) {
					swiper.updateAutoHeight( swiper.params.speed || 300 );
				}
			};

			ee.GallerySlider.events = function( data ) {
				var $thumbs = data.scope.find('.ee-gallery__item');

				if ( ! data.slider || 'function' !== typeof data.slider.on ) {
					return;
				}

				data.slider.on('slideChange', function() {
					ee.GallerySlider.onSlideChange( data );
				} );

				$thumbs.on( 'click', function( e ) {
					e.preventDefault();
					ee.GallerySlider.slideToIndex(
						data.slider,
						ee.GallerySlider.getThumbIndex( $( this ), $thumbs ),
						sliderSettings.element.loop
					);
				});
			};

			ee.GallerySlider.onSlideChange = function( data ) {
				if ( ! data.slider ) {
					return;
				}

				var activeIndex = sliderSettings.element.loop ? data.slider.realIndex : data.slider.activeIndex;

				if ( hasCarousel && data.carousel ) {
					ee.GallerySlider.slideToIndex( data.carousel, activeIndex, false );
				}

				var $thumbs = data.scope.find('.ee-gallery__item');

				$thumbs.removeClass('is--active');
				$thumbs.eq( activeIndex ).addClass('is--active');
			};

			ee.onElementRemove( $scope, function() {
				$scope.find('.ee-swiper__container').each( function() {
					if ( $(this).data('swiper') ) {
						$(this).data('swiper').destroy();
					}
				});
			});

			ee.GallerySlider.init();
	}


	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/gallery-slider.default', function ( $scope ) {
			if ( $scope.data( 'ltxeOnce_gallery-slider.default' ) ) {
				return;
			}
			$scope.data( 'ltxeOnce_gallery-slider.default', true );
			initGallerySlider( $scope );
		} );
	} );
}( jQuery ) );
