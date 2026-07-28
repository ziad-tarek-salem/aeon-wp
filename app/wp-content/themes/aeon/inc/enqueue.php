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

/**
 * Cache-busting version for an asset the theme ships itself.
 *
 * AEON_VERSION alone is not enough: editing main.css without bumping the
 * constant leaves the ?ver= query identical, so browsers and host caches keep
 * serving the stale file. Fall back to the constant if the file is missing.
 *
 * @param string $relative Path relative to the theme root (e.g. 'assets/css/main.css').
 * @return string
 */
function aeon_asset_ver( $relative ) {
	$path = AEON_DIR . '/' . ltrim( $relative, '/' );
	if ( file_exists( $path ) ) {
		return AEON_VERSION . '.' . filemtime( $path );
	}
	return AEON_VERSION;
}

function aeon_assets() {
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
	wp_enqueue_style( 'aeon-main', AEON_URI . '/assets/css/main.css', array( 'aeon-fonts' ), aeon_asset_ver( 'assets/css/main.css' ) );

	// Company-profile brand layer: the dash-dot title rules, corner ribbons,
	// proof strips and the sections built straight from the profile deck.
	wp_enqueue_style( 'aeon-profile', AEON_URI . '/assets/css/profile.css', array( 'aeon-main' ), aeon_asset_ver( 'assets/css/profile.css' ) );

	// WordPress required style.css (theme header).
	wp_enqueue_style( 'aeon-style', get_stylesheet_uri(), array( 'aeon-profile' ), aeon_asset_ver( 'style.css' ) );

	// --- Scripts (all deferred). Local-first with CDN fallback. ---
	wp_enqueue_script( 'gsap',    aeon_lib_url( 'js/lib/gsap.min.js',          'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js' ), array(), '3.12.5', true );
	wp_enqueue_script( 'gsap-st', aeon_lib_url( 'js/lib/ScrollTrigger.min.js', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js' ), array( 'gsap' ), '3.12.5', true );
	wp_enqueue_script( 'lenis',   aeon_lib_url( 'js/lib/lenis.min.js',         'https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js' ), array(), '1.1.13', true );
	wp_enqueue_script( 'swiper',  aeon_lib_url( 'js/lib/swiper-bundle.min.js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js' ), array(), '11', true );

	wp_enqueue_script( 'aeon-app', AEON_URI . '/assets/js/app.js', array( 'gsap', 'gsap-st', 'lenis', 'swiper' ), aeon_asset_ver( 'assets/js/app.js' ), true );

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
 * Drop the Hostinger Reach subscription-block assets from pages that do not use
 * the block.
 *
 * The host pre-installs that plugin on production, and it enqueues its block CSS
 * and view script site-wide. None of the AEON templates render post content, so
 * the block never appears and the two files are pure weight — and they are the
 * only thing left making the deployed markup differ from the reference site.
 * The guard keeps them for any page that genuinely embeds the block, so turning
 * the feature on later still works.
 */
function aeon_dequeue_unused_host_assets() {
	if ( is_singular() && has_block( 'hostinger-reach/subscription' ) ) {
		return;
	}

	foreach ( array( 'hostinger-reach-subscription-block', 'hostinger-reach-subscription-block-view' ) as $handle ) {
		wp_dequeue_style( $handle );
		wp_dequeue_script( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'aeon_dequeue_unused_host_assets', 100 );

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
