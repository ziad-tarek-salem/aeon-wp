<?php
/**
 * Work chapter — social-media design (company profile p22–37).
 *
 * The profile devotes one page per client and tags each with an industry chip
 * (سياحة، قهوة، عطور…). Here the samples are grouped under those same chips.
 *
 * @package AEON
 */
$groups = aeon_social_groups();
if ( empty( $groups ) ) {
	return;
}
?>
<section class="chapter" id="chapter-social">
	<?php aeon_soft_bg(); ?>
	<div class="container">

		<header class="chapter__head section-head--center" data-reveal>
			<?php
			aeon_rule_heading(
				esc_html( aeon_t( 'social_title_1' ) ) . ' <span class="accent">' . esc_html( aeon_t( 'social_title_2' ) ) . '</span>'
			);
			?>
			<p class="section-sub"><?php aeon_e( 'social_sub' ); ?></p>
		</header>

		<?php foreach ( $groups as $group ) : ?>
			<div class="industry-group" data-reveal>
				<div class="industry-group__head">
					<span class="industry-group__chip">
						<?php echo aeon_icon( $group['icon'] ); ?>
						<span><?php echo esc_html( $group['label'] ); ?></span>
					</span>
					<span class="industry-group__rule" aria-hidden="true"></span>
				</div>

				<div class="social-row">
					<?php foreach ( $group['items'] as $item ) : ?>
						<div class="social-row__item">
							<img src="<?php echo esc_url( aeon_img( $item ) ); ?>"
								alt="<?php echo esc_attr( aeon_t( 'social_title_1' ) . ' — ' . $group['label'] ); ?>"
								loading="lazy" decoding="async">
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>

	</div>
</section>
