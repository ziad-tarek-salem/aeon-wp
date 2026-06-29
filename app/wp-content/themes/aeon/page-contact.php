<?php
/**
 * Template Name: Contact Page
 *
 * @package AEON
 */
get_header();
get_template_part( 'template-parts/components/page-banner', null, array(
	'title'    => aeon_t( 'nav_contact' ),
	'subtitle' => aeon_t( 'contact_sub' ),
) );

get_template_part( 'template-parts/sections/contact' );

$map = aeon_opt( 'aeon_map' );
if ( $map ) :
	?>
	<section class="contact-map" data-reveal>
		<iframe src="<?php echo esc_url( $map ); ?>" width="100%" height="420" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map"></iframe>
	</section>
	<?php
endif;

get_footer();
