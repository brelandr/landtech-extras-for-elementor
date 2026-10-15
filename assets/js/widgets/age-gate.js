( function ( $ ) {
	'use strict';

	function initAgeGate( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		var scopeId = $scope.data( 'id' );
		var $form = $scope.find( '.ee-form--age-gate' );
		var $header = $scope.find( '.ee-age-gate__header' );
		var $popup = $scope.find( '.ee-popup__content' );
		var $age = $scope.find( '[name=ee-age-gate-age]' );
		var $denied = $scope.find( '.ee-notification--error' );
		var storageID = 'ltxe_AgeGate';
		var isAdmin = 'undefined' !== typeof settings.popup_open_admin && 'yes' === settings.popup_open_admin;
		var popupVAlignClass = 'mfp-popup--valign-' + settings.popup_valign;
		var glightboxInstance = null;

		function buildGlightbox() {
			return GLightbox( {
				elements: [
					{
						content: window.ee.prepareGlightboxInlineNode( $popup ),
						type: 'inline',
					},
				],
				closeButton: false,
				touchNavigation: false,
				loop: false,
				closeOnOutsideClick: elementorFrontend.isEditMode(),
				keyboardNavigation: elementorFrontend.isEditMode(),
				cssClasses: {
					container: 'glightbox-container ee-mfp-popup ee-mfp-popup-' + scopeId + ' ' + popupVAlignClass + ' ' + settings.popup_animation,
				},
				afterOpen: function () {
					window.ee.markGlightboxPopupReady( scopeId );
					var wrapEl = document.querySelector( '.glightbox-container.ee-mfp-popup-' + scopeId ) || document.querySelector( '.glightbox-container' );
					window.ee.ltxePopupFocusTrap( wrapEl, function () {} );
				},
				beforeClose: function () {
					window.ee.unmarkGlightboxPopupReady( scopeId );
				},
			} );
		}

		function openLightbox() {
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

		function onSubmit( e ) {
			e.preventDefault();

			var age = $age.val();

			if ( age >= Math.abs( parseFloat( settings.age ) ) ) {
				closeLightbox();

				if ( ! isAdmin ) {
					localStorage.setItem( storageID, age );
				}
			} else {
				$denied.show();
				if ( settings.hide_form_on_denied ) {
					$form.remove();
					$header.remove();
				} else if ( settings.clear_form_on_denied ) {
					$form[ 0 ].reset();
				}
			}
		}

		if ( elementorFrontend.isEditMode() ) {
			closeLightbox();
			glightboxInstance = buildGlightbox();

			if ( 'yes' === settings.popup_open ) {
				openLightbox();
			}
		} else {
			if ( isAdmin ) {
				localStorage.removeItem( storageID );
			}

			if ( ! localStorage.getItem( storageID ) || localStorage.getItem( storageID ) < settings.age ) {
				openLightbox();
				$form.on( 'submit', onSubmit );
			}
		}
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-age-gate.classic', initAgeGate );
	} );
}( jQuery ) );
