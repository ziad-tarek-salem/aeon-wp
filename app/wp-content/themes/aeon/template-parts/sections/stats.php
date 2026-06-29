<?php
/**
 * Animated stats counters.
 *
 * @package AEON
 */
$stats = array(
	array( 'val' => aeon_opt( 'aeon_stat_projects', '200' ),     'suffix' => '+', 'label' => 'stat_projects' ),
	array( 'val' => aeon_opt( 'aeon_stat_clients', '150' ),      'suffix' => '+', 'label' => 'stat_clients' ),
	array( 'val' => aeon_opt( 'aeon_stat_growth', '300' ),       'suffix' => '%', 'label' => 'stat_growth' ),
	array( 'val' => aeon_opt( 'aeon_stat_satisfaction', '98' ),  'suffix' => '%', 'label' => 'stat_satisfaction' ),
);
?>
<section class="stats" data-reveal>
	<div class="container stats__grid">
		<?php foreach ( $stats as $s ) : ?>
			<div class="stat">
				<span class="stat__num" data-count="<?php echo esc_attr( $s['val'] ); ?>" data-suffix="<?php echo esc_attr( $s['suffix'] ); ?>">0<?php echo esc_html( $s['suffix'] ); ?></span>
				<span class="stat__label"><?php aeon_e( $s['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
