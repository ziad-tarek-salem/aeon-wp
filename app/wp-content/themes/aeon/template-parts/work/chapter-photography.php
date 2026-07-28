<?php
/**
 * Work chapter — professional photography (company profile p11–16).
 * Keeps the deck's four side labels and the closing tagline.
 *
 * Args:
 *   items string[] Gallery paths relative to assets/images.
 *
 * @package AEON
 */
$items = isset( $args['items'] ) ? $args['items'] : array();
if ( empty( $items ) ) {
	return;
}
?>
<section class="chapter" id="chapter-photography">
	<?php aeon_soft_bg(); ?>
	<div class="container">

		<header class="chapter__head section-head--center" data-reveal>
			<?php
			aeon_rule_heading(
				esc_html( aeon_t( 'photo_title_1' ) ) . ' <span class="accent">' . esc_html( aeon_t( 'photo_title_2' ) ) . '</span>'
			);
			?>
			<p class="section-sub"><?php aeon_e( 'photo_sub' ); ?></p>
		</header>

		<div class="photoblock" data-reveal>
			<div class="photolabels">
				<?php foreach ( aeon_photo_labels() as $label ) : ?>
					<div class="photolabel">
						<span class="photolabel__icon"><?php echo aeon_icon( $label['icon'] ); ?></span>
						<span>
							<span class="photolabel__t"><?php aeon_e( $label['t'] ); ?></span><br>
							<span class="photolabel__d"><?php aeon_e( $label['d'] ); ?></span>
						</span>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="photo-mosaic">
				<?php foreach ( $items as $item ) : ?>
					<img src="<?php echo esc_url( aeon_img( $item ) ); ?>"
						alt="<?php echo esc_attr( aeon_t( 'photo_title_1' ) . ' ' . aeon_t( 'photo_title_2' ) ); ?>"
						loading="lazy" decoding="async">
				<?php endforeach; ?>
			</div>
		</div>

		<p class="chapter__tagline" data-reveal>
			<?php
			// The profile prints this line under every photography page, with
			// the middle word carrying the accent colour.
			echo wp_kses_post( str_replace(
				array( 'تحكي', 'tells' ),
				array( '<span class="accent">تحكي</span>', '<span class="accent">tells</span>' ),
				esc_html( aeon_t( 'photo_tagline' ) )
			) );
			?>
		</p>

	</div>
</section>
