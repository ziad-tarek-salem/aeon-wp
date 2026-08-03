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
 * The three-tier icon control shared by every section that shows an icon:
 * pick from the brand set, upload a file, or paste a URL. Resolution order is
 * upload → URL → picked key (see aeon_card_icon()).
 *
 * @param string $pick_hint Hint shown under the picker.
 * @return array<string,array> Field configs keyed by meta suffix.
 */
function aeon_icon_fields( $pick_hint = 'اختر أيقونة من مجموعة الهوية. تُستخدم ما لم ترفع أيقونة مخصّصة بالأسفل.' ) {
	return array(
		'icon'     => array( 'type' => 'duoicon', 'label' => 'الأيقونة', 'hint' => $pick_hint ),
		'iconfile' => array(
			'type'  => 'image',
			'label' => 'أيقونة مخصّصة (رفع ملف)',
			'hint'  => 'ارفع ملف SVG أو PNG شفاف. يحلّ محل الأيقونة المختارة بالأعلى. المقاس المثالي 96×96 بكسل.',
			'mime'  => 'svg-image',
		),
		'iconurl'  => array(
			'type'        => 'url',
			'label'       => 'أو رابط أيقونة مباشر',
			'hint'        => 'رابط مباشر لملف SVG أو PNG. يُستخدم فقط عند عدم رفع ملف بالأعلى.',
			'placeholder' => 'https://example.com/icon.svg',
		),
	);
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
			'name_hint'     => 'يظهر كعنوان البطاقة في الصفحة الرئيسية وكعنوان القسم في صفحة الخدمات.',
			'show_desc'     => true,
			'desc_label'    => 'الوصف المختصر',
			'desc_hint'     => 'سطر أو سطران يظهران أسفل العنوان في بطاقة الصفحة الرئيسية.',
			// The three fields below fill this service's block on the Services
			// page; the name, description and icon above drive its home-page card.
			'fields'        => array_merge(
				array(
					'intro'    => array(
						'type'  => 'textarea',
						'label' => 'النص التعريفي (صفحة الخدمات)',
						'hint'  => 'الفقرة الطويلة التي تشرح الخدمة في صفحة الخدمات. اتركها فارغة ليُستخدم الوصف المختصر بدلاً منها.',
						'rows'  => 6,
					),
					'features' => array(
						'type'      => 'lines',
						'label'     => 'أبرز المميزات (صفحة الخدمات)',
						'hint'      => 'نقاط قصيرة تظهر بجانب النص التعريفي، كل نقطة في سطر مستقل. الترتيب هنا هو ترتيب ظهورها.',
						'add_label' => 'إضافة ميزة',
					),
					'includes' => array(
						'type'      => 'lines',
						'label'     => 'ما تشمله الخدمة (صفحة الخدمات)',
						'hint'      => 'قائمة ما تشمله الخدمة، كل عنصر في سطر مستقل.',
						'add_label' => 'إضافة عنصر',
					),
				),
				aeon_icon_fields( 'اختر أيقونة تمثّل الخدمة. تُستخدم ما لم ترفع أيقونة مخصّصة بالأسفل.' )
			),
		),

		'aeon_expertise' => array(
			'menu'          => 'نقاط الخبرة',
			'icon'          => 'dashicons-yes-alt',
			'labels'        => array(
				'name'          => 'نقاط الخبرة',
				'singular_name' => 'نقطة خبرة',
				'add_new_item'  => 'إضافة نقطة خبرة',
				'edit_item'     => 'تعديل النقطة',
				'update_item'   => 'تحديث النقطة',
				'new_item_name' => 'نص النقطة',
				'search_items'  => 'بحث',
				'not_found'     => 'لا توجد نقاط بعد',
				'back_to_items' => '→ كل النقاط',
			),
			'name_label'    => 'نص النقطة',
			'name_hint'     => 'الجملة التي تظهر بجانب الأيقونة في قسم «نبذة عن خبراتنا».',
			'show_desc'     => false,
			'fields'        => aeon_icon_fields( 'اختر أيقونة تمثّل نقطة الخبرة.' ),
		),

		'aeon_capability' => array(
			'menu'          => 'الشريط السفلي (مميزاتنا)',
			'icon'          => 'dashicons-star-filled',
			'labels'        => array(
				'name'          => 'مميزاتنا',
				'singular_name' => 'ميزة',
				'add_new_item'  => 'إضافة ميزة',
				'edit_item'     => 'تعديل الميزة',
				'update_item'   => 'تحديث الميزة',
				'new_item_name' => 'نص الميزة',
				'search_items'  => 'بحث',
				'not_found'     => 'لا توجد مميزات بعد',
				'back_to_items' => '→ كل المميزات',
			),
			'name_label'    => 'نص الميزة',
			'name_hint'     => 'يظهر في الشريط البنفسجي أسفل قسم «نبذة عن خبراتنا».',
			'show_desc'     => false,
			'fields'        => aeon_icon_fields( 'اختر أيقونة تمثّل الميزة.' ),
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

		'aeon_value' => array(
			'menu'          => 'رسالتنا وقيمنا',
			'icon'          => 'dashicons-heart',
			'labels'        => array(
				'name'          => 'القيم',
				'singular_name' => 'قيمة',
				'add_new_item'  => 'إضافة قيمة جديدة',
				'edit_item'     => 'تعديل القيمة',
				'update_item'   => 'تحديث القيمة',
				'new_item_name' => 'العنوان',
				'search_items'  => 'بحث',
				'not_found'     => 'لا توجد قيم بعد',
				'back_to_items' => '→ كل القيم',
			),
			'name_label'    => 'العنوان',
			'name_hint'     => 'اسم القيمة (مثال: النتائج).',
			'show_desc'     => true,
			'desc_label'    => 'الوصف',
			'desc_hint'     => 'نص مختصر يظهر أسفل العنوان داخل البطاقة.',
			'fields'        => array(
				'icon' => array( 'type' => 'icon', 'label' => 'الأيقونة', 'hint' => 'اختر أيقونة تمثّل القيمة.' ),
			),
		),

		'aeon_event' => array(
			'menu'          => 'المشاركات والفعاليات',
			'icon'          => 'dashicons-groups',
			'labels'        => array(
				'name'          => 'المشاركات والفعاليات',
				'singular_name' => 'مشاركة',
				'add_new_item'  => 'إضافة مشاركة جديدة',
				'edit_item'     => 'تعديل المشاركة',
				'update_item'   => 'تحديث المشاركة',
				'new_item_name' => 'وصف المشاركة',
				'search_items'  => 'بحث',
				'not_found'     => 'لا توجد مشاركات بعد',
				'back_to_items' => '→ كل المشاركات',
			),
			'name_label'    => 'وصف المشاركة',
			'name_hint'     => 'جملة تصف المشاركة أو الفعالية (تظهر أسفل الصورة).',
			'show_desc'     => false,
			'fields'        => array_merge(
				array(
					'image' => array( 'type' => 'image', 'label' => 'صورة المشاركة', 'hint' => 'صورة الفعالية (اختياري — تُستخدم صورة افتراضية عند تركها فارغة).' ),
				),
				aeon_icon_fields( 'اختر أيقونة تظهر فوق الصورة. تُستخدم أيقونة افتراضية مناسبة عند تركها فارغة.' )
			),
		),

		'aeon_industry' => array(
			'menu'          => 'القطاعات التي نخدمها',
			'icon'          => 'dashicons-category',
			'labels'        => array(
				'name'          => 'القطاعات',
				'singular_name' => 'قطاع',
				'add_new_item'  => 'إضافة قطاع جديد',
				'edit_item'     => 'تعديل القطاع',
				'update_item'   => 'تحديث القطاع',
				'new_item_name' => 'اسم القطاع',
				'search_items'  => 'بحث',
				'not_found'     => 'لا توجد قطاعات بعد',
				'back_to_items' => '→ كل القطاعات',
			),
			'name_label'    => 'اسم القطاع',
			'name_hint'     => 'القطاع كما يظهر في البروفايل (مثال: مطاعم، عطور، عقارات).',
			'show_desc'     => false,
			'fields'        => aeon_icon_fields( 'اختر أيقونة تمثّل القطاع.' ),
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
			'fields'        => array_merge(
				array(
					'number' => array( 'type' => 'number', 'label' => 'الرقم', 'hint' => 'الرقم الذي يَعُدّ إليه العدّاد.', 'placeholder' => 'مثال: 200' ),
					'suffix' => array(
						'type'    => 'select',
						'label'   => 'الرمز',
						'hint'    => 'رمز يظهر بعد الرقم مباشرة.',
						'options' => array( '' => 'بدون', '+' => '+ (زائد)', '%' => '٪ (نسبة مئوية)' ),
					),
				),
				aeon_icon_fields( 'اختر أيقونة تظهر أعلى الرقم.' )
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

		'aeon_branch' => array(
			'menu'          => 'الفروع والمواقع',
			'icon'          => 'dashicons-location',
			'labels'        => array(
				'name'          => 'الفروع والمواقع',
				'singular_name' => 'فرع',
				'add_new_item'  => 'إضافة فرع جديد',
				'edit_item'     => 'تعديل الفرع',
				'update_item'   => 'تحديث الفرع',
				'new_item_name' => 'اسم الفرع',
				'search_items'  => 'بحث في الفروع',
				'not_found'     => 'لا توجد فروع بعد',
				'back_to_items' => '→ كل الفروع',
			),
			'name_label'    => 'اسم الفرع',
			'name_hint'     => 'اسم الفرع كما سيظهر في الموقع (مثال: فرع دبي).',
			'show_desc'     => true,
			'desc_label'    => 'الوصف / العنوان',
			'desc_hint'     => 'وصف مختصر أو عنوان الفرع، يظهر في تذييل الموقع.',
			'fields'        => array_merge(
				array(
					'lat' => array( 'type' => 'decimal', 'label' => 'خط العرض (Latitude)', 'hint' => 'إحداثي خط العرض من خرائط جوجل. مثال: 25.204849', 'placeholder' => 'مثال: 25.204849' ),
					'lng' => array( 'type' => 'decimal', 'label' => 'خط الطول (Longitude)', 'hint' => 'إحداثي خط الطول من خرائط جوجل. للحصول على الإحداثيات: افتح خرائط جوجل، اضغط بزر الفأرة الأيمن على الموقع، ثم انسخ الرقمين الظاهرين.', 'placeholder' => 'مثال: 55.270783' ),
				),
				aeon_icon_fields( 'أيقونة الفرع. الافتراضي علامة الموقع.' )
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
