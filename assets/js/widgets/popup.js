( function ( $ ) {
	'use strict';

	var openedLock = {};

	function cookieKey( id ) {
		return 'ltxe_popup_' + id;
	}

	function trackPopup( eventName, config ) {
		var api = window.ltxePopupAnalytics;
		if ( ! api || ! api.rest ) {
			return;
		}
		var postId = 0;
		if ( window.elementorFrontend && elementorFrontend.config && elementorFrontend.config.post ) {
			postId = parseInt( elementorFrontend.config.post.id || 0, 10 );
		}
		fetch( api.rest + 'popup-analytics/event', {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify( {
				event: eventName,
				widget_id: config && config.id ? String( config.id ) : '',
				post_id: postId,
			} ),
		} ).catch( function () {} );
	}

	function sessionKey( id ) {
		return 'ltxe_popup_sess_' + id;
	}

	function hasCookie( name ) {
		return -1 !== document.cookie.indexOf( name + '=1' );
	}

	function shouldShow( config ) {
		var id = config.id;
		if ( config.suppress_cookie && hasCookie( cookieKey( id ) ) ) {
			return false;
		}
		if ( config.suppress_session && window.sessionStorage && sessionStorage.getItem( sessionKey( id ) ) ) {
			return false;
		}
		if ( config.show_max_times > 0 && window.sessionStorage ) {
			var shown = parseInt( sessionStorage.getItem( sessionKey( id ) + '_count' ) || '0', 10 );
			if ( shown >= config.show_max_times ) {
				return false;
			}
		}
		return true;
	}

	function onClose( config ) {
		var id = config.id;
		if ( config.suppress_cookie ) {
			var days = config.suppress_cookie_days || 30;
			var expires = new Date( Date.now() + days * 864e5 ).toUTCString();
			document.cookie = cookieKey( id ) + '=1; expires=' + expires + '; path=/; SameSite=Lax';
		}
		if ( window.sessionStorage ) {
			if ( config.suppress_session ) {
				sessionStorage.setItem( sessionKey( id ), '1' );
			}
			var count = parseInt( sessionStorage.getItem( sessionKey( id ) + '_count' ) || '0', 10 );
			sessionStorage.setItem( sessionKey( id ) + '_count', String( count + 1 ) );
		}
	}

	function readConfig( $scope ) {
		var settings = {};
		if ( window.elementorFrontend && elementorFrontend.config && $scope.data( 'id' ) ) {
			try {
				settings = elementorFrontend.getElementSettings ? elementorFrontend.getElementSettings( $scope ) : {};
			} catch ( e ) {
				settings = $scope.data( 'settings' ) || {};
			}
		} else {
			settings = $scope.data( 'settings' ) || {};
		}
		var pct = settings.trigger_scroll_pct;
		return {
			id: String( $scope.data( 'id' ) || '' ),
			trigger: settings.popup_trigger || 'click',
			trigger_delay: parseFloat( settings.trigger_delay || settings.popup_delay / 1000 || 5 ),
			popup_delay: parseInt( settings.popup_delay || 3000, 10 ),
			trigger_scroll_pct: pct && pct.size ? parseFloat( pct.size ) : 50,
			trigger_inactivity: parseInt( settings.trigger_inactivity || 30, 10 ),
			suppress_cookie: 'yes' === settings.suppress_cookie,
			suppress_cookie_days: parseInt( settings.suppress_cookie_days || 30, 10 ),
			suppress_session: 'yes' === settings.suppress_session,
			show_max_times: parseInt( settings.show_max_times || 0, 10 ),
		};
	}

	function initAdvanced( $scope ) {
		if ( $scope.data( 'ltxePopupAdvInit' ) ) {
			return;
		}
		$scope.data( 'ltxePopupAdvInit', true );
		var config = readConfig( $scope );
		if ( ! config.id ) {
			return;
		}

		$( document ).on( 'landtech_extras/popup_before_open.ltxe' + config.id, function ( e, scopeId ) {
			if ( String( scopeId ) !== config.id ) {
				return;
			}
			if ( openedLock[ config.id ] ) {
				e.preventDefault();
				return;
			}
			if ( ! shouldShow( config ) ) {
				trackPopup( 'popup_cookie_skip', config );
				e.preventDefault();
			}
		} );

		$scope.on( 'ltxe:popup:closed', function () {
			onClose( config );
			trackPopup( 'popup_close', config );
		} );

		$scope.on( 'click.ltxePopupAnalytics', 'a, button, .elementor-button', function () {
			if ( $( this ).closest( '.ee-popup__content, .glightbox-container' ).length ) {
				trackPopup( 'popup_cta_click', config );
			}
		} );

		var fire = function () {
			if ( openedLock[ config.id ] || ! shouldShow( config ) ) {
				return;
			}
			openedLock[ config.id ] = true;
			$( document ).trigger( 'landtech_extras/popup_advanced_open', [ config.id, $scope ] );
			trackPopup( 'popup_open', config );
		};

		if ( 'delay' === config.trigger ) {
			setTimeout( fire, Math.max( 0, config.trigger_delay ) * 1000 );
		}

		if ( 'scroll_pct' === config.trigger ) {
			var onScroll = function () {
				var max = document.body.scrollHeight - window.innerHeight;
				var scrolled = max > 0 ? ( window.scrollY / max ) * 100 : 100;
				if ( scrolled >= config.trigger_scroll_pct ) {
					fire();
					window.removeEventListener( 'scroll', onScroll );
				}
			};
			window.addEventListener( 'scroll', onScroll, { passive: true } );
		}

		if ( 'inactivity' === config.trigger ) {
			var timer;
			var reset = function () {
				window.clearTimeout( timer );
				timer = window.setTimeout( fire, Math.max( 1, config.trigger_inactivity ) * 1000 );
			};
			[ 'mousemove', 'keypress', 'scroll', 'touchstart' ].forEach( function ( evt ) {
				window.addEventListener( evt, reset, { passive: true } );
			} );
			reset();
		}
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-popup.classic', initAdvanced );
	} );

	$( function () {
		$( '.elementor-widget-ee-popup' ).each( function () {
			initAdvanced( $( this ) );
		} );
	} );

	/**
	 * U30 Popup focus trap — exact FREE-PLUGIN-UPGRADE-PLAN.md snippet.
	 *
	 * @param {Element}  modal      Dialog root.
	 * @param {Function} closePopup Close callback.
	 * @return {void}
	 */
	function ltxePopupFocusTrap( modal, closePopup ) {
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
	}

	function tagOpenLightbox( scopeId ) {
		var wrap = document.querySelector( '.glightbox-container' );
		if ( ! wrap || ! wrap.querySelector( '.ee-popup__content' ) ) {
			return;
		}
		wrap.classList.add( 'ee-mfp-popup', 'ee-mfp-popup--overlay', 'mfp-popup--valign-middle' );
		if ( scopeId ) {
			wrap.classList.add( 'ee-mfp-popup-' + String( scopeId ) );
		}
	}

	if ( window.MutationObserver ) {
		var popupChromeObserver = new window.MutationObserver( function () {
			tagOpenLightbox( $( '.elementor-widget-ee-popup' ).first().data( 'id' ) );
		} );
		popupChromeObserver.observe( document.body, { childList: true, subtree: true } );
	}

	$( document ).on( 'click.ltxePopupFallback', '.ee-popup__trigger--click', function ( e ) {
		var $scope = $( this ).closest( '.elementor-widget-ee-popup' );
		if ( ! $scope.length || $scope.data( 'ltxePopupInit' ) || typeof window.GLightbox !== 'function' ) {
			return;
		}
		e.preventDefault();
		var $content = $scope.find( '.ee-popup__content' ).first();
		if ( ! $content.length ) {
			return;
		}
		var node = $content.get( 0 );
		node.classList.remove( 'mfp-hide', 'glightbox-hide' );
		node.style.removeProperty( 'display' );
		var instance = window.GLightbox( {
			elements: [ { content: node, type: 'inline' } ],
			closeOnOutsideClick: true,
		} );
		instance.open();
		trackPopup( 'popup_open', { id: String( $scope.data( 'id' ) || '' ) } );
		window.setTimeout( function () {
			tagOpenLightbox( $scope.data( 'id' ) );
			var modal = document.querySelector( '.glightbox-container' );
			ltxePopupFocusTrap( modal, function () {
				instance.close();
			} );
		}, 30 );
	} );

	$( document ).on( 'click.ltxePopupFooterClose', '.glightbox-container .ee-popup__footer__button', function ( e ) {
		e.preventDefault();
		var closeBtn = document.querySelector( '.glightbox-container .gclose' );
		if ( closeBtn ) {
			closeBtn.click();
		}
	} );
}( jQuery ) );
