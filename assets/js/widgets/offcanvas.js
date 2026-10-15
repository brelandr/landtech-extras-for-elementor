( function ( $ ) {
	'use strict';

	window.LandTechExtrasOffcanvas = window.LandTechExtrasOffcanvas || function () {
		var self = this;

		self.initialized = false;
		self.controller = null;

		self.init = function () {
			self.controller = new slidebars();
			self.controller.init();
			self.initialized = true;
		};
	};

	if ( ! window.ltxeOffcanvas ) {
		window.ltxeOffcanvas = new window.LandTechExtrasOffcanvas();
	}

	var offcanvas = window.ltxeOffcanvas;

	function attachOffcanvas() {
		if ( window.elementorFrontend ) {
			window.elementorFrontend.eeOffcanvas = offcanvas;
		}
	}

	attachOffcanvas();

	var ee = {
		Offcanvas: {},
		getElementSettings: function ( $scope ) {
			return window.ee.getElementSettings( $scope );
		},
		applyReducedMotionClass: function ( $scope ) {
			window.ee.applyReducedMotionClass( $scope );
		},
		setAriaExpanded: function ( $trigger, expanded, controlsId ) {
			window.ee.setAriaExpanded( $trigger, expanded, controlsId );
		},
		makeFocusableControl: function ( $el, label ) {
			window.ee.makeFocusableControl( $el, label );
		},
		getWindow: function () {
			return window.ee.getWindow();
		},
		onElementRemove: function ( $element, callback ) {
			window.ee.onElementRemove( $element, callback );
		},
		refreshElements: function ( $container, refreshable ) {
			window.ee.refreshElements( $container, refreshable );
		},
		hasMultipleScopeId: function ( scopeId ) {
			var $clones = $( '[data-id="' + scopeId + '"]' );
			return 1 !== $clones.length;
		},
		getOffcanvasUniqueScopeId: function ( $scope ) {
			var scopeId = $scope.data( 'id' );
			var $clones = $( '[data-id="' + scopeId + '"]' );

			if ( ! ee.hasMultipleScopeId( scopeId ) ) {
				return scopeId;
			}

			$clones.each( function ( index ) {
				$( this ).attr( 'data-offcanvas-index', index );
			} );

			return scopeId + '_' + $scope.data( 'offcanvas-index' );
		},
		bindEscapeClose: function ( namespace, closeFn ) {
			$( document ).off( 'keydown.' + namespace ).on( 'keydown.' + namespace, function ( e ) {
				if ( 27 === e.which ) {
					closeFn( e );
				}
			} );
		},
		unbindEscapeClose: function ( namespace ) {
			$( document ).off( 'keydown.' + namespace );
		},
		focusTrap: function ( $container, namespace ) {
			if ( ! $container || ! $container.length ) {
				return;
			}

			var focusableSelector = 'a[href], button:not([disabled]), textarea, input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])';
			var $focusable = $container.find( focusableSelector ).filter( ':visible' );

			if ( ! $focusable.length ) {
				return;
			}

			var $first = $focusable.first();
			var $last = $focusable.last();

			$first.focus();

			$container.off( 'keydown.' + namespace ).on( 'keydown.' + namespace, function ( e ) {
				if ( 9 !== e.which ) {
					return;
				}

				if ( e.shiftKey ) {
					if ( document.activeElement === $first[ 0 ] ) {
						e.preventDefault();
						$last.focus();
					}
				} else if ( document.activeElement === $last[ 0 ] ) {
					e.preventDefault();
					$first.focus();
				}
			} );
		},
		releaseFocusTrap: function ( $container, namespace ) {
			if ( $container && $container.length ) {
				$container.off( 'keydown.' + namespace );
			}
		}
	};

	function initOffcanvas( $scope ) {

			ee.Offcanvas.elementSettings 	= ee.getElementSettings( $scope );
			ee.applyReducedMotionClass( $scope );

			var scopeId 		= $scope.data('id'),
				scopeUniqueId 	= ee.getOffcanvasUniqueScopeId( $scope ),
				slidebarPos 	= ee.Offcanvas.elementSettings.position,
				slidebarAnim 	= ee.Offcanvas.elementSettings.animation,
				slidebarId 		= 'oc' + scopeUniqueId,
				a11yNamespace   = 'ltxeOffcanvas' + scopeUniqueId,
				lastFocusedEl   = null,
				$body 			= $('body'),
				$window 		= ee.getWindow(),
				scroll 			= $window.scrollTop(),
				$trigger 		= $scope.find( '.ee-offcanvas__trigger' ),
				$content 		= $scope.find( '.ee-offcanvas__content' ),
				$html 			= elementorFrontend.isEditMode() ? window.elementor.$previewContents.find('html') : $('html'),
				$close 			= $( '.ee-offcanvas__header__close' ),
				$overlay 		= $( '<div class="ee-offcanvas__overlay"></div>' ),
				$wrapper 		= $( '<div class="ee-offcanvas__container" canvas="container"></div>' ),
				$slidebar 		= $( '<div class="ee-offcanvas__slidebar ee-offcanvas__slidebar--' + scopeId + '" id="' + slidebarId + '" off-canvas="' + slidebarId + ' ' + slidebarPos + ' ' + slidebarAnim + '" />' );

			ee.Offcanvas.setTriggers = function() {
				if ( 'id' === ee.Offcanvas.elementSettings.trigger_source && '' !== ee.Offcanvas.elementSettings.trigger_id ) {
					$trigger = $( '#' + ee.Offcanvas.elementSettings.trigger_id );
					$trigger
						.addClass( 'ee-offcanvas__trigger' );
				} else if ( 'class' === ee.Offcanvas.elementSettings.trigger_source && '' !== ee.Offcanvas.elementSettings.trigger_class ) {
					$trigger = $( '.' + ee.Offcanvas.elementSettings.trigger_class );
					$trigger
						.addClass( 'ee-offcanvas__trigger' );
				}

				$trigger.attr( 'data-slidebar-id', slidebarId );
				$trigger.each( function() {
					var $el = $( this );
					var expanded = false;

					// setTriggers() runs before slidebars creates the controller.
					if ( offcanvas.controller && 'function' === typeof offcanvas.controller.isActiveSlidebar ) {
						expanded = offcanvas.controller.isActiveSlidebar( slidebarId );
					}

					ee.setAriaExpanded( $el, expanded, slidebarId );
					ee.makeFocusableControl( $el, $el.attr( 'aria-label' ) || 'Menu' );
				} );

				if ( 'id' === ee.Offcanvas.elementSettings.header_close_source && '' !== ee.Offcanvas.elementSettings.header_close_id ) {
					$close = $content.find( '#' + ee.Offcanvas.elementSettings.header_close_id );
					$close.addClass( 'ee-offcanvas__close' );
				}

				if ( 'class' === ee.Offcanvas.elementSettings.header_close_source && '' !== ee.Offcanvas.elementSettings.header_close_class ) {
					$close = $content.find( '.' + ee.Offcanvas.elementSettings.header_close_class );
					$close.addClass( 'ee-offcanvas__close' );
				}

				$close.each( function() {
					ee.makeFocusableControl( $( this ), 'Close menu' );
				} );
			};

			ee.Offcanvas.prepare = function() {

				$scope.attr( 'data-slidebar-id', slidebarId );

				// Offcanvas doesn't work with 100% height on the document,
				// so we take the risk of removing it
				if ( ee.Offcanvas.elementSettings.scroll_fix ) {
					$('html').addClass( 'ee-offcanvas-scroll-fix-' + ee.Offcanvas.elementSettings.scroll_fix ).css( { 'height' : 'auto' } );
				}

				// Wrap body in container only if it's not already wrapped
				// which is the case when offcanvas is already available through
				// other widgets on the page
				if ( ! $body.find( '.ee-offcanvas__container' ).length )
					$body.wrapInner( $wrapper );
				
				// Make sure we redefine the wrapper after it wrap body
				$wrapper = $body.find( '.ee-offcanvas__container' );

				// Remove slidebar if it exists
				$( '#' + slidebarId ).remove();

				// Append content to slidebar
				$slidebar.append( $content );

				// Add slidebar to body
				$body.prepend( $slidebar );
				$slidebar.attr( {
					role: 'dialog',
					'aria-modal': 'true',
					'aria-hidden': 'true',
				} );
				if ( $slidebar[ 0 ] ) {
					$slidebar[ 0 ].inert = true;
				}

				if ( ! $body.find( '.ee-offcanvas__overlay' ).length ) {
					$wrapper.append( $overlay );
				} else {
					$overlay = $wrapper.find( '> .ee-offcanvas__overlay' );
				}

				$html.addClass('ee-offcanvas--closed');
			};

			ee.Offcanvas.onResize = function() {
				if ( elementorFrontend.isEditMode() )
					offcanvas.controller.css();
			};

			ee.Offcanvas.destroy = function() {

				// Remove this slidebar
				$slidebar.remove();
			};

			ee.Offcanvas.events = function() {

				if ( elementorFrontend.isEditMode() )
					$slidebar._resize( ee.Offcanvas.onResize );

				$trigger.on( 'click', function ( event ) {

					// Stop default action and bubbling
					event.stopPropagation();
					event.preventDefault();

					lastFocusedEl = document.activeElement;

					// Restyle elements
					offcanvas.controller.css();

					// Toggle this offcanvas
					if ( offcanvas.controller.isActiveSlidebar( slidebarId ) ) {
						offcanvas.controller.close( slidebarId );
					} else {
						offcanvas.controller.open( slidebarId );
					}

					// Add active class to trigger
					$(this).addClass( 'ee--is-active' );
				} );

				$overlay.attr( 'role', 'presentation' );

				$close.on( 'click', function ( event ) {

					// Stop default action and bubbling
					event.stopPropagation();
					event.preventDefault();
					
					offcanvas.controller.close();
				} );

				$overlay.on('click', function () {
					if ( offcanvas.controller.isActiveSlidebar( slidebarId ) )
						offcanvas.controller.close();
				});

				$window.on( 'scroll', function() {
					scroll = this.scrollY;
				});

				$( offcanvas.controller.events ).on( 'opening', function( event, id ) {

					if ( slidebarId !== id ) {
						return;
					}

					var $element = $body.find( '.elementor-widget-ee-offcanvas[data-slidebar-id="' + id + '"]' ).data('id');

					ee.setAriaExpanded( $trigger, true, slidebarId );
					$slidebar.attr( 'aria-hidden', 'false' );
					if ( $slidebar[ 0 ] ) {
						$slidebar[ 0 ].inert = false;
					}
					ee.bindEscapeClose( a11yNamespace, function() {
						offcanvas.controller.close( slidebarId );
					} );

					// Add widget specific body class
					$body.addClass( 'ee-offcanvas--id-' + $element );

					$html.removeClass( 'ee-offcanvas--closed ee-offcanvas--open' );
					$body.removeClass( 'ee-offcanvas--closed ee-offcanvas--open' );
					$body.addClass( 'ee-offcanvas--opening' );
					$html.addClass( 'ee-offcanvas--opening' );

					ee.Offcanvas.setOverflows();

					// Run ready triggers for widgets inside panel
					if ( 'yes' === ee.Offcanvas.elementSettings.refresh_widgets ) {
						ee.refreshElements( $content, true );
					}
				} );

				$( offcanvas.controller.events ).on( 'opened', function( event, id ) {

					if ( slidebarId !== id ) {
						return;
					}

					$html.removeClass( 'ee-offcanvas--closed ee-offcanvas--opening' );
					$body.removeClass( 'ee-offcanvas--closed ee-offcanvas--opening' );
					$body.addClass('ee-offcanvas--open');
					$html.addClass('ee-offcanvas--open');

					if ( 'yes' === ee.Offcanvas.elementSettings.container_scroll ) {
						$body.addClass('ee-offcanvas--scroll');
					}

					ee.focusTrap( $slidebar, a11yNamespace );
				} );

				$( offcanvas.controller.events ).on( 'closing', function( event, id ) {

					if ( slidebarId !== id ) {
						return;
					}

					ee.unbindEscapeClose( a11yNamespace );
					ee.releaseFocusTrap( $slidebar, a11yNamespace );
					ee.setAriaExpanded( $trigger, false, slidebarId );
					$slidebar.attr( 'aria-hidden', 'true' );
					if ( $slidebar[ 0 ] ) {
						$slidebar[ 0 ].inert = true;
					}

					$html.removeClass( 'ee-offcanvas--open ee-offcanvas--opening ee-offcanvas--closed' );
					$body.removeClass( 'ee-offcanvas--open ee-offcanvas--opening ee-offcanvas--closed' );
					$body.addClass( 'ee-offcanvas--closing' );
					$html.addClass( 'ee-offcanvas--closing' );
				} );

				$( offcanvas.controller.events ).on( 'closed', function( event, id ) {

					if ( slidebarId !== id ) {
						return;
					}

					$body.removeClass( 'ee-offcanvas--open ee-offcanvas--closing' );
					$html.removeClass( 'ee-offcanvas--open ee-offcanvas--closing' );

					ee.Offcanvas.removeOverflows();

					if ( 'yes' === ee.Offcanvas.elementSettings.container_scroll ) {
						$body.removeClass('ee-offcanvas--scroll');
					}

					$body.removeClass( function ( index, className ) {
						return (className.match (/(^|\s)ee-offcanvas--id-\S+/g) || []).join(' ');
					});

					$body.addClass( 'ee-offcanvas--closed' );
					$html.addClass( 'ee-offcanvas--closed' );

					$( '.ee-offcanvas__trigger' ).removeClass( 'ee--is-active' );

					if ( lastFocusedEl && lastFocusedEl.focus ) {
						lastFocusedEl.focus();
					}

					$(window).trigger('resize');
				} );

				ee.onElementRemove( $scope, function() {
					ee.Offcanvas.destroy();
				});
			};

			ee.Offcanvas.anchorNavigation = function() {

				var $links = $content.find('a[href*=\\#]:not([href^="%23elementor-action"]):not([href^="#elementor-action"])');

				$links.each( function() {
					var $link 	= $(this),
						url 	= $link.attr('href'),
						hash 	= url.substring( url.indexOf('#') + 1 ),
						$el 	= $( '#' + hash ),
						speed 	= ( 'undefined' !== typeof ee.Offcanvas.elementSettings.anchor_navigation_speed.size ) ? ee.Offcanvas.elementSettings.anchor_navigation_speed.size : 0;

					if ( ! $el.length )
						return;

					$link.on( 'click', function( e ) {

						e.preventDefault();
						e.stopPropagation();

						scroll = $el.offset().top + $wrapper.scrollTop();
						
						$wrapper.animate({ scrollTop: scroll }, speed, function(){
							if( 'yes' === ee.Offcanvas.elementSettings.anchor_navigation_close ) {
								offcanvas.controller.close();
							}
						});
					});
				});

			};

			ee.Offcanvas.setOverflows = function() {
				// Get current scroll
				var _scroll = $window.scrollTop();

				$('html').css( { 'height' : '100%' } );

				$wrapper.on( 'scroll', ee.Offcanvas.onWrapperScroll );

				$wrapper.animate({ scrollTop : scroll }, 0 );

				$overlay.css({'top' : scroll });
			};

			ee.Offcanvas.onWrapperScroll = function( event ) {
				$overlay.css({
					'top' : $wrapper.scrollTop(),
				});
			};

			ee.Offcanvas.removeOverflows = function() {
				var _scroll = $wrapper.scrollTop();

				$wrapper.animate({ scrollTop : 0 }, 0 );

				$wrapper.off( 'scroll', ee.Offcanvas.onWrapperScroll );

				$html.css( { 'height' : 'auto' } );

				$('html, body').animate({ scrollTop : _scroll }, 0 );
			};

			ee.Offcanvas.getClickedTriggerId = function( $trigger ) {
				return $trigger.closest( '.elementor-element' ).data('id');
			};

			ee.Offcanvas.init = function() {

				ee.Offcanvas.setTriggers();

				if ( offcanvas.initialized )
					offcanvas.controller.close();

				if ( $body.is(':not(.ee-offcanvas)') ) {
					$body.addClass( 'ee-offcanvas' );
				}

				ee.Offcanvas.prepare();

				offcanvas.init();
				
				ee.Offcanvas.events();

				if ( 'yes' === ee.Offcanvas.elementSettings.anchor_navigation ) {
					ee.Offcanvas.anchorNavigation();
				}
			};

			ee.Offcanvas.init();
	}


	$( window ).on( 'elementor/frontend/init', function () {
		attachOffcanvas();
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-offcanvas.classic', function ( $scope ) {
			if ( $scope.data( 'ltxeOnce_ee-offcanvas.classic' ) ) {
				return;
			}
			$scope.data( 'ltxeOnce_ee-offcanvas.classic', true );
			initOffcanvas( $scope );
		} );
	} );
}( jQuery ) );
