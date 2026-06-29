<?php
/**
 * Enqueue styles, scripts and third-party libraries.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a local asset URL if the file is vendored in the theme, otherwise a
 * CDN fallback. Keeps the front-end working with zero external dependencies
 * when the libs are bundled, and degrades gracefully if they are not.
 *
 * @param string $relative Path relative to /assets (e.g. 'js/lib/gsap.min.js').
 * @param string $cdn      CDN URL to use when the local file is missing.
 * @return string
 */
function aeon_lib_url( $relative, $cdn ) {
	$local_path = AEON_DIR . '/assets/' . $relative;
	if ( file_exists( $local_path ) ) {
		return AEON_URI . '/assets/' . $relative;
	}
	return $cdn;
}

function aeon_assets() {
	$ver = AEON_VERSION;

	// Google Fonts: Cairo (Arabic) + Poppins (English).
	wp_enqueue_style(
		'aeon-fonts',
		'https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Poppins:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	// Swiper CSS — prefer the vendored copy, fall back to CDN if it is absent.
	wp_enqueue_style( 'swiper', aeon_lib_url( 'css/lib/swiper-bundle.min.css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css' ), array(), '11' );

	// Main stylesheet.
	wp_enqueue_style( 'aeon-main', AEON_URI . '/assets/css/main.css', array( 'aeon-fonts' ), $ver );

	// WordPress required style.css (theme header).
	wp_enqueue_style( 'aeon-style', get_stylesheet_uri(), array( 'aeon-main' ), $ver );

	// --- Scripts (all deferred). Local-first with CDN fallback. ---
	wp_enqueue_script( 'gsap',    aeon_lib_url( 'js/lib/gsap.min.js',          'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js' ), array(), '3.12.5', true );
	wp_enqueue_script( 'gsap-st', aeon_lib_url( 'js/lib/ScrollTrigger.min.js', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js' ), array( 'gsap' ), '3.12.5', true );
	wp_enqueue_script( 'lenis',   aeon_lib_url( 'js/lib/lenis.min.js',         'https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js' ), array(), '1.1.13', true );
	wp_enqueue_script( 'swiper',  aeon_lib_url( 'js/lib/swiper-bundle.min.js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js' ), array(), '11', true );

	wp_enqueue_script( 'aeon-app', AEON_URI . '/assets/js/app.js', array( 'gsap', 'gsap-st', 'lenis', 'swiper' ), $ver, true );

	wp_localize_script( 'aeon-app', 'AEON', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'aeon_contact' ),
		'lang'    => aeon_lang(),
		'rtl'     => aeon_is_rtl() ? 1 : 0,
		'i18n'    => array(
			'sending'  => aeon_t( 'form_sending' ),
			'success'  => aeon_t( 'form_success' ),
			'error'    => aeon_t( 'form_error' ),
			'required' => aeon_t( 'form_required' ),
		),
	) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'aeon_assets' );

/**
 * Add defer to heavy third-party scripts.
 */
function aeon_defer_scripts( $tag, $handle ) {
	$defer = array( 'gsap', 'gsap-st', 'lenis', 'swiper', 'aeon-app' );
	if ( in_array( $handle, $defer, true ) && false === strpos( $tag, 'defer' ) ) {
		$tag = str_replace( ' src', ' defer src', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'aeon_defer_scripts', 10, 2 );

/**
 * Preconnect to font/CDN origins for faster first paint.
 */
function aeon_resource_hints( $hints, $relation ) {
	if ( 'preconnect' === $relation ) {
		$hints[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
		$hints[] = 'https://cdn.jsdelivr.net';
	}
	return $hints;
}
add_filter( 'wp_resource_hints', 'aeon_resource_hints', 10, 2 );
