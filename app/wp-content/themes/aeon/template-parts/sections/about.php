<?php
/**
 * About teaser section.
 *
 * @package AEON
 */
?>
<section class="about" id="about">
	<div class="container about__inner">

		<div class="about__media" data-reveal>
			<div class="about__media-frame">
				<img src="<?php echo esc_url( AEON_URI . '/assets/images/why-choose.jpeg' ); ?>" alt="<?php echo esc_attr( aeon_t( 'about_title' ) ); ?>" loading="lazy">
			</div>
			<div class="about__badge">
				<span class="about__badge-num" data-count="5" data-suffix="+">5+</span>
				<span class="about__badge-label"><?php aeon_e( 'stat_years' ); ?></span>
			</div>
			<span class="dot-grid dot-grid--about" aria-hidden="true"></span>
		</div>

		<div class="about__content" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'about_eyebrow' ); ?></p>
			<h2 class="section-title"><?php aeon_e( 'about_title' ); ?></h2>
			<p class="about__text"><?php aeon_e( 'about_text' ); ?></p>

			<div class="about__cards">
				<div class="mini-card">
					<span class="mini-card__icon"><?php echo aeon_icon( 'target' ); ?></span>
					<h3><?php aeon_e( 'about_mission_t' ); ?></h3>
					<p><?php aeon_e( 'about_mission' ); ?></p>
				</div>
				<div class="mini-card">
					<span class="mini-card__icon"><?php echo aeon_icon( 'bulb' ); ?></span>
					<h3><?php aeon_e( 'about_vision_t' ); ?></h3>
					<p><?php aeon_e( 'about_vision' ); ?></p>
				</div>
			</div>

			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">
				<span><?php aeon_e( 'about_more' ); ?></span>
				<?php echo aeon_icon( 'arrow' ); ?>
			</a>
		</div>

	</div>
</section>
