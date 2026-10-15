( function ( $ ) {
	'use strict';

	var exitTimer = null;

	function isJson( item ) {
		item = typeof item !== 'string' ? JSON.stringify( item ) : item;
		try {
			item = JSON.parse( item );
		} catch ( e ) {
			return false;
		}
		return typeof item === 'object' && item !== null;
	}

	function parseAjaxResponse( data, container ) {
		data = data.replace( /\<\!\[CDATA\[\/\/\>\<\!\-\-/gi, '' );
		data = data.replace( /\/\/\-\-\>\<\!\]\]\>/gi, '' );
		var head = data.match( /<\s*head.*>[\s\S]*<\s*\/head\s*>/ig ).join( '' );
		var body = data.match( /<\s*body.*>[\s\S]*<\s*\/body\s*>/ig ).join( '' );
		head = head.replace( /<\s*head/gi, '<div' );
		head = head.replace( /<\s*\/head/gi, '</div' );
		body = body.replace( /<\s*body/gi, '<div' );
		body = body.replace( /<\s*\/body/gi, '</div' );
		var $response = $( head );
		if ( container && $( body ).find( container ).length ) {
			$response = $( head ).add( $( body ).find( container ) );
		} else {
			$response = $( head ).add( $( body ) );
		}
		return $response;
	}

	if ( ! $.exitIntent ) {
		function trackLeave( ev ) {
			if ( ev.clientY > 0 ) {
				return;
			}
			if ( exitTimer ) {
				clearTimeout( exitTimer );
			}
			if ( $.exitIntent.settings.sensitivity <= 0 ) {
				$.event.trigger( 'exitintent' );
				return;
			}
			exitTimer = setTimeout( function () {
				exitTimer = null;
				$.event.trigger( 'exitintent' );
			}, $.exitIntent.settings.sensitivity );
		}
		function trackEnter() {
			if ( exitTimer ) {
				clearTimeout( exitTimer );
				exitTimer = null;
			}
		}
		$.exitIntent = function ( enable, options ) {
			$.exitIntent.settings = $.extend( $.exitIntent.settings, options );
			if ( enable == 'enable' ) {
				$( window ).mouseleave( trackLeave );
				$( window ).mouseenter( trackEnter );
			} else if ( enable == 'disable' ) {
				trackEnter();
				$( window ).unbind( 'mouseleave', trackLeave );
				$( window ).unbind( 'mouseenter', trackEnter );
			} else {
				throw "Invalid parameter to jQuery.exitIntent -- should be 'enable'/'disable'";
			}
		};
		$.exitIntent.settings = {
			sensitivity: 300
		};
	}

	var LandTechExtrasUtils = {
		isJson: isJson,
		parseAjaxResponse: parseAjaxResponse
	};

	var ee = {
		Popup: {},
		getElementSettings: function ( $scope ) {
			return window.ee.getElementSettings( $scope );
		},
		applyReducedMotionClass: function ( $scope ) {
			window.ee.applyReducedMotionClass( $scope );
		},
		prefersReducedMotion: function () {
			return window.ee.prefersReducedMotion();
		},
		getWindow: function () {
			return window.ee.getWindow();
		},
		setAriaExpanded: function ( $trigger, expanded, controlsId ) {
			window.ee.setAriaExpanded( $trigger, expanded, controlsId );
		},
		prepareGlightboxInlineNode: function ( $element ) {
			return window.ee.prepareGlightboxInlineNode( $element );
		},
		markGlightboxPopupReady: function ( scopeId ) {
			window.ee.markGlightboxPopupReady( scopeId );
		},
		unmarkGlightboxPopupReady: function ( scopeId ) {
			window.ee.unmarkGlightboxPopupReady( scopeId );
		},
		ltxePopupFocusTrap: function ( modal, closePopup ) {
			window.ee.ltxePopupFocusTrap( modal, closePopup );
		},
		refreshElements: function ( $container, refreshable ) {
			window.ee.refreshElements( $container, refreshable );
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
		releaseFocusTrap: function ( $container, namespace ) {
			if ( $container && $container.length ) {
				$container.off( 'keydown.' + namespace );
			}
		}
	};

	function initPopupWidget( $scope ) {

			if ( $scope.data( 'ltxePopupInit' ) ) {
				return;
			}
			$scope.data( 'ltxePopupInit', true );

			var elementSettings 	= ee.getElementSettings( $scope );
			ee.Popup.elementSettings = elementSettings;
			ee.applyReducedMotionClass( $scope );

			var scopeId 			= $scope.data('id'),
				$trigger 			= $scope.find( '.ee-popup__trigger' ),
				$content 			= $scope.find( '.ee-popup__content' ),
				storageID 			= 'ltxe_PopupShown_' + scopeId,
				timesShow 			= elementSettings.popup_times,
				$window 			= ee.getWindow(),
				$html 				= elementorFrontend.isEditMode() ? $window.find('html') : $('html'),
				persist 			= elementSettings.popup_persist,
				isAdmin 			= 'undefined' !== typeof elementSettings.popup_open_admin && 'yes' === elementSettings.popup_open_admin,
				$closeButton 		= 'default' === elementSettings.popup_close_button_position ? $scope.find( '.ee-popup__footer__button' ) : $scope.find( elementSettings.popup_close_button_selector ),
				a11yNamespace       = 'ltxePopup' + scopeId,
				lastFocusedEl       = null,

				popupVAlignClass 	= 'mfp-popup--valign-' + elementSettings.popup_valign,
				closeHAlignClass 	= 'mfp-close--halign-' + elementSettings.popup_close_halign,
				closeVAlignClass 	= 'mfp-close--valign-' + elementSettings.popup_close_valign,
				noOverlayClass 	 	= 'yes' === elementSettings.popup_no_overlay ? 'ee-mfp-popup--no-overlay' : 'ee-mfp-popup--overlay',

				glightboxInstance 	= null;

			function closeForeignLightbox() {
				var foreign = document.querySelector( '.glightbox-container:not(.ee-mfp-popup-' + scopeId + ')' );
				if ( foreign ) {
					var closeBtn = foreign.querySelector( '.gclose, .gbtn.gclose' );
					if ( closeBtn ) {
						closeBtn.click();
					}
					document.body.classList.remove( 'glightbox-open' );
					document.documentElement.classList.remove( 'glightbox-open' );
				}
			}

			function buildGlightbox() {
				var popupType 	= elementSettings.popup_type,
					elements 	= [],
					slide 		= {},
					containerClass = 'ee-mfp-popup ee-mfp-popup-' + scopeId + ' ' + noOverlayClass + ' ' + popupVAlignClass + ' ' + elementSettings.popup_animation;

				if ( 'iframe' === popupType ) {
					slide = {
						href: $trigger.attr( 'href' ) || $content.data( 'ee-popup-url' ),
						type: 'iframe',
					};
				} else if ( 'image' === popupType ) {
					slide = {
						href: $trigger.attr( 'href' ) || $content.data( 'ee-popup-url' ),
						type: 'image',
					};
				} else if ( 'url' === popupType ) {
					slide = {
						href: $content.data( 'ee-popup-url' ),
						type: 'ajax',
					};
				} else {
					slide = {
						content: ee.prepareGlightboxInlineNode( $content ),
						type: 'inline',
					};
				}

				elements.push( slide );

				var openEffect = '' !== elementSettings.popup_animation && ! ee.prefersReducedMotion() ? 'zoom' : 'fade';
				var closeEffect = '' !== elementSettings.popup_animation && ! ee.prefersReducedMotion() ? 'zoom' : 'fade';

				return GLightbox( {
					elements: elements,
					closeButton: elementSettings.popup_close_position || false,
					touchNavigation: true,
					loop: false,
					autoplayVideos: false,
					openEffect: openEffect,
					closeEffect: closeEffect,
					closeOnOutsideClick: 'yes' === elementSettings.popup_close_on_bg,
					keyboardNavigation: 'yes' === elementSettings.popup_close_on_escape,
					cssClasses: {
						container: 'glightbox-container ' + containerClass,
					},
					slideHTML: '<div class="gslide"><div class="gslide-inner-content"><div class="ginner-container"><div class="gslide-media"></div></div></div><button class="gclose gbtn ee-popup__close mfp-close ' + closeHAlignClass + ' ' + closeVAlignClass + ' eicon-close" title="Close" aria-label="Close"></button></div>',
					afterOpen: function() {
						var wrap = document.querySelector( '.glightbox-container' );
						if ( wrap ) {
							wrap.classList.add( 'ee-mfp-popup', 'ee-mfp-popup-' + scopeId, noOverlayClass, popupVAlignClass );
						}
						ee.markGlightboxPopupReady( scopeId );
						onOpen( glightboxInstance, $trigger );
						ee.setAriaExpanded( $trigger, true, $content.attr( 'id' ) );
						ee.bindEscapeClose( a11yNamespace, function() {
							closeLightbox();
						} );
						var wrapEl = document.querySelector( '.glightbox-container.ee-mfp-popup-' + scopeId ) || document.querySelector( '.glightbox-container' );
						ee.ltxePopupFocusTrap( wrapEl, closeLightbox );
					},
					beforeClose: function() {
						ee.unmarkGlightboxPopupReady( scopeId );
						$scope.trigger( 'ltxe:popup:closed' );
						ee.unbindEscapeClose( a11yNamespace );
						ee.releaseFocusTrap( $( '.glightbox-container .gslide.open' ), a11yNamespace );
						ee.setAriaExpanded( $trigger, false, $content.attr( 'id' ) );
						if ( lastFocusedEl && lastFocusedEl.focus ) {
							lastFocusedEl.focus();
						}
						if ( 'yes' !== elementSettings.popup_prevent_scroll ) {
							$html.css( { overflow: '' } );
						}
					},
					onSlideBeforeLoad: function( data ) {
						if ( 'ajax' !== data.slideType || 'url' !== popupType ) {
							return;
						}

						var xhr = data.xhr;

						if ( ! xhr || ! xhr.responseText ) {
							return;
						}

						var $clone = $content.clone(),
							container = ( '' !== elementSettings.popup_url_container ) ? elementSettings.popup_url_container : false,
							$appendedContent = LandTechExtrasUtils.parseAjaxResponse( xhr.responseText, container );

						$clone.removeClass( 'mfp-hide glightbox-hide' ).find( '.ee-popup__content__body' ).append( $appendedContent );
						data.slideNode.querySelector( '.gslide-media' ).appendChild( $clone.get( 0 ) );
					},
				} );
			}

			function isThisOpen() {
				return !!( document.querySelector( '.glightbox-container.ee-mfp-popup-' + scopeId ) );
			}

			function openLightbox() {
				lastFocusedEl = document.activeElement;
				closeForeignLightbox();
				if ( ! glightboxInstance ) {
					glightboxInstance = buildGlightbox();
				}
				glightboxInstance.open();
			}

			function closeLightbox() {
				if ( glightboxInstance ) {
					glightboxInstance.close();
				}
			}

			function initPopup() {

				if ( $scope.is(':not(:visible)') )
					return;

				if ( $trigger.length ) {
					$trigger.attr( 'aria-haspopup', 'dialog' );
					ee.setAriaExpanded( $trigger, false, $content.attr( 'id' ) );
				}

				if ( $closeButton.length ) {
					$closeButton.on( 'click', function( e ) {
						e.preventDefault();
						e.stopPropagation();
						closeLightbox();
					});
				}

				if ( elementorFrontend.isEditMode() ) {
					closeLightbox();
					glightboxInstance = buildGlightbox();

					if ( 'yes' === elementSettings.popup_open )
						openLightbox();
				} else {
					bindBehaviour();
				}
			}

			function bindBehaviour() {

				switch ( elementSettings.popup_trigger ) {

					case 'click':
						bindClick();
						break;

					case 'instant':
						bindInstant();
						break;

					case 'scroll':
						bindScroll();
						break;

					case 'intent':
						bindIntent();
						break;

					case 'delay':
						var delayMs = elementSettings.trigger_delay ? ( parseFloat( elementSettings.trigger_delay ) * 1000 ) : elementSettings.popup_delay;
						setTimeout( openPopup, delayMs );
						break;

					case 'scroll_pct':
					case 'inactivity':
						break;

					default:
						break;
				}

				$( document ).on( 'landtech_extras/popup_advanced_open', function( _e, openedId ) {
					if ( String( openedId ) === String( $scope.data( 'id' ) ) ) {
						openPopup();
					}
				} );
			}

			function bindClick() {

				if ( 'text' !== elementSettings.popup_click_target ) {
					var $custom_trigger = null,
						_selector 		= null,
						$selector 		= null,
						$template 		= $scope.closest( '.elementor-template' );

					if ( 'id' === elementSettings.popup_click_target ) {
						_selector = '#' + $trigger.data('trigger-id');
					} else if ( 'class' === elementSettings.popup_click_target ) {
						_selector = '.' + $trigger.data('trigger-class');
					}

					if ( $template.length && $scope.data( 'ee-template-widget-id' ) ) {
						$selector = $template.find( _selector );
					} else {
						$selector = $(document).find( _selector );
					}

					if ( $selector ) $custom_trigger = $selector;

					if ( $custom_trigger.length ) {
						$custom_trigger.on( 'click', function( e ) {
							e.preventDefault();
							e.stopPropagation();
							openLightbox();
						});
					}
				} else {
					$trigger.on( 'click', function( e ) {
						e.preventDefault();
						openLightbox();
					} );
				}
			}

			function bindInstant() {
				setTimeout( openPopup, elementSettings.popup_delay );
			}

			function bindScroll() {
				var doc = document.documentElement,
					limit;

				if ( 'amount' === elementSettings.popup_scroll_type ) {
					limit = elementSettings.popup_scroll_amount;
				} else if ( 'element' === elementSettings.popup_scroll_type ) {
					var scrollElement = $( '#' + elementSettings.popup_scroll_element );

					if ( scrollElement.length ) {
						limit = scrollElement.offset().top;
					}
				}

				$window.on( 'scroll', function() {
					var scrollTop = ( window.pageYOffset || doc.scrollTop )  - ( doc.clientTop || 0 );
					if ( scrollTop >= limit ) openPopup();
				});
			}

			function bindIntent() {
				var exitIntentArgs = {};

				if ( elementSettings.popup_intent_sensitivity ) {
					exitIntentArgs.sensitivity = elementSettings.popup_intent_sensitivity.size;
				}
				
				$.exitIntent( 'enable', exitIntentArgs );

				$(document).bind( 'exitintent', openPopup );
			}

			function onOpen( instance ) {

				if ( 'yes' === elementSettings.refresh_widgets && instance && instance.activeSlide ) {
					var slideNode = instance.activeSlide.slideNode || instance.activeSlide;
					ee.refreshElements( $( slideNode ), true );
				}

				if ( 'yes' !== elementSettings.popup_prevent_scroll ) {
					$html.css({ 'overflow' : '' });
				}
			}

			function openPopup() {
				if ( isThisOpen() ) {
					return;
				}

				var gateEvent = $.Event( 'landtech_extras/popup_before_open' );
				$( document ).trigger( gateEvent, [ scopeId ] );
				if ( gateEvent.isDefaultPrevented() ) {
					return;
				}

				var now 		= new Date(),
					dateShown 	= false,
					timesShown 	= 0,
					canShow 	= false,
					datePassed 	= false,
					timesPassed = false,
					storageData = {
						date : now,
						times : timesShown,
					},
					storedString = localStorage.getItem( storageID ),
					storedData 	= LandTechExtrasUtils.isJson( storedString ) ? JSON.parse( storedString ) : storedString;

				if ( ! persist /* && ! isAdmin */ ) {
					if ( storedData !== null ) {
						dateShown = LandTechExtrasUtils.isJson( storedString ) ? Date.parse( storedData.date ) : storedString;
						timesShown = LandTechExtrasUtils.isJson( storedString ) ? storedData.times : timesShown;
					}

					timesPassed = timesShown >= timesShow;
					datePassed 	= ( now - dateShown ) >= ( elementSettings.popup_days * 86400000 );
					canShow 	= ! dateShown || ! timesPassed || ( timesPassed && datePassed );

					if ( canShow ) {
						openLightbox();
						storageData.times = timesShown + 1;
						storageData.date = now;
						localStorage.setItem( storageID, JSON.stringify( storageData ) );
					}
				} else {
					localStorage.removeItem( storageID );
					openLightbox();
				}
			}

			initPopup();
	}


	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-popup.classic', initPopupWidget );
	} );
}( jQuery ) );
