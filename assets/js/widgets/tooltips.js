( function ( $ ) {
	'use strict';

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

	function initTooltips( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		var globalSettings = window.ee.getGlobalSettings( 'extras' );
		var $hotspots = $scope.find( '.hotip' );
		var scopeId = $scope.data( 'id' );
		var skin = window.ee.getElementSkin( $scope );
		var tooltips = new eeTooltips( $scope );

		skin = ( '' !== skin && 'default' !== skin ) ? skin + '_' : '';

		var hotipsArgs = {
			id: scopeId,
			fixed: 'fixed' === settings[ skin + 'css_position' ],
			position: settings[ skin + 'position' ] || globalSettings.ltxe_tooltips_position,
			arrowPositionH: settings[ skin + 'arrow_position_h' ] || globalSettings.ltxe_tooltips_arrow_position_h,
			arrowPositionV: settings[ skin + 'arrow_position_v' ] || globalSettings.ltxe_tooltips_arrow_position_v,
			trigger: {
				desktop: settings[ skin + 'trigger' ],
				tablet: settings[ skin + 'trigger_tablet' ],
				mobile: settings[ skin + 'trigger_mobile' ],
			},
			hide: {
				desktop: settings[ skin + '_hide' ],
				tablet: settings[ skin + '_hide_tablet' ],
				mobile: settings[ skin + '_hide_mobile' ],
			},
			responsive: {
				disable: settings[ skin + 'disable' ] || globalSettings.ltxe_tooltips_disable,
				breakpoints: ( function () {
					var bp = ( elementorFrontend.config && elementorFrontend.config.breakpoints ) || {};
					var rbp = ( elementorFrontend.config && elementorFrontend.config.responsive && elementorFrontend.config.responsive.breakpoints ) || {};
					return {
						mobile: bp.xs || ( rbp.mobile && rbp.mobile.value ) || 767,
						tablet: bp.md || ( rbp.tablet && rbp.tablet.value ) || 1024,
						desktop: bp.lg || ( rbp.laptop && rbp.laptop.value ) || 1440,
					};
				}() ),
			},
		};

		tooltips.remove( $scope );

		if ( $hotspots.length ) {
			var delayInSetting = settings[ skin + 'delay_in' ];
			if ( delayInSetting && '' !== delayInSetting.size ) {
				hotipsArgs.delayIn = delayInSetting.size;
			} else if ( globalSettings && globalSettings.ltxe_tooltips_delay_in && globalSettings.ltxe_tooltips_delay_in.size ) {
				hotipsArgs.delayIn = globalSettings.ltxe_tooltips_delay_in.size;
			}

			var delayOutSetting = settings[ skin + 'delay_out' ];
			if ( delayOutSetting && '' !== delayOutSetting.size ) {
				hotipsArgs.delayOut = delayOutSetting.size;
			} else if ( globalSettings && globalSettings.ltxe_tooltips_delay_out && globalSettings.ltxe_tooltips_delay_out.size ) {
				hotipsArgs.delayOut = globalSettings.ltxe_tooltips_delay_out.size;
			}

			var durationSetting = settings[ skin + 'duration' ];
			if ( durationSetting && '' !== durationSetting.size ) {
				hotipsArgs.speed = durationSetting.size;
			} else if ( globalSettings && globalSettings.ltxe_tooltips_duration && globalSettings.ltxe_tooltips_duration.size ) {
				hotipsArgs.speed = globalSettings.ltxe_tooltips_duration.size;
			}

			if ( elementorFrontend.isEditMode() ) {
				hotipsArgs.scope = window.ee.getWindow();
				$hotspots.attr( 'data-hotips-class', 'ee-global ee-tooltip ee-tooltip-' + scopeId );
			}

			$hotspots.hotips( hotipsArgs );
		}

		window.ee.onElementRemove( $scope, function () {
			tooltips.remove( $scope );
		} );
	}

	window.ee.initTooltips = initTooltips;

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/hotspots.default', initTooltips );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/button-group.default', initTooltips );
	} );
}( jQuery ) );
