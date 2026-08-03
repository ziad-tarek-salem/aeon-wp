/**
 * Repeatable list rows for `lines` section fields (service highlights, "what's
 * included").
 *
 * Each row is a plain `key[]` input, so the browser posts the list in order and
 * the only job here is adding, removing and moving rows.
 *
 * @package AEON
 */
(function ($) {
	'use strict';

	/**
	 * Build a row. Mirrors aeon_lines_row() in PHP — keep the two in step.
	 *
	 * @param {string} name Field key.
	 * @return {jQuery}
	 */
	function row(name) {
		var $li = $('<li class="aeon-lines-row"></li>');

		$li.append('<span class="aeon-lines-handle dashicons dashicons-menu-alt2" aria-hidden="true"></span>');
		$li.append($('<input type="text">').attr('name', name + '[]'));
		$li.append(
			button('aeon-lines-up', 'dashicons-arrow-up-alt2', 'تحريك لأعلى'),
			button('aeon-lines-down', 'dashicons-arrow-down-alt2', 'تحريك لأسفل'),
			button('aeon-lines-remove', 'dashicons-no-alt', 'حذف العنصر')
		);

		return $li;
	}

	function button(cls, icon, label) {
		return $('<button type="button" class="button"></button>')
			.addClass(cls)
			.attr('aria-label', label)
			.append($('<span class="dashicons"></span>').addClass(icon));
	}

	function sortable($box) {
		$box.find('.aeon-lines-list').sortable({
			items: '> .aeon-lines-row',
			handle: '.aeon-lines-handle',
			placeholder: 'aeon-lines-row aeon-lines-row--drop',
			forcePlaceholderSize: true,
			tolerance: 'pointer'
		});
	}

	$(function () {
		$('.aeon-lines').each(function () {
			sortable($(this));
		});
	});

	$(document).on('click', '.aeon-lines-add', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.aeon-lines');
		var $new = row($box.data('name'));
		$box.find('.aeon-lines-list').append($new);
		$new.find('input').trigger('focus');
	});

	$(document).on('click', '.aeon-lines-remove', function (e) {
		e.preventDefault();
		$(this).closest('.aeon-lines-row').remove();
	});

	$(document).on('click', '.aeon-lines-up, .aeon-lines-down', function (e) {
		e.preventDefault();

		var $btn = $(this);
		var $row = $btn.closest('.aeon-lines-row');
		var up = $btn.hasClass('aeon-lines-up');
		var $swap = up ? $row.prev('.aeon-lines-row') : $row.next('.aeon-lines-row');

		if (!$swap.length) {
			return;
		}

		if (up) {
			$row.insertBefore($swap);
		} else {
			$row.insertAfter($swap);
		}

		// Focus follows the row so repeated presses keep moving the same item.
		$btn.trigger('focus');
	});

	// The add-tag form posts over AJAX and is not re-rendered, so clear its rows
	// once a term has been created.
	$(document).ajaxComplete(function (e, xhr, settings) {
		if (settings && settings.data && settings.data.indexOf('action=add-tag') !== -1) {
			$('#addtag .aeon-lines-list').empty();
		}
	});
}(jQuery));
