<?php
/**
 * Generic page template.
 *
 * @package AEON
 */
get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/components/page-banner', null, array( 'title' => get_the_title() ) );
	?>
	<article class="section">
		<div class="container narrow entry-content" data-reveal>
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
