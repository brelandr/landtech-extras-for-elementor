( function ( $ ) {
	'use strict';

	var stores = {};

	var ee = {
		getElementSettings: function ( $scope ) {
			return window.ee.getElementSettings( $scope );
		},
		getElementSkin: function ( $element ) {
			return window.ee.getElementSkin( $element );
		},
		getUniqueLoopScopeId: function ( $scope ) {
			if ( $scope.data( 'ee-template-widget-id' ) ) {
				return $scope.data( 'ee-template-widget-id' );
			}
			return $scope.data( 'id' );
		},
		refreshElements: function ( $container, refreshable ) {
			window.ee.refreshElements( $container, refreshable );
		},
		applyReducedMotionClass: function ( $scope ) {
			window.ee.applyReducedMotionClass( $scope );
		},
		makeFocusableControl: function ( $el, label ) {
			window.ee.makeFocusableControl( $el, label );
		},
		onElementRemove: function ( $element, callback ) {
			window.ee.onElementRemove( $element, callback );
		},
		prefersReducedMotion: function () {
			return window.ee.prefersReducedMotion();
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
	function initPostsClassic( $scope ) {

			if ( elementorFrontend.isEditMode() )
				return;

			var elementSettings = ee.getElementSettings( $scope );

			var skin 				= ee.getElementSkin( $scope ) || 'classic';
			if ( 'carousel' === skin ) {
				return;
			}

			var sk 					= skin + '_',
				layoutSettingKey 	= sk + 'layout',
				layoutModeVal 		= elementSettings[ layoutSettingKey ];
			if ( 'undefined' === typeof layoutModeVal || null === layoutModeVal || '' === layoutModeVal ) {
				layoutModeVal = 'default';
			}
			var isoLayoutMode = layoutModeVal;
			if ( 'metro' === layoutModeVal ) {
				isoLayoutMode = 'fitRows';
			} else if ( 'packery' === layoutModeVal ) {
				isoLayoutMode = 'packery';
			}
			var uniqueId 			= ee.getUniqueLoopScopeId( $scope ),
				scopeId 			= $scope.data('id'),
				$loop 				= $scope.find('.ee-loop'),
				$filters 			= $scope.find('.ee-filters'),
				$currentFilter 		= null,
				$triggers 			= $filters.find( '[data-filter]' ),

				elementClass 		= '.elementor-element-' + scopeId,
				isLayout 			= 'default' !== layoutModeVal && 1 < elementSettings.columns,
				isInfinite 			= 'yes' === elementSettings[ sk + 'infinite_scroll' ],
				isFiltered 			= 'yes' === elementSettings[ sk + 'filters' ],
				hasHistory 			= 'yes' === elementSettings[ sk + 'infinite_scroll_history' ] ? 'replace' : false,
				isotopeInstance 	= null,
				infScrollInstance 	= null;

			if ( ! $scope.find('.ee-pagination').length )
					isInfinite = false;

				var infiniteScrollArgs = {
						history 	: hasHistory,
						path 		: elementClass + ' .ee-pagination__next',
						append 		: elementClass + ' .ee-loop__item',
						hideNav 	: elementClass + ' .ee-pagination',
						status 		: elementClass + ' .ee-load-status',
					},

					isotopeArgs = {
						isOriginLeft 	: elementorFrontend.config.is_rtl ? false : true,
						itemSelector	: elementClass + ' .ee-loop__item',
						layoutMode 		: isLayout ? isoLayoutMode : 'masonry',
						percentPosition : true,
						hiddenStyle 	: {
							opacity 	: 0,
						},
					},

					filteryArgs = {
						wrapper 			: $loop,
						filterables 		: '.ee-loop__item',
						activeFilterClass 	: 'ee--active',
						notFound 			: $scope.find('.ee-grid__notice--not-found'),
					};

				if ( isLayout && 'masonry' === layoutModeVal ) {
					isotopeArgs.masonry = { columnWidth: elementClass + ' .ee-grid__item--sizer' };
				}
				if ( isLayout && 'packery' === layoutModeVal ) {
					isotopeArgs.packery = { columnWidth: elementClass + ' .ee-grid__item--sizer' };
				}

			var bindInfiniteScrollEvents = function( infScroll ) {
				if ( ! infScroll ) {
					return;
				}

				infScroll.on( 'request', function( path ) {
					$loop.addClass( 'ee-loop--loading' );
					$scope.find( '.ee-load-button' ).hide();
				} );

				infScroll.on( 'load', function( response, path ) {
					$loop.addClass( 'ee-loop--loaded' );
					$scope.find( '.ee-load-button' ).show();
				} );

				infScroll.on( 'append', function( response, path, items ) {
					$loop.removeClass( 'ee-loop--loading ee-loop--loaded' ).addClass( 'ee-loop--added' );
					ee.refreshElements( $( items ), false );
				} );
			};

			var runInfiniteScroll = function() {
				if ( isInfinite ) {
					if ( 'yes' === elementSettings[ sk + 'infinite_scroll_button' ] ) {
						infiniteScrollArgs.loadOnScroll 	= false;
						infiniteScrollArgs.scrollThreshold 	= false;
						infiniteScrollArgs.button 			= '.ee-load-button__trigger--' + scopeId;
					}
				}

				if ( isInfinite && ! isLayout ) {
					if ( 'undefined' !== typeof window.InfiniteScroll ) {
						infScrollInstance = new window.InfiniteScroll( $loop[0], infiniteScrollArgs );
						bindInfiniteScrollEvents( infScrollInstance );
						ee.ltxeSetInstance( 'posts-infscroll', uniqueId, infScrollInstance );
					}

				} else if ( isLayout ) {

					var $isotope = $loop.isotope( isotopeArgs );

					$isotope.imagesLoaded().progress( function() {
						$isotope.isotope('layout');
					});

					var ltxeIsoRelayoutTimer = null;
					var scheduleLtxeIsoRelayout = function() {
						window.clearTimeout( ltxeIsoRelayoutTimer );
						ltxeIsoRelayoutTimer = window.setTimeout( function() {
							if ( $loop.data( 'isotope' ) ) {
								$loop.isotope( 'layout' );
							}
						}, 120 );
					};

					$( window ).on( 'resize.ltxePostsIso.' + scopeId, scheduleLtxeIsoRelayout );

					if ( 'undefined' !== typeof window.ResizeObserver && $loop[0] ) {
						var ltxePostsLoopResizeObserver = new window.ResizeObserver( scheduleLtxeIsoRelayout );
						ltxePostsLoopResizeObserver.observe( $loop[0] );
					}

					isotopeInstance = $loop.data( 'isotope' );
					ee.ltxeSetInstance( 'posts-isotope', uniqueId, isotopeInstance );

					if ( isInfinite ) {
						if ( ! isFiltered || ! $triggers.length ) {
							infiniteScrollArgs.outlayer = isotopeInstance;
						}

						if ( 'undefined' !== typeof window.InfiniteScroll ) {
							infScrollInstance = new window.InfiniteScroll( $loop[0], infiniteScrollArgs );
							bindInfiniteScrollEvents( infScrollInstance );
							ee.ltxeSetInstance( 'posts-infscroll', uniqueId, infScrollInstance );

							infScrollInstance.on( 'append', function( response, path, items ) {
								$isotope.imagesLoaded().always( function() {
									$isotope.isotope( 'layout' );
								} );
							} );
						}
					}
				}
			};

			var runFilters = function() {
				if ( isLayout ) { // Masonry active

					// Init isotope with filters
					if ( $currentFilter.length && $currentFilter.data('filter') ) {
						$loop.isotope( $.extend({}, isotopeArgs, { filter : $currentFilter.data('filter') } ) );
					}

					$triggers.on( 'click', function( event ){
						var filter = $(this).data('filter');

						if ( filter ) {
							$loop.isotope( $.extend({}, isotopeArgs, { filter : filter } ) );
						}

						$triggers.removeClass('ee--active');
						$(this).addClass('ee--active');
					});

					if ( isInfinite && infScrollInstance ) {

						infScrollInstance.on( 'load', function( response, path ) {
							var $items = $( response ).find( elementClass + ' .ee-loop__item' );

							$( response ).imagesLoaded( function() {
								$loop.append( $items ).isotope( 'appended', $items );
							});
						});
					}

				} else {

					$filters.filtery( filteryArgs );

					var filteryInstance = $filters.data( 'filtery' );

					if ( isInfinite && infScrollInstance ) {

						infScrollInstance.on( 'load', function( response, path ) {
							var $items = $( response ).find( elementClass + ' .ee-loop__item' );

							$( response ).imagesLoaded( function() {
								filteryInstance.update();
								$loop.append( $items );
							});
						});
					}
				}
			};

			var initPostsClassic = function() {
				runInfiniteScroll();

				if ( isFiltered && $triggers.length ) {
					$currentFilter = $triggers.filter('.ee--active');
					runFilters();
				}
			};

			initPostsClassic();
	}

		////////////////////////////////////////////
		// PostsCarousel 						////
		////////////////////////////////////////////


	function initPostsCarousel( $scope ) {

			var elementSettings = ee.getElementSettings( $scope ),
				uniqueId 	= ee.getUniqueLoopScopeId( $scope ),
				scopeId 	= $scope.data('id'),
				$swiper 	= $scope.find('.ee-swiper__container'),
				$thumbs 	= $scope.find('.ee-grid__item');

			ee.applyReducedMotionClass( $scope );

			if ( ! $swiper.length ) {
				return;
			}

			var swiper = null,
				settings = {
					scope 	: $scope,
					id 		: uniqueId,
					stretch : true,
					element : {
						autoHeight 						: 'yes' === elementSettings.carousel_autoheight,
						autoplay 						: 'yes' === elementSettings.carousel_autoplay,
						loop 							: 'yes' === elementSettings.carousel_loop,
						direction 						: elementSettings.carousel_direction,
						effect 							: elementSettings.carousel_effect,
						speed 							: elementSettings.carousel_speed.size || 500,
						resistance 						: elementSettings.carousel_resistance_ratio.size || 0.25,
						autoplaySpeed 					: elementSettings.carousel_autoplay ? elementSettings.carousel_autoplay_speed : false,
						slidesPerView 					: elementSettings.carousel_slides_per_view_mobile,
						slidesPerColumn 				: 'vertical' === elementSettings.carousel_direction ? 1 : elementSettings.carousel_slides_per_column_mobile,
						slidesPerGroup 					: elementSettings.carousel_slides_to_scroll_mobile,
						spaceBetween 					: elementSettings.carousel_grid_columns_spacing_mobile.size || 0,
						disableOnInteraction 			: 'yes' === elementSettings.carousel_pause_on_interaction,
						stopOnHover 					: 'yes' === elementSettings.carousel_stop_on_hover,
						arrows 							: '' !== elementSettings.carousel_arrows,
						arrowPrev 						: '.ee-swiper__button--prev',
						arrowNext 						: '.ee-swiper__button--next',
						freeMode 						: 'yes' === elementSettings.carousel_free_mode,
						freeModeSticky 					: 'yes' === elementSettings.carousel_free_mode_sticky,
						freeModeMomentum 				: 'yes' === elementSettings.carousel_free_mode_momentum,
						freeModeMomentumBounce 			: 'yes' === elementSettings.carousel_free_mode_momentum_bounce,
						freeModeMomentumRatio 			: elementSettings.carousel_free_mode_momentum_ratio ? elementSettings.carousel_free_mode_momentum_ratio.size : false,
						freeModeMomentumVelocityRatio 	: elementSettings.carousel_free_mode_momentum_velocity ? elementSettings.carousel_free_mode_momentum_velocity.size : false,
						freeModeMomentumBounceRatio 	: elementSettings.carousel_free_mode_momentum_bounce_ratio ? elementSettings.carousel_free_mode_momentum_bounce_ratio.size : false,
						pagination 						: '' !== elementSettings.carousel_pagination,
						paginationType 					: elementSettings.carousel_pagination_type,
						paginationClickable 			: 'yes' === elementSettings.carousel_pagination_clickable,
						slideChangeTriggerResize 		: 'yes' === elementSettings.carousel_slide_change_resize,
						breakpoints 		: {
							tablet : {
								slidesPerView 	: elementSettings.carousel_slides_per_view_tablet,
								slidesPerColumn : 'vertical' === elementSettings.carousel_direction ? 1 : elementSettings.carousel_slides_per_column_tablet,
								slidesPerGroup 	: elementSettings.carousel_slides_to_scroll_tablet,
								spaceBetween 	: elementSettings.carousel_grid_columns_spacing_tablet.size || 0,
							},
							desktop : {
								slidesPerView 	: elementSettings.carousel_slides_per_view,
								slidesPerColumn : 'vertical' === elementSettings.carousel_direction ? 1 : elementSettings.carousel_slides_per_column,
								slidesPerGroup 	: elementSettings.carousel_slides_to_scroll,
								spaceBetween 	: elementSettings.carousel_grid_columns_spacing.size || 0,
							},
						},
					},
					default : {
						slidesPerView 	: 1,
						slidesPerGroup 	: 1,
						slidesPerColumn : 1,
						spaceBetween 	: 6,
						breakpoints 	: {
							tablet : {
								slidesPerView 	: 2,
								slidesPerGroup 	: 1,
								slidesPerColumn : 1,
								spaceBetween 	: 12,
							},
							desktop : {
								slidesPerView 	: 3,
								slidesPerGroup 	: 1,
								slidesPerColumn : 1,
								spaceBetween 	: 24,
							},
						},
					},
				};

			var initPostsCarousel = function() {
				ee.makeFocusableControl( $scope.find( '.ee-swiper__button--prev' ), 'Previous slide' );
				ee.makeFocusableControl( $scope.find( '.ee-swiper__button--next' ), 'Next slide' );

				var swiperArgs = ee.Carousel( $swiper, settings );

				if ( 'undefined' === typeof Swiper ) {
					const asyncSwiper = elementorFrontend.utils.swiper;

					new asyncSwiper( $swiper, swiperArgs ).then( function( newSwiperInstance ) {
						ee.ltxeSetInstance( 'posts-swiper', uniqueId, newSwiperInstance );
						ee.Carousel.onAfterInit( $swiper, newSwiperInstance, settings );
					} );
				} else {
					swiper = new Swiper( $swiper, swiperArgs );
					ee.ltxeSetInstance( 'posts-swiper', uniqueId, swiper );
					ee.Carousel.onAfterInit( $swiper, swiper, settings );
				}
			};

			ee.onElementRemove( $scope, function() {
				$scope.find('.ee-swiper__container').each( function() {
					if ( $(this).data('swiper') ) {
						$(this).data('swiper').destroy();
					}
				});
			});

			initPostsCarousel();
	}


	$( window ).on( 'elementor/frontend/init', function () {
		var classicSkins = [ 'classic', 'editorial', 'studio', 'brutalist', 'minimal', 'glass', 'magazine', 'lift', 'capsule', 'cinema', 'soft', 'list', 'featured-grid', 'timeline' ];
		elementorFrontend.hooks.addAction( 'frontend/element_ready/posts-extra.carousel', function ( $scope ) {
			if ( $scope.data( 'ltxeOnce_posts-extra.carousel' ) ) {
				return;
			}
			$scope.data( 'ltxeOnce_posts-extra.carousel', true );
			initPostsCarousel( $scope );
		} );
		classicSkins.forEach( function ( skin ) {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/posts-extra.' + skin, function ( $scope ) {
				var flag = 'ltxeOnce_posts-extra.' + skin;
				if ( $scope.data( flag ) ) {
					return;
				}
				$scope.data( flag, true );
				initPostsClassic( $scope );
			} );
		} );
	} );
}( jQuery ) );
