<?php
/**
 * Work chapter — projects the client has added from the dashboard.
 *
 * Rendered above the profile chapters so newly-published work leads the page.
 * Each project can carry an industry via the `_aeon_industry` meta box.
 *
 * @package AEON
 */
if ( ! have_posts() ) {
	return;
}
$aeon_industry_labels = wp_list_pluck( aeon_industries(), 'ar', 'slug' );
?>
<section class="chapter" id="chapter-projects">
	<?php aeon_soft_bg(); ?>
	<div class="container">

		<header class="chapter__head section-head--center" data-reveal>
			<?php aeon_rule_heading( esc_html( aeon_t( 'work_title' ) ) ); ?>
			<p class="section-sub"><?php aeon_e( 'work_sub' ); ?></p>
		</header>

		<div class="work__grid work__grid--archive stagger" data-reveal>
			<?php
			while ( have_posts() ) :
				the_post();
				$aeon_terms    = get_the_terms( get_the_ID(), 'work_category' );
				$aeon_industry = (string) get_post_meta( get_the_ID(), '_aeon_industry', true );
				$aeon_classes  = '';
				if ( $aeon_terms && ! is_wp_error( $aeon_terms ) ) {
					foreach ( $aeon_terms as $aeon_term ) {
						$aeon_classes .= ' cat-' . $aeon_term->slug;
					}
				}
				?>
				<a class="work-card<?php echo esc_attr( $aeon_classes ); ?>" href="<?php the_permalink(); ?>">
					<div class="work-card__media">
						<?php
						echo has_post_thumbnail()
							? get_the_post_thumbnail( null, 'aeon-card' )
							: '<img src="' . esc_url( aeon_image_url( 'services_grid', 'large' ) ) . '" alt="" loading="lazy">';
						?>
					</div>
					<div class="work-card__overlay">
						<?php if ( $aeon_industry && isset( $aeon_industry_labels[ $aeon_industry ] ) ) : ?>
							<span class="work-card__cat"><?php echo esc_html( $aeon_industry_labels[ $aeon_industry ] ); ?></span>
						<?php elseif ( $aeon_terms && ! is_wp_error( $aeon_terms ) ) : ?>
							<span class="work-card__cat"><?php echo esc_html( $aeon_terms[0]->name ); ?></span>
						<?php endif; ?>
						<h3 class="work-card__title"><?php the_title(); ?></h3>
						<span class="work-card__view"><?php aeon_e( 'work_view' ); ?> <?php echo aeon_icon( 'arrow' ); ?></span>
					</div>
				</a>
			<?php endwhile; ?>
		</div>

		<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '‹', 'next_text' => '›' ) ); ?>
	</div>
</section>
