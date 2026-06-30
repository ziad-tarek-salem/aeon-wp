<?php
/**
 * Hero section.
 *
 * @package AEON
 */
?>
<section class="hero" data-hero>
	<div class="hero__bg" aria-hidden="true">
		<span class="hero__orb hero__orb--1"></span>
		<span class="hero__orb hero__orb--2"></span>
		<span class="hero__orb hero__orb--3"></span>
		<span class="hero__orb hero__orb--4"></span>
		<span class="hero__ring hero__ring--1"></span>
		<span class="hero__ring hero__ring--2"></span>
		<span class="hero__ring hero__ring--3"></span>
		<span class="hero__ring hero__ring--4"></span>
		<span class="hero__ring hero__ring--5"></span>
		<span class="hero__grid"></span>
		<span class="hero__grid hero__grid--2"></span>
		<span class="hero__grid hero__grid--3"></span>
		<span class="hero__grid hero__grid--4"></span>
		<span class="hero__stroke hero__stroke--1"></span>
		<span class="hero__stroke hero__stroke--2"></span>
		<span class="hero__plus hero__plus--1"></span>
		<span class="hero__plus hero__plus--2"></span>
		<span class="hero__plus hero__plus--3"></span>
		<span class="hero__plus hero__plus--4"></span>
		<span class="hero__spark hero__spark--1"></span>
		<span class="hero__spark hero__spark--2"></span>
		<span class="hero__spark hero__spark--3"></span>
		<span class="hero__spark hero__spark--4"></span>
		<span class="hero__spark hero__spark--5"></span>
		<span class="hero__spark hero__spark--6"></span>
		<span class="hero__spark hero__spark--7"></span>
		<span class="hero__spark hero__spark--8"></span>
		<span class="hero__swoosh"></span>
	</div>

	<div class="container hero__inner">
		<p class="hero__eyebrow" data-hero-el><?php aeon_e( 'hero_eyebrow' ); ?></p>

		<h1 class="hero__title">
			<span class="hero__line" data-hero-el><?php aeon_e( 'hero_title_1' ); ?></span>
			<span class="hero__line gradient-text" data-hero-el><?php aeon_e( 'hero_title_2' ); ?></span>
			<span class="hero__line text-orange" data-hero-el><?php aeon_e( 'hero_title_3' ); ?></span>
		</h1>

		<p class="hero__sub" data-hero-el><?php aeon_e( 'hero_sub' ); ?></p>

		<div class="hero__actions" data-hero-el>
			<a class="btn btn--primary btn--lg magnetic" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
				<span><?php aeon_e( 'hero_cta_primary' ); ?></span>
				<?php echo aeon_icon( 'arrow' ); ?>
			</a>
			<a class="btn btn--ghost btn--lg" href="<?php echo esc_url( home_url( '/services/' ) ); ?>">
				<span><?php aeon_e( 'hero_cta_secondary' ); ?></span>
			</a>
		</div>
	</div>

	<a href="#about" class="hero__scroll" aria-hidden="true">
		<span class="hero__scroll-line"></span>
	</a>
</section>
