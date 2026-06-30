<?php
/**
 * Testimonials slider (Swiper). Falls back to brand sample quotes.
 *
 * @package AEON
 */
$review_terms = aeon_section_terms( 'aeon_review' );

$items = array();
if ( $review_terms ) {
	foreach ( $review_terms as $review ) {
		$img_id = (int) get_term_meta( $review->term_id, '_aeon_image', true );
		$items[] = array(
			'quote' => $review->description,
			'name'  => $review->name,
			'role'  => get_term_meta( $review->term_id, '_aeon_role', true ),
			'img'   => $img_id ? wp_get_attachment_image_url( $img_id, 'thumbnail' ) : '',
		);
	}
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

	<div class="testimonials__slider" data-reveal>
		<div class="testimonials__viewport swiper" data-swiper="testimonials">
			<div class="swiper-wrapper">
				<?php foreach ( $items as $t ) : ?>
					<div class="swiper-slide">
						<figure class="tst-card">
							<span class="tst-card__quote" aria-hidden="true">
								<svg viewBox="0 0 32 32" fill="currentColor" aria-hidden="true"><path d="M13 7C8 9 5 13.5 5 19v6h9v-9H9.5C9.7 12 11 9.8 14 8.5L13 7zm15 0c-5 2-8 6.5-8 12v6h9v-9h-4.5c.2-4 1.5-6.2 4.5-7.5L28 7z"/></svg>
							</span>
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
		</div>

		<div class="testimonials__controls">
			<button type="button" class="testimonials__nav testimonials__nav--prev" aria-label="<?php esc_attr_e( 'Previous', 'aeon' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 5l-7 7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<div class="testimonials__pagination swiper-pagination"></div>
			<button type="button" class="testimonials__nav testimonials__nav--next" aria-label="<?php esc_attr_e( 'Next', 'aeon' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
		</div>
	</div>
</section>
