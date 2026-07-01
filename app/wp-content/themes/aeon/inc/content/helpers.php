<?php
/**
 * Shared helpers for the editable content sections.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Icon keys (from aeon_icon()) offered in the icon picker, with Arabic labels.
 *
 * @return array<string,string> key => Arabic label.
 */
function aeon_icon_choices() {
	return array(
		'camera'    => 'كاميرا / تصوير',
		'pen'       => 'قلم / تصميم',
		'video'     => 'فيديو / مونتاج',
		'megaphone' => 'تسويق',
		'social'    => 'سوشيال ميديا',
		'target'    => 'هدف',
		'globe'     => 'الويب',
		'chart'     => 'تحليلات',
		'bulb'      => 'فكرة',
		'team'      => 'فريق',
		'shield'    => 'درع / ثقة',
	);
}

/**
 * Guard a post meta-box save: verify nonce, skip autosave, check capability.
 *
 * @param int    $post_id Post being saved.
 * @param string $nonce   Nonce field name in $_POST.
 * @param string $action  Nonce action.
 * @return bool True when it is safe to persist meta.
 */
function aeon_can_save_meta( $post_id, $nonce, $action ) {
	if ( ! isset( $_POST[ $nonce ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce ] ) ), $action ) ) {
		return false;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}
	return current_user_can( 'edit_post', $post_id );
}

/**
 * Ordered terms for a section taxonomy (creation order, like mostarstar).
 *
 * @param string $taxonomy Taxonomy slug.
 * @param int    $limit    Max terms (0 = all).
 * @return WP_Term[]
 */
function aeon_section_terms( $taxonomy, $limit = 0 ) {
	$terms = get_terms( array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => false,
		'orderby'    => 'term_id',
		'order'      => 'ASC',
		'number'     => $limit,
	) );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms : array();
}

/**
 * Branch locations for the footer, normalised and ready to render.
 *
 * Each branch is an `aeon_branch` term: name = branch name, description = the
 * address/description, plus `_aeon_lat` / `_aeon_lng` meta. A Google Maps link
 * is built only when both coordinates are present.
 *
 * @return array<int,array{name:string,desc:string,lat:string,lng:string,maps_url:string}>
 */
function aeon_branch_locations() {
	$out = array();
	foreach ( aeon_section_terms( 'aeon_branch' ) as $term ) {
		$lat        = (string) get_term_meta( $term->term_id, '_aeon_lat', true );
		$lng        = (string) get_term_meta( $term->term_id, '_aeon_lng', true );
		$has_coords = is_numeric( $lat ) && is_numeric( $lng );

		$out[] = array(
			'name'     => $term->name,
			'desc'     => $term->description,
			'lat'      => $lat,
			'lng'      => $lng,
			// api=1 search URL opens the exact point in Google Maps on web + app.
			'maps_url' => $has_coords
				? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $lat . ',' . $lng )
				: '',
		);
	}
	return $out;
}
