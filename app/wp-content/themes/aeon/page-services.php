<?php
/**
 * Template Name: Services Page
 *
 * A detailed page: one rich, deep-linkable block per service (intro, key
 * highlights, what's included, CTA). Homepage "View Details" buttons link to
 * each block via #service-<slug>.
 *
 * @package AEON
 */
get_header();
get_template_part( 'template-parts/components/page-banner', null, array(
	'title'    => aeon_t( 'services_title' ),
	'subtitle' => aeon_t( 'services_sub' ),
) );

$services  = aeon_services_list();
$grads     = array( 'a', 'b', 'c' );
?>
<div class="svc-details">
	<?php foreach ( $services as $i => $svc ) : ?>
		<?php
		$slug     = $svc['slug'];
		$details  = $slug ? aeon_service_details( $slug ) : array( 'intro' => '', 'includes' => array() );
		$features = $slug ? aeon_service_features( $slug ) : array();
		$anchor   = $slug ? 'service-' . $slug : 'service-' . ( $i + 1 );
		$grad     = $grads[ $i % 3 ];
		$rev      = ( $i % 2 === 1 ) ? ' svc-detail--rev' : '';
		?>
		<section class="svc-detail<?php echo esc_attr( $rev ); ?>" id="<?php echo esc_attr( $anchor ); ?>">
			<div class="container svc-detail__inner">

				<div class="svc-detail__visual svc-detail__visual--<?php echo esc_attr( $grad ); ?>" data-reveal aria-hidden="true">
					<span class="svc-detail__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
					<span class="svc-detail__icon"><?php echo aeon_card_icon( $svc ); ?></span>
				</div>

				<div class="svc-detail__body" data-reveal>
					<p class="eyebrow"><?php aeon_e( 'svc_detail_kicker' ); ?> <?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></p>
					<h2 class="section-title"><?php echo esc_html( $svc['name'] ); ?></h2>
					<p class="svc-detail__intro"><?php echo esc_html( $details['intro'] ? $details['intro'] : $svc['desc'] ); ?></p>

					<?php if ( $features ) : ?>
						<h3 class="svc-detail__subhead"><?php aeon_e( 'svc_highlights_t' ); ?></h3>
						<ul class="svc-detail__chips">
							<?php foreach ( $features as $feat ) : ?>
								<li><?php echo aeon_icon( 'check' ); ?><span><?php echo esc_html( $feat ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( ! empty( $details['includes'] ) ) : ?>
						<h3 class="svc-detail__subhead"><?php aeon_e( 'svc_includes_t' ); ?></h3>
						<ul class="svc-detail__list">
							<?php foreach ( $details['includes'] as $inc ) : ?>
								<li><?php echo aeon_icon( 'check' ); ?><span><?php echo esc_html( $inc ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<a class="btn btn--primary" href="<?php echo esc_url( aeon_contact_url() ); ?>">
						<span><?php aeon_e( 'svc_cta' ); ?></span>
						<?php echo aeon_icon( 'arrow' ); ?>
					</a>
				</div>

			</div>
		</section>
	<?php endforeach; ?>
</div>

<?php
get_template_part( 'template-parts/sections/why' );
get_footer();
