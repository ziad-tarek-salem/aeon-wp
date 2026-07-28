<?php
/**
 * "Industries We Serve" — icon grid of the sectors the company works with,
 * drawn from the company profile. Static (bilingual via aeon_industries()).
 *
 * @package AEON
 */
$industries = aeon_industries();
$lang       = aeon_lang();
?>
<section class="industries section" id="industries">
	<?php aeon_soft_bg(); ?>
	<div class="container">
		<header class="section-head section-head--center" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'industries_eyebrow' ); ?></p>
			<?php aeon_rule_heading( esc_html( aeon_t( 'industries_title' ) ) ); ?>
			<p class="section-sub"><?php aeon_e( 'industries_sub' ); ?></p>
		</header>

		<div class="industries__grid stagger" data-reveal>
			<?php foreach ( $industries as $ind ) : ?>
				<div class="industry-card">
					<span class="industry-card__icon"><?php echo aeon_icon( $ind['icon'] ); ?></span>
					<h3 class="industry-card__title"><?php echo esc_html( 'ar' === $lang ? $ind['ar'] : $ind['en'] ); ?></h3>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
