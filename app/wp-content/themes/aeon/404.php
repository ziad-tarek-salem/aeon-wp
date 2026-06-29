<?php
/**
 * 404 template.
 *
 * @package AEON
 */
get_header();
?>
<section class="error-404 section">
	<div class="container narrow error-404__inner" data-reveal>
		<p class="error-404__code gradient-text">404</p>
		<h1 class="error-404__title"><?php aeon_e( 'e404_title' ); ?></h1>
		<p class="error-404__text"><?php aeon_e( 'e404_text' ); ?></p>
		<a class="btn btn--primary btn--lg" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span><?php aeon_e( 'back_home' ); ?></span><?php echo aeon_icon( 'arrow' ); ?>
		</a>
	</div>
</section>
<?php
get_footer();
