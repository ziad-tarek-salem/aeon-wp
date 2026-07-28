<?php
/**
 * Visit our office — "نتشرف بزيارتكم" (company profile p6).
 * Consultation CTA plus the office photo gallery. The gallery is dashboard-
 * managed (محتوى الموقع → صور الموقع → صور المكتب) and falls back to the shots
 * cropped from the company profile.
 *
 * @package AEON
 */
$office_imgs = aeon_image_gallery( 'office', 'large' );
if ( empty( $office_imgs ) ) {
	return;
}
$office_main = array_shift( $office_imgs );
?>
<section class="office section" id="office">
	<?php aeon_soft_bg(); ?>
	<div class="container office__inner">

		<div class="office__content" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'office_eyebrow' ); ?></p>
			<h2 class="section-title"><?php aeon_e( 'office_title' ); ?></h2>
			<p class="office__sub"><?php aeon_e( 'office_sub' ); ?></p>

			<a class="btn btn--primary btn--lg" href="<?php echo esc_url( aeon_contact_url() ); ?>">
				<span><?php aeon_e( 'office_cta' ); ?></span>
				<?php echo aeon_icon( 'arrow' ); ?>
			</a>
		</div>

		<div class="office__gallery" data-reveal>
			<div class="office__main">
				<img src="<?php echo esc_url( $office_main['url'] ); ?>" alt="<?php echo esc_attr( $office_main['alt'] ? $office_main['alt'] : aeon_t( 'office_title' ) ); ?>" loading="lazy">
			</div>
			<?php if ( $office_imgs ) : ?>
				<div class="office__thumbs">
					<?php foreach ( $office_imgs as $img ) : ?>
						<div class="office__thumb">
							<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ? $img['alt'] : aeon_t( 'office_eyebrow' ) ); ?>" loading="lazy">
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

	</div>
</section>
