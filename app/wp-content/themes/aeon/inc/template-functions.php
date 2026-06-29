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
 */
function aeon_language_attributes( $output ) {
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
		array( 'icon' => 'social',    'title' => 'svc_social_t',     'desc' => 'svc_social_d' ),
		array( 'icon' => 'target',    'title' => 'svc_brand_t',      'desc' => 'svc_brand_d' ),
		array( 'icon' => 'globe',     'title' => 'svc_web_t',        'desc' => 'svc_web_d' ),
		array( 'icon' => 'chart',     'title' => 'svc_analytics_t',  'desc' => 'svc_analytics_d' ),
	);
}

/**
 * "Why choose us" items.
 */
function aeon_why_items() {
	return array(
		array( 'icon' => 'target',    'title' => 'why_1_t', 'desc' => 'why_1_d' ),
		array( 'icon' => 'chart',     'title' => 'why_2_t', 'desc' => 'why_2_d' ),
		array( 'icon' => 'bulb',      'title' => 'why_3_t', 'desc' => 'why_3_d' ),
		array( 'icon' => 'team',      'title' => 'why_4_t', 'desc' => 'why_4_d' ),
		array( 'icon' => 'shield',    'title' => 'why_5_t', 'desc' => 'why_5_d' ),
	);
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
