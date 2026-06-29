<?php
/**
 * Main index / blog listing (fallback template).
 *
 * @package AEON
 */
get_header();

$banner_title = is_home() ? aeon_t( 'blog_title' ) : ( is_search() ? sprintf( '%s', get_search_query() ) : get_the_archive_title() );
get_template_part( 'template-parts/components/page-banner', null, array( 'title' => wp_strip_all_tags( $banner_title ) ) );
?>
<section class="blog section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="blog__grid stagger" data-reveal>
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="post-card">
						<a class="post-card__media" href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'aeon-card' ); ?>
							<?php else : ?>
								<span class="post-card__ph"><?php echo aeon_icon( 'pen' ); ?></span>
							<?php endif; ?>
						</a>
						<div class="post-card__body">
							<div class="post-card__meta">
								<span><?php echo esc_html( get_the_date() ); ?></span>
								<?php $cat = get_the_category(); if ( $cat ) : ?>
									<span class="post-card__cat"><?php echo esc_html( $cat[0]->name ); ?></span>
								<?php endif; ?>
							</div>
							<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p class="post-card__excerpt"><?php echo esc_html( aeon_excerpt( 22 ) ); ?></p>
							<a class="post-card__more" href="<?php the_permalink(); ?>"><?php aeon_e( 'read_more' ); ?> <?php echo aeon_icon( 'arrow' ); ?></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '‹', 'next_text' => '›' ) ); ?>
		<?php else : ?>
			<p class="empty-state"><?php aeon_e( 'work_empty' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
