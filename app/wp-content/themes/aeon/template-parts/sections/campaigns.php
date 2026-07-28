<?php
/**
 * Ad-campaign platforms — Google, Snapchat, TikTok, Facebook & Instagram.
 *
 * Uses the AEON-branded campaign creatives the client supplied alongside the
 * profile. Renders nothing when the artwork is absent.
 *
 * @package AEON
 */
$platforms = aeon_campaign_platforms();
if ( empty( $platforms ) ) {
	return;
}
?>
<section class="campaigns section" id="campaigns">
	<?php aeon_soft_bg(); ?>
	<div class="container">
		<header class="section-head section-head--center" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'camp_eyebrow' ); ?></p>
			<?php aeon_rule_heading( esc_html( aeon_t( 'camp_title' ) ) ); ?>
			<p class="section-sub"><?php aeon_e( 'camp_sub' ); ?></p>
		</header>

		<div class="campaigns__grid stagger" data-reveal>
			<?php foreach ( $platforms as $card ) : ?>
				<article class="campaign-card">
					<div class="campaign-card__media">
						<img src="<?php echo esc_url( aeon_img( $card['file'] ) ); ?>"
							alt="<?php echo esc_attr( aeon_t( $card['title_key'] ) ); ?>"
							loading="lazy" decoding="async">
					</div>
					<div class="campaign-card__body">
						<h3 class="campaign-card__title"><?php aeon_e( $card['title_key'] ); ?></h3>
						<p class="campaign-card__text"><?php aeon_e( $card['text_key'] ); ?></p>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
