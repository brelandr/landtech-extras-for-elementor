( function( $, window ) {

	"use strict";

	var ee = { 

		isAdminBar : function() {
			return $('body').is('.admin-bar');
		},

		prefersReducedMotion : function() {
			return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		},

		applyReducedMotionClass : function( $scope ) {
			if ( ee.prefersReducedMotion() && $scope && $scope.length ) {
				$scope.addClass( 'ltxe-reduced-motion' );
			}
		},

		makeFocusableControl : function( $el, label ) {
			if ( ! $el || ! $el.length ) {
				return;
			}

			$el.attr( {
				role: 'button',
				tabindex: '0',
				'aria-label': label,
			} );

			$el.off( 'keydown.ltxeA11y' ).on( 'keydown.ltxeA11y', function( e ) {
				if ( 13 === e.which || 32 === e.which ) {
					e.preventDefault();
					$el.trigger( 'click' );
				}
			} );
		},

		setAriaExpanded : function( $trigger, expanded, controlsId ) {
			if ( ! $trigger || ! $trigger.length ) {
				return;
			}

			$trigger.attr( 'aria-expanded', expanded ? 'true' : 'false' );

			if ( controlsId ) {
				$trigger.attr( 'aria-controls', controlsId );
			}
		},

		bindEscapeClose : function( namespace, closeFn ) {
			$( document ).off( 'keydown.' + namespace ).on( 'keydown.' + namespace, function( e ) {
				if ( 27 === e.which ) {
					closeFn( e );
				}
			} );
		},

		unbindEscapeClose : function( namespace ) {
			$( document ).off( 'keydown.' + namespace );
		},

		focusTrap : function( $container, namespace ) {
			if ( ! $container || ! $container.length ) {
				return;
			}

			var focusableSelector = 'a[href], button:not([disabled]), textarea, input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])',
				$focusable = $container.find( focusableSelector ).filter( ':visible' );

			if ( ! $focusable.length ) {
				return;
			}

			var $first = $focusable.first(),
				$last = $focusable.last();

			$first.focus();

			$container.off( 'keydown.' + namespace ).on( 'keydown.' + namespace, function( e ) {
				if ( 9 !== e.which ) {
					return;
				}

				if ( e.shiftKey ) {
					if ( document.activeElement === $first[0] ) {
						e.preventDefault();
						$last.focus();
					}
				} else if ( document.activeElement === $last[0] ) {
					e.preventDefault();
					$first.focus();
				}
			} );
		},

		releaseFocusTrap : function( $container, namespace ) {
			if ( $container && $container.length ) {
				$container.off( 'keydown.' + namespace );
			}
		},

		/**
		 * U30 Popup focus trap — exact FREE-PLUGIN-UPGRADE-PLAN.md snippet.
		 *
		 * @param {Element}  modal      Dialog root.
		 * @param {Function} closePopup Close callback.
		 * @return {void}
		 */
		ltxePopupFocusTrap : function( modal, closePopup ) {
			if ( ! modal || modal.getAttribute( 'data-ltxe-popup-trap' ) ) {
				return;
			}
			modal.setAttribute( 'data-ltxe-popup-trap', '1' );
			modal.setAttribute( 'role', 'dialog' );
			modal.setAttribute( 'aria-modal', 'true' );
			const focusable = modal.querySelectorAll('button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])');
			const first = focusable[0], last = focusable[ focusable.length - 1 ];
			modal.addEventListener('keydown', (e) => {
				if ( e.key === 'Escape' ) { closePopup(); return; }
				if ( e.key !== 'Tab' ) return;
				if ( e.shiftKey && document.activeElement === first ) { e.preventDefault(); last.focus(); }
				else if ( ! e.shiftKey && document.activeElement === last ) { e.preventDefault(); first.focus(); }
			});
			if ( first && first.focus ) {
				first.focus();
			}
		},

		init : function( replayExisting ) {

			if ( ee._ltxeHooksRegistered ) {
				if ( replayExisting ) {
					ee.replayReadyWidgets();
				}
				return;
			}
			ee._ltxeHooksRegistered = true;

			var widgets = {};

			ee._ltxeWidgetHandlers = {};

			$.each( widgets, function( widget, callback ) {
				if ( 'object' === typeof callback && ! $.isFunction( callback ) ) {
					var wrappedList = [];
					$.each( callback, function( index, cb ) {
						wrappedList.push( ee.oncePerScope( widget + '_' + index, cb ) );
					} );
					ee._ltxeWidgetHandlers[ widget ] = wrappedList;
				} else {
					ee._ltxeWidgetHandlers[ widget ] = ee.oncePerScope( widget, callback );
				}
			} );

			$.each( ee._ltxeWidgetHandlers, function( widget, callback ) {
				if ( 'object' === typeof callback && ! $.isFunction( callback ) ) {
					$.each( callback, function( index, cb ) {
						elementorFrontend.hooks.addAction( 'frontend/element_ready/' + widget, cb );
					});
				} else {
					elementorFrontend.hooks.addAction( 'frontend/element_ready/' + widget, callback );
				}
			});

			$( document ).on( 'click', '.ee-button-wrapper--no-link', function( event ) {
				event.preventDefault();
			} );

			if ( replayExisting ) {
				ee.replayReadyWidgets();
			}
		},

		oncePerScope : function( key, callback ) {
			return function( $scope, $ ) {
				var flag = 'ltxeOnce_' + key;
				if ( ! $scope || ! $scope.length || $scope.data( flag ) ) {
					return;
				}
				$scope.data( flag, true );
				if ( $.isFunction( callback ) ) {
					return callback( $scope, $ );
				}
			};
		},

		replayReadyWidgets : function() {
			if ( ! ee._ltxeWidgetHandlers ) {
				return;
			}

			$.each( ee._ltxeWidgetHandlers, function( widget, callback ) {
				$( '.elementor-element[data-widget_type="' + widget + '"]' ).each( function() {
					var $scope = $( this );
					if ( 'object' === typeof callback && ! $.isFunction( callback ) ) {
						$.each( callback, function( index, cb ) {
							if ( $.isFunction( cb ) ) {
								cb( $scope, $ );
							}
						} );
					} else if ( $.isFunction( callback ) ) {
						callback( $scope, $ );
					}
				} );
			} );
		},

		getRefreshableWidgets : function() {
			if ( ! landtechExtrasFrontendConfig )
				return false;
			
			return landtechExtrasFrontendConfig.refreshableWidgets;
		},

		getGlobalSettings : function( section ) {

			if ( section in elementorFrontendConfig.settings ) {

				return elementorFrontendConfig.settings[section];

			} else if ( 'extras' === section ) { // Quick fix until transition to kit settings
				
				return {
					ltxe_tooltips_position : 'top',
					ltxe_tooltips_arrow_position_h : '',
					ltxe_tooltips_arrow_position_v : '',
					ltxe_tooltips_delay_in : {
						'size' : 0,
						'unit' : 'px',
					},
					ltxe_tooltips_delay_out : {
						'size' : 0,
						'unit' : 'px',
					},
					ltxe_tooltips_duration : {
						'size' : 0.2,
						'unit' : 'px',
					},
					ltxe_tooltips_disable : '',
				};
			}

			return false;
		},

		getItems : function ( items, itemKey ) {
			if ( itemKey) {
				var keyStack = itemKey.split('.'),
					currentKey = keyStack.splice(0, 1);

				if ( ! keyStack.length ) {
					return items[ currentKey ];
				}

				if ( ! items[ currentKey ] ) {
					return;
				}

				return this.getItems( items[ currentKey ], keyStack.join('.'));
			}

			return items;
		},

		getElementSettings : function( $element, setting ) {

			var elementSettings = {},
				modelCID 		= $element.data( 'model-cid' );

			if ( elementorFrontend.isEditMode() && modelCID ) {
				var settings 		= elementorFrontend.config.elements.data[ modelCID ],
					type 			= settings.attributes.widgetType || settings.attributes.elType,
					settingsKeys 	= elementorFrontend.config.elements.keys[ type ];

				if ( ! settingsKeys ) {
					settingsKeys = elementorFrontend.config.elements.keys[type] = [];

					jQuery.each( settings.controls, function ( name, control ) {
						if ( control.frontend_available ) {
							settingsKeys.push( name );
						}
					});
				}

				jQuery.each( settings.getActiveControls(), function( controlKey ) {
					if ( -1 !== settingsKeys.indexOf( controlKey ) ) {
						elementSettings[ controlKey ] = settings.attributes[ controlKey ];
					}
				} );
			} else {
				elementSettings = $element.data('settings') || {};
			}

			return ee.getItems( elementSettings, setting );
		},

		/**
		 * GLightbox inline slides need hide classes removed; !important beats inline display.
		 *
		 * @param {jQuery} $element Popup content wrapper.
		 * @return {HTMLElement|null}
		 */
		prepareGlightboxInlineNode : function( $element ) {
			var node = $element && $element.length ? $element.get( 0 ) : null;

			if ( node ) {
				node.classList.remove( 'mfp-hide', 'glightbox-hide' );
				node.style.removeProperty( 'display' );
			}

			return node;
		},

		/**
		 * Magnific Popup entrance CSS expects .mfp-ready on the container; GLightbox does not add it.
		 *
		 * @param {string|number} scopeId Elementor widget scope ID.
		 * @return {void}
		 */
		markGlightboxPopupReady : function( scopeId ) {
			$( '.glightbox-container.ee-mfp-popup-' + scopeId ).addClass( 'mfp-ready' );
		},

		/**
		 * @param {string|number} scopeId Elementor widget scope ID.
		 * @return {void}
		 */
		unmarkGlightboxPopupReady : function( scopeId ) {
			$( '.glightbox-container.ee-mfp-popup-' + scopeId ).removeClass( 'mfp-ready mfp-removing' );
		},

		getElementType : function ( $element ) {
			if ( 'section' === $element.data( 'element_type' ) || 'column' === $element.data( 'element_type' ) ) {
				return $element.data( 'element_type' );
			}

			return 'widget';
		},

		/**
		 * Direct child .elementor-widget-container is omitted when Elementor Optimized Markup is active.
		 */
		getWidgetContentRoot : function ( $widgetScope ) {
			var $inner = $widgetScope.children( '.elementor-widget-container' );
			return $inner.length ? $inner : $widgetScope;
		},

		getElementSkin : function ( $element ) {
			return $element.attr('data-widget_type').split('.')[1];
		},

		getUniqueLoopScopeId : function( $scope ) {
			if ( $scope.data('ee-template-widget-id') ) {
				return $scope.data('ee-template-widget-id');
			}

			return $scope.data('id');
		},

		/**
		 * Per-instance store (Loop Builder). Never keep Isotope/Swiper on a shared function property.
		 */
		ltxeInstanceStore : function( kind ) {
			if ( ! ee._ltxeInstances ) {
				ee._ltxeInstances = {};
			}
			if ( ! ee._ltxeInstances[ kind ] ) {
				ee._ltxeInstances[ kind ] = ( typeof Map === 'function' ) ? new Map() : {};
			}
			return ee._ltxeInstances[ kind ];
		},

		ltxeSetInstance : function( kind, id, instance ) {
			var store = ee.ltxeInstanceStore( kind );
			var key = String( id );
			if ( store.set ) {
				store.set( key, instance );
			} else {
				store[ key ] = instance;
			}
		},

		ltxeGetInstance : function( kind, id ) {
			var store = ee.ltxeInstanceStore( kind );
			var key = String( id );
			if ( store.get ) {
				return store.get( key );
			}
			return store[ key ];
		},

		getOffcanvasUniqueScopeId : function( $scope ) {
			var scopeId = $scope.data('id'),
				$clones = $( '[data-id="' + scopeId + '"]' );

			if ( ! ee.hasMultipleScopeId( scopeId ) ) {
				return scopeId;
			}
			
			$clones.each( function( index ) {
				$(this).attr( 'data-offcanvas-index', index );
			});

			scopeId = scopeId + '_' + $scope.data('offcanvas-index');

			return scopeId;
		},

		refreshElements : function( $container, refreshable ) {
			var selectors = [
					'.elementor-widget',
					'.elementor-section',
					'.elementor-column',
				],
				excludes = [
					'.elementor-widget-form', // Offcanvas fails when initializing recaptcha on forms
				];

			$container.each( function() {
				$(this).find( selectors.join(',') ).each( function() {
					var $element = $(this),
						canRefresh = true;

					excludes.forEach( function( selector, index ) {
						if ( $element.is( selector ) ) {
							canRefresh = false;
						}
					});

					if ( canRefresh ) {
						ee.refreshElement( $(this), refreshable );
					}
				});
			});
		},

		refreshElement : function( $element, refreshable ) {

			var elementType = $element.data('element_type');

			if ( refreshable ) { // Refresh only if it's a refreshable widget
				if ( 'widget' === elementType ) {
					var widgetType = $element.data('widget_type');

					if ( $.inArray( widgetType, ee.getRefreshableWidgets() ) ) {
						elementorFrontend.elementsHandler.runReadyTrigger( $element );
					}
				} else {
					elementorFrontend.elementsHandler.runReadyTrigger( $element );
				}
			} else {
				elementorFrontend.elementsHandler.runReadyTrigger( $element );
			}
		},

		fixSwipers : function( $container ) {
			var $swipers = $container.find( '.swiper-container-initialized, .swiper-initialized' );

			$swipers.each( function() {
				var swiper = $(this).data('swiper');

				if ( swiper ) {
					swiper.params.observer = true;
					swiper.params.observeParents = true;
					swiper.update();
				}
			});
		},

		hasMultipleScopeId : function( scopeId ) {
			var $clones = $( '[data-id="' + scopeId + '"]' );

			if ( 1 === $clones.length ) {
				return false;
			}
			
			return true;
		},

		getWindow : function() {
			return elementorFrontend.isEditMode() ? window.elementor.$previewContents : $(window);
		},

		onElementRemove : function( $element, callback ) {
			if ( elementorFrontend.isEditMode() ) {
				// Make sure sticky is destroyed when element is removed in editor mode
				elementor.channels.data.on( 'element:before:remove', function ( model ) {
					if ( $element.data('id') === model.id ) {
						callback();
					}
				});
			}
		},

	};

	var LandTechExtrasUtils = {

		timer : null,

		isJson : function( item ) {
			item = typeof item !== "string"
				? JSON.stringify( item )
				: item;

			try {
				item = JSON.parse( item );
			} catch (e) {
				return false;
			}

			if ( typeof item === "object" && item !== null) {
				return true;
			}

			return false;
		},

		countDecimals : function ( value ) {
			if( Math.floor( value ) === value ) return 0;
			return value.toString().split(".")[1].length || 0;
		},

		parseValue : function ( value, _default ) {
			var _value = value;

			if ( 'string' === typeof _value ) {
				_value = _value.replace(/\s/g, '');
				_value = _value.replace( ',', '.' );

				if ( _value.indexOf('/') > -1 ) {
					var _div_value = _value.split('/');

					if ( ! isNaN( _div_value[0] ) && ! isNaN( _div_value[1] ) ) {
						_div_value = parseInt(_div_value[0]) / _div_value[1];
						_value = _div_value * 100;
						_value = _value.toFixed( 0 );
					}
				}
			}

			if ( ! isNaN( _value ) ) {
				_value = Math.abs( parseFloat( _value ) );
			} else {
				_value = _default;
			}
			return _value;
		},

		findObjectByKey : function( array, key, value ) {
			for ( var i = 0; i < array.length; i++ ) {
				if ( array[ i ][ key ] === value ) {
					return array[ i ];
				}
			}
			return null;
		},

		moveDecimal : function( n, x ) {
			var v = n / Math.pow( 10, x );
				v = ( v > 1 ) ? Math.round( v ) : Math.round( v * Math.pow ( 10, x + 1 ) ) / Math.pow ( 10, x + 1 );
			return v;
		},

		trackLeave : function (ev) {
			if ( ev.clientY > 0 ) {
				return;
			}

			if ( LandTechExtrasUtils.timer ) {
				clearTimeout( LandTechExtrasUtils.timer );
			}

			if ( $.exitIntent.settings.sensitivity <= 0 ) {
				$.event.trigger('exitintent');
				return;
			}

			LandTechExtrasUtils.timer = setTimeout( function() {
				LandTechExtrasUtils.timer = null;
				$.event.trigger( 'exitintent' );
			}, $.exitIntent.settings.sensitivity );
		},

		serializeObject : function( data ) {
			var o = {};
			var a = data.serializeArray();

			$.each(a, function() {
				if (!this.value) return;
				if (o[this.name]) {
					if (!o[this.name].push) {
						o[this.name] = [o[this.name]];
					}
					o[this.name].push(this.value || '');
				} else {
					o[this.name] = this.value || '';
				}
			});
			return o;
		},

		trackEnter : function() {
			if ( LandTechExtrasUtils.timer ) {
				clearTimeout( LandTechExtrasUtils.timer );
				LandTechExtrasUtils.timer = null;
			}
		},

		/*
		 * Parse an ajax response string and and build a valid structure where
		 * we can parse the response using jquery
		 *
		 * @source https://stackoverflow.com/questions/7001926/cannot-get-body-element-from-ajax-response
		 */
		parseAjaxResponse : function( data, container ) {
			data = data.replace(/\<\!\[CDATA\[\/\/\>\<\!\-\-/gi,'');
			data = data.replace(/\/\/\-\-\>\<\!\]\]\>/gi,'');

			// extracting the the head and body tags
			var head  = data.match(/<\s*head.*>[\s\S]*<\s*\/head\s*>/ig).join("");
			var body  = data.match(/<\s*body.*>[\s\S]*<\s*\/body\s*>/ig).join("");

			head  = head.replace(/<\s*head/gi,"<div");
			head  = head.replace(/<\s*\/head/gi,"</div");

			body  = body.replace(/<\s*body/gi,"<div");
			body  = body.replace(/<\s*\/body/gi,"</div");

			// comments
			// var commentPattern = /\<\!\-\-([\s\S]*?)\-\-\>/ig;

			// get head comment tags
			// var headComments = dataHead.match( commentPattern );

			// get body comment tags
			// var bodyComments = dataBody.match( commentPattern );

			var $response = $( head );

			if ( container && $( body ).find( container ).length ) {
				$response = $( head ).add( $( body ).find( container ) );
			} else {
				$response = $( head ).add( $( body ) );
			}

			return $response;
		},
	};

	window.LandTechExtrasOffcanvas = function() {
		var self = this;

		self.initialized 	= false;
		self.controller 	= null;

		self.init = function() {
			self.controller = new slidebars();
			self.controller.init();
			self.initialized = true;
		};
	};

	var offcanvas = new LandTechExtrasOffcanvas();

	ee.attachOffcanvas = function() {
		if ( ! window.elementorFrontend ) {
			return;
		}

		if ( window.ltxeOffcanvas ) {
			window.elementorFrontend.eeOffcanvas = window.ltxeOffcanvas;
			return;
		}

		if ( ! window.elementorFrontend.eeOffcanvas ) {
			window.elementorFrontend.eeOffcanvas = offcanvas;
		}
	};

	ee.attachOffcanvas();

	$.exitIntent = function( enable, options ) {
		$.exitIntent.settings = $.extend($.exitIntent.settings, options);

		if ( enable == 'enable' ) {
			$(window).mouseleave( LandTechExtrasUtils.trackLeave );
			$(window).mouseenter( LandTechExtrasUtils.trackEnter );
		} else if ( enable == 'disable' ) {
			trackEnter(); // Turn off any outstanding timer
			$(window).unbind( 'mouseleave', LandTechExtrasUtils.trackLeave );
			$(window).unbind( 'mouseenter', LandTechExtrasUtils.trackEnter );
		} else {
			throw "Invalid parameter to jQuery.exitIntent -- should be 'enable'/'disable'";
		}
	}

	$.exitIntent.settings = {
		'sensitivity': 300
	};

	var ltxeHeardFrontendInit = false;

	$( window ).on( 'elementor/frontend/init', function() {
		ltxeHeardFrontendInit = true;
		ee.attachOffcanvas();
		ee.init( false );
		window.setTimeout( function() {
			ee.replayReadyWidgets();
		}, 0 );
	} );

	$( function() {
		window.setTimeout( function() {
			if ( window.elementorFrontend && elementorFrontend.hooks ) {
				ee.attachOffcanvas();
				ee.init( true );
			}
		}, 0 );
	} );

	$( window ).on( 'load', function() {
		window.setTimeout( function() {
			if ( window.elementorFrontend && elementorFrontend.hooks ) {
				ee.attachOffcanvas();
				ee.init( true );
			}
		}, 0 );
	} );

}( jQuery, window ) );