/**
 * Dismiss the WordPress.org review nudge.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var nudge = document.querySelector( '.ltxe-review-nudge' );
		var config = window.landtechExtrasReviewNudge;

		if ( ! nudge || ! config ) {
			return;
		}

		function dismissNudge() {
			var body = new window.URLSearchParams();
			body.set( 'action', config.action );
			body.set( '_wpnonce', config.nonce );
			window.fetch( config.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: body.toString()
			} );
		}

		nudge.addEventListener( 'click', function ( event ) {
			if ( event.target && event.target.classList.contains( 'notice-dismiss' ) ) {
				dismissNudge();
			}
		} );

		var manualDismiss = nudge.querySelector( '.ltxe-dismiss-review-nudge' );
		if ( manualDismiss ) {
			manualDismiss.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				dismissNudge();
				nudge.remove();
			} );
		}
	} );
}() );
