( function () {
	'use strict';

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	}

	function parseConfig( el ) {
		try {
			return JSON.parse( el.getAttribute( 'data-ltxe-testimonials' ) || '{}' );
		} catch ( e ) {
			return {};
		}
	}

	function colsFromEl( el ) {
		var raw = window.getComputedStyle( el ).getPropertyValue( '--ltxe-tm-cols' );
		var n = parseInt( raw, 10 );
		return n > 0 ? n : 1;
	}

	function initOne( el ) {
		if ( el.getAttribute( 'data-ltxe-tm-init' ) ) {
			return;
		}
		el.setAttribute( 'data-ltxe-tm-init', '1' );

		var cfg = parseConfig( el );
		if ( 'carousel' !== cfg.display ) {
			return;
		}

		var track = el.querySelector( '.ltxe-testimonials__track' );
		var slides = el.querySelectorAll( '.ltxe-testimonial' );
		if ( ! track || slides.length < 2 ) {
			return;
		}

		var index = 0;
		var timer = null;
		var dotsWrap = el.querySelector( '.ltxe-testimonials__dots' );
		var status = el.querySelector( '.ltxe-testimonials__status' );
		var prev = el.querySelector( '.ltxe-testimonials__btn--prev' );
		var next = el.querySelector( '.ltxe-testimonials__btn--next' );

		function pageCount() {
			return Math.max( 1, Math.ceil( slides.length / colsFromEl( el ) ) );
		}

		function go( nextIndex ) {
			var pages = pageCount();
			if ( cfg.loop ) {
				index = ( ( nextIndex % pages ) + pages ) % pages;
			} else {
				index = Math.max( 0, Math.min( pages - 1, nextIndex ) );
			}
			var gap = parseFloat( window.getComputedStyle( el ).getPropertyValue( '--ltxe-tm-gap' ) ) || 0;
			var shift = index * ( el.querySelector( '.ltxe-testimonials__viewport' ).clientWidth + gap );
			track.style.transform = 'translateX(-' + shift + 'px)';
			updateChrome();
		}

		function updateChrome() {
			if ( status ) {
				status.textContent = ( index + 1 ) + ' / ' + pageCount();
			}
			if ( dotsWrap ) {
				var buttons = dotsWrap.querySelectorAll( '.ltxe-testimonials__dot' );
				buttons.forEach( function ( btn, i ) {
					btn.classList.toggle( 'is-active', i === index );
					btn.setAttribute( 'aria-selected', i === index ? 'true' : 'false' );
				} );
			}
		}

		function buildDots() {
			if ( ! dotsWrap ) {
				return;
			}
			dotsWrap.textContent = '';
			var pages = pageCount();
			for ( var i = 0; i < pages; i++ ) {
				var btn = document.createElement( 'button' );
				btn.type = 'button';
				btn.className = 'ltxe-testimonials__dot';
				var labelTpl = el.getAttribute( 'data-slide-label' ) || 'Review %s';
				btn.setAttribute( 'aria-label', labelTpl.replace( '%s', String( i + 1 ) ) );
				btn.addEventListener( 'click', function ( page ) {
					return function () {
						stopAuto();
						go( page );
					};
				}( i ) );
				dotsWrap.appendChild( btn );
			}
		}

		function stopAuto() {
			if ( timer ) {
				window.clearInterval( timer );
				timer = null;
			}
			el.classList.add( 'is-paused' );
		}

		function startAuto() {
			if ( ! cfg.autoplay || prefersReducedMotion() || slides.length < 2 ) {
				return;
			}
			stopAuto();
			el.classList.remove( 'is-paused' );
			timer = window.setInterval( function () {
				go( index + 1 );
			}, cfg.speed >= 2000 ? cfg.speed : 5000 );
		}

		if ( prev ) {
			prev.addEventListener( 'click', function () {
				stopAuto();
				go( index - 1 );
			} );
		}
		if ( next ) {
			next.addEventListener( 'click', function () {
				stopAuto();
				go( index + 1 );
			} );
		}

		var pauseBtn = el.querySelector( '.ltxe-testimonials__pause' );
		if ( pauseBtn ) {
			pauseBtn.addEventListener( 'click', function () {
				if ( timer ) {
					stopAuto();
					pauseBtn.setAttribute( 'aria-pressed', 'true' );
					pauseBtn.textContent = pauseBtn.getAttribute( 'data-play' ) || 'Play';
					pauseBtn.setAttribute( 'aria-label', pauseBtn.getAttribute( 'data-play-label' ) || 'Play carousel' );
				} else {
					startAuto();
					pauseBtn.setAttribute( 'aria-pressed', 'false' );
					pauseBtn.textContent = pauseBtn.getAttribute( 'data-pause' ) || 'Pause';
					pauseBtn.setAttribute( 'aria-label', pauseBtn.getAttribute( 'data-pause-label' ) || 'Pause carousel' );
				}
			} );
		}

		el.addEventListener( 'keydown', function ( e ) {
			if ( 'ArrowLeft' === e.key ) {
				e.preventDefault();
				stopAuto();
				go( index - 1 );
			}
			if ( 'ArrowRight' === e.key ) {
				e.preventDefault();
				stopAuto();
				go( index + 1 );
			}
		} );
		if ( ! el.hasAttribute( 'tabindex' ) ) {
			el.setAttribute( 'tabindex', '0' );
		}

		buildDots();
		go( 0 );
		startAuto();

		window.addEventListener( 'resize', function () {
			buildDots();
			go( index );
		} );
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ltxe-testimonials' ).forEach( initOne );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ltxe-testimonials.default', function ( $scope ) {
				init( $scope[ 0 ] );
			} );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			init( document );
		} );
	} else {
		init( document );
	}
}() );
