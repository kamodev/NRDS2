<?php
/**
 * Template Name: Full Width (no sidebars)
 * Template Post Type: page
 *
 * Content without a title or sidebars, for pages built from full-width blocks.
 *
 * @package NRDS
 */

get_header();
?>
<main id="main" class="site-main nrds-canvas">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
