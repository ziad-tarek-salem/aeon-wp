<?php
/**
 * Animated stats counters (About page). The home page renders the same figures
 * inside the About section instead — see template-parts/sections/about.php.
 *
 * @package AEON
 */
?>
<section class="stats" data-reveal>
	<div class="container stats__grid">
		<?php foreach ( aeon_stats_list() as $s ) : ?>
			<div class="stat">
				<span class="stat__icon"><?php echo aeon_card_icon( $s ); ?></span>
				<span class="stat__num" data-count="<?php echo esc_attr( $s['val'] ); ?>" data-suffix="<?php echo esc_attr( $s['suffix'] ); ?>">0<?php echo esc_html( $s['suffix'] ); ?></span>
				<span class="stat__label"><?php echo esc_html( $s['name'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
