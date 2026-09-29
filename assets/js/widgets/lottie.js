( function ( $ ) {
	'use strict';

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	}

	function parseConfig( el ) {
		var raw = el.getAttribute( 'data-ltxe-lottie' ) || '{}';
		try {
			return JSON.parse( raw );
		} catch ( e ) {
			return {};
		}
	}

	function getLottieApi() {
		if ( window.lottie ) {
			return window.lottie;
		}
		if ( window.bodymovin ) {
			return window.bodymovin;
		}
		return null;
	}

	function LtxeLottie( container ) {
		this.el = container;
		this.config = parseConfig( container );
		this.anim = null;
		this.scrubRaf = 0;
		this.init();
	}

	LtxeLottie.prototype.init = function () {
		var api = getLottieApi();
		var canvas = this.el.querySelector( '.ee-lottie__canvas' );
		if ( ! api || ! canvas || ! this.config.src ) {
			return;
		}

		this.anim = api.loadAnimation( {
			container: canvas,
			renderer: 'svg',
			loop: !! this.config.loop && 'autoplay' === this.config.trigger,
			autoplay: false,
			path: this.config.src,
		} );

		var self = this;
		this.anim.addEventListener( 'DOMLoaded', function () {
			if ( self.config.use_segment ) {
				self.anim.playSegments(
					[ self.config.segment_start, self.config.segment_end ],
					true
				);
				self.anim.pause();
			}
			self.attachTrigger();
		} );
	};

	LtxeLottie.prototype.attachTrigger = function () {
		var self = this;
		if ( ! this.anim ) {
			return;
		}

		if ( prefersReducedMotion() ) {
			this.anim.goToAndStop( this.config.use_segment ? this.config.segment_start : 0, true );
			return;
		}

		if ( this.config.speed ) {
			this.anim.setSpeed( this.config.speed );
		}

		if ( 'reverse' === this.config.direction ) {
			this.anim.setDirection( -1 );
		}

		switch ( this.config.trigger ) {
			case 'autoplay':
				if ( false !== this.config.autoplay ) {
					this.anim.play();
				}
				if ( 'alternate' === this.config.direction ) {
					this.anim.addEventListener( 'complete', function () {
						self.anim.setDirection( self.anim.playDirection * -1 );
						self.anim.play();
					} );
				}
				break;

			case 'scroll':
				if ( 'undefined' !== typeof IntersectionObserver ) {
					new IntersectionObserver( function ( entries ) {
						if ( entries[ 0 ] && entries[ 0 ].isIntersecting ) {
							self.anim.goToAndPlay( 0 );
						}
					}, { threshold: 0.3 } ).observe( this.el );
				} else {
					this.anim.play();
				}
				break;

			case 'scroll_scrub':
				this.initScrollScrub();
				break;

			case 'hover':
				this.el.addEventListener( 'mouseenter', function () {
					self.anim.play();
				} );
				this.el.addEventListener( 'mouseleave', function () {
					self.anim.stop();
					self.anim.goToAndStop( 0, true );
				} );
				break;

			case 'click':
				this.el.addEventListener( 'click', function () {
					if ( self.anim.isPaused ) {
						self.anim.play();
					} else {
						self.anim.pause();
					}
				} );
				this.el.addEventListener( 'keydown', function ( e ) {
					if ( 13 === e.which || 32 === e.which ) {
						e.preventDefault();
						self.el.click();
					}
				} );
				break;
		}
	};

	LtxeLottie.prototype.initScrollScrub = function () {
		var self = this;
		var totalFrames = this.anim.totalFrames || 1;
		var startOffset = this.config.scrub_start || 'enter_bottom';
		var endOffset = this.config.scrub_end || 'exit_top';

		var getProgress = function () {
			var rect = self.el.getBoundingClientRect();
			var vh = window.innerHeight;
			var start;
			var end;

			if ( 'enter_center' === startOffset ) {
				start = rect.top + ( rect.height / 2 ) - ( vh / 2 );
			} else if ( 'enter_top' === startOffset ) {
				start = rect.top;
			} else {
				start = rect.top - vh;
			}

			if ( 'exit_center' === endOffset ) {
				end = rect.top + ( rect.height / 2 ) - ( vh / 2 );
			} else {
				end = rect.bottom - vh;
			}

			var range = end - start;
			if ( 0 === range ) {
				return 0;
			}
			return Math.min( 1, Math.max( 0, ( 0 - start ) / range ) );
		};

		var onScroll = function () {
			if ( self.scrubRaf ) {
				return;
			}
			self.scrubRaf = window.requestAnimationFrame( function () {
				self.scrubRaf = 0;
				var progress = getProgress();
				var frame = Math.round( progress * totalFrames );
				self.anim.goToAndStop( frame, true );
			} );
		};

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();
	};

	function initLottie( $scope ) {
		var root = $scope && $scope.length ? $scope[ 0 ] : document;
		var nodes = root.querySelectorAll( '[data-ltxe-lottie]' );
		for ( var i = 0; i < nodes.length; i++ ) {
			if ( nodes[ i ].getAttribute( 'data-ltxe-lottie-init' ) ) {
				continue;
			}
			nodes[ i ].setAttribute( 'data-ltxe-lottie-init', '1' );
			new LtxeLottie( nodes[ i ] );
		}
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-lottie.default', initLottie );
	} );

	$( function () {
		initLottie( $( document.body ) );
	} );
}( jQuery ) );
