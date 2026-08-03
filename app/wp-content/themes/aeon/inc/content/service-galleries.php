<?php
/**
 * Services page galleries — the "أعمالنا في هذه الخدمة" strip under each service
 * block, managed from the dashboard (صفحة الخدمات → اسم الخدمة).
 *
 * One screen per service. Each holds an ordered set of Media Library
 * attachments plus the two knobs that shape the strip: the tile aspect ratio and
 * the column count. Nothing here is required — a service with no images keeps
 * rendering the shots bundled with the theme, so the live site is unchanged
 * until someone opts in, one service at a time.
 *
 * Storage is a single option keyed by the *service slug* (see
 * aeon_service_slug_map()), not the term ID, so renaming a service in
 * محتوى الموقع → الخدمات renames its gallery screen without stranding its images.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AEON_SVC_GALLERY_OPTION = 'aeon_service_galleries';
const AEON_SVC_GALLERY_MENU   = 'aeon-services-page';
const AEON_SVC_GALLERY_PREFIX = 'aeon-svc-gallery-';
const AEON_SVC_GALLERY_GROUP  = 'aeon_service_galleries_group';

/* -------------------------------------------------------------------------
 * Vocabulary
 * ---------------------------------------------------------------------- */

/**
 * Selectable tile aspect ratios: CSS value => Arabic label.
 *
 * The values are written exactly as the `--svc-shot-ratio` custom property
 * expects them, so a stored choice drops straight into the template.
 *
 * @return array<string,string>
 */
function aeon_service_gallery_ratios() {
	return array(
		'1 / 1'   => 'مربّع (1:1)',
		'4 / 5'   => 'طولي (4:5)',
		'2 / 3'   => 'طولي ممتد (2:3)',
		'9 / 16'  => 'ستوري / ريلز (9:16)',
		'3 / 2'   => 'عرضي (3:2)',
		'16 / 10' => 'عرضي واسع (16:10)',
		'16 / 9'  => 'عريض (16:9)',
	);
}

/**
 * Selectable column counts for the strip.
 *
 * @return int[]
 */
function aeon_service_gallery_columns() {
	return array( 2, 3, 4, 5 );
}

/**
 * Services whose strip holds video rather than stills: slug => 'video'.
 *
 * Video editing is the one service whose work *is* motion — a still frame of a
 * reel shows the least interesting thing about it — so its gallery takes
 * uploaded reels and plays them in place. Filterable, so switching another
 * service over needs no edit here.
 *
 * @return array<string,string>
 */
function aeon_service_gallery_kinds() {
	return (array) apply_filters( 'aeon_service_gallery_kinds', array( 'video' => 'video' ) );
}

/**
 * Which medium a service's strip holds.
 *
 * @param string $slug Service slug.
 * @return string 'video' or 'image'.
 */
function aeon_service_gallery_kind( $slug ) {
	$kinds = aeon_service_gallery_kinds();
	return ( isset( $kinds[ $slug ] ) && 'video' === $kinds[ $slug ] ) ? 'video' : 'image';
}

/**
 * Is this attachment the right medium for the given gallery kind?
 *
 * @param int    $id   Attachment ID.
 * @param string $kind 'video' or 'image'.
 * @return bool
 */
function aeon_service_gallery_accepts( $id, $kind ) {
	if ( 'attachment' !== get_post_type( $id ) ) {
		return false;
	}
	return ( 'video' === $kind ) ? wp_attachment_is( 'video', $id ) : wp_attachment_is_image( $id );
}

/**
 * The services that get a gallery screen: slug => label.
 *
 * Labels follow the live service names so renaming a service in the dashboard
 * renames its screen. Services listed in the dashboard come first, in page
 * order; any of the eight known services whose name no longer maps to a slug is
 * appended under its default name so its images stay reachable.
 *
 * @return array<string,string>
 */
function aeon_service_gallery_targets() {
	$out = array();

	foreach ( aeon_services_list() as $svc ) {
		if ( ! empty( $svc['slug'] ) && ! isset( $out[ $svc['slug'] ] ) ) {
			$out[ $svc['slug'] ] = $svc['name'];
		}
	}

	foreach ( array_flip( aeon_service_slug_map() ) as $slug => $name ) {
		if ( ! isset( $out[ $slug ] ) ) {
			$out[ $slug ] = $name;
		}
	}

	return $out;
}

/** Admin page slug for one service's gallery screen. */
function aeon_service_gallery_page_slug( $slug ) {
	return AEON_SVC_GALLERY_PREFIX . $slug;
}

/**
 * Edit link for the term behind a service, so a gallery screen can point at the
 * screen that owns the same service's words.
 *
 * @param string $slug Service slug.
 * @return string Empty when no live term maps to this slug.
 */
function aeon_service_gallery_term_link( $slug ) {
	foreach ( aeon_section_terms( 'aeon_service' ) as $term ) {
		if ( aeon_service_slug( $term->name ) === $slug ) {
			return (string) get_edit_term_link( $term->term_id, 'aeon_service' );
		}
	}

	return '';
}

/**
 * The service whose screen is being viewed, or '' when it is not one of ours.
 *
 * @return string
 */
function aeon_service_gallery_current_slug() {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
	if ( 0 !== strpos( $page, AEON_SVC_GALLERY_PREFIX ) ) {
		return '';
	}
	$slug    = substr( $page, strlen( AEON_SVC_GALLERY_PREFIX ) );
	$targets = aeon_service_gallery_targets();

	return isset( $targets[ $slug ] ) ? $slug : '';
}

/* -------------------------------------------------------------------------
 * Storage
 * ---------------------------------------------------------------------- */

/**
 * The stored map of service slug => { ids, ratio, cols }.
 *
 * @return array
 */
function aeon_service_gallery_option() {
	$stored = get_option( AEON_SVC_GALLERY_OPTION, array() );
	return is_array( $stored ) ? $stored : array();
}

/**
 * Resolved settings for one service: the picked images plus the strip shape.
 *
 * Every service's real shape is written by the content sync when it seeds the
 * galleries, so the values below only apply to a service added afterwards.
 *
 * @param string $slug Service slug.
 * @return array{ids:int[],ratio:string,cols:int}
 */
function aeon_service_gallery_settings( $slug ) {
	$ratio = '4 / 5';
	$cols  = 4;

	$stored = aeon_service_gallery_option();
	$row    = ( isset( $stored[ $slug ] ) && is_array( $stored[ $slug ] ) ) ? $stored[ $slug ] : array();

	// Filtered by medium as well as validity, so a service switched from stills
	// to video never tries to render its old images (and vice versa).
	$kind = aeon_service_gallery_kind( $slug );
	$ids  = array();
	if ( isset( $row['ids'] ) ) {
		foreach ( (array) $row['ids'] as $value ) {
			$id = is_numeric( $value ) ? (int) $value : 0;
			if ( $id > 0 && aeon_service_gallery_accepts( $id, $kind ) ) {
				$ids[] = $id;
			}
		}
	}

	$choices = aeon_service_gallery_ratios();
	if ( ! empty( $row['ratio'] ) && isset( $choices[ $row['ratio'] ] ) ) {
		$ratio = $row['ratio'];
	}
	if ( ! empty( $row['cols'] ) && in_array( (int) $row['cols'], aeon_service_gallery_columns(), true ) ) {
		$cols = (int) $row['cols'];
	}

	return array( 'ids' => $ids, 'ratio' => $ratio, 'cols' => $cols );
}

/** Register the option. */
function aeon_service_gallery_register_setting() {
	register_setting( AEON_SVC_GALLERY_GROUP, AEON_SVC_GALLERY_OPTION, array(
		'type'              => 'array',
		'sanitize_callback' => 'aeon_service_gallery_sanitize',
		'default'           => array(),
	) );
}
add_action( 'admin_init', 'aeon_service_gallery_register_setting' );

/**
 * options.php gates every settings group on `manage_options` unless told
 * otherwise, which would let an Editor open these screens but not save them.
 * Picking images is content work, so the group follows the same capability as
 * the menu itself.
 *
 * @return string
 */
function aeon_service_gallery_option_capability() {
	return 'upload_files';
}
add_filter( 'option_page_capability_' . AEON_SVC_GALLERY_GROUP, 'aeon_service_gallery_option_capability' );

/**
 * Normalise one service's row: attachment IDs in order, plus a known ratio and
 * column count. An empty ratio or a zero column count means "use the default
 * baked into the theme", so the row survives a change to those defaults.
 *
 * @param mixed  $row  Raw submitted row.
 * @param string $kind Gallery medium, 'image' or 'video'.
 * @return array{ids:int[],ratio:string,cols:int}
 */
function aeon_service_gallery_sanitize_row( $row, $kind = 'image' ) {
	$row = is_array( $row ) ? $row : array();

	$raw = isset( $row['ids'] ) ? $row['ids'] : array();
	$raw = is_array( $raw ) ? $raw : explode( ',', (string) $raw );

	// (int) rather than absint(): absint('-5') is 5, which would quietly turn a
	// malformed value into a real attachment. Anything that is not a positive
	// integer pointing at an attachment of the right medium is dropped.
	$ids = array();
	foreach ( $raw as $value ) {
		$id = is_numeric( $value ) ? (int) $value : 0;
		if ( $id > 0 && ! in_array( $id, $ids, true ) && aeon_service_gallery_accepts( $id, $kind ) ) {
			$ids[] = $id;
		}
	}

	$choices = aeon_service_gallery_ratios();
	$ratio   = ( isset( $row['ratio'] ) && isset( $choices[ $row['ratio'] ] ) ) ? $row['ratio'] : '';

	$cols = isset( $row['cols'] ) ? absint( $row['cols'] ) : 0;
	if ( ! in_array( $cols, aeon_service_gallery_columns(), true ) ) {
		$cols = 0;
	}

	return array( 'ids' => $ids, 'ratio' => $ratio, 'cols' => $cols );
}

/**
 * All eight services share one option, but each screen posts only its own row.
 * The hidden `_editing` field names that row, and everything else is carried
 * over untouched — so saving one service can never blank the other seven.
 *
 * Without `_editing` the value is treated as a complete map, which keeps a
 * programmatic update_option() honest.
 *
 * @param mixed $value Raw submitted value.
 * @return array
 */
function aeon_service_gallery_sanitize( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$targets = aeon_service_gallery_targets();
	$editing = isset( $value['_editing'] ) ? sanitize_key( $value['_editing'] ) : '';

	if ( $editing && isset( $targets[ $editing ] ) ) {
		$out             = aeon_service_gallery_option();
		$out[ $editing ] = aeon_service_gallery_sanitize_row(
			isset( $value[ $editing ] ) ? $value[ $editing ] : array(),
			aeon_service_gallery_kind( $editing )
		);
		return $out;
	}

	$out = array();
	foreach ( array_keys( $targets ) as $slug ) {
		if ( isset( $value[ $slug ] ) ) {
			$out[ $slug ] = aeon_service_gallery_sanitize_row( $value[ $slug ], aeon_service_gallery_kind( $slug ) );
		}
	}

	return $out;
}

/* -------------------------------------------------------------------------
 * Menu
 * ---------------------------------------------------------------------- */

/**
 * Register "صفحة الخدمات": the page-text screen, then one real submenu page per
 * service gallery.
 *
 * Position 24.5 seats it directly under "محتوى الموقع" (24) and above Comments
 * (25); core stores the position as a string key, so the fraction survives.
 *
 * Real pages rather than query-string variants of one page, so WordPress' own
 * menu highlighting, capability checks and per-screen hooks all work unaided.
 */
function aeon_service_gallery_menu() {
	add_menu_page(
		'صفحة الخدمات',
		'صفحة الخدمات',
		'upload_files',
		AEON_SVC_GALLERY_MENU,
		'aeon_services_text_page',
		'dashicons-format-gallery',
		24.5
	);

	// Relabel the auto-created first submenu (otherwise it duplicates the parent).
	add_submenu_page( AEON_SVC_GALLERY_MENU, 'نصوص صفحة الخدمات', 'نصوص الصفحة', 'upload_files', AEON_SVC_GALLERY_MENU, 'aeon_services_text_page' );

	foreach ( aeon_service_gallery_targets() as $slug => $label ) {
		add_submenu_page(
			AEON_SVC_GALLERY_MENU,
			'صور خدمة ' . $label,
			$label,
			'upload_files',
			aeon_service_gallery_page_slug( $slug ),
			'aeon_service_gallery_screen'
		);
	}
}
add_action( 'admin_menu', 'aeon_service_gallery_menu' );

/* -------------------------------------------------------------------------
 * Screens
 * ---------------------------------------------------------------------- */

/** One service's gallery manager. */
function aeon_service_gallery_screen() {
	$slug = aeon_service_gallery_current_slug();
	if ( ! $slug ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to access this page.' ), 403 );
	}

	$targets = aeon_service_gallery_targets();
	$label   = $targets[ $slug ];
	$set     = aeon_service_gallery_settings( $slug );
	$name    = AEON_SVC_GALLERY_OPTION;
	$anchor  = trailingslashit( home_url( '/services/' ) ) . '#service-' . $slug;
	$kind    = aeon_service_gallery_kind( $slug );
	$video   = ( 'video' === $kind );
	$noun    = $video ? 'الفيديوهات' : 'الصور';

	echo '<div class="wrap aeon-svc" dir="rtl" style="text-align:right">';
	printf( '<h1>%s خدمة: %s</h1>', $video ? 'فيديوهات' : 'صور', esc_html( $label ) );
	printf(
		'<p class="aeon-svc__lead">%s الظاهرة في شريط الأعمال أسفل هذه الخدمة في صفحة الخدمات. ترتيب %s هنا هو ترتيبها في الموقع. <a href="%s" target="_blank" rel="noopener">عرض القسم في الموقع ↗</a></p>',
		$video ? 'هذه هي الفيديوهات' : 'هذه هي الصور',
		esc_html( $noun ),
		esc_url( $anchor )
	);

	if ( $video ) {
		echo '<p class="description aeon-svc__note">هذه الخدمة تعرض فيديوهات تُشغَّل داخل الصفحة بدلاً من الصور. ';
		echo 'ارفع مقاطع رأسية (ريلز) بصيغة MP4 أو MOV — صيغة MP4 هي الأوسع توافقاً مع المتصفحات. ';
		echo 'لضبط الصورة الظاهرة قبل التشغيل، افتح الفيديو في مكتبة الوسائط وعيّن له «صورة بارزة».</p>';
	}

	$term_link = aeon_service_gallery_term_link( $slug );
	if ( $term_link ) {
		printf(
			'<p class="description aeon-svc__note">نصوص هذه الخدمة (الاسم، الوصف، النص التعريفي، المميزات، وما تشمله الخدمة) تُعدَّل من <a href="%s">صفحة تعديل الخدمة ←</a></p>',
			esc_url( $term_link )
		);
	}

	echo '<form method="post" action="options.php">';
	settings_fields( AEON_SVC_GALLERY_GROUP );
	printf( '<input type="hidden" name="%s[_editing]" value="%s">', esc_attr( $name ), esc_attr( $slug ) );

	printf( '<div class="aeon-svc-gallery" data-slug="%s" data-kind="%s">', esc_attr( $slug ), esc_attr( $kind ) );
	printf(
		'<input type="hidden" class="aeon-svc-ids" name="%s[%s][ids]" value="%s">',
		esc_attr( $name ),
		esc_attr( $slug ),
		esc_attr( implode( ',', $set['ids'] ) )
	);

	echo '<p class="aeon-svc-toolbar">';
	printf(
		'<button type="button" class="button button-primary aeon-svc-add"><span class="dashicons dashicons-plus-alt2"></span> %s</button> ',
		$video ? 'إضافة فيديوهات' : 'إضافة صور'
	);
	printf(
		'<button type="button" class="button aeon-svc-clear"%s>%s</button>',
		$set['ids'] ? '' : ' style="display:none"',
		$video ? 'إزالة كل الفيديوهات' : 'إزالة كل الصور'
	);
	echo '</p>';

	echo '<ul class="aeon-svc-grid' . ( $set['ids'] ? '' : ' is-empty' ) . '">';
	foreach ( $set['ids'] as $i => $id ) {
		echo aeon_service_gallery_tile( $id, $i + 1, $kind ); // phpcs:ignore WordPress.Security.EscapeOutput -- built escaped.
	}
	echo '</ul>';

	// Empty state, rendered either way and toggled by JS so emptying the grid
	// brings the explanation straight back.
	echo '<div class="aeon-svc-empty"' . ( $set['ids'] ? ' hidden' : '' ) . '>';
	if ( $video ) {
		echo '<p><strong>لا توجد فيديوهات لهذه الخدمة.</strong> لن يظهر شريط الأعمال أسفلها في الموقع حتى يُضاف فيديو واحد على الأقل.</p>';
		echo '<p class="description">اضغط «إضافة فيديوهات» لرفع مقاطع جديدة أو اختيارها من مكتبة الوسائط.</p>';
	} else {
		echo '<p><strong>لا توجد صور لهذه الخدمة.</strong> لن يظهر شريط الأعمال أسفلها في الموقع حتى تُضاف صورة واحدة على الأقل.</p>';
		echo '<p class="description">اضغط «إضافة صور» لاختيار الصور من مكتبة الوسائط أو رفع صور جديدة.</p>';
	}
	echo '</div>';

	printf(
		'<p class="description aeon-svc-hint">اسحب أي عنصر لتغيير ترتيبه، أو استخدم زرّي التقديم والتأخير. «حذف» يزيل %s من هذه الخدمة فقط ولا يحذفه من مكتبة الوسائط.</p>',
		$video ? 'الفيديو' : 'الصورة'
	);
	echo '</div>';

	echo '<h2>شكل الشريط</h2>';
	echo '<table class="form-table" role="presentation"><tbody>';

	echo '<tr><th scope="row"><label for="aeon-svc-ratio">نسبة العرض إلى الارتفاع</label></th><td>';
	printf( '<select id="aeon-svc-ratio" name="%s[%s][ratio]">', esc_attr( $name ), esc_attr( $slug ) );
	foreach ( aeon_service_gallery_ratios() as $value => $text ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $value ), selected( $set['ratio'], $value, false ), esc_html( $text ) );
	}
	echo '</select>';
	if ( $video ) {
		echo '<p class="description">إطار كل فيديو يأخذ هذه النسبة. للريلز الرأسية اختر 9:16 حتى يظهر الفيديو كاملاً بلا أشرطة سوداء.</p>';
	} else {
		echo '<p class="description">كل الصور تُقصّ على هذه النسبة ليبقى الشريط منتظماً. اختر النسبة الأقرب لصورك حتى لا تُقتطع أجزاء مهمة.</p>';
	}
	echo '</td></tr>';

	echo '<tr><th scope="row"><label for="aeon-svc-cols">عدد الأعمدة</label></th><td>';
	printf( '<select id="aeon-svc-cols" name="%s[%s][cols]">', esc_attr( $name ), esc_attr( $slug ) );
	foreach ( aeon_service_gallery_columns() as $value ) {
		printf( '<option value="%d"%s>%d</option>', $value, selected( $set['cols'], $value, false ), $value );
	}
	echo '</select>';
	echo '<p class="description">عدد الصور في الصف الواحد على شاشات الكمبيوتر. تتحول الشاشات الصغيرة إلى عدد أقل تلقائياً.</p>';
	echo '</td></tr>';

	echo '</tbody></table>';

	submit_button( 'حفظ التغييرات' );
	echo '</form></div>';
}

/**
 * One tile, escaped and ready to echo.
 *
 * The same shape is rebuilt in JS after a pick, so keep the two in step.
 *
 * @param int    $id       Attachment ID.
 * @param int    $position 1-based position badge.
 * @param string $kind     Gallery medium, 'image' or 'video'.
 * @return string
 */
function aeon_service_gallery_tile( $id, $position, $kind = 'image' ) {
	$id    = (int) $id;
	$video = ( 'video' === $kind );

	// A video's tile art is its featured image when one is set; otherwise the
	// tile falls back to a film icon over the file name, which is still enough
	// to tell the reels apart while reordering.
	$thumb = $video ? (string) get_the_post_thumbnail_url( $id, 'thumbnail' ) : (string) wp_get_attachment_image_url( $id, 'thumbnail' );

	// An item that cannot be previewed still gets a tile. Returning nothing here
	// used to hide it from the grid while leaving it in the saved list, and the
	// grid is what the next save is built from — so the id vanished the moment
	// anything else on the screen was touched. A visible broken tile is the only
	// version of this the client can act on.
	$broken = ( ! $video && ! $thumb ) || 'attachment' !== get_post_type( $id );

	$alt  = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
	$edit = get_edit_post_link( $id );

	$out  = '<li class="aeon-svc-tile' . ( $video ? ' aeon-svc-tile--video' : '' ) . ( $broken ? ' aeon-svc-tile--broken' : '' ) . '" data-id="' . esc_attr( $id ) . '">';
	$out .= '<span class="aeon-svc-tile__num">' . esc_html( number_format_i18n( $position ) ) . '</span>';

	if ( $thumb ) {
		$out .= '<img src="' . esc_url( $thumb ) . '" alt="">';
	} else {
		$icon = $broken ? 'dashicons-warning' : 'dashicons-format-video';
		$out .= '<span class="aeon-svc-tile__ph"><span class="dashicons ' . esc_attr( $icon ) . '"></span></span>';
	}

	$noun = $video ? 'الفيديو' : 'الصورة';

	$out .= '<span class="aeon-svc-tile__actions">';
	$out .= '<button type="button" class="button aeon-svc-earlier" aria-label="تقديم ' . esc_attr( $noun ) . '"><span class="dashicons dashicons-arrow-up-alt2"></span></button>';
	$out .= '<button type="button" class="button aeon-svc-later" aria-label="تأخير ' . esc_attr( $noun ) . '"><span class="dashicons dashicons-arrow-down-alt2"></span></button>';
	$out .= '<button type="button" class="button aeon-svc-replace" aria-label="استبدال ' . esc_attr( $noun ) . '"><span class="dashicons dashicons-image-rotate-left"></span></button>';
	$out .= '<button type="button" class="button aeon-svc-remove" aria-label="حذف ' . esc_attr( $noun ) . ' من هذه الخدمة"><span class="dashicons dashicons-no-alt"></span></button>';
	$out .= '</span>';

	// A video's caption is its library title — that is what the front end labels
	// the player with, and unlike alt text every attachment already has one.
	if ( $broken ) {
		$out .= '<span class="aeon-svc-tile__alt aeon-svc-tile__alt--missing">ملف مفقود — احذفه</span>';
	} elseif ( $video ) {
		$title = (string) get_the_title( $id );
		$out  .= '<span class="aeon-svc-tile__alt" title="' . esc_attr( $title ) . '">' . esc_html( $title ) . '</span>';
	} elseif ( '' !== $alt ) {
		$out .= '<span class="aeon-svc-tile__alt" title="' . esc_attr( $alt ) . '">' . esc_html( $alt ) . '</span>';
	} elseif ( $edit ) {
		$out .= '<a class="aeon-svc-tile__alt aeon-svc-tile__alt--missing" href="' . esc_url( $edit ) . '" target="_blank" rel="noopener">أضف نصاً بديلاً</a>';
	} else {
		$out .= '<span class="aeon-svc-tile__alt aeon-svc-tile__alt--missing">بدون نص بديل</span>';
	}

	$out .= '</li>';

	return $out;
}

/* -------------------------------------------------------------------------
 * Assets
 * ---------------------------------------------------------------------- */

/**
 * Media frame, sortable and the screen's own CSS/JS — on these screens only.
 *
 * @param string $hook Current admin page hook.
 */
function aeon_service_gallery_assets( $hook ) {
	$is_service  = false !== strpos( (string) $hook, AEON_SVC_GALLERY_PREFIX );
	$is_overview = false !== strpos( (string) $hook, AEON_SVC_GALLERY_MENU );

	if ( ! $is_service && ! $is_overview ) {
		return;
	}

	wp_enqueue_style( 'aeon-service-galleries', AEON_URI . '/assets/admin/service-galleries.css', array( 'dashicons' ), AEON_VERSION );

	if ( ! $is_service ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'aeon-service-galleries',
		AEON_URI . '/assets/admin/service-galleries.js',
		array( 'jquery', 'jquery-ui-sortable' ),
		AEON_VERSION,
		true
	);
	wp_localize_script( 'aeon-service-galleries', 'aeonSvcGallery', array(
		'editBase' => admin_url( 'post.php?action=edit&post=' ),
		'image'    => array(
			'addTitle'     => 'اختيار صور الخدمة',
			'addButton'    => 'إضافة إلى الخدمة',
			'swapTitle'    => 'اختيار صورة بديلة',
			'swapButton'   => 'استبدال الصورة',
			'confirmClear' => 'إزالة كل الصور من هذه الخدمة؟ لن تُحذف من مكتبة الوسائط.',
			'earlier'      => 'تقديم الصورة',
			'later'        => 'تأخير الصورة',
			'replace'      => 'استبدال الصورة',
			'remove'       => 'حذف الصورة من هذه الخدمة',
			'missingAlt'   => 'أضف نصاً بديلاً',
		),
		'video'    => array(
			'addTitle'     => 'اختيار فيديوهات الخدمة',
			'addButton'    => 'إضافة إلى الخدمة',
			'swapTitle'    => 'اختيار فيديو بديل',
			'swapButton'   => 'استبدال الفيديو',
			'confirmClear' => 'إزالة كل الفيديوهات من هذه الخدمة؟ لن تُحذف من مكتبة الوسائط.',
			'earlier'      => 'تقديم الفيديو',
			'later'        => 'تأخير الفيديو',
			'replace'      => 'استبدال الفيديو',
			'remove'       => 'حذف الفيديو من هذه الخدمة',
			'missingAlt'   => 'بدون عنوان',
		),
	) );
}
add_action( 'admin_enqueue_scripts', 'aeon_service_gallery_assets' );
