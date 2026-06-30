<?php
/**
 * Services grid section. Reads the "الخدمات" terms (aeon_service), else brand
 * defaults.
 *
 * @package AEON
 */
$services = aeon_section_terms( 'aeon_service' );
?>
<section class="services section" id="services">
	<div class="container">
		<header class="section-head" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'services_eyebrow' ); ?></p>
			<h2 class="section-title"><?php aeon_e( 'services_title' ); ?></h2>
			<p class="section-sub"><?php aeon_e( 'services_sub' ); ?></p>
		</header>

		<div class="services__grid stagger" data-reveal>
			<?php if ( $services ) : ?>
				<?php foreach ( $services as $service ) : ?>
					<?php $svc_icon = get_term_meta( $service->term_id, '_aeon_icon', true ); ?>
					<div class="service-card">
						<span class="service-card__icon"><?php echo aeon_icon( $svc_icon ? $svc_icon : 'globe' ); ?></span>
						<h3 class="service-card__title"><?php echo esc_html( $service->name ); ?></h3>
						<p class="service-card__desc"><?php echo esc_html( $service->description ); ?></p>
						<span class="service-card__more"><?php aeon_e( 'svc_learn' ); ?> <?php echo aeon_icon( 'arrow' ); ?></span>
					</div>
				<?php endforeach; ?>
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
