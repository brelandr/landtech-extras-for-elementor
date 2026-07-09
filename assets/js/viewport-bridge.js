( function () {
	'use strict';

	var cookieName = 'ltxe_vp_width';

	function setWidthCookie() {
		var w = window.innerWidth || document.documentElement.clientWidth || 0;
		if ( w < 1 ) {
			return;
		}
		document.cookie = cookieName + '=' + String( w ) + ';path=/;max-age=3600;SameSite=Lax';
	}

	setWidthCookie();
	window.addEventListener( 'resize', setWidthCookie, { passive: true } );
}() );
