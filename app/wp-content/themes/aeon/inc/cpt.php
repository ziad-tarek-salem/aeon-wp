<?php
/**
 * Editable content loader.
 *
 * The dashboard-managed home sections (Services, Why Us, Statistics, Customer
 * Reviews) are flat taxonomies edited on the compact term screen; Our Works is
 * the portfolio post type. All are grouped under the Arabic "محتوى الموقع" menu.
 * Each concern is a self-contained module in inc/content/.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once AEON_DIR . '/inc/content/config.php';
require_once AEON_DIR . '/inc/content/helpers.php';
require_once AEON_DIR . '/inc/content/menu.php';
require_once AEON_DIR . '/inc/content/host.php';
require_once AEON_DIR . '/inc/content/taxonomies.php';
require_once AEON_DIR . '/inc/content/fields.php';
require_once AEON_DIR . '/inc/content/works.php';
require_once AEON_DIR . '/inc/content/settings.php';
require_once AEON_DIR . '/inc/content/migrate.php';

/**
 * Flush rewrite rules on theme activation so the public CPT slugs resolve.
 */
function aeon_rewrite_flush() {
	aeon_register_section_host();
	aeon_register_section_taxonomies();
	aeon_register_portfolio_cpt();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'aeon_rewrite_flush' );
