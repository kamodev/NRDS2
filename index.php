<?php get_header(); ?>

<main id="main">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post(); ?>
            <article>
                <h2><?php the_title(); ?></h2>
                <div><?php the_content(); ?></div>
            </article>
        <?php endwhile;
    else : ?>
        <p>No content found</p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
