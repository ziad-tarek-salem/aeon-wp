<?php
/**
 * Groups the section editors under one Arabic admin menu: "محتوى الموقع".
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

/** Submenu items, in display order. slug => label. */
function aeon_content_submenus() {
	return array(
		'edit-tags.php?taxonomy=aeon_service'    => 'الخدمات',
		'edit-tags.php?taxonomy=aeon_why'        => 'لماذا نحن',
		'edit-tags.php?taxonomy=aeon_statistic'  => 'الإحصائيات',
		'edit.php?post_type=portfolio'           => 'أعمالنا',
		'edit-tags.php?taxonomy=aeon_review'     => 'آراء العملاء',
	);
}

/** Register the parent menu and its submenus. */
function aeon_content_menu() {
	add_menu_page( 'محتوى الموقع', 'محتوى الموقع', 'edit_posts', AEON_CONTENT_MENU, 'aeon_content_menu_page', 'dashicons-layout', 24 );

	// Relabel the auto-created first submenu (otherwise it duplicates the parent).
	add_submenu_page( AEON_CONTENT_MENU, 'محتوى الموقع', 'نظرة عامة', 'edit_posts', AEON_CONTENT_MENU, 'aeon_content_menu_page' );

	foreach ( aeon_content_submenus() as $slug => $label ) {
		$cap = ( 0 === strpos( $slug, 'edit-tags.php' ) ) ? 'manage_categories' : 'edit_posts';
		add_submenu_page( AEON_CONTENT_MENU, $label, $label, $cap, $slug );
	}
}
add_action( 'admin_menu', 'aeon_content_menu' );

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
