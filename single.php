<?php
/**
 * Single post.
 *
 * The header shows the post's featured image, or the theme's custom header
 * image, behind the title unless post header images are turned off in
 * Appearance → Theme Settings → Layout.
 *
 * @package NRDS
 */

get_header();
?>
<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();

		$nrds_header_image = '';
		if ( 'show' === nrds_setting( 'post_header_image' ) ) {
			$nrds_header_image = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'full' ) : get_header_image();
		}
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( $nrds_header_image ) : ?>
				<header class="post-header-image" style="background-image: url('<?php echo esc_url( $nrds_header_image ); ?>');">
					<div class="post-header-overlay">
						<h1 class="post-title entry-title"><?php the_title(); ?></h1>
						<div class="post-meta"><?php nrds_posted_on(); ?></div>
					</div>
				</header>
			<?php else : ?>
				<header class="entry-header">
					<h1 class="post-title entry-title"><?php the_title(); ?></h1>
					<div class="post-meta entry-meta"><?php nrds_posted_on(); ?></div>
				</header>
			<?php endif; ?>

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

			<footer class="entry-footer">
				<?php the_tags( '<p class="entry-tags">' . esc_html__( 'Tags: ', 'nrds-theme' ), ', ', '</p>' ); ?>
				<?php edit_post_link( __( 'Edit this post', 'nrds-theme' ), '<p class="edit-link">', '</p>' ); ?>
			</footer>
		</article>

		<?php
		the_post_navigation(
			array(
				'prev_text' => '&larr; %title',
				'next_text' => '%title &rarr;',
			)
		);

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>
</main>
<?php
get_footer();
