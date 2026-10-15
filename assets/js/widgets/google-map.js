( function ( $ ) {
	'use strict';

	var ee = {
		GoogleMap: {},
		getElementSettings: function ( $scope ) {
			return window.ee.getElementSettings( $scope );
		},
		applyReducedMotionClass: function ( $scope ) {
			window.ee.applyReducedMotionClass( $scope );
		}
	};

	var LandTechExtrasUtils = {
		findObjectByKey: function ( array, key, value ) {
			for ( var i = 0; i < array.length; i++ ) {
				if ( array[ i ][ key ] === value ) {
					return array[ i ];
				}
			}
			return null;
		}
	};

	function initGoogleMap( $scope ) {

			ee.GoogleMap.elementSettings = ee.getElementSettings( $scope );
			ee.applyReducedMotionClass( $scope );

			var $map 		= $scope.find( '.ee-google-map' );

			// Bail out early
			if ( ! $map.length ) return;

			var $pins 		= $map.find( '.ee-google-map__pin' ),
				$navigation = $scope.find( '.ee-google-map__navigation' ),
				settings 	= ee.GoogleMap.elementSettings;

			if ( ! ee.GoogleMap.initLeaflet ) {
				ee.GoogleMap.initLeaflet = function( $mapEl, $pinsEl, $navigationEl, mapSettings ) {
				if ( 'undefined' === typeof window.L ) {
					return;
				}

				var iconBase = ( window.landtechExtrasFrontendConfig && window.landtechExtrasFrontendConfig.urls && window.landtechExtrasFrontendConfig.urls.leafletIcons )
					? window.landtechExtrasFrontendConfig.urls.leafletIcons
					: '';

				if ( iconBase ) {
					delete L.Icon.Default.prototype._getIconUrl;
					L.Icon.Default.mergeOptions( {
						iconRetinaUrl: iconBase + 'marker-icon-2x.png',
						iconUrl: iconBase + 'marker-icon.png',
						shadowUrl: iconBase + 'marker-shadow.png',
					} );
				}

				var centerLat = parseFloat( $mapEl.data( 'lat' ) ) || 48.8583736;
				var centerLng = parseFloat( $mapEl.data( 'lng' ) ) || 2.2922873;
				var mapZoom = mapSettings.zoom && mapSettings.zoom.size ? parseInt( mapSettings.zoom.size, 10 ) : 10;
				var leafletMap = L.map( $mapEl[0], {
					scrollWheelZoom: 'yes' === mapSettings.scrollwheel,
				} );

				L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					maxZoom: 19,
					attribution: '&copy; OpenStreetMap contributors',
				} ).addTo( leafletMap );

				var leafletMarkers = [];

				$pinsEl.each( function() {
					var $pin = $( this );
					var pinLat = parseFloat( $pin.data( 'lat' ) );
					var pinLng = parseFloat( $pin.data( 'lng' ) );

					if ( isNaN( pinLat ) || isNaN( pinLng ) ) {
						return;
					}

					var marker = L.marker( [ pinLat, pinLng ] ).addTo( leafletMap );
					leafletMarkers.push( marker );

					if ( 'yes' === mapSettings.popups ) {
						var popupHtml = $pin.html();
						if ( popupHtml && $.trim( popupHtml ) ) {
							marker.bindPopup( popupHtml );
						}
					}
				} );

				if ( 'yes' === mapSettings.fit && leafletMarkers.length ) {
					leafletMap.fitBounds( L.featureGroup( leafletMarkers ).getBounds().pad( 0.2 ) );
				} else {
					leafletMap.setView( [ centerLat, centerLng ], mapZoom );
				}

				if ( $navigationEl.length ) {
					$navigationEl.attr( 'role', 'navigation' );
					if ( ! $navigationEl.attr( 'aria-label' ) ) {
						$navigationEl.attr( 'aria-label', 'Map locations' );
					}

					$navigationEl.on( 'click', '.ee-google-map__navigation__link', function( event ) {
						event.preventDefault();
						var pinId = $( this ).closest( '.ee-google-map__navigation__item' ).data( 'id' );

						if ( ! pinId ) {
							if ( leafletMarkers.length ) {
								leafletMap.fitBounds( L.featureGroup( leafletMarkers ).getBounds().pad( 0.2 ) );
							}
							return;
						}

						$pinsEl.each( function( index ) {
							if ( $( this ).data( 'id' ) === pinId && leafletMarkers[ index ] ) {
								leafletMap.setView( leafletMarkers[ index ].getLatLng(), mapSettings.navigation_zoom ? mapSettings.navigation_zoom.size : 16 );
								leafletMarkers[ index ].openPopup();
							}
						} );
					} );
				}

				$mapEl._resize( function() {
					leafletMap.invalidateSize();
				} );
				};
			}

			if ( 'openstreetmap' === settings.map_provider ) {
				ee.GoogleMap.initLeaflet( $map, $pins, $navigation, settings );
				return;
			}

			var gmapArgs 	= {
					center 					: [ 48.8583736, 2.2922873 ],

					mapTypeId 				: google.maps.MapTypeId[ settings.map_type ],
					scrollwheel 			: 'yes' === settings.scrollwheel,
					clickableIcons 			: 'yes' === settings.clickable_icons,
					disableDoubleClickZoom 	: 'yes' !== settings.doubleclick_zoom,
					keyboardShortcuts 		: 'yes' === settings.keyboard_shortcuts,
					draggable 				: ( ! elementorFrontend.isEditMode() && 'yes' === settings.draggable ),

					fullscreenControl 		: 'yes' === settings.fullscreen_control,
					mapTypeControl 			: 'yes' === settings.map_type_control,
					rotateControl 			: 'yes' === settings.rotate_control,
					scaleControl 			: 'yes' === settings.scale_control,
					streetViewControl 		: 'yes' === settings.streetview_control,
					zoomControl 			: 'yes' === settings.zoom_control,
				},
				polygonArgs = {
					default : {
						strokeColor 	: ( settings.polygon_stroke_color ) ? settings.polygon_stroke_color : '#FF0000',
						strokeWeight 	: ( settings.polygon_stroke_weight ) ? settings.polygon_stroke_weight.size : 2,
						strokeOpacity 	: ( settings.polygon_stroke_opacity ) ? settings.polygon_stroke_opacity.size : 0.8,
						fillColor 		: ( settings.polygon_fill_color ) ? settings.polygon_fill_color : '#FF0000',
						fillOpacity 	: ( settings.polygon_fill_opacity ) ? settings.polygon_fill_opacity.size : 0.35,
					},
					hover : {
						strokeColor 	: ( settings.polygon_stroke_color_hover ) ? settings.polygon_stroke_color_hover : '#FF0000',
						strokeWeight 	: ( settings.polygon_stroke_weight_hover ) ? settings.polygon_stroke_weight_hover.size : 2,
						strokeOpacity 	: ( settings.polygon_stroke_opacity_hover ) ? settings.polygon_stroke_opacity_hover.size : 0.8,
						fillColor 		: ( settings.polygon_fill_color_hover ) ? settings.polygon_fill_color_hover : '#FF0000',
						fillOpacity 	: ( settings.polygon_fill_opacity_hover ) ? settings.polygon_fill_opacity_hover.size : 0.35,
					}
				},
				markers 	= [],
				paths 		= [],
				instance 	= null;

			ee.GoogleMap.init = function() {

				var mapStyle = settings.map_style_json;

				if ( 'api' === settings.map_style_type && settings.map_style_api ) {
					var jsonParse = JSON.parse( settings.map_style_api );

					if ( jsonParse ) {
						mapStyle = JSON.parse( settings.map_style_api ).json;
					}
				}

				if ( '' !== $.trim( mapStyle ) && undefined !== mapStyle ) {
					gmapArgs.styles = ee.GoogleMap.parseStyles( mapStyle );
				}

				if ( 'yes' !== settings.fit ) {
					if ( settings.zoom ) {
						if ( settings.zoom.size ) {
							gmapArgs.zoom = settings.zoom.size;
						}
					}

					if ( $map.data('lat') && $map.data('lng') ) {
						gmapArgs.center = [ $map.data('lat'), $map.data('lng') ];
					}
				}

				instance = $map.gmap3( gmapArgs );

				ee.GoogleMap.addPins();

				if ( 'yes' === settings.popups )
					ee.GoogleMap.addInfoWindows();

				if ( 'yes' === settings.route && $pins.length > 1 )
					ee.GoogleMap.addRoute();

				if ( 'yes' === settings.polygon )
					ee.GoogleMap.addPolygon();

				if ( 'yes' === settings.navigation )
					ee.GoogleMap.navigation();

				// Init events
				ee.GoogleMap.events();

				// Center to fit or custom
				ee.GoogleMap.center();
			};

			ee.GoogleMap.events = function() {
				$map._resize( ee.GoogleMap.onResize );
			};

			ee.GoogleMap.onResize = function() {
				ee.GoogleMap.center();
			};

			ee.GoogleMap.center = function() {
				if ( 'yes' === settings.fit ) {
					instance.wait(2000).fit();
				}
			};

			ee.GoogleMap.parseStyles = function( style ) {

				try {
					var json = JSON.parse( style );

					if ( json && typeof json === "object") { return json; }
				}
				catch ( e ) {
					alert( 'Invalid JSON' );
				}

				return false;
			};

			ee.GoogleMap.addPolygon = function() {

				if ( $pins.length <= 1 )
					return;

				instance
					.polygon( {
						strokeColor 	: polygonArgs.default.strokeColor,
						strokeWeight 	: polygonArgs.default.strokeWeight,
						strokeOpacity 	: polygonArgs.default.strokeOpacity,
						fillColor 		: polygonArgs.default.fillColor,
						fillOpacity 	: polygonArgs.default.fillOpacity,
						paths 			: paths,
					} )
					.on({
						mouseover: function ( polygon, event ) {
							polygon.setOptions( {
								strokeColor 	: polygonArgs.hover.strokeColor,
								strokeWeight 	: polygonArgs.hover.strokeWeight,
								strokeOpacity 	: polygonArgs.hover.strokeOpacity,
								fillColor 		: polygonArgs.hover.fillColor,
								fillOpacity 	: polygonArgs.hover.fillOpacity,
							} );
						},
						mouseout: function ( polygon, event ) {
							polygon.setOptions( {
								strokeColor 	: polygonArgs.default.strokeColor,
								strokeWeight 	: polygonArgs.default.strokeWeight,
								strokeOpacity 	: polygonArgs.default.strokeOpacity,
								fillColor 		: polygonArgs.default.fillColor,
								fillOpacity 	: polygonArgs.default.fillOpacity,
							} );
						}
					});
			};

			ee.GoogleMap.addPins = function() {
				if ( ! $pins.length )
					return;

				$pins.each( function() {
					var marker = {},
						pin = {
							id 			: $(this).data('id'),
							input 		: $(this).data('input'),
							lat 		: $(this).data('lat'),
							lng 		: $(this).data('lng'),
							trigger 	: $(this).data('trigger'),
							icon 		: $(this).data('icon'),
							content 	: $(this).html(),
						};

					if ( ! pin.lat || ! pin.lng ) {
						return;
					}

					marker.id 		= pin.id;
					marker.trigger 	= pin.trigger;
					marker.position = [ pin.lat, pin.lng ];

					paths.push( marker.position );

					if ( pin.icon ) {
						var iconSize = ( settings.pin_size ) ? settings.pin_size.size : 50,
							iconPosition = ee.GoogleMap.getIconPosition( iconSize );

						marker.icon = {
							url 		: pin.icon,
							scaledSize	: new google.maps.Size( iconSize, iconSize ),
							origin 		: new google.maps.Point( 0, 0 ),
							anchor 		: new google.maps.Point( iconPosition[0], iconPosition[1] ),
						};
					}

					if ( pin.content && settings.popups )
						marker.content = pin.content;

					markers.push( marker );
				});

				instance.marker( markers );
			};

			ee.GoogleMap.getIconPosition = function( size ) {
				var horiz = 25,
					vert = 50;

				switch ( settings.pin_position_horizontal ) {
					case 'left' :
						horiz = size;
						break;
					case 'center' :
						horiz = size / 2;
						break;
					case 'right' :
						horiz = 0;
						break;
					default :
						horiz = size / 2;
				}

				switch ( settings.pin_position_vertical ) {
					case 'top' :
						vert = size;
						break;
					case 'middle' :
						vert = size / 2;
						break;
					case 'bottom' :
						vert = 0;
						break;
					default :
						vert = size;
				}

				return [ horiz, vert ];
			};

			ee.GoogleMap.addInfoWindows = function() {
				if ( ! $pins.length )
					return;

				instance
					.infowindow( markers )
					.then( function( infowindow ) {

						var map = this.get(0),
							marker = this.get(1);

						marker.forEach( function( pin, index ) {

							if ( 'auto' === pin.trigger ) {
								infowindow[ index ].open( map, pin );

								pin.addListener( 'click', function() {
									infowindow[ index ].open( map, pin );
								});
							} else if ( 'mouseover' === pin.trigger ) {
								pin.addListener( 'mouseover', function() {
									infowindow[ index ].open( map, pin );
								});
								pin.addListener( 'mouseout', function() {
									infowindow[ index ].close( map, pin );
								});
							} else if ( 'click' === pin.trigger ) {
								pin.addListener( 'click', function() {
									infowindow[ index ].open( map, pin );
								});
							}
						})
					});
			};

			ee.GoogleMap.addRoute = function() {

				if ( $pins.length <= 1 )
					return;

				var points = markers.slice(),
					origin = ee.GoogleMap.getMarkerDataForRoutes( markers[0] ),
					destination = ee.GoogleMap.getMarkerDataForRoutes( markers[ markers.length - 1 ] ),
					waypoints = ( markers.length >= 3 ) ? ee.GoogleMap.getWaypoints( points ) : null; // Waypoints make sense for more than 2 markers

				instance
					.route({
						origin : origin,
						destination : destination,
						waypoints : waypoints,
						travelMode : google.maps.DirectionsTravelMode[settings.route_mode],
					})
					.directionsrenderer( function( results ) {
						if ( results ) {
							return {
								suppressMarkers: 'yes' !== settings.route_markers,
								directions: results,
							}
						}
					});
			};

			ee.GoogleMap.getWaypoints = function( points ) {
				var waypoints = [];

				// Remove first and last markers
				points.shift();
				points.pop();

				points.forEach( function( point, index ) {
					waypoints.push( {
						location : ee.GoogleMap.getMarkerDataForRoutes( point ),
						stopover : true,
					} );
				} );

				return waypoints;
			};

			ee.GoogleMap.getMarkerDataForRoutes = function( marker ) {
				return new google.maps.LatLng( marker.position[0], marker.position[1] );
			};

			ee.GoogleMap.navigation = function() {
				var $items 	= $navigation.find( '.ee-google-map__navigation__item' ),
					$all 	= $items.filter( '.ee-google-map__navigation__item--all' );

				$all.addClass( 'ee--is-active' );

				$items.on( 'click', function( e ) {
					e.preventDefault();
					e.stopPropagation();

					$items.removeClass( 'ee--is-active' );
					$(this).addClass( 'ee--is-active' );

					var marker = LandTechExtrasUtils.findObjectByKey( markers, 'id', $(this).data('id') );

					if ( marker ) {
						var navZoom = settings.navigation_zoom ? settings.navigation_zoom.size : 18;

						instance.get(0).setCenter( new google.maps.LatLng( marker.position[0], marker.position[1] ) );
						instance.get(0).setZoom( navZoom );
					} else {
						instance.fit();
					}
				});
			};

			ee.GoogleMap.init();
	}


	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-google-map.default', function ( $scope ) {
			if ( $scope.data( 'ltxeOnce_ee-google-map.default' ) ) {
				return;
			}
			$scope.data( 'ltxeOnce_ee-google-map.default', true );
			initGoogleMap( $scope );
		} );
	} );
}( jQuery ) );
