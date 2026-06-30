<?php
/**
 * Declarative configuration for the dashboard-managed home sections.
 *
 * Each section is a flat taxonomy edited on the compact single-screen term
 * editor (list + inline add form), modelled on the mostarstar layout:
 *   - the term NAME        = the card title
 *   - the term DESCRIPTION = the card body text (hidden where unused)
 *   - extra `fields`       = inline Arabic term-meta inputs (icon, number, …)
 *
 * Adding a new section later = add one entry here. Registration, the admin
 * fields, the menu item and the front-end getter all read from this array.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The section taxonomies, in display order.
 *
 * @return array<string,array>
 */
function aeon_sections_config() {
	static $config = null;
	if ( null !== $config ) {
		return $config;
	}

	$config = array(

		'aeon_service' => array(
			'menu'          => 'الخدمات',
			'icon'          => 'dashicons-screenoptions',
			'labels'        => array(
				'name'          => 'الخدمات',
				'singular_name' => 'خدمة',
				'add_new_item'  => 'إضافة خدمة جديدة',
				'edit_item'     => 'تعديل الخدمة',
				'update_item'   => 'تحديث الخدمة',
				'new_item_name' => 'اسم الخدمة',
				'search_items'  => 'بحث في الخدمات',
				'not_found'     => 'لا توجد خدمات بعد',
				'back_to_items' => '→ كل الخدمات',
			),
			'name_label'    => 'اسم الخدمة',
			'name_hint'     => 'يظهر كعنوان البطاقة.',
			'show_desc'     => true,
			'desc_label'    => 'الوصف',
			'desc_hint'     => 'وصف مختصر يظهر أسفل العنوان داخل البطاقة.',
			'fields'        => array(
				'icon' => array( 'type' => 'icon', 'label' => 'الأيقونة', 'hint' => 'اختر أيقونة تمثّل الخدمة.' ),
			),
		),

		'aeon_why' => array(
			'menu'          => 'لماذا نحن',
			'icon'          => 'dashicons-awards',
			'labels'        => array(
				'name'          => 'لماذا نحن',
				'singular_name' => 'سبب',
				'add_new_item'  => 'إضافة سبب جديد',
				'edit_item'     => 'تعديل السبب',
				'update_item'   => 'تحديث السبب',
				'new_item_name' => 'العنوان',
				'search_items'  => 'بحث',
				'not_found'     => 'لا توجد عناصر بعد',
				'back_to_items' => '→ كل العناصر',
			),
			'name_label'    => 'العنوان',
			'name_hint'     => 'يظهر كعنوان البطاقة.',
			'show_desc'     => true,
			'desc_label'    => 'الوصف',
			'desc_hint'     => 'نص مختصر يظهر أسفل العنوان داخل البطاقة.',
			'fields'        => array(
				'icon' => array( 'type' => 'icon', 'label' => 'الأيقونة', 'hint' => 'اختر أيقونة مناسبة.' ),
			),
		),

		'aeon_statistic' => array(
			'menu'          => 'الإحصائيات',
			'icon'          => 'dashicons-chart-bar',
			'labels'        => array(
				'name'          => 'الإحصائيات',
				'singular_name' => 'إحصائية',
				'add_new_item'  => 'إضافة إحصائية جديدة',
				'edit_item'     => 'تعديل الإحصائية',
				'update_item'   => 'تحديث الإحصائية',
				'new_item_name' => 'النص التوضيحي',
				'search_items'  => 'بحث',
				'not_found'     => 'لا توجد إحصائيات بعد',
				'back_to_items' => '→ كل الإحصائيات',
			),
			'name_label'    => 'النص التوضيحي',
			'name_hint'     => 'النص الذي يظهر أسفل الرقم (مثال: مشروع مكتمل).',
			'show_desc'     => false,
			'fields'        => array(
				'number' => array( 'type' => 'number', 'label' => 'الرقم', 'hint' => 'الرقم الذي يَعُدّ إليه العدّاد.', 'placeholder' => 'مثال: 200' ),
				'suffix' => array(
					'type'    => 'select',
					'label'   => 'الرمز',
					'hint'    => 'رمز يظهر بعد الرقم مباشرة.',
					'options' => array( '' => 'بدون', '+' => '+ (زائد)', '%' => '٪ (نسبة مئوية)' ),
				),
			),
		),

		'aeon_review' => array(
			'menu'          => 'آراء العملاء',
			'icon'          => 'dashicons-format-quote',
			'labels'        => array(
				'name'          => 'آراء العملاء',
				'singular_name' => 'رأي',
				'add_new_item'  => 'إضافة رأي جديد',
				'edit_item'     => 'تعديل الرأي',
				'update_item'   => 'تحديث الرأي',
				'new_item_name' => 'اسم العميل',
				'search_items'  => 'بحث في الآراء',
				'not_found'     => 'لا توجد آراء بعد',
				'back_to_items' => '→ كل الآراء',
			),
			'name_label'    => 'اسم العميل',
			'name_hint'     => 'اسم العميل صاحب الرأي.',
			'show_desc'     => true,
			'desc_label'    => 'نص الرأي',
			'desc_hint'     => 'نص الرأي كما سيظهر بين علامتي اقتباس.',
			'fields'        => array(
				'role'  => array( 'type' => 'text', 'label' => 'المسمى الوظيفي / الشركة', 'hint' => 'مثال: مديرة تسويق، نوفا.', 'placeholder' => 'مثال: مديرة تسويق، نوفا' ),
				'image' => array( 'type' => 'image', 'label' => 'صورة العميل', 'hint' => 'صورة تظهر بجانب الاسم (اختياري).' ),
			),
		),

	);

	return $config;
}

/**
 * The taxonomy slugs we manage, in order.
 *
 * @return string[]
 */
function aeon_section_taxonomies() {
	return array_keys( aeon_sections_config() );
}
