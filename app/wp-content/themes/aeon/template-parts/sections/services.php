<?php
/**
 * Services grid section. Uses Service CPT if available, else brand defaults.
 *
 * @package AEON
 */
$service_q = new WP_Query( array(
	'post_type'      => 'service',
	'posts_per_page' => 8,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );
?>
<section class="services section" id="services">
	<div class="container">
		<header class="section-head" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'services_eyebrow' ); ?></p>
			<h2 class="section-title"><?php aeon_e( 'services_title' ); ?></h2>
			<p class="section-sub"><?php aeon_e( 'services_sub' ); ?></p>
		</header>

		<div class="services__grid stagger" data-reveal>
			<?php if ( $service_q->have_posts() ) : ?>
				<?php while ( $service_q->have_posts() ) : $service_q->the_post(); ?>
					<a class="service-card" href="<?php the_permalink(); ?>">
						<span class="service-card__icon">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'thumbnail' );
							} else {
								echo aeon_icon( 'globe' );
							}
							?>
						</span>
						<h3 class="service-card__title"><?php the_title(); ?></h3>
						<p class="service-card__desc"><?php echo esc_html( aeon_excerpt( 16 ) ); ?></p>
						<span class="service-card__more"><?php aeon_e( 'svc_learn' ); ?> <?php echo aeon_icon( 'arrow' ); ?></span>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			<?php else : ?>
				<?php foreach ( aeon_default_services() as $svc ) : ?>
					<div class="service-card">
						<span class="service-card__icon"><?php echo aeon_icon( $svc['icon'] ); ?></span>
						<h3 class="service-card__title"><?php aeon_e( $svc['title'] ); ?></h3>
						<p class="service-card__desc"><?php aeon_e( $svc['desc'] ); ?></p>
						<span class="service-card__more"><?php aeon_e( 'svc_learn' ); ?> <?php echo aeon_icon( 'arrow' ); ?></span>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
