( function () {
	'use strict';

	function ytId( url ) {
		var m = String( url ).match( /(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/ );
		return m ? m[ 1 ] : '';
	}

	function vimeoId( url ) {
		var m = String( url ).match( /vimeo\.com\/(?:video\/)?(\d+)/ );
		return m ? m[ 1 ] : '';
	}

	function buildPlayer( item, playerEl ) {
		while ( playerEl.firstChild ) {
			playerEl.removeChild( playerEl.firstChild );
		}
		if ( 'youtube' === item.type ) {
			var yid = ytId( item.url );
			if ( ! yid ) {
				return;
			}
			var iframe = document.createElement( 'iframe' );
			iframe.setAttribute( 'src', 'https://www.youtube.com/embed/' + encodeURIComponent( yid ) + '?autoplay=1' );
			iframe.setAttribute( 'allowfullscreen', 'allowfullscreen' );
			iframe.setAttribute( 'allow', 'autoplay; encrypted-media' );
			iframe.setAttribute( 'title', item.title || 'YouTube video' );
			playerEl.appendChild( iframe );
			return;
		}
		if ( 'vimeo' === item.type ) {
			var vid = vimeoId( item.url );
			if ( ! vid ) {
				return;
			}
			var viframe = document.createElement( 'iframe' );
			viframe.setAttribute( 'src', 'https://player.vimeo.com/video/' + encodeURIComponent( vid ) + '?autoplay=1' );
			viframe.setAttribute( 'allowfullscreen', 'allowfullscreen' );
			viframe.setAttribute( 'allow', 'autoplay' );
			viframe.setAttribute( 'title', item.title || 'Vimeo video' );
			playerEl.appendChild( viframe );
			return;
		}
		var video = document.createElement( 'video' );
		video.setAttribute( 'controls', 'controls' );
		video.setAttribute( 'autoplay', 'autoplay' );
		video.setAttribute( 'src', item.url );
		playerEl.appendChild( video );
		return video;
	}

	function initOne( container ) {
		if ( container.getAttribute( 'data-ltxe-vp-init' ) ) {
			return;
		}
		container.setAttribute( 'data-ltxe-vp-init', '1' );

		var config;
		try {
			config = JSON.parse( container.getAttribute( 'data-ltxe-playlist' ) || '{}' );
		} catch ( e ) {
			return;
		}
		var items = config.items || [];
		if ( ! items.length ) {
			return;
		}

		var playerEl = container.querySelector( '.ltxe-vp__player' );
		var counter = container.querySelector( '.ltxe-vp__counter' );
		var current = 0;

		function load( idx ) {
			if ( idx < 0 || idx >= items.length ) {
				return;
			}
			current = idx;
			var media = buildPlayer( items[ idx ], playerEl );
			container.querySelectorAll( '.ltxe-vp__item' ).forEach( function ( el, i ) {
				el.classList.toggle( 'is-active', i === idx );
			} );
			if ( counter ) {
				counter.textContent = ( idx + 1 ) + ' / ' + items.length;
			}
			if ( media && config.auto_advance ) {
				media.addEventListener( 'ended', function () {
					if ( current < items.length - 1 ) {
						load( current + 1 );
					}
				} );
			}
		}

		container.querySelectorAll( '.ltxe-vp__item' ).forEach( function ( el ) {
			el.addEventListener( 'click', function () {
				load( parseInt( el.getAttribute( 'data-index' ), 10 ) || 0 );
			} );
		} );

		load( 0 );
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-vp' ).forEach( initOne );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-video-playlist.default', function ( $scope ) {
				init( $scope[ 0 ] );
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			init( document );
		} );
	} else {
		init( document );
	}
}() );
