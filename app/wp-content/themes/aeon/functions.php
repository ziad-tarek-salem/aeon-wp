<?php
/**
 * AEON Digital Marketing — theme bootstrap.
 *
 * @package AEON
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AEON_VERSION', '1.1.0' );
define( 'AEON_DIR', get_template_directory() );
define( 'AEON_URI', get_template_directory_uri() );

require_once AEON_DIR . '/inc/theme-setup.php';
require_once AEON_DIR . '/inc/i18n.php';
require_once AEON_DIR . '/inc/enqueue.php';
require_once AEON_DIR . '/inc/cpt.php';
require_once AEON_DIR . '/inc/customizer.php';
require_once AEON_DIR . '/inc/template-functions.php';
require_once AEON_DIR . '/inc/ajax-contact.php';
