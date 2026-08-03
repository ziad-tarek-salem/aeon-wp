<?php
/**
 * Generic inline term-meta fields for the section taxonomies.
 *
 * Reads each section's `fields` from aeon_sections_config() and renders them on
 * the term Add/Edit screens (types: text, number, select, icon, image), saving
 * to `_aeon_{key}` term meta. Also polishes those screens: media uploader, icon
 * grid behaviour, Arabic core labels, hidden Slug, hidden Description where the
 * section doesn't use it, and RTL alignment.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Wire add/edit/save hooks for every configured field. */
function aeon_fields_init() {
	foreach ( aeon_sections_config() as $tax => $section ) {
		if ( empty( $section['fields'] ) ) {
			continue;
		}
		$fields = $section['fields'];

		add_action( "{$tax}_add_form_fields", function () use ( $fields ) {
			aeon_render_fields( $fields, null );
		} );
		add_action( "{$tax}_edit_form_fields", function ( $term ) use ( $fields ) {
			aeon_render_fields( $fields, $term );
		} );
		$save = function ( $term_id ) use ( $fields ) {
			aeon_save_fields( $fields, $term_id );
		};
		add_action( "created_{$tax}", $save );
		add_action( "edited_{$tax}", $save );
	}
}
add_action( 'init', 'aeon_fields_init', 11 );

/**
 * Render every field. $term === null → "add new" layout (divs); otherwise the
 * "edit" layout (table rows).
 *
 * @param array        $fields Field configs.
 * @param WP_Term|null $term   Term being edited, or null on the add screen.
 */
function aeon_render_fields( $fields, $term ) {
	$is_edit = $term instanceof WP_Term;
	foreach ( $fields as $key => $f ) {
		$value = $is_edit ? get_term_meta( $term->term_id, '_aeon_' . $key, true ) : '';
		$label = esc_html( $f['label'] );
		$id    = 'aeon_field_' . $key;

		if ( $is_edit ) {
			echo '<tr class="form-field aeon-field aeon-field--' . esc_attr( $f['type'] ) . '">';
			echo '<th scope="row"><label for="' . esc_attr( $id ) . '">' . $label . '</label></th><td>';
			aeon_field_control( $key, $f, $value, $id );
			echo '</td></tr>';
		} else {
			echo '<div class="form-field aeon-field aeon-field--' . esc_attr( $f['type'] ) . '">';
			echo '<label for="' . esc_attr( $id ) . '">' . $label . '</label>';
			aeon_field_control( $key, $f, $value, $id );
			echo '</div>';
		}
	}
}

/**
 * Render a single field control + its hint.
 *
 * @param string $key   Field key (also the form field name).
 * @param array  $f     Field config.
 * @param mixed  $value Current value.
 * @param string $id    Input id.
 */
function aeon_field_control( $key, $f, $value, $id ) {
	$name = esc_attr( $key );
	$ph   = isset( $f['placeholder'] ) ? esc_attr( $f['placeholder'] ) : '';

	switch ( $f['type'] ) {

		case 'number':
			printf(
				'<input type="number" id="%1$s" name="%2$s" value="%3$s" min="0" step="1" placeholder="%4$s">',
				esc_attr( $id ), $name, esc_attr( $value ), $ph
			);
			break;

		case 'decimal':
			// Allows fractional and negative values (e.g. GPS coordinates).
			printf(
				'<input type="number" id="%1$s" name="%2$s" value="%3$s" step="any" inputmode="decimal" placeholder="%4$s">',
				esc_attr( $id ), $name, esc_attr( $value ), $ph
			);
			break;

		case 'select':
			echo '<select id="' . esc_attr( $id ) . '" name="' . $name . '">';
			foreach ( $f['options'] as $val => $opt_label ) {
				printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $val ), selected( $value, $val, false ), esc_html( $opt_label ) );
			}
			echo '</select>';
			break;

		case 'icon':
			echo '<div class="aeon-icon-grid">';
			foreach ( aeon_icon_choices() as $ik => $il ) {
				printf(
					'<label class="aeon-icon-opt%5$s" title="%3$s"><input type="radio" name="%4$s" value="%1$s" %2$s>%6$s<span>%3$s</span></label>',
					esc_attr( $ik ), checked( $value, $ik, false ), esc_attr( $il ), $name,
					( $value === $ik ? ' is-selected' : '' ), aeon_icon( $ik )
				);
			}
			echo '</div>';
			break;

		case 'duoicon':
			// Same picker, rendered with the two-tone brand set the front end uses.
			echo '<div class="aeon-icon-grid aeon-icon-grid--duo">';
			foreach ( aeon_duo_icon_choices() as $ik => $il ) {
				printf(
					'<label class="aeon-icon-opt%5$s" title="%3$s"><input type="radio" name="%4$s" value="%1$s" %2$s>%6$s<span>%3$s</span></label>',
					esc_attr( $ik ), checked( $value, $ik, false ), esc_attr( $il ), $name,
					( $value === $ik ? ' is-selected' : '' ), aeon_duo_icon( $ik )
				);
			}
			echo '</div>';
			break;

		case 'url':
			printf(
				'<input type="url" class="aeon-url" id="%1$s" name="%2$s" value="%3$s" placeholder="%4$s" dir="ltr">',
				esc_attr( $id ), $name, esc_attr( $value ), $ph
			);
			break;

		case 'image':
			$aid  = (int) $value;
			$url  = $aid ? wp_get_attachment_image_url( $aid, 'thumbnail' ) : '';
			$mime = isset( $f['mime'] ) ? $f['mime'] : 'image';
			echo '<div class="aeon-image-wrap" data-mime="' . esc_attr( $mime ) . '">';
			echo '<input type="hidden" class="aeon-image-id" name="' . $name . '" value="' . esc_attr( $aid ) . '">';
			echo '<div class="aeon-image-preview">' . ( $url ? '<img src="' . esc_url( $url ) . '" alt="">' : '' ) . '</div>';
			echo '<button type="button" class="button aeon-image-pick">اختيار ملف</button> ';
			echo '<button type="button" class="button aeon-image-clear"' . ( $aid ? '' : ' style="display:none"' ) . '>إزالة</button>';
			echo '</div>';
			break;

		case 'textarea':
			printf(
				'<textarea id="%1$s" name="%2$s" rows="%5$d" class="large-text aeon-textarea" placeholder="%4$s">%3$s</textarea>',
				esc_attr( $id ), $name, esc_textarea( $value ), $ph,
				isset( $f['rows'] ) ? (int) $f['rows'] : 5
			);
			break;

		case 'lines':
			// One row per item, each its own input named `key[]`, so the browser
			// posts the list in order without any JavaScript in the loop. The
			// leading empty row guarantees the key is present even when every item
			// has been deleted — otherwise "delete them all" would post nothing and
			// silently leave the old value in place.
			$items = aeon_lines_to_array( $value );
			$add   = isset( $f['add_label'] ) ? $f['add_label'] : 'إضافة عنصر';

			echo '<div class="aeon-lines" data-name="' . $name . '">';
			printf( '<input type="hidden" name="%s[]" value="">', $name );
			echo '<ul class="aeon-lines-list">';
			foreach ( $items as $item ) {
				echo aeon_lines_row( $key, $item ); // phpcs:ignore WordPress.Security.EscapeOutput -- built escaped.
			}
			echo '</ul>';
			echo '<button type="button" class="button aeon-lines-add"><span class="dashicons dashicons-plus-alt2"></span> ' . esc_html( $add ) . '</button>';
			echo '</div>';
			break;

		case 'text':
		default:
			printf(
				'<input type="text" id="%1$s" name="%2$s" value="%3$s" placeholder="%4$s">',
				esc_attr( $id ), $name, esc_attr( $value ), $ph
			);
			break;
	}

	if ( ! empty( $f['hint'] ) ) {
		echo '<p class="description">' . esc_html( $f['hint'] ) . '</p>';
	}
}

/**
 * One editable row of a `lines` field, escaped and ready to echo.
 *
 * The same shape is rebuilt in assets/admin/section-fields.js when a row is
 * added — keep the two in step.
 *
 * @param string $key   Field key (the form field name).
 * @param string $value Row value.
 * @return string
 */
function aeon_lines_row( $key, $value = '' ) {
	$out  = '<li class="aeon-lines-row">';
	$out .= '<span class="aeon-lines-handle dashicons dashicons-menu-alt2" aria-hidden="true"></span>';
	$out .= '<input type="text" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $value ) . '">';
	$out .= '<button type="button" class="button aeon-lines-up" aria-label="تحريك لأعلى"><span class="dashicons dashicons-arrow-up-alt2"></span></button>';
	$out .= '<button type="button" class="button aeon-lines-down" aria-label="تحريك لأسفل"><span class="dashicons dashicons-arrow-down-alt2"></span></button>';
	$out .= '<button type="button" class="button aeon-lines-remove" aria-label="حذف العنصر"><span class="dashicons dashicons-no-alt"></span></button>';
	$out .= '</li>';

	return $out;
}

/**
 * Persist a term's fields. Core verifies the term nonce before created_/edited_
 * fire, so we only enforce capability here.
 *
 * @param array $fields  Field configs.
 * @param int   $term_id Term ID.
 */
function aeon_save_fields( $fields, $term_id ) {
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	foreach ( $fields as $key => $f ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw  = wp_unslash( $_POST[ $key ] );
		$meta = '_aeon_' . $key;

		switch ( $f['type'] ) {
			case 'number':
				update_term_meta( $term_id, $meta, (string) absint( $raw ) );
				break;
			case 'decimal':
				$raw = trim( $raw );
				// Keep the exact numeric string (preserves coordinate precision); drop anything non-numeric.
				update_term_meta( $term_id, $meta, is_numeric( $raw ) ? $raw : '' );
				break;
			case 'select':
				update_term_meta( $term_id, $meta, array_key_exists( $raw, $f['options'] ) ? $raw : '' );
				break;
			case 'icon':
			case 'duoicon':
				update_term_meta( $term_id, $meta, sanitize_key( $raw ) );
				break;
			case 'url':
				$raw = trim( $raw );
				if ( '' === $raw ) {
					delete_term_meta( $term_id, $meta );
				} else {
					// esc_url_raw drops anything that is not an http(s) URL.
					update_term_meta( $term_id, $meta, esc_url_raw( $raw, array( 'http', 'https' ) ) );
				}
				break;
			case 'image':
				$aid = (int) $raw;
				if ( $aid > 0 ) {
					update_term_meta( $term_id, $meta, $aid );
				} else {
					delete_term_meta( $term_id, $meta );
				}
				break;
			case 'textarea':
				$clean = sanitize_textarea_field( $raw );
				if ( '' === trim( $clean ) ) {
					delete_term_meta( $term_id, $meta );
				} else {
					update_term_meta( $term_id, $meta, $clean );
				}
				break;

			case 'lines':
				// Stored as one item per line; blank rows are dropped rather than
				// saved as empty bullets.
				$items = is_array( $raw ) ? $raw : preg_split( '/\r\n|\r|\n/', (string) $raw );
				$items = array_filter( array_map( static function ( $v ) {
					return sanitize_text_field( trim( (string) $v ) );
				}, (array) $items ), 'strlen' );

				if ( $items ) {
					update_term_meta( $term_id, $meta, implode( "\n", array_values( $items ) ) );
				} else {
					delete_term_meta( $term_id, $meta );
				}
				break;

			case 'text':
			default:
				update_term_meta( $term_id, $meta, sanitize_text_field( $raw ) );
				break;
		}
	}
}

/* -------------------------------------------------------------------------- *
 *  Admin screen polish (assets + Arabic core labels), our taxonomies only.
 * -------------------------------------------------------------------------- */

/** Current taxonomy if we're on one of our term screens, else ''. */
function aeon_current_section_tax() {
	if ( ! is_admin() ) {
		return '';
	}
	$tax = isset( $_GET['taxonomy'] ) ? sanitize_key( wp_unslash( $_GET['taxonomy'] ) ) : '';
	return in_array( $tax, aeon_section_taxonomies(), true ) ? $tax : '';
}

/** Media frame + inline CSS/JS for the term screens. */
function aeon_fields_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) ) {
		return;
	}
	$tax = aeon_current_section_tax();
	if ( '' === $tax ) {
		return;
	}
	$section   = aeon_sections_config()[ $tax ];
	$has_image = ! empty( array_filter( $section['fields'], function ( $f ) {
		return 'image' === $f['type'];
	} ) );
	if ( $has_image ) {
		wp_enqueue_media();
	}

	$has_lines = ! empty( array_filter( $section['fields'], static function ( $f ) {
		return 'lines' === $f['type'];
	} ) );
	if ( $has_lines ) {
		wp_enqueue_script(
			'aeon-section-fields',
			AEON_URI . '/assets/admin/section-fields.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			AEON_VERSION,
			true
		);
	}

	$hide_desc = empty( $section['show_desc'] ) ? '.term-description-wrap{display:none;}' : '';
	$css = '
		.aeon-icon-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(84px,1fr));gap:8px;max-width:560px;}
		.aeon-icon-opt{display:flex;flex-direction:column;align-items:center;gap:4px;padding:10px 6px;border:1px solid #dcdcde;border-radius:8px;cursor:pointer;text-align:center;font-size:11px;line-height:1.3;background:#fff;}
		.aeon-icon-opt:hover{border-color:#2271b1;}
		.aeon-icon-opt.is-selected{border-color:#2271b1;box-shadow:0 0 0 1px #2271b1;background:#f0f6fc;}
		.aeon-icon-opt input{position:absolute;opacity:0;pointer-events:none;}
		.aeon-icon-opt .aeon-icon{width:26px;height:26px;color:#1d2327;}
		/* Two-tone brand icons: identical tinting to the front end. */
		.aeon-icon-grid--duo{grid-template-columns:repeat(auto-fill,minmax(96px,1fr));max-width:640px;}
		.aeon-duo{--ico-a:#6D28D9;--ico-b:#F36F21;display:block;width:34px;height:34px;}
		.aeon-duo .da{fill:var(--ico-a);}
		.aeon-duo .db{fill:var(--ico-b);}
		.aeon-duo .sa{stroke:var(--ico-a);fill:none;}
		.aeon-duo .sb{stroke:var(--ico-b);fill:none;}
		.aeon-image-preview img{max-width:96px;height:auto;border-radius:8px;display:block;margin:.4em 0;background:#f6f7f7;padding:6px;}
		.aeon-url{width:100%;max-width:520px;}
		.term-slug-wrap{display:none;}
		/* Repeatable list rows (service highlights, "what is included"). */
		.aeon-textarea{max-width:640px;}
		.aeon-lines{max-width:640px;}
		.aeon-lines-list{margin:0 0 8px;padding:0;list-style:none;}
		.aeon-lines-row{display:flex;align-items:center;gap:4px;margin-bottom:6px;}
		.aeon-lines-row input[type=text]{flex:1 1 auto;min-width:0;}
		.aeon-lines-handle{flex:0 0 auto;color:#a7aaad;cursor:move;}
		.aeon-lines-row .button{flex:0 0 auto;display:flex;align-items:center;justify-content:center;width:28px;min-width:28px;height:28px;padding:0;}
		.aeon-lines-row .button .dashicons{width:16px;height:16px;font-size:16px;line-height:1;}
		.aeon-lines-remove:hover,.aeon-lines-remove:focus{border-color:#d63638;color:#d63638;}
		.aeon-lines-add .dashicons{margin-inline-end:4px;vertical-align:text-top;font-size:16px;line-height:1.4;}
		.aeon-lines-row--drop{outline:2px dashed #2271b1;outline-offset:2px;}
		' . $hide_desc . '
		body.rtl .aeon-field input[type=text],body.rtl .aeon-field input[type=number]{text-align:right;}
	';
	wp_register_style( 'aeon-fields', false );
	wp_enqueue_style( 'aeon-fields' );
	wp_add_inline_style( 'aeon-fields', $css );

	$js = <<<'JS'
jQuery(function($){
  // Icon grid: reflect selection.
  $(document).on('change','.aeon-icon-grid input[type=radio]',function(){
    var $g=$(this).closest('.aeon-icon-grid');
    $g.find('.aeon-icon-opt').removeClass('is-selected');
    $(this).closest('.aeon-icon-opt').addClass('is-selected');
  });
  // Media picker. Icon fields (data-mime="svg-image") also accept SVG.
  var frame;
  $(document).on('click','.aeon-image-pick',function(e){
    e.preventDefault();
    var $w=$(this).closest('.aeon-image-wrap');
    var lib=($w.data('mime')==='svg-image')?{type:['image','image/svg+xml']}:{type:'image'};
    frame=wp.media({title:'اختيار ملف',button:{text:'استخدام هذا الملف'},library:lib,multiple:false});
    frame.on('select',function(){
      var a=frame.state().get('selection').first().toJSON();
      var url=(a.sizes&&a.sizes.thumbnail)?a.sizes.thumbnail.url:a.url;
      $w.find('.aeon-image-id').val(a.id);
      $w.find('.aeon-image-preview').html('<img src="'+url+'" alt="">');
      $w.find('.aeon-image-clear').show();
    });
    frame.open();
  });
  $(document).on('click','.aeon-image-clear',function(e){
    e.preventDefault();
    var $w=$(this).closest('.aeon-image-wrap');
    $w.find('.aeon-image-id').val(''); $w.find('.aeon-image-preview').empty(); $(this).hide();
  });
  // Reset the add-form fields after a term is added via AJAX.
  $(document).ajaxComplete(function(e,xhr,s){
    if(s && s.data && s.data.indexOf('action=add-tag')!==-1){
      $('#addtag .aeon-icon-grid input:checked').prop('checked',false).trigger('change');
      $('#addtag .aeon-icon-opt').removeClass('is-selected');
      $('#addtag .aeon-url').val('');
      var $w=$('#addtag .aeon-image-wrap');
      $w.find('.aeon-image-id').val(''); $w.find('.aeon-image-preview').empty(); $w.find('.aeon-image-clear').hide();
    }
  });
});
JS;
	wp_add_inline_script( 'jquery-core', $js );
}
add_action( 'admin_enqueue_scripts', 'aeon_fields_admin_assets' );

/** Arabic labels for the residual core fields (Name/Description/Slug) on our screens. */
function aeon_fields_label_filter() {
	if ( '' === aeon_current_section_tax() ) {
		return;
	}
	add_filter( 'gettext', function ( $translated, $text ) {
		switch ( $text ) {
			case 'Name':
				return 'الاسم';
			case 'Description':
				return 'الوصف';
			case 'Slug':
				return 'الاسم اللطيف';
		}
		return $translated;
	}, 10, 2 );
}
add_action( 'admin_init', 'aeon_fields_label_filter' );
