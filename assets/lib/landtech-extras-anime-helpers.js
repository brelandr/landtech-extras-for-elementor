/**
 * GSAP-compatible animation helpers using anime.js (MIT).
 *
 * @package LandTechExtras
 */
( function( window ) {
	'use strict';

	if ( 'undefined' === typeof window.anime ) {
		return;
	}

	function ltxeAnimeIsV4() {
		return window.anime && 'function' === typeof window.anime.animate;
	}

	function ltxeAnimeV4Params( opts ) {
		var params = Object.assign( {}, opts || {} );

		delete params.targets;

		if ( params.easing ) {
			params.ease = params.easing;
			delete params.easing;
		}

		if ( params.complete ) {
			params.onComplete = params.complete;
			delete params.complete;
		}

		return params;
	}

	function ltxeAnimeRun( opts ) {
		if ( ! opts ) {
			return null;
		}

		var targets = ltxeTargets( opts.targets );

		if ( ! targets.length ) {
			return null;
		}

		if ( ltxeAnimeIsV4() ) {
			return window.anime.animate( targets, ltxeAnimeV4Params( opts ) );
		}

		if ( 'function' === typeof window.anime ) {
			return window.anime( opts );
		}

		return null;
	}

	function ltxeAnimeSet( targets, props ) {
		var mapped = ltxeMapProps( props );

		if ( ltxeAnimeIsV4() ) {
			return window.anime.animate(
				ltxeTargets( targets ),
				Object.assign( { duration: 0 }, mapped )
			);
		}

		return ltxeAnimeRun( Object.assign( {
			targets: ltxeTargets( targets ),
			duration: 0,
		}, mapped ) );
	}

	function ltxeAnimeStagger( ms ) {
		if ( window.anime && 'function' === typeof window.anime.stagger ) {
			return window.anime.stagger( ms );
		}
		return ms;
	}

	function ltxeAnimeTimeline( opts ) {
		var params = Object.assign( {}, opts || {} );

		if ( params.easing ) {
			params.playbackEase = params.easing;
			delete params.easing;
		}

		if ( ltxeAnimeIsV4() && 'function' === typeof window.anime.createTimeline ) {
			return window.anime.createTimeline( params );
		}

		if ( window.anime && 'function' === typeof window.anime.timeline ) {
			return window.anime.timeline( params );
		}

		return {
			add: function() {
				return this;
			},
			init: function() {
				return this;
			},
			pause: function() {
				return this;
			},
		};
	}

	var ltxeEase = 'cubicBezier(0.446, 0, 0.034, 1)';

	function ltxeTargets( target ) {
		if ( ! target ) {
			return [];
		}
		if ( target.jquery ) {
			return target.get();
		}
		if ( Array.isArray( target ) ) {
			var nodes = [];
			var i;

			for ( i = 0; i < target.length; i++ ) {
				if ( target[ i ] && target[ i ].jquery ) {
					nodes = nodes.concat( target[ i ].get() );
				} else if ( target[ i ] ) {
					nodes.push( target[ i ] );
				}
			}

			return nodes;
		}
		if ( NodeList.prototype.isPrototypeOf( target ) ) {
			return Array.prototype.slice.call( target );
		}
		return [ target ];
	}

	function ltxeMapProps( props ) {
		var mapped = {};
		var key;

		if ( ! props ) {
			return mapped;
		}

		for ( key in props ) {
			if ( ! Object.prototype.hasOwnProperty.call( props, key ) ) {
				continue;
			}
			if ( 'ease' === key ) {
				mapped.easing = props.ease === ltxeEase || 'function' === typeof props.ease ? ltxeEase : props.ease;
				continue;
			}
			if ( 'clearProps' === key ) {
				mapped.clearProps = props.clearProps;
				continue;
			}
			if ( 'autoAlpha' === key ) {
				mapped.opacity = props.autoAlpha;
				if ( 0 === props.autoAlpha ) {
					mapped.visibility = 'hidden';
				} else {
					mapped.visibility = 'visible';
				}
				continue;
			}
			if ( 'x' === key ) {
				mapped.translateX = props.x;
				continue;
			}
			if ( 'y' === key ) {
				mapped.translateY = props.y;
				continue;
			}
			if ( 'rotationX' === key ) {
				mapped.rotateX = props.rotationX;
				continue;
			}
			if ( 'rotationY' === key ) {
				mapped.rotateY = props.rotationY;
				continue;
			}
			if ( 'transformStyle' === key ) {
				mapped.transformStyle = props.transformStyle;
				continue;
			}
			if ( 'perspective' === key ) {
				mapped.perspective = props.perspective;
				continue;
			}
			if ( 'backgroundColor' === key ) {
				mapped.backgroundColor = props.backgroundColor;
				continue;
			}
			if ( 'width' === key || 'height' === key || 'left' === key || 'top' === key || 'scale' === key ) {
				mapped[ key ] = 'number' === typeof props[ key ] ? props[ key ] + 'px' : props[ key ];
				continue;
			}
			if ( 'strokeDasharray' === key || 'strokeDashoffset' === key ) {
				mapped[ key ] = props[ key ];
				continue;
			}
			mapped[ key ] = props[ key ];
		}

		return mapped;
	}

	function ltxeApplyClear( targets, clearProps ) {
		if ( ! clearProps ) {
			return;
		}
		var list = ltxeTargets( targets );
		var i;
		for ( i = 0; i < list.length; i++ ) {
			if ( list[ i ] && list[ i ].style ) {
				if ( 'all' === clearProps ) {
					list[ i ].style.cssText = '';
				}
			}
		}
	}

	/**
	 * Resolve GSAP-style timeline positions for anime.js (seconds → ms).
	 *
	 * Supports label names, label+=seconds, +=seconds, -=seconds, and numeric seconds.
	 *
	 * @param {string|number|undefined} offset  GSAP position parameter.
	 * @param {Object<string,number>} labels    Label name → start time (ms).
	 * @param {number} endTime                    Current timeline end (ms).
	 * @return {number} Start time in ms.
	 */
	function ltxeResolveTimelineOffset( offset, labels, endTime ) {
		var match;
		var labelName;
		var seconds;

		if ( 'undefined' === typeof offset || null === offset ) {
			return endTime;
		}

		if ( 'number' === typeof offset ) {
			return offset * 1000;
		}

		if ( 'string' !== typeof offset ) {
			return endTime;
		}

		match = offset.match( /^([A-Za-z0-9_-]+)\+=([0-9.]+)$/ );
		if ( match ) {
			labelName = match[ 1 ];
			seconds   = parseFloat( match[ 2 ] );
			if ( ! Object.prototype.hasOwnProperty.call( labels, labelName ) ) {
				labels[ labelName ] = 0;
			}
			return labels[ labelName ] + ( seconds * 1000 );
		}

		match = offset.match( /^([A-Za-z0-9_-]+)-=([0-9.]+)$/ );
		if ( match ) {
			labelName = match[ 1 ];
			seconds   = parseFloat( match[ 2 ] );
			if ( ! Object.prototype.hasOwnProperty.call( labels, labelName ) ) {
				labels[ labelName ] = 0;
			}
			return labels[ labelName ] - ( seconds * 1000 );
		}

		if ( Object.prototype.hasOwnProperty.call( labels, offset ) ) {
			return labels[ offset ];
		}

		if ( 0 === offset.indexOf( '-=' ) ) {
			return endTime - ( parseFloat( offset.slice( 2 ) ) * 1000 );
		}

		if ( 0 === offset.indexOf( '+=' ) ) {
			return endTime + ( parseFloat( offset.slice( 2 ) ) * 1000 );
		}

		if ( /^[A-Za-z0-9_-]+$/.test( offset ) ) {
			labels[ offset ] = 0;
			return 0;
		}

		return endTime;
	}

	/**
	 * @param {Object} tl Anime timeline instance.
	 * @param {number} startMs Start offset in ms.
	 * @param {number} durationMs Animation duration in ms.
	 * @param {number} endTime Current end time ref.
	 */
	function ltxeTrackTimelineEnd( startMs, durationMs, endTime ) {
		return Math.max( endTime, startMs + durationMs );
	}

	function ltxeAnimeFromEndValue( key, fromValue ) {
		if ( 'opacity' === key ) {
			return [ fromValue, 1 ];
		}
		if ( 'scale' === key ) {
			return [ fromValue, 1 ];
		}
		return [ fromValue, 0 ];
	}

	window.ltxeAnimate = {
		ease: ltxeEase,

		set: function( targets, props ) {
			var mapped = ltxeMapProps( props );
			if ( mapped.clearProps ) {
				ltxeApplyClear( targets, mapped.clearProps );
				delete mapped.clearProps;
			}
			if ( Object.keys( mapped ).length ) {
				ltxeAnimeSet( targets, mapped );
			}
		},

		to: function( targets, duration, props ) {
			var mapped = ltxeMapProps( props );
			var opts = {
				targets: ltxeTargets( targets ),
				duration: ( duration || 0 ) * 1000,
				easing: mapped.easing || ltxeEase,
			};
			delete mapped.easing;
			if ( props && props.onComplete ) {
				opts.complete = props.onComplete;
				delete mapped.onComplete;
			}
			if ( mapped.clearProps ) {
				var clear = mapped.clearProps;
				delete mapped.clearProps;
				opts.complete = function() {
					ltxeApplyClear( targets, clear );
					if ( props && props.onComplete ) {
						props.onComplete();
					}
				};
			}
			Object.assign( opts, mapped );
			return ltxeAnimeRun( opts );
		},

		from: function( targets, duration, props ) {
			var mapped = ltxeMapProps( props );
			var nodes = ltxeTargets( targets );
			var params = {
				duration: ( duration || 0 ) * 1000,
				ease: mapped.easing || ltxeEase,
			};
			var clear = mapped.clearProps;
			var key;

			if ( ! nodes.length ) {
				return null;
			}

			delete mapped.easing;
			delete mapped.clearProps;

			for ( key in mapped ) {
				if ( ! Object.prototype.hasOwnProperty.call( mapped, key ) ) {
					continue;
				}
				params[ key ] = ltxeAnimeFromEndValue( key, mapped[ key ] );
			}

			if ( props && props.onComplete ) {
				params.onComplete = props.onComplete;
			}

			if ( clear ) {
				params.onComplete = function() {
					ltxeApplyClear( targets, clear );
					if ( props && props.onComplete ) {
						props.onComplete();
					}
				};
			}

			if ( ltxeAnimeIsV4() ) {
				return window.anime.animate( nodes, params );
			}

			return ltxeAnimeRun( Object.assign( { targets: nodes }, params ) );
		},

		fromTo: function( targets, duration, fromProps, toProps ) {
			var from = ltxeMapProps( fromProps );
			var to = ltxeMapProps( toProps );
			var complete = toProps && toProps.onComplete ? toProps.onComplete : null;
			delete to.onComplete;
			return ltxeAnimeRun( {
				targets: ltxeTargets( targets ),
				duration: ( duration || 0 ) * 1000,
				easing: to.easing || from.easing || ltxeEase,
				...from,
				...to,
				complete: complete,
			} );
		},

		killTweensOf: function( targets ) {
			if ( window.anime && 'function' === typeof window.anime.remove ) {
				window.anime.remove( ltxeTargets( targets ) );
			}
		},

		timeline: function( options ) {
			var labels = {};
			var endTime = 0;
			var tl = ltxeAnimeTimeline( {
				onComplete: options && options.onComplete ? options.onComplete : undefined,
			} );
			var animeAdd = tl.add.bind( tl );

			tl.add = function( arg1, arg2 ) {
				var startMs;
				var durationMs;

				if ( 'string' === typeof arg1 && ( 'undefined' === typeof arg2 || 'number' === typeof arg2 || 'string' === typeof arg2 ) ) {
					startMs = ltxeResolveTimelineOffset( arg2, labels, endTime );
					labels[ arg1 ] = startMs;
					return tl;
				}

				startMs = ltxeResolveTimelineOffset( arg2, labels, endTime );
				durationMs = arg1 && arg1.duration ? arg1.duration : 0;
				endTime = ltxeTrackTimelineEnd( startMs, durationMs, endTime );
				animeAdd( arg1, startMs );
				return tl;
			};

			tl.to = function( targets, duration, props, offset ) {
				var mapped = ltxeMapProps( props );
				var addOpts = {
					duration: ( duration || 0 ) * 1000,
					ease: mapped.easing || ltxeEase,
				};
				var startMs;
				var clear = mapped.clearProps;
				delete mapped.easing;
				delete mapped.clearProps;
				Object.assign( addOpts, mapped );
				if ( clear ) {
					addOpts.onComplete = function() {
						ltxeApplyClear( targets, clear );
					};
				}
				startMs = ltxeResolveTimelineOffset( offset, labels, endTime );
				endTime = ltxeTrackTimelineEnd( startMs, addOpts.duration, endTime );
				animeAdd( ltxeTargets( targets ), addOpts, startMs );
				return tl;
			};

			tl.set = function( targets, props, offset ) {
				window.ltxeAnimate.set( targets, props );
				return tl;
			};

			tl.staggerFromTo = function( targets, duration, fromProps, toProps, stagger, offset ) {
				var from = ltxeMapProps( fromProps );
				var to = ltxeMapProps( toProps );
				var addOpts = {
					duration: ( duration || 0 ) * 1000,
					delay: ltxeAnimeStagger( ( stagger || 0 ) * 1000 ),
					ease: to.easing || from.easing || ltxeEase,
				};
				var startMs;
				Object.assign( addOpts, from, to );
				delete addOpts.easing;
				startMs = ltxeResolveTimelineOffset( offset, labels, endTime );
				endTime = ltxeTrackTimelineEnd( startMs, addOpts.duration, endTime );
				animeAdd( ltxeTargets( targets ), addOpts, startMs );
				return tl;
			};

			tl.kill = function() {
				this.pause();
			};

			return tl;
		},
	};

	// Back-compat aliases used throughout legacy Elementor Extras scripts.
	window.TweenMax = window.ltxeAnimate;
	window.TimelineMax = window.ltxeAnimate.timeline;
	window.TimelineLite = window.ltxeAnimate.timeline;
	window.Power0 = { easeInOut: ltxeEase };
	window.Power1 = { easeIn: ltxeEase, easeOut: ltxeEase };
	window.Power4 = { easeInOut: ltxeEase, easeIn: ltxeEase, easeOut: ltxeEase };
	window.Elastic = { easeInOut: ltxeEase, easeIn: ltxeEase, easeOut: ltxeEase };
	window.Bounce = { easeInOut: ltxeEase, easeIn: ltxeEase, easeOut: ltxeEase };
	window.Back = { easeOut: { config: function() { return ltxeEase; } } };
	window.SlowMo = { config: function() { return ltxeEase; } };
	window.SteppedEase = { config: function() { return ltxeEase; } };
	window.CustomEase = {
		create: function( name, path ) {
			return ltxeEase;
		},
	};

}( window ) );
