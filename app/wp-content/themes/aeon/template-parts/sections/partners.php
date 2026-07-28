<?php
/**
 * Partners / trust marquee — an auto-scrolling strip of real client logos
 * from the company profile. Falls back to nothing if logos are unavailable.
 *
 * @package AEON
 */
$logos = aeon_partner_logos();
?>
<section class="partners" aria-label="<?php echo esc_attr( aeon_t( 'partners_title' ) ); ?>">
	<div class="container">
		<p class="partners__title"><?php aeon_e( 'partners_title' ); ?></p>
	</div>
	<?php if ( $logos ) : ?>
		<div class="marquee marquee--logos" data-marquee>
			<div class="marquee__track">
				<?php for ( $i = 0; $i < 2; $i++ ) : ?>
					<?php foreach ( $logos as $logo ) : ?>
						<span class="marquee__logo">
							<img src="<?php echo esc_url( $logo['url'] ); ?>"
								alt="<?php echo esc_attr( $logo['name'] ); ?>"
								loading="lazy" decoding="async" width="150" height="83"
								<?php echo 0 === $i ? '' : 'aria-hidden="true"'; ?>>
						</span>
					<?php endforeach; ?>
				<?php endfor; ?>
			</div>
		</div>
	<?php endif; ?>
</section>
