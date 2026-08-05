<?php
/**
 * Groups the section editors under one Arabic admin menu: "النافذة المنبثقة".
 *
 * The four card sections are taxonomies (edit-tags screens); Our Works is the
 * portfolio post type. All are added here as submenus in display order, and the
 * parent stays highlighted while editing any of them.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AEON_CONTENT_MENU', 'aeon-content' );

/**
 * Every section editor this menu can carry. slug => label.
 *
 * Kept in full even though none are shown: the taxonomies, the terms saved in
 * them and the templates that render them are all untouched, so the site still
 * draws its services, counters, branches and Services-page copy from exactly
 * the content that is already there. Only the editors are out of the way.
 *
 * Putting one back is a single line in a child theme or functions.php — no code
 * here has to be restored:
 *
 *     add_filter( 'aeon_content_sections', function ( $shown, $all ) {
 *         return array( 'edit-tags.php?taxonomy=aeon_service' => $all['edit-tags.php?taxonomy=aeon_service'] );
 *     }, 10, 2 );
 *
 * @return array<string,string>
 */
function aeon_content_all_submenus() {
	return array(
		'edit-tags.php?taxonomy=aeon_service'    => 'الخدمات',
		'edit-tags.php?taxonomy=aeon_why'        => 'لماذا نحن',
		'edit-tags.php?taxonomy=aeon_statistic'  => 'الإحصائيات',
		'edit.php?post_type=portfolio'           => 'أعمالنا',
		'edit-tags.php?taxonomy=aeon_review'     => 'آراء العملاء',
		'edit-tags.php?taxonomy=aeon_branch'     => 'الفروع والمواقع',
	);
}

/**
 * Section editors actually shown — none, by request: this menu now carries the
 * welcome popup and nothing else.
 *
 * @return array<string,string>
 */
function aeon_content_submenus() {
	return (array) apply_filters( 'aeon_content_sections', array(), aeon_content_all_submenus() );
}

/**
 * Submenus other files hang off this menu, removed so only the popup is left.
 *
 * Removed from here rather than by deleting each add_submenu_page() call, so
 * every one of those screens stays whole and working — reachable by URL, and
 * back in the menu the moment its slug comes off this list.
 *
 * @return array<int,string>
 */
function aeon_content_hidden_pages() {
	return (array) apply_filters( 'aeon_content_hidden_pages', array(
		'aeon-images',      // inc/content/images.php — صور الموقع
		'aeon-whatsapp',    // inc/content/settings.php — زر الواتساب
		'aeon-about-image', // inc/content/settings.php — صورة «نبذة عن خبراتنا»
	) );
}

/**
 * Register the parent menu and whatever sections are shown.
 *
 * The parent opens the popup editor itself. With no section editors left there
 * is nothing for a landing page to list, and a menu whose only child repeats
 * its parent is one click of pure ceremony — so the screen someone came for is
 * the screen they land on. WordPress prints no submenu list for a top-level
 * item that has none, which is what keeps the sidebar to a single row.
 */
function aeon_content_menu() {
	$sections = aeon_content_submenus();
	$callback = $sections ? 'aeon_content_menu_page' : 'aeon_popup_settings_page';
	$cap      = $sections ? 'edit_posts' : 'manage_options';

	add_menu_page( 'النافذة المنبثقة', 'النافذة المنبثقة', $cap, AEON_CONTENT_MENU, $callback, 'dashicons-layout', 24 );

	if ( ! $sections ) {
		return;
	}

	// Relabel the auto-created first submenu (otherwise it duplicates the parent).
	add_submenu_page( AEON_CONTENT_MENU, 'محتوى الموقع', 'نظرة عامة', 'edit_posts', AEON_CONTENT_MENU, 'aeon_content_menu_page' );

	foreach ( $sections as $slug => $label ) {
		$section_cap = ( 0 === strpos( $slug, 'edit-tags.php' ) ) ? 'manage_categories' : 'edit_posts';
		add_submenu_page( AEON_CONTENT_MENU, $label, $label, $section_cap, $slug );
	}
}
add_action( 'admin_menu', 'aeon_content_menu' );

/**
 * Drop the submenus other files registered.
 *
 * Late on purpose: those files hook admin_menu at 10–12, so this has to run
 * after all of them to have anything to remove.
 */
function aeon_content_trim_menu() {
	foreach ( aeon_content_hidden_pages() as $slug ) {
		remove_submenu_page( AEON_CONTENT_MENU, $slug );
	}

	// Registering any child makes WordPress auto-add a submenu row mirroring the
	// parent. With every child now gone that row is the last thing standing, and
	// it only repeats the item above it — so the flyout goes and the menu is one
	// row. The parent keeps its own callback either way.
	if ( ! aeon_content_submenus() ) {
		remove_submenu_page( AEON_CONTENT_MENU, AEON_CONTENT_MENU );
	}
}
add_action( 'admin_menu', 'aeon_content_trim_menu', 99 );

/** Landing screen for the parent menu. */
function aeon_content_menu_page() {
	echo '<div class="wrap" dir="rtl" style="text-align:right">';
	echo '<h1>محتوى الموقع</h1>';
	echo '<p>من هنا تتحكّم في محتوى أقسام الصفحة الرئيسية. اختر القسم الذي تريد تعديله:</p>';
	echo '<ul style="font-size:14px;line-height:2;list-style:disc;padding-inline-start:20px">';
	foreach ( aeon_content_submenus() as $slug => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( admin_url( $slug ) ), esc_html( $label ) );
	}
	printf( '<li><a href="%s">%s</a></li>', esc_url( admin_url( 'admin.php?page=aeon-whatsapp' ) ), 'زر الواتساب' );
	echo '</ul>';
	echo '<p style="color:#646970">في كل قسم: الاسم في الأعلى هو العنوان، والوصف هو النص، ثم الحقول الإضافية بالأسفل. أضف عنصراً جديداً من النموذج على اليمين، أو عدّل/احذف عنصراً من القائمة.</p>';
	echo '</div>';
}

/** Keep "محتوى الموقع" highlighted while editing any of its sections. */
function aeon_content_parent_highlight( $parent_file ) {
	global $current_screen, $submenu_file;
	if ( ! $current_screen ) {
		return $parent_file;
	}
	if ( ! empty( $current_screen->taxonomy ) && in_array( $current_screen->taxonomy, aeon_section_taxonomies(), true ) ) {
		$submenu_file = 'edit-tags.php?taxonomy=' . $current_screen->taxonomy;
		return AEON_CONTENT_MENU;
	}
	if ( ! empty( $current_screen->post_type ) && 'portfolio' === $current_screen->post_type ) {
		$submenu_file = 'edit.php?post_type=portfolio';
		return AEON_CONTENT_MENU;
	}
	return $parent_file;
}
add_filter( 'parent_file', 'aeon_content_parent_highlight' );
