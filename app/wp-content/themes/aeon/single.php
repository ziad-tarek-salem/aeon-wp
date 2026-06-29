<?php
/**
 * Single blog post.
 *
 * @package AEON
 */
get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/components/page-banner', null, array( 'title' => get_the_title() ) );
	?>
	<article class="single-post section">
		<div class="container narrow">
			<div class="single-post__meta" data-reveal>
				<span><?php echo esc_html( get_the_date() ); ?></span>
				<span><?php the_author(); ?></span>
			</div>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="single-post__hero" data-reveal><?php the_post_thumbnail( 'aeon-wide' ); ?></figure>
			<?php endif; ?>
			<div class="entry-content" data-reveal>
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
