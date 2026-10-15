( function ( $ ) {
	'use strict';

	var ee = {
		Sticky: {},
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

	window.eeSticky = window.eeSticky || function( $scope, settings ) {
		var self            = this,
			$stickyParent 	= null,
			$stickyElement 	= $scope,
			$column 		= $scope.closest('.elementor-column'),
			$section 		= $scope.closest('.elementor-section'),
			$selector 		= null,
			$window 		= ee.getWindow(),
			$body 			= elementorFrontend.isEditMode() ? window.elementor.$previewContents.find('body') : $('body'),

			customParent 	= false,
			timeout 		= null,
			instance 		= null,
			breakpoint 		= 'tablet' === settings.sticky_unstick_on ? 1023 : 767,
			
			stickyArgs 		= {
				top 			: ( ee.isAdminBar() ) ? 32 : 0,
				stickyClass 	: 'ee-sticky--stuck',
				followScroll	: 'yes' === settings.sticky_follow_scroll,
				bottomEnd		: 0,
				responsive		: {},
			};

		self.isEnabled = function() {
			return 'yes' === settings.sticky_enable;
		};

		self.getStickyContainer = function() {

			var $container = $scope.parent();

			if ( '' === settings.sticky_parent ) { // Column
				$container = ( 'widget' === ee.getElementType( $scope ) ) ? $column : $container;
			} else if ( 'section' === settings.sticky_parent ) { // Section
				$container = ( 'widget' === ee.getElementType( $scope ) ) ? $section : $container;
			} else if ( 'body' === settings.sticky_parent ) { // Body
				$container = $('body');
			} else if ( 'custom' === settings.sticky_parent && '' !== settings.sticky_parent_selector ) { // Custom
				if ( $scope.closest( settings.sticky_parent_selector ).length ) {
					$container = $scope.closest( settings.sticky_parent_selector );
				}
			}
			return $container;
		};

		self.setStickyParent = function() {
			$stickyParent = $scope.parent();
			$stickyParent.addClass( 'ee-sticky-parent' );
			stickyArgs.stickTo = $stickyParent.get(0);
		};

		self.getBottomEndValue = function( $element ) {
			return ( ( $element.offset().top + $element.outerHeight() ) - ( $stickyParent.offset().top + $stickyParent.outerHeight() ) ) * -1;
		};

		self.getBottomEnd = function() {
			var bottomEnd = 0;

			bottomEnd += self.getBottomEndValue( self.getStickyContainer() );

			if ( settings.sticky_offset_bottom ) {
				bottomEnd += settings.sticky_offset_bottom.size;
			}

			return bottomEnd;
		};

		self.setBottomEnd = function() {
			stickyArgs.bottomEnd = self.getBottomEnd();
		};

		self.setOffset = function() {
			stickyArgs.top = ee.isAdminBar() ? 32 : 0;

			if ( settings.sticky_offset ) {
				if ( '%' === settings.sticky_offset.unit ) {
					stickyArgs.top += $(window).height() * ( settings.sticky_offset.size / 100 );
				} else {
					stickyArgs.top += settings.sticky_offset.size;
				}
			}
		};

		self.events = function() {

			// Container resize
			self.getStickyContainer()._resize( self.onResize );

			// Window resize
			$(window).on( 'resize', self.onResize );
			
			ee.onElementRemove( $scope, function() {
				$stickyElement.hcSticky( 'detach' );
			});
		};

		self.onResize = function() {
			self.setOffset();
			self.setBottomEnd();
			self.update();
		};

		self.update = function() {
			$stickyElement.hcSticky( 'update', stickyArgs );
		};

		self.init = function() {

			if ( $stickyElement.data( 'hcSticky' ) )
				$stickyElement.hcSticky( 'destroy' );

			// Exit if sticky not enabled
			if ( ! self.isEnabled() || ! $stickyElement.length )
				return;

			// Set sticky parent element
			self.setStickyParent();

			if ( ! $stickyParent.length )
				return;

			self.setBottomEnd();

			stickyArgs.onStart = function() {
				$stickyParent.addClass( 'ee-sticky-parent--stuck' );
			};

			stickyArgs.onStop = function() {
				$stickyParent.removeClass( 'ee-sticky-parent--stuck' );
			};

			stickyArgs.onResize = self.onResize();

			// Set offset option
			if ( settings.sticky_offset ) {
				if ( '%' === settings.sticky_offset.unit ) {
					stickyArgs.top += $(window).height() * ( settings.sticky_offset.size / 100 );
				} else {
					stickyArgs.top += settings.sticky_offset.size;
				}
			}

			// Set responsive options
			if ( 'none' !== settings.sticky_unstick_on ) {
				stickyArgs.responsive[ breakpoint ] = {
					disable: true
				};
			}

			$stickyElement
				.addClass( 'ee-sticky' )
				.hcSticky( stickyArgs );

			if ( elementorFrontend.isEditMode() ) {
				self.update(); 
			}

			self.events();
		};
	};

	function initSticky( $scope ) {

			ee.Sticky.elementSettings 	= ee.getElementSettings( $scope );
			ee.Sticky.elementType 		= ee.getElementType( $scope );

			var instance = new eeSticky( $scope, ee.Sticky.elementSettings );
			instance.init();
	}


	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) {
			initSticky( $scope );
		} );
	} );
}( jQuery ) );
