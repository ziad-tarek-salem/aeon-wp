<?php
/**
 * Portfolio teaser — the homepage stays an overview, so each of the profile's
 * five work chapters (web, photography, reels, motion, social) is reduced to one
 * card that links through to the full /work/ archive.
 *
 * @package AEON
 */
$gallery = aeon_profile_gallery();
if ( empty( $gallery ) ) {
	return;
}
$work_url = get_post_type_archive_link( 'portfolio' );
if ( ! $work_url ) {
	$work_url = home_url( '/work/' );
}
?>
<section class="showcase section" id="showcase">
	<?php aeon_soft_bg(); ?>
	<div class="container">
		<header class="section-head section-head--center" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'work_eyebrow' ); ?></p>
			<?php aeon_rule_heading( esc_html( aeon_t( 'show_title' ) ) ); ?>
			<p class="section-sub"><?php aeon_e( 'show_sub' ); ?></p>
		</header>

		<div class="teaser__grid stagger" data-reveal>
			<?php
			foreach ( $gallery as $slug => $cat ) :
				$label = aeon_t( $cat['label_key'] );
				$shots = array_slice( $cat['items'], 0, 3 );
				?>
				<a class="teaser-card" href="<?php echo esc_url( $work_url . '#chapter-' . $slug ); ?>">
					<div class="teaser-card__stack<?php echo count( $shots ) < 3 ? ' teaser-card__stack--single' : ''; ?>">
						<?php foreach ( $shots as $shot ) : ?>
							<img src="<?php echo esc_url( aeon_img( $shot ) ); ?>"
								alt="<?php echo esc_attr( $label ); ?>" loading="lazy" decoding="async">
						<?php endforeach; ?>
					</div>
					<span class="teaser-card__bar">
						<span class="teaser-card__title"><?php echo esc_html( $label ); ?></span>
						<span class="teaser-card__count"><?php echo esc_html( number_format_i18n( count( $cat['items'] ) ) ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>

		<div class="teaser__more" data-reveal>
			<a class="btn btn--primary btn--lg magnetic" href="<?php echo esc_url( $work_url ); ?>">
				<span><?php aeon_e( 'show_all_work' ); ?></span>
				<?php echo aeon_icon( 'arrow' ); ?>
			</a>
		</div>
	</div>
</section>
