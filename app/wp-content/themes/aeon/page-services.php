<?php
/**
 * Template Name: Services Page
 *
 * A detailed page: one rich, deep-linkable block per service (intro, key
 * highlights, what's included, CTA). Homepage "View Details" buttons link to
 * each block via #service-<slug>.
 *
 * Every word and image on this page is dashboard-managed. Each service's name,
 * description, intro, highlights and "what's included" are term fields on
 * محتوى الموقع → الخدمات; its work samples and the page's own headings live
 * under صفحة الخدمات.
 *
 * Each block reads title → narrative → specs → real work. It carries no
 * decorative artwork: the work-samples strip is the section's imagery, and the
 * client's own photographs earn the space a placeholder used to take.
 *
 * @package AEON
 */
get_header();
get_template_part( 'template-parts/components/page-banner', null, array(
	'title'    => aeon_svc_text( 'aeon_svc_page_title' ),
	'subtitle' => aeon_svc_text( 'aeon_svc_page_sub' ),
) );

$services = aeon_services_list();
?>
<div class="svc-details">
	<?php foreach ( $services as $i => $svc ) : ?>
		<?php
		$slug      = $svc['slug'];
		$features  = $svc['features'];
		$includes  = $svc['includes'];
		$gallery   = $slug ? aeon_service_gallery( $slug ) : array( 'shots' => array() );
		$anchor    = $slug ? 'service-' . $slug : 'service-' . ( $i + 1 );
		// A service whose specs have not been filled in has nothing to show beside
		// its narrative, which then runs as a single measured column rather than
		// leaving an empty track.
		$has_specs = $features || $includes;
		// Every other block is tinted. That alternation is what keeps eight
		// stacked sections from reading as one wall — the job the alternating
		// gradient tile used to do, minus the tile.
		$alt       = ( $i % 2 === 1 ) ? ' svc-detail--alt' : '';
		?>
		<section class="svc-detail<?php echo esc_attr( $alt ); ?>" id="<?php echo esc_attr( $anchor ); ?>">
			<div class="container">

				<header class="svc-detail__head" data-reveal>
					<p class="eyebrow"><?php aeon_svc_text_e( 'aeon_svc_kicker' ); ?> <?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></p>
					<h2 class="section-title"><?php echo esc_html( $svc['name'] ); ?></h2>
				</header>

				<div class="svc-detail__inner<?php echo $has_specs ? '' : ' svc-detail__inner--solo'; ?>">

					<div class="svc-detail__lead" data-reveal>
						<p class="svc-detail__intro"><?php echo esc_html( $svc['intro'] ? $svc['intro'] : $svc['desc'] ); ?></p>

						<?php // Straight into WhatsApp with this service already named in the message. ?>
						<a class="btn btn--primary" href="<?php echo esc_url( aeon_service_whatsapp_url( $svc['name'] ) ); ?>"
							target="_blank" rel="noopener">
							<span><?php aeon_svc_text_e( 'aeon_svc_cta' ); ?></span>
							<?php echo aeon_icon( 'arrow' ); ?>
						</a>
					</div>

					<?php if ( $has_specs ) : ?>
						<div class="svc-detail__specs" data-reveal>
							<?php if ( $features ) : ?>
								<h3 class="svc-detail__subhead"><?php aeon_svc_text_e( 'aeon_svc_highlights_t' ); ?></h3>
								<ul class="svc-detail__chips">
									<?php foreach ( $features as $feat ) : ?>
										<li><?php echo aeon_icon( 'check' ); ?><span><?php echo esc_html( $feat ); ?></span></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<?php if ( $includes ) : ?>
								<h3 class="svc-detail__subhead"><?php aeon_svc_text_e( 'aeon_svc_includes_t' ); ?></h3>
								<ul class="svc-detail__list">
									<?php foreach ( $includes as $inc ) : ?>
										<li><?php echo aeon_icon( 'check' ); ?><span><?php echo esc_html( $inc ); ?></span></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				</div>

				<?php if ( ! empty( $gallery['shots'] ) ) : ?>
					<div class="svc-work">
						<h3 class="svc-detail__subhead svc-work__head" data-reveal><?php aeon_svc_text_e( 'aeon_svc_work_t' ); ?></h3>
						<div class="svc-work__grid stagger" data-reveal
							style="--svc-shot-ratio: <?php echo esc_attr( $gallery['ratio'] ); ?>; --svc-shot-cols: <?php echo (int) $gallery['cols']; ?>;">
							<?php foreach ( $gallery['shots'] as $shot ) : ?>
								<?php if ( 'video' === $gallery['kind'] ) : ?>
									<?php
									// Reels play in place. preload="metadata" fetches just enough
									// for the first frame and duration, so a strip of five costs
									// nothing until one is actually played; playsinline keeps iOS
									// from hijacking it into fullscreen.
									?>
									<figure class="svc-work__shot svc-work__shot--video">
										<video controls playsinline preload="metadata"
											<?php if ( $shot['poster'] ) : ?>poster="<?php echo esc_url( $shot['poster'] ); ?>"<?php endif; ?>
											aria-label="<?php echo esc_attr( $shot['alt'] ); ?>">
											<source src="<?php echo esc_url( $shot['url'] ); ?>"
												<?php if ( $shot['mime'] ) : ?>type="<?php echo esc_attr( $shot['mime'] ); ?>"<?php endif; ?>>
										</video>
									</figure>
								<?php else : ?>
									<figure class="svc-work__shot">
										<img src="<?php echo esc_url( $shot['url'] ); ?>"
											alt="<?php echo esc_attr( $shot['alt'] ); ?>"
											loading="lazy" decoding="async">
									</figure>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</section>
	<?php endforeach; ?>
</div>

<?php
get_template_part( 'template-parts/sections/why' );
get_footer();
