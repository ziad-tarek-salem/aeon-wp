/**
 * Services page galleries — the per-service media manager.
 *
 * Owns one grid of tiles. Every mutation (add, replace, remove, reorder) runs
 * through sync(), which rewrites the hidden CSV the form actually submits, so
 * the tiles on screen are always the exact thing that gets saved.
 *
 * A gallery holds either images or videos, declared by `data-kind` on the box.
 * That choice drives the media frame's filter, the tile art and the wording, so
 * the video service can never be handed a JPEG or vice versa.
 *
 * @package AEON
 */
(function ($) {
	'use strict';

	var CFG = window.aeonSvcGallery || {};

	/** The gallery's medium: 'video' or 'image'. */
	function kindOf($box) {
		return 'video' === $box.data('kind') ? 'video' : 'image';
	}

	/** Wording for a gallery's medium. */
	function L($box) {
		return CFG[kindOf($box)] || {};
	}

	/**
	 * Tile art. Images use their own thumbnail; a video uses its featured image
	 * when one is set, and otherwise gets the film-icon placeholder that PHP
	 * renders for the same case.
	 *
	 * @param {Object} att  Attachment JSON from the media frame.
	 * @param {string} kind Gallery medium.
	 * @return {string} URL, or '' when there is nothing to show.
	 */
	function art(att, kind) {
		if ('video' === kind) {
			// wp.media exposes a video's featured image as `image`/`thumb`; `icon`
			// is the generic mime glyph, which we deliberately do not use.
			if (att.image && att.image.src && !/wp-includes\/images\/media\//.test(att.image.src)) { return att.image.src; }
			if (att.thumb && att.thumb.src) { return att.thumb.src; }
			return '';
		}
		return (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : (att.url || '');
	}

	/**
	 * Build a tile. Mirrors aeon_service_gallery_tile() in PHP — keep the two
	 * in step.
	 *
	 * @param {Object} att  Attachment JSON.
	 * @param {jQuery} $box The gallery.
	 * @return {jQuery}
	 */
	function tile(att, $box) {
		var kind = kindOf($box);
		var l = L($box);
		var video = 'video' === kind;
		var src = art(att, kind);
		var $li = $('<li class="aeon-svc-tile"></li>').attr('data-id', att.id).toggleClass('aeon-svc-tile--video', video);

		$li.append('<span class="aeon-svc-tile__num"></span>');

		if (src) {
			$li.append($('<img alt="">').attr('src', src));
		} else {
			$li.append('<span class="aeon-svc-tile__ph"><span class="dashicons dashicons-format-video"></span></span>');
		}

		$li.append(
			$('<span class="aeon-svc-tile__actions"></span>').append(
				button('aeon-svc-earlier', 'dashicons-arrow-up-alt2', l.earlier),
				button('aeon-svc-later', 'dashicons-arrow-down-alt2', l.later),
				button('aeon-svc-replace', 'dashicons-image-rotate-left', l.replace),
				button('aeon-svc-remove', 'dashicons-no-alt', l.remove)
			)
		);

		// Videos are labelled by library title; images by alt text, which is what
		// the front end prints and so is worth chasing when missing.
		var caption = video ? $.trim(att.title || '') : $.trim(att.alt || '');
		if (caption) {
			$li.append($('<span class="aeon-svc-tile__alt"></span>').attr('title', caption).text(caption));
		} else if (video) {
			$li.append($('<span class="aeon-svc-tile__alt aeon-svc-tile__alt--missing"></span>').text(l.missingAlt || ''));
		} else {
			$li.append(
				$('<a class="aeon-svc-tile__alt aeon-svc-tile__alt--missing" target="_blank" rel="noopener"></a>')
					.attr('href', (CFG.editBase || '') + att.id)
					.text(l.missingAlt || '')
			);
		}

		return $li;
	}

	function button(cls, icon, label) {
		return $('<button type="button" class="button"></button>')
			.addClass(cls)
			.attr('aria-label', label || '')
			.append($('<span class="dashicons"></span>').addClass(icon));
	}

	/** Push the on-screen order into the hidden field and refresh the chrome. */
	function sync($box) {
		var ids = [];

		$box.find('.aeon-svc-tile').each(function (i) {
			ids.push($(this).attr('data-id'));
			$(this).find('.aeon-svc-tile__num').text(i + 1);
		});

		$box.find('.aeon-svc-ids').val(ids.join(','));
		$box.find('.aeon-svc-grid').toggleClass('is-empty', ids.length === 0);
		$box.find('.aeon-svc-clear').toggle(ids.length > 0);
		$box.closest('form').find('.aeon-svc-empty').prop('hidden', ids.length > 0);
	}

	/** IDs already in this grid, so a re-pick never duplicates a tile. */
	function currentIds($box) {
		return $box.find('.aeon-svc-tile').map(function () {
			return parseInt($(this).attr('data-id'), 10);
		}).get();
	}

	/** A media frame restricted to this gallery's medium. */
	function frameFor($box, opts) {
		var l = L($box);
		return wp.media({
			title: opts.multiple ? l.addTitle : l.swapTitle,
			button: { text: opts.multiple ? l.addButton : l.swapButton },
			library: { type: kindOf($box) },
			multiple: opts.multiple ? 'add' : false
		});
	}

	$(function () {
		$('.aeon-svc-gallery').each(function () {
			var $box = $(this);

			$box.find('.aeon-svc-grid').sortable({
				items: '> .aeon-svc-tile',
				cancel: 'button, a',
				placeholder: 'aeon-svc-tile aeon-svc-tile--drop',
				forcePlaceholderSize: true,
				tolerance: 'pointer',
				update: function () {
					sync($box);
				}
			});

			sync($box);
		});
	});

	// Add: append whatever is new, keep what is already there.
	$(document).on('click', '.aeon-svc-add', function (e) {
		e.preventDefault();

		var $box = $(this).closest('.aeon-svc-gallery');
		var frame = frameFor($box, { multiple: true });

		frame.on('select', function () {
			var have = currentIds($box);
			var $grid = $box.find('.aeon-svc-grid');

			frame.state().get('selection').each(function (model) {
				var att = model.toJSON();
				if (have.indexOf(att.id) === -1) {
					have.push(att.id);
					$grid.append(tile(att, $box));
				}
			});

			sync($box);
		});

		frame.open();
	});

	// Replace one item in place, keeping its position.
	$(document).on('click', '.aeon-svc-replace', function (e) {
		e.preventDefault();

		var $tile = $(this).closest('.aeon-svc-tile');
		var $box = $(this).closest('.aeon-svc-gallery');
		var frame = frameFor($box, { multiple: false });

		frame.on('select', function () {
			var att = frame.state().get('selection').first().toJSON();
			// Swapping in an item the grid already holds would leave two tiles
			// pointing at one attachment; the save would dedupe them and silently
			// drop a slot, so refuse the swap instead.
			if (currentIds($box).indexOf(att.id) !== -1 && parseInt($tile.attr('data-id'), 10) !== att.id) {
				return;
			}
			$tile.replaceWith(tile(att, $box));
			sync($box);
		});

		frame.open();
	});

	$(document).on('click', '.aeon-svc-remove', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.aeon-svc-gallery');
		$(this).closest('.aeon-svc-tile').remove();
		sync($box);
	});

	// Keyboard- and click-friendly reordering, alongside drag and drop.
	$(document).on('click', '.aeon-svc-earlier, .aeon-svc-later', function (e) {
		e.preventDefault();

		var $btn = $(this);
		var $tile = $btn.closest('.aeon-svc-tile');
		var $box = $btn.closest('.aeon-svc-gallery');
		var earlier = $btn.hasClass('aeon-svc-earlier');
		var $swap = earlier ? $tile.prev('.aeon-svc-tile') : $tile.next('.aeon-svc-tile');

		if (!$swap.length) {
			return;
		}

		if (earlier) {
			$tile.insertBefore($swap);
		} else {
			$tile.insertAfter($swap);
		}

		sync($box);
		// Focus follows the tile so repeated presses keep moving the same item.
		$tile.find(earlier ? '.aeon-svc-earlier' : '.aeon-svc-later').trigger('focus');
	});

	$(document).on('click', '.aeon-svc-clear', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.aeon-svc-gallery');
		var msg = L($box).confirmClear;
		if (msg && !window.confirm(msg)) {
			return;
		}
		$box.find('.aeon-svc-tile').remove();
		sync($box);
	});
}(jQuery));
