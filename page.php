<?php
/**
 * Page.
 *
 * @package NRDS
 */

get_header();
?>
<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>

			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'nrds-theme' ) . '">' . esc_html__( 'Pages:', 'nrds-theme' ),
						'after'  => '</nav>',
					)
				);
				?>
			</div>

			<?php edit_post_link( __( 'Edit this page', 'nrds-theme' ), '<p class="edit-link">', '</p>' ); ?>
		</article>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>
</main>
<?php
get_footer();
