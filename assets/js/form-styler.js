/**
 * Form styler — focus classes, preview validation, reduced-motion submit cue.
 */
(function ($) {
	'use strict';

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	}

	function markFilled($field) {
		var $input = $field.find('input, textarea, select').first();
		$field.toggleClass('has-value', !!($input.val() && String($input.val()).trim()));
	}

	function bind($root) {
		if (!$root.length || $root.data('ltxeFormStyler')) {
			return;
		}
		$root.data('ltxeFormStyler', true);

		$root.on('focusin.ltxeFs', 'input, textarea, select', function () {
			$(this).closest('p, .wpforms-field, .ltxe-form-styler__field').addClass('is-focused');
		});
		$root.on('focusout.ltxeFs', 'input, textarea, select', function () {
			var $field = $(this).closest('p, .wpforms-field, .ltxe-form-styler__field');
			$field.removeClass('is-focused');
			markFilled($field);
		});
		$root.find('p, .wpforms-field, .ltxe-form-styler__field').each(function () {
			markFilled($(this));
		});

		$root.on('submit.ltxeFs', 'form.ltxe-form-styler__preview', function (e) {
			e.preventDefault();
			var $form = $(this);
			var $error = $form.find('.ltxe-form-styler__error');
			var $success = $form.find('.ltxe-form-styler__success');
			var valid = true;
			$form.find('[required]').each(function () {
				if (!this.checkValidity() || !String($(this).val() || '').trim()) {
					valid = false;
				}
			});
			if (!valid) {
				$error.prop('hidden', false);
				$success.prop('hidden', true);
				return;
			}
			$error.prop('hidden', true);
			$success.prop('hidden', false);
			if (!prefersReducedMotion()) {
				$form.find('.ltxe-form-styler__submit').addClass('is-sending');
			}
		});
	}

	function init(ctx) {
		$(ctx || document).find('.ltxe-form-styler').each(function () {
			bind($(this));
		});
	}

	$(function () {
		init(document);
	});
	$(window).on('elementor/frontend/init', function () {
		if (window.elementorFrontend && elementorFrontend.hooks) {
			elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-cf7-styler.default', function ($scope) {
				init($scope);
			});
			elementorFrontend.hooks.addAction('frontend/element_ready/ltxe-wpforms-styler.default', function ($scope) {
				init($scope);
			});
		}
	});
})(jQuery);
