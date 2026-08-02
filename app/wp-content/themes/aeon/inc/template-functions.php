<?php
/**
 * Template helper functions & data providers.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set <html dir> and lang from the active language.
 *
 * Front-end only: wp-admin also builds its <html> tag via language_attributes(),
 * and the site is Arabic-first, so without this guard the whole admin would be
 * forced into RTL (with English text) on every screen.
 */
function aeon_language_attributes( $output ) {
	if ( is_admin() ) {
		return $output;
	}
	$lang = aeon_lang();
	$dir  = aeon_dir();
	$locale = 'ar' === $lang ? 'ar' : 'en-US';
	return 'lang="' . esc_attr( $locale ) . '" dir="' . esc_attr( $dir ) . '"';
}
add_filter( 'language_attributes', 'aeon_language_attributes' );

/**
 * Add the active-language class to <body>.
 */
function aeon_body_classes( $classes ) {
	$classes[] = 'aeon-lang-' . aeon_lang();
	$classes[] = aeon_is_rtl() ? 'aeon-rtl' : 'aeon-ltr';
	return $classes;
}
add_filter( 'body_class', 'aeon_body_classes' );

/**
 * Normalise a menu label for matching (strip tags, trim, lowercase).
 *
 * @param string $s
 * @return string
 */
function aeon_norm_label( $s ) {
	$s = trim( wp_strip_all_tags( (string) $s ) );
	return function_exists( 'mb_strtolower' ) ? mb_strtolower( $s, 'UTF-8' ) : strtolower( $s );
}

/**
 * Translate primary-nav menu item titles to the active language.
 *
 * The theme is bilingual without a multilingual plugin, so a menu assigned in
 * the WordPress admin is stored in whichever language it was created. This maps
 * known nav items onto the active language so the navbar always matches the
 * site language — e.g. an English menu shows Arabic labels when the site is in
 * Arabic.
 *
 * Matching is done two ways, so the navbar stays translated even when the
 * client renamed items in the admin:
 *   1. By label — an exact match against any known nav label (either language).
 *   2. By destination URL — the item's path (/about/, /services/, …) mapped to
 *      its nav key. This catches custom labels like "Portfolio" → /work/ or
 *      "Get in Touch" → /contact/ that a label lookup alone would miss.
 *
 * @param array    $items Menu item objects.
 * @param stdClass $args  wp_nav_menu args.
 * @return array
 */
function aeon_translate_menu_items( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	static $lookup = null;
	if ( null === $lookup ) {
		$lookup  = array();
		$strings = aeon_strings();
		$keys    = array( 'nav_home', 'nav_about', 'nav_services', 'nav_work', 'nav_blog', 'nav_contact' );
		foreach ( $keys as $key ) {
			if ( empty( $strings[ $key ] ) ) {
				continue;
			}
			foreach ( array( 'ar', 'en' ) as $l ) {
				if ( isset( $strings[ $key ][ $l ] ) ) {
					$lookup[ aeon_norm_label( $strings[ $key ][ $l ] ) ] = $key;
				}
			}
		}
	}

	// First path segment of a menu item's URL → nav key. Aliases (portfolio,
	// news) point at the same destinations the theme's default menu uses.
	$path_map = array(
		'about'     => 'nav_about',
		'services'  => 'nav_services',
		'work'      => 'nav_work',
		'portfolio' => 'nav_work',
		'blog'      => 'nav_blog',
		'news'      => 'nav_blog',
		'contact'   => 'nav_contact',
	);
	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );

	foreach ( $items as $item ) {
		$key  = null;
		$norm = aeon_norm_label( $item->title );

		if ( isset( $lookup[ $norm ] ) ) {
			$key = $lookup[ $norm ];
		} else {
			$path = trim( (string) wp_parse_url( $item->url, PHP_URL_PATH ), '/' );
			// Strip the site's subdirectory (if WP lives in /subdir/) so the
			// match works on both root and subdirectory installs.
			if ( '' !== $home_path && 0 === strpos( $path, $home_path ) ) {
				$path = trim( substr( $path, strlen( $home_path ) ), '/' );
			}
			$segment = '' === $path ? '' : strtolower( strtok( $path, '/' ) );

			if ( '' === $path ) {
				$key = 'nav_home';
			} elseif ( isset( $path_map[ $segment ] ) ) {
				$key = $path_map[ $segment ];
			}
		}

		if ( $key ) {
			$item->title = aeon_t( $key );
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'aeon_translate_menu_items', 10, 2 );

/**
 * Is the site running as a one-pager?
 *
 * The client asked for a single-page site: Services keeps its own page, and
 * every other destination is a section of the homepage reached by anchor. The
 * standalone About, Contact, portfolio and blog routes stop resolving.
 *
 * Nothing is deleted — the pages, posts, portfolio items and menu entries all
 * stay in the dashboard, and their templates stay in the theme. Return false
 * here (or filter it) to restore the full multi-page site, then put the
 * '/about/', '/work/', '/blog/' and '/contact/' entries back in
 * aeon_default_menu() in header.php and the footer links in footer.php.
 *
 * @return bool
 */
function aeon_single_page_mode() {
	return (bool) apply_filters( 'aeon_single_page_mode', true );
}

/**
 * Menu path → homepage anchor for the one-page navigation.
 *
 * @return array<string,string>
 */
function aeon_single_page_anchors() {
	return (array) apply_filters( 'aeon_single_page_anchors', array(
		'about'   => '#about',
		'contact' => '#contact',
	) );
}

/**
 * Where every "contact us" call-to-action points.
 *
 * One destination for the header, hero, footer, services and office buttons, so
 * flipping aeon_single_page_mode() moves all of them at once.
 *
 * @return string
 */
function aeon_contact_url() {
	return aeon_single_page_mode() ? home_url( '/#contact' ) : home_url( '/contact/' );
}

/**
 * Where a service's "request this service" button points: WhatsApp, with the
 * chat pre-filled naming that service.
 *
 * The visitor lands in the conversation with the first message already typed,
 * so the manager sees which service prompted it without having to ask. The text
 * follows the active language, same as the button label.
 *
 * Falls back to the ordinary contact destination if no number is configured, so
 * the button is never dead.
 *
 * @param string $name Service name, exactly as shown on the page.
 * @return string
 */
function aeon_service_whatsapp_url( $name ) {
	$number = aeon_whatsapp_number();

	if ( ! $number ) {
		return aeon_contact_url();
	}

	$message = sprintf( aeon_t( 'svc_wa_msg' ), $name );

	return 'https://wa.me/' . $number . '?text=' . rawurlencode( $message );
}

/**
 * Menu paths that have no homepage section to scroll to, so their items are
 * dropped from the menus entirely.
 *
 * @return array<int,string>
 */
function aeon_single_page_dropped() {
	return (array) apply_filters( 'aeon_single_page_dropped', array( 'work', 'portfolio', 'blog', 'news' ) );
}

/**
 * First path segment of a URL, relative to the site root.
 *
 * @param string $url Absolute or relative URL.
 * @return string
 */
function aeon_url_segment( $url ) {
	$path = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
	$home = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );

	if ( '' !== $home && 0 === strpos( $path, $home ) ) {
		$path = trim( substr( $path, strlen( $home ) ), '/' );
	}

	$parts = explode( '/', $path );
	return $parts[0];
}

/**
 * Point the nav menus at homepage sections instead of standalone pages.
 *
 * Runs after aeon_translate_menu_items(), which still needs the original paths
 * to label each item. Matches the assigned posts page by ID as well, so a
 * dashboard "Blog" entry goes whatever the client titled it.
 *
 * @param array $items Menu item objects.
 * @return array
 */
function aeon_single_page_menu_items( $items ) {
	if ( ! aeon_single_page_mode() ) {
		return $items;
	}

	$anchors   = aeon_single_page_anchors();
	$dropped   = aeon_single_page_dropped();
	$blog_page = (int) get_option( 'page_for_posts' );
	$home      = home_url( '/' );

	foreach ( $items as $i => $item ) {
		if ( $blog_page && isset( $item->object, $item->object_id )
			&& 'page' === $item->object && (int) $item->object_id === $blog_page ) {
			unset( $items[ $i ] );
			continue;
		}

		if ( empty( $item->url ) ) {
			continue;
		}

		$segment = aeon_url_segment( $item->url );

		if ( isset( $anchors[ $segment ] ) ) {
			$item->url = $home . $anchors[ $segment ];
			continue;
		}

		if ( in_array( $segment, $dropped, true ) ) {
			unset( $items[ $i ] );
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'aeon_single_page_menu_items', 20 );

/**
 * Pages that keep a route of their own, by slug. Everything else is a homepage
 * section.
 *
 * @return array<int,string>
 */
function aeon_allowed_page_slugs() {
	return (array) apply_filters( 'aeon_allowed_page_slugs', array( 'services' ) );
}

/**
 * Serve a 404 for everything except the homepage and the Services page.
 *
 * An allow-list rather than a block-list, so no stray route survives: About,
 * Contact, the portfolio archive and its items, the blog and every post-driven
 * archive, search results and any orphan page all stop resolving. WordPress'
 * own utility endpoints (robots.txt, feeds, the favicon) are left alone so the
 * site stays crawlable, and an existing 404 is not re-flagged.
 *
 * Everything is reachable again the moment aeon_single_page_mode() returns false.
 */
function aeon_block_disabled_routes() {
	if ( is_admin() || ! aeon_single_page_mode() ) {
		return;
	}

	if ( is_robots() || is_feed() || ( function_exists( 'is_favicon' ) && is_favicon() ) ) {
		return;
	}

	if ( is_404() || is_front_page() || is_page( aeon_allowed_page_slugs() ) ) {
		return;
	}

	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
}
add_action( 'template_redirect', 'aeon_block_disabled_routes' );

/**
 * Fallback services used on the homepage / services page before the client
 * adds their own Service posts. Pulls straight from the bilingual strings.
 *
 * @return array
 */
function aeon_default_services() {
	return array(
		array( 'icon' => 'camera',    'title' => 'svc_photo_t',     'desc' => 'svc_photo_d' ),
		array( 'icon' => 'pen',       'title' => 'svc_design_t',    'desc' => 'svc_design_d' ),
		array( 'icon' => 'video',     'title' => 'svc_video_t',     'desc' => 'svc_video_d' ),
		array( 'icon' => 'megaphone', 'title' => 'svc_marketing_t', 'desc' => 'svc_marketing_d' ),
		array( 'icon' => 'growth',    'title' => 'svc_social_t',     'desc' => 'svc_social_d' ),
		array( 'icon' => 'target',    'title' => 'svc_brand_t',      'desc' => 'svc_brand_d' ),
		array( 'icon' => 'globe',     'title' => 'svc_web_t',        'desc' => 'svc_web_d' ),
		array( 'icon' => 'shield',    'title' => 'svc_analytics_t',  'desc' => 'svc_analytics_d' ),
	);
}

/**
 * "Why choose us" items.
 */
function aeon_why_items() {
	return array(
		array( 'icon' => 'shield',    'title' => 'why_1_t', 'desc' => 'why_1_d' ),
		array( 'icon' => 'target',    'title' => 'why_2_t', 'desc' => 'why_2_d' ),
		array( 'icon' => 'megaphone', 'title' => 'why_3_t', 'desc' => 'why_3_d' ),
		array( 'icon' => 'chart',     'title' => 'why_4_t', 'desc' => 'why_4_d' ),
		array( 'icon' => 'team',      'title' => 'why_5_t', 'desc' => 'why_5_d' ),
	);
}

/**
 * Map a service (by its Arabic term name) to a stable slug, so the detailed
 * feature bullets can be attached to both dashboard terms and the fallbacks.
 *
 * @param string $name Arabic service name.
 * @return string Slug, or '' when unknown.
 */
function aeon_service_slug( $name ) {
	$map = array(
		'التصوير الاحترافي'       => 'photo',
		'التصميم الجرافيكي'       => 'design',
		'المونتاج وصناعة الفيديو' => 'video',
		'التسويق الرقمي'          => 'marketing',
		'إدارة السوشيال ميديا'    => 'social',
		'بناء الهوية التجارية'    => 'brand',
		'تصميم وتطوير المواقع'    => 'web',
		'تحليل الأداء والتقارير'  => 'analytics',
	);
	return isset( $map[ $name ] ) ? $map[ $name ] : '';
}

/**
 * Three short feature bullets per service, in the active language.
 * Sourced from the company profile.
 *
 * @param string $slug Service slug (see aeon_service_slug()).
 * @return string[] Feature lines, or an empty array.
 */
function aeon_service_features( $slug ) {
	$f = array(
		'photo' => array(
			'ar' => array( 'إضاءة احترافية تُبرز تفاصيل منتجك', 'ألوان جذّابة تعزّز الجاذبية والشهية', 'جودة سينمائية وعرض مثالي' ),
			'en' => array( 'Professional lighting that reveals detail', 'Vibrant colors that boost appeal', 'Cinematic quality & perfect presentation' ),
		),
		'design' => array(
			'ar' => array( 'هوية بصرية متكاملة ومتّسقة', 'تصاميم تترك انطباعاً دائماً', 'اتساق كامل عبر كل المنصات' ),
			'en' => array( 'A complete, consistent visual identity', 'Designs that leave a lasting impression', 'Full consistency across every platform' ),
		),
		'video' => array(
			'ar' => array( 'محتوى ريلز يستهدف جمهورك بدقة', 'نتائج قابلة للقياس', 'نمو مستمر لعلامتك' ),
			'en' => array( 'Reels content precisely targeting your audience', 'Measurable results', 'Sustained growth for your brand' ),
		),
		'marketing' => array(
			'ar' => array( 'استهداف دقيق للجمهور المناسب', 'حملات على جميع المنصات', 'أعلى نسب وصول وتحويل' ),
			'en' => array( 'Precise targeting of the right audience', 'Campaigns across every platform', 'The highest reach & conversion rates' ),
		),
		'social' => array(
			'ar' => array( 'إدارة يومية احترافية لحساباتك', 'محتوى يصنع التفاعل', 'تفاعل حقيقي مع جمهورك' ),
			'en' => array( 'Professional daily account management', 'Content that drives engagement', 'Genuine interaction with your audience' ),
		),
		'brand' => array(
			'ar' => array( 'هوية قوية ومميزة', 'تُبرز علامتك بين المنافسين', 'حضور بصري لا يُنسى' ),
			'en' => array( 'A strong, distinctive identity', 'Makes your brand stand out', 'An unforgettable visual presence' ),
		),
		'web' => array(
			'ar' => array( 'متوافق مع جميع الأجهزة', 'سرعة عالية وأداء متميّز', 'أمان متقدّم وحماية للبيانات' ),
			'en' => array( 'Compatible with all devices', 'High speed & outstanding performance', 'Advanced security & data protection' ),
		),
		'analytics' => array(
			'ar' => array( 'تحليل دقيق للأداء', 'تقارير تفصيلية دورية', 'تحسين مستمر للنتائج' ),
			'en' => array( 'Precise performance analysis', 'Detailed, regular reporting', 'Continuous improvement of results' ),
		),
	);
	if ( ! isset( $f[ $slug ] ) ) {
		return array();
	}
	$lang = aeon_lang();
	return isset( $f[ $slug ][ $lang ] ) ? $f[ $slug ][ $lang ] : $f[ $slug ]['en'];
}

/**
 * Work samples for a service, drawn from the client's own material.
 *
 * The company profile deck devotes one chapter to each kind of work, so its
 * shots are traceable to a page: photography p11–17, reels p18–20 and motion
 * p21–22 (both are the video service), the social-media design boards p23–37,
 * the website mockups p10 and the campaign insights card p38. Two services are
 * sourced from outside the deck: photography also uses the client's own
 * full-resolution originals, and digital marketing uses AEON's four platform
 * campaign banners, which the deck has no chapter for.
 *
 * The design boards chapter is the deck's only source for both the graphic
 * design and the social media services, so it is split between them — no shot
 * is used twice. Brand identity has no source in any of the supplied material;
 * it returns an empty list and its section renders without a strip.
 *
 * Nothing here comes from the `حملاتي` campaign screenshots: those carry other
 * companies' account names, ad-account IDs, phone numbers and identifiable
 * people, so they are deliberately not shipped.
 *
 * @param string $slug Service slug (see aeon_service_slug()).
 * @return array{ratio:string,cols:int,shots:array<int,array{file:string,alt:string}>} Empty shots when unknown.
 */
function aeon_service_gallery( $slug ) {
	$g = array(
		// Square tiles: five of these come from the client's own full-resolution
		// originals (portrait) and three from the deck collages (landscape), so a
		// 1:1 crop is the only ratio that treats both fairly.
		'photo' => array(
			'ratio' => '1 / 1',
			'cols'  => 4,
			'shots' => array(
				array( 'burger.jpg',          'ar' => 'تصوير الأطعمة والوجبات',   'en' => 'Food & meal photography' ),
				array( 'coffee-brew.jpg',     'ar' => 'تصوير محامص القهوة',       'en' => 'Coffee roastery photography' ),
				array( 'perfume-labelle.jpg', 'ar' => 'تصوير العطور والمنتجات',   'en' => 'Perfume & product photography' ),
				array( 'latte.jpg',           'ar' => 'تصوير مشروبات الكافيهات',  'en' => 'Café drinks photography' ),
				array( 'baklava.jpg',         'ar' => 'تصوير الحلويات الشرقية',   'en' => 'Pastry & sweets photography' ),
				array( 'milkshake.jpg',       'ar' => 'تصوير الحلويات والمشروبات', 'en' => 'Dessert & drinks photography' ),
				array( 'juices.jpg',          'ar' => 'تصوير العصائر الطازجة',    'en' => 'Fresh juice photography' ),
				array( 'grill-platter.jpg',   'ar' => 'تصوير أطباق المطاعم',      'en' => 'Restaurant dishes photography' ),
			),
		),
		'design' => array(
			'ratio' => '4 / 5',
			'cols'  => 4,
			'shots' => array(
				array( 'business-setup.jpg', 'ar' => 'تصميمات إدارة المنشآت',   'en' => 'Business setup designs' ),
				array( 'perfume-gift.jpg',   'ar' => 'تصميمات العطور والهدايا', 'en' => 'Perfume & gifting designs' ),
				array( 'perfume-clove.jpg',  'ar' => 'تصميمات علامات العطور',   'en' => 'Perfume brand designs' ),
				array( 'meat.jpg',           'ar' => 'تصميمات محلات اللحوم',    'en' => 'Butchery brand designs' ),
			),
		),
		'video' => array(
			'ratio' => '9 / 16',
			'cols'  => 5,
			'shots' => array(
				array( 'reel-podcast.jpg',   'ar' => 'ريلز بودكاست وحوارات',   'en' => 'Podcast & interview reels' ),
				array( 'reel-clinic.jpg',    'ar' => 'ريلز العيادات والتجميل', 'en' => 'Clinic & beauty reels' ),
				array( 'reel-corporate.jpg', 'ar' => 'ريلز الشركات والعلامات', 'en' => 'Corporate & brand reels' ),
				array( 'motion-dental.jpg',  'ar' => 'موشن جرافيك طبي',        'en' => 'Medical motion graphics' ),
				array( 'motion-goals.jpg',   'ar' => 'موشن جرافيك تسويقي',     'en' => 'Marketing motion graphics' ),
			),
		),
		// AEON's own campaign banners, one per ad platform — not from the deck.
		'marketing' => array(
			'ratio' => '16 / 9',
			'cols'  => 2,
			'shots' => array(
				array( 'google.jpg',   'ar' => 'حملات جوجل المدفوعة وتحسين محركات البحث', 'en' => 'Google Ads campaigns & SEO' ),
				array( 'facebook.jpg', 'ar' => 'حملات فيسبوك وإنستجرام الإعلانية',        'en' => 'Facebook & Instagram ad campaigns' ),
				array( 'tiktok.jpg',   'ar' => 'حملات تيك توك الإعلانية',                 'en' => 'TikTok ad campaigns' ),
				array( 'snapchat.jpg', 'ar' => 'حملات سناب شات الإعلانية',                'en' => 'Snapchat ad campaigns' ),
			),
		),
		'social' => array(
			'ratio' => '4 / 5',
			'cols'  => 4,
			'shots' => array(
				array( 'travel.jpg',     'ar' => 'محتوى حسابات السياحة',   'en' => 'Travel account content' ),
				array( 'coffee.jpg',     'ar' => 'محتوى حسابات القهوة',    'en' => 'Coffee account content' ),
				array( 'delivery.jpg',   'ar' => 'محتوى شركات التوصيل',    'en' => 'Delivery account content' ),
				array( 'restaurant.jpg', 'ar' => 'محتوى حسابات المطاعم',   'en' => 'Restaurant account content' ),
			),
		),
		'web' => array(
			'ratio' => '16 / 10',
			'cols'  => 2,
			'shots' => array(
				array( 'devices.jpg', 'ar' => 'موقع متجاوب على كل الأجهزة', 'en' => 'A responsive site on every device' ),
				array( 'laptop.jpg',  'ar' => 'واجهة الصفحة الرئيسية',      'en' => 'Home page interface' ),
			),
		),
		'analytics' => array(
			'ratio' => '2 / 3',
			'cols'  => 4,
			'shots' => array(
				array( 'insights.jpg', 'ar' => 'تقرير أداء حملة إعلانية', 'en' => 'Campaign performance report' ),
			),
		),
	);

	if ( ! isset( $g[ $slug ] ) ) {
		return array( 'ratio' => '4 / 5', 'cols' => 4, 'shots' => array() );
	}

	$lang  = aeon_lang();
	$shots = array();
	foreach ( $g[ $slug ]['shots'] as $shot ) {
		$shots[] = array(
			'file' => 'services/' . $slug . '/' . $shot[0],
			'alt'  => isset( $shot[ $lang ] ) ? $shot[ $lang ] : $shot['en'],
		);
	}

	return array(
		'ratio' => $g[ $slug ]['ratio'],
		'cols'  => $g[ $slug ]['cols'],
		'shots' => $shots,
	);
}

/**
 * Display-ready services list (dashboard terms first, else brand defaults),
 * each with a stable slug for deep-linking to its detail block.
 *
 * Each entry also carries the optional custom-icon overrides set in the
 * dashboard (`icon_id` for an uploaded file, `icon_url` for a pasted link);
 * aeon_card_icon() decides which of the three wins at render time.
 *
 * @return array<int,array{name:string,desc:string,icon:string,icon_id:int,icon_url:string,slug:string}>
 */
function aeon_services_list() {
	$out   = array();
	$terms = aeon_section_terms( 'aeon_service' );
	if ( $terms ) {
		foreach ( $terms as $t ) {
			$icon  = get_term_meta( $t->term_id, '_aeon_icon', true );
			$out[] = array(
				'name'     => $t->name,
				'desc'     => $t->description,
				'icon'     => $icon ? $icon : 'globe',
				'icon_id'  => (int) get_term_meta( $t->term_id, '_aeon_iconfile', true ),
				'icon_url' => (string) get_term_meta( $t->term_id, '_aeon_iconurl', true ),
				'slug'     => aeon_service_slug( $t->name ),
			);
		}
	} else {
		$strings = aeon_strings();
		foreach ( aeon_default_services() as $svc ) {
			$ar    = isset( $strings[ $svc['title'] ]['ar'] ) ? $strings[ $svc['title'] ]['ar'] : '';
			$out[] = array(
				'name'     => aeon_t( $svc['title'] ),
				'desc'     => aeon_t( $svc['desc'] ),
				'icon'     => $svc['icon'],
				'icon_id'  => 0,
				'icon_url' => '',
				'slug'     => aeon_service_slug( $ar ),
			);
		}
	}
	return $out;
}

/**
 * Full detail content for a service, in the active language: a long intro
 * paragraph and a "what's included" list. Sourced from the company profile.
 * Feature highlights come from aeon_service_features().
 *
 * @param string $slug Service slug (see aeon_service_slug()).
 * @return array{intro:string,includes:string[]} Empty strings/arrays when unknown.
 */
function aeon_service_details( $slug ) {
	$d = array(
		'photo' => array(
			'intro' => array(
				'ar' => 'نلتقط علامتك التجارية في أبهى صورها. نقدّم تصويراً احترافياً للمنتجات والأطعمة والمنتجات التجارية بإضاءة مدروسة وألوان جذّابة وجودة سينمائية — فكل لقطة تحكي طعم الجودة وتُبرز تفاصيل منتجك بأفضل شكل يعكس هوية علامتك.',
				'en' => 'We capture your brand at its very best. We deliver professional photography for products, food and commercial items with considered lighting, appealing colors and cinematic quality — every shot tells the taste of quality and showcases your product in the best light for your brand.',
			),
			'includes' => array(
				'ar' => array( 'تصوير المنتجات والأطعمة', 'إضاءة استوديو احترافية', 'معالجة وتنقيح الصور (ريتاتش)', 'صور جاهزة للسوشيال ميديا والمتاجر', 'جلسات تصوير في الموقع أو الاستوديو' ),
				'en' => array( 'Product & food photography', 'Professional studio lighting', 'Editing & retouching', 'Assets ready for social & storefronts', 'On-location or in-studio sessions' ),
			),
		),
		'design' => array(
			'intro' => array(
				'ar' => 'نبني هوية بصرية متكاملة تترك انطباعاً دائماً. من الشعارات إلى تصاميم السوشيال ميديا والمطبوعات، نصمّم بأسلوب إبداعي متّسق يعبّر عن شخصية علامتك ويميّزها بصرياً عبر كل نقطة تواصل مع جمهورك.',
				'en' => 'We build a complete visual identity that leaves a lasting impression. From logos to social media designs and print, we design with a consistent creative style that expresses your brand’s personality and sets it apart at every touchpoint.',
			),
			'includes' => array(
				'ar' => array( 'تصميم الشعارات والهوية البصرية', 'تصاميم منشورات السوشيال ميديا', 'المطبوعات والإعلانات', 'أدلة استخدام العلامة (Brand Guidelines)', 'تصاميم مخصّصة لكل حملة ومناسبة' ),
				'en' => array( 'Logo & visual identity design', 'Social media post designs', 'Print & advertising material', 'Brand guidelines', 'Custom designs for every campaign' ),
			),
		),
		'video' => array(
			'intro' => array(
				'ar' => 'نحوّل أفكارك إلى قصص مؤثرة. ننتج ونمنتج فيديوهات ريلز احترافية تصنع الفارق لمحتواك — باستهداف دقيق ومحتوى يجذب الانتباه ويحقق نتائج قابلة للقياس ونمواً مستمراً لعلامتك.',
				'en' => 'We turn your ideas into compelling stories. We produce and edit professional reels that set your content apart — with precise targeting and attention-grabbing content that delivers measurable results and sustained growth.',
			),
			'includes' => array(
				'ar' => array( 'مونتاج فيديوهات ريلز وقصيرة', 'موشن جرافيك احترافي', 'إضافة المؤثرات والترجمة', 'تجهيز الفيديو لكل منصة', 'سرد بصري يخدم رسالتك' ),
				'en' => array( 'Reels & short-video editing', 'Professional motion graphics', 'Effects & subtitles', 'Platform-ready delivery', 'Visual storytelling for your message' ),
			),
		),
		'marketing' => array(
			'intro' => array(
				'ar' => 'نضع استراتيجيات ذكية مبنية على تحليل السوق والمنافسين لتحقيق وصول أكبر وتحوّلات أعلى. ندير حملاتك الإعلانية على جميع المنصات ونحسّن ظهورك في محرّكات البحث لتصل إلى الجمهور المناسب في الوقت المناسب.',
				'en' => 'We craft smart strategies grounded in market and competitor analysis to achieve greater reach and higher conversions. We manage your ad campaigns across every platform and improve your search visibility to reach the right audience at the right time.',
			),
			'includes' => array(
				'ar' => array( 'إدارة إعلانات جوجل ومنصات التواصل', 'تحسين محرّكات البحث (SEO)', 'استهداف دقيق للجمهور', 'تحليل المنافسين وخطة تسويق', 'تقارير أداء دورية' ),
				'en' => array( 'Google & social ad management', 'Search engine optimization (SEO)', 'Precise audience targeting', 'Competitor analysis & marketing plan', 'Regular performance reports' ),
			),
		),
		'social' => array(
			'intro' => array(
				'ar' => 'نبني حضورك الرقمي ونصنع تفاعلاً حقيقياً مع جمهورك. ندير حساباتك بشكل احترافي عبر خطة محتوى متكاملة تجمع بين التصميم والكتابة والنشر والتفاعل لتنمية مجتمع علامتك.',
				'en' => 'We build your digital presence and create genuine engagement with your audience. We manage your accounts professionally through an integrated content plan that blends design, copywriting, publishing and community management to grow your brand’s community.',
			),
			'includes' => array(
				'ar' => array( 'خطة محتوى شهرية', 'تصميم وكتابة المنشورات', 'جدولة ونشر يومي', 'إدارة التفاعل والرسائل', 'تقارير نمو وتفاعل' ),
				'en' => array( 'Monthly content plan', 'Post design & copywriting', 'Daily scheduling & publishing', 'Engagement & message management', 'Growth & engagement reports' ),
			),
		),
		'brand' => array(
			'intro' => array(
				'ar' => 'نخلق هوية قوية ومميّزة تجعل علامتك تبرز بين المنافسين. من اسم العلامة وشعارها إلى نظام الألوان والخطوط ونبرة الصوت، نبني حضوراً بصرياً متماسكاً لا يُنسى.',
				'en' => 'We create a strong, distinctive identity that makes your brand stand out from competitors. From the brand name and logo to the color system, typography and tone of voice, we build a cohesive, unforgettable visual presence.',
			),
			'includes' => array(
				'ar' => array( 'تصميم الشعار والهوية', 'نظام الألوان والخطوط', 'نبرة صوت العلامة', 'تطبيقات الهوية الرقمية والمطبوعة', 'دليل استخدام الهوية' ),
				'en' => array( 'Logo & identity design', 'Color & typography system', 'Brand tone of voice', 'Digital & print applications', 'Brand usage guide' ),
			),
		),
		'web' => array(
			'intro' => array(
				'ar' => 'نصمّم تجارب رقمية متكاملة تجمع بين جمالية الواجهات (UI) وسهولة الاستخدام (UX). نضمن لك موقعاً سريع الاستجابة متوافقاً مع كافة الأجهزة، ليكون مرآة تعكس هوية علامتك وتروي قصتها بأسلوب تقني مبتكر وسلس.',
				'en' => 'We design integrated digital experiences that blend interface aesthetics (UI) with ease of use (UX). We guarantee a fast, responsive site compatible with every device — a mirror that reflects your brand identity and tells its story in an innovative, seamless technical style.',
			),
			'includes' => array(
				'ar' => array( 'تصميم واجهات UI/UX', 'مواقع متجاوبة مع كل الأجهزة', 'المتاجر الإلكترونية', 'سرعة عالية وأمان متقدّم', 'تهيئة للظهور في البحث (SEO)' ),
				'en' => array( 'UI/UX interface design', 'Responsive on every device', 'E-commerce stores', 'High speed & advanced security', 'Search-ready (SEO)' ),
			),
		),
		'analytics' => array(
			'intro' => array(
				'ar' => 'نقيس ما يهم. نقدّم تحليلاً دقيقاً وتقارير تفصيلية دورية لأداء حملاتك وحساباتك، لنحوّل البيانات إلى قرارات تحسّن النتائج باستمرار وتضمن عائداً حقيقياً على استثمارك.',
				'en' => 'We measure what matters. We provide precise analysis and regular, detailed reports on the performance of your campaigns and accounts — turning data into decisions that continuously improve results and ensure a real return on your investment.',
			),
			'includes' => array(
				'ar' => array( 'تقارير أداء دورية', 'تحليل مؤشرات الأداء (KPIs)', 'متابعة التحويلات', 'رؤى وتوصيات للتحسين', 'لوحات متابعة واضحة' ),
				'en' => array( 'Regular performance reports', 'KPI analysis', 'Conversion tracking', 'Insights & recommendations', 'Clear dashboards' ),
			),
		),
	);
	if ( ! isset( $d[ $slug ] ) ) {
		return array( 'intro' => '', 'includes' => array() );
	}
	$lang = aeon_lang();
	$row  = $d[ $slug ];
	return array(
		'intro'    => isset( $row['intro'][ $lang ] ) ? $row['intro'][ $lang ] : $row['intro']['en'],
		'includes' => isset( $row['includes'][ $lang ] ) ? $row['includes'][ $lang ] : $row['includes']['en'],
	);
}

/**
 * Industries the company serves, for the "Industries We Serve" section.
 *
 * @return array<int,array{icon:string,ar:string,en:string}>
 */
function aeon_industries() {
	$terms = aeon_section_terms( 'aeon_industry' );
	if ( ! $terms ) {
		return aeon_default_industries();
	}

	// Terms are Arabic-only. Where a term still carries one of the seeded
	// profile names, reuse that entry's English label so the EN site keeps
	// reading in English; anything the client renamed falls back to its own
	// text in both languages.
	$english = array();
	foreach ( aeon_default_industries() as $default ) {
		$english[ $default['ar'] ] = $default;
	}

	$out = array();
	foreach ( $terms as $term ) {
		$known = isset( $english[ $term->name ] ) ? $english[ $term->name ] : null;
		$out[] = array_merge(
			array(
				'ar'   => $term->name,
				'en'   => $known ? $known['en'] : $term->name,
				'slug' => $known ? $known['slug'] : $term->slug,
			),
			aeon_term_icon( $term->term_id, $known ? $known['icon'] : 'target' )
		);
	}
	return $out;
}

/**
 * The sectors exactly as the company profile tags them on its social-media
 * pages (p22–37). Seeded into the `aeon_industry` taxonomy and used as the
 * fallback before the client has edited them.
 *
 * @return array<int,array{slug:string,icon:string,ar:string,en:string}>
 */
function aeon_default_industries() {
	return array(
		array( 'slug' => 'tourism',    'icon' => 'globe',     'ar' => 'سياحة',        'en' => 'Tourism' ),
		array( 'slug' => 'business',   'icon' => 'briefcase', 'ar' => 'إدارة منشآت',  'en' => 'Business Setup' ),
		array( 'slug' => 'coffee',     'icon' => 'coffee',    'ar' => 'قهوة',         'en' => 'Coffee' ),
		array( 'slug' => 'restaurant', 'icon' => 'food',      'ar' => 'مطاعم',        'en' => 'Restaurants' ),
		array( 'slug' => 'sweets',     'icon' => 'sparkle',   'ar' => 'حلويات',       'en' => 'Sweets' ),
		array( 'slug' => 'juice',      'icon' => 'store',     'ar' => 'عصائر',        'en' => 'Juices' ),
		array( 'slug' => 'meat',       'icon' => 'food',      'ar' => 'لحوم',         'en' => 'Meats' ),
		array( 'slug' => 'perfume',    'icon' => 'sparkle',   'ar' => 'عطور',         'en' => 'Perfumes' ),
		array( 'slug' => 'realestate', 'icon' => 'building',  'ar' => 'عقارات',       'en' => 'Real Estate' ),
		array( 'slug' => 'delivery',   'icon' => 'truck',     'ar' => 'توصيل',        'en' => 'Delivery' ),
		array( 'slug' => 'cleaning',   'icon' => 'shield',    'ar' => 'خدمات تنظيف',  'en' => 'Cleaning Services' ),
	);
}

/**
 * The client-logo roster for the "Success Partners" section. Logos were taken
 * from the official company profile (assets/images/partners/logo-NN.png) and
 * normalised onto uniform white cards. Order matches the profile pages; the
 * name is used for the image alt text.
 *
 * @return array<int,array{file:string,name:string}>
 */
function aeon_partner_logos() {
	$names = array(
		// Profile page 1.
		'Rose & Alreem', 'Mostar Star', 'الريادة لإدارة المنشآت', 'الريادة', 'Delicacy Vita',
		'Golden Man Hair', 'Doctor Travel & Tourism', 'فن جوس', 'Master Clean', 'أثر للتوصيل',
		'ملحمة الوطنية للحوم', 'Crepe City', 'مبادرة دعم', 'Clove Perfumes', 'مطعم المصري',
		'الحارة المصرية', 'United', 'باحث للاستثمار العقاري', 'Stage Line', 'الزيودي للعقارات',
		'د. محمد كيوان',
		// Profile page 2.
		'Vonto', 'Captain Sherif Ismail', 'Primary Care', 'Pro Designer', 'الملكة رودي للمفروشات',
		'Mody Fashion', 'المضياف', 'Elsafy', 'مطعم البجعة', 'Mohamed Home Decoration',
		'كسري أبو سيد', 'Mr. Hamed Elbauomy', 'Hazar', 'واصل للتوصيل', 'DigitRecipe',
		'Electro Master', 'Inside Out', 'Daily Box', 'Nutrascience', 'إدارة بلس',
		'د. أحمد السعدني',
		// Profile page 3.
		'المحامي أحمد سليمان', 'تصميم', 'BPS', 'Fries', 'MTC',
		'Meta Plus', 'الشعلة', 'بنك التنمية الاجتماعية', 'Ahjar', 'Cafe Studio',
		'Aqaar', 'Mdares', 'Dubai Now', 'Bursa Uludağ University', 'RAKEZ',
		'Emerge Petrochem', 'Ingaz', 'Markitee.com', 'كاتب عدل', 'ORK Dubai',
		'Vivian',
	);
	// Client-picked logos win as a complete set — the media library's title (or
	// alt text) becomes the client name. See محتوى الموقع → صور الموقع.
	$picked = aeon_image_gallery( 'partners', 'medium' );
	if ( $picked ) {
		$out = array();
		foreach ( $picked as $img ) {
			$out[] = array( 'url' => $img['url'], 'name' => $img['alt'] );
		}
		return $out;
	}

	$out = array();
	foreach ( $names as $i => $name ) {
		$file = sprintf( 'partners/logo-%02d.png', $i + 1 );
		if ( file_exists( AEON_DIR . '/assets/images/' . $file ) ) {
			$out[] = array( 'url' => aeon_img( $file ), 'name' => $name );
		}
	}
	return $out;
}

/**
 * Inline SVG icon set (single-color, currentColor).
 *
 * @param string $name Icon key.
 * @return string SVG markup.
 */
function aeon_icon( $name ) {
	$icons = array(
		'camera'    => '<path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/>',
		'pen'       => '<path d="M4 20l4-1L19 8a2.5 2.5 0 0 0-4-4L4 15z"/><path d="M14 5l4 4"/>',
		'video'     => '<rect x="3" y="6" width="18" height="12" rx="2"/><path d="M10 9l5 3-5 3z"/>',
		'megaphone' => '<path d="M4 10v4l9 4V6z"/><path d="M13 8c2 0 3 1.5 3 4s-1 4-3 4"/>',
		'social'    => '<rect x="5" y="3" width="14" height="18" rx="3"/><circle cx="12" cy="12" r="3.5"/><circle cx="16.5" cy="7" r="1"/>',
		'target'    => '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="4"/><path d="M12 4v3M20 12h-3"/>',
		'globe'     => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18"/>',
		'chart'     => '<path d="M4 20V4M4 20h16"/><path d="M8 16l3-4 3 2 4-6"/>',
		'bulb'      => '<path d="M9 18h6M10 21h4"/><path d="M12 3a6 6 0 0 0-4 10c1 1 1.5 2 1.5 3h5c0-1 .5-2 1.5-3a6 6 0 0 0-4-10z"/>',
		'team'      => '<circle cx="9" cy="9" r="3"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="M16 7a3 3 0 0 1 0 6M21 20c0-2.5-1.3-4.6-3.3-5.6"/>',
		'shield'    => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M9 12l2 2 4-4"/>',
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'check'     => '<path d="M5 12l5 5 9-11"/>',
		'pin'       => '<path d="M12 21s7-6.3 7-11a7 7 0 1 0-14 0c0 4.7 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/>',
		// Industry icons.
		'food'      => '<path d="M6 3v7a2 2 0 0 0 4 0V3M8 10v11M17 3c-1.6 0-2.5 1.8-2.5 4.5S15.4 12 17 12v9"/>',
		'coffee'    => '<path d="M4 8h13v4a5 5 0 0 1-5 5H9a5 5 0 0 1-5-5z"/><path d="M17 9h2.2a2.3 2.3 0 0 1 0 4.6H17"/><path d="M7 3v2M11 3v2"/>',
		'store'     => '<path d="M4 9V6l1.5-3h13L20 6v3M4 9h16M4 9l1 11h14l1-11M4 9a2 2 0 0 0 4 0 2 2 0 0 0 4 0 2 2 0 0 0 4 0 2 2 0 0 0 4 0"/>',
		'building'  => '<rect x="5" y="3" width="14" height="18" rx="1.5"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2M10 21v-3h4v3"/>',
		'health'    => '<path d="M12 21s-7-4.5-9-9.5A4.6 4.6 0 0 1 12 7a4.6 4.6 0 0 1 9 4.5c-2 5-9 9.5-9 9.5z"/><path d="M12 9v4M10 11h4"/>',
		'education' => '<path d="M12 4 2 9l10 5 10-5z"/><path d="M6 11v5c0 1.3 2.7 3 6 3s6-1.7 6-3v-5"/>',
		'sparkle'   => '<path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/><path d="M18 15l.7 2 2 .7-2 .7-.7 2-.7-2-2-.7 2-.7z"/>',
		'truck'     => '<path d="M3 6h11v9H3zM14 9h4l3 3v3h-7z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17.5" cy="18" r="1.6"/>',
		'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 12h18"/>',
		// Profile-deck icons: growth arrow, photography side labels, web features.
		'growth'    => '<path d="M4 20V4M4 20h16"/><path d="M7 16l4-4 3 2.5L20 7"/><path d="M16 7h4v4"/>',
		'image'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10" r="1.6"/><path d="M5 17l4.5-4.5L13 16l3-2.5L19 17"/>',
		'badge'     => '<circle cx="12" cy="9" r="6"/><path d="m9 14-1.5 7L12 19l4.5 2L15 14"/><path d="m10 9 1.4 1.5L14 7.6"/>',
		'plate'     => '<path d="M4 13a8 8 0 0 1 16 0z"/><path d="M2.5 16.5h19"/><path d="M12 5V3"/>',
		'gauge'     => '<path d="M4.5 18a9 9 0 1 1 15 0"/><path d="M12 14.5 16 9"/><circle cx="12" cy="15.5" r="1.4"/>',
		'devices'   => '<rect x="2" y="5" width="13" height="9" rx="1.5"/><path d="M5 18h7"/><rect x="16.5" y="9" width="5.5" height="10" rx="1.5"/>',
		'eye'       => '<path d="M2 12s3.6-6 10-6 10 6 10 6-3.6 6-10 6-10-6-10-6z"/><circle cx="12" cy="12" r="3"/>',
		'play'      => '<path d="M8 5.5v13l11-6.5z" fill="currentColor" stroke="none"/>',
		'handshake' => '<path d="m11 7-3.5 3.5a2 2 0 0 0 2.8 2.8L12 12l2 2 2 2"/><path d="M3 8.5 7 5h4l3 2.5h3.5L21 10"/><path d="m14 14 2 2M12 16l1.5 1.5"/>',
		'mail'      => '<rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="m3.5 7 7.3 5.4a2 2 0 0 0 2.4 0L20.5 7"/>',
		'phone'     => '<path d="M6.6 3.5h2.2l1.6 4-2 1.3a11.5 11.5 0 0 0 5.3 5.3l1.3-2 4 1.6v2.2a2.6 2.6 0 0 1-2.9 2.6A16.2 16.2 0 0 1 4 6.4 2.6 2.6 0 0 1 6.6 3.5z"/>',
	);
	$path = isset( $icons[ $name ] ) ? $icons[ $name ] : $icons['check'];
	return '<svg class="aeon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}

/**
 * Social links that are actually filled in, ready to render.
 */
function aeon_social_links() {
	$map = array(
		'aeon_instagram' => array( 'Instagram', 'instagram' ),
		'aeon_facebook'  => array( 'Facebook', 'facebook' ),
		'aeon_linkedin'  => array( 'LinkedIn', 'linkedin' ),
		'aeon_x'         => array( 'X', 'x' ),
		'aeon_tiktok'    => array( 'TikTok', 'tiktok' ),
		'aeon_youtube'   => array( 'YouTube', 'youtube' ),
	);
	$out = array();
	foreach ( $map as $key => $data ) {
		$url = aeon_opt( $key );
		if ( $url ) {
			$out[] = array( 'url' => $url, 'label' => $data[0], 'icon' => $data[1] );
		}
	}
	return $out;
}

/**
 * Brand social glyphs.
 */
function aeon_social_icon( $name ) {
	$icons = array(
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1"/>',
		'facebook'  => '<path d="M14 9h3V6h-3c-2 0-3 1.3-3 3v2H8v3h3v6h3v-6h2.5l.5-3H14V9z"/>',
		'linkedin'  => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M7 10v7M7 7v.01M11 17v-4a2 2 0 0 1 4 0v4M11 11v6"/>',
		'x'         => '<path d="M4 4l16 16M20 4L4 20"/>',
		'tiktok'    => '<path d="M14 4v9.5a3.5 3.5 0 1 1-3-3.46"/><path d="M14 4c.5 2.5 2 4 4.5 4.2"/>',
		'youtube'   => '<rect x="3" y="6" width="18" height="12" rx="4"/><path d="M10 9.5l5 2.5-5 2.5z"/>',
	);
	$path = isset( $icons[ $name ] ) ? $icons[ $name ] : '';
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}

/**
 * Excerpt helper with custom length.
 */
function aeon_excerpt( $len = 22 ) {
	return wp_trim_words( get_the_excerpt(), $len, '…' );
}

/**
 * First character of a string, multibyte-safe but tolerant of hosts without
 * the mbstring extension (avoids a fatal "undefined function mb_substr()").
 *
 * @param string $str
 * @return string
 */
function aeon_first_char( $str ) {
	$str = (string) $str;
	if ( '' === $str ) {
		return '';
	}
	if ( function_exists( 'mb_substr' ) ) {
		return mb_substr( $str, 0, 1 );
	}
	// Fallback: grab the first UTF-8 codepoint without mbstring.
	if ( preg_match( '/./u', $str, $m ) ) {
		return $m[0];
	}
	return substr( $str, 0, 1 );
}

/**
 * The company's four expertise bullets (profile p3), as i18n keys.
 *
 * @return string[]
 */
function aeon_expertise_bullets() {
	return array( 'about_b1', 'about_b2', 'about_b3', 'about_b4' );
}

/**
 * The capability strip under the expertise section (profile p3), as i18n keys.
 *
 * @return string[]
 */
function aeon_capability_strip() {
	return array( 'cap_1', 'cap_2', 'cap_3', 'cap_4', 'cap_5' );
}

/**
 * Expertise bullets for the About section, with their icons. Dashboard
 * `aeon_expertise` terms override the profile defaults.
 *
 * @return array<int,array{name:string,icon:string,icon_id:int,icon_url:string}>
 */
function aeon_expertise_list() {
	return aeon_icon_list( 'aeon_expertise', array(
		array( 'key' => 'about_b1', 'icon' => 'target' ),
		array( 'key' => 'about_b2', 'icon' => 'building' ),
		array( 'key' => 'about_b3', 'icon' => 'team' ),
		array( 'key' => 'about_b4', 'icon' => 'growth' ),
	) );
}

/**
 * The capability strip under the About section, with icons. Dashboard
 * `aeon_capability` terms override the profile defaults.
 *
 * @return array<int,array{name:string,icon:string,icon_id:int,icon_url:string}>
 */
function aeon_capability_list() {
	return aeon_icon_list( 'aeon_capability', array(
		array( 'key' => 'cap_1', 'icon' => 'analytics' ),
		array( 'key' => 'cap_2', 'icon' => 'seo' ),
		array( 'key' => 'cap_3', 'icon' => 'megaphone' ),
		array( 'key' => 'cap_4', 'icon' => 'webdev' ),
		array( 'key' => 'cap_5', 'icon' => 'report' ),
	) );
}

/**
 * Animated counters, with icons. Dashboard `aeon_statistic` terms first.
 *
 * @return array<int,array{name:string,val:string,suffix:string,icon:string,icon_id:int,icon_url:string}>
 */
function aeon_stats_list() {
	$out = array();
	foreach ( aeon_section_terms( 'aeon_statistic' ) as $term ) {
		$out[] = array_merge(
			array(
				'name'   => $term->name,
				'val'    => (string) get_term_meta( $term->term_id, '_aeon_number', true ),
				'suffix' => (string) get_term_meta( $term->term_id, '_aeon_suffix', true ),
			),
			aeon_term_icon( $term->term_id, 'chart' )
		);
	}
	if ( $out ) {
		return $out;
	}

	$defaults = array(
		array( 'stat_years', '10', '+', 'trophy' ),
		array( 'stat_clients', '200', '+', 'team' ),
		array( 'stat_projects', '500', '+', 'briefcase' ),
		array( 'stat_satisfaction', '95', '%', 'growth' ),
		array( 'stat_commitment', '100', '%', 'target' ),
	);
	foreach ( $defaults as $d ) {
		$out[] = array(
			'name'     => aeon_t( $d[0] ),
			'val'      => $d[1],
			'suffix'   => $d[2],
			'icon'     => $d[3],
			'icon_id'  => 0,
			'icon_url' => '',
		);
	}
	return $out;
}

/**
 * Company values (profile p4). Dashboard `aeon_value` terms override these.
 *
 * @return array<int,array{icon:string,title:string,desc:string}>
 */
function aeon_value_items() {
	return array(
		array( 'icon' => 'target', 'title' => 'value_results_t',     'desc' => 'value_results_d' ),
		array( 'icon' => 'bulb',   'title' => 'value_innovation_t',  'desc' => 'value_innovation_d' ),
		array( 'icon' => 'team',   'title' => 'value_partnership_t', 'desc' => 'value_partnership_d' ),
	);
}

/**
 * Events & participation captions (profile p5). Dashboard `aeon_event` terms
 * override these.
 *
 * @return string[] i18n keys.
 */
function aeon_event_items() {
	return array( 'event_1', 'event_2', 'event_3', 'event_4', 'event_5' );
}

/**
 * The four ad-campaign result metrics (profile p38).
 *
 * @return array<int,array{icon:string,value_key:string,label_key:string}>
 */
function aeon_ad_metrics() {
	return array(
		array( 'icon' => 'eye',    'value_key' => 'metric_reach_v',  'label_key' => 'metric_reach_l' ),
		array( 'icon' => 'video',  'value_key' => 'metric_views_v',  'label_key' => 'metric_views_l' ),
		array( 'icon' => 'social', 'value_key' => 'metric_engage_v', 'label_key' => 'metric_engage_l' ),
		array( 'icon' => 'team',   'value_key' => 'metric_follow_v', 'label_key' => 'metric_follow_l' ),
		array( 'icon' => 'chart',  'value_key' => 'metric_watch_v',  'label_key' => 'metric_watch_l' ),
		array( 'icon' => 'target', 'value_key' => 'metric_avg_v',    'label_key' => 'metric_avg_l' ),
	);
}

/**
 * Profile office/visit gallery images (cropped from the company profile, p6).
 * Returns web-relative paths that exist on disk.
 *
 * @return string[]
 */
function aeon_office_images() {
	$files = array( 'office/office-main.jpg', 'office/office-1.jpg', 'office/office-2.jpg', 'office/office-3.jpg' );
	$out   = array();
	foreach ( $files as $f ) {
		if ( file_exists( AEON_DIR . '/assets/images/profile/' . $f ) ) {
			$out[] = 'profile/' . $f;
		}
	}
	return $out;
}

/**
 * The categorised portfolio gallery, built from the images cropped out of the
 * company profile (assets/images/profile/<category>/*.jpg). Each category keeps
 * the profile's order; the front-end renders filter tabs from the keys.
 *
 * @return array<string,array{label_key:string,ratio:string,items:string[]}>
 */
function aeon_profile_gallery() {
	// Slug => [ i18n label key, card aspect ('wide' landscape | 'portrait') ].
	$cats = array(
		'web'         => array( 'cat_web',         'wide' ),
		'photography' => array( 'cat_photography', 'wide' ),
		'reels'       => array( 'cat_reels',       'portrait' ),
		'motion'      => array( 'cat_motion',      'portrait' ),
		'social'      => array( 'cat_social',      'portrait' ),
	);
	$out = array();
	foreach ( $cats as $slug => $meta ) {
		$dir = AEON_DIR . '/assets/images/profile/' . $slug;
		if ( ! is_dir( $dir ) ) {
			continue;
		}
		$files = glob( $dir . '/*.jpg' );
		if ( ! $files ) {
			continue;
		}
		sort( $files );
		$items = array();
		foreach ( $files as $f ) {
			$items[] = 'profile/' . $slug . '/' . basename( $f );
		}
		$out[ $slug ] = array(
			'label_key' => $meta[0],
			'ratio'     => $meta[1],
			'items'     => $items,
		);
	}
	return $out;
}

/**
 * Contact numbers: the Customizer phone first, then the other UAE lines the
 * client supplied. Deduplicated on digits so editing the Customizer value to
 * one of the extras does not print it twice.
 *
 * The Egyptian number is deliberately absent — the client designated it for
 * WhatsApp only, so it is exposed through the WhatsApp button instead.
 *
 * @return string[]
 */
function aeon_phone_numbers() {
	// The Customizer value leads when set; otherwise the client's primary line.
	$candidates = array(
		(string) aeon_opt( 'aeon_phone', '+971 56 109 8015' ),
		'+971 56 355 2017',
		'+971 56 569 3330',
	);
	$out        = array();
	$seen       = array();
	foreach ( $candidates as $number ) {
		$number = trim( $number );
		$digits = preg_replace( '/\D+/', '', $number );
		if ( '' === $digits || isset( $seen[ $digits ] ) ) {
			continue;
		}
		$seen[ $digits ] = true;
		$out[]           = $number;
	}
	return $out;
}

/**
 * Branch list supplied by the client (July 2026), used until the branches are
 * edited from the dashboard. Supersedes the older labels printed in the PDF.
 *
 * @return array<int,array{name:string}>
 */
function aeon_default_branches() {
	return array(
		array( 'name' => 'فرع العين وسط المدينة' ),
		array( 'name' => 'فرع العين عود التوبة' ),
		array( 'name' => 'فرع عجمان الراشدية' ),
		array( 'name' => 'فرع مصر الإسكندرية' ),
	);
}

/* -------------------------------------------------------------------------
 * Company-profile brand primitives.
 * ---------------------------------------------------------------------- */

/**
 * Full URL for a file under /assets/images.
 *
 * @param string $relative Path relative to assets/images (e.g. 'profile/x.jpg').
 * @return string
 */
function aeon_img( $relative ) {
	return AEON_URI . '/assets/images/' . ltrim( $relative, '/' );
}

/**
 * Render a section title flanked by the profile's orange dash-dot rules.
 *
 * @param string $html  Already-escaped heading HTML (may contain <span class="accent">).
 * @param string $tag   Heading tag.
 * @param string $class Extra classes for the heading element.
 */
function aeon_rule_heading( $html, $tag = 'h2', $class = 'section-title' ) {
	$tag = preg_match( '/^h[1-6]$/', $tag ) ? $tag : 'h2';
	printf(
		'<div class="rule-heading"><span class="rule-heading__line rule-heading__line--start" aria-hidden="true"></span>' .
		'<%1$s class="rule-heading__text %2$s">%3$s</%1$s>' .
		'<span class="rule-heading__line rule-heading__line--end" aria-hidden="true"></span></div>',
		$tag, // phpcs:ignore -- validated against /^h[1-6]$/ above.
		esc_attr( $class ),
		wp_kses_post( $html )
	);
}

/**
 * Soft hero-style background accents for a section. Purely presentational.
 *
 * Replaces the old corner ribbons and dotted grids site-wide: the same blurred
 * brand orbs, thin ring and spark used behind the hero, dialled down so they sit
 * quietly behind the content. Render it as the first child of the section — the
 * section needs `position: relative`, which `.section` already sets.
 */
function aeon_soft_bg() {
	echo '<span class="soft-bg" aria-hidden="true">'
		. '<span class="soft-bg__orb soft-bg__orb--1"></span>'
		. '<span class="soft-bg__orb soft-bg__orb--2"></span>'
		. '<span class="soft-bg__ring"></span>'
		. '<span class="soft-bg__spark"></span>'
		. '</span>';
}

/**
 * The four-item proof strip repeated under the reels, motion and results
 * pages of the profile.
 *
 * @return array<int,array{icon:string,key:string}>
 */
function aeon_proof_items() {
	return array(
		array( 'icon' => 'target', 'key' => 'proof_1' ),
		array( 'icon' => 'chart',  'key' => 'proof_2' ),
		array( 'icon' => 'growth', 'key' => 'proof_3' ),
		array( 'icon' => 'team',   'key' => 'proof_4' ),
	);
}

/**
 * Render the proof strip.
 *
 * @param string $extra Extra classes (e.g. 'proof-strip--plain').
 */
function aeon_proof_strip( $extra = '' ) {
	echo '<div class="proof-strip ' . esc_attr( $extra ) . '">';
	foreach ( aeon_proof_items() as $item ) {
		echo '<div class="proof-strip__item">' . aeon_icon( $item['icon'] ) . '<span>' . esc_html( aeon_t( $item['key'] ) ) . '</span></div>';
	}
	echo '</div>';
}

/**
 * Ad-campaign platforms, using the AEON-branded creatives supplied by the
 * client. Cards are skipped when their image is missing.
 *
 * @return array<int,array{file:string,title_key:string,text_key:string}>
 */
function aeon_campaign_platforms() {
	$cards = array(
		array( 'file' => 'campaigns/campaign-google.jpg',   'title_key' => 'camp_google_t', 'text_key' => 'camp_google_d' ),
		array( 'file' => 'campaigns/campaign-snapchat.jpg', 'title_key' => 'camp_snap_t',   'text_key' => 'camp_snap_d' ),
		array( 'file' => 'campaigns/campaign-tiktok.jpg',   'title_key' => 'camp_tiktok_t', 'text_key' => 'camp_tiktok_d' ),
		array( 'file' => 'campaigns/campaign-meta.jpg',     'title_key' => 'camp_meta_t',   'text_key' => 'camp_meta_d' ),
	);
	return array_values( array_filter(
		$cards,
		static function ( $c ) {
			return file_exists( AEON_DIR . '/assets/images/' . $c['file'] );
		}
	) );
}

/**
 * The four reel proof cards on the profile's results page (p38), with the
 * view counts printed beside them. Images are the profile's own blurred
 * thumbnails, matching how the client chose to publish them.
 *
 * @return array<int,array{file:string,views:string}>
 */
function aeon_reel_proofs() {
	$views = array( '105', '105', '93.6', '101' );
	$out   = array();
	foreach ( $views as $i => $v ) {
		$file = 'profile/results/reel-' . ( $i + 1 ) . '.jpg';
		if ( file_exists( AEON_DIR . '/assets/images/' . $file ) ) {
			$out[] = array( 'file' => $file, 'views' => $v );
		}
	}
	return $out;
}

/**
 * The four side labels on the profile's photography pages (p11–16).
 *
 * @return array<int,array{icon:string,t:string,d:string}>
 */
function aeon_photo_labels() {
	return array(
		array( 'icon' => 'camera', 't' => 'photo_l1_t', 'd' => 'photo_l1_d' ),
		array( 'icon' => 'image',  't' => 'photo_l2_t', 'd' => 'photo_l2_d' ),
		array( 'icon' => 'badge',  't' => 'photo_l3_t', 'd' => 'photo_l3_d' ),
		array( 'icon' => 'plate',  't' => 'photo_l4_t', 'd' => 'photo_l4_d' ),
	);
}

/**
 * The three feature chips under the profile's web-design page (p10).
 *
 * @return array<int,array{icon:string,t:string,d:string}>
 */
function aeon_web_features() {
	return array(
		array( 'icon' => 'shield',  't' => 'web_f1', 'd' => 'web_f1_d' ),
		array( 'icon' => 'gauge',   't' => 'web_f2', 'd' => 'web_f2_d' ),
		array( 'icon' => 'devices', 't' => 'web_f3', 'd' => 'web_f3_d' ),
	);
}

/**
 * Social-media samples grouped by the industry chip the profile prints on each
 * page (p22–37). Files are named `<industry>[-<variant>]-<n>.jpg`, so the slug
 * before the first digit is the industry key.
 *
 * @return array<int,array{slug:string,label:string,icon:string,items:string[]}>
 */
function aeon_social_groups() {
	$dir = AEON_DIR . '/assets/images/profile/social';
	if ( ! is_dir( $dir ) ) {
		return array();
	}
	$files = glob( $dir . '/*.jpg' );
	if ( ! $files ) {
		return array();
	}
	sort( $files );

	$buckets = array();
	foreach ( $files as $file ) {
		$base = basename( $file, '.jpg' );
		// 'perfume-a-1' => 'perfume', 'meat-2' => 'meat'.
		$slug = preg_replace( '/-(?:[a-z]-)?\d+$/', '', $base );
		$buckets[ $slug ][] = 'profile/social/' . basename( $file );
	}

	// Keep the profile's own running order, then anything unexpected.
	$order = array( 'tourism', 'business', 'coffee', 'perfume', 'sweets', 'juice', 'delivery', 'cleaning', 'realestate', 'meat', 'restaurant' );
	$known = array_column( aeon_default_industries(), null, 'slug' );

	$out = array();
	foreach ( array_merge( $order, array_keys( $buckets ) ) as $slug ) {
		if ( empty( $buckets[ $slug ] ) ) {
			continue;
		}
		$meta  = isset( $known[ $slug ] ) ? $known[ $slug ] : array( 'icon' => 'social', 'ar' => $slug, 'en' => $slug );
		$out[] = array(
			'slug'  => $slug,
			'label' => 'ar' === aeon_lang() ? $meta['ar'] : $meta['en'],
			'icon'  => $meta['icon'],
			'items' => $buckets[ $slug ],
		);
		unset( $buckets[ $slug ] );
	}
	return $out;
}
