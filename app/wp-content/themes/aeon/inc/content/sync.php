<?php
/**
 * Content parity sync — brings a site's dashboard-managed content up to the
 * revision the theme was built against.
 *
 * Only the theme folder is deployed (see .github/workflows/deploy.yml): the
 * database never travels, so a section the client's server seeded months ago
 * keeps its old copy while the theme moves on. That is how production ended up
 * showing four different service blurbs, a stale set of statistics and a single
 * "فرع دبي" branch long after the profile redesign landed.
 *
 * This module closes that gap. AEON_CONTENT_REVISION names one authoritative
 * snapshot of every section taxonomy plus the handful of settings that drive the
 * front end; on the first request after a deploy the site reconciles itself to
 * that snapshot and records the revision, so it runs exactly once per revision.
 *
 * A taxonomy is only touched when it actually differs, which makes the pass a
 * no-op on the site the snapshot was taken from. When it does differ the terms
 * are rebuilt from scratch rather than renamed in place — WordPress rejects two
 * terms sharing a name, so a positional rename can deadlock against itself.
 *
 * TO PUBLISH NEW CONTENT: edit aeon_content_snapshot() (or the settings below)
 * and bump AEON_CONTENT_REVISION. Anything left out of the snapshot — posts,
 * pages, portfolio items, media — is never touched.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Bump this whenever the snapshot below changes; that is what re-runs the sync. */
const AEON_CONTENT_REVISION = '2026-07-28-profile-parity';

/** Where the applied revision is recorded. */
const AEON_CONTENT_REV_OPTION = 'aeon_content_revision';

/**
 * Where the logo step records itself.
 *
 * Kept apart from the revision above because publishing the logo is the one step
 * that depends on the host (a read-only uploads folder fails it). Tracking it
 * separately lets that step retry on its own without replaying the term rebuild.
 */
const AEON_CONTENT_LOGO_OPTION = 'aeon_content_logo_revision';

/** Short-lived marker so two concurrent requests cannot both run the sync. */
const AEON_CONTENT_LOCK_OPTION = 'aeon_content_sync_lock';

/** Seconds after which an abandoned lock (fatal mid-sync) may be taken over. */
const AEON_CONTENT_LOCK_TTL = 300;

/**
 * The authoritative content, keyed by taxonomy, in display order.
 *
 * Term order is term-id order (see aeon_section_terms()), so the array order
 * here is the order the front end renders.
 *
 * @return array<string,array<int,array{name:string,desc:string,meta:array<string,string>}>>
 */
function aeon_content_snapshot() {
	return array(

		'aeon_service' => array(
			array(
				'name' => 'التصوير الاحترافي',
				'desc' => 'صور احترافية تعكس هوية علامتك بأفضل شكل.',
				'meta' => array( '_aeon_icon' => 'camera' ),
			),
			array(
				'name' => 'التصميم الجرافيكي',
				'desc' => 'تصاميم إبداعية تبني هوية بصرية تترك انطباعاً دائماً.',
				'meta' => array( '_aeon_icon' => 'pen' ),
			),
			array(
				'name' => 'المونتاج وصناعة الفيديو',
				'desc' => 'مونتاج احترافي يحوّل أفكارك إلى قصص مؤثرة.',
				'meta' => array( '_aeon_icon' => 'video' ),
			),
			array(
				'name' => 'التسويق الرقمي',
				'desc' => 'استراتيجيات ذكية تحقق وصولاً أكبر وتحوّلات أعلى.',
				'meta' => array( '_aeon_icon' => 'megaphone' ),
			),
			array(
				'name' => 'إدارة السوشيال ميديا',
				'desc' => 'إدارة احترافية لحساباتك وبناء تفاعل حقيقي مع جمهورك.',
				'meta' => array( '_aeon_icon' => 'growth' ),
			),
			array(
				'name' => 'بناء الهوية التجارية',
				'desc' => 'خلق هوية قوية ومميزة تجعل علامتك تبرز بين المنافسين.',
				'meta' => array( '_aeon_icon' => 'target' ),
			),
			array(
				'name' => 'تصميم وتطوير المواقع',
				'desc' => 'مواقع حديثة وسريعة الاستجابة تحوّل الزوار إلى عملاء.',
				'meta' => array( '_aeon_icon' => 'globe' ),
			),
			array(
				'name' => 'تحليل الأداء والتقارير',
				'desc' => 'تحليل دقيق وتقارير تفصيلية لتحسين مستمر وتحقيق أفضل النتائج.',
				'meta' => array( '_aeon_icon' => 'shield' ),
			),
		),

		'aeon_expertise' => array(
			array(
				'name' => 'نمتلك خبرة بالتسويق الإلكتروني وإدارة الحسابات الإعلانية وإعلانات جوجل وتحسين محركات البحث (SEO) تتجاوز عشر سنوات بمصر والإمارات والكويت والمملكة العربية السعودية.',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'target' ),
			),
			array(
				'name' => 'عملنا مع شركات كبرى وشركات قابضة وأسواق تجارية ومولات وجهات حكومية.',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'building' ),
			),
			array(
				'name' => 'لدينا فريق ذو خبرة وكفاءة عالية وندير حسابات إعلانية على جميع المنصات.',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'team' ),
			),
			array(
				'name' => 'خبرة في تهيئة المواقع الإلكترونية وتحسين محركات البحث ووضع إستراتيجيات تسويق وخطة مبيعات فعّالة حقّقت نتائج سريعة الانتشار والظهور.',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'growth' ),
			),
		),

		'aeon_capability' => array(
			array(
				'name' => 'تحليل دقيق لمنافسيك',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'analytics' ),
			),
			array(
				'name' => 'استراتيجيات تسويق ذكية ومخصّصة',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'seo' ),
			),
			array(
				'name' => 'حملات إعلانية فعّالة على جميع المنصات',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'megaphone' ),
			),
			array(
				'name' => 'تطوير المواقع وتحسين محركات البحث',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'webdev' ),
			),
			array(
				'name' => 'تقارير دورية لقياس الأداء والتطوّر',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'report' ),
			),
		),

		'aeon_why' => array(
			array(
				'name' => 'خبرة تتجاوز 10 سنوات',
				'desc' => 'عشر سنوات في التسويق الرقمي وإدارة الإعلانات وتحسين محرّكات البحث عبر الإمارات ومصر والكويت والسعودية.',
				'meta' => array( '_aeon_icon' => 'shield' ),
			),
			array(
				'name' => 'استهداف دقيق واستراتيجية مخصّصة',
				'desc' => 'نحلّل السوق والمنافسين ونبني استراتيجية تسويق ذكية مخصّصة لعلامتك.',
				'meta' => array( '_aeon_icon' => 'target' ),
			),
			array(
				'name' => 'حملات فعّالة على كل المنصات',
				'desc' => 'إدارة حملات إعلانية احترافية على جميع المنصات بأعلى نسب وصول وتحويل.',
				'meta' => array( '_aeon_icon' => 'megaphone' ),
			),
			array(
				'name' => 'نتائج قابلة للقياس',
				'desc' => 'تقارير دورية دقيقة لقياس الأداء والتطوّر وتحقيق نمو مستمر لعلامتك.',
				'meta' => array( '_aeon_icon' => 'chart' ),
			),
			array(
				'name' => 'فريق خبير وشراكة حقيقية',
				'desc' => 'فريق من الاستراتيجيين والمبدعين والمطوّرين يعتبرك شريك نجاح ويعمل معك كفريق واحد.',
				'meta' => array( '_aeon_icon' => 'team' ),
			),
		),

		'aeon_value' => array(
			array(
				'name' => 'النتائج',
				'desc' => 'نركّز على تحقيق أهداف قابلة للقياس ونضمن عائداً حقيقياً لاستثمارك.',
				'meta' => array( '_aeon_icon' => 'target' ),
			),
			array(
				'name' => 'الابتكار',
				'desc' => 'نبتكر حلولاً إبداعية تواكب التطوّرات وتضعك دائماً في المقدّمة.',
				'meta' => array( '_aeon_icon' => 'bulb' ),
			),
			array(
				'name' => 'الشراكة',
				'desc' => 'نعتبر عملاءنا شركاء نجاح ونعمل معهم كفريق واحد لبناء مستقبل أفضل.',
				'meta' => array( '_aeon_icon' => 'team' ),
			),
		),

		'aeon_event' => array(
			array(
				'name' => 'مشاركات في المؤتمرات والفعاليات المتخصّصة',
				'desc' => '',
				'meta' => array(),
			),
			array(
				'name' => 'ورش عمل ودورات تدريبية لروّاد الأعمال وأصحاب المشاريع',
				'desc' => '',
				'meta' => array(),
			),
			array(
				'name' => 'تقديم نصائح عملية لتطوير الأعمال وزيادة المبيعات',
				'desc' => '',
				'meta' => array(),
			),
			array(
				'name' => 'حلقات نقاش وجلسات حوارية حول أحدث استراتيجيات التسويق',
				'desc' => '',
				'meta' => array(),
			),
			array(
				'name' => 'بناء علاقات استراتيجية وشراكات تحقق نتائج مستدامة',
				'desc' => '',
				'meta' => array(),
			),
		),

		'aeon_industry' => array(
			array(
				'name' => 'سياحة',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'globe' ),
			),
			array(
				'name' => 'إدارة منشآت',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'briefcase' ),
			),
			array(
				'name' => 'قهوة',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'coffee' ),
			),
			array(
				'name' => 'مطاعم',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'food' ),
			),
			array(
				'name' => 'حلويات',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'sparkle' ),
			),
			array(
				'name' => 'عصائر',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'store' ),
			),
			array(
				'name' => 'لحوم',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'food' ),
			),
			array(
				'name' => 'عطور',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'sparkle' ),
			),
			array(
				'name' => 'عقارات',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'building' ),
			),
			array(
				'name' => 'توصيل',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'truck' ),
			),
			array(
				'name' => 'خدمات تنظيف',
				'desc' => '',
				'meta' => array( '_aeon_icon' => 'shield' ),
			),
		),

		'aeon_statistic' => array(
			array(
				'name' => 'مشروع مكتمل بنجاح',
				'desc' => '',
				'meta' => array( '_aeon_number' => '500', '_aeon_suffix' => '+', '_aeon_icon' => 'briefcase' ),
			),
			array(
				'name' => 'عميل سعيد',
				'desc' => '',
				'meta' => array( '_aeon_number' => '200', '_aeon_suffix' => '+', '_aeon_icon' => 'team' ),
			),
			array(
				'name' => 'التزام بتحقيق الأهداف',
				'desc' => '',
				'meta' => array( '_aeon_number' => '100', '_aeon_suffix' => '%', '_aeon_icon' => 'target' ),
			),
			array(
				'name' => 'رضا العملاء',
				'desc' => '',
				'meta' => array( '_aeon_number' => '95', '_aeon_suffix' => '%', '_aeon_icon' => 'growth' ),
			),
			array(
				'name' => 'سنوات من الخبرة',
				'desc' => '',
				'meta' => array( '_aeon_number' => '10', '_aeon_suffix' => '+', '_aeon_icon' => 'trophy' ),
			),
		),

		'aeon_review' => array(
			array(
				'name' => 'سارة المنصوري',
				'desc' => 'فريق AEON نقل علامتنا التجارية إلى مستوى آخر. احترافية ونتائج فاقت توقعاتنا.',
				'meta' => array( '_aeon_role' => 'مديرة تسويق، نوفا', '_aeon_image' => '0' ),
			),
			array(
				'name' => 'خالد العامري',
				'desc' => 'أفضل قرار اتخذناه هو الشراكة مع AEON. نمو حقيقي في المبيعات خلال أشهر.',
				'meta' => array( '_aeon_role' => 'مؤسس، أويسيس جروب', '_aeon_image' => '0' ),
			),
			array(
				'name' => 'ليلى حسن',
				'desc' => 'إبداع لا حدود له وفريق يفهم احتياجاتنا تماماً. ننصح بهم بشدة.',
				'meta' => array( '_aeon_role' => 'الرئيس التنفيذي، بيكسل لاب', '_aeon_image' => '0' ),
			),
		),

		'aeon_branch' => array(
			array(
				'name' => 'فرع عجمان الراشدية',
				'desc' => 'عجمان، الإمارات العربية المتحدة',
				'meta' => array( '_aeon_lat' => '25.405216', '_aeon_lng' => '55.513643', '_aeon_icon' => 'pin' ),
			),
			array(
				'name' => 'فرع العين وسط المدينة',
				'desc' => 'مدينة العين، أبو ظبي، الإمارات العربية المتحدة',
				'meta' => array( '_aeon_lat' => '24.207500', '_aeon_lng' => '55.744700', '_aeon_icon' => 'pin' ),
			),
			array(
				'name' => 'فرع العين عود التوبة',
				'desc' => 'مدينة العين، أبو ظبي، الإمارات العربية المتحدة',
				'meta' => array( '_aeon_lat' => '24.211700', '_aeon_lng' => '55.768300', '_aeon_icon' => 'pin' ),
			),
			array(
				'name' => 'فرع مصر الإسكندرية',
				'desc' => 'الإسكندرية، جمهورية مصر العربية',
				'meta' => array( '_aeon_lat' => '31.200100', '_aeon_lng' => '29.918700', '_aeon_icon' => 'pin' ),
			),
		),

	);
}

/**
 * Settings that drive the front end, as option key => value.
 *
 * @return array<string,string>
 */
function aeon_content_settings() {
	return array(
		AEON_WA_OPTION => '971561098015',
	);
}

/**
 * The header logo, as the file the theme ships and the media-library path it is
 * published at.
 *
 * The bundled JPEG predates the transparent artwork and sits on an opaque white
 * rectangle, which shows against the header. Publishing the PNG under the same
 * uploads folder the reference site uses keeps the rendered `src` identical too.
 *
 * @return array{asset:string,subdir:string,filename:string,title:string}
 */
function aeon_content_logo() {
	return array(
		'asset'    => 'logo-wordmark.png',
		'subdir'   => '2026/06',
		'filename' => 'logo-wordmark.png',
		'title'    => 'AEON Logo',
	);
}

/* -------------------------------------------------------------------------
 * Runner
 * ---------------------------------------------------------------------- */

/**
 * Reconcile this site to AEON_CONTENT_REVISION, once.
 *
 * Runs on `init` at 20 — after the section taxonomies register at 10 — rather
 * than on `admin_init`, so a deploy takes effect on the first front-end hit
 * instead of waiting for somebody to open wp-admin.
 */
function aeon_maybe_sync_content() {
	$data_done = ( AEON_CONTENT_REVISION === get_option( AEON_CONTENT_REV_OPTION ) );
	$logo_done = ( AEON_CONTENT_REVISION === get_option( AEON_CONTENT_LOGO_OPTION ) );

	if ( $data_done && $logo_done ) {
		return;
	}
	if ( ! aeon_content_claim_lock() ) {
		return;
	}

	try {
		if ( ! $data_done ) {
			aeon_content_sync_taxonomies();
			aeon_content_sync_settings();
			update_option( AEON_CONTENT_REV_OPTION, AEON_CONTENT_REVISION );
			$data_done = true;
		}
		if ( ! $logo_done && aeon_content_sync_logo() ) {
			update_option( AEON_CONTENT_LOGO_OPTION, AEON_CONTENT_REVISION );
			$logo_done = true;
		}
	} catch ( \Throwable $e ) {
		// Never take the site down over a data fix: leave the revision unset so
		// a later request retries, and leave a trail in the error log.
		error_log( 'AEON content sync failed: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	if ( $data_done && $logo_done ) {
		delete_option( AEON_CONTENT_LOCK_OPTION );
		return;
	}

	// Something did not land. Holding the lock rate-limits the retry to once per
	// AEON_CONTENT_LOCK_TTL instead of re-attempting on every single request.
}
add_action( 'init', 'aeon_maybe_sync_content', 20 );

/**
 * Claim the sync lock.
 *
 * add_option() fails when the row already exists, which makes it a cheap mutex
 * against the burst of requests a freshly-deployed site gets. A lock older than
 * AEON_CONTENT_LOCK_TTL is assumed abandoned and taken over.
 *
 * @return bool True when this request owns the lock.
 */
function aeon_content_claim_lock() {
	if ( add_option( AEON_CONTENT_LOCK_OPTION, (string) time(), '', 'no' ) ) {
		return true;
	}
	$started = (int) get_option( AEON_CONTENT_LOCK_OPTION );
	if ( $started && ( time() - $started ) > AEON_CONTENT_LOCK_TTL ) {
		update_option( AEON_CONTENT_LOCK_OPTION, (string) time() );
		return true;
	}
	return false;
}

/**
 * Rebuild every section taxonomy that does not already match the snapshot.
 */
function aeon_content_sync_taxonomies() {
	foreach ( aeon_content_snapshot() as $taxonomy => $rows ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}
		if ( aeon_content_tax_matches( $taxonomy, $rows ) ) {
			continue;
		}
		aeon_content_rebuild_tax( $taxonomy, $rows );
	}
}

/**
 * Does a taxonomy already hold exactly the snapshot, in order?
 *
 * Every `_aeon_*` meta key on the term is compared, not just the ones the
 * snapshot names — otherwise a leftover icon upload or URL override would keep
 * winning at render time and pass unnoticed.
 *
 * @param string $taxonomy Taxonomy slug.
 * @param array  $rows     Snapshot rows.
 * @return bool
 */
function aeon_content_tax_matches( $taxonomy, $rows ) {
	$terms = aeon_section_terms( $taxonomy );
	if ( count( $terms ) !== count( $rows ) ) {
		return false;
	}

	foreach ( array_values( $terms ) as $i => $term ) {
		$row = $rows[ $i ];
		if ( $term->name !== $row['name'] || $term->description !== (string) $row['desc'] ) {
			return false;
		}

		$want = array_map( 'strval', (array) $row['meta'] );
		$have = array();
		foreach ( (array) get_term_meta( $term->term_id ) as $key => $values ) {
			if ( 0 === strpos( $key, '_aeon_' ) && '' !== (string) $values[0] ) {
				$have[ $key ] = (string) $values[0];
			}
		}
		// A declared '0' (e.g. "no review photo") is the same as no meta at all.
		$want = array_filter( $want, static function ( $v ) {
			return '' !== $v && '0' !== $v;
		} );
		$have = array_filter( $have, static function ( $v ) {
			return '0' !== $v;
		} );

		ksort( $want );
		ksort( $have );
		if ( $want !== $have ) {
			return false;
		}
	}

	return true;
}

/**
 * Delete a taxonomy's terms and re-create the snapshot in order.
 *
 * Insertion order is term-id order, which is the order the front end renders,
 * so this also fixes a site whose terms are merely out of sequence.
 *
 * @param string $taxonomy Taxonomy slug.
 * @param array  $rows     Snapshot rows.
 */
function aeon_content_rebuild_tax( $taxonomy, $rows ) {
	$existing = get_terms( array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => false,
		'fields'     => 'ids',
	) );
	if ( $existing && ! is_wp_error( $existing ) ) {
		foreach ( $existing as $term_id ) {
			wp_delete_term( (int) $term_id, $taxonomy );
		}
	}

	foreach ( $rows as $row ) {
		$res = wp_insert_term( $row['name'], $taxonomy, array( 'description' => (string) $row['desc'] ) );

		if ( is_wp_error( $res ) ) {
			// A name that survived deletion (shared slug, another taxonomy) still
			// reports its id, so reuse it rather than dropping the row.
			$term_id = (int) $res->get_error_data( 'term_exists' );
			if ( ! $term_id ) {
				continue;
			}
			wp_update_term( $term_id, $taxonomy, array(
				'name'        => $row['name'],
				'description' => (string) $row['desc'],
			) );
		} else {
			$term_id = (int) $res['term_id'];
		}

		foreach ( (array) get_term_meta( $term_id ) as $key => $values ) {
			if ( 0 === strpos( $key, '_aeon_' ) ) {
				delete_term_meta( $term_id, $key );
			}
		}
		foreach ( (array) $row['meta'] as $key => $value ) {
			if ( '' !== (string) $value ) {
				update_term_meta( $term_id, $key, (string) $value );
			}
		}
	}
}

/**
 * Apply the settings snapshot.
 */
function aeon_content_sync_settings() {
	foreach ( aeon_content_settings() as $key => $value ) {
		if ( (string) get_option( $key ) !== (string) $value ) {
			update_option( $key, $value );
		}
	}
}

/**
 * Publish the bundled header logo and select it as the site's custom logo.
 *
 * The upload is skipped once the media library already holds the file, so
 * re-running a deploy cannot litter uploads with logo-wordmark-1.png, -2.png and
 * so on.
 *
 * @return bool True when the custom logo now points at the bundled artwork.
 */
function aeon_content_sync_logo() {
	$logo   = aeon_content_logo();
	$source = AEON_DIR . '/assets/images/' . $logo['asset'];
	if ( ! file_exists( $source ) ) {
		return false;
	}

	$relative = $logo['subdir'] . '/' . $logo['filename'];
	$id       = aeon_content_attachment_by_file( $relative );

	if ( ! $id ) {
		$id = aeon_content_publish_logo( $source, $logo );
	}
	if ( ! $id ) {
		return false;
	}

	if ( (int) get_theme_mod( 'custom_logo' ) !== $id ) {
		set_theme_mod( 'custom_logo', $id );
	}

	return true;
}

/**
 * Attachment ID for an uploads-relative path, or 0.
 *
 * @param string $relative e.g. '2026/06/logo-wordmark.png'.
 * @return int
 */
function aeon_content_attachment_by_file( $relative ) {
	global $wpdb;

	$id = $wpdb->get_var( $wpdb->prepare(
		"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
		$relative
	) );

	return $id ? (int) $id : 0;
}

/**
 * Copy the bundled logo into the media library at a fixed uploads subfolder.
 *
 * The folder is pinned rather than left to default to the current month so the
 * rendered `src` matches the reference site byte for byte.
 *
 * @param string $source Absolute path to the theme asset.
 * @param array  $logo   aeon_content_logo() config.
 * @return int Attachment ID, or 0 on failure.
 */
function aeon_content_publish_logo( $source, $logo ) {
	$pin = static function ( $dirs ) use ( $logo ) {
		$dirs['subdir'] = '/' . $logo['subdir'];
		$dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
		$dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];
		return $dirs;
	};

	add_filter( 'upload_dir', $pin );
	$upload = wp_upload_bits( $logo['filename'], null, (string) file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	remove_filter( 'upload_dir', $pin );

	if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) ) {
		return 0;
	}

	$id = wp_insert_attachment( array(
		'post_mime_type' => 'image/png',
		'post_title'     => $logo['title'],
		'post_content'   => '',
		'post_status'    => 'inherit',
	), $upload['file'] );

	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}

	// Sizes and the width/height the <img> prints both come from this metadata.
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );

	return (int) $id;
}
