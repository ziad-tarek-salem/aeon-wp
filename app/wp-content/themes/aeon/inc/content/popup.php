<?php
/**
 * Welcome popup controls — the greeting dialog's image and its on/off switch,
 * under the "محتوى الموقع" menu.
 *
 * Both settings are deliberately absent by default rather than seeded: an
 * install that has never opened this screen keeps the popup on and shows the
 * lockup shipped with the theme, which is exactly how the popup behaved before
 * these controls existed. Nothing changes until someone changes it.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AEON_POPUP_ENABLED_OPTION = 'aeon_popup_enabled';
const AEON_POPUP_IMAGE_OPTION   = 'aeon_popup_image';

/**
 * Whether the greeting dialog may render.
 *
 * Defaults to on, so the switch only ever turns something off that was already
 * running — an install upgrading to this version sees no change.
 *
 * @return bool
 */
function aeon_popup_enabled() {
	return '1' === (string) get_option( AEON_POPUP_ENABLED_OPTION, '1' );
}

/**
 * The popup's image: the dashboard upload when set, otherwise the lockup that
 * ships with the theme.
 *
 * Real intrinsic dimensions travel with the URL because the markup prints them
 * as width/height attributes. The card sizes the image by CSS (height 66px,
 * width auto), so these do not affect the design — they reserve the right box
 * before the file loads. Carrying over the theme lockup's 816×294 for an upload
 * of another shape would reserve the wrong one and jump the card on load.
 *
 * @return array{url:string,width:int,height:int}
 */
function aeon_popup_image() {
	$id = (int) get_option( AEON_POPUP_IMAGE_OPTION, 0 );

	if ( $id > 0 ) {
		$src = wp_get_attachment_image_src( $id, 'full' );
		if ( $src ) {
			return array(
				'url'    => $src[0],
				'width'  => (int) $src[1],
				'height' => (int) $src[2],
			);
		}
	}

	// The theme's own lockup, at the size the popup has always used.
	return array(
		'url'    => aeon_image_url( 'logo_lockup', 'full' ),
		'width'  => 816,
		'height' => 294,
	);
}

/* -------------------------------------------------------------------------- *
 *  Settings screen
 * -------------------------------------------------------------------------- */

/** Register both settings and their fields. */
function aeon_popup_register_settings() {
	register_setting( 'aeon_popup_group', AEON_POPUP_ENABLED_OPTION, array(
		'type'              => 'string',
		'sanitize_callback' => 'aeon_sanitize_popup_enabled',
		'default'           => '1',
	) );

	register_setting( 'aeon_popup_group', AEON_POPUP_IMAGE_OPTION, array(
		'type'              => 'integer',
		'sanitize_callback' => 'absint',
		'default'           => 0,
	) );

	add_settings_section( 'aeon_popup_section', '', 'aeon_popup_section_cb', 'aeon-popup' );
	add_settings_field( AEON_POPUP_ENABLED_OPTION, 'إظهار النافذة', 'aeon_popup_enabled_field_cb', 'aeon-popup', 'aeon_popup_section' );
	add_settings_field( AEON_POPUP_IMAGE_OPTION, 'صورة النافذة', 'aeon_popup_image_field_cb', 'aeon-popup', 'aeon_popup_section' );
}
add_action( 'admin_init', 'aeon_popup_register_settings' );

/**
 * A checkbox posts nothing when it is off, which on its own would leave the old
 * value standing and make the switch impossible to turn off. The field pairs it
 * with a hidden "0", so something always arrives here.
 *
 * @param mixed $value Submitted value.
 * @return string '1' or '0'.
 */
function aeon_sanitize_popup_enabled( $value ) {
	return '1' === (string) $value ? '1' : '0';
}

function aeon_popup_section_cb() {
	echo '<p style="max-width:680px">النافذة الترحيبية التي تظهر تلقائياً عند فتح الصفحة الرئيسية، وتحمل شعار الشركة وزر التواصل عبر الواتساب. '
		. 'تظهر مرة واحدة لكل زيارة، وفي الصفحة الرئيسية فقط.</p>';
}

function aeon_popup_enabled_field_cb() {
	$on = aeon_popup_enabled();

	// Hidden "0" first: see aeon_sanitize_popup_enabled().
	echo '<input type="hidden" name="' . esc_attr( AEON_POPUP_ENABLED_OPTION ) . '" value="0">';
	echo '<label><input type="checkbox" name="' . esc_attr( AEON_POPUP_ENABLED_OPTION ) . '" value="1" ' . checked( $on, true, false ) . '> ';
	echo 'إظهار النافذة الترحيبية على الموقع</label>';
	echo '<p class="description">عند إلغاء التحديد لن تظهر النافذة نهائياً لأي زائر. باقي أجزاء الموقع لا تتأثر، ويظل زر الواتساب العائم كما هو.</p>';
}

function aeon_popup_image_field_cb() {
	$id  = (int) get_option( AEON_POPUP_IMAGE_OPTION, 0 );
	$url = $id ? wp_get_attachment_image_url( $id, 'medium' ) : aeon_image_url( 'logo_lockup', 'full' );

	// The theme lockup travels with the markup so "restore default" can repaint
	// the preview without a round trip.
	echo '<div class="aeon-popup-img" data-default="' . esc_url( aeon_image_url( 'logo_lockup', 'full' ) ) . '">';
	echo '<input type="hidden" id="aeon-popup-img-id" name="' . esc_attr( AEON_POPUP_IMAGE_OPTION ) . '" value="' . esc_attr( $id ) . '">';
	echo '<div class="aeon-popup-img-preview"><img src="' . esc_url( $url ) . '" alt=""></div>';
	echo '<button type="button" class="button aeon-popup-img-pick">تغيير الصورة</button> ';
	echo '<button type="button" class="button aeon-popup-img-clear"' . ( $id ? '' : ' style="display:none"' ) . '>العودة للشعار الافتراضي</button>';
	echo '<p class="description">الشعار الافتراضي المرفق مع القالب مستخدم حالياً ما لم تختر صورة أخرى. '
		. 'يُفضّل صورة أفقية بخلفية شفافة (PNG)؛ يظهر منها ارتفاع ثابت 66 بكسل والعرض يتناسب معه تلقائياً.</p>';
	echo '</div>';
}

/** Render the popup settings page. */
function aeon_popup_settings_page() {
	echo '<div class="wrap" dir="rtl" style="text-align:right">';
	echo '<h1>النافذة الترحيبية</h1>';
	echo '<form method="post" action="options.php">';
	settings_fields( 'aeon_popup_group' );
	do_settings_sections( 'aeon-popup' );
	submit_button( 'حفظ التغييرات' );
	echo '</form></div>';
}

/**
 * Add the page under "محتوى الموقع".
 *
 * Skipped while that menu carries nothing else, because then the parent opens
 * this screen directly (see aeon_content_menu()) and a submenu would only
 * repeat it. Bring any section back and this returns as a normal entry.
 */
function aeon_popup_menu() {
	if ( ! aeon_content_submenus() ) {
		return;
	}
	add_submenu_page( AEON_CONTENT_MENU, 'النافذة الترحيبية', 'النافذة الترحيبية', 'manage_options', 'aeon-popup', 'aeon_popup_settings_page' );
}
add_action( 'admin_menu', 'aeon_popup_menu', 10 );

/**
 * Media frame for the popup page only.
 *
 * Two slugs to match: its own submenu, and the parent menu that renders this
 * screen when it is the only one left. Missing the second would load the page
 * without wp.media and leave "تغيير الصورة" doing nothing.
 */
function aeon_popup_assets( $hook ) {
	$hook = (string) $hook;

	if ( false === strpos( $hook, 'aeon-popup' ) && false === strpos( $hook, AEON_CONTENT_MENU ) ) {
		return;
	}
	wp_enqueue_media();

	wp_register_style( 'aeon-popup-img', false );
	wp_enqueue_style( 'aeon-popup-img' );
	wp_add_inline_style(
		'aeon-popup-img',
		// Checkerboard, so a transparent PNG reads as transparent instead of
		// looking like it has a white background baked in.
		'.aeon-popup-img-preview{display:inline-block;margin:.6em 0;padding:14px 18px;border-radius:10px;'
		. 'background-color:#fff;background-image:linear-gradient(45deg,#eee 25%,transparent 25%,transparent 75%,#eee 75%),'
		. 'linear-gradient(45deg,#eee 25%,transparent 25%,transparent 75%,#eee 75%);'
		. 'background-size:16px 16px;background-position:0 0,8px 8px;box-shadow:0 4px 14px rgba(0,0,0,.12);}'
		. '.aeon-popup-img-preview img{max-width:340px;height:auto;display:block;}'
	);

	$js = <<<'JS'
jQuery(function($){
  var frame;
  $(document).on('click','.aeon-popup-img-pick',function(e){
    e.preventDefault();
    frame=wp.media({title:'اختيار صورة النافذة',button:{text:'استخدام الصورة'},library:{type:'image'},multiple:false});
    frame.on('select',function(){
      var a=frame.state().get('selection').first().toJSON();
      var u=(a.sizes&&a.sizes.medium)?a.sizes.medium.url:a.url;
      $('#aeon-popup-img-id').val(a.id);
      $('.aeon-popup-img-preview').html('<img src="'+u+'" alt="">');
      $('.aeon-popup-img-clear').show();
    });
    frame.open();
  });
  $(document).on('click','.aeon-popup-img-clear',function(e){
    e.preventDefault();
    $('#aeon-popup-img-id').val('0');
    $(this).hide();
    var d=$('.aeon-popup-img').data('default');
    if(d){ $('.aeon-popup-img-preview').html('<img src="'+d+'" alt="">'); }
  });
});
JS;
	wp_add_inline_script( 'jquery-core', $js );
}
add_action( 'admin_enqueue_scripts', 'aeon_popup_assets' );
