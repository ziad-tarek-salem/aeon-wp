<?php
/**
 * Branch pill row — repeated at the foot of several company-profile pages
 * (p3, p5, p6). Reads the dashboard-managed `aeon_branch` terms and falls back
 * to the branch list the client supplied.
 *
 * Args:
 *   note      string  Optional i18n key rendered as a bold note beside the pills.
 *   note_icon string  Icon key for that note (default 'target').
 *
 * @package AEON
 */

$branches = aeon_branch_locations();
if ( empty( $branches ) ) {
	$branches = array_map(
		static function ( $b ) {
			return array( 'name' => $b['name'], 'maps_url' => '', 'icon' => 'pin', 'icon_url' => '' );
		},
		aeon_default_branches()
	);
}

if ( empty( $branches ) ) {
	return;
}

$note      = isset( $args['note'] ) ? $args['note'] : '';
$note_icon = isset( $args['note_icon'] ) ? $args['note_icon'] : 'target';
?>
<div class="branch-chips">
	<?php if ( $note ) : ?>
		<span class="branch-chips__note">
			<?php echo aeon_icon( $note_icon ); ?>
			<span><?php aeon_e( $note ); ?></span>
		</span>
	<?php endif; ?>

	<?php foreach ( $branches as $branch ) : ?>
		<?php if ( ! empty( $branch['maps_url'] ) ) : ?>
			<a class="branch-chip" href="<?php echo esc_url( $branch['maps_url'] ); ?>" target="_blank" rel="noopener noreferrer">
				<?php echo aeon_icon( 'pin' ); ?>
				<span><?php echo esc_html( $branch['name'] ); ?></span>
			</a>
		<?php else : ?>
			<span class="branch-chip">
				<?php echo aeon_icon( 'pin' ); ?>
				<span><?php echo esc_html( $branch['name'] ); ?></span>
			</span>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
