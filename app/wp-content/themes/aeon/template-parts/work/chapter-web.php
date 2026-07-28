<?php
/**
 * Work chapter — website design & development (company profile p10).
 *
 * Args:
 *   items string[] Gallery paths relative to assets/images.
 *
 * @package AEON
 */
$items = isset( $args['items'] ) ? $args['items'] : array();
$shot  = $items ? reset( $items ) : '';
?>
<section class="chapter" id="chapter-web">
	<?php aeon_soft_bg(); ?>
	<div class="container">
		<div class="webblock" data-reveal>

			<div class="webblock__text">
				<?php
				aeon_rule_heading(
					esc_html( aeon_t( 'web_title_1' ) ) . ' <span class="accent">' . esc_html( aeon_t( 'web_title_2' ) ) . '</span>',
					'h2',
					'section-title'
				);
				?>
				<p class="webblock__lead"><?php aeon_e( 'web_lead' ); ?></p>
				<p><?php aeon_e( 'web_text' ); ?></p>

				<a class="btn btn--primary btn--lg magnetic" href="<?php echo esc_url( aeon_contact_url() ); ?>">
					<?php echo aeon_icon( 'globe' ); ?>
					<span><?php aeon_e( 'web_cta' ); ?></span>
				</a>

				<div class="webblock__features">
					<?php foreach ( aeon_web_features() as $f ) : ?>
						<div class="webfeature">
							<?php echo aeon_icon( $f['icon'] ); ?>
							<span>
								<span class="webfeature__t"><?php aeon_e( $f['t'] ); ?></span><br>
								<span class="webfeature__d"><?php aeon_e( $f['d'] ); ?></span>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( $shot ) : ?>
				<div class="webblock__media">
					<img src="<?php echo esc_url( aeon_img( $shot ) ); ?>"
						alt="<?php echo esc_attr( aeon_t( 'web_title_1' ) . ' ' . aeon_t( 'web_title_2' ) ); ?>"
						loading="lazy" decoding="async">
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
