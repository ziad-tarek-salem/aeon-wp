<?php
/**
 * Favicon — served from the theme, never from the database.
 *
 * The dashboard's Site Icon is an attachment ID in wp_options pointing at a file
 * under wp-content/uploads. The theme ships as a zip (tools/package-theme.ps1),
 * so on a deploy neither the option nor the upload travels: every site keeps
 * whatever icon its own database already holds. That is why replacing the icon
 * here and uploading the theme changed nothing on production — the option there
 * still names the JPEG the site started with, and a JPEG has no alpha channel,
 * which is what put the white square behind the logo in the first place. The
 * fix is not a better icon in the database; it is not using the database.
 *
 * One filter carries the whole thing. get_site_icon_url() runs it *before* it
 * looks at the option, so has_site_icon() turns true on its own and core's
 * wp_site_icon() prints the full set — 32, 192, apple-touch 180 and the
 * msapplication tile — against these files. do_favicon() sends a bare
 * /favicon.ico request through the same function, so that lands here too, and
 * so does the admin. Nothing is left resolving to the old attachment.
 *
 * @package AEON
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon files shipped in the theme, smallest first.
 *
 * Sizes rather than one master because a 512px PNG is ~225KB and a tab icon has
 * no business costing that; the 32px file is ~2KB. Every one carries a real
 * alpha channel, so the corners outside the logo's ring stay clear on a dark
 * browser tab instead of showing a white box.
 *
 * @return array<int,string> Pixel size => file name.
 */
function aeon_favicon_sizes() {
	return array(
		32  => 'favicon-32.png',
		180 => 'favicon-180.png',
		192 => 'favicon-192.png',
		270 => 'favicon-270.png',
		512 => 'favicon.png',
	);
}

/**
 * URL of the theme icon that best answers a requested size.
 *
 * Smallest file at least as large as the request, so nothing is ever upscaled;
 * anything above the largest shipped size gets that one. The mtime query keeps a
 * replaced icon from being served out of cache on the next deploy — browsers
 * hold favicons far longer than ordinary images, and the file name alone would
 * not tell them anything changed.
 *
 * @param int $size Requested size in pixels.
 * @return string Empty when the file is missing, which hands control back to core.
 */
function aeon_favicon_url( $size = 512 ) {
	$sizes = aeon_favicon_sizes();
	$pick  = end( $sizes );

	foreach ( $sizes as $px => $file ) {
		if ( $size <= $px ) {
			$pick = $file;
			break;
		}
	}

	$path = get_theme_file_path( 'assets/images/' . $pick );

	if ( ! file_exists( $path ) ) {
		return '';
	}

	return add_query_arg( 'v', filemtime( $path ), get_theme_file_uri( 'assets/images/' . $pick ) );
}

/**
 * Point every site-icon consumer at the theme's PNG.
 *
 * Filtered rather than printed into wp_head, so the front end, the admin and
 * anything else that calls get_site_icon_url() all agree — and so a stale Site
 * Icon left in the database cannot surface anywhere.
 *
 * Deliberately outranks the dashboard setting: an icon that only exists in one
 * site's options is exactly what failed to deploy. A client who would rather
 * manage it from Settings → General can return false from 'aeon_theme_favicon',
 * and core's own resolution takes over untouched.
 *
 * @param string $url  URL core resolved from the Site Icon option.
 * @param int    $size Requested size in pixels.
 * @return string
 */
function aeon_site_icon_url( $url, $size = 512 ) {
	if ( ! apply_filters( 'aeon_theme_favicon', true ) ) {
		return $url;
	}

	$favicon = aeon_favicon_url( $size );

	return $favicon ? $favicon : $url;
}
add_filter( 'get_site_icon_url', 'aeon_site_icon_url', 10, 2 );
