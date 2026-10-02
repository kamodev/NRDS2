<?php
/**
 * Post summary in lists (blog, archives, search results).
 *
 * @package NRDS
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'nrds-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="nrds-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'nrds-card', array( 'alt' => '' ) ); ?>
		</a>
	<?php endif; ?>
	<div class="nrds-card__body">
		<h2 class="nrds-card__title entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="nrds-card__meta entry-meta"><?php nrds_posted_on(); ?></div>
		<?php endif; ?>
		<div class="nrds-card__excerpt"><?php the_excerpt(); ?></div>
		<a class="nrds-card__more" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read more', 'nrds-theme' ); ?> &rarr;<span class="screen-reader-text"> <?php the_title(); ?></span>
		</a>
	</div>
</article>
