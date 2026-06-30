<?php
/**
 * Why choose us section.
 *
 * @package AEON
 */
?>
<section class="why section section--dark" id="why">
	<div class="why__bg" aria-hidden="true"><span class="dot-grid dot-grid--why"></span></div>
	<div class="container">
		<header class="section-head section-head--center" data-reveal>
			<p class="eyebrow eyebrow--light"><?php aeon_e( 'why_eyebrow' ); ?></p>
			<h2 class="section-title section-title--light"><?php aeon_e( 'why_title' ); ?></h2>
		</header>

		<?php $why_items = aeon_section_terms( 'aeon_why' ); ?>
		<div class="why__grid stagger" data-reveal>
			<?php if ( $why_items ) : ?>
				<?php foreach ( $why_items as $why ) : ?>
					<?php $why_icon = get_term_meta( $why->term_id, '_aeon_icon', true ); ?>
					<div class="why-card">
						<span class="why-card__icon"><?php echo aeon_icon( $why_icon ? $why_icon : 'check' ); ?></span>
						<h3 class="why-card__title"><?php echo esc_html( $why->name ); ?></h3>
						<p class="why-card__desc"><?php echo esc_html( $why->description ); ?></p>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<?php foreach ( aeon_why_items() as $item ) : ?>
					<div class="why-card">
						<span class="why-card__icon"><?php echo aeon_icon( $item['icon'] ); ?></span>
						<h3 class="why-card__title"><?php aeon_e( $item['title'] ); ?></h3>
						<p class="why-card__desc"><?php aeon_e( $item['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
