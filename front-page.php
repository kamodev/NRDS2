<?php
/**
 * Front page.
 *
 * A static front page shows its content without a title, so it can start
 * with a full-width hero built in the editor. When the front page shows the
 * latest posts, the blog template is used instead.
 *
 * @package NRDS
 */

if ( is_home() ) {
	get_template_part( 'index' );
	return;
}

get_header();
?>
<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'nrds-front' ); ?>>
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
