<?php
/**
 * Main template: the blog, archives and search results.
 *
 * @package NRDS
 */

get_header();

$nrds_heading = nrds_archive_heading();
?>
<main id="main" class="site-main">
	<?php nrds_page_header( $nrds_heading['title'], $nrds_heading['description'] ); ?>

	<?php if ( have_posts() ) : ?>
		<div class="nrds-posts">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
			endwhile;
			?>
		</div>
		<?php nrds_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</main>
<?php
get_footer();
