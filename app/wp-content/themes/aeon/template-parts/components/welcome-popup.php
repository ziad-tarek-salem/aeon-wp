<?php
/**
 * Welcome popup.
 *
 * A greeting dialog that shows itself on the home page and carries a single
 * message: talk to us on WhatsApp. Deliberately holds nothing but the lockup
 * and the button — no headline, no blurb — so the call to action is the only
 * thing to read.
 *
 * Home only: footer.php renders this behind an is_front_page() check, so it
 * never greets someone who is already deeper in the site.
 *
 * The only dialog on the site: nothing opens it on click. The floating button
 * in footer.php is a plain link to the same bare wa.me URL this button uses, so
 * both go straight to WhatsApp's own chat page for the number — no JS in either
 * path. The number comes from the dashboard setting, so the popup simply does
 * not render until one is configured.
 *
 * @package AEON
 */

$aeon_welcome_wa = aeon_whatsapp_number();

if ( ! $aeon_welcome_wa ) {
	return;
}
?>

<div class="welcome-modal" data-welcome-modal hidden>
	<div class="welcome-modal__backdrop" data-welcome-close></div>

	<div class="welcome-card" role="dialog" aria-modal="true"
		aria-label="<?php echo esc_attr( aeon_t( 'welcome_aria' ) ); ?>">

		<button type="button" class="welcome-card__close" data-welcome-close
			aria-label="<?php echo esc_attr( aeon_t( 'wa_close' ) ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>

		<?php // Soft brand bloom behind the lockup — keeps the white card from reading empty. ?>
		<span class="welcome-card__bloom" aria-hidden="true"></span>

		<img class="welcome-card__logo" src="<?php echo esc_url( aeon_image_url( 'logo_lockup', 'full' ) ); ?>"
			alt="<?php bloginfo( 'name' ); ?>" width="816" height="294" decoding="async">

		<?php // Real wa.me link, so the button still works with JS disabled. ?>
		<a class="welcome-cta" href="https://wa.me/<?php echo esc_attr( $aeon_welcome_wa ); ?>"
			target="_blank" rel="noopener" data-welcome-link>
			<span class="welcome-cta__badge" aria-hidden="true">
				<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15l-1.4 5 5.1-1.3A10 10 0 1 0 12 2zm0 18a8 8 0 0 1-4.1-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8 8 0 1 1 12 20zm4.4-6c-.2-.1-1.4-.7-1.7-.8s-.4-.1-.6.1-.6.8-.8 1-.3.2-.5.1a6.5 6.5 0 0 1-1.9-1.2 7.2 7.2 0 0 1-1.4-1.7c-.1-.2 0-.4.1-.5l.4-.4.2-.4a.5.5 0 0 0 0-.4l-.8-1.9c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.9 11.9 0 0 0 4.6 4c.6.3 1.1.4 1.5.6.6.2 1.2.2 1.6.1.5-.1 1.4-.6 1.6-1.1s.2-1 .2-1.1z"/></svg>
			</span>
			<span class="welcome-cta__label"><?php aeon_e( 'wa_cta' ); ?></span>
			<svg class="welcome-cta__arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
		</a>
	</div>
</div>
