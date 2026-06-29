<?php
/**
 * Partners / trust marquee.
 *
 * @package AEON
 */
$partners = array( 'BURJ MEDIA', 'NOVA', 'OASIS GROUP', 'PIXEL LAB', 'SUMMIT', 'AL NAJM', 'VERTEX', 'HORIZON' );
?>
<section class="partners" aria-label="<?php echo esc_attr( aeon_t( 'partners_title' ) ); ?>">
	<div class="container">
		<p class="partners__title"><?php aeon_e( 'partners_title' ); ?></p>
	</div>
	<div class="marquee" data-marquee>
		<div class="marquee__track">
			<?php for ( $i = 0; $i < 2; $i++ ) : ?>
				<?php foreach ( $partners as $p ) : ?>
					<span class="marquee__item"><?php echo esc_html( $p ); ?></span>
					<span class="marquee__dot" aria-hidden="true">•</span>
				<?php endforeach; ?>
			<?php endfor; ?>
		</div>
	</div>
</section>
