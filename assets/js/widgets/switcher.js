( function ( $ ) {
	'use strict';

	function sliderSize( control ) {
		if ( ! control || 'object' !== typeof control ) {
			return '';
		}
		return control.size;
	}

	function initSwitcher( $scope ) {
		var settings = window.ee.getElementSettings( $scope ) || {};
		var $media = $scope.find( '.ee-switcher__media-wrapper' );
		var $content = $scope.find( '.ee-switcher__titles' );
		var switcherArgs = {
			mediaEffect: settings.effect_media || 'swipeLeft',
			contentEffect: settings.effect_title || 'slideLeft',
			entranceAnimation: 'yes' === settings.effect_entrance,
			contentEffectZoom: 'yes' === settings.effect_media_zoom,
			contentStagger: 'yes' === settings.effect_title_stagger,
			autoplay: 'yes' === settings.autoplay,
			loop: 'yes' === settings.loop,
			cancelOnInteraction: 'yes' === settings.autoplay_cancel,
			changeBackground: 'yes' === settings.background_switcher,
		};
		var mediaParallaxArgs = {
			type: 'mouse',
			mouse: {
				relative: 'viewport',
				axis: settings.parallax_pan_axis,
			},
			speed: {
				desktop: 0.20,
			},
		};
		var titleParallaxArgs = {
			type: 'mouse',
			invert: true,
			mouse: {
				relative: 'viewport',
				axis: settings.parallax_pan_axis,
			},
			speed: {
				desktop: 0.20,
			},
		};

		function maybeDestroy() {
			if ( $scope.data( 'eeSwitcher' ) ) {
				$scope.data( 'eeSwitcher' ).destroy();
			}
			if ( $media.data( 'parallaxElement' ) ) {
				$media.data( 'parallaxElement' ).destroy();
			}
			if ( $content.data( 'parallaxElement' ) ) {
				$content.data( 'parallaxElement' ).destroy();
			}
		}

		maybeDestroy();

		if ( elementorFrontend.isEditMode() ) {
			switcherArgs.scope = window.elementor.$previewContents;
			mediaParallaxArgs.scope = window.elementor.$previewContents;

			if ( 'yes' === settings.autoplay && 'yes' !== settings.autoplay_preview ) {
				switcherArgs.autoplay = false;
			}

			if ( 'yes' === settings.effect_entrance && 'yes' !== settings.effect_entrance_preview ) {
				switcherArgs.entranceAnimation = false;
			}
		}

		if ( 'yes' === settings.autoplay ) {
			var durationSize = sliderSize( settings.duration );
			if ( durationSize ) {
				switcherArgs.duration = durationSize;
			}
		}

		var speedSize = sliderSize( settings.speed );
		if ( speedSize ) {
			switcherArgs.speed = speedSize;
		}

		if ( 'yes' === settings.parallax_enable ) {
			if ( 'undefined' !== typeof settings.parallax_amount && '' !== settings.parallax_amount.size ) {
				mediaParallaxArgs.speed.desktop = settings.parallax_amount.size;
				titleParallaxArgs.speed.desktop = settings.parallax_amount.size;
			}

			$media.parallaxElement( mediaParallaxArgs );
			$content.parallaxElement( titleParallaxArgs );
		}

		switch ( settings.background_switcher_element ) {
			case 'widget':
				switcherArgs.background = window.ee.getWidgetContentRoot( $scope );
				break;
			case 'section':
				switcherArgs.background = $scope.parents( '.elementor-section' ).first();
				if ( ! switcherArgs.background.length ) {
					switcherArgs.background = $scope.parents( '.e-con, .elementor-element[data-element_type="container"]' ).first();
				}
				break;
			default:
				switcherArgs.background = elementorFrontend.isEditMode() ? switcherArgs.scope.find( 'body' ) : $( 'body' );
		}

		try {
			if ( $.isFunction( $scope.eeSwitcher ) ) {
				$scope.eeSwitcher( switcherArgs );
			}
		} catch ( ltxeSwitcherError ) {
			$scope.find( '.ee-switcher' ).addClass( 'is--loaded' );
		}

		if ( ! $scope.find( '.ee-switcher.is--loaded' ).length ) {
			$scope.find( '.ee-switcher' ).addClass( 'is--loaded' );
		}

		$scope.find( '.ee-switcher__nav__item' ).each( function () {
			var $item = $( this );
			window.ee.makeFocusableControl( $item, $.trim( $item.text() ) || 'Slide' );
		} );
		$scope.off( 'click.ltxeSwitcherA11y' ).on( 'click.ltxeSwitcherA11y', '.ee-switcher__nav__item', function () {
			window.setTimeout( function () {
				$scope.find( '.ee-switcher__nav__item' ).each( function () {
					$( this ).attr( 'aria-pressed', $( this ).hasClass( 'is--active' ) ? 'true' : 'false' );
				} );
			}, 0 );
		} );

		var switcherInstance = $scope.data( 'eeSwitcher' );
		if ( switcherInstance && switcherInstance.maybeAppear ) {
			switcherInstance.maybeAppear();
			window.setTimeout( function () {
				if ( switcherInstance.maybeAppear ) {
					switcherInstance.maybeAppear();
				}
			}, 150 );
		}

		window.ee.onElementRemove( $scope, maybeDestroy );
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-switcher.classic', initSwitcher );
	} );
}( jQuery ) );
