<?php
/**
 * Single service detail.
 *
 * @package AEON
 */
get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/components/page-banner', null, array(
		'title'    => get_the_title(),
		'subtitle' => get_the_excerpt(),
	) );
	?>
	<article class="single-service section">
		<div class="container narrow">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="single-service__hero" data-reveal><?php the_post_thumbnail( 'aeon-wide' ); ?></figure>
			<?php endif; ?>
			<div class="entry-content" data-reveal><?php the_content(); ?></div>

			<div class="single-service__cta" data-reveal>
				<h3><?php aeon_e( 'cta_band_title' ); ?></h3>
				<a class="btn btn--primary btn--lg" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
					<span><?php aeon_e( 'cta_request' ); ?></span><?php echo aeon_icon( 'arrow' ); ?>
				</a>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
