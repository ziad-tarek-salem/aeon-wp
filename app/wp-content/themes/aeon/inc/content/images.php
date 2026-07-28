<?php
/**
 * Site images — every replaceable image on the front end, managed from the
 * dashboard (محتوى الموقع → صور الموقع).
 *
 * Each image the theme renders is a named "slot". A slot resolves to the
 * attachment the client picked in the Media Library, and falls back to the file
 * bundled with the theme when nothing has been picked yet — so the site never
 * renders a broken image, and replacing artwork never requires a code change.
 *
 * Two kinds of slot:
 *   - single  : one image  (logos, banners, section photos)
 *   - gallery : an ordered set (office gallery, client logos, event photos)
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AEON_IMG_OPTION = 'aeon_site_images';

/**
 * Single-image slots: key => label, hint and the bundled fallback file
 * (relative to assets/images/).
 *
 * @return array<string,array{label:string,hint:string,fallback:string}>
 */
function aeon_image_slots() {
	return (array) apply_filters( 'aeon_image_slots', array(
		'logo_mark'     => array(
			'label'    => 'شعار الهيدر',
			'hint'     => 'الشعار الظاهر أعلى الموقع. يُفضّل PNG أو SVG بخلفية شفافة وارتفاع لا يقل عن 200 بكسل.',
			'fallback' => 'logo-mark.png',
		),
		'logo_lockup'   => array(
			'label'    => 'شعار الفوتر والنافذة المنبثقة',
			'hint'     => 'الشعار الكامل الظاهر في الفوتر وفي نافذة الواتساب. يُفضّل PNG بخلفية شفافة.',
			'fallback' => 'logo-lockup.png',
		),
		'about_page'    => array(
			'label'    => 'صورة صفحة «من نحن»',
			'hint'     => 'الصورة الكبيرة بجانب نص التعريف في صفحة من نحن. يُفضّل صورة أفقية 1200×960 بكسل أو أكبر.',
			'fallback' => 'brand-1.jpeg',
		),
		'why_choose'    => array(
			'label'    => 'صورة «لماذا نحن»',
			'hint'     => 'تُستخدم كصورة افتراضية لقسم نبذة عن خبراتنا وأقسام أخرى.',
			'fallback' => 'why-choose.jpeg',
		),
		'services_grid' => array(
			'label'    => 'صورة بديلة لبطاقات الأعمال',
			'hint'     => 'تظهر عندما لا تحتوي بطاقة العمل على صورة بارزة خاصة بها.',
			'fallback' => 'services-grid.jpeg',
		),
	) );
}

/**
 * Gallery slots: key => label, hint and the bundled fallback list (each entry
 * relative to assets/images/).
 *
 * @return array<string,array{label:string,hint:string,fallback:array}>
 */
function aeon_image_gallery_slots() {
	return (array) apply_filters( 'aeon_image_gallery_slots', array(
		'office'   => array(
			'label'    => 'صور المكتب',
			'hint'     => 'معرض قسم «نتشرف بزيارتكم». الصورة الأولى هي الصورة الكبيرة، وما بعدها صور مصغّرة.',
			'fallback' => aeon_office_images(),
		),
		'partners' => array(
			'label'    => 'شعارات العملاء وشركاء النجاح',
			'hint'     => 'شعارات العملاء في قسم «شركاء النجاح». يُفضّل PNG بخلفية شفافة ومقاس موحّد. يُستخدم عنوان الصورة في المكتبة كاسم العميل.',
			'fallback' => array(),
		),
		'events'   => array(
			'label'    => 'صور المشاركات والفعاليات (افتراضية)',
			'hint'     => 'تُستخدم لبطاقات المشاركات التي لم تُرفع لها صورة خاصة من صفحة «المشاركات والفعاليات».',
			'fallback' => array(),
		),
	) );
}

/**
 * The stored map of slot => attachment ID (single) or IDs (gallery).
 *
 * @return array
 */
function aeon_image_option() {
	$stored = get_option( AEON_IMG_OPTION, array() );
	return is_array( $stored ) ? $stored : array();
}

/**
 * URL for a single-image slot, falling back to the bundled file.
 *
 * @param string $slot Slot key from aeon_image_slots().
 * @param string $size Registered image size for the attachment.
 * @return string Image URL, or '' when the slot is unknown and has no fallback.
 */
function aeon_image_url( $slot, $size = 'full' ) {
	$stored = aeon_image_option();
	$id     = isset( $stored[ $slot ] ) ? (int) $stored[ $slot ] : 0;

	if ( $id > 0 ) {
		$url = wp_get_attachment_image_url( $id, $size );
		if ( $url ) {
			return $url;
		}
	}

	$slots = aeon_image_slots();
	if ( isset( $slots[ $slot ]['fallback'] ) ) {
		return aeon_img( $slots[ $slot ]['fallback'] );
	}

	return '';
}

/**
 * Attachment ID behind a single-image slot, or 0 when it is using the fallback.
 *
 * @param string $slot Slot key.
 * @return int
 */
function aeon_image_id( $slot ) {
	$stored = aeon_image_option();
	return isset( $stored[ $slot ] ) ? (int) $stored[ $slot ] : 0;
}

/**
 * Is WordPress' own custom logo usable?
 *
 * has_custom_logo() only checks that an attachment ID is stored — it stays true
 * after the underlying file has been deleted, which renders a broken image. This
 * also confirms the file is actually on disk, so the header can fall back to the
 * logo slot instead.
 *
 * @return bool
 */
function aeon_custom_logo_ok() {
	$id = (int) get_theme_mod( 'custom_logo' );
	if ( $id < 1 ) {
		return false;
	}
	$path = get_attached_file( $id );
	return $path && file_exists( $path );
}

/**
 * Serve the header logo as a single fixed asset.
 *
 * The logo is rendered at a fixed 40px height, so a responsive srcset buys
 * nothing — and because the theme sizes it with `width: auto`, any failure to
 * resolve a candidate collapses the logo to zero width. Dropping srcset/sizes
 * makes it behave like every other fixed-size mark on the page.
 *
 * @param array $attr Attachment image attributes.
 * @param mixed $att  Attachment post object.
 * @return array
 */
function aeon_custom_logo_no_srcset( $attr, $att ) {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id && isset( $att->ID ) && (int) $att->ID === $logo_id ) {
		unset( $attr['srcset'], $attr['sizes'] );
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'aeon_custom_logo_no_srcset', 10, 2 );

/**
 * Images for a gallery slot, as { url, alt } rows.
 *
 * Client-picked attachments win as a complete set; otherwise the bundled
 * fallback list is used, so a half-filled gallery is never mixed with defaults.
 *
 * @param string $slot Slot key from aeon_image_gallery_slots().
 * @param string $size Registered image size.
 * @return array<int,array{url:string,alt:string,id:int}>
 */
function aeon_image_gallery( $slot, $size = 'large' ) {
	$stored = aeon_image_option();
	$ids    = isset( $stored[ $slot ] ) ? (array) $stored[ $slot ] : array();
	$out    = array();

	foreach ( $ids as $id ) {
		$id  = (int) $id;
		$url = $id ? wp_get_attachment_image_url( $id, $size ) : '';
		if ( ! $url ) {
			continue;
		}
		$alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		if ( '' === $alt ) {
			$alt = get_the_title( $id );
		}
		$out[] = array( 'url' => $url, 'alt' => $alt, 'id' => $id );
	}

	if ( $out ) {
		return $out;
	}

	$slots = aeon_image_gallery_slots();
	$files = isset( $slots[ $slot ]['fallback'] ) ? (array) $slots[ $slot ]['fallback'] : array();
	foreach ( $files as $file ) {
		$out[] = array( 'url' => aeon_img( $file ), 'alt' => '', 'id' => 0 );
	}

	return $out;
}

/* -------------------------------------------------------------------------
 * Dashboard page
 * ---------------------------------------------------------------------- */

/** Register the option. */
function aeon_images_register_setting() {
	register_setting( 'aeon_images_group', AEON_IMG_OPTION, array(
		'type'              => 'array',
		'sanitize_callback' => 'aeon_images_sanitize',
		'default'           => array(),
	) );
}
add_action( 'admin_init', 'aeon_images_register_setting' );

/**
 * Keep only known slots, and only attachment IDs.
 *
 * @param mixed $value Raw submitted value.
 * @return array
 */
function aeon_images_sanitize( $value ) {
	$out = array();
	if ( ! is_array( $value ) ) {
		return $out;
	}

	foreach ( array_keys( aeon_image_slots() ) as $slot ) {
		if ( ! empty( $value[ $slot ] ) ) {
			$out[ $slot ] = absint( $value[ $slot ] );
		}
	}

	foreach ( array_keys( aeon_image_gallery_slots() ) as $slot ) {
		if ( empty( $value[ $slot ] ) ) {
			continue;
		}
		$ids = is_array( $value[ $slot ] ) ? $value[ $slot ] : explode( ',', (string) $value[ $slot ] );
		$ids = array_values( array_filter( array_map( 'absint', $ids ) ) );
		if ( $ids ) {
			$out[ $slot ] = $ids;
		}
	}

	return $out;
}

/** Add the page under the site-content menu. */
function aeon_images_menu() {
	add_submenu_page( AEON_CONTENT_MENU, 'صور الموقع', 'صور الموقع', 'manage_options', 'aeon-images', 'aeon_images_page' );
}
add_action( 'admin_menu', 'aeon_images_menu', 11 );

/** Render the page. */
function aeon_images_page() {
	$name = AEON_IMG_OPTION;
	echo '<div class="wrap aeon-images" dir="rtl" style="text-align:right">';
	echo '<h1>صور الموقع</h1>';
	echo '<p style="max-width:760px">كل صور الموقع قابلة للتغيير من هنا دون الحاجة لتعديل الكود. اترك أي خانة فارغة لاستخدام الصورة الافتراضية المرفقة مع القالب.</p>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'aeon_images_group' );

	echo '<h2>صور مفردة</h2><table class="form-table" role="presentation"><tbody>';
	foreach ( aeon_image_slots() as $slot => $meta ) {
		$id  = aeon_image_id( $slot );
		$url = $id ? wp_get_attachment_image_url( $id, 'medium' ) : aeon_image_url( $slot, 'medium' );

		echo '<tr><th scope="row">' . esc_html( $meta['label'] ) . '</th><td>';
		echo '<div class="aeon-img-slot" data-slot="' . esc_attr( $slot ) . '">';
		echo '<input type="hidden" name="' . esc_attr( $name ) . '[' . esc_attr( $slot ) . ']" value="' . esc_attr( $id ) . '">';
		echo '<div class="aeon-img-preview">' . ( $url ? '<img src="' . esc_url( $url ) . '" alt="">' : '' ) . '</div>';
		echo '<button type="button" class="button aeon-img-pick">اختيار صورة</button> ';
		echo '<button type="button" class="button aeon-img-clear"' . ( $id ? '' : ' style="display:none"' ) . '>إزالة</button>';
		echo '<p class="description">' . esc_html( $meta['hint'] ) . ( $id ? '' : ' <strong>(تُستخدم الصورة الافتراضية حالياً)</strong>' ) . '</p>';
		echo '</div></td></tr>';
	}
	echo '</tbody></table>';

	echo '<h2>معارض الصور</h2><table class="form-table" role="presentation"><tbody>';
	foreach ( aeon_image_gallery_slots() as $slot => $meta ) {
		$stored = aeon_image_option();
		$ids    = isset( $stored[ $slot ] ) ? array_map( 'absint', (array) $stored[ $slot ] ) : array();

		echo '<tr><th scope="row">' . esc_html( $meta['label'] ) . '</th><td>';
		echo '<div class="aeon-img-gallery" data-slot="' . esc_attr( $slot ) . '">';
		echo '<input type="hidden" name="' . esc_attr( $name ) . '[' . esc_attr( $slot ) . ']" value="' . esc_attr( implode( ',', $ids ) ) . '">';
		echo '<div class="aeon-img-thumbs">';
		foreach ( $ids as $id ) {
			$thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
			if ( $thumb ) {
				echo '<img src="' . esc_url( $thumb ) . '" alt="">';
			}
		}
		echo '</div>';
		echo '<button type="button" class="button aeon-gal-pick">اختيار الصور</button> ';
		echo '<button type="button" class="button aeon-gal-clear"' . ( $ids ? '' : ' style="display:none"' ) . '>إزالة الكل</button>';
		echo '<p class="description">' . esc_html( $meta['hint'] ) . ( $ids ? '' : ' <strong>(تُستخدم الصور الافتراضية حالياً)</strong>' ) . '</p>';
		echo '</div></td></tr>';
	}
	echo '</tbody></table>';

	submit_button( 'حفظ التغييرات' );
	echo '</form></div>';
}

/** Media frame + styles, on this page only. */
function aeon_images_assets( $hook ) {
	if ( false === strpos( (string) $hook, 'aeon-images' ) ) {
		return;
	}
	wp_enqueue_media();

	wp_register_style( 'aeon-images-admin', false );
	wp_enqueue_style( 'aeon-images-admin' );
	wp_add_inline_style(
		'aeon-images-admin',
		'.aeon-img-preview img{max-width:260px;height:auto;border-radius:10px;display:block;margin:.6em 0;box-shadow:0 4px 14px rgba(0,0,0,.12)}'
		. '.aeon-img-thumbs{display:flex;flex-wrap:wrap;gap:8px;margin:.6em 0}'
		. '.aeon-img-thumbs img{width:78px;height:78px;object-fit:cover;border-radius:8px;box-shadow:0 3px 10px rgba(0,0,0,.14)}'
	);

	$js = <<<'JS'
jQuery(function ($) {
  // Single-image slots.
  $(document).on('click', '.aeon-img-pick', function (e) {
    e.preventDefault();
    var box = $(this).closest('.aeon-img-slot');
    var frame = wp.media({ title: 'اختيار صورة', button: { text: 'استخدام الصورة' }, library: { type: 'image' }, multiple: false });
    frame.on('select', function () {
      var a = frame.state().get('selection').first().toJSON();
      var u = (a.sizes && a.sizes.medium) ? a.sizes.medium.url : a.url;
      box.find('input[type=hidden]').val(a.id);
      box.find('.aeon-img-preview').html('<img src="' + u + '" alt="">');
      box.find('.aeon-img-clear').show();
    });
    frame.open();
  });
  $(document).on('click', '.aeon-img-clear', function (e) {
    e.preventDefault();
    var box = $(this).closest('.aeon-img-slot');
    box.find('input[type=hidden]').val('');
    box.find('.aeon-img-preview').empty();
    $(this).hide();
  });

  // Gallery slots.
  $(document).on('click', '.aeon-gal-pick', function (e) {
    e.preventDefault();
    var box = $(this).closest('.aeon-img-gallery');
    var frame = wp.media({ title: 'اختيار الصور', button: { text: 'استخدام الصور' }, library: { type: 'image' }, multiple: 'add' });
    frame.on('select', function () {
      var ids = [], thumbs = '';
      frame.state().get('selection').each(function (m) {
        var a = m.toJSON();
        ids.push(a.id);
        thumbs += '<img src="' + ((a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url) + '" alt="">';
      });
      box.find('input[type=hidden]').val(ids.join(','));
      box.find('.aeon-img-thumbs').html(thumbs);
      box.find('.aeon-gal-clear').toggle(ids.length > 0);
    });
    frame.open();
  });
  $(document).on('click', '.aeon-gal-clear', function (e) {
    e.preventDefault();
    var box = $(this).closest('.aeon-img-gallery');
    box.find('input[type=hidden]').val('');
    box.find('.aeon-img-thumbs').empty();
    $(this).hide();
  });
});
JS;
	wp_add_inline_script( 'jquery-core', $js );
}
add_action( 'admin_enqueue_scripts', 'aeon_images_assets' );
