( function ( $ ) {
	'use strict';

	function slugify( text ) {
		return String( text || '' )
			.toLowerCase()
			.trim()
			.replace( /[^\w\s-]/g, '' )
			.replace( /[\s_-]+/g, '-' )
			.replace( /^-+|-+$/g, '' );
	}

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	}

	function getScopeRoot( $nav ) {
		var scope = $nav.attr( 'data-scope' ) || 'page';
		if ( 'wrapper' === scope ) {
			var $container = $nav.closest( '.elementor-element' ).parent().closest( '.elementor-element' );
			if ( $container.length ) {
				return $container;
			}
		}
		return $( '#content, .site-main, main, article' ).first();
	}

	function buildToc( $nav ) {
		var levels = [];
		try {
			levels = JSON.parse( $nav.attr( 'data-levels' ) || '[]' );
		} catch ( e ) {
			levels = [ 'h2', 'h3', 'h4' ];
		}
		if ( ! levels.length ) {
			return;
		}

		var exclude = $nav.attr( 'data-exclude' ) || '';
		var $root = getScopeRoot( $nav );
		var selector = levels.join( ',' );
		var $headings = $root.find( selector );
		if ( exclude ) {
			$headings = $headings.not( exclude ).filter( function () {
				return ! $( this ).closest( exclude ).length;
			} );
		}

		var $list = $nav.find( '.ltxe-toc__list' ).empty();
		var usedIds = {};

		$headings.each( function () {
			var $h = $( this );
			var text = $h.text().trim();
			if ( ! text ) {
				return;
			}
			var id = $h.attr( 'id' );
			if ( ! id ) {
				id = slugify( text );
				var base = id;
				var n = 2;
				while ( usedIds[ id ] ) {
					id = base + '-' + n;
					n += 1;
				}
				$h.attr( 'id', id );
			}
			usedIds[ id ] = true;

			var tag = ( this.tagName || 'H2' ).toLowerCase();
			var depth = parseInt( tag.replace( 'h', '' ), 10 ) - ( parseInt( String( levels[ 0 ] ).replace( 'h', '' ), 10 ) || 2 );
			if ( depth < 0 ) {
				depth = 0;
			}

			var $li = $( '<li class="ltxe-toc__item ltxe-toc__item--depth-' + depth + '">' );
			var $a = $( '<a>' ).attr( 'href', '#' + id ).text( text );
			$li.append( $a );
			$list.append( $li );
		} );
	}

	function bindScroll( $nav ) {
		if ( '1' !== $nav.attr( 'data-highlight-active' ) ) {
			return;
		}
		if ( ! window.IntersectionObserver ) {
			return;
		}

		var $links = $nav.find( '.ltxe-toc__list a' );
		var map = {};
		$links.each( function () {
			var id = ( this.getAttribute( 'href' ) || '' ).replace( '#', '' );
			if ( id ) {
				map[ id ] = this;
			}
		} );

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						var id = entry.target.id;
						$links.removeClass( 'is-active' );
						if ( map[ id ] ) {
							$( map[ id ] ).addClass( 'is-active' );
						}
					}
				} );
			},
			{ rootMargin: '0px 0px -70% 0px', threshold: 0 }
		);

		Object.keys( map ).forEach( function ( id ) {
			var el = document.getElementById( id );
			if ( el ) {
				observer.observe( el );
			}
		} );
	}

	function bindProgress( $nav ) {
		if ( '1' !== $nav.attr( 'data-progress' ) ) {
			return;
		}
		var $bar = $nav.find( '.ltxe-toc__progress-bar' );
		$( window ).on( 'scroll.ltxeTocProgress', function () {
			var doc = document.documentElement;
			var scrollTop = doc.scrollTop || document.body.scrollTop;
			var height = doc.scrollHeight - doc.clientHeight;
			var pct = height > 0 ? Math.min( 100, ( scrollTop / height ) * 100 ) : 0;
			$bar.css( 'width', pct + '%' );
		} );
	}

	function bindSmoothScroll( $nav ) {
		$nav.on( 'click', '.ltxe-toc__list a', function ( e ) {
			var id = ( this.getAttribute( 'href' ) || '' ).replace( '#', '' );
			var target = document.getElementById( id );
			if ( ! target ) {
				return;
			}
			e.preventDefault();
			target.scrollIntoView( { behavior: prefersReducedMotion() ? 'auto' : 'smooth', block: 'start' } );
			if ( history.replaceState ) {
				history.replaceState( null, '', '#' + id );
			}
		} );
	}

	function initToc( $scope ) {
		$scope.find( '.ltxe-toc' ).each( function () {
			var $nav = $( this );
			if ( $nav.data( 'ltxeTocInit' ) ) {
				return;
			}
			$nav.data( 'ltxeTocInit', true );
			buildToc( $nav );
			bindSmoothScroll( $nav );
			bindScroll( $nav );
			bindProgress( $nav );
		} );
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-table-of-contents.default', initToc );
	} );

	$( function () {
		initToc( $( document.body ) );
	} );
}( jQuery ) );
