<?php
/**
 * Footer.
 *
 * @package AEON
 */
$socials = aeon_social_links();
?>
</main><!-- #main -->

<section class="cta-band" data-reveal>
	<div class="container cta-band__inner">
		<div class="cta-band__text">
			<h2 class="cta-band__title"><?php aeon_e( 'cta_band_title' ); ?></h2>
			<p class="cta-band__sub"><?php aeon_e( 'cta_band_sub' ); ?></p>
		</div>
		<a class="btn btn--light btn--lg" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
			<span><?php aeon_e( 'cta_start' ); ?></span>
			<?php echo aeon_icon( 'arrow' ); ?>
		</a>
	</div>
</section>

<footer class="site-footer">
	<div class="container site-footer__grid">

		<div class="footer-col footer-col--brand">
			<img src="<?php echo esc_url( AEON_URI . '/assets/images/logo-wordmark.jpeg' ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="180" height="56" class="footer-logo">
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
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php aeon_e( 'nav_home' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php aeon_e( 'nav_about' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>"><?php aeon_e( 'nav_services' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/work/' ) ); ?>"><?php aeon_e( 'nav_work' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php aeon_e( 'nav_blog' ); ?></a></li>
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
					<li><?php echo aeon_icon( 'arrow' ); ?><a href="mailto:<?php echo esc_attr( aeon_opt( 'aeon_email' ) ); ?>"><?php echo esc_html( aeon_opt( 'aeon_email' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( aeon_opt( 'aeon_phone' ) ) : ?>
					<li><?php echo aeon_icon( 'arrow' ); ?><a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', aeon_opt( 'aeon_phone' ) ) ); ?>"><?php echo esc_html( aeon_opt( 'aeon_phone' ) ); ?></a></li>
				<?php endif; ?>
				<?php if ( aeon_opt( 'aeon_address' ) ) : ?>
					<li><?php echo aeon_icon( 'arrow' ); ?><span><?php echo esc_html( aeon_opt( 'aeon_address' ) ); ?></span></li>
				<?php endif; ?>
			</ul>
			<p class="footer-uae">🇦🇪 <?php aeon_e( 'footer_uae' ); ?></p>
		</div>

	</div>

	<div class="site-footer__bottom">
		<div class="container site-footer__bottom-inner">
			<p>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php aeon_e( 'footer_rights' ); ?></p>
			<p class="footer-tagline"><?php aeon_e( 'site_tagline' ); ?></p>
		</div>
	</div>
</footer>

<?php if ( aeon_opt( 'aeon_whatsapp' ) ) : ?>
	<a class="whatsapp-fab" href="https://wa.me/<?php echo esc_attr( aeon_opt( 'aeon_whatsapp' ) ); ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
		<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15l-1.4 5 5.1-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20zm4.4-6c-.2-.1-1.4-.7-1.7-.8s-.4-.1-.6.1-.6.8-.8 1-.3.2-.5.1a6.5 6.5 0 0 1-1.9-1.2 7.2 7.2 0 0 1-1.4-1.7c-.1-.2 0-.4.1-.5l.4-.4.2-.4a.5.5 0 0 0 0-.4l-.8-1.9c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.9 11.9 0 0 0 4.6 4c.6.3 1.1.4 1.5.6.6.2 1.2.2 1.6.1.5-.1 1.4-.6 1.6-1.1s.2-1 .2-1.1z"/></svg>
	</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
