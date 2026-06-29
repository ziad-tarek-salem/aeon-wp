<?php
/**
 * Testimonials slider (Swiper). Falls back to brand sample quotes.
 *
 * @package AEON
 */
$tst_q = new WP_Query( array( 'post_type' => 'testimonial', 'posts_per_page' => 9 ) );

$items = array();
if ( $tst_q->have_posts() ) {
	while ( $tst_q->have_posts() ) {
		$tst_q->the_post();
		$items[] = array(
			'quote' => get_the_content(),
			'name'  => get_the_title(),
			'role'  => get_post_meta( get_the_ID(), '_aeon_role', true ),
			'img'   => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ),
		);
	}
	wp_reset_postdata();
} else {
	$ar = aeon_is_rtl();
	$items = array(
		array( 'quote' => $ar ? 'فريق AEON نقل علامتنا التجارية إلى مستوى آخر. احترافية ونتائج فاقت توقعاتنا.' : 'The AEON team took our brand to another level. Professionalism and results beyond our expectations.', 'name' => $ar ? 'سارة المنصوري' : 'Sara Al Mansoori', 'role' => $ar ? 'مديرة تسويق، نوفا' : 'Marketing Director, NOVA', 'img' => '' ),
		array( 'quote' => $ar ? 'أفضل قرار اتخذناه هو الشراكة مع AEON. نمو حقيقي في المبيعات خلال أشهر.' : 'Partnering with AEON was the best decision we made. Real sales growth within months.', 'name' => $ar ? 'خالد العامري' : 'Khalid Al Amri', 'role' => $ar ? 'مؤسس، أويسيس جروب' : 'Founder, Oasis Group', 'img' => '' ),
		array( 'quote' => $ar ? 'إبداع لا حدود له وفريق يفهم احتياجاتنا تماماً. ننصح بهم بشدة.' : 'Boundless creativity and a team that truly understands our needs. Highly recommended.', 'name' => $ar ? 'ليلى حسن' : 'Layla Hassan', 'role' => $ar ? 'الرئيس التنفيذي، بيكسل لاب' : 'CEO, Pixel Lab', 'img' => '' ),
	);
}
?>
<section class="testimonials section" id="testimonials">
	<div class="container">
		<header class="section-head section-head--center" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'tst_eyebrow' ); ?></p>
			<h2 class="section-title"><?php aeon_e( 'tst_title' ); ?></h2>
		</header>
	</div>

	<div class="testimonials__slider swiper" data-reveal data-swiper="testimonials">
		<div class="swiper-wrapper">
			<?php foreach ( $items as $t ) : ?>
				<div class="swiper-slide">
					<figure class="tst-card">
						<span class="tst-card__quote" aria-hidden="true">&ldquo;</span>
						<blockquote class="tst-card__text"><?php echo esc_html( wp_strip_all_tags( $t['quote'] ) ); ?></blockquote>
						<figcaption class="tst-card__author">
							<?php if ( ! empty( $t['img'] ) ) : ?>
								<img src="<?php echo esc_url( $t['img'] ); ?>" alt="<?php echo esc_attr( $t['name'] ); ?>" class="tst-card__avatar">
							<?php else : ?>
								<span class="tst-card__avatar tst-card__avatar--ph"><?php echo esc_html( aeon_first_char( $t['name'] ) ); ?></span>
							<?php endif; ?>
							<span class="tst-card__meta">
								<strong><?php echo esc_html( $t['name'] ); ?></strong>
								<small><?php echo esc_html( $t['role'] ); ?></small>
							</span>
						</figcaption>
					</figure>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="swiper-pagination"></div>
	</div>
</section>
