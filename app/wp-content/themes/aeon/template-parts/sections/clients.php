<?php
/**
 * "Success Partners" — the real client-logo wall from the company profile,
 * shown as a responsive grid of uniform logo cards.
 *
 * @package AEON
 */
$logos = aeon_partner_logos();
if ( ! $logos ) {
	return;
}
?>
<section class="clients section" id="clients">
	<?php aeon_soft_bg(); ?>
	<div class="container">
		<header class="section-head section-head--center" data-reveal>
			<h2 class="clients__proud"><?php aeon_e( 'clients_proud' ); ?></h2>
			<p class="clients__region"><?php aeon_e( 'clients_region' ); ?></p>
		</header>

		<div class="section-head section-head--center" data-reveal>
			<?php aeon_rule_heading( esc_html( aeon_t( 'clients_partners' ) ), 'h3', 'section-title' ); ?>
		</div>

		<div class="clients__grid stagger" data-reveal>
			<?php foreach ( $logos as $logo ) : ?>
				<div class="client-logo">
					<img
						src="<?php echo esc_url( $logo['url'] ); ?>"
						alt="<?php echo esc_attr( $logo['name'] ); ?>"
						loading="lazy" decoding="async" width="180" height="100">
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
