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
