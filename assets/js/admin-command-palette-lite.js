( function () {
	'use strict';

	var cfg = window.ltxCommandPaletteLite || {};
	var i18n = cfg.i18n || {};

	if ( ! window.wp || ! wp.commands || ! wp.commands.registerCommand ) {
		return;
	}

	if ( cfg.settingsUrl ) {
		wp.commands.registerCommand( {
			name: 'landtech-extras/open-settings',
			label: i18n.openSettings || 'LandTech: Open LandTech Extras settings',
			callback: function () {
				window.location.href = cfg.settingsUrl;
			}
		} );
	}

	if ( cfg.docsUrl ) {
		wp.commands.registerCommand( {
			name: 'landtech-extras/open-docs',
			label: i18n.openDocs || 'LandTech: Open plugin documentation',
			callback: function () {
				window.open( cfg.docsUrl, '_blank', 'noopener,noreferrer' );
			}
		} );
	}
}() );
