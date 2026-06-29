<?php
/**
 * Portfolio preview section.
 *
 * @package AEON
 */
$work_q = new WP_Query( array(
	'post_type'      => 'portfolio',
	'posts_per_page' => 6,
) );
?>
<section class="work section" id="work">
	<div class="container">
		<header class="section-head" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'work_eyebrow' ); ?></p>
			<h2 class="section-title"><?php aeon_e( 'work_title' ); ?></h2>
			<p class="section-sub"><?php aeon_e( 'work_sub' ); ?></p>
		</header>

		<div class="work__grid stagger" data-reveal>
			<?php if ( $work_q->have_posts() ) : ?>
				<?php $i = 0; while ( $work_q->have_posts() ) : $work_q->the_post(); $i++; ?>
					<a class="work-card <?php echo ( 1 === $i || 4 === $i ) ? 'work-card--wide' : ''; ?>" href="<?php the_permalink(); ?>">
						<div class="work-card__media">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'aeon-card' ); ?>
							<?php else : ?>
								<img src="<?php echo esc_url( AEON_URI . '/assets/images/services-grid.jpeg' ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
							<?php endif; ?>
						</div>
						<div class="work-card__overlay">
							<?php
							$cats = get_the_terms( get_the_ID(), 'work_category' );
							if ( $cats && ! is_wp_error( $cats ) ) :
								?>
								<span class="work-card__cat"><?php echo esc_html( $cats[0]->name ); ?></span>
							<?php endif; ?>
							<h3 class="work-card__title"><?php the_title(); ?></h3>
							<span class="work-card__view"><?php aeon_e( 'work_view' ); ?> <?php echo aeon_icon( 'arrow' ); ?></span>
						</div>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			<?php else : ?>
				<?php
				$placeholders = array(
					array( 'brand-1.jpeg', 'svc_brand_t', true ),
					array( 'services-grid.jpeg', 'svc_design_t', false ),
					array( 'why-choose.jpeg', 'svc_marketing_t', false ),
					array( 'banner-cover.jpeg', 'svc_video_t', true ),
					array( 'services-grid.jpeg', 'svc_social_t', false ),
					array( 'brand-1.jpeg', 'svc_web_t', false ),
				);
				foreach ( $placeholders as $p ) :
					?>
					<div class="work-card <?php echo $p[2] ? 'work-card--wide' : ''; ?>">
						<div class="work-card__media">
							<img src="<?php echo esc_url( AEON_URI . '/assets/images/' . $p[0] ); ?>" alt="<?php echo esc_attr( aeon_t( $p[1] ) ); ?>" loading="lazy">
						</div>
						<div class="work-card__overlay">
							<span class="work-card__cat"><?php aeon_e( 'work_eyebrow' ); ?></span>
							<h3 class="work-card__title"><?php aeon_e( $p[1] ); ?></h3>
							<span class="work-card__view"><?php aeon_e( 'work_view' ); ?> <?php echo aeon_icon( 'arrow' ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<div class="section-cta" data-reveal>
			<a class="btn btn--outline btn--lg" href="<?php echo esc_url( home_url( '/work/' ) ); ?>">
				<span><?php aeon_e( 'work_view_all' ); ?></span>
				<?php echo aeon_icon( 'arrow' ); ?>
			</a>
		</div>
	</div>
</section>
