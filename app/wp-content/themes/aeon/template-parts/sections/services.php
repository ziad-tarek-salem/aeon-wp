<?php
/**
 * Services grid section. Clean cards (icon, title, short description) each with
 * a "View Details" button linking to the service's detail block on the Services
 * page. Reads the "الخدمات" terms (aeon_service), else brand defaults.
 *
 * @package AEON
 */
$services      = aeon_services_list();
$services_base = trailingslashit( home_url( '/services/' ) );
?>
<section class="services section" id="services">
	<?php aeon_soft_bg(); ?>
	<div class="container">
		<header class="section-head" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'services_eyebrow' ); ?></p>
			<h2 class="section-title"><?php aeon_e( 'services_title' ); ?></h2>
			<p class="section-sub"><?php aeon_e( 'services_sub' ); ?></p>
		</header>

		<div class="services__grid stagger" data-reveal>
			<?php foreach ( $services as $svc ) : ?>
				<?php $url = $svc['slug'] ? $services_base . '#service-' . $svc['slug'] : $services_base; ?>
				<div class="service-card">
					<span class="service-card__icon"><?php echo aeon_card_icon( $svc ); ?></span>
					<h3 class="service-card__title"><?php echo esc_html( $svc['name'] ); ?></h3>
					<p class="service-card__desc"><?php echo esc_html( $svc['desc'] ); ?></p>
					<a class="service-card__cta" href="<?php echo esc_url( $url ); ?>">
						<span><?php aeon_e( 'svc_learn' ); ?></span>
						<?php echo aeon_icon( 'arrow' ); ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
