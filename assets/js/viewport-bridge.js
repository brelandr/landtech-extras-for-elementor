( function () {
	'use strict';

	var cookieName = 'ltxe_vp_width';

	function consentAllows() {
		if ( ! document.querySelector( '.ltxe-cookie-consent' ) ) {
			return true;
		}
		return !! ( window.ltxeConsent && window.ltxeConsent.allows && window.ltxeConsent.allows( 'functional' ) );
	}

	function setWidthCookie() {
		if ( ! consentAllows() ) {
			document.cookie = cookieName + '=;path=/;max-age=0;SameSite=Lax';
			return;
		}
		var w = window.innerWidth || document.documentElement.clientWidth || 0;
		if ( w < 1 ) {
			return;
		}
		document.cookie = cookieName + '=' + String( w ) + ';path=/;max-age=3600;SameSite=Lax';
	}

	setWidthCookie();
	window.addEventListener( 'resize', setWidthCookie, { passive: true } );
	document.addEventListener( 'ltxe_consent_changed', setWidthCookie );
}() );
