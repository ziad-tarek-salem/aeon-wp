<?php
/**
 * Services page texts — the banner and the repeated headings on that page,
 * managed from the dashboard (صفحة الخدمات → نصوص الصفحة).
 *
 * Everything else on the page comes from content the client already owns: each
 * service's name, description, intro, highlights and "what's included" are term
 * fields on محتوى الموقع → الخدمات, and its work samples are the galleries in
 * this same menu. These seven strings are what was left in code.
 *
 * Each string is its own option so the content sync can seed and reconcile it
 * (see aeon_content_settings()), and each falls back to the copy the theme
 * shipped with, so the page reads correctly before anything is ever saved.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AEON_SVC_TEXT_GROUP = 'aeon_services_text_group';

/**
 * The editable page strings: option key => label, hint, control and the copy the
 * theme ships with.
 *
 * @return array<string,array{label:string,hint:string,type:string,default:string}>
 */
function aeon_services_text_fields() {
	return array(
		'aeon_svc_page_title'   => array(
			'label'   => 'عنوان الصفحة',
			'hint'    => 'العنوان الكبير في أعلى صفحة الخدمات.',
			'type'    => 'text',
			'default' => 'كل ما تحتاجه علامتك التجارية',
		),
		'aeon_svc_page_sub'     => array(
			'label'   => 'العنوان الفرعي',
			'hint'    => 'السطر التوضيحي أسفل العنوان الكبير.',
			'type'    => 'textarea',
			'default' => 'باقة متكاملة من الخدمات الرقمية تحت سقف واحد — من التصوير والتصميم إلى التسويق وتطوير المواقع.',
		),
		'aeon_svc_kicker'       => array(
			'label'   => 'الكلمة قبل رقم الخدمة',
			'hint'    => 'تظهر فوق اسم كل خدمة متبوعةً برقمها، هكذا: «خدمة 01».',
			'type'    => 'text',
			'default' => 'خدمة',
		),
		'aeon_svc_highlights_t' => array(
			'label'   => 'عنوان قائمة المميزات',
			'hint'    => 'العنوان الصغير فوق نقاط «أبرز المميزات» في كل خدمة.',
			'type'    => 'text',
			'default' => 'أبرز المميزات',
		),
		'aeon_svc_includes_t'   => array(
			'label'   => 'عنوان قائمة ما تشمله الخدمة',
			'hint'    => 'العنوان الصغير فوق قائمة ما تشمله كل خدمة.',
			'type'    => 'text',
			'default' => 'ما تشمله الخدمة',
		),
		'aeon_svc_work_t'       => array(
			'label'   => 'عنوان شريط الأعمال',
			'hint'    => 'العنوان فوق صور الأعمال أسفل كل خدمة.',
			'type'    => 'text',
			'default' => 'من أعمالنا',
		),
		'aeon_svc_cta'          => array(
			'label'   => 'نص زر الطلب',
			'hint'    => 'نص الزر الذي يفتح واتساب برسالة تحمل اسم الخدمة.',
			'type'    => 'text',
			'default' => 'اطلب هذه الخدمة',
		),
	);
}

/**
 * One page string: the saved value, else the copy the theme ships with.
 *
 * @param string $key Option key from aeon_services_text_fields().
 * @return string
 */
function aeon_svc_text( $key ) {
	$fields = aeon_services_text_fields();
	if ( ! isset( $fields[ $key ] ) ) {
		return '';
	}

	$value = (string) get_option( $key, '' );

	return ( '' !== trim( $value ) ) ? $value : $fields[ $key ]['default'];
}

/** Echo a page string, escaped. */
function aeon_svc_text_e( $key ) {
	echo esc_html( aeon_svc_text( $key ) );
}

/* -------------------------------------------------------------------------
 * Settings
 * ---------------------------------------------------------------------- */

/** Register one option per string. */
function aeon_services_text_register() {
	foreach ( aeon_services_text_fields() as $key => $field ) {
		register_setting( AEON_SVC_TEXT_GROUP, $key, array(
			'type'              => 'string',
			'sanitize_callback' => ( 'textarea' === $field['type'] ) ? 'sanitize_textarea_field' : 'sanitize_text_field',
			'default'           => '',
		) );
	}
}
add_action( 'admin_init', 'aeon_services_text_register' );

/**
 * These strings sit in the same menu as the galleries and are edited by the
 * same people, so the group follows the menu's capability rather than
 * options.php's `manage_options` default.
 *
 * @return string
 */
function aeon_services_text_capability() {
	return 'upload_files';
}
add_filter( 'option_page_capability_' . AEON_SVC_TEXT_GROUP, 'aeon_services_text_capability' );

/** Render the screen. */
function aeon_services_text_page() {
	echo '<div class="wrap aeon-svc" dir="rtl" style="text-align:right">';
	echo '<h1>نصوص صفحة الخدمات</h1>';
	echo '<p class="aeon-svc__lead">النصوص الثابتة في صفحة الخدمات: عنوان الصفحة والعناوين الصغيرة المتكرّرة داخل كل خدمة. ';
	printf(
		'أما محتوى كل خدمة على حدة (الاسم، الوصف، النص التعريفي، المميزات، وما تشمله الخدمة) فيُعدَّل من <a href="%s">محتوى الموقع ← الخدمات</a>، وصورها من القائمة الجانبية هنا.</p>',
		esc_url( admin_url( 'edit-tags.php?taxonomy=aeon_service' ) )
	);

	echo '<form method="post" action="options.php">';
	settings_fields( AEON_SVC_TEXT_GROUP );
	echo '<table class="form-table" role="presentation"><tbody>';

	foreach ( aeon_services_text_fields() as $key => $field ) {
		$value = aeon_svc_text( $key );

		echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
		if ( 'textarea' === $field['type'] ) {
			printf(
				'<textarea id="%1$s" name="%1$s" rows="3" class="large-text">%2$s</textarea>',
				esc_attr( $key ),
				esc_textarea( $value )
			);
		} else {
			printf(
				'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text">',
				esc_attr( $key ),
				esc_attr( $value )
			);
		}
		echo '<p class="description">' . esc_html( $field['hint'] ) . '</p>';
		echo '</td></tr>';
	}

	echo '</tbody></table>';
	printf(
		'<p class="description">لمعاينة النتيجة افتح <a href="%s" target="_blank" rel="noopener">صفحة الخدمات ↗</a></p>',
		esc_url( trailingslashit( home_url( '/services/' ) ) )
	);
	submit_button( 'حفظ التغييرات' );
	echo '</form></div>';
}
