<?php
/**
 * Work archive — the company profile's five portfolio chapters at full
 * fidelity: websites (p10), photography (p11–16), reels (p17–19), motion
 * (p20–21) and social-media design grouped by industry chip (p22–37).
 *
 * Projects the client adds from the dashboard are listed first; the profile
 * chapters below are always available as the baseline showcase.
 *
 * @package AEON
 */
get_header();

get_template_part( 'template-parts/components/page-banner', null, array(
	'title'    => aeon_t( 'work_page_title' ),
	'subtitle' => aeon_t( 'work_page_sub' ),
) );

$aeon_gallery = aeon_profile_gallery();

/**
 * Gallery items for one profile chapter.
 *
 * @param array  $gallery Result of aeon_profile_gallery().
 * @param string $slug    Chapter slug.
 * @return string[]
 */
$aeon_chapter_items = static function ( $gallery, $slug ) {
	return isset( $gallery[ $slug ]['items'] ) ? $gallery[ $slug ]['items'] : array();
};

// Dashboard-managed projects, when the client has added any.
if ( have_posts() ) {
	get_template_part( 'template-parts/work/chapter-projects' );
}

get_template_part( 'template-parts/work/chapter-web', null, array(
	'items' => $aeon_chapter_items( $aeon_gallery, 'web' ),
) );

get_template_part( 'template-parts/work/chapter-photography', null, array(
	'items' => array_slice( $aeon_chapter_items( $aeon_gallery, 'photography' ), 0, 12 ),
) );

get_template_part( 'template-parts/work/chapter-vertical', null, array(
	'slug'    => 'reels',
	'title_1' => 'reels_title_1',
	'title_2' => 'reels_title_2',
	'sub'     => 'reels_sub',
	'items'   => $aeon_chapter_items( $aeon_gallery, 'reels' ),
) );

get_template_part( 'template-parts/work/chapter-vertical', null, array(
	'slug'    => 'motion',
	'title_1' => 'motion_title_1',
	'title_2' => 'motion_title_2',
	'sub'     => 'motion_sub',
	'items'   => $aeon_chapter_items( $aeon_gallery, 'motion' ),
) );

get_template_part( 'template-parts/work/chapter-social' );

get_footer();
