( function ( $ ) {
	'use strict';

	var ee = {
		GlobalTooltip: {},
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

	window.eeTooltips = window.eeTooltips || function () {
		var self = this;

		self.remove = function ( $scope ) {
			var $hotips;

			if ( $scope && $scope.length ) {
				var scopeId = $scope.data( 'id' );
				$hotips = $( '.hotip-tooltip[data-target-id="' + scopeId + '"]' );
			} else {
				$hotips = $( '.hotip-tooltip' );
			}

			if ( $hotips.length ) {
				$hotips.remove();
			}
		};
	};

	function initGlobalTooltip( $scope ) {

			ee.GlobalTooltip.elementSettings 	= ee.getElementSettings( $scope );
			ee.GlobalTooltip.globalSettings 	= ee.getGlobalSettings( 'extras' );

			var $target 	= $scope,
				scopeId 	= $scope.data( 'id' ),
				instance 	= $target.data( 'hotips' ),
				tooltips 	= new eeTooltips( $scope ),
				hotipsArgs 	= {
					fixed 			: 'fixed' === ee.GlobalTooltip.elementSettings.tooltip_css_position,
					position 		: ee.GlobalTooltip.elementSettings.tooltip_position || ee.GlobalTooltip.globalSettings.ltxe_tooltips_position,
					arrowPositionH 	: ee.GlobalTooltip.elementSettings.tooltip_arrow_position_h || ee.GlobalTooltip.globalSettings.ltxe_tooltips_arrow_positio_h,
					arrowPositionV 	: ee.GlobalTooltip.elementSettings.tooltip_arrow_position_v || ee.GlobalTooltip.globalSettings.ltxe_tooltips_arrow_positio_v,
					responsive 		: {
						disable : ee.GlobalTooltip.elementSettings.tooltip_disable || ee.GlobalTooltip.globalSettings.ltxe_tooltips_disable,
						breakpoints		: {
							'mobile' 	: elementorFrontend.config.breakpoints.xs,
							'tablet'	: elementorFrontend.config.breakpoints.md,
							'desktop' 	: elementorFrontend.config.breakpoints.lg,
						},
					},
					source 		: $scope.find( '#hotip-content-' + scopeId ),
				};

			ee.GlobalTooltip.setTarget = function() {
				if ( 'custom' === ee.GlobalTooltip.elementSettings.tooltip_target ) {
					if ( '' !== ee.GlobalTooltip.elementSettings.tooltip_selector ) {
						var $_target = $scope.find( ee.GlobalTooltip.elementSettings.tooltip_selector );

						if ( $_target.length ) {
							$target = $_target;
							instance = $target.data( 'hotips' );
						}
					}
				}
			};

			ee.GlobalTooltip.maybeDestroy = function() {
				if ( instance )
					instance.destroy();
			};

			ee.GlobalTooltip.init = function() {

				ee.GlobalTooltip.maybeDestroy();

				if ( 'yes' !== ee.GlobalTooltip.elementSettings.tooltip_enable )
					return;

				ee.GlobalTooltip.setTarget();

				if ( ee.GlobalTooltip.elementSettings.tooltip_trigger ) {
					hotipsArgs.trigger = {
						desktop : ee.GlobalTooltip.elementSettings.tooltip_trigger,
						tablet : ee.GlobalTooltip.elementSettings.tooltip_trigger_tablet,
						mobile : ee.GlobalTooltip.elementSettings.tooltip_trigger_mobile,
					};
				}

				if ( ee.GlobalTooltip.elementSettings.tooltip__hide ) {
					hotipsArgs.hide = {
						desktop : ee.GlobalTooltip.elementSettings.tooltip__hide,
						tablet : ee.GlobalTooltip.elementSettings.tooltip__hide_tablet,
						mobile : ee.GlobalTooltip.elementSettings.tooltip__hide_mobile,
					};
				}

				if ( '' !== ee.GlobalTooltip.elementSettings.tooltip_delay_in.size ) {
					hotipsArgs.delayIn = ee.GlobalTooltip.elementSettings.tooltip_delay_in.size;
				} else if ( ee.GlobalTooltip.globalSettings.ltxe_tooltips_delay_in.size ) {
					hotipsArgs.delayIn = ee.GlobalTooltip.globalSettings.ltxe_tooltips_delay_in.size;
				}

				if ( '' !== ee.GlobalTooltip.elementSettings.tooltip_delay_out.size ) {
					hotipsArgs.delayOut = ee.GlobalTooltip.elementSettings.tooltip_delay_out.size;
				} else if ( ee.GlobalTooltip.globalSettings.ltxe_tooltips_delay_out.size ) {
					hotipsArgs.delayOut = ee.GlobalTooltip.globalSettings.ltxe_tooltips_delay_out.size;
				}

				if ( '' !== ee.GlobalTooltip.elementSettings.tooltip_duration.size ) {
					hotipsArgs.speed = ee.GlobalTooltip.elementSettings.tooltip_duration.size;
				} else if ( ee.GlobalTooltip.globalSettings.ltxe_tooltips_duration.size ) {
					hotipsArgs.speed = ee.GlobalTooltip.globalSettings.ltxe_tooltips_duration.size;
				}

				if ( elementorFrontend.isEditMode() ) {
					hotipsArgs.scope = window.elementor.$previewContents;
				}

				$target.attr( 'data-hotips-class', 'ee-global ee-tooltip ee-tooltip-' + scopeId );
				
				$target.hotips( hotipsArgs );

				ee.onElementRemove( $scope, function() {
					ee.GlobalTooltip.maybeDestroy();
				});
			};

			ee.GlobalTooltip.init();
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) {
			initGlobalTooltip( $scope );
		} );
	} );
}( jQuery ) );
