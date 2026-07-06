/**
 * Elementor editor only: initialise Isotope masonry for galleries with data-ee-editor-masonry.
 */
(function ($) {
	'use strict';

	$(function () {
		$('.ee-gallery[data-ee-editor-masonry]').each(function () {
			var $gallery = $(this);
			var $scope = $gallery.closest('[data-id]');

			if (!$scope.length || $gallery.closest($scope).length < 1) {
				return;
			}

			var isotopeArgs = {
				itemSelector: '.ee-gallery__item',
				percentPosition: true,
				hiddenStyle: {
					opacity: 0,
				},
			};

			var $isotope = $gallery.isotope(isotopeArgs);

			$isotope.masonry();

			$gallery.find('.ee-gallery__item')._resize(function () {
				$isotope.masonry();
			});

			$(window).on('resize.eeGalleryMasonryEditor', function () {
				$isotope.masonry();
			});
		});
	});
})(jQuery);
