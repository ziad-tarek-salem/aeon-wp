<?php
/**
 * Reusable inner-page banner.
 *
 * @param array $args { title, subtitle }
 * @package AEON
 */
$title    = isset( $args['title'] ) ? $args['title'] : get_the_title();
$subtitle = isset( $args['subtitle'] ) ? $args['subtitle'] : '';
?>
<section class="page-banner">
	<div class="page-banner__bg" aria-hidden="true">
		<span class="hero__orb hero__orb--1"></span>
		<span class="dot-grid dot-grid--banner"></span>
	</div>
	<div class="container page-banner__inner">
		<nav class="breadcrumb" aria-label="Breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php aeon_e( 'nav_home' ); ?></a>
			<span aria-hidden="true">/</span>
			<span class="breadcrumb__current"><?php echo esc_html( $title ); ?></span>
		</nav>
		<h1 class="page-banner__title"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $subtitle ) : ?>
			<p class="page-banner__sub"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>
	</div>
</section>
