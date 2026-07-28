<?php
/**
 * Work chapter — vertical video galleries. Shared by the reels (profile p17–19)
 * and motion (p20–21) chapters, which use the same layout and proof strip.
 *
 * Args:
 *   slug    string   Chapter slug, used for the anchor id.
 *   title_1 string   i18n key for the leading words of the heading.
 *   title_2 string   i18n key for the accented words.
 *   sub     string   i18n key for the subtitle.
 *   items   string[] Gallery paths relative to assets/images.
 *
 * @package AEON
 */
$items = isset( $args['items'] ) ? $args['items'] : array();
if ( empty( $items ) ) {
	return;
}
$slug = isset( $args['slug'] ) ? $args['slug'] : 'reels';
?>
<section class="chapter" id="chapter-<?php echo esc_attr( $slug ); ?>">
	<?php aeon_soft_bg(); ?>
	<div class="container">

		<header class="chapter__head section-head--center" data-reveal>
			<?php
			aeon_rule_heading(
				esc_html( aeon_t( $args['title_1'] ) ) . ' <span class="accent">' . esc_html( aeon_t( $args['title_2'] ) ) . '</span>'
			);
			?>
			<p class="section-sub"><?php aeon_e( $args['sub'] ); ?></p>
		</header>

		<div class="vgrid stagger" data-reveal>
			<?php foreach ( $items as $item ) : ?>
				<div class="vgrid__item">
					<img src="<?php echo esc_url( aeon_img( $item ) ); ?>"
						alt="<?php echo esc_attr( aeon_t( $args['title_1'] ) ); ?>"
						loading="lazy" decoding="async">
				</div>
			<?php endforeach; ?>
		</div>

		<div data-reveal><?php aeon_proof_strip(); ?></div>

	</div>
</section>
