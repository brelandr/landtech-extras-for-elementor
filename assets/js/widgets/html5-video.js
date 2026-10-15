( function ( $ ) {
	'use strict';

	function initHtml5Video( $scope ) {
		var settings = window.ee.getElementSettings( $scope );
		var $video = $scope.find( '.ee-video-player' );
		var videoPlayerArgs = {
			playOnViewport: 'yes' === settings.video_play_viewport,
			stopOffViewport: 'yes' === settings.video_stop_viewport,
			endAtLastFrame: 'yes' === settings.video_end_at_last_frame,
			restartOnPause: 'yes' === settings.video_restart_on_pause,
			stopOthersOnPlay: 'yes' === settings.video_stop_others,
			disableFsRightClick: 'yes' === settings.video_fs_no_rightclick,
		};

		if ( 'undefined' !== typeof settings.video_speed ) {
			videoPlayerArgs.speed = settings.video_speed.size;
		}

		if ( ! $video.length ) {
			return;
		}

		if ( 'undefined' !== typeof settings.video_volume ) {
			videoPlayerArgs.volume = settings.video_volume.size;
		}

		$video.videoPlayer( videoPlayerArgs );

		var $nativeVideo = $video.find( 'video.ee-video-player__source' ).get( 0 );
		if ( $nativeVideo ) {
			var playbackRate = parseFloat( $nativeVideo.getAttribute( 'data-playback-rate' ) );
			if ( ! isNaN( playbackRate ) && playbackRate > 0 ) {
				$nativeVideo.playbackRate = playbackRate;
			}
			if ( 'undefined' !== typeof videoPlayerArgs.speed && ! isNaN( parseFloat( videoPlayerArgs.speed ) ) ) {
				$nativeVideo.playbackRate = parseFloat( videoPlayerArgs.speed );
			}
		}
	}

	window.ee.initHtml5Video = initHtml5Video;

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/html5-video.default', initHtml5Video );
	} );
}( jQuery ) );
