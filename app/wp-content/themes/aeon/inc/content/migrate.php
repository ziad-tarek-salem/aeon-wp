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
				array( 'projects', '500', '+' ),
				array( 'clients', '200', '+' ),
				array( 'commitment', '100', '%' ),
				array( 'satisfaction', '95', '%' ),
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
		case 'aeon_value':
			foreach ( aeon_value_items() as $v ) {
				aeon_make_term( $taxonomy, aeon_ar( $v['title'] ), aeon_ar( $v['desc'] ), array( '_aeon_icon' => $v['icon'] ) );
			}
			break;
		case 'aeon_event':
			foreach ( aeon_event_items() as $key ) {
				aeon_make_term( $taxonomy, aeon_ar( $key ), '', array() );
			}
			break;
		case 'aeon_expertise':
			foreach ( aeon_expertise_defaults() as $d ) {
				aeon_make_term( $taxonomy, aeon_ar( $d[0] ), '', array( '_aeon_icon' => $d[1] ) );
			}
			break;
		case 'aeon_capability':
			foreach ( aeon_capability_defaults() as $d ) {
				aeon_make_term( $taxonomy, aeon_ar( $d[0] ), '', array( '_aeon_icon' => $d[1] ) );
			}
			break;
		case 'aeon_industry':
			foreach ( aeon_default_industries() as $ind ) {
				aeon_make_term( $taxonomy, $ind['ar'], '', array( '_aeon_icon' => $ind['icon'] ) );
			}
			break;
	}
}

/** Expertise bullets as [i18n key, icon key], in profile order. */
function aeon_expertise_defaults() {
	return array(
		array( 'about_b1', 'target' ),
		array( 'about_b2', 'building' ),
		array( 'about_b3', 'team' ),
		array( 'about_b4', 'growth' ),
	);
}

/** Capability strip as [i18n key, icon key], in profile order. */
function aeon_capability_defaults() {
	return array(
		array( 'cap_1', 'analytics' ),
		array( 'cap_2', 'seo' ),
		array( 'cap_3', 'megaphone' ),
		array( 'cap_4', 'webdev' ),
		array( 'cap_5', 'report' ),
	);
}

/** Default icon for each stat, keyed by its Arabic label. */
function aeon_stat_icon_defaults() {
	return array(
		aeon_ar( 'stat_years' )        => 'trophy',
		aeon_ar( 'stat_clients' )      => 'team',
		aeon_ar( 'stat_projects' )     => 'briefcase',
		aeon_ar( 'stat_satisfaction' ) => 'growth',
		aeon_ar( 'stat_commitment' )   => 'target',
	);
}

/**
 * Seed the sections added after the original migration (values, events).
 *
 * The main migration is guarded by AEON_MIGRATED_FLAG and returns early on
 * installs that already ran it, so newly-introduced section taxonomies need
 * their own one-time pass. Idempotent: a per-run flag plus an emptiness check
 * means a client who later deletes all terms is not re-seeded.
 */
const AEON_SEED_NEW_FLAG = 'aeon_sections_seed_v3';

function aeon_maybe_seed_new_sections() {
	if ( get_option( AEON_SEED_NEW_FLAG ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	foreach ( array( 'aeon_value', 'aeon_event' ) as $tax ) {
		if ( aeon_tax_is_empty( $tax ) ) {
			aeon_seed_terms( $tax );
		}
	}
	update_option( AEON_SEED_NEW_FLAG, time() );
}
add_action( 'admin_init', 'aeon_maybe_seed_new_sections' );

/**
 * Seed the About-section taxonomies introduced with the profile redesign, and
 * backfill icons onto the stats/branches that predate the icon fields.
 *
 * Only fills gaps: a term that already carries an icon is never overwritten, so
 * a client's own choices survive re-runs.
 */
const AEON_SEED_ABOUT_FLAG = 'aeon_about_icons_seed_v1';

function aeon_maybe_seed_about_section() {
	if ( get_option( AEON_SEED_ABOUT_FLAG ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	foreach ( array( 'aeon_expertise', 'aeon_capability' ) as $tax ) {
		if ( aeon_tax_is_empty( $tax ) ) {
			aeon_seed_terms( $tax );
		}
	}

	// Stats predate the icon field — give each one the profile's icon.
	$stat_icons = aeon_stat_icon_defaults();
	$seen       = array();
	foreach ( aeon_section_terms( 'aeon_statistic' ) as $term ) {
		$seen[] = $term->name;
		if ( get_term_meta( $term->term_id, '_aeon_icon', true ) ) {
			continue;
		}
		$icon = isset( $stat_icons[ $term->name ] ) ? $stat_icons[ $term->name ] : 'chart';
		update_term_meta( $term->term_id, '_aeon_icon', $icon );
	}

	// The "years of experience" figure used to live on the image badge; the
	// redesign drops that badge, so keep the number as a proper stat.
	$years = aeon_ar( 'stat_years' );
	if ( ! aeon_tax_is_empty( 'aeon_statistic' ) && ! in_array( $years, $seen, true ) ) {
		aeon_make_term( 'aeon_statistic', $years, '', array(
			'_aeon_number' => '10',
			'_aeon_suffix' => '+',
			'_aeon_icon'   => 'trophy',
		) );
	}

	foreach ( aeon_section_terms( 'aeon_branch' ) as $term ) {
		if ( ! get_term_meta( $term->term_id, '_aeon_icon', true ) ) {
			update_term_meta( $term->term_id, '_aeon_icon', 'pin' );
		}
	}

	update_option( AEON_SEED_ABOUT_FLAG, time() );
}
add_action( 'admin_init', 'aeon_maybe_seed_about_section' );

/**
 * Seed the sectors taken from the profile's social-media pages, and refresh the
 * branch list to the names the client supplied in July 2026 (the PDF prints an
 * older set).
 *
 * Branch names are only replaced while the terms still carry the superseded PDF
 * labels, so any edit the client has already made is left alone.
 */
const AEON_SEED_PROFILE_FLAG = 'aeon_profile_seed_v4';

function aeon_maybe_seed_profile_sections() {
	if ( get_option( AEON_SEED_PROFILE_FLAG ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( aeon_tax_is_empty( 'aeon_industry' ) ) {
		aeon_seed_terms( 'aeon_industry' );
	}

	// The PDF labels the branches loosely ("أبو ظبي – العين 1"), so match on the
	// distinguishing fragment rather than the exact string.
	foreach ( aeon_section_terms( 'aeon_branch' ) as $term ) {
		$old = trim( $term->name );
		$new = '';

		if ( false !== strpos( $old, 'عجمان' ) ) {
			$new = 'فرع عجمان الراشدية';
		} elseif ( false !== strpos( $old, 'سكندري' ) ) {
			$new = 'فرع مصر الإسكندرية';
		} elseif ( false !== strpos( $old, 'العين' ) ) {
			// "1"/"١" is the city-centre branch, "2"/"٢" is Oud Al Touba.
			if ( preg_match( '/[1١]\s*$/u', $old ) ) {
				$new = 'فرع العين وسط المدينة';
			} elseif ( preg_match( '/[2٢]\s*$/u', $old ) ) {
				$new = 'فرع العين عود التوبة';
			}
		}

		if ( '' === $new || $new === $old || get_term_by( 'name', $new, 'aeon_branch' ) ) {
			continue;
		}
		wp_update_term( $term->term_id, 'aeon_branch', array( 'name' => $new ) );
	}

	// Fresh installs with no branches at all get the supplied list.
	if ( aeon_tax_is_empty( 'aeon_branch' ) ) {
		foreach ( aeon_default_branches() as $branch ) {
			aeon_make_term( 'aeon_branch', $branch['name'], '', array( '_aeon_icon' => 'pin' ) );
		}
	}

	update_option( AEON_SEED_PROFILE_FLAG, time() );
}
add_action( 'admin_init', 'aeon_maybe_seed_profile_sections' );
