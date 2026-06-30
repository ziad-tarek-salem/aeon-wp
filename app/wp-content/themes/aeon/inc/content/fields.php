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

		case 'image':
			$aid = (int) $value;
			$url = $aid ? wp_get_attachment_image_url( $aid, 'thumbnail' ) : '';
			echo '<div class="aeon-image-wrap">';
			echo '<input type="hidden" class="aeon-image-id" name="' . $name . '" value="' . esc_attr( $aid ) . '">';
			echo '<div class="aeon-image-preview">' . ( $url ? '<img src="' . esc_url( $url ) . '" alt="">' : '' ) . '</div>';
			echo '<button type="button" class="button aeon-image-pick">اختيار صورة</button> ';
			echo '<button type="button" class="button aeon-image-clear"' . ( $aid ? '' : ' style="display:none"' ) . '>إزالة</button>';
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
			case 'select':
				update_term_meta( $term_id, $meta, array_key_exists( $raw, $f['options'] ) ? $raw : '' );
				break;
			case 'icon':
				update_term_meta( $term_id, $meta, sanitize_key( $raw ) );
				break;
			case 'image':
				$aid = (int) $raw;
				if ( $aid > 0 ) {
					update_term_meta( $term_id, $meta, $aid );
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

	$hide_desc = empty( $section['show_desc'] ) ? '.term-description-wrap{display:none;}' : '';
	$css = '
		.aeon-icon-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(84px,1fr));gap:8px;max-width:560px;}
		.aeon-icon-opt{display:flex;flex-direction:column;align-items:center;gap:4px;padding:10px 6px;border:1px solid #dcdcde;border-radius:8px;cursor:pointer;text-align:center;font-size:11px;line-height:1.3;background:#fff;}
		.aeon-icon-opt:hover{border-color:#2271b1;}
		.aeon-icon-opt.is-selected{border-color:#2271b1;box-shadow:0 0 0 1px #2271b1;background:#f0f6fc;}
		.aeon-icon-opt input{position:absolute;opacity:0;pointer-events:none;}
		.aeon-icon-opt .aeon-icon{width:26px;height:26px;color:#1d2327;}
		.aeon-image-preview img{max-width:120px;height:auto;border-radius:8px;display:block;margin:.4em 0;}
		.term-slug-wrap{display:none;}
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
  // Media picker.
  var frame;
  $(document).on('click','.aeon-image-pick',function(e){
    e.preventDefault();
    var $w=$(this).closest('.aeon-image-wrap');
    frame=wp.media({title:'اختيار صورة',button:{text:'استخدام الصورة'},library:{type:'image'},multiple:false});
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
