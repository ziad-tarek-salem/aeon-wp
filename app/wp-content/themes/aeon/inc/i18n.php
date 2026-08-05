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
		'site_tagline'      => array( 'ar' => 'القوة الرقمية لمستقبل الميديا', 'en' => 'The Digital Power for the Future of Media' ),
		'nav_home'          => array( 'ar' => 'الرئيسية', 'en' => 'Home' ),
		'nav_about'         => array( 'ar' => 'من نحن', 'en' => 'About' ),
		'nav_services'      => array( 'ar' => 'خدماتنا', 'en' => 'Services' ),
		'nav_work'          => array( 'ar' => 'أعمالنا', 'en' => 'Work' ),
		'nav_blog'          => array( 'ar' => 'المدونة', 'en' => 'Blog' ),
		'nav_events'        => array( 'ar' => 'فعالياتنا', 'en' => 'Events' ),
		'nav_clients'       => array( 'ar' => 'شركاء النجاح', 'en' => 'Success Partners' ),
		'nav_office'        => array( 'ar' => 'زورونا', 'en' => 'Visit Us' ),
		'nav_contact'       => array( 'ar' => 'تواصل معنا', 'en' => 'Contact' ),
		'cta_request'       => array( 'ar' => 'اطلب خدمتك', 'en' => 'Request a Service' ),
		'cta_start'         => array( 'ar' => 'ابدأ مشروعك', 'en' => 'Start Your Project' ),
		'lang_switch'       => array( 'ar' => 'EN', 'en' => 'ع' ),
		'skip_to_content'   => array( 'ar' => 'تخطَّ إلى المحتوى', 'en' => 'Skip to content' ),

		// Hero.
		'hero_eyebrow'      => array( 'ar' => 'استهداف دقيق • نتائج قابلة للقياس • نمو مستمر', 'en' => 'Precise Targeting • Measurable Results • Sustained Growth' ),
		'hero_title_1'      => array( 'ar' => 'نصنع', 'en' => 'We Craft' ),
		'hero_title_2'      => array( 'ar' => 'الفارق الرقمي', 'en' => 'Digital Impact' ),
		'hero_title_3'      => array( 'ar' => 'لمستقبل علامتك', 'en' => 'For Your Brand' ),
		'hero_sub'          => array( 'ar' => 'شركة تسويق رقمي متكاملة بخبرة تتجاوز عشر سنوات في الإمارات ومصر والسعودية والكويت — نقدّم حلولاً تسويقية مبتكرة تصنع الفارق وتحقق نجاحاً رقمياً مستداماً.', 'en' => 'A full-service digital marketing agency with 10+ years of experience across the UAE, Egypt, Saudi Arabia and Kuwait — delivering innovative solutions that make a difference and drive sustainable digital success.' ),
		'hero_cta_primary'  => array( 'ar' => 'لنصنع النجاح معاً', 'en' => "Let's Build Success Together" ),
		'hero_cta_secondary'=> array( 'ar' => 'استكشف خدماتنا', 'en' => 'Explore Our Services' ),

		// About.
		'about_eyebrow'     => array( 'ar' => 'من نحن', 'en' => 'About AEON' ),
		// Section heading, split so the second word takes the accent colour.
		'about_exp_t1'      => array( 'ar' => 'نبذة عن', 'en' => 'About our' ),
		'about_exp_t2'      => array( 'ar' => 'خبراتنا', 'en' => 'expertise' ),
		'about_title'       => array( 'ar' => 'شريكك الرقمي للنمو والتميّز', 'en' => 'Your Digital Partner for Growth & Excellence' ),
		'about_text'        => array( 'ar' => 'في AEON للتسويق الرقمي نؤمن بأن كل علامة تجارية تحمل قصة تستحق أن تُروى. نمتلك خبرة تتجاوز عشر سنوات في التسويق الإلكتروني وإدارة الحسابات الإعلانية وإعلانات جوجل وتحسين محرّكات البحث عبر الإمارات ومصر والكويت والسعودية، وعملنا مع شركات كبرى وقابضة وأسواق ومولات وجهات حكومية. نحوّل قصتك إلى تجربة رقمية متكاملة تجذب الجمهور وتبني الثقة وتحقق نتائج ملموسة.', 'en' => 'At AEON Digital Marketing we believe every brand carries a story worth telling. With 10+ years of experience in digital marketing, ad-account management, Google Ads and SEO across the UAE, Egypt, Kuwait and Saudi Arabia — working with major corporations, holdings, retail markets, malls and government entities — we turn your story into an integrated digital experience that attracts audiences, builds trust and delivers tangible results.' ),
		'about_mission_t'   => array( 'ar' => 'رسالتنا', 'en' => 'Our Mission' ),
		'about_mission'     => array( 'ar' => 'تمكين الشركات من تحقيق أهدافها عبر حلول مبتكرة تجمع بين الإبداع والتقنية، من الفكرة إلى التنفيذ، لنحقق لك ميزة تنافسية حقيقية.', 'en' => 'Empower businesses to reach their goals through innovative solutions that blend creativity and technology — from idea to execution — giving you a genuine competitive edge.' ),
		'about_vision_t'    => array( 'ar' => 'رؤيتنا', 'en' => 'Our Vision' ),
		'about_vision'      => array( 'ar' => 'أن نكون الشريك الرقمي الأول لكل علامة تجارية طموحة في المنطقة.', 'en' => 'To be the leading digital partner for every ambitious brand in the region.' ),
		'about_more'        => array( 'ar' => 'اعرف المزيد عنا', 'en' => 'More About Us' ),

		// Services.
		'services_eyebrow'  => array( 'ar' => 'خدماتنا', 'en' => 'Our Services' ),
		'services_title'    => array( 'ar' => 'كل ما تحتاجه علامتك التجارية', 'en' => 'Everything Your Brand Needs' ),
		'services_sub'      => array( 'ar' => 'باقة متكاملة من الخدمات الرقمية تحت سقف واحد — من التصوير والتصميم إلى التسويق وتطوير المواقع.', 'en' => 'A full suite of digital services under one roof — from photography and design to marketing and web development.' ),
		'svc_learn'         => array( 'ar' => 'عرض التفاصيل', 'en' => 'View Details' ),
		// The Services page banner and its repeated headings moved to the
		// dashboard — see aeon_services_text_fields() in inc/content/services-page.php.
		// Pre-filled WhatsApp message behind the Services page CTA. %s is the
		// service name, so the chat opens already saying which one was read.
		'svc_wa_msg'        => array( 'ar' => 'أرغب في الاستفسار عن خدمة %s.', 'en' => 'I would like to inquire about the %s service.' ),
		'svc_back'          => array( 'ar' => 'كل الخدمات', 'en' => 'All Services' ),

		'svc_photo_t'       => array( 'ar' => 'التصوير الاحترافي', 'en' => 'Professional Photography' ),
		'svc_photo_d'       => array( 'ar' => 'صور احترافية تعكس هوية علامتك بأفضل شكل وتُبرز منتجاتك بجودة سينمائية.', 'en' => 'Professional photography that reflects your brand identity and showcases your products with cinematic quality.' ),
		'svc_design_t'      => array( 'ar' => 'التصميم الجرافيكي', 'en' => 'Graphic Design' ),
		'svc_design_d'      => array( 'ar' => 'تصاميم إبداعية تبني هوية بصرية تترك انطباعاً دائماً.', 'en' => 'Creative designs that build a visual identity and leave a lasting impression.' ),
		'svc_video_t'       => array( 'ar' => 'المونتاج وصناعة الفيديو', 'en' => 'Video Editing & Production' ),
		'svc_video_d'       => array( 'ar' => 'مونتاج احترافي يحوّل أفكارك إلى قصص مؤثرة تصنع الفارق لمحتواك.', 'en' => 'Professional editing that turns your ideas into compelling stories that set your content apart.' ),
		'svc_marketing_t'   => array( 'ar' => 'التسويق الرقمي', 'en' => 'Digital Marketing' ),
		'svc_marketing_d'   => array( 'ar' => 'استراتيجيات ذكية تحقق وصولاً أكبر وتحوّلات أعلى على جميع المنصات.', 'en' => 'Smart strategies that achieve greater reach and higher conversions across every platform.' ),
		'svc_social_t'      => array( 'ar' => 'إدارة السوشيال ميديا', 'en' => 'Social Media Management' ),
		'svc_social_d'      => array( 'ar' => 'إدارة احترافية لحساباتك وبناء تفاعل حقيقي مع جمهورك.', 'en' => 'Professional account management that builds genuine engagement with your audience.' ),
		'svc_brand_t'       => array( 'ar' => 'بناء الهوية التجارية', 'en' => 'Brand Identity' ),
		'svc_brand_d'       => array( 'ar' => 'خلق هوية قوية ومميزة تجعل علامتك تبرز بين المنافسين.', 'en' => 'Building a strong, distinctive identity that makes your brand stand out from competitors.' ),
		'svc_web_t'         => array( 'ar' => 'تصميم وتطوير المواقع', 'en' => 'Web Design & Development' ),
		'svc_web_d'         => array( 'ar' => 'مواقع حديثة وسريعة الاستجابة تحوّل الزوار إلى عملاء.', 'en' => 'Modern, responsive websites that convert visitors into customers.' ),
		'svc_analytics_t'   => array( 'ar' => 'تحليل الأداء والتقارير', 'en' => 'Analytics & Reporting' ),
		'svc_analytics_d'   => array( 'ar' => 'تحليل دقيق وتقارير تفصيلية لتحسين مستمر وتحقيق أفضل النتائج.', 'en' => 'Precise analysis and detailed reports for continuous improvement and the best results.' ),

		// Why choose us.
		'why_eyebrow'       => array( 'ar' => 'لماذا نحن', 'en' => 'Why Choose Us' ),
		'why_title'         => array( 'ar' => 'لماذا AEON شريكك الأفضل', 'en' => 'Why AEON Is Your Best Partner' ),
		'why_1_t'           => array( 'ar' => 'خبرة تتجاوز 10 سنوات', 'en' => '10+ Years of Experience' ),
		'why_1_d'           => array( 'ar' => 'عشر سنوات في التسويق الرقمي وإدارة الإعلانات وتحسين محرّكات البحث عبر الإمارات ومصر والكويت والسعودية.', 'en' => 'A decade in digital marketing, ad management and SEO across the UAE, Egypt, Kuwait and Saudi Arabia.' ),
		'why_2_t'           => array( 'ar' => 'استهداف دقيق واستراتيجية مخصّصة', 'en' => 'Precise Targeting & Tailored Strategy' ),
		'why_2_d'           => array( 'ar' => 'نحلّل السوق والمنافسين ونبني استراتيجية تسويق ذكية مخصّصة لعلامتك.', 'en' => 'We analyse the market and competitors and build a smart marketing strategy tailored to your brand.' ),
		'why_3_t'           => array( 'ar' => 'حملات فعّالة على كل المنصات', 'en' => 'Effective Campaigns on Every Platform' ),
		'why_3_d'           => array( 'ar' => 'إدارة حملات إعلانية احترافية على جميع المنصات بأعلى نسب وصول وتحويل.', 'en' => 'Professional ad campaigns across all platforms with the highest reach and conversion rates.' ),
		'why_4_t'           => array( 'ar' => 'نتائج قابلة للقياس', 'en' => 'Measurable Results' ),
		'why_4_d'           => array( 'ar' => 'تقارير دورية دقيقة لقياس الأداء والتطوّر وتحقيق نمو مستمر لعلامتك.', 'en' => 'Regular, precise reports to measure performance and drive continuous growth for your brand.' ),
		'why_5_t'           => array( 'ar' => 'فريق خبير وشراكة حقيقية', 'en' => 'Expert Team & True Partnership' ),
		'why_5_d'           => array( 'ar' => 'فريق من الاستراتيجيين والمبدعين والمطوّرين يعتبرك شريك نجاح ويعمل معك كفريق واحد.', 'en' => 'A team of strategists, creatives and developers who treat you as a success partner and work as one team.' ),

		// Stats.
		'stat_projects'     => array( 'ar' => 'مشروع مكتمل بنجاح', 'en' => 'Projects Delivered' ),
		'stat_clients'      => array( 'ar' => 'عميل سعيد', 'en' => 'Happy Clients' ),
		'stat_growth'       => array( 'ar' => 'متوسط نمو العملاء', 'en' => 'Average Client Growth' ),
		'stat_satisfaction' => array( 'ar' => 'رضا العملاء', 'en' => 'Client Satisfaction' ),
		'stat_commitment'   => array( 'ar' => 'التزام بتحقيق الأهداف', 'en' => 'Commitment to Goals' ),
		'stat_years'        => array( 'ar' => 'سنوات من الخبرة', 'en' => 'Years of Experience' ),

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
		'partners_title'    => array( 'ar' => 'شركاؤنا في النجاح — أكثر من 120 عميل يثقون بنا', 'en' => 'Our Partners in Success — Trusted by 120+ Clients' ),

		// Industries we serve.
		'industries_eyebrow' => array( 'ar' => 'القطاعات التي نخدمها', 'en' => 'Industries We Serve' ),
		'industries_title'   => array( 'ar' => 'خبرة تمتد عبر مختلف المجالات', 'en' => 'Experience Across Diverse Sectors' ),
		'industries_sub'     => array( 'ar' => 'نعمل مع علامات تجارية في قطاعات متنوّعة ونصمّم لكل منها حلولاً تناسب طبيعة عمله وجمهوره.', 'en' => 'We work with brands across a wide range of sectors, tailoring solutions to each business and its audience.' ),

		// Success partners / clients.
		'clients_eyebrow'    => array( 'ar' => 'شركاؤنا في النجاح', 'en' => 'Success Partners' ),
		'clients_title'      => array( 'ar' => 'أكثر من 120 عميل يثقون بنا', 'en' => 'Trusted by 120+ Clients' ),
		'clients_sub'        => array( 'ar' => 'نفخر بشراكتنا مع نخبة من العلامات التجارية في مصر والسعودية والإمارات والكويت.', 'en' => 'Proud to partner with leading brands across Egypt, Saudi Arabia, the UAE and Kuwait.' ),

		// CTA band.
		'cta_band_title'    => array( 'ar' => 'لنبنِ شيئاً مذهلاً معاً', 'en' => "Let's Build Something Amazing Together" ),
		'cta_band_sub'      => array( 'ar' => 'رؤيتك، استراتيجيتنا، نتائج حقيقية.', 'en' => 'Your vision. Our strategy. Real results.' ),

		// Contact.
		'contact_eyebrow'   => array( 'ar' => 'تواصل معنا', 'en' => 'Get in Touch' ),
		'contact_title'     => array( 'ar' => 'جاهزون لبدء مشروعك القادم', 'en' => 'Ready to Start Your Next Project' ),
		'contact_sub'       => array( 'ar' => 'أخبرنا عن مشروعك وسنعود إليك خلال 24 ساعة.', 'en' => 'Tell us about your project and we will get back within 24 hours.' ),
		'form_name'         => array( 'ar' => 'الاسم', 'en' => 'Name' ),
		'form_service'      => array( 'ar' => 'الخدمة المطلوبة', 'en' => 'Service Needed' ),
		'form_message'      => array( 'ar' => 'رسالتك', 'en' => 'Your Message' ),
		'form_send'         => array( 'ar' => 'إرسال الطلب', 'en' => 'Send Request' ),
		'form_sending'      => array( 'ar' => 'جارٍ الإرسال...', 'en' => 'Sending...' ),
		'form_success'      => array( 'ar' => 'شكراً لك! تم استلام رسالتك وسنتواصل معك قريباً.', 'en' => 'Thank you! Your message was received and we will be in touch soon.' ),
		'form_error'        => array( 'ar' => 'حدث خطأ ما. يرجى المحاولة مرة أخرى.', 'en' => 'Something went wrong. Please try again.' ),
		'form_required'     => array( 'ar' => 'يرجى تعبئة الحقول المطلوبة.', 'en' => 'Please fill in the required fields.' ),
		'form_done'         => array( 'ar' => 'تم الإرسال بنجاح', 'en' => 'Sent successfully' ),
		// Strings for the mailto: handoff. 'mail_subject' carries a %s that the
		// front end swaps for the service the visitor picked; when the field is
		// left blank 'mail_subject_gen' is used instead, so the subject never
		// ends up with a pair of empty quotes in it.
		'mail_subject'      => array( 'ar' => 'الاستفسار عن خدمة "%s"', 'en' => 'Inquiry about "%s" service' ),
		'mail_subject_gen'  => array( 'ar' => 'استفسار عام من الموقع', 'en' => 'General inquiry from the website' ),
		'form_mail_opened'  => array( 'ar' => 'تم فتح تطبيق البريد لديك. راجع الرسالة ثم اضغط إرسال.', 'en' => 'Your email app is opening. Review the message, then press Send.' ),
		'form_mail_fallback' => array( 'ar' => 'إذا لم يفتح تطبيق البريد، راسلنا مباشرة على', 'en' => 'If your email app did not open, write to us directly at' ),
		'contact_email_l'   => array( 'ar' => 'البريد الإلكتروني', 'en' => 'Email' ),
		'contact_phone_l'   => array( 'ar' => 'الهاتف', 'en' => 'Phone' ),
		'contact_addr_l'    => array( 'ar' => 'العنوان', 'en' => 'Address' ),

		// Footer.
		'footer_about'      => array( 'ar' => 'AEON للتسويق الرقمي — القوة الرقمية لمستقبل الميديا. خبرة تتجاوز عشر سنوات في التسويق الرقمي عبر الإمارات ومصر والسعودية والكويت، نحوّل أفكارك إلى نجاحات رقمية مستدامة.', 'en' => 'AEON Digital Marketing — the digital power for the future of media. With 10+ years of experience across the UAE, Egypt, Saudi Arabia and Kuwait, we turn your ideas into sustainable digital success.' ),
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

		// Welcome popup. It holds only the logo and the button, so the dialog's
		// accessible name has to come from here rather than a visible heading.
		'welcome_aria'      => array( 'ar' => 'رسالة ترحيب — تواصل معنا على واتساب', 'en' => 'Welcome — contact us on WhatsApp' ),
		'wa_cta'            => array( 'ar' => 'ابدأ المحادثة الآن', 'en' => 'Start the chat now' ),
		'wa_close'          => array( 'ar' => 'إغلاق', 'en' => 'Close' ),

		// 404 / misc.
		'e404_title'        => array( 'ar' => 'الصفحة غير موجودة', 'en' => 'Page Not Found' ),
		'e404_text'        => array( 'ar' => 'عذراً، الصفحة التي تبحث عنها غير موجودة.', 'en' => 'Sorry, the page you are looking for does not exist.' ),
		'back_home'         => array( 'ar' => 'العودة للرئيسية', 'en' => 'Back to Home' ),
		'read_more'         => array( 'ar' => 'اقرأ المزيد', 'en' => 'Read more' ),
		'blog_title'        => array( 'ar' => 'المدونة وآخر الأخبار', 'en' => 'Blog & Latest News' ),

		// Services closing tagline (profile p2).

		// About / Expertise (profile p3).
		'about_quote'       => array( 'ar' => 'شركة تسويق رقمي متكاملة تقدّم حلولاً تسويقية مبتكرة تصنع الفارق وتحقق النجاح المستدام.', 'en' => 'A full-service digital marketing agency delivering innovative solutions that make the difference and achieve sustainable success.' ),
		'about_b1'          => array( 'ar' => 'نمتلك خبرة بالتسويق الإلكتروني وإدارة الحسابات الإعلانية وإعلانات جوجل وتحسين محركات البحث (SEO) تتجاوز عشر سنوات بمصر والإمارات والكويت والمملكة العربية السعودية.', 'en' => 'We have over ten years of experience in digital marketing, ad-account management, Google Ads and SEO across Egypt, the UAE, Kuwait and Saudi Arabia.' ),
		'about_b2'          => array( 'ar' => 'عملنا مع شركات كبرى وشركات قابضة وأسواق تجارية ومولات وجهات حكومية.', 'en' => 'We have worked with major corporations, holding companies, commercial markets, malls and government entities.' ),
		'about_b3'          => array( 'ar' => 'لدينا فريق ذو خبرة وكفاءة عالية وندير حسابات إعلانية على جميع المنصات.', 'en' => 'We have a highly experienced, skilled team and manage ad accounts across every platform.' ),
		'about_b4'          => array( 'ar' => 'خبرة في تهيئة المواقع الإلكترونية وتحسين محركات البحث ووضع إستراتيجيات تسويق وخطة مبيعات فعّالة حقّقت نتائج سريعة الانتشار والظهور.', 'en' => 'Expertise in website optimization, SEO and building effective marketing strategies and sales plans that deliver fast-spreading, visible results.' ),
		'about_branches_t'  => array( 'ar' => 'فروعنا بالإمارات العربية المتحدة', 'en' => 'Our Branches in the UAE' ),
		'cap_1'             => array( 'ar' => 'تحليل دقيق لمنافسيك', 'en' => 'Precise competitor analysis' ),
		'cap_2'             => array( 'ar' => 'استراتيجيات تسويق ذكية ومخصّصة', 'en' => 'Smart, tailored marketing strategies' ),
		'cap_3'             => array( 'ar' => 'حملات إعلانية فعّالة على جميع المنصات', 'en' => 'Effective ad campaigns on every platform' ),
		'cap_4'             => array( 'ar' => 'تطوير المواقع وتحسين محركات البحث', 'en' => 'Web development & search optimization' ),
		'cap_5'             => array( 'ar' => 'تقارير دورية لقياس الأداء والتطوّر', 'en' => 'Regular reports to measure performance & growth' ),

		// Message & Values (profile p4).
		'values_eyebrow'    => array( 'ar' => 'رسالتنا', 'en' => 'Our Message' ),
		'values_title'      => array( 'ar' => 'شريكك الرقمي للنمو والتميّز', 'en' => 'Your Digital Partner for Growth & Excellence' ),
		'values_lead_1'     => array( 'ar' => 'في AEON للتسويق الرقمي نؤمن بأن كل علامة تجارية تحمل قصة تستحق أن تُروى. نحوّل هذه القصة إلى تجربة رقمية متكاملة تجذب الجمهور وتبني الثقة وتحقق نتائج ملموسة.', 'en' => 'At AEON Digital Marketing we believe every brand carries a story worth telling. We turn that story into an integrated digital experience that attracts audiences, builds trust and delivers tangible results.' ),
		'values_lead_2'     => array( 'ar' => 'نحن فريق من الاستراتيجيين والمبدعين والمطوّرين نعمل بشغف لمساعدة الشركات على تحقيق أهدافها من الفكرة إلى التنفيذ، ونصنع حلولاً مبتكرة تجمع بين الإبداع والتقنية لنحقق لك ميزة تنافسية حقيقية.', 'en' => 'We are a team of strategists, creatives and developers working passionately to help businesses reach their goals — from idea to execution — crafting innovative solutions that blend creativity and technology to give you a genuine competitive edge.' ),
		'values_believe_t'  => array( 'ar' => 'نؤمن بـ', 'en' => 'We Believe In' ),
		'value_results_t'   => array( 'ar' => 'النتائج', 'en' => 'Results' ),
		'value_results_d'   => array( 'ar' => 'نركّز على تحقيق أهداف قابلة للقياس ونضمن عائداً حقيقياً لاستثمارك.', 'en' => 'We focus on measurable goals and guarantee a real return on your investment.' ),
		'value_innovation_t' => array( 'ar' => 'الابتكار', 'en' => 'Innovation' ),
		'value_innovation_d' => array( 'ar' => 'نبتكر حلولاً إبداعية تواكب التطوّرات وتضعك دائماً في المقدّمة.', 'en' => 'We craft creative solutions that keep pace with change and keep you ahead.' ),
		'value_partnership_t' => array( 'ar' => 'الشراكة', 'en' => 'Partnership' ),
		'value_partnership_d' => array( 'ar' => 'نعتبر عملاءنا شركاء نجاح ونعمل معهم كفريق واحد لبناء مستقبل أفضل.', 'en' => 'We treat our clients as success partners and work as one team to build a better future.' ),
		'values_circle'     => array( 'ar' => 'نحوّل أفكارك إلى نجاحات رقمية مستدامة', 'en' => 'We turn your ideas into sustainable digital success' ),
		'values_solutions'  => array( 'ar' => 'نقدّم حلولاً متكاملة', 'en' => 'We deliver integrated solutions' ),

		// Events & participation (profile p5).
		'events_eyebrow'    => array( 'ar' => 'مشاركات وفاعليات', 'en' => 'Events & Participation' ),
		'events_title'      => array( 'ar' => 'في مجال التسويق الإلكتروني لروّاد الأعمال وأصحاب المشاريع', 'en' => 'In digital marketing — for entrepreneurs and business owners' ),
		'event_1'           => array( 'ar' => 'مشاركات في المؤتمرات والفعاليات المتخصّصة', 'en' => 'Participation in specialized conferences and events' ),
		'event_2'           => array( 'ar' => 'ورش عمل ودورات تدريبية لروّاد الأعمال وأصحاب المشاريع', 'en' => 'Workshops and training courses for entrepreneurs and business owners' ),
		'event_3'           => array( 'ar' => 'تقديم نصائح عملية لتطوير الأعمال وزيادة المبيعات', 'en' => 'Practical advice for growing businesses and increasing sales' ),
		'event_4'           => array( 'ar' => 'حلقات نقاش وجلسات حوارية حول أحدث استراتيجيات التسويق', 'en' => 'Panel discussions on the latest marketing strategies' ),
		'event_5'           => array( 'ar' => 'بناء علاقات استراتيجية وشراكات تحقق نتائج مستدامة', 'en' => 'Building strategic relationships and partnerships that deliver lasting results' ),
		'events_note'       => array( 'ar' => 'خبرة تتجاوز 10 سنوات في التسويق الإلكتروني', 'en' => '10+ years of experience in digital marketing' ),

		// Portfolio showcase (profile p11–37).
		'show_title'        => array( 'ar' => 'معرض أعمالنا', 'en' => 'Our Portfolio' ),
		'show_sub'          => array( 'ar' => 'لمحة من إبداعاتنا في التصميم والتصوير والفيديو والمواقع عبر مختلف القطاعات.', 'en' => 'A glimpse of our work in design, photography, video and web across many sectors.' ),
		'cat_web'           => array( 'ar' => 'المواقع الإلكترونية', 'en' => 'Websites' ),
		'cat_photography'   => array( 'ar' => 'التصوير الاحترافي', 'en' => 'Photography' ),
		'cat_reels'         => array( 'ar' => 'فيديوهات ريلز', 'en' => 'Reels' ),
		'cat_motion'        => array( 'ar' => 'موشن جرافيك', 'en' => 'Motion Graphics' ),
		'cat_social'        => array( 'ar' => 'سوشيال ميديا', 'en' => 'Social Media' ),

		// Ad-campaign results (profile p38).
		'results_eyebrow'   => array( 'ar' => 'إعلانات انستجرام', 'en' => 'Instagram Ads' ),
		'results_title'     => array( 'ar' => 'أكثر من 100 ألف مشاهدة للفيديو الواحد', 'en' => '100K+ views on a single video' ),
		'results_sub'       => array( 'ar' => 'نربط البراند الخاص بك بأعلى نسب الوصول ونحقق أرقاماً حقيقية قابلة للقياس.', 'en' => 'We connect your brand to the highest reach and deliver real, measurable numbers.' ),
		'metric_reach_l'    => array( 'ar' => 'الوصول', 'en' => 'Reach' ),
		'metric_reach_v'    => array( 'ar' => '180,303', 'en' => '180,303' ),
		'metric_views_l'    => array( 'ar' => 'المشاهدات', 'en' => 'Views' ),
		'metric_views_v'    => array( 'ar' => '171,336', 'en' => '171,336' ),
		'metric_engage_l'   => array( 'ar' => 'التفاعلات', 'en' => 'Engagements' ),
		'metric_engage_v'   => array( 'ar' => '1,861', 'en' => '1,861' ),
		'metric_watch_l'    => array( 'ar' => 'وقت المشاهدة', 'en' => 'Watch Time' ),
		'metric_watch_v'    => array( 'ar' => '3 س 10 د', 'en' => '3h 10m' ),
		'results_partner'   => array( 'ar' => 'شريكك في النجاح الرقمي', 'en' => 'Your Partner in Digital Success' ),

		// Visit our office (profile p6).
		'office_eyebrow'    => array( 'ar' => 'نتشرف بزيارتكم', 'en' => 'Visit Us' ),
		'office_title'      => array( 'ar' => 'نتشرف بزيارتكم لموقع الشركة', 'en' => 'We’d Be Honored to Welcome You' ),
		'office_sub'        => array( 'ar' => 'تواصلوا معنا لترتيب موعد للاستشارات.', 'en' => 'Get in touch to arrange a consultation appointment.' ),
		'office_cta'        => array( 'ar' => 'احجز موعد استشارة', 'en' => 'Book a Consultation' ),
		'office_partner'    => array( 'ar' => 'شراكة حقيقية — بناء نجاح مستدام', 'en' => 'A true partnership — building lasting success' ),

		// Web design chapter (profile p10).
		'web_title_1'       => array( 'ar' => 'تصميم', 'en' => 'Website' ),
		'web_title_2'       => array( 'ar' => 'المواقع الإلكترونية', 'en' => 'Design & Development' ),
		'web_lead'          => array( 'ar' => 'تصميم وتطوير المواقع الإلكترونية', 'en' => 'Web design and development' ),
		'web_text'          => array( 'ar' => 'نصمّم تجارب رقمية متكاملة تجمع بين جمالية الواجهات (UI) وسهولة الاستخدام (UX). نضمن لك موقعاً سريع الاستجابة، متوافقاً مع كافة الأجهزة، ليكون مرآة تعكس هوية علامتك التجارية وتروي قصتها بأسلوب تقني مبتكر وسلس.', 'en' => 'We design complete digital experiences that combine beautiful interfaces (UI) with effortless usability (UX). Your site will be fast, responsive and consistent across every device — a mirror of your brand identity that tells its story with smooth, inventive craft.' ),
		'web_cta'           => array( 'ar' => 'عرض النماذج', 'en' => 'View Samples' ),
		'web_f1'            => array( 'ar' => 'أمان متقدم', 'en' => 'Advanced security' ),
		'web_f1_d'          => array( 'ar' => 'وحماية للبيانات', 'en' => 'and data protection' ),
		'web_f2'            => array( 'ar' => 'سرعة عالية', 'en' => 'High speed' ),
		'web_f2_d'          => array( 'ar' => 'وأداء متميز', 'en' => 'and standout performance' ),
		'web_f3'            => array( 'ar' => 'متوافق', 'en' => 'Compatible' ),
		'web_f3_d'          => array( 'ar' => 'مع جميع الأجهزة', 'en' => 'with every device' ),

		// Photography chapter (profile p11–16).
		'photo_title_1'     => array( 'ar' => 'تصوير', 'en' => 'Professional' ),
		'photo_title_2'     => array( 'ar' => 'إحترافي', 'en' => 'photography' ),
		'photo_sub'         => array( 'ar' => 'يبرز منتجاتك', 'en' => 'that makes your products stand out' ),
		'photo_l1_t'        => array( 'ar' => 'إضاءة إحترافية', 'en' => 'Professional lighting' ),
		'photo_l1_d'        => array( 'ar' => 'تُظهر تفاصيل منتجك', 'en' => 'reveals every detail of your product' ),
		'photo_l2_t'        => array( 'ar' => 'ألوان جذابة', 'en' => 'Appetising colour' ),
		'photo_l2_d'        => array( 'ar' => 'تعزز المذاق والشهية', 'en' => 'that heightens taste and appetite' ),
		'photo_l3_t'        => array( 'ar' => 'جودة عالية', 'en' => 'High quality' ),
		'photo_l3_d'        => array( 'ar' => 'صور بجودة سينمائية', 'en' => 'cinematic-grade imagery' ),
		'photo_l4_t'        => array( 'ar' => 'عرض مثالي', 'en' => 'Perfect presentation' ),
		'photo_l4_d'        => array( 'ar' => 'يزيد من جاذبية منتجك', 'en' => 'that makes your product irresistible' ),
		'photo_tagline'     => array( 'ar' => 'كل لقطة تحكي طعم الجودة', 'en' => 'Every shot tells the taste of quality' ),

		// Reels & motion chapters (profile p17–21).
		'reels_title_1'     => array( 'ar' => 'فيديوهات ريلز', 'en' => 'Reels videos' ),
		'reels_title_2'     => array( 'ar' => 'بشكل إحترافي', 'en' => 'done professionally' ),
		'reels_sub'         => array( 'ar' => 'تصنع الفارق لمحتواك', 'en' => 'that set your content apart' ),
		'motion_title_1'    => array( 'ar' => 'موشن', 'en' => 'Motion graphics' ),
		'motion_title_2'    => array( 'ar' => 'بشكل إحترافي', 'en' => 'done professionally' ),
		'motion_sub'        => array( 'ar' => 'يستهدف احتياجك بشكل دقيق', 'en' => 'targeted precisely to what you need' ),

		// The recurring four-item proof strip (profile p17–21, p38).
		'proof_1'           => array( 'ar' => 'استهداف دقيق', 'en' => 'Precise targeting' ),
		'proof_2'           => array( 'ar' => 'نتائج قابلة للقياس', 'en' => 'Measurable results' ),
		'proof_3'           => array( 'ar' => 'نمو مستمر لبراندك', 'en' => 'Continuous brand growth' ),
		'proof_4'           => array( 'ar' => 'محتوى يصنع الفرق', 'en' => 'Content that makes the difference' ),

		// Social-media chapter (profile p22–37).
		'social_title_1'    => array( 'ar' => 'تصميمات سوشيال ميديا', 'en' => 'Social media design' ),
		'social_title_2'    => array( 'ar' => 'بشكل إحترافي', 'en' => 'done professionally' ),
		'social_sub'        => array( 'ar' => 'أعمال منفّذة لعلامات تجارية في قطاعات متنوّعة.', 'en' => 'Work delivered for brands across a range of sectors.' ),

		// Clients (profile p7–9) — the profile's own wording.
		'clients_proud'     => array( 'ar' => 'نفخر بتعاملنا مع <span class="accent">أكثر من 120 عميل</span>', 'en' => 'Proud to work with <span class="accent">more than 120 clients</span>' ),
		'clients_region'    => array( 'ar' => 'بمصر والسعودية والإمارات والكويت', 'en' => 'across Egypt, Saudi Arabia, the UAE and Kuwait' ),
		'clients_partners'  => array( 'ar' => 'شركاؤنا في النجاح', 'en' => 'Our partners in success' ),

		// Results card detail (profile p38).
		'metric_avg_l'      => array( 'ar' => 'متوسط وقت المشاهدة', 'en' => 'Avg. watch time' ),
		'metric_avg_v'      => array( 'ar' => '2 ث', 'en' => '2s' ),
		'metric_follow_l'   => array( 'ar' => 'متابعو مقطع ريلز', 'en' => 'Reel followers' ),
		'metric_follow_v'   => array( 'ar' => '—', 'en' => '—' ),
		'donut_title'       => array( 'ar' => 'المشاهدات', 'en' => 'Views' ),
		'donut_sub'         => array( 'ar' => 'المتابعون مقابل غير المتابعين', 'en' => 'Followers vs non-followers' ),
		'donut_followers'   => array( 'ar' => 'المتابعون', 'en' => 'Followers' ),
		'donut_non'         => array( 'ar' => 'غير المتابعين', 'en' => 'Non-followers' ),
		'views_unit'        => array( 'ar' => 'ألف مشاهدة', 'en' => 'K views' ),

		// Ad-campaign platforms (client-supplied AEON campaign creatives).
		'camp_eyebrow'      => array( 'ar' => 'حملاتنا الإعلانية', 'en' => 'Our Ad Campaigns' ),
		'camp_title'        => array( 'ar' => 'حملات مدفوعة على المنصات التي يستخدمها جمهورك', 'en' => 'Paid campaigns on the platforms your audience actually uses' ),
		'camp_sub'          => array( 'ar' => 'ندير حساباتك الإعلانية على جوجل وسناب شات وتيك توك وفيسبوك وإنستجرام باستهداف دقيق ونتائج قابلة للقياس.', 'en' => 'We run your ad accounts on Google, Snapchat, TikTok, Facebook and Instagram with precise targeting and measurable results.' ),
		'camp_google_t'     => array( 'ar' => 'إعلانات جوجل', 'en' => 'Google Ads' ),
		'camp_google_d'     => array( 'ar' => 'حملات مدفوعة وتحسين SEO لتتصدّر نتائج البحث.', 'en' => 'Paid campaigns and SEO to top the search results.' ),
		'camp_snap_t'       => array( 'ar' => 'إعلانات سناب شات', 'en' => 'Snapchat Ads' ),
		'camp_snap_d'       => array( 'ar' => 'وصول عالي الاستهداف لجمهورك في السوق المحلي.', 'en' => 'Highly targeted reach to your audience in the local market.' ),
		'camp_tiktok_t'     => array( 'ar' => 'إعلانات تيك توك', 'en' => 'TikTok Ads' ),
		'camp_tiktok_d'     => array( 'ar' => 'فيديوهاتك تصل إلى ملايين المشاهدات بمحتوى يصنع الفرق.', 'en' => 'Your videos reach millions of views with content that makes a difference.' ),
		'camp_meta_t'       => array( 'ar' => 'إعلانات فيسبوك وإنستجرام', 'en' => 'Facebook & Instagram Ads' ),
		'camp_meta_d'       => array( 'ar' => 'حملات رسائل وتحويلات تجلب لك عملاء حقيقيين.', 'en' => 'Message and conversion campaigns that bring in real customers.' ),

		// Work archive page.
		'work_page_title'   => array( 'ar' => 'أعمالنا', 'en' => 'Our Work' ),
		'work_page_sub'     => array( 'ar' => 'نماذج من أعمالنا في تصميم المواقع والتصوير والريلز والموشن وتصميمات السوشيال ميديا.', 'en' => 'Samples of our work in web design, photography, reels, motion graphics and social media design.' ),
		'work_back_home'    => array( 'ar' => 'العودة للرئيسية', 'en' => 'Back to home' ),
		'show_all_work'     => array( 'ar' => 'شاهد كل الأعمال', 'en' => 'View all work' ),
	);

	return $s;
}
