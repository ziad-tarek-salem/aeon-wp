<?php
/**
 * Portfolio archive — filterable grid.
 *
 * @package AEON
 */
get_header();
get_template_part( 'template-parts/components/page-banner', null, array(
	'title'    => aeon_t( 'work_title' ),
	'subtitle' => aeon_t( 'work_sub' ),
) );

$cats = get_terms( array( 'taxonomy' => 'work_category', 'hide_empty' => true ) );
?>
<section class="work section">
	<div class="container">

		<?php if ( $cats && ! is_wp_error( $cats ) ) : ?>
			<div class="work-filters" data-work-filters data-reveal>
				<button class="work-filter is-active" data-filter="*"><?php aeon_e( 'work_all' ); ?></button>
				<?php foreach ( $cats as $c ) : ?>
					<button class="work-filter" data-filter="cat-<?php echo esc_attr( $c->slug ); ?>"><?php echo esc_html( $c->name ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="work__grid work__grid--archive stagger" data-work-grid data-reveal>
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post();
					$terms = get_the_terms( get_the_ID(), 'work_category' );
					$classes = '';
					if ( $terms && ! is_wp_error( $terms ) ) {
						foreach ( $terms as $t ) {
							$classes .= ' cat-' . $t->slug;
						}
					}
					?>
					<a class="work-card<?php echo esc_attr( $classes ); ?>" href="<?php the_permalink(); ?>">
						<div class="work-card__media">
							<?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'aeon-card' ) : '<img src="' . esc_url( AEON_URI . '/assets/images/services-grid.jpeg' ) . '" alt="" loading="lazy">'; ?>
						</div>
						<div class="work-card__overlay">
							<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
								<span class="work-card__cat"><?php echo esc_html( $terms[0]->name ); ?></span>
							<?php endif; ?>
							<h3 class="work-card__title"><?php the_title(); ?></h3>
							<span class="work-card__view"><?php aeon_e( 'work_view' ); ?> <?php echo aeon_icon( 'arrow' ); ?></span>
						</div>
					</a>
				<?php endwhile; ?>
			<?php else : ?>
				<p class="empty-state"><?php aeon_e( 'work_empty' ); ?></p>
			<?php endif; ?>
		</div>

		<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '‹', 'next_text' => '›' ) ); ?>
	</div>
</section>
<?php
get_footer();
