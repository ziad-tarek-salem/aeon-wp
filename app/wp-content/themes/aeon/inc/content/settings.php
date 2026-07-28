<?php
/**
 * WhatsApp button settings — a dashboard field for the floating WhatsApp
 * number, under the "محتوى الموقع" menu. No code edits needed to change it.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AEON_WA_OPTION = 'aeon_whatsapp_number';

/** Fallback WhatsApp number supplied by the client, used until one is saved. */
const AEON_WA_DEFAULT = '201119308101';

/**
 * The WhatsApp number for the floating button, digits only.
 * Reads the dashboard setting first, then the legacy Customizer field.
 *
 * @return string Intl digits (e.g. 971501234567), or '' when unset.
 */
function aeon_whatsapp_number() {
	$num = get_option( AEON_WA_OPTION, '' );
	if ( '' === $num ) {
		$num = aeon_opt( 'aeon_whatsapp' ); // backward-compat with the old Customizer field.
	}
	if ( '' === $num ) {
		// The client designated this number for WhatsApp only (July 2026).
		$num = AEON_WA_DEFAULT;
	}
	return preg_replace( '/\D+/', '', (string) $num );
}

/** Keep only digits when saving. */
function aeon_sanitize_whatsapp( $value ) {
	return preg_replace( '/\D+/', '', (string) $value );
}

/** Register the setting + field (Settings API). */
function aeon_whatsapp_register_setting() {
	register_setting( 'aeon_whatsapp_group', AEON_WA_OPTION, array(
		'type'              => 'string',
		'sanitize_callback' => 'aeon_sanitize_whatsapp',
		'default'           => '',
	) );

	add_settings_section( 'aeon_whatsapp_section', '', 'aeon_whatsapp_section_cb', 'aeon-whatsapp' );
	add_settings_field( AEON_WA_OPTION, 'رقم الواتساب', 'aeon_whatsapp_field_cb', 'aeon-whatsapp', 'aeon_whatsapp_section' );
}
add_action( 'admin_init', 'aeon_whatsapp_register_setting' );

function aeon_whatsapp_section_cb() {
	echo '<p style="max-width:640px">أدخل رقم الواتساب الذي سيفتحه زر الواتساب العائم الظاهر في جميع صفحات الموقع. أي تعديل هنا ينعكس مباشرةً على الموقع.</p>';
}

function aeon_whatsapp_field_cb() {
	$val = (string) get_option( AEON_WA_OPTION, '' );
	echo '<input type="text" name="' . esc_attr( AEON_WA_OPTION ) . '" value="' . esc_attr( $val ) . '" class="regular-text" placeholder="971501234567" style="direction:ltr;text-align:left">';
	echo '<p class="description">بصيغة دولية وبأرقام فقط، بدون «+» أو مسافات. مثال لرقم إماراتي: <code>971501234567</code></p>';
	if ( '' !== $val ) {
		$link = 'https://wa.me/' . $val;
		echo '<p class="description">معاينة الرابط: <a href="' . esc_url( $link ) . '" target="_blank" rel="noopener">' . esc_html( $link ) . '</a></p>';
	} else {
		echo '<p class="description" style="color:#b32d2e">لن يظهر زر الواتساب على الموقع حتى تُدخل رقماً وتحفظ.</p>';
	}
}

/** Render the settings page. */
function aeon_whatsapp_settings_page() {
	echo '<div class="wrap" dir="rtl" style="text-align:right">';
	echo '<h1>زر الواتساب</h1>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'aeon_whatsapp_group' );
	do_settings_sections( 'aeon-whatsapp' );
	submit_button( 'حفظ التغييرات' );
	echo '</form></div>';
}

/** Add the settings page under "محتوى الموقع" (after the content sections). */
function aeon_whatsapp_menu() {
	add_submenu_page( AEON_CONTENT_MENU, 'زر الواتساب', 'زر الواتساب', 'manage_options', 'aeon-whatsapp', 'aeon_whatsapp_settings_page' );
}
add_action( 'admin_menu', 'aeon_whatsapp_menu', 11 );

/* -------------------------------------------------------------------------- *
 *  About section photo — the skyline shot beside the expertise bullets.
 * -------------------------------------------------------------------------- */

const AEON_ABOUT_IMG_OPTION = 'aeon_about_image';

/**
 * Photo for the About/expertise section: the dashboard upload when set,
 * otherwise the image shipped with the theme.
 *
 * @return string Image URL.
 */
function aeon_about_image_url() {
	$id = (int) get_option( AEON_ABOUT_IMG_OPTION, 0 );
	if ( $id > 0 ) {
		$url = wp_get_attachment_image_url( $id, 'large' );
		if ( $url ) {
			return $url;
		}
	}
	return aeon_image_url( 'why_choose', 'large' );
}

/** Register the About-photo setting + field. */
function aeon_about_image_register_setting() {
	register_setting( 'aeon_about_image_group', AEON_ABOUT_IMG_OPTION, array(
		'type'              => 'integer',
		'sanitize_callback' => 'absint',
		'default'           => 0,
	) );
	add_settings_section( 'aeon_about_image_section', '', 'aeon_about_image_section_cb', 'aeon-about-image' );
	add_settings_field( AEON_ABOUT_IMG_OPTION, 'صورة القسم', 'aeon_about_image_field_cb', 'aeon-about-image', 'aeon_about_image_section' );
}
add_action( 'admin_init', 'aeon_about_image_register_setting' );

function aeon_about_image_section_cb() {
	echo '<p style="max-width:680px">الصورة الكبيرة التي تظهر بجانب نقاط الخبرة في قسم «نبذة عن خبراتنا» بالصفحة الرئيسية. '
		. 'يُفضّل صورة أفقية عالية الدقة (مثل صورة أفق دبي المستخدمة في ملف الشركة) بمقاس لا يقل عن 1200×960 بكسل.</p>';
}

function aeon_about_image_field_cb() {
	$id  = (int) get_option( AEON_ABOUT_IMG_OPTION, 0 );
	$url = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';

	echo '<div class="aeon-about-img">';
	echo '<input type="hidden" id="aeon-about-img-id" name="' . esc_attr( AEON_ABOUT_IMG_OPTION ) . '" value="' . esc_attr( $id ) . '">';
	echo '<div class="aeon-about-img-preview">' . ( $url ? '<img src="' . esc_url( $url ) . '" alt="">' : '' ) . '</div>';
	echo '<button type="button" class="button aeon-about-img-pick">اختيار صورة</button> ';
	echo '<button type="button" class="button aeon-about-img-clear"' . ( $id ? '' : ' style="display:none"' ) . '>إزالة</button>';
	echo '<p class="description">اتركها فارغة لاستخدام الصورة الافتراضية المرفقة مع القالب.</p>';
	echo '</div>';
}

/** Render the About-photo page. */
function aeon_about_image_settings_page() {
	echo '<div class="wrap" dir="rtl" style="text-align:right">';
	echo '<h1>صورة قسم «نبذة عن خبراتنا»</h1>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'aeon_about_image_group' );
	do_settings_sections( 'aeon-about-image' );
	submit_button( 'حفظ التغييرات' );
	echo '</form></div>';
}

function aeon_about_image_menu() {
	add_submenu_page( AEON_CONTENT_MENU, 'صورة قسم نبذة عن خبراتنا', 'صورة «نبذة عن خبراتنا»', 'manage_options', 'aeon-about-image', 'aeon_about_image_settings_page' );
}
add_action( 'admin_menu', 'aeon_about_image_menu', 12 );

/** Media frame for the About-photo page only. */
function aeon_about_image_assets( $hook ) {
	if ( false === strpos( (string) $hook, 'aeon-about-image' ) ) {
		return;
	}
	wp_enqueue_media();

	wp_register_style( 'aeon-about-img', false );
	wp_enqueue_style( 'aeon-about-img' );
	wp_add_inline_style( 'aeon-about-img', '.aeon-about-img-preview img{max-width:340px;height:auto;border-radius:10px;display:block;margin:.6em 0;box-shadow:0 4px 14px rgba(0,0,0,.12);}' );

	$js = <<<'JS'
jQuery(function($){
  var frame;
  $(document).on('click','.aeon-about-img-pick',function(e){
    e.preventDefault();
    frame=wp.media({title:'اختيار صورة القسم',button:{text:'استخدام الصورة'},library:{type:'image'},multiple:false});
    frame.on('select',function(){
      var a=frame.state().get('selection').first().toJSON();
      var u=(a.sizes&&a.sizes.medium)?a.sizes.medium.url:a.url;
      $('#aeon-about-img-id').val(a.id);
      $('.aeon-about-img-preview').html('<img src="'+u+'" alt="">');
      $('.aeon-about-img-clear').show();
    });
    frame.open();
  });
  $(document).on('click','.aeon-about-img-clear',function(e){
    e.preventDefault();
    $('#aeon-about-img-id').val(''); $('.aeon-about-img-preview').empty(); $(this).hide();
  });
});
JS;
	wp_add_inline_script( 'jquery-core', $js );
}
add_action( 'admin_enqueue_scripts', 'aeon_about_image_assets' );
