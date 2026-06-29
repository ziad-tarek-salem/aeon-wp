<?php
/**
 * Theme setup: supports, menus, image sizes.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function aeon_setup() {
	load_theme_textdomain( 'aeon', AEON_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 120,
		'width'       => 360,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'aeon' ),
		'footer'  => __( 'Footer Menu', 'aeon' ),
	) );

	// Portfolio / case-study thumbnails.
	add_image_size( 'aeon-card', 720, 540, true );
	add_image_size( 'aeon-wide', 1280, 720, true );
}
add_action( 'after_setup_theme', 'aeon_setup' );

function aeon_content_width() {
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'aeon_content_width', 0 );

/**
 * Register widget area for the footer.
 */
function aeon_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Footer Widgets', 'aeon' ),
		'id'            => 'footer-widgets',
		'description'   => __( 'Appears in the site footer.', 'aeon' ),
		'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="footer-widget__title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'aeon_widgets_init' );
