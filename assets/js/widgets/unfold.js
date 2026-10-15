( function ( $ ) {
	'use strict';

	function assignSize( target, key, control ) {
		if ( control && 'undefined' !== typeof control.size ) {
			target[ key ] = control.size;
		}
	}

	function initUnfold( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		var $unfold = $scope.find( '.ee-unfold' );
		var $unfoldText = $unfold.find( '.ee-button-text' );
		var unfoldArgs = {};
		var instance = $unfold.data( 'unfold' );

		if ( instance ) {
			instance.destroy();
		}

		if ( settings.animation_unfold ) {
			unfoldArgs.animation_unfold = settings.animation_unfold;
		}
		if ( settings.animation_fold ) {
			unfoldArgs.animation_fold = settings.animation_fold;
		}
		if ( settings.easing_unfold ) {
			unfoldArgs.easing_unfold = settings.easing_unfold;
		}
		if ( settings.easing_fold ) {
			unfoldArgs.easing_fold = settings.easing_fold;
		}

		assignSize( unfoldArgs, 'steps_unfold', settings.steps_unfold );
		assignSize( unfoldArgs, 'steps_fold', settings.steps_fold );
		assignSize( unfoldArgs, 'slow_unfold', settings.slow_unfold );
		assignSize( unfoldArgs, 'slow_fold', settings.slow_fold );
		assignSize( unfoldArgs, 'duration_unfold', settings.duration_unfold );
		assignSize( unfoldArgs, 'duration_fold', settings.duration_fold );

		if ( 'yes' === settings.focus_close ) {
			unfoldArgs.focusOnClose = true;
		}
		if ( 'top' === settings.focus_open || 'scroll' === settings.focus_open ) {
			unfoldArgs.focusOnOpen = settings.focus_open;
		}
		if ( 'lines' === settings.visible_type ) {
			assignSize( unfoldArgs, 'visible_lines', settings.visible_lines );
		}
		if ( settings.visible_percentage ) {
			assignSize( unfoldArgs, 'visible_percentage', settings.visible_percentage );
		}
		if ( '' !== $unfoldText.data( 'open-label' ) ) {
			unfoldArgs.text_closed = $unfoldText.data( 'open-label' );
		}
		if ( '' !== $unfoldText.data( 'close-label' ) ) {
			unfoldArgs.text_open = $unfoldText.data( 'close-label' );
		}

		$unfold.unfold( unfoldArgs );

		var $triggerBtn = $unfold.find( '.ee-unfold__trigger .ee-button' );
		var contentId = $unfold.find( '.ee-unfold__content' ).attr( 'id' );
		window.ee.makeFocusableControl( $triggerBtn, $unfoldText.text() || 'Toggle content' );
		window.ee.setAriaExpanded( $triggerBtn, false, contentId );
		$triggerBtn.off( 'click.ltxeUnfoldA11y' ).on( 'click.ltxeUnfoldA11y', function () {
			var expanded = 'true' === $triggerBtn.attr( 'aria-expanded' );
			window.ee.setAriaExpanded( $triggerBtn, ! expanded, contentId );
		} );

		window.ee.onElementRemove( $scope, function () {
			var live = $unfold.data( 'unfold' );
			if ( live ) {
				live.destroy();
			}
		} );
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/unfold.default', initUnfold );
	} );
}( jQuery ) );
