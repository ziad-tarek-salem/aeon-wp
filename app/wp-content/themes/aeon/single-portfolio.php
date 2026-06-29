<?php
/**
 * Single portfolio / case study.
 *
 * @package AEON
 */
get_header();

while ( have_posts() ) :
	the_post();
	$terms = get_the_terms( get_the_ID(), 'work_category' );
	get_template_part( 'template-parts/components/page-banner', null, array(
		'title'    => get_the_title(),
		'subtitle' => ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '',
	) );
	?>
	<article class="single-work section">
		<div class="container narrow">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="single-work__hero" data-reveal>
					<?php the_post_thumbnail( 'aeon-wide' ); ?>
				</figure>
			<?php endif; ?>
			<div class="entry-content" data-reveal>
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
