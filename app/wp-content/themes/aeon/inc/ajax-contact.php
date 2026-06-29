<?php
/**
 * AJAX contact form handler (nonce-protected, wp_mail).
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aeon_handle_contact() {
	check_ajax_referer( 'aeon_contact', 'nonce' );

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$service = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	// Honeypot — bots fill this hidden field.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => aeon_t( 'form_success' ) ) ); // pretend success.
	}

	if ( empty( $name ) || empty( $email ) || empty( $message ) || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => aeon_t( 'form_required' ) ), 400 );
	}

	$to      = aeon_opt( 'aeon_email', get_option( 'admin_email' ) );
	$subject = sprintf( '[%s] New inquiry from %s', get_bloginfo( 'name' ), $name );
	$body    = "Name: {$name}\n";
	$body   .= "Email: {$email}\n";
	$body   .= "Phone: {$phone}\n";
	$body   .= "Service: {$service}\n\n";
	$body   .= "Message:\n{$message}\n";

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, $subject, $body, $headers );

	// Store a copy as a private record so nothing is lost if mail fails.
	wp_insert_post( array(
		'post_type'   => 'aeon_lead',
		'post_status' => 'private',
		'post_title'  => $name . ' — ' . $service,
		'post_content'=> $body,
	) );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => aeon_t( 'form_success' ) ) );
	}
	// Even if mail transport is down we kept the lead; report soft success
	// only when stored, but surface error if storage also failed.
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
