<?php
/**
 * Footer branch locations. Managed from the dashboard
 * (محتوى الموقع → الفروع والمواقع). Renders nothing until a branch is added.
 *
 * @package AEON
 */

$aeon_branches = aeon_branch_locations();
if ( ! $aeon_branches ) {
	return;
}
?>
<section class="footer-locations" aria-label="<?php echo esc_attr( aeon_t( 'footer_locations' ) ); ?>">
	<div class="container">
		<h4 class="footer-locations__title"><?php echo aeon_icon( 'pin' ); ?><span><?php aeon_e( 'footer_locations' ); ?></span></h4>
		<ul class="footer-locations__grid">
			<?php foreach ( $aeon_branches as $branch ) : ?>
				<li class="loc-card">
					<div class="loc-card__text">
						<?php if ( $branch['name'] ) : ?>
							<strong class="loc-card__name"><?php echo esc_html( $branch['name'] ); ?></strong>
						<?php endif; ?>
						<?php if ( $branch['desc'] ) : ?>
							<p class="loc-card__desc"><?php echo esc_html( $branch['desc'] ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( $branch['maps_url'] ) : ?>
						<a class="loc-card__pin"
							href="<?php echo esc_url( $branch['maps_url'] ); ?>"
							target="_blank" rel="noopener"
							title="<?php echo esc_attr( aeon_t( 'open_in_maps' ) ); ?>"
							aria-label="<?php echo esc_attr( trim( aeon_t( 'open_in_maps' ) . ' — ' . $branch['name'] ) ); ?>">
							<?php echo aeon_icon( 'pin' ); ?>
						</a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
