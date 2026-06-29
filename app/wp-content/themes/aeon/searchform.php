<?php
/**
 * Search form.
 *
 * @package AEON
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="s"><?php esc_html_e( 'Search', 'aeon' ); ?></label>
	<input type="search" id="s" name="s" placeholder="<?php echo esc_attr( aeon_is_rtl() ? 'ابحث...' : 'Search...' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
	<button type="submit" aria-label="Search"><?php echo aeon_icon( 'arrow' ); ?></button>
</form>
