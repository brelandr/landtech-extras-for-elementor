( function( $, elementor ) {
	'use strict';

	if ( 'undefined' === typeof landtechExtrasWidgetHealth ) {
		return;
	}

	var config = landtechExtrasWidgetHealth;
	var dismissed = config.dismissed || [];

	function isDismissed( key ) {
		return dismissed.indexOf( key ) !== -1;
	}

	function dismissNotice( key ) {
		if ( isDismissed( key ) ) {
			return;
		}

		dismissed.push( key );

		$.post( config.ajaxUrl, {
			action: 'landtech_extras_dismiss_notice',
			option_name: key,
			dismissible_length: 'forever',
			nonce: config.dismissNonce,
		} );
	}

	function showHealthNotice( dismissKey, message ) {
		if ( isDismissed( dismissKey ) || ! message ) {
			return;
		}

		if ( elementor.notifications && 'function' === typeof elementor.notifications.showToast ) {
			elementor.notifications.showToast( {
				message: message,
				buttons: [
					{
						name: 'dismiss',
						text: config.strings.dismiss,
						callback: function() {
							dismissNotice( dismissKey );
						},
					},
				],
			} );
			return;
		}

		window.alert( message );
	}

	function getSettings( model ) {
		return model && model.get ? model.get( 'settings' ) : null;
	}

	function galleryIsEmpty( settings ) {
		if ( ! settings ) {
			return true;
		}

		var galleryType = settings.get( 'gallery_type' ) || 'manual';

		if ( 'manual' === galleryType ) {
			var items = settings.get( 'gallery' ) || [];
			return ! items.length;
		}

		if ( 'wordpress' === galleryType || 'acf_gallery' === galleryType ) {
			var wpGallery = settings.get( 'wp_gallery' ) || [];
			return ! wpGallery.length;
		}

		return false;
	}

	function inlineSvgIsEmpty( settings ) {
		if ( ! settings ) {
			return true;
		}

		var source = settings.get( 'svg_source' ) || 'media';

		if ( 'url' === source ) {
			var customUrl = settings.get( 'svg_custom_url' );
			return ! customUrl || ! customUrl.url;
		}

		var svg = settings.get( 'svg' );
		return ! svg || ! svg.url;
	}

	function audioPlaylistIsEmpty( settings ) {
		if ( ! settings ) {
			return true;
		}

		var playlist = settings.get( 'playlist' ) || [];
		return ! playlist.length;
	}

	function tableIsEmpty( settings ) {
		if ( ! settings ) {
			return true;
		}

		var rows = settings.get( 'rows' ) || [];
		return ! rows.length;
	}

	function registerWidgetHealth( widgetName, callback ) {
		elementor.hooks.addAction( 'panel/open_editor/widget/' + widgetName, function( panel, model ) {
			callback( model );
		} );
	}

	$( window ).on( 'elementor:init', function() {
		registerWidgetHealth( 'ee-google-map', function( model ) {
			var settings = getSettings( model );

			if ( ! settings ) {
				return;
			}

			var provider = settings.get( 'map_provider' ) || 'google';

			if ( 'google' === provider && ! config.googleMapsApiKeySet ) {
				showHealthNotice( 'ltxe-editor-health-google-map', config.strings.googleMapsMissingKey );
			}
		} );

		registerWidgetHealth( 'gallery-extra', function( model ) {
			if ( galleryIsEmpty( getSettings( model ) ) ) {
				showHealthNotice( 'ltxe-editor-health-gallery-extra', config.strings.galleryEmpty );
			}
		} );

		registerWidgetHealth( 'ee-inline-svg', function( model ) {
			if ( inlineSvgIsEmpty( getSettings( model ) ) ) {
				showHealthNotice( 'ltxe-editor-health-inline-svg', config.strings.inlineSvgMissing );
			}
		} );

		registerWidgetHealth( 'ee-audio-player', function( model ) {
			if ( audioPlaylistIsEmpty( getSettings( model ) ) ) {
				showHealthNotice( 'ltxe-editor-health-audio-player', config.strings.audioPlaylistEmpty );
			}
		} );

		registerWidgetHealth( 'table', function( model ) {
			if ( tableIsEmpty( getSettings( model ) ) ) {
				showHealthNotice( 'ltxe-editor-health-table', config.strings.tableEmpty );
			}
		} );
	} );

}( jQuery, window.elementor ) );
