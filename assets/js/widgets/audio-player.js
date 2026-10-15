( function ( $ ) {
	'use strict';

	function initAudioPlayer( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		var $player = $scope.find( '.ee-audio-player' );

		window.ee.applyReducedMotionClass( $scope );

		$player.audioPlayer( {
			restartOnPause: 'yes' === settings.restart_on_pause,
			loopPlaylist: 'yes' === settings.loop_playlist,
			autoplay: 'yes' === settings.autoplay && ! elementorFrontend.isEditMode() && ! window.ee.prefersReducedMotion(),
			volume: settings.volume.size,
		} );

		window.ee.makeFocusableControl( $player.find( '.ee-player__controls__play' ), 'Play' );
		window.ee.makeFocusableControl( $player.find( '.ee-player__controls__previous' ), 'Previous track' );
		window.ee.makeFocusableControl( $player.find( '.ee-player__controls__next' ), 'Next track' );
		window.ee.makeFocusableControl( $player.find( '.ee-player__controls__rewind' ), 'Restart' );
		window.ee.makeFocusableControl( $player.find( '.ee-player__controls__volume' ), 'Volume' );

		if ( 'yes' === settings.waveform_skin && 'undefined' !== typeof window.WaveSurfer ) {
			var $wave = $player.find( '.ee-audio-player__waveform' );
			if ( ! $wave.length ) {
				$wave = $( '<div class="ee-audio-player__waveform" aria-hidden="true"></div>' );
				$player.prepend( $wave );
			}
			var audioSrc = $player.find( 'audio source' ).first().attr( 'src' );
			if ( audioSrc && $wave.length ) {
				window.WaveSurfer.create( {
					container: $wave.get( 0 ),
					waveColor: '#cbd5e1',
					progressColor: '#0f172a',
					height: 48,
					url: audioSrc,
				} );
			}
		}
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/ee-audio-player.default', initAudioPlayer );
	} );
}( jQuery ) );
