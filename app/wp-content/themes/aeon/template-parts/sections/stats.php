<?php
/**
 * Animated stats counters.
 *
 * @package AEON
 */
// Prefer dashboard-managed stats (aeon_statistic terms); else the brand defaults.
$stats      = array();
$stat_terms = aeon_section_terms( 'aeon_statistic' );
if ( $stat_terms ) {
	foreach ( $stat_terms as $stat ) {
		$stats[] = array(
			'val'    => get_term_meta( $stat->term_id, '_aeon_number', true ),
			'suffix' => get_term_meta( $stat->term_id, '_aeon_suffix', true ),
			'label'  => $stat->name,
		);
	}
} else {
	$stats = array(
		array( 'val' => aeon_opt( 'aeon_stat_projects', '200' ),     'suffix' => '+', 'label' => aeon_t( 'stat_projects' ) ),
		array( 'val' => aeon_opt( 'aeon_stat_clients', '150' ),      'suffix' => '+', 'label' => aeon_t( 'stat_clients' ) ),
		array( 'val' => aeon_opt( 'aeon_stat_growth', '300' ),       'suffix' => '%', 'label' => aeon_t( 'stat_growth' ) ),
		array( 'val' => aeon_opt( 'aeon_stat_satisfaction', '98' ),  'suffix' => '%', 'label' => aeon_t( 'stat_satisfaction' ) ),
	);
}
?>
<section class="stats" data-reveal>
	<div class="container stats__grid">
		<?php foreach ( $stats as $s ) : ?>
			<div class="stat">
				<span class="stat__num" data-count="<?php echo esc_attr( $s['val'] ); ?>" data-suffix="<?php echo esc_attr( $s['suffix'] ); ?>">0<?php echo esc_html( $s['suffix'] ); ?></span>
				<span class="stat__label"><?php echo esc_html( $s['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
