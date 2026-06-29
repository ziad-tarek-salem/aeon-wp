<?php
/**
 * Theme Customizer: stats, contact details, social links.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aeon_customize_register( $wp_customize ) {

	// ---- Contact panel ----
	$wp_customize->add_section( 'aeon_contact', array(
		'title'    => __( 'AEON · Contact Info', 'aeon' ),
		'priority' => 30,
	) );

	$fields = array(
		'aeon_email'    => array( __( 'Email', 'aeon' ), 'hello@aeondm.com' ),
		'aeon_phone'    => array( __( 'Phone', 'aeon' ), '+971 50 000 0000' ),
		'aeon_whatsapp' => array( __( 'WhatsApp (digits only, intl)', 'aeon' ), '971500000000' ),
		'aeon_address'  => array( __( 'Address', 'aeon' ), 'Dubai, United Arab Emirates' ),
		'aeon_map'      => array( __( 'Google Maps embed URL', 'aeon' ), '' ),
	);
	foreach ( $fields as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'aeon_contact', 'type' => 'text' ) );
	}

	// ---- Social panel ----
	$wp_customize->add_section( 'aeon_social', array(
		'title'    => __( 'AEON · Social Links', 'aeon' ),
		'priority' => 31,
	) );
	$socials = array(
		'aeon_instagram' => 'Instagram',
		'aeon_facebook'  => 'Facebook',
		'aeon_linkedin'  => 'LinkedIn',
		'aeon_x'         => 'X (Twitter)',
		'aeon_tiktok'    => 'TikTok',
		'aeon_youtube'   => 'YouTube',
	);
	foreach ( $socials as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( $id, array( 'label' => $label, 'section' => 'aeon_social', 'type' => 'url' ) );
	}

	// ---- Stats panel ----
	$wp_customize->add_section( 'aeon_stats', array(
		'title'    => __( 'AEON · Stats', 'aeon' ),
		'priority' => 32,
	) );
	$stats = array(
		'aeon_stat_projects'     => array( __( 'Projects number', 'aeon' ), '200' ),
		'aeon_stat_clients'      => array( __( 'Clients number', 'aeon' ), '150' ),
		'aeon_stat_growth'       => array( __( 'Growth %', 'aeon' ), '300' ),
		'aeon_stat_satisfaction' => array( __( 'Satisfaction %', 'aeon' ), '98' ),
		'aeon_stat_years'        => array( __( 'Years', 'aeon' ), '5' ),
	);
	foreach ( $stats as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'aeon_stats', 'type' => 'number' ) );
	}
}
add_action( 'customize_register', 'aeon_customize_register' );

/** Helper: get a Customizer value with fallback to its registered default. */
function aeon_opt( $key, $default = '' ) {
	return get_theme_mod( $key, $default );
}
