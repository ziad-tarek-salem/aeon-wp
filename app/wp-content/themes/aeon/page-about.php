<?php
/**
 * Template Name: About Page
 *
 * @package AEON
 */
get_header();
get_template_part( 'template-parts/components/page-banner', null, array(
	'title'    => aeon_t( 'about_eyebrow' ),
	'subtitle' => aeon_t( 'about_title' ),
) );
?>

<section class="about about--page section">
	<div class="container about__inner">
		<div class="about__media" data-reveal>
			<div class="about__media-frame">
				<img src="<?php echo esc_url( AEON_URI . '/assets/images/brand-1.jpeg' ); ?>" alt="<?php echo esc_attr( aeon_t( 'about_title' ) ); ?>" loading="lazy">
			</div>
			<span class="dot-grid dot-grid--about" aria-hidden="true"></span>
		</div>
		<div class="about__content" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'about_eyebrow' ); ?></p>
			<h2 class="section-title"><?php aeon_e( 'about_title' ); ?></h2>
			<p class="about__text"><?php aeon_e( 'about_text' ); ?></p>
			<?php
			while ( have_posts() ) :
				the_post();
				if ( trim( get_the_content() ) ) :
					echo '<div class="entry-content">';
					the_content();
					echo '</div>';
				endif;
			endwhile;
			?>
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
		</div>
	</div>
</section>

<?php
get_template_part( 'template-parts/sections/stats' );
get_template_part( 'template-parts/sections/why' );
get_template_part( 'template-parts/sections/testimonials' );
get_footer();
