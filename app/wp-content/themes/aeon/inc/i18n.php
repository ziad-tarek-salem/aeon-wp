<?php
/**
 * Lightweight self-contained bilingual layer (Arabic-first / English).
 *
 * Language is resolved from ?lang=en|ar (which sets a cookie) or the
 * aeon_lang cookie, defaulting to Arabic. All UI + content strings live in
 * one array so the theme is fully bilingual without a paid plugin.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve and persist the active language.
 *
 * @return string 'ar' or 'en'.
 */
function aeon_lang() {
	static $lang = null;
	if ( null !== $lang ) {
		return $lang;
	}

	$allowed = array( 'ar', 'en' );

	if ( isset( $_GET['lang'] ) && in_array( $_GET['lang'], $allowed, true ) ) {
		$lang = sanitize_key( $_GET['lang'] );
		if ( ! headers_sent() ) {
			setcookie( 'aeon_lang', $lang, time() + YEAR_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN );
		}
		$_COOKIE['aeon_lang'] = $lang;
		return $lang;
	}

	if ( isset( $_COOKIE['aeon_lang'] ) && in_array( $_COOKIE['aeon_lang'], $allowed, true ) ) {
		$lang = sanitize_key( $_COOKIE['aeon_lang'] );
		return $lang;
	}

	$lang = 'ar'; // Arabic-first default.
	return $lang;
}

/**
 * Is the current language right-to-left?
 */
function aeon_is_rtl() {
	return 'ar' === aeon_lang();
}

/**
 * Document direction.
 */
function aeon_dir() {
	return aeon_is_rtl() ? 'rtl' : 'ltr';
}

/**
 * URL to switch to the opposite language, preserving the current page.
 */
function aeon_switch_url() {
	$target = aeon_is_rtl() ? 'en' : 'ar';
	// add_query_arg( array() ) returns the current request URI (path + query),
	// so we only need to set/replace the lang param on it.
	return esc_url( add_query_arg( 'lang', $target ) );
}

/**
 * Translate a key for the active language.
 *
 * @param string $key Dot/flat key from the strings table.
 * @return string
 */
function aeon_t( $key ) {
	$strings = aeon_strings();
	$lang    = aeon_lang();
	if ( isset( $strings[ $key ][ $lang ] ) ) {
		return $strings[ $key ][ $lang ];
	}
	if ( isset( $strings[ $key ]['en'] ) ) {
		return $strings[ $key ]['en'];
	}
	return $key;
}

/**
 * Echo a translated key.
 */
function aeon_e( $key ) {
	echo wp_kses_post( aeon_t( $key ) );
}

/**
 * The full bilingual string table.
 *
 * @return array
 */
function aeon_strings() {
	static $s = null;
	if ( null !== $s ) {
		return $s;
	}

	$s = array(
		// Global / nav.
		'site_tagline'      => array( 'ar' => 'القوة الرقمية لمستقبل الميديا', 'en' => 'Your Partner in Digital Success' ),
		'nav_home'          => array( 'ar' => 'الرئيسية', 'en' => 'Home' ),
		'nav_about'         => array( 'ar' => 'من نحن', 'en' => 'About' ),
		'nav_services'      => array( 'ar' => 'خدماتنا', 'en' => 'Services' ),
		'nav_work'          => array( 'ar' => 'أعمالنا', 'en' => 'Work' ),
		'nav_blog'          => array( 'ar' => 'المدونة', 'en' => 'Blog' ),
		'nav_contact'       => array( 'ar' => 'تواصل معنا', 'en' => 'Contact' ),
		'cta_request'       => array( 'ar' => 'اطلب خدمتك', 'en' => 'Request a Service' ),
		'cta_start'         => array( 'ar' => 'ابدأ مشروعك', 'en' => 'Start Your Project' ),
		'lang_switch'       => array( 'ar' => 'EN', 'en' => 'ع' ),
		'skip_to_content'   => array( 'ar' => 'تخطَّ إلى المحتوى', 'en' => 'Skip to content' ),

		// Hero.
		'hero_eyebrow'      => array( 'ar' => 'استراتيجيات ذكية | نمو حقيقي | أثر دائم', 'en' => 'Smart Strategies | Real Growth | Lasting Impact' ),
		'hero_title_1'      => array( 'ar' => 'أطلق', 'en' => 'Unlock' ),
		'hero_title_2'      => array( 'ar' => 'إمكانات', 'en' => "Your Brand's" ),
		'hero_title_3'      => array( 'ar' => 'علامتك التجارية', 'en' => 'Potential' ),
		'hero_sub'          => array( 'ar' => 'حلول إبداعية ونتائج مبنية على البيانات تنقل علامتك التجارية إلى المستوى التالي.', 'en' => 'Creative solutions and data-driven results that take your brand to the next level.' ),
		'hero_cta_primary'  => array( 'ar' => 'لنصنع شيئاً مذهلاً معاً', 'en' => "Let's Build Something Amazing" ),
		'hero_cta_secondary'=> array( 'ar' => 'استكشف خدماتنا', 'en' => 'Explore Our Services' ),

		// About.
		'about_eyebrow'     => array( 'ar' => 'من نحن', 'en' => 'About AEON' ),
		'about_title'       => array( 'ar' => 'نُنمّي العلامات التجارية التي تُنمّي الأعمال', 'en' => 'We Grow Brands That Grow Businesses' ),
		'about_text'        => array( 'ar' => 'في AEON للتسويق الرقمي نمزج الإبداع بالبيانات لنصنع تجارب علامة تجارية لا تُنسى. من التصوير والتصميم إلى التسويق وتطوير المواقع، نقدّم حلولاً متكاملة تحقق نتائج قابلة للقياس.', 'en' => 'At AEON Digital Marketing we blend creativity with data to craft unforgettable brand experiences. From photography and design to marketing and web development, we deliver integrated solutions that drive measurable results.' ),
		'about_mission_t'   => array( 'ar' => 'رسالتنا', 'en' => 'Our Mission' ),
		'about_mission'     => array( 'ar' => 'تمكين العلامات التجارية من النمو عبر استراتيجيات رقمية ذكية وإبداع بلا حدود.', 'en' => 'Empower brands to grow through smart digital strategies and boundless creativity.' ),
		'about_vision_t'    => array( 'ar' => 'رؤيتنا', 'en' => 'Our Vision' ),
		'about_vision'      => array( 'ar' => 'أن نكون الشريك الرقمي الأول لكل علامة تجارية طموحة في المنطقة.', 'en' => 'To be the leading digital partner for every ambitious brand in the region.' ),
		'about_more'        => array( 'ar' => 'اعرف المزيد عنا', 'en' => 'More About Us' ),

		// Services.
		'services_eyebrow'  => array( 'ar' => 'خدماتنا', 'en' => 'Our Services' ),
		'services_title'    => array( 'ar' => 'كل ما تحتاجه علامتك التجارية', 'en' => 'Everything Your Brand Needs' ),
		'services_sub'      => array( 'ar' => 'باقة متكاملة من الخدمات الرقمية تحت سقف واحد.', 'en' => 'A full suite of digital services under one roof.' ),
		'svc_learn'         => array( 'ar' => 'التفاصيل', 'en' => 'Learn more' ),

		'svc_photo_t'       => array( 'ar' => 'التصوير الاحترافي', 'en' => 'Professional Photography' ),
		'svc_photo_d'       => array( 'ar' => 'تصوير عالي الجودة يُبرز علامتك التجارية في أبهى صورها.', 'en' => 'High-quality photography that captures your brand at its best.' ),
		'svc_design_t'      => array( 'ar' => 'التصميم الجرافيكي', 'en' => 'Graphic Design' ),
		'svc_design_d'      => array( 'ar' => 'تصاميم إبداعية تبني هويتك وتترك انطباعاً يدوم.', 'en' => 'Creative designs that build your identity and leave a lasting impression.' ),
		'svc_video_t'       => array( 'ar' => 'المونتاج وصناعة الفيديو', 'en' => 'Video Editing & Production' ),
		'svc_video_d'       => array( 'ar' => 'مونتاج احترافي يروي قصتك بأسلوب يأسر الجمهور.', 'en' => 'Professional video editing that brings your story to life.' ),
		'svc_marketing_t'   => array( 'ar' => 'التسويق الرقمي', 'en' => 'Digital Marketing' ),
		'svc_marketing_d'   => array( 'ar' => 'استراتيجيات مبنية على البيانات تحقق نتائج حقيقية.', 'en' => 'Data-driven strategies that deliver real results.' ),
		'svc_social_t'      => array( 'ar' => 'إدارة السوشيال ميديا', 'en' => 'Social Media Management' ),
		'svc_social_d'      => array( 'ar' => 'ابنِ حضورك وتفاعل مع جمهورك بفعالية.', 'en' => 'Build your presence and engage your audience effectively.' ),
		'svc_brand_t'       => array( 'ar' => 'بناء الهوية التجارية', 'en' => 'Branding' ),
		'svc_brand_d'       => array( 'ar' => 'هوية قوية تجعل علامتك التجارية تتميز.', 'en' => 'Strong branding that makes your business stand out.' ),
		'svc_web_t'         => array( 'ar' => 'تصميم وتطوير المواقع', 'en' => 'Web Development' ),
		'svc_web_d'         => array( 'ar' => 'مواقع عصرية ومتجاوبة تحوّل الزوار إلى عملاء.', 'en' => 'Modern, responsive websites that convert visitors into customers.' ),
		'svc_analytics_t'   => array( 'ar' => 'تحليل الأداء والتقارير', 'en' => 'Analytics & Reporting' ),
		'svc_analytics_d'   => array( 'ar' => 'قياس دقيق وتقارير واضحة لتحسين كل قرار.', 'en' => 'Precise measurement and clear reports to improve every decision.' ),

		// Why choose us.
		'why_eyebrow'       => array( 'ar' => 'لماذا نحن', 'en' => 'Why Choose Us' ),
		'why_title'         => array( 'ar' => 'شريكك الموثوق في النجاح الرقمي', 'en' => 'Your Trusted Partner in Digital Success' ),
		'why_1_t'           => array( 'ar' => 'موجّهون بالنتائج', 'en' => 'Result Driven' ),
		'why_1_d'           => array( 'ar' => 'نركّز على ما يهم: نمو حقيقي وقابل للقياس.', 'en' => 'We focus on what matters: real, measurable growth.' ),
		'why_2_t'           => array( 'ar' => 'مدعومون بالبيانات', 'en' => 'Data Powered' ),
		'why_2_d'           => array( 'ar' => 'كل قرار مدعوم برؤى وتحليلات دقيقة.', 'en' => 'Every decision backed by precise insights and analytics.' ),
		'why_3_t'           => array( 'ar' => 'حلول إبداعية', 'en' => 'Creative Solutions' ),
		'why_3_d'           => array( 'ar' => 'أفكار جريئة تجعل علامتك التجارية لا تُنسى.', 'en' => 'Bold ideas that make your brand unforgettable.' ),
		'why_4_t'           => array( 'ar' => 'فريق خبير', 'en' => 'Expert Team' ),
		'why_4_d'           => array( 'ar' => 'محترفون شغوفون بالتميّز في كل تفصيل.', 'en' => 'Professionals passionate about excellence in every detail.' ),
		'why_5_t'           => array( 'ar' => 'شريك موثوق', 'en' => 'Trusted Partner' ),
		'why_5_d'           => array( 'ar' => 'علاقات طويلة الأمد مبنية على الثقة والشفافية.', 'en' => 'Long-term relationships built on trust and transparency.' ),

		// Stats.
		'stat_projects'     => array( 'ar' => 'مشروع مكتمل', 'en' => 'Projects Completed' ),
		'stat_clients'      => array( 'ar' => 'عميل سعيد', 'en' => 'Happy Clients' ),
		'stat_growth'       => array( 'ar' => 'متوسط نمو العملاء', 'en' => 'Average Client Growth' ),
		'stat_satisfaction' => array( 'ar' => 'رضا العملاء', 'en' => 'Client Satisfaction' ),
		'stat_years'        => array( 'ar' => 'سنوات خبرة', 'en' => 'Years of Experience' ),

		// Portfolio.
		'work_eyebrow'      => array( 'ar' => 'أعمالنا', 'en' => 'Our Work' ),
		'work_title'        => array( 'ar' => 'أعمالنا تتحدث عنا', 'en' => 'Our Work Speaks' ),
		'work_sub'          => array( 'ar' => 'لمحة من المشاريع التي نفخر بها.', 'en' => 'A glimpse of the projects we are proud of.' ),
		'work_all'          => array( 'ar' => 'الكل', 'en' => 'All' ),
		'work_view'         => array( 'ar' => 'عرض المشروع', 'en' => 'View Project' ),
		'work_view_all'     => array( 'ar' => 'شاهد كل الأعمال', 'en' => 'View All Work' ),
		'work_empty'        => array( 'ar' => 'سيتم إضافة المشاريع قريباً.', 'en' => 'Projects coming soon.' ),

		// Testimonials.
		'tst_eyebrow'       => array( 'ar' => 'آراء العملاء', 'en' => 'Testimonials' ),
		'tst_title'         => array( 'ar' => 'ماذا يقول عملاؤنا', 'en' => 'What Our Clients Say' ),

		// Partners.
		'partners_title'    => array( 'ar' => 'موثوقون من قِبل علامات رائدة', 'en' => 'Trusted by Leading Brands' ),

		// CTA band.
		'cta_band_title'    => array( 'ar' => 'لنبنِ شيئاً مذهلاً معاً', 'en' => "Let's Build Something Amazing Together" ),
		'cta_band_sub'      => array( 'ar' => 'رؤيتك، استراتيجيتنا، نتائج حقيقية.', 'en' => 'Your vision. Our strategy. Real results.' ),

		// Contact.
		'contact_eyebrow'   => array( 'ar' => 'تواصل معنا', 'en' => 'Get in Touch' ),
		'contact_title'     => array( 'ar' => 'جاهزون لبدء مشروعك القادم', 'en' => 'Ready to Start Your Next Project' ),
		'contact_sub'       => array( 'ar' => 'أخبرنا عن مشروعك وسنعود إليك خلال 24 ساعة.', 'en' => 'Tell us about your project and we will get back within 24 hours.' ),
		'form_name'         => array( 'ar' => 'الاسم', 'en' => 'Name' ),
		'form_email'        => array( 'ar' => 'البريد الإلكتروني', 'en' => 'Email' ),
		'form_phone'        => array( 'ar' => 'رقم الهاتف', 'en' => 'Phone' ),
		'form_service'      => array( 'ar' => 'الخدمة المطلوبة', 'en' => 'Service Needed' ),
		'form_message'      => array( 'ar' => 'رسالتك', 'en' => 'Your Message' ),
		'form_send'         => array( 'ar' => 'إرسال الطلب', 'en' => 'Send Request' ),
		'form_sending'      => array( 'ar' => 'جارٍ الإرسال...', 'en' => 'Sending...' ),
		'form_success'      => array( 'ar' => 'شكراً لك! تم استلام رسالتك وسنتواصل معك قريباً.', 'en' => 'Thank you! Your message was received and we will be in touch soon.' ),
		'form_error'        => array( 'ar' => 'حدث خطأ ما. يرجى المحاولة مرة أخرى.', 'en' => 'Something went wrong. Please try again.' ),
		'form_required'     => array( 'ar' => 'يرجى تعبئة الحقول المطلوبة.', 'en' => 'Please fill in the required fields.' ),
		'contact_email_l'   => array( 'ar' => 'البريد الإلكتروني', 'en' => 'Email' ),
		'contact_phone_l'   => array( 'ar' => 'الهاتف', 'en' => 'Phone' ),
		'contact_addr_l'    => array( 'ar' => 'العنوان', 'en' => 'Address' ),

		// Footer.
		'footer_about'      => array( 'ar' => 'AEON للتسويق الرقمي — وكالة إبداعية مقرها الإمارات العربية المتحدة، نصنع النجاح الرقمي لعلامتك التجارية.', 'en' => 'AEON Digital Marketing — a UAE-based creative agency crafting digital success for your brand.' ),
		'footer_links'      => array( 'ar' => 'روابط سريعة', 'en' => 'Quick Links' ),
		'footer_services'   => array( 'ar' => 'خدماتنا', 'en' => 'Services' ),
		'footer_contact'    => array( 'ar' => 'تواصل', 'en' => 'Contact' ),
		'footer_newsletter' => array( 'ar' => 'النشرة البريدية', 'en' => 'Newsletter' ),
		'footer_news_sub'   => array( 'ar' => 'اشترك لتصلك آخر الأخبار والعروض.', 'en' => 'Subscribe for the latest news and offers.' ),
		'footer_subscribe'  => array( 'ar' => 'اشتراك', 'en' => 'Subscribe' ),
		'footer_rights'     => array( 'ar' => 'جميع الحقوق محفوظة.', 'en' => 'All rights reserved.' ),
		'footer_uae'        => array( 'ar' => 'بفخر من الإمارات العربية المتحدة', 'en' => 'Proudly based in the UAE' ),
		'footer_locations'  => array( 'ar' => 'فروعنا ومواقعنا', 'en' => 'Our Locations' ),
		'open_in_maps'      => array( 'ar' => 'افتح الموقع على خرائط جوجل', 'en' => 'Open in Google Maps' ),

		// 404 / misc.
		'e404_title'        => array( 'ar' => 'الصفحة غير موجودة', 'en' => 'Page Not Found' ),
		'e404_text'        => array( 'ar' => 'عذراً، الصفحة التي تبحث عنها غير موجودة.', 'en' => 'Sorry, the page you are looking for does not exist.' ),
		'back_home'         => array( 'ar' => 'العودة للرئيسية', 'en' => 'Back to Home' ),
		'read_more'         => array( 'ar' => 'اقرأ المزيد', 'en' => 'Read more' ),
		'blog_title'        => array( 'ar' => 'المدونة وآخر الأخبار', 'en' => 'Blog & Latest News' ),
	);

	return $s;
}
