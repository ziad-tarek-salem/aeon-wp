<?php
/**
 * Front page — composed of section parts.
 *
 * @package AEON
 */
get_header();

get_template_part( 'template-parts/sections/hero' );
get_template_part( 'template-parts/sections/partners' );
get_template_part( 'template-parts/sections/about' );
get_template_part( 'template-parts/sections/services' );
get_template_part( 'template-parts/sections/why' );
get_template_part( 'template-parts/sections/stats' );
get_template_part( 'template-parts/sections/portfolio' );
get_template_part( 'template-parts/sections/testimonials' );
get_template_part( 'template-parts/sections/contact' );

get_footer();
