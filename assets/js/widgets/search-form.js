( function ( $ ) {
	'use strict';

	function serializeObject( data ) {
		var o = {};
		var a = data.serializeArray();

		$.each( a, function () {
			if ( ! this.value ) {
				return;
			}
			if ( o[ this.name ] ) {
				if ( ! o[ this.name ].push ) {
					o[ this.name ] = [ o[ this.name ] ];
				}
				o[ this.name ].push( this.value || '' );
			} else {
				o[ this.name ] = this.value || '';
			}
		} );

		return o;
	}

	function once( $scope, key ) {
		var flag = 'ltxeOnce_' + key;
		if ( ! $scope || ! $scope.length || $scope.data( flag ) ) {
			return false;
		}
		$scope.data( flag, true );
		return true;
	}

	function initFilters( $scope ) {
		if ( ! once( $scope, 'ee-search-form-filters' ) ) {
			return;
		}

		var elementSettings = window.ee.getElementSettings( $scope );
		var scopeId = $scope.data( 'id' );
		var $form = $scope.find( '.ee-search-form' );
		var $query = $scope.find( 'input[type=hidden][name=ltxe_search_query]' );
		var $input = $scope.find( '.ee-search-form__input' );
		var $container = $scope.find( '.ee-search-form__container' );
		var $searchFields = $form.find( ':input' ).filter( '.ee-form__field__control--search' );
		var $selectFields = $form.find( '.ee-form__field--select.ee-custom' ).find( ':input' ).filter( '.ee-form__field__control--select' );
		var $allFields = $form.find( ':input' ).filter( '.ee-form__field__control--all' );
		var $filterFields = $form.find( ':input' ).filter( ':not(.ee-form__field__control--sent)' );

		function setHiddenField() {
			var data = serializeObject( $searchFields );
			var staticRaw = $form.attr( 'data-ltxe-static-restrictions' );

			if ( staticRaw ) {
				try {
					var staticRestrictions = JSON.parse( staticRaw );
					if ( staticRestrictions && typeof staticRestrictions === 'object' ) {
						Object.keys( staticRestrictions ).forEach( function ( key ) {
							data[ key ] = staticRestrictions[ key ];
						} );
					}
				} catch ( e ) {
					// Ignore malformed static restriction payloads.
				}
			}

			$query.val( JSON.stringify( data ).replace( /\\/g, '' ) );
		}

		function optionsCheck( $control ) {
			var name = $control.attr( 'name' );
			var $checkAllControl = $allFields.filter( '[name=' + name + ']' );

			if ( $checkAllControl.is( ':checked' ) ) {
				$checkAllControl.prop( 'checked', false );
			}
		}

		function allCheck( $checkAllControl ) {
			var name = $checkAllControl.attr( 'name' );
			var $controls = $searchFields.filter( '[name=' + name + ']' );

			if ( $checkAllControl.is( ':checked' ) ) {
				$controls.prop( 'checked', true );
			} else {
				$controls.prop( 'checked', false );
			}

			setHiddenField();
		}

		function onFormSubmit() {
			$filterFields.attr( 'disabled', false );
			setHiddenField();
			$filterFields.attr( 'disabled', true );
		}

		setHiddenField();

		if ( $allFields.filter( '[type=checkbox]' ).length ) {
			$allFields.filter( '[type=checkbox]' ).on( 'change', function () {
				allCheck( $( this ) );
			} );

			$searchFields.filter( '[type=checkbox]' ).on( 'change', function () {
				optionsCheck( $( this ) );
			} );
		}

		$selectFields.select2( {
			containerCssClass: 'ee-select2 ee-form__field__control ee-form__field__control--text ee-form__field__control--select2',
			dropdownCssClass: 'ee-select2__dropdown ee-select2__dropdown--' + scopeId,
			minimumResultsForSearch: -1,
			width: '100%',
		} );

		if ( ! elementorFrontend.isEditMode() ) {
			$form.on( 'submit', onFormSubmit );

			$input.on( 'click blur', function () {
				$container.removeClass( 'ee--empty' );
			} );

			if ( 'yes' === elementSettings.live_ajax_search ) {
				var $liveResults = $scope.find( '.ee-search-form__live-results' );
				if ( ! $liveResults.length ) {
					$liveResults = $( '<div class="ee-search-form__live-results" role="listbox" aria-live="polite"></div>' );
					$container.append( $liveResults );
				}
				var liveTimer = null;
				$input.on( 'input.ltxeLiveSearch', function () {
					window.clearTimeout( liveTimer );
					var query = $.trim( $input.val() );
					if ( query.length < 2 ) {
						$liveResults.empty().hide();
						return;
					}
					liveTimer = window.setTimeout( function () {
						var restUrl = ( window.landtechExtrasFrontendConfig && window.landtechExtrasFrontendConfig.restSearchUrl ) ? window.landtechExtrasFrontendConfig.restSearchUrl : '';
						if ( ! restUrl ) {
							return;
						}
						$.getJSON( restUrl, {
							q: query,
							post_type: elementSettings.live_ajax_post_type || 'post',
						} ).done( function ( response ) {
							$liveResults.empty();
							if ( response && response.results && response.results.length ) {
								response.results.forEach( function ( item ) {
									$( '<a>', {
										'class': 'ee-search-form__live-results__item',
										'href': item.url,
										'text': item.title,
									} ).appendTo( $liveResults );
								} );
							}
							$liveResults.toggle( $liveResults.children().length > 0 );
						} );
					}, 350 );
				} );
			}

			$searchFields.on( 'change', function () {
				setHiddenField();
			} );
		}

		window.ee.onElementRemove( $scope, function () {} );
	}

	function initExpand( $scope ) {
		if ( ! once( $scope, 'ee-search-form-expand' ) ) {
			return;
		}

		var $form = $scope.find( '.ee-search-form' );
		var $overlay = $scope.find( '.ee-search-form__overlay' );
		var $close = $scope.find( '.ee-search-form__overlay__close' );
		var $input = $scope.find( '.ee-search-form__input' );
		var $submit = $scope.find( '.ee-search-form__submit' );
		var skin = window.ee.getElementSkin( $scope );

		function isOpen( form ) {
			return form.is( '.ee--active' );
		}

		function open( form ) {
			form.addClass( 'ee--active' );
			form.find( '.ee-search-form__input' ).focus();
		}

		function close( form ) {
			form.removeClass( 'ee--active' );
			form.find( '.ee-search-form__input' ).blur();
		}

		function onSubmitClick( e ) {
			e.preventDefault();

			if ( isOpen( $form ) ) {
				if ( $input.val() ) {
					$form.submit();
				} else {
					close( $form );
				}
				return;
			}

			open( $form );
		}

		function onDocumentClick( e ) {
			var $target = $( e.target );

			if ( e.target !== $form[ 0 ] && ! $target.closest( '.ee-search-form' ).length ) {
				close( $form );
			}
		}

		function onOverlayClick( e ) {
			if ( e.target === $overlay[ 0 ] && isOpen( $form ) ) {
				close( $form );
			}
		}

		$submit.on( 'click', onSubmitClick );

		if ( skin === 'expand' ) {
			$( document ).on( 'click', onDocumentClick );
		} else {
			$overlay.on( 'click', onOverlayClick );
			$close.on( 'click', close );
		}

		window.ee.onElementRemove( $scope, function () {
			$submit.off( 'click', onSubmitClick );

			if ( skin === 'expand' ) {
				$( document ).off( 'click', onDocumentClick );
			} else {
				$overlay.off( 'click', onOverlayClick );
				$close.off( 'click', close );
			}
		} );
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-search-form.classic', initFilters );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-search-form.expand', function ( $scope ) {
			initExpand( $scope );
			initFilters( $scope );
		} );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-search-form.fullscreen', function ( $scope ) {
			initExpand( $scope );
			initFilters( $scope );
		} );
	} );
}( jQuery ) );
