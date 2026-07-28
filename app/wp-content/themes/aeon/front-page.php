<?php
/**
 * Front page — composed of section parts.
 *
 * @package AEON
 */
get_header();

// Homepage flow mirrors the company profile: identity → services → expertise →
// stats → events → clients → office → contact. Message/values, the portfolio
// teaser, the ad-campaign band and the Instagram-ads proof are all hidden for
// now — each is one commented-out line below, and every section template is
// untouched, so bringing one back is a matter of deleting two slashes.
get_template_part( 'template-parts/sections/hero' );
// The partners marquee was removed at the client's request; the same client
// logos still appear in full on the Clients section further down.
get_template_part( 'template-parts/sections/services' );
// The counters now live inside the About section (profile p3), so the
// standalone stats band would repeat them here. It still runs on the About page.
get_template_part( 'template-parts/sections/about' );
// Message & Values ("رسالتنا") is hidden on the homepage for now at the client's
// request. The section template is untouched at
// template-parts/sections/values.php — uncomment the line below to bring it back.
// get_template_part( 'template-parts/sections/values' );
get_template_part( 'template-parts/sections/events' );
// Portfolio teaser ("أعمالنا") — hidden for now.
// get_template_part( 'template-parts/sections/showcase' );
get_template_part( 'template-parts/sections/clients' );
// Ad campaigns ("حملاتنا الإعلانية") and the Instagram-ads proof
// ("إعلانات انستجرام") — hidden for now.
// get_template_part( 'template-parts/sections/campaigns' );
// get_template_part( 'template-parts/sections/results' );
get_template_part( 'template-parts/sections/office' );
get_template_part( 'template-parts/sections/contact' );

get_footer();
