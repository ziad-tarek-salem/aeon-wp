<?php
/**
 * Safe SVG uploads for the icon fields.
 *
 * WordPress blocks SVG by design: an SVG is an XML document that can carry
 * <script>, event handlers and external references, so an uploaded file becomes
 * stored XSS the moment someone opens it. We still want SVG icons, so this file
 * opens the door narrowly:
 *
 *   1. Only users who can `manage_options` (administrators) may upload SVG.
 *   2. Every uploaded SVG is rewritten through an ALLOW-LIST sanitiser — unknown
 *      elements and attributes are dropped rather than pattern-matched away.
 *   3. Files that cannot be parsed as XML are rejected outright.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Users allowed to upload SVG files. */
function aeon_can_upload_svg() {
	return current_user_can( 'manage_options' );
}

/**
 * Register the SVG mime type for permitted users only.
 *
 * @param array $mimes Allowed mime types.
 * @return array
 */
function aeon_allow_svg_mime( $mimes ) {
	if ( aeon_can_upload_svg() ) {
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	} else {
		unset( $mimes['svg'], $mimes['svgz'] );
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'aeon_allow_svg_mime' );

/**
 * Core sniffs real file contents and would call our SVG "text/plain"; tell it
 * the extension/mime pair is legitimate when the name really ends in .svg.
 *
 * @param array  $data     ext/type/proper_filename result.
 * @param string $file     Full path to the file.
 * @param string $filename Original file name.
 * @return array
 */
function aeon_svg_filetype_check( $data, $file, $filename ) {
	if ( ! aeon_can_upload_svg() ) {
		return $data;
	}
	$ext = strtolower( (string) pathinfo( $filename, PATHINFO_EXTENSION ) );
	if ( 'svg' === $ext || 'svgz' === $ext ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'aeon_svg_filetype_check', 10, 3 );

/**
 * Sanitise an SVG the moment it lands in uploads. Anything that fails to parse,
 * or that sanitises to nothing, is rejected with an error the uploader sees.
 *
 * @param array $file Entry from $_FILES as handed to wp_handle_upload.
 * @return array
 */
function aeon_sanitize_svg_upload( $file ) {
	if ( empty( $file['tmp_name'] ) || ! empty( $file['error'] ) ) {
		return $file;
	}

	$name = isset( $file['name'] ) ? $file['name'] : '';
	$ext  = strtolower( (string) pathinfo( $name, PATHINFO_EXTENSION ) );
	if ( 'svg' !== $ext && 'svgz' !== $ext ) {
		return $file;
	}

	if ( ! aeon_can_upload_svg() ) {
		$file['error'] = 'رفع ملفات SVG متاح لمديري الموقع فقط.';
		return $file;
	}

	$raw = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( false === $raw || '' === trim( (string) $raw ) ) {
		$file['error'] = 'تعذّر قراءة ملف SVG.';
		return $file;
	}

	// .svgz is gzip-compressed SVG; expand before sanitising.
	if ( 'svgz' === $ext && function_exists( 'gzdecode' ) ) {
		$expanded = @gzdecode( $raw ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		if ( false !== $expanded ) {
			$raw = $expanded;
		}
	}

	$clean = aeon_sanitize_svg_markup( $raw );
	if ( '' === $clean ) {
		$file['error'] = 'ملف SVG غير صالح أو يحتوي على عناصر غير مسموح بها.';
		return $file;
	}

	// Always store the sanitised, uncompressed markup under a .svg name.
	if ( false === file_put_contents( $file['tmp_name'], $clean ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions
		$file['error'] = 'تعذّر حفظ ملف SVG بعد تنظيفه.';
		return $file;
	}
	$file['name'] = preg_replace( '/\.svgz$/i', '.svg', $name );
	$file['type'] = 'image/svg+xml';

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'aeon_sanitize_svg_upload' );
// Sideloads (programmatic imports, media_handle_sideload) take a different path.
add_filter( 'wp_handle_sideload_prefilter', 'aeon_sanitize_svg_upload' );

/**
 * Rewrite SVG markup keeping only allow-listed elements and attributes.
 *
 * @param string $svg Raw SVG document.
 * @return string Sanitised markup, or '' when the input is unusable.
 */
function aeon_sanitize_svg_markup( $svg ) {
	if ( ! class_exists( 'DOMDocument' ) || '' === trim( (string) $svg ) ) {
		return '';
	}

	// Drop XML processing instructions and any DOCTYPE (blocks entity tricks).
	// The DOCTYPE pattern must swallow an internal subset — `<!DOCTYPE svg [ … ]>`
	// contains its own `>` characters, and stopping at the first one would leave
	// a stray `]>` that makes every Illustrator/Inkscape export fail to parse.
	$svg = preg_replace( '/<\?xml[^>]*\?>/i', '', $svg );
	$svg = preg_replace( '/<!DOCTYPE[^>\[]*(\[[^\]]*\])?[^>]*>/is', '', $svg );
	$svg = preg_replace( '/<!ENTITY[^>]*>/is', '', $svg );

	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = false;
	$doc->strictErrorChecking = false;

	$prev = libxml_use_internal_errors( true );
	// LIBXML_NONET forbids network access while parsing. External entity loading
	// is off by default on PHP 8, and no DOCTYPE survived the strip above.
	$ok = $doc->loadXML( $svg, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );
	libxml_clear_errors();
	libxml_use_internal_errors( $prev );

	if ( ! $ok || ! $doc->documentElement ) {
		return '';
	}
	if ( 'svg' !== strtolower( $doc->documentElement->localName ) ) {
		return '';
	}

	aeon_svg_scrub_node( $doc->documentElement );

	$out = $doc->saveXML( $doc->documentElement );
	return is_string( $out ) ? trim( $out ) : '';
}

/** Elements an icon may legitimately contain. */
function aeon_svg_allowed_elements() {
	return array(
		'svg', 'g', 'defs', 'symbol', 'use', 'title', 'desc', 'style',
		'path', 'circle', 'ellipse', 'rect', 'line', 'polyline', 'polygon', 'image',
		'linearGradient', 'radialGradient', 'stop', 'clipPath', 'mask',
		'text', 'tspan', 'pattern', 'filter',
		'feGaussianBlur', 'feOffset', 'feBlend', 'feMerge', 'feMergeNode',
		'feColorMatrix', 'feFlood', 'feComposite',
	);
}

/** Attributes an icon may legitimately carry. */
function aeon_svg_allowed_attributes() {
	return array(
		'id', 'class', 'style', 'transform', 'viewbox', 'xmlns', 'xmlns:xlink',
		'width', 'height', 'x', 'y', 'x1', 'y1', 'x2', 'y2', 'cx', 'cy', 'r', 'rx', 'ry',
		'd', 'points', 'fill', 'fill-rule', 'fill-opacity', 'clip-rule', 'clip-path',
		'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'stroke-opacity',
		'stroke-dasharray', 'stroke-dashoffset', 'stroke-miterlimit',
		'opacity', 'offset', 'stop-color', 'stop-opacity', 'gradientunits',
		'gradienttransform', 'spreadmethod', 'patternunits', 'maskunits',
		'clippathunits', 'filterunits', 'preserveaspectratio',
		'font-family', 'font-size', 'font-weight', 'text-anchor', 'dominant-baseline',
		'aria-hidden', 'role', 'focusable', 'overflow', 'mask', 'filter',
		'in', 'in2', 'result', 'stddeviation', 'dx', 'dy', 'mode', 'values', 'type',
		'href', 'xlink:href',
	);
}

/**
 * Recursively strip disallowed nodes and attributes, in place.
 *
 * @param DOMElement $node Element to scrub.
 */
function aeon_svg_scrub_node( $node ) {
	$allowed_els   = array_map( 'strtolower', aeon_svg_allowed_elements() );
	$allowed_attrs = aeon_svg_allowed_attributes();

	// Walk children backwards so removals don't disturb the live NodeList.
	for ( $i = $node->childNodes->length - 1; $i >= 0; $i-- ) {
		$child = $node->childNodes->item( $i );

		if ( XML_ELEMENT_NODE === $child->nodeType ) {
			if ( ! in_array( strtolower( $child->localName ), $allowed_els, true ) ) {
				$node->removeChild( $child );
				continue;
			}
			// <style> carries CSS as TEXT, which the attribute pass below never
			// sees — an @import or url() in there would still reach the network.
			if ( 'style' === strtolower( $child->localName ) ) {
				$child->textContent = aeon_svg_sanitize_css( $child->textContent );
				continue;
			}
			aeon_svg_scrub_node( $child );
			continue;
		}

		// Comments and CDATA can smuggle markup past naive parsers — drop them.
		if ( XML_COMMENT_NODE === $child->nodeType || XML_PI_NODE === $child->nodeType ) {
			$node->removeChild( $child );
		}
	}

	if ( ! $node->hasAttributes() ) {
		return;
	}

	for ( $i = $node->attributes->length - 1; $i >= 0; $i-- ) {
		$attr = $node->attributes->item( $i );
		$name = strtolower( $attr->nodeName );
		$val  = (string) $attr->nodeValue;

		// Every event handler, no exceptions.
		if ( 0 === strpos( $name, 'on' ) ) {
			$node->removeAttribute( $attr->nodeName );
			continue;
		}
		if ( ! in_array( $name, $allowed_attrs, true ) ) {
			$node->removeAttribute( $attr->nodeName );
			continue;
		}
		// References may only point inside this document or at an inline image.
		if ( 'href' === $name || 'xlink:href' === $name ) {
			$trimmed = ltrim( $val );
			if ( 0 !== strpos( $trimmed, '#' ) && 0 !== stripos( $trimmed, 'data:image/' ) ) {
				$node->removeAttribute( $attr->nodeName );
				continue;
			}
		}
		// Block script/data URLs and CSS escapes hiding in style or paint values.
		$flat = strtolower( preg_replace( '/\s|&#x?[0-9a-f]+;?/i', '', $val ) );
		if ( false !== strpos( $flat, 'javascript:' )
			|| false !== strpos( $flat, 'vbscript:' )
			|| false !== strpos( $flat, '@import' )
			|| false !== strpos( $flat, 'expression(' ) ) {
			$node->removeAttribute( $attr->nodeName );
		}
	}
}

/**
 * Scrub the CSS inside an SVG <style> block.
 *
 * Keeps ordinary rules (icon sets from libraries often style classes this way)
 * but removes anything that can reach the network or execute: @import, url()
 * pointing anywhere other than an inline data: image or a same-document
 * fragment, and the legacy script pseudo-protocols.
 *
 * @param string $css Raw stylesheet text.
 * @return string
 */
function aeon_svg_sanitize_css( $css ) {
	$css = (string) $css;

	// @import / @charset / @namespace — none belong in an icon, all fetch or shift parsing.
	$css = preg_replace( '/@(import|charset|namespace)[^;{]*(;|\{[^}]*\})/is', '', $css );

	// url(...) is allowed only for inline data: images and document fragments.
	$css = preg_replace_callback(
		'/url\(\s*([\'"]?)(.*?)\1\s*\)/is',
		function ( $m ) {
			$target = trim( $m[2] );
			if ( 0 === strpos( $target, '#' ) || 0 === stripos( $target, 'data:image/' ) ) {
				return $m[0];
			}
			return 'none';
		},
		$css
	);

	// Script pseudo-protocols and IE expressions, whitespace-obfuscation included.
	if ( preg_match( '/javascript\s*:|vbscript\s*:|expression\s*\(|behavior\s*:|-moz-binding/i', $css ) ) {
		$css = preg_replace( '/javascript\s*:|vbscript\s*:|behavior\s*:|-moz-binding/i', '', $css );
		$css = preg_replace( '/expression\s*\([^)]*\)/i', '', $css );
	}

	return $css;
}

/**
 * Give SVG attachments a usable size so media-library thumbnails and the term
 * editor preview don't collapse to 0×0.
 *
 * @param array|false  $image      Existing [url, width, height] or false.
 * @param int          $id         Attachment ID.
 * @return array|false
 */
function aeon_svg_attachment_size( $image, $id ) {
	if ( 'image/svg+xml' !== get_post_mime_type( $id ) ) {
		return $image;
	}
	$url = wp_get_attachment_url( $id );
	return $url ? array( $url, 64, 64, false ) : $image;
}
add_filter( 'wp_get_attachment_image_src', 'aeon_svg_attachment_size', 10, 2 );

/**
 * Show the actual SVG in the media library grid instead of a generic file icon.
 *
 * @param array   $response   Prepared attachment data.
 * @param WP_Post $attachment Attachment post.
 * @return array
 */
function aeon_svg_media_response( $response, $attachment ) {
	if ( 'image/svg+xml' !== $response['mime'] ) {
		return $response;
	}
	$url = wp_get_attachment_url( $attachment->ID );
	if ( ! $url ) {
		return $response;
	}
	$response['sizes'] = array(
		'full'      => array( 'url' => $url, 'width' => 64, 'height' => 64, 'orientation' => 'portrait' ),
		'thumbnail' => array( 'url' => $url, 'width' => 64, 'height' => 64, 'orientation' => 'portrait' ),
	);
	$response['icon'] = $url;
	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'aeon_svg_media_response', 10, 2 );
