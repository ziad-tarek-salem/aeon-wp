<?php
/**
 * Footer.
 *
 * Light, on-brand and built from the same parts as the rest of the page — the
 * soft orb backdrop, translucent cards and gradient accents used by the About,
 * Events and Contact sections — so it reads as the last section of the site
 * rather than a separate dark slab.
 *
 * @package AEON
 */
$socials      = aeon_social_links();
$aeon_numbers = aeon_phone_numbers();
?>
</main><!-- #main -->

<footer class="site-footer">
	<?php aeon_soft_bg(); ?>

	<div class="container site-footer__inner">

		<div class="site-footer__grid">

			<div class="footer-col footer-col--brand">
				<img src="<?php echo esc_url( aeon_image_url( 'logo_lockup', 'full' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="816" height="294" class="footer-logo" loading="lazy">
				<p class="footer-about"><?php aeon_e( 'footer_about' ); ?></p>
				<?php if ( $socials ) : ?>
					<ul class="social-list">
						<?php foreach ( $socials as $s ) : ?>
							<li><a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $s['label'] ); ?>"><?php echo aeon_social_icon( $s['icon'] ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="footer-col">
				<h4 class="footer-col__title"><?php aeon_e( 'footer_links' ); ?></h4>
				<ul class="footer-links">
					<?php // One-pager — see aeon_single_page_mode() in inc/template-functions.php. ?>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php aeon_e( 'nav_home' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#about' ) ); ?>"><?php aeon_e( 'nav_about' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php aeon_e( 'nav_services' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>"><?php aeon_e( 'nav_contact' ); ?></a></li>
				</ul>
			</div>

			<div class="footer-col">
				<h4 class="footer-col__title"><?php aeon_e( 'footer_services' ); ?></h4>
				<ul class="footer-links">
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php aeon_e( 'svc_marketing_t' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php aeon_e( 'svc_social_t' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php aeon_e( 'svc_brand_t' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php aeon_e( 'svc_web_t' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php aeon_e( 'svc_video_t' ); ?></a></li>
				</ul>
			</div>

			<div class="footer-col footer-col--contact">
				<h4 class="footer-col__title"><?php aeon_e( 'footer_contact' ); ?></h4>
				<ul class="footer-contact">
					<?php if ( aeon_opt( 'aeon_email' ) ) : ?>
						<li class="footer-contact__item">
							<span class="footer-contact__icon"><?php echo aeon_icon( 'mail' ); ?></span>
							<a href="mailto:<?php echo esc_attr( aeon_opt( 'aeon_email' ) ); ?>"><?php echo esc_html( aeon_opt( 'aeon_email' ) ); ?></a>
						</li>
					<?php endif; ?>
					<?php
					// Every line the agency answers on — moved here from the contact
					// section so the form column stays uncluttered.
					if ( $aeon_numbers ) :
						?>
						<li class="footer-contact__item">
							<span class="footer-contact__icon"><?php echo aeon_icon( 'phone' ); ?></span>
							<span class="footer-phones">
								<?php foreach ( $aeon_numbers as $aeon_number ) : ?>
									<a class="footer-phone" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $aeon_number ) ); ?>" dir="ltr"><?php echo esc_html( $aeon_number ); ?></a>
								<?php endforeach; ?>
							</span>
						</li>
					<?php endif; ?>
					<?php if ( aeon_opt( 'aeon_address' ) ) : ?>
						<li class="footer-contact__item">
							<span class="footer-contact__icon"><?php echo aeon_icon( 'pin' ); ?></span>
							<span><?php echo esc_html( aeon_opt( 'aeon_address' ) ); ?></span>
						</li>
					<?php endif; ?>
				</ul>
				<p class="footer-uae">🇦🇪 <?php aeon_e( 'footer_uae' ); ?></p>
			</div>

		</div>

		<?php get_template_part( 'template-parts/components/footer-locations' ); ?>

		<div class="site-footer__bottom">
			<div class="site-footer__bottom-inner">
				<p>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php aeon_e( 'footer_rights' ); ?></p>
				<p class="footer-tagline"><?php aeon_e( 'site_tagline' ); ?></p>
			</div>
		</div>

	</div>
</footer>

<?php
// Number is managed from the dashboard (محتوى الموقع → زر الواتساب). The button
// only renders once a number is configured.
$aeon_wa = aeon_whatsapp_number();
if ( $aeon_wa ) : ?>

	<?php
	// The dialog is a root-level modal, not a panel tethered to the button: its
	// backdrop has to cover the whole viewport, which it could not do from
	// inside the button's fixed bottom-corner box.
	?>
	<div class="wa-modal" data-wa-modal hidden>
		<div class="wa-modal__backdrop" data-wa-close></div>

		<div class="wa-popup" id="aeon-wa-popup" role="dialog" aria-modal="true"
			aria-labelledby="aeon-wa-title" aria-describedby="aeon-wa-sub">

			<div class="wa-popup__head">
				<button type="button" class="wa-popup__close" data-wa-close aria-label="<?php echo esc_attr( aeon_t( 'wa_close' ) ); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
				</button>
				<img class="wa-popup__logo" src="<?php echo esc_url( aeon_image_url( 'logo_lockup', 'full' ) ); ?>"
					alt="<?php bloginfo( 'name' ); ?>" width="816" height="294" loading="lazy" decoding="async">
				<p class="wa-popup__status"><span class="wa-popup__dot" aria-hidden="true"></span><?php aeon_e( 'wa_online' ); ?></p>
			</div>

			<div class="wa-popup__body">
				<h2 class="wa-popup__title" id="aeon-wa-title"><?php aeon_e( 'wa_title' ); ?></h2>
				<p class="wa-popup__sub" id="aeon-wa-sub"><?php aeon_e( 'wa_sub' ); ?></p>

				<a class="wa-cta" href="https://wa.me/<?php echo esc_attr( $aeon_wa ); ?>"
					target="_blank" rel="noopener" data-wa-link>
					<svg class="wa-cta__icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15l-1.4 5 5.1-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20zm4.4-6c-.2-.1-1.4-.7-1.7-.8s-.4-.1-.6.1-.6.8-.8 1-.3.2-.5.1a6.5 6.5 0 0 1-1.9-1.2 7.2 7.2 0 0 1-1.4-1.7c-.1-.2 0-.4.1-.5l.4-.4.2-.4a.5.5 0 0 0 0-.4l-.8-1.9c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.9 11.9 0 0 0 4.6 4c.6.3 1.1.4 1.5.6.6.2 1.2.2 1.6.1.5-.1 1.4-.6 1.6-1.1s.2-1 .2-1.1z"/></svg>
					<span><?php aeon_e( 'wa_cta' ); ?></span>
				</a>
			</div>
		</div>
	</div>

	<?php // Stays a real link, so the button still works with JS disabled. ?>
	<a class="whatsapp-fab" href="https://wa.me/<?php echo esc_attr( $aeon_wa ); ?>"
		target="_blank" rel="noopener" aria-label="WhatsApp"
		aria-expanded="false" aria-controls="aeon-wa-popup" data-wa-toggle>
		<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15l-1.4 5 5.1-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20zm4.4-6c-.2-.1-1.4-.7-1.7-.8s-.4-.1-.6.1-.6.8-.8 1-.3.2-.5.1a6.5 6.5 0 0 1-1.9-1.2 7.2 7.2 0 0 1-1.4-1.7c-.1-.2 0-.4.1-.5l.4-.4.2-.4a.5.5 0 0 0 0-.4l-.8-1.9c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.9 11.9 0 0 0 4.6 4c.6.3 1.1.4 1.5.6.6.2 1.2.2 1.6.1.5-.1 1.4-.6 1.6-1.1s.2-1 .2-1.1z"/></svg>
	</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
