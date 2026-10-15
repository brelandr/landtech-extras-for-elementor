( function ( $ ) {
	'use strict';

	function initHeading( $scope ) {
		var elementSettings = window.ee.getElementSettings( $scope );
		var $heading = $scope.find( '.ee-heading' );
		var $longShadow = $heading.find( '.ee-heading__long-shadow' );
		var $text = $heading.find( '.ee-heading__text' ).first();
		var longShadowArgs = {};

		if ( 'yes' === elementSettings.title_long_shadow_enable ) {
			if ( elementSettings.title_long_shadow_color ) {
				longShadowArgs.colorShadow = elementSettings.title_long_shadow_color;
			}

			if ( elementSettings.title_long_shadow_size ) {
				longShadowArgs.sizeShadow = elementSettings.title_long_shadow_size.size;
			}

			if ( elementSettings.title_long_shadow_direction ) {
				longShadowArgs.directionShadow = elementSettings.title_long_shadow_direction;
			}

			$longShadow.longShadow( longShadowArgs );
		}

		if ( ! $text.length || ! elementSettings.title_animation_preset || 'none' === elementSettings.title_animation_preset ) {
			return;
		}

		if ( 'undefined' === typeof window.TweenMax ) {
			return;
		}

		var preset = elementSettings.title_animation_preset;

		if ( 'fade_up' === preset ) {
			TweenMax.from( $text, 0.8, { y: 24, opacity: 0, ease: Power4.easeOut } );
		} else if ( 'typewriter' === preset ) {
			var fullText = $text.text();
			$text.text( '' );
			var charIndex = 0;
			var typeTimer = window.setInterval( function () {
				charIndex++;
				$text.text( fullText.substring( 0, charIndex ) );
				if ( charIndex >= fullText.length ) {
					window.clearInterval( typeTimer );
				}
			}, 35 );
		} else if ( 'split_words' === preset ) {
			var words = $.trim( $text.text() ).split( /\s+/ );
			$text.empty();
			$.each( words, function ( _idx, word ) {
				$text.append( $( '<span class="ee-heading__word"></span>' ).css( 'display', 'inline-block' ).text( word + ' ' ) );
			} );
			TweenMax.staggerFrom( $text.find( '.ee-heading__word' ), 0.5, { y: 16, opacity: 0 }, 0.08 );
		} else if ( 'highlight_sweep' === preset ) {
			$text.addClass( 'ee-heading__text--highlight-sweep' );
			TweenMax.fromTo( $text, 1, { backgroundSize: '0% 100%' }, { backgroundSize: '100% 100%' } );
		}
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/heading-extended.default', initHeading );
	} );
}( jQuery ) );
