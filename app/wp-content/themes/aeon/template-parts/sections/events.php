<?php
/**
 * Events & Participation — "مشاركات وفاعليات" (company profile p5).
 * Dashboard-managed via the `aeon_event` taxonomy (name = caption, optional
 * image and icon), falling back to the profile captions paired with the
 * extracted photos.
 *
 * @package AEON
 */
$event_terms = aeon_section_terms( 'aeon_event' );

// Photo and badge per caption position, matching the client's reference strip:
//   1 conferences  → presenting the roll-up banner   → speaker at a lectern
//   2 workshops    → talking beside the stand        → group of three
//   3 advice       → speaking at the podium          → lightbulb with rays
//   4 panel talks  → the group shot                  → speech bubbles
//   5 partnerships → the handshake                   → clasped hands
// A term's own image or icon always wins over these defaults.
$event_icons = array( 'podium', 'users', 'idea', 'comments', 'handshake' );
$event_photos = array( 'event-2.jpg', 'event-4.jpg', 'event-5.jpg', 'event-1.jpg', 'event-3.jpg' );

// Normalise into { caption, img, icon } rows. Photos come from three places, in
// order: the event term's own image, then the dashboard gallery
// (محتوى الموقع → صور الموقع → صور المشاركات), then the bundled profile crops.
$event_rows = array();
$fallback   = array();
foreach ( aeon_image_gallery( 'events', 'large' ) as $img ) {
	$fallback[] = $img['url'];
}
if ( ! $fallback ) {
	foreach ( $event_photos as $file ) {
		if ( file_exists( AEON_DIR . '/assets/images/profile/events/' . $file ) ) {
			$fallback[] = aeon_img( 'profile/events/' . $file );
		}
	}
}

if ( $event_terms ) {
	$i = 0;
	foreach ( $event_terms as $term ) {
		$img_id = (int) get_term_meta( $term->term_id, '_aeon_image', true );
		$img    = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : '';
		if ( ! $img && $fallback ) {
			$img = $fallback[ $i % count( $fallback ) ];
		}
		$event_rows[] = array_merge(
			array( 'caption' => $term->name, 'img' => $img ),
			aeon_term_icon( $term->term_id, $event_icons[ $i % count( $event_icons ) ] )
		);
		$i++;
	}
} else {
	$keys = aeon_event_items();
	foreach ( $keys as $i => $key ) {
		$event_rows[] = array(
			'caption' => aeon_t( $key ),
			'img'     => ( $fallback ) ? $fallback[ $i % count( $fallback ) ] : '',
			'icon'    => $event_icons[ $i % count( $event_icons ) ],
			'icon_id' => 0,
			'icon_url' => '',
		);
	}
}
?>
<section class="events section" id="events">
	<?php aeon_soft_bg(); ?>
	<div class="container">
		<header class="section-head section-head--center" data-reveal>
			<p class="eyebrow"><?php aeon_e( 'events_eyebrow' ); ?></p>
			<?php aeon_rule_heading( esc_html( aeon_t( 'events_title' ) ) ); ?>
		</header>

		<div class="events__grid stagger" data-reveal>
			<?php foreach ( $event_rows as $row ) : ?>
				<figure class="event-card">
					<?php if ( $row['img'] ) : ?>
						<div class="event-card__media">
							<img src="<?php echo esc_url( $row['img'] ); ?>" alt="<?php echo esc_attr( $row['caption'] ); ?>" loading="lazy">
						</div>
					<?php endif; ?>
					<figcaption class="event-card__caption">
						<span class="event-card__icon"><?php echo aeon_card_icon( $row ); ?></span>
						<span class="event-card__text"><?php echo esc_html( $row['caption'] ); ?></span>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
