<?php
/**
 * 404 (page not found).
 *
 * @package NRDS
 */

get_header();
?>
<main id="main" class="site-main nrds-404">
	<?php nrds_page_header( __( 'Page not found', 'nrds-theme' ), __( 'The page you were looking for could not be found. It may have moved, or the link may be wrong.', 'nrds-theme' ) ); ?>

	<div class="entry-content">
		<p><?php esc_html_e( 'Try a search, or head back to the home page.', 'nrds-theme' ); ?></p>
		<?php get_search_form(); ?>
		<p class="nrds-404__actions">
			<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'nrds-theme' ); ?></a>
		</p>
	</div>
</main>
<?php
get_footer();
