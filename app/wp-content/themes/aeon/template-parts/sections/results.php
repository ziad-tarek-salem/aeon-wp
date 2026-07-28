<?php
/**
 * Ad-campaign results — "إعلانات انستجرام" proof block (company profile p38).
 *
 * Mirrors the profile page: the analytics card on one side (six metrics plus
 * the followers/non-followers donut) and the four reel thumbnails with their
 * view counts on the other, closed by the recurring proof strip.
 *
 * The thumbnails are the profile's own blurred versions — the client chose to
 * obscure the client-facing creatives there, so the site does the same.
 *
 * @package AEON
 */
$reels = aeon_reel_proofs();
?>
<section class="results section section--dark" id="results">
	<?php aeon_soft_bg(); ?>
	<div class="container results__inner">

		<header class="results__head" data-reveal>
			<p class="eyebrow eyebrow--light"><?php aeon_e( 'results_eyebrow' ); ?></p>
			<h2 class="section-title section-title--light"><?php aeon_e( 'results_title' ); ?></h2>
			<p class="section-sub section-sub--light"><?php aeon_e( 'results_sub' ); ?></p>
			<span class="results__badge"><?php aeon_e( 'results_partner' ); ?></span>
		</header>

		<div class="results__panel" data-reveal>

			<div class="analytics-card">
				<div class="analytics-card__bar" aria-hidden="true"><span></span><span></span><span></span></div>

				<div class="analytics-card__metrics">
					<?php foreach ( aeon_ad_metrics() as $m ) : ?>
						<div class="analytics-metric">
							<span class="analytics-metric__label">
								<?php echo aeon_icon( $m['icon'] ); ?>
								<span><?php aeon_e( $m['label_key'] ); ?></span>
							</span>
							<span class="analytics-metric__value"><?php aeon_e( $m['value_key'] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>

				<div class="analytics-card__donut-wrap">
					<p class="analytics-card__donut-title"><?php aeon_e( 'donut_title' ); ?></p>
					<p class="analytics-card__donut-sub"><?php aeon_e( 'donut_sub' ); ?></p>
					<div class="analytics-card__donut-row">
						<span class="donut" role="img"
							aria-label="<?php echo esc_attr( aeon_t( 'donut_non' ) . ' 100% — ' . aeon_t( 'donut_followers' ) . ' 0%' ); ?>"></span>
						<span class="donut-key">
							<span class="donut-key__item">
								<span class="donut-key__dot donut-key__dot--a" aria-hidden="true"></span>
								<span class="donut-key__pct">100%</span>
								<span><?php aeon_e( 'donut_non' ); ?></span>
							</span>
							<span class="donut-key__item">
								<span class="donut-key__dot donut-key__dot--b" aria-hidden="true"></span>
								<span class="donut-key__pct">0%</span>
								<span><?php aeon_e( 'donut_followers' ); ?></span>
							</span>
						</span>
					</div>
				</div>
			</div>

			<?php if ( $reels ) : ?>
				<div class="reel-grid">
					<?php foreach ( $reels as $reel ) : ?>
						<div class="reel-card">
							<div class="reel-card__media">
								<img src="<?php echo esc_url( aeon_img( $reel['file'] ) ); ?>"
									alt="<?php echo esc_attr( aeon_t( 'results_eyebrow' ) ); ?>"
									loading="lazy" decoding="async">
								<span class="reel-card__play" aria-hidden="true"><?php echo aeon_icon( 'play' ); ?></span>
							</div>
							<p class="reel-card__views">
								<?php echo aeon_icon( 'eye' ); ?>
								<span><?php echo esc_html( $reel['views'] . ' ' . aeon_t( 'views_unit' ) ); ?></span>
							</p>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>

		<div data-reveal><?php aeon_proof_strip(); ?></div>

	</div>
</section>
