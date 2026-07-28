<?php
/**
 * Message & Values — "رسالتنا" (company profile p4).
 * Mission paragraphs and the three brand values. Dashboard-managed via the
 * `aeon_value` taxonomy, falling back to the profile defaults.
 *
 * @package AEON
 */
$value_terms = aeon_section_terms( 'aeon_value' );
?>
<section class="values section section--dark" id="values">
	<?php aeon_soft_bg(); ?>
	<div class="container">

		<header class="section-head section-head--center" data-reveal>
			<p class="eyebrow eyebrow--light"><?php aeon_e( 'values_eyebrow' ); ?></p>
			<h2 class="section-title section-title--light"><?php aeon_e( 'values_title' ); ?></h2>
		</header>

		<div class="values__lead" data-reveal>
			<p><?php aeon_e( 'values_lead_1' ); ?></p>
			<p><?php aeon_e( 'values_lead_2' ); ?></p>
		</div>

		<p class="values__believe" data-reveal><span><?php aeon_e( 'values_believe_t' ); ?></span></p>

		<div class="values__grid stagger" data-reveal>
			<?php if ( $value_terms ) : ?>
				<?php foreach ( $value_terms as $val ) : ?>
					<?php $v_icon = get_term_meta( $val->term_id, '_aeon_icon', true ); ?>
					<div class="value-card">
						<span class="value-card__icon"><?php echo aeon_icon( $v_icon ? $v_icon : 'target' ); ?></span>
						<h3 class="value-card__title"><?php echo esc_html( $val->name ); ?></h3>
						<p class="value-card__desc"><?php echo esc_html( $val->description ); ?></p>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<?php foreach ( aeon_value_items() as $item ) : ?>
					<div class="value-card">
						<span class="value-card__icon"><?php echo aeon_icon( $item['icon'] ); ?></span>
						<h3 class="value-card__title"><?php aeon_e( $item['title'] ); ?></h3>
						<p class="value-card__desc"><?php aeon_e( $item['desc'] ); ?></p>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<p class="values__closer" data-reveal>
			<span class="values__solutions"><?php aeon_e( 'values_solutions' ); ?></span>
			<span class="values__circle"><?php aeon_e( 'values_circle' ); ?></span>
		</p>

	</div>
</section>
