<?php
/**
 * Contact section with AJAX form.
 *
 * @package AEON
 */
?>
<section class="contact section" id="contact">
	<?php aeon_soft_bg(); ?>
	<div class="container contact__inner">

		<div class="contact__intro" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'contact_eyebrow' ); ?></p>
			<h2 class="section-title"><?php aeon_e( 'contact_title' ); ?></h2>
			<p class="section-sub"><?php aeon_e( 'contact_sub' ); ?></p>

			<?php
			// Phone numbers live in the footer now — see footer.php. The list is
			// skipped entirely when neither field is filled in, so an empty <ul>
			// never leaves a gap under the intro.
			if ( aeon_opt( 'aeon_email' ) || aeon_opt( 'aeon_address' ) ) :
				?>
			<ul class="contact__details">
				<?php if ( aeon_opt( 'aeon_email' ) ) : ?>
					<li class="contact__detail">
						<span class="contact__detail-icon"><?php echo aeon_icon( 'mail' ); ?></span>
						<span class="contact__detail-body">
							<span class="contact__detail-label"><?php aeon_e( 'contact_email_l' ); ?></span>
							<a class="contact__detail-value" href="mailto:<?php echo esc_attr( aeon_opt( 'aeon_email' ) ); ?>"><?php echo esc_html( aeon_opt( 'aeon_email' ) ); ?></a>
						</span>
					</li>
				<?php endif; ?>
				<?php if ( aeon_opt( 'aeon_address' ) ) : ?>
					<li class="contact__detail">
						<span class="contact__detail-icon"><?php echo aeon_icon( 'pin' ); ?></span>
						<span class="contact__detail-body">
							<span class="contact__detail-label"><?php aeon_e( 'contact_addr_l' ); ?></span>
							<span class="contact__detail-value"><?php echo esc_html( aeon_opt( 'aeon_address' ) ); ?></span>
						</span>
					</li>
				<?php endif; ?>
			</ul>
			<?php endif; ?>
		</div>

		<form class="contact-form" data-contact-form data-reveal novalidate>
			<div class="form-row">
				<label class="field">
					<span class="field__label"><?php aeon_e( 'form_name' ); ?> *</span>
					<input type="text" name="name" required>
				</label>
				<label class="field">
					<span class="field__label"><?php aeon_e( 'form_email' ); ?> *</span>
					<input type="email" name="email" required>
				</label>
			</div>
			<div class="form-row">
				<label class="field">
					<span class="field__label"><?php aeon_e( 'form_phone' ); ?></span>
					<input type="tel" name="phone">
				</label>
				<label class="field">
					<span class="field__label"><?php aeon_e( 'form_service' ); ?></span>
					<select name="service">
						<option value=""></option>
						<?php foreach ( aeon_default_services() as $svc ) : ?>
							<option value="<?php echo esc_attr( aeon_t( $svc['title'] ) ); ?>"><?php aeon_e( $svc['title'] ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			</div>
			<label class="field">
				<span class="field__label"><?php aeon_e( 'form_message' ); ?> *</span>
				<textarea name="message" rows="5" required></textarea>
			</label>

			<!-- honeypot: hidden from users, bots fill it -->
			<input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">

			<button type="submit" class="btn btn--primary btn--lg btn--block">
				<span class="btn__label"><?php aeon_e( 'form_send' ); ?></span>
				<?php echo aeon_icon( 'arrow' ); ?>
			</button>
			<p class="form-status" data-form-status role="status" aria-live="polite"></p>
		</form>

	</div>
</section>
