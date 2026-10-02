<?php
/**
 * No posts found.
 *
 * @package NRDS
 */

?>
<div class="nrds-callout">
	<h2><?php esc_html_e( 'Nothing found', 'nrds-theme' ); ?></h2>
	<?php if ( is_search() ) : ?>
		<p><?php esc_html_e( 'No matches for that search. Try different keywords.', 'nrds-theme' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'There is nothing here yet. Check back soon.', 'nrds-theme' ); ?></p>
	<?php endif; ?>
</div>
