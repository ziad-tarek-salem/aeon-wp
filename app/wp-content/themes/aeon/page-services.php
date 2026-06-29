<?php
/**
 * Template Name: Services Page
 *
 * @package AEON
 */
get_header();
get_template_part( 'template-parts/components/page-banner', null, array(
	'title'    => aeon_t( 'services_title' ),
	'subtitle' => aeon_t( 'services_sub' ),
) );

$service_q = new WP_Query( array(
	'post_type'      => 'service',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );
?>
<section class="services section">
	<div class="container">
		<div class="services__grid services__grid--page stagger" data-reveal>
			<?php if ( $service_q->have_posts() ) : ?>
				<?php while ( $service_q->have_posts() ) : $service_q->the_post(); ?>
					<a class="service-card" href="<?php the_permalink(); ?>">
						<span class="service-card__icon"><?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'thumbnail' ) : aeon_icon( 'globe' ); ?></span>
						<h3 class="service-card__title"><?php the_title(); ?></h3>
						<p class="service-card__desc"><?php echo esc_html( aeon_excerpt( 18 ) ); ?></p>
						<span class="service-card__more"><?php aeon_e( 'svc_learn' ); ?> <?php echo aeon_icon( 'arrow' ); ?></span>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			<?php else : ?>
				<?php foreach ( aeon_default_services() as $svc ) : ?>
					<div class="service-card">
						<span class="service-card__icon"><?php echo aeon_icon( $svc['icon'] ); ?></span>
						<h3 class="service-card__title"><?php aeon_e( $svc['title'] ); ?></h3>
						<p class="service-card__desc"><?php aeon_e( $svc['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php
get_template_part( 'template-parts/sections/why' );
get_footer();
