<?php
/**
 * Search form.
 *
 * @package NRDS
 */

$nrds_search_id = wp_unique_id( 'nrds-search-' );
?>
<form role="search" method="get" class="search-form nrds-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $nrds_search_id ); ?>"><?php esc_html_e( 'Search for:', 'nrds-theme' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $nrds_search_id ); ?>" class="search-field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search…', 'nrds-theme' ); ?>">
	<button type="submit" class="search-submit"><?php esc_html_e( 'Search', 'nrds-theme' ); ?></button>
</form>
