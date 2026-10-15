( function () {
	'use strict';

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	}

	function animateSvg( el ) {
		var type = el.getAttribute( 'data-svg-animation' ) || 'none';
		if ( 'none' === type || prefersReducedMotion() ) {
			return;
		}
		if ( el.getAttribute( 'data-ltxe-svg-anim' ) ) {
			return;
		}
		el.setAttribute( 'data-ltxe-svg-anim', '1' );

		var duration = parseInt( el.getAttribute( 'data-svg-duration' ), 10 ) || 1200;
		var svg = el.querySelector( 'svg' ) || el;
		var paths = svg.querySelectorAll ? svg.querySelectorAll( 'path' ) : [];

		function run() {
			if ( typeof window.ltxeAnime !== 'function' && ( ! window.anime || typeof window.anime.animate !== 'function' ) ) {
				return;
			}
			var animate = window.ltxeAnime || function ( opts ) {
				return window.anime.animate( opts.targets, window.ltxeAnimeV4Params ? window.ltxeAnimeV4Params( opts ) : opts );
			};

			if ( 'draw' === type ) {
				paths.forEach( function ( path ) {
					var len = 0;
					try {
						len = path.getTotalLength();
					} catch ( e ) {
						len = 0;
					}
					if ( ! len ) {
						return;
					}
					path.style.strokeDasharray = String( len );
					path.style.strokeDashoffset = String( len );
					animate( {
						targets: path,
						strokeDashoffset: [ len, 0 ],
						duration: duration,
						easing: 'easeInOutQuad',
					} );
				} );
				return;
			}

			if ( 'fade_in' === type ) {
				animate( { targets: svg, opacity: [ 0, 1 ], duration: duration } );
				return;
			}
			if ( 'scale_up' === type ) {
				animate( { targets: svg, scale: [ 0.6, 1 ], duration: duration } );
				return;
			}
			if ( 'rotate' === type ) {
				animate( { targets: svg, rotate: [ 0, 360 ], duration: duration } );
			}
		}

		var trigger = el.getAttribute( 'data-svg-trigger' ) || 'scroll';
		if ( 'load' === trigger ) {
			run();
			return;
		}
		if ( 'hover' === trigger ) {
			el.addEventListener( 'mouseenter', run, { once: true } );
			return;
		}
		if ( 'IntersectionObserver' in window ) {
			var io = new IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						run();
						io.disconnect();
					}
				} );
			}, { threshold: 0.25 } );
			io.observe( el );
		} else {
			run();
		}
	}

	/**
	 * Strips active content before the node is inserted into the page.
	 */
	function sanitizeSvgNode( svg ) {
		var svgNs = 'http://www.w3.org/2000/svg';
		var xlinkNs = 'http://www.w3.org/1999/xlink';
		var xmlNs = 'http://www.w3.org/XML/1998/namespace';
		var blocked = {
			script: 1,
			style: 1,
			foreignobject: 1,
			iframe: 1,
			embed: 1,
			object: 1,
			audio: 1,
			video: 1,
			link: 1
		};

		function localName( node ) {
			return String( node.localName || node.nodeName || '' ).toLowerCase().split( ':' ).pop();
		}

		function hrefIsFragment( value ) {
			var trimmed = String( value || '' ).replace( /^\s+|\s+$/g, '' );
			return '#' === trimmed.charAt( 0 );
		}

		function elementAllowed( node ) {
			var ns = node.namespaceURI || '';
			if ( blocked[ localName( node ) ] ) {
				return false;
			}
			if ( ns && ns !== svgNs ) {
				return false;
			}
			return true;
		}

		function stripAttributes( node ) {
			var attrs = node.attributes ? Array.prototype.slice.call( node.attributes ) : [];
			var index, attr, name, ns, value;

			for ( index = 0; index < attrs.length; index++ ) {
				attr = attrs[ index ];
				name = localName( attr );
				ns = attr.namespaceURI || '';
				value = attr.value || '';

				if ( ns === xlinkNs && 'href' !== name ) {
					node.removeAttributeNS( ns, attr.localName || name );
					continue;
				}
				if ( ns && ns !== svgNs && ns !== xlinkNs && ns !== xmlNs ) {
					node.removeAttributeNS( ns, attr.localName || name );
					continue;
				}
				if ( 0 === name.indexOf( 'on' ) ) {
					if ( ns ) {
						node.removeAttributeNS( ns, attr.localName || name );
					} else {
						node.removeAttribute( attr.name );
					}
					continue;
				}
				if ( 'href' === name && ! hrefIsFragment( value ) ) {
					if ( ns ) {
						node.removeAttributeNS( ns, attr.localName || name );
					} else {
						node.removeAttribute( attr.name );
					}
				}
			}
		}

		if ( ! svg || 1 !== svg.nodeType || ! elementAllowed( svg ) ) {
			return null;
		}

		stripAttributes( svg );

		var nodes = svg.querySelectorAll ? svg.querySelectorAll( '*' ) : [];
		var nodeIndex;

		for ( nodeIndex = nodes.length - 1; nodeIndex >= 0; nodeIndex-- ) {
			if ( ! elementAllowed( nodes[ nodeIndex ] ) ) {
				if ( nodes[ nodeIndex ].parentNode ) {
					nodes[ nodeIndex ].parentNode.removeChild( nodes[ nodeIndex ] );
				}
				continue;
			}
			stripAttributes( nodes[ nodeIndex ] );
		}

		return svg;
	}

	function extractSvgNode( data ) {
		if ( ! data ) {
			return null;
		}
		if ( data.documentElement && data.documentElement.tagName && 'svg' === data.documentElement.tagName.toLowerCase() ) {
			return data.documentElement;
		}
		if ( data.tagName && 'svg' === data.tagName.toLowerCase() ) {
			return data;
		}
		var markup = 'string' === typeof data ? data : '';
		if ( ! markup && data.documentElement ) {
			try {
				markup = new XMLSerializer().serializeToString( data.documentElement );
			} catch ( e ) {
				markup = '';
			}
		}
		if ( ! markup ) {
			return null;
		}
		var parsed = new DOMParser().parseFromString( markup, 'image/svg+xml' );
		var found = parsed.querySelector( 'svg' );
		if ( found && ! parsed.querySelector( 'parsererror' ) ) {
			return found;
		}
		return null;
	}

	function settingsFor( el ) {
		if ( ! window.jQuery || ! window.ee || ! window.ee.getElementSettings ) {
			return {};
		}
		var $widget = window.jQuery( el ).closest( '.elementor-element' );
		if ( ! $widget.length ) {
			return {};
		}
		try {
			return window.ee.getElementSettings( $widget ) || {};
		} catch ( e ) {
			return {};
		}
	}

	function resolveUrl( el, settings ) {
		var url = el.getAttribute( 'data-url' ) || '';
		if ( settings && 'url' === settings.svg_source && settings.svg_custom_url && settings.svg_custom_url.url ) {
			url = settings.svg_custom_url.url;
		} else if ( settings && settings.svg && settings.svg.url ) {
			url = settings.svg.url;
		}
		return url;
	}

	function applySvgOptions( el, settings ) {
		if ( ! window.jQuery || ! settings || ! Object.keys( settings ).length ) {
			return;
		}
		var $ = window.jQuery;
		var $svg = $( el ).find( 'svg' ).first();
		if ( ! $svg.length ) {
			return;
		}

		$svg.find( 'title' ).remove();
		$svg.find( 'desc' ).remove();

		var $shapes = $svg.find( 'circle, ellipse, polygon, rect, path, line, polyline' );
		settings = settings || {};

		if ( 'yes' === settings.remove_inline_css ) {
			$shapes.each( function () {
				var $shape = $( this );
				var strokeWidth = $shape.css( 'stroke-width' );
				strokeWidth = ( strokeWidth > 0 && strokeWidth < 1 ) ? 1 : strokeWidth;
				$shape.attr( 'stroke', $shape.css( 'stroke' ) );
				$shape.attr( 'stroke-width', strokeWidth );
				$shape.attr( 'stroke-linecap', $shape.css( 'stroke-linecap' ) );
				$shape.attr( 'stroke-dasharray', $shape.css( 'stroke-dasharray' ) );
				$shape.attr( 'stroke-miterlimit', $shape.css( 'stroke-miterlimit' ) );
				$shape.attr( 'fill', $shape.css( 'fill' ) );
			} );
			$svg.find( 'style' ).remove();
		}

		if ( 'yes' === settings.override_colors ) {
			$shapes.filter( '[fill]:not([fill="none"])' ).attr( 'fill', 'currentColor' );
			$shapes.filter( '[stroke]:not([stroke="none"])' ).attr( 'stroke', 'currentColor' );
		}

		var node = $svg.get( 0 );
		if ( ! node ) {
			return;
		}
		if ( 'yes' !== settings.maintain_ratio ) {
			node.setAttribute( 'preserveAspectRatio', 'none' );
		}
		if ( 'yes' === settings.sizing ) {
			node.removeAttribute( 'width' );
			node.removeAttribute( 'height' );
		}
	}

	function prepareUrl( el, settings ) {
		var url = resolveUrl( el, settings );
		if ( ! url ) {
			return false;
		}
		if ( url.split( '.' ).pop().toLowerCase() !== 'svg' ) {
			if ( ! el.getAttribute( 'data-ltxe-svg-alerted' ) ) {
				el.setAttribute( 'data-ltxe-svg-alerted', '1' );
				window.alert( 'Please select a SVG file format.' );
			}
			return false;
		}
		if ( url !== ( el.getAttribute( 'data-url' ) || '' ) ) {
			el.setAttribute( 'data-url', url );
			el.removeAttribute( 'data-ltxe-svg-loaded' );
			var existing = el.querySelector( 'svg' );
			if ( existing && existing.parentNode ) {
				existing.parentNode.removeChild( existing );
			}
		}
		return true;
	}

	function injectFromUrl( el, settings ) {
		if ( el.getAttribute( 'data-ltxe-svg-loaded' ) || el.querySelector( 'svg' ) ) {
			return;
		}
		var url = el.getAttribute( 'data-url' ) || '';
		if ( ! url || url.split( '.' ).pop().split( '?' )[ 0 ].toLowerCase() !== 'svg' ) {
			return;
		}
		el.setAttribute( 'data-ltxe-svg-loaded', '1' );
		window.fetch( url, { credentials: 'same-origin' } ).then( function ( response ) {
			if ( ! response.ok ) {
				el.removeAttribute( 'data-ltxe-svg-loaded' );
				return '';
			}
			return response.text();
		} ).then( function ( text ) {
			var svg = sanitizeSvgNode( extractSvgNode( text ) );
			if ( ! svg ) {
				el.removeAttribute( 'data-ltxe-svg-loaded' );
				return;
			}
			el.innerHTML = '';
			el.appendChild( document.importNode( svg, true ) );
			applySvgOptions( el, settings );
			animateSvg( el );
		} ).catch( function () {
			el.removeAttribute( 'data-ltxe-svg-loaded' );
		} );
	}

	function init( scope ) {
		var root = scope && scope.querySelectorAll ? scope : document;
		root.querySelectorAll( '.ee-inline-svg' ).forEach( function ( el ) {
			var settings = settingsFor( el );
			if ( ! prepareUrl( el, settings ) && ! el.querySelector( 'svg' ) ) {
				return;
			}
			if ( el.querySelector( 'svg' ) ) {
				sanitizeSvgNode( el.querySelector( 'svg' ) );
				applySvgOptions( el, settings );
				animateSvg( el );
				return;
			}
			injectFromUrl( el, settings );
			if ( 'none' === ( el.getAttribute( 'data-svg-animation' ) || 'none' ) ) {
				return;
			}
			var observer = new MutationObserver( function () {
				if ( el.querySelector( 'svg' ) ) {
					observer.disconnect();
					applySvgOptions( el, settings );
					animateSvg( el );
				}
			} );
			observer.observe( el, { childList: true, subtree: true } );
		} );
	}

	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-inline-svg.default', function ( $scope ) {
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
