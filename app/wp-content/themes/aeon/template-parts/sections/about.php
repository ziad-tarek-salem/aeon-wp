<?php
/**
 * About / Expertise section — "نبذة عن خبراتنا" (company profile p3).
 *
 * Full-width horizontal layout: centred heading, expertise points in two
 * columns, the branch pins on a row of their own, then a row of counters.
 *
 * Every icon here is dashboard-managed — each item resolves through
 * aeon_card_icon(), so an uploaded file or a pasted URL beats the picked key.
 *
 * @package AEON
 */

$about_branches = aeon_branch_locations();
if ( empty( $about_branches ) ) {
	// Profile fallback when no branches have been added yet.
	$about_branches = array_map(
		function ( $name ) {
			return array( 'name' => $name, 'maps_url' => '', 'icon' => 'pin', 'icon_id' => 0, 'icon_url' => '' );
		},
		array( 'عجمان', 'أبو ظبي - العين 1', 'أبو ظبي - العين 2', 'الإسكندرية - مصر' )
	);
}
?>
<section class="about section about--tight" id="about">
	<?php aeon_soft_bg(); ?>
	<div class="container">

		<h2 class="about__title" data-reveal>
			<?php aeon_e( 'about_exp_t1' ); ?>
			<span class="about__title-accent"><?php aeon_e( 'about_exp_t2' ); ?></span>
		</h2>

		<ul class="about__bullets stagger" data-reveal>
			<?php foreach ( aeon_expertise_list() as $item ) : ?>
				<li class="about-point">
					<span class="about-point__icon"><?php echo aeon_card_icon( $item ); ?></span>
					<p class="about-point__text"><?php echo esc_html( $item['name'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="about__branches" data-reveal>
			<p class="about__branches-title"><span><?php aeon_e( 'about_branches_t' ); ?></span></p>
			<ul class="about__branch-list">
				<?php foreach ( $about_branches as $branch ) : ?>
					<li class="branch">
						<?php if ( ! empty( $branch['maps_url'] ) ) : ?>
							<a class="branch__link" href="<?php echo esc_url( $branch['maps_url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<span class="branch__pin"><?php echo aeon_card_icon( $branch ); ?></span>
								<span class="branch__name"><?php echo esc_html( $branch['name'] ); ?></span>
							</a>
						<?php else : ?>
							<span class="branch__link">
								<span class="branch__pin"><?php echo aeon_card_icon( $branch ); ?></span>
								<span class="branch__name"><?php echo esc_html( $branch['name'] ); ?></span>
							</span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<ul class="about__stats stagger" data-reveal>
			<?php foreach ( aeon_stats_list() as $stat ) : ?>
				<li class="about-stat">
					<span class="about-stat__icon"><?php echo aeon_card_icon( $stat ); ?></span>
					<span class="about-stat__num" data-count="<?php echo esc_attr( $stat['val'] ); ?>" data-suffix="<?php echo esc_attr( $stat['suffix'] ); ?>">0<?php echo esc_html( $stat['suffix'] ); ?></span>
					<span class="about-stat__label"><?php echo esc_html( $stat['name'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
