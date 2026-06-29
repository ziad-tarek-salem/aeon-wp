<?php
/**
 * Comments template.
 *
 * @package AEON
 */
if ( post_password_required() ) {
	return;
}
?>
<section class="comments" id="comments">
	<?php if ( have_comments() ) : ?>
		<h3 class="comments__title">
			<?php
			$count = get_comments_number();
			printf( esc_html( _n( '%s Comment', '%s Comments', $count, 'aeon' ) ), number_format_i18n( $count ) );
			?>
		</h3>
		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'=> 48,
			) );
			?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php
	comment_form( array(
		'class_submit' => 'btn btn--primary',
		'title_reply'  => aeon_is_rtl() ? 'اترك تعليقاً' : 'Leave a Comment',
	) );
	?>
</section>
