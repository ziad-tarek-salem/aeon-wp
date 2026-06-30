<?php
/**
 * Register the section taxonomies from aeon_sections_config().
 *
 * All are flat, non-public, hidden from REST and attached to the aeon_section
 * host. They are surfaced under the "محتوى الموقع" menu (see menu.php) and
 * edited on the compact term screen (see fields.php).
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aeon_register_section_taxonomies() {
	foreach ( aeon_sections_config() as $taxonomy => $section ) {
		$l = $section['labels'];

		$labels = array(
			'name'          => $l['name'],
			'singular_name' => $l['singular_name'],
			'menu_name'     => $section['menu'],
			'all_items'     => 'كل العناصر',
			'add_new_item'  => $l['add_new_item'],
			'edit_item'     => $l['edit_item'],
			'update_item'   => $l['update_item'],
			'view_item'     => $l['edit_item'],
			'new_item_name' => $l['new_item_name'],
			'search_items'  => $l['search_items'],
			'not_found'     => $l['not_found'],
			'back_to_items' => $l['back_to_items'],
		);

		// WP 5.9+ shows these as the hint under each core field.
		$labels['name_field_description'] = $section['name_label'] . ' — ' . $section['name_hint'];
		if ( ! empty( $section['show_desc'] ) ) {
			$labels['desc_field_description'] = $section['desc_label'] . ' — ' . $section['desc_hint'];
		}

		register_taxonomy( $taxonomy, array( 'aeon_section' ), array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => false, // surfaced manually under "محتوى الموقع".
			'show_in_nav_menus'  => false,
			'show_in_rest'       => false,
			'show_admin_column'  => false,
			'show_tagcloud'      => false,
			'hierarchical'       => false,
			'meta_box_cb'        => false,
			'rewrite'            => false,
		) );
	}
}
add_action( 'init', 'aeon_register_section_taxonomies', 10 );
