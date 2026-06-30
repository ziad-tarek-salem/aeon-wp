<?php
/**
 * Hidden host post type for the section taxonomies.
 *
 * Flat taxonomies need an object type to attach to. This UI-less type exists
 * only to host them; users never see it — the taxonomies are surfaced under the
 * "محتوى الموقع" menu via inc/content/menu.php.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aeon_register_section_host() {
	register_post_type( 'aeon_section', array(
		'public'              => false,
		'show_ui'             => false,
		'show_in_menu'        => false,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'rewrite'             => false,
		'can_export'          => false,
		'supports'            => false,
	) );
}
add_action( 'init', 'aeon_register_section_host', 9 ); // before the taxonomies.
