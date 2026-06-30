<?php
/**
 * One-time data migration + seeding for the section taxonomies.
 *
 * Moves the previous CPT-based content (service / why_card / aeon_stat /
 * testimonial posts) into the new terms — preserving any edits the client made —
 * then deletes the old posts. On a fresh install with no old posts, seeds the
 * Arabic defaults instead. Idempotent: guarded by an option flag and per-taxonomy
 * emptiness checks. Our Works (portfolio) is unchanged.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AEON_MIGRATED_FLAG = 'aeon_sections_migrated_v2';

/** old post type => new taxonomy. */
function aeon_migration_map() {
	return array(
		'service'     => 'aeon_service',
		'why_card'    => 'aeon_why',
		'aeon_stat'   => 'aeon_statistic',
		'testimonial' => 'aeon_review',
	);
}

/** Arabic value of an i18n string key. */
function aeon_ar( $key ) {
	$s = aeon_strings();
	return isset( $s[ $key ]['ar'] ) ? $s[ $key ]['ar'] : $key;
}

/** Run once, under an admin request. */
function aeon_maybe_migrate_sections() {
	if ( get_option( AEON_MIGRATED_FLAG ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	foreach ( aeon_migration_map() as $old_type => $taxonomy ) {
		if ( ! aeon_tax_is_empty( $taxonomy ) ) {
			continue; // already populated.
		}
		$old_ids = aeon_old_post_ids( $old_type );
		if ( $old_ids ) {
			aeon_migrate_posts( $taxonomy, $old_ids );
		} else {
			aeon_seed_terms( $taxonomy );
		}
	}

	// Retire the old posts now that their content lives in terms.
	foreach ( array_keys( aeon_migration_map() ) as $old_type ) {
		foreach ( aeon_old_post_ids( $old_type ) as $pid ) {
			wp_delete_post( $pid, true );
		}
	}

	update_option( AEON_MIGRATED_FLAG, time() );
}
add_action( 'admin_init', 'aeon_maybe_migrate_sections' );

/** True when a taxonomy has no terms. */
function aeon_tax_is_empty( $taxonomy ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'number' => 1, 'fields' => 'ids' ) );
	return empty( $terms ) || is_wp_error( $terms );
}

/** Old post IDs for a (possibly unregistered) post type, in display order. */
function aeon_old_post_ids( $post_type ) {
	global $wpdb;
	$ids = $wpdb->get_col( $wpdb->prepare(
		"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ('publish','draft','pending','private') ORDER BY menu_order ASC, ID ASC",
		$post_type
	) );
	return array_map( 'intval', (array) $ids );
}

/** Create one term with description + meta; returns term_id or 0. */
function aeon_make_term( $taxonomy, $name, $description = '', $meta = array() ) {
	$name = trim( (string) $name );
	if ( '' === $name ) {
		return 0;
	}
	$existing = get_term_by( 'name', $name, $taxonomy );
	if ( $existing ) {
		$term_id = (int) $existing->term_id;
	} else {
		$res = wp_insert_term( $name, $taxonomy, array( 'description' => $description ) );
		if ( is_wp_error( $res ) ) {
			return 0;
		}
		$term_id = (int) $res['term_id'];
	}
	foreach ( $meta as $k => $v ) {
		if ( '' !== $v && null !== $v ) {
			update_term_meta( $term_id, $k, $v );
		}
	}
	return $term_id;
}

/** Migrate old posts of one type into terms. */
function aeon_migrate_posts( $taxonomy, $ids ) {
	foreach ( $ids as $pid ) {
		$post = get_post( $pid );
		if ( ! $post ) {
			continue;
		}
		switch ( $taxonomy ) {
			case 'aeon_service':
			case 'aeon_why':
				aeon_make_term( $taxonomy, $post->post_title, (string) get_post_meta( $pid, '_aeon_desc', true ), array(
					'_aeon_icon' => get_post_meta( $pid, '_aeon_icon', true ),
				) );
				break;
			case 'aeon_statistic':
				aeon_make_term( $taxonomy, $post->post_title, '', array(
					'_aeon_number' => get_post_meta( $pid, '_aeon_number', true ),
					'_aeon_suffix' => get_post_meta( $pid, '_aeon_suffix', true ),
				) );
				break;
			case 'aeon_review':
				aeon_make_term( $taxonomy, $post->post_title, (string) $post->post_content, array(
					'_aeon_role'  => get_post_meta( $pid, '_aeon_role', true ),
					'_aeon_image' => (int) get_post_thumbnail_id( $pid ),
				) );
				break;
		}
	}
}

/** Seed Arabic defaults for a fresh install (no prior posts). */
function aeon_seed_terms( $taxonomy ) {
	switch ( $taxonomy ) {
		case 'aeon_service':
			foreach ( aeon_default_services() as $s ) {
				aeon_make_term( $taxonomy, aeon_ar( $s['title'] ), aeon_ar( $s['desc'] ), array( '_aeon_icon' => $s['icon'] ) );
			}
			break;
		case 'aeon_why':
			foreach ( aeon_why_items() as $s ) {
				aeon_make_term( $taxonomy, aeon_ar( $s['title'] ), aeon_ar( $s['desc'] ), array( '_aeon_icon' => $s['icon'] ) );
			}
			break;
		case 'aeon_statistic':
			$stats = array(
				array( 'projects', '200', '+' ),
				array( 'clients', '150', '+' ),
				array( 'growth', '300', '%' ),
				array( 'satisfaction', '98', '%' ),
			);
			foreach ( $stats as $s ) {
				aeon_make_term( $taxonomy, aeon_ar( 'stat_' . $s[0] ), '', array( '_aeon_number' => $s[1], '_aeon_suffix' => $s[2] ) );
			}
			break;
		case 'aeon_review':
			$reviews = array(
				array( 'سارة المنصوري', 'مديرة تسويق، نوفا', 'فريق AEON نقل علامتنا التجارية إلى مستوى آخر. احترافية ونتائج فاقت توقعاتنا.' ),
				array( 'خالد العامري', 'مؤسس، أويسيس جروب', 'أفضل قرار اتخذناه هو الشراكة مع AEON. نمو حقيقي في المبيعات خلال أشهر.' ),
				array( 'ليلى حسن', 'الرئيس التنفيذي، بيكسل لاب', 'إبداع لا حدود له وفريق يفهم احتياجاتنا تماماً. ننصح بهم بشدة.' ),
			);
			foreach ( $reviews as $r ) {
				aeon_make_term( $taxonomy, $r[0], $r[2], array( '_aeon_role' => $r[1] ) );
			}
			break;
	}
}
