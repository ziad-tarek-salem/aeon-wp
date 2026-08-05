<?php
/**
 * AJAX contact form handler (nonce-protected).
 *
 * Records inquiries as private `aeon_lead` posts. Delivery itself happens in
 * the visitor's own mail client via a mailto: handoff on the front end, so
 * this file no longer sends anything and the site needs no SMTP transport.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aeon_handle_contact() {
	check_ajax_referer( 'aeon_contact', 'nonce' );

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$service = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	// Honeypot — bots fill this hidden field.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => aeon_t( 'form_success' ) ) ); // pretend success.
	}

	if ( empty( $name ) || empty( $message ) ) {
		wp_send_json_error( array( 'message' => aeon_t( 'form_required' ) ), 400 );
	}

	$body  = "Name: {$name}\n";
	$body .= "Service: {$service}\n\n";
	$body .= "Message:\n{$message}\n";

	/**
	 * Delivery is the visitor's own mail client — see initContactForm() in
	 * app.js, which hands the composed message to whatever app the device
	 * registers for mailto:. The server sends nothing, so no SMTP transport is
	 * needed; it only keeps the record so a lead survives the visitor's mail
	 * app failing to open, or opening and never being sent.
	 *
	 * Success is therefore tied to storage. Reporting failure on a mail
	 * transport that is no longer used would show an error for a lead that
	 * saved perfectly well.
	 */
	$stored = wp_insert_post( array(
		'post_type'    => 'aeon_lead',
		'post_status'  => 'private',
		'post_title'   => $service ? $name . ' — ' . $service : $name,
		'post_content' => $body,
	) );

	if ( $stored && ! is_wp_error( $stored ) ) {
		wp_send_json_success( array( 'message' => aeon_t( 'form_success' ) ) );
	}
	wp_send_json_error( array( 'message' => aeon_t( 'form_error' ) ), 500 );
}
add_action( 'wp_ajax_aeon_contact', 'aeon_handle_contact' );
add_action( 'wp_ajax_nopriv_aeon_contact', 'aeon_handle_contact' );

/**
 * Private CPT to retain submitted leads in wp-admin.
 */
function aeon_register_leads() {
	register_post_type( 'aeon_lead', array(
		'labels'   => array(
			'name'          => __( 'Leads', 'aeon' ),
			'singular_name' => __( 'Lead', 'aeon' ),
		),
		'public'   => false,
		'show_ui'  => true,
		'menu_icon'=> 'dashicons-email-alt',
		'supports' => array( 'title', 'editor' ),
		'capability_type' => 'post',
	) );
}
add_action( 'init', 'aeon_register_leads' );
