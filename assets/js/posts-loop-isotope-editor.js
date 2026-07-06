/**
 * Elementor editor only: Isotope / filtery on .ee-loop; layout from data-ee-editor-isotope-layout.
 */
(function ($) {
	'use strict';

	function runAfterImagesReady($loop, cb) {
		if ($.fn.imagesLoaded) {
			$loop.imagesLoaded(cb);
			return;
		}
		cb();
	}

	$(function () {
		$('.ee-loop[data-ee-editor-isotope-layout]').each(function () {
			var $loop = $(this);
			var $scope = $loop.closest('[data-id]');

			if (!$scope.length || $loop.closest($scope).length < 1) {
				return;
			}

			var scopeId = $scope.data('id') || '0';

			var $filters = $loop.siblings('.ee-filters');
			var $triggers = $filters.find('[data-filter]');
			var layoutRaw = ($loop.attr('data-ee-editor-isotope-layout') || 'default').toString();

			var isoMode = layoutRaw;
			if ('metro' === layoutRaw) {
				isoMode = 'fitRows';
			} else if ('packery' === layoutRaw) {
				isoMode = 'packery';
			}

			var isotopeArgs = {
				itemSelector: '.ee-loop__item',
				layoutMode: isoMode,
				percentPosition: true,
				hiddenStyle: {
					opacity: 0,
				},
			};

			if ('masonry' === layoutRaw) {
				isotopeArgs.masonry = {
					columnWidth: '.ee-grid__item--sizer',
				};
			}
			if ('packery' === layoutRaw) {
				isotopeArgs.packery = {
					columnWidth: '.ee-grid__item--sizer',
				};
			}

			var filteryArgs = {
				wrapper: $loop,
				filterables: '.ee-loop__item',
				activeFilterClass: 'ee--active',
			};

			runAfterImagesReady($loop, function () {
				if (layoutRaw !== 'default') {
					$loop.isotope(isotopeArgs);

					$loop.find('.ee-grid__item:last-child')._resize(function () {
						$loop.isotope('layout');
					});

					var edIsoTimer = null;
					var scheduleEditorIsoRelayout = function () {
						window.clearTimeout(edIsoTimer);
						edIsoTimer = window.setTimeout(function () {
							if ($loop.data('isotope')) {
								$loop.isotope('layout');
							}
						}, 120);
					};

					$(window).on('resize.ltxePostsIsoEd.' + scopeId, scheduleEditorIsoRelayout);

					if ('undefined' !== typeof window.ResizeObserver && $loop[0]) {
						var ltxeEditorPostsRo = new window.ResizeObserver(scheduleEditorIsoRelayout);
						ltxeEditorPostsRo.observe($loop[0]);
					}

					if ($triggers.length) {
						var $defaultTrigger = $triggers.filter('.ee--active');

						if ($defaultTrigger.length) {
							var defaultFilter = $defaultTrigger.data('filter');

							if (typeof defaultFilter !== 'undefined') {
								$loop.isotope({filter: defaultFilter});
							}
						}

						$triggers.on('click', function () {
							var filterValue = $(this).data('filter');

							$loop.isotope({filter: filterValue});
							$triggers.removeClass('ee--active');
							$(this).addClass('ee--active');
						});
					}
				} else if ($triggers.length && $.fn.filtery) {
					$filters.filtery(filteryArgs);
				}
			});
		});
	});
})(jQuery);
