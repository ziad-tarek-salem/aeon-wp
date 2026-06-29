<?php
/**
 * Custom post types & taxonomies.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aeon_register_cpts() {

	// Portfolio / Work.
	register_post_type( 'portfolio', array(
		'labels'       => array(
			'name'          => __( 'Work', 'aeon' ),
			'singular_name' => __( 'Project', 'aeon' ),
			'add_new_item'  => __( 'Add New Project', 'aeon' ),
			'edit_item'     => __( 'Edit Project', 'aeon' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-portfolio',
		'rewrite'      => array( 'slug' => 'work' ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		'show_in_rest' => true,
	) );

	register_taxonomy( 'work_category', 'portfolio', array(
		'labels'       => array(
			'name'          => __( 'Work Categories', 'aeon' ),
			'singular_name' => __( 'Category', 'aeon' ),
		),
		'public'       => true,
		'hierarchical' => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'work-category' ),
	) );

	// Services.
	register_post_type( 'service', array(
		'labels'       => array(
			'name'          => __( 'Services', 'aeon' ),
			'singular_name' => __( 'Service', 'aeon' ),
			'add_new_item'  => __( 'Add New Service', 'aeon' ),
		),
		'public'       => true,
		'has_archive'  => false,
		'menu_icon'    => 'dashicons-screenoptions',
		'rewrite'      => array( 'slug' => 'services' ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		'show_in_rest' => true,
	) );

	// Testimonials.
	register_post_type( 'testimonial', array(
		'labels'       => array(
			'name'          => __( 'Testimonials', 'aeon' ),
			'singular_name' => __( 'Testimonial', 'aeon' ),
		),
		'public'       => false,
		'show_ui'      => true,
		'menu_icon'    => 'dashicons-format-quote',
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
	) );
}
add_action( 'init', 'aeon_register_cpts' );

/**
 * Flush rewrite rules on theme activation.
 */
function aeon_rewrite_flush() {
	aeon_register_cpts();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'aeon_rewrite_flush' );

/**
 * Simple meta box for testimonial author role/company.
 */
function aeon_testimonial_meta() {
	add_meta_box( 'aeon_tst_meta', __( 'Author Details', 'aeon' ), 'aeon_testimonial_meta_cb', 'testimonial', 'side' );
}
add_action( 'add_meta_boxes', 'aeon_testimonial_meta' );

function aeon_testimonial_meta_cb( $post ) {
	wp_nonce_field( 'aeon_tst', 'aeon_tst_nonce' );
	$role = get_post_meta( $post->ID, '_aeon_role', true );
	echo '<p><label>' . esc_html__( 'Role / Company', 'aeon' ) . '</label>';
	echo '<input type="text" name="aeon_role" value="' . esc_attr( $role ) . '" style="width:100%"></p>';
}

function aeon_save_testimonial_meta( $post_id ) {
	if ( ! isset( $_POST['aeon_tst_nonce'] ) || ! wp_verify_nonce( $_POST['aeon_tst_nonce'], 'aeon_tst' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( isset( $_POST['aeon_role'] ) ) {
		update_post_meta( $post_id, '_aeon_role', sanitize_text_field( $_POST['aeon_role'] ) );
	}
}
add_action( 'save_post_testimonial', 'aeon_save_testimonial_meta' );
