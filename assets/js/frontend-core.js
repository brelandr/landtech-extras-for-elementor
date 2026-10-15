( function ( $, window ) {
	'use strict';

	window.LtxeUtils = window.LtxeUtils || {
		getSettings: function ( element ) {
			var el = element && element.jquery ? element[ 0 ] : element;
			if ( ! el ) {
				return {};
			}
			var raw = el.getAttribute( 'data-settings' ) || el.getAttribute( 'data-element_type' );
			try {
				return JSON.parse( el.getAttribute( 'data-settings' ) || '{}' );
			} catch ( e ) {
				return {};
			}
		},
		onReady: function ( callback ) {
			if ( 'loading' === document.readyState ) {
				document.addEventListener( 'DOMContentLoaded', callback );
			} else {
				callback();
			}
		},
		prefersReducedMotion: function () {
			return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		},
		debounce: function ( fn, ms ) {
			var t;
			return function () {
				var ctx = this;
				var args = arguments;
				window.clearTimeout( t );
				t = window.setTimeout( function () {
					fn.apply( ctx, args );
				}, ms );
			};
		},
	};

	window.ltxeActions = window.ltxeActions || {};

	/**
	 * U32 — single delegated click listener. Widgets emit data-ltxe-action
	 * instead of onclick / javascript: hrefs so script-src can omit unsafe-inline.
	 */
	document.addEventListener( 'click', function ( e ) {
		var node = e.target.closest ? e.target.closest( '[data-ltxe-action]' ) : null;
		var action = node && node.dataset ? node.dataset.ltxeAction : ( node ? node.getAttribute( 'data-ltxe-action' ) : '' );
		if ( action && window.ltxeActions[ action ] ) {
			window.ltxeActions[ action ]( e, node );
		}
	} );

	/**
	 * Apply values that used to live in HTML style="" via CSSOM (allowed without style-src unsafe-inline).
	 *
	 * @param {ParentNode} root Scope.
	 * @return {void}
	 */
	function applyCspSafeStyles( root ) {
		var scope = root && root.querySelectorAll ? root : document;
		scope.querySelectorAll( '.ltxe-cq-wrapper[data-ltxe-cq]' ).forEach( function ( el ) {
			var name = el.getAttribute( 'data-ltxe-cq' );
			if ( name ) {
				el.style.setProperty( 'container-type', 'inline-size' );
				el.style.setProperty( 'container-name', name );
			}
		} );
		scope.querySelectorAll( '[data-ltxe-style-max-width]' ).forEach( function ( el ) {
			el.style.maxWidth = el.getAttribute( 'data-ltxe-style-max-width' );
		} );
		scope.querySelectorAll( '[data-ltxe-hero-image]' ).forEach( function ( el ) {
			var url = el.getAttribute( 'data-ltxe-hero-image' );
			if ( url ) {
				el.style.backgroundImage = 'linear-gradient(135deg,rgba(15,23,42,.88),rgba(30,64,175,.72)),url("' + url.replace( /"/g, '' ) + '")';
			}
		} );
	}

	window.LtxeUtils.applyCspSafeStyles = applyCspSafeStyles;

	function bootCspStyles() {
		applyCspSafeStyles( document );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', bootCspStyles );
	} else {
		bootCspStyles();
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) {
					applyCspSafeStyles( $scope[ 0 ] );
				} );
			}
		} );
	}

	window.ee = window.ee || {};

	window.ee.getItems = function ( items, itemKey ) {
		if ( itemKey ) {
			var keyStack = itemKey.split( '.' ),
				currentKey = keyStack.splice( 0, 1 );

			if ( ! keyStack.length ) {
				return items[ currentKey ];
			}

			if ( ! items[ currentKey ] ) {
				return;
			}

			return window.ee.getItems( items[ currentKey ], keyStack.join( '.' ) );
		}

		return items;
	};

	window.ee.getElementSettings = function ( $element, setting ) {
		var elementSettings = {},
			modelCID = $element.data( 'model-cid' );

		if ( window.elementorFrontend && elementorFrontend.isEditMode() && modelCID ) {
			var settings = elementorFrontend.config.elements.data[ modelCID ],
				type = settings.attributes.widgetType || settings.attributes.elType,
				settingsKeys = elementorFrontend.config.elements.keys[ type ];

			if ( ! settingsKeys ) {
				settingsKeys = elementorFrontend.config.elements.keys[ type ] = [];

				jQuery.each( settings.controls, function ( name, control ) {
					if ( control.frontend_available ) {
						settingsKeys.push( name );
					}
				} );
			}

			jQuery.each( settings.getActiveControls(), function ( controlKey ) {
				if ( -1 !== settingsKeys.indexOf( controlKey ) ) {
					elementSettings[ controlKey ] = settings.attributes[ controlKey ];
				}
			} );
		} else {
			elementSettings = $element.data( 'settings' ) || {};
		}

		return window.ee.getItems( elementSettings, setting );
	};

	window.ee.getElementSkin = function ( $element ) {
		return $element.attr( 'data-widget_type' ).split( '.' )[ 1 ];
	};

	window.ee.getWindow = function () {
		return ( window.elementorFrontend && elementorFrontend.isEditMode() ) ? window.elementor.$previewContents : jQuery( window );
	};

	window.ee.onElementRemove = function ( $element, callback ) {
		if ( window.elementorFrontend && elementorFrontend.isEditMode() ) {
			elementor.channels.data.on( 'element:before:remove', function ( model ) {
				if ( $element.data( 'id' ) === model.id ) {
					callback();
				}
			} );
		}
	};

	window.ee.prefersReducedMotion = function () {
		return window.LtxeUtils.prefersReducedMotion();
	};

	window.ee.applyReducedMotionClass = function ( $scope ) {
		if ( window.ee.prefersReducedMotion() && $scope && $scope.length ) {
			$scope.addClass( 'ltxe-reduced-motion' );
		}
	};

	window.ee.makeFocusableControl = function ( $el, label ) {
		if ( ! $el || ! $el.length ) {
			return;
		}

		$el.attr( {
			role: 'button',
			tabindex: '0',
			'aria-label': label,
		} );

		$el.off( 'keydown.ltxeA11y' ).on( 'keydown.ltxeA11y', function ( e ) {
			if ( 13 === e.which || 32 === e.which ) {
				e.preventDefault();
				$el.trigger( 'click' );
			}
		} );
	};

	window.ee.setAriaExpanded = function ( $trigger, expanded, controlsId ) {
		if ( ! $trigger || ! $trigger.length ) {
			return;
		}

		$trigger.attr( 'aria-expanded', expanded ? 'true' : 'false' );

		if ( controlsId ) {
			$trigger.attr( 'aria-controls', controlsId );
		}
	};

	window.ee.getWidgetContentRoot = function ( $widgetScope ) {
		var $inner = $widgetScope.children( '.elementor-widget-container' );
		return $inner.length ? $inner : $widgetScope;
	};

	window.ee.prepareGlightboxInlineNode = function ( $element ) {
		var node = $element && $element.length ? $element.get( 0 ) : null;

		if ( node ) {
			node.classList.remove( 'mfp-hide', 'glightbox-hide' );
			node.style.removeProperty( 'display' );
		}

		return node;
	};

	window.ee.markGlightboxPopupReady = function ( scopeId ) {
		jQuery( '.glightbox-container.ee-mfp-popup-' + scopeId ).addClass( 'mfp-ready' );
	};

	window.ee.unmarkGlightboxPopupReady = function ( scopeId ) {
		jQuery( '.glightbox-container.ee-mfp-popup-' + scopeId ).removeClass( 'mfp-ready mfp-removing' );
	};

	window.ee.ltxePopupFocusTrap = function ( modal, closePopup ) {
		if ( ! modal || modal.getAttribute( 'data-ltxe-popup-trap' ) ) {
			return;
		}
		modal.setAttribute( 'data-ltxe-popup-trap', '1' );
		modal.setAttribute( 'role', 'dialog' );
		modal.setAttribute( 'aria-modal', 'true' );
		var focusable = modal.querySelectorAll( 'button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])' );
		var first = focusable[ 0 ];
		var last = focusable[ focusable.length - 1 ];
		modal.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key ) {
				closePopup();
				return;
			}
			if ( 'Tab' !== e.key ) {
				return;
			}
			if ( e.shiftKey && document.activeElement === first ) {
				e.preventDefault();
				last.focus();
			} else if ( ! e.shiftKey && document.activeElement === last ) {
				e.preventDefault();
				first.focus();
			}
		} );
		if ( first && first.focus ) {
			first.focus();
		}
	};

	window.ee.getRefreshableWidgets = function () {
		if ( 'undefined' === typeof landtechExtrasFrontendConfig ) {
			return false;
		}
		return landtechExtrasFrontendConfig.refreshableWidgets;
	};

	window.ee.refreshElement = function ( $element, refreshable ) {
		var elementType = $element.data( 'element_type' );

		if ( refreshable ) {
			if ( 'widget' === elementType ) {
				var widgetType = $element.data( 'widget_type' );
				if ( jQuery.inArray( widgetType, window.ee.getRefreshableWidgets() ) ) {
					elementorFrontend.elementsHandler.runReadyTrigger( $element );
				}
			} else {
				elementorFrontend.elementsHandler.runReadyTrigger( $element );
			}
		} else {
			elementorFrontend.elementsHandler.runReadyTrigger( $element );
		}
	};

	window.ee.refreshElements = function ( $container, refreshable ) {
		var selectors = [
			'.elementor-widget',
			'.elementor-section',
			'.elementor-column',
		];
		var excludes = [
			'.elementor-widget-form',
		];

		$container.each( function () {
			jQuery( this ).find( selectors.join( ',' ) ).each( function () {
				var $element = jQuery( this );
				var canRefresh = true;

				excludes.forEach( function ( selector ) {
					if ( $element.is( selector ) ) {
						canRefresh = false;
					}
				} );

				if ( canRefresh ) {
					window.ee.refreshElement( $element, refreshable );
				}
			} );
		} );
	};

	window.ee.getGlobalSettings = function ( section ) {
		if ( window.elementorFrontendConfig && section in elementorFrontendConfig.settings ) {
			return elementorFrontendConfig.settings[ section ];
		}

		if ( 'extras' === section ) {
			return {
				ltxe_tooltips_position: 'top',
				ltxe_tooltips_arrow_position_h: '',
				ltxe_tooltips_arrow_position_v: '',
				ltxe_tooltips_delay_in: {
					size: 0,
					unit: 'px',
				},
				ltxe_tooltips_delay_out: {
					size: 0,
					unit: 'px',
				},
				ltxe_tooltips_duration: {
					size: 0.2,
					unit: 'px',
				},
				ltxe_tooltips_disable: '',
			};
		}

		return false;
	};

	window.ee.fixSwipers = function ( $container ) {
		var $swipers = $container.find( '.swiper-container-initialized, .swiper-initialized' );

		$swipers.each( function () {
			var swiper = jQuery( this ).data( 'swiper' );

			if ( swiper ) {
				swiper.params.observer = true;
				swiper.params.observeParents = true;
				swiper.update();
			}
		} );
	};

	jQuery( document ).on( 'click', '.ee-button-wrapper--no-link', function ( event ) {
		event.preventDefault();
	} );
}( jQuery, window ) );
