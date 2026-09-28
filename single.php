<?php
/**
 * The template for displaying single posts
 *
 * This template displays a header image that prefers the post featured image (post thumbnail)
 * and falls back to the theme's custom header image (if set). It also outputs the title,
 * post meta, content and the comments template.
 *
 * @package nrds
 */

get_header();
?>

<main id="main" class="site-main">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            // Check theme option: optionally disable post header images
            $options = get_option( 'nrds_theme_settings_options', array() );
            $disable_header = ! empty( $options['disable_post_header_images'] );

            // Determine header image only when not disabled in settings
            $header_image = '';
            if ( ! $disable_header ) {
                if ( has_post_thumbnail() ) {
                    $header_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                } else {
                    // get_custom_header()->url is available when custom-header support is enabled
                    $custom_header = get_header_image();
                    if ( $custom_header ) {
                        $header_image = esc_url( $custom_header );
                    }
                }
            }
            ?>

            <?php if ( $header_image ) : ?>
                <div class="post-header-image" style="background-image: url('<?php echo esc_url( $header_image ); ?>');">
                    <div class="post-header-overlay">
                        <h1 class="post-title"><?php the_title(); ?></h1>
                        <div class="post-meta">Posted on <?php echo get_the_date(); ?> by <?php the_author_posts_link(); ?></div>
                    </div>
                </div>
            <?php else : ?>
                <header class="entry-header">
                    <h1 class="post-title"><?php the_title(); ?></h1>
                    <div class="post-meta">Posted on <?php echo get_the_date(); ?> by <?php the_author_posts_link(); ?></div>
                </header>
            <?php endif; ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>

            <?php
            // If comments are open or we have comments, load the comment template
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;

        endwhile;
    else :
        echo '<p>No content found</p>';
    endif;
    ?>
</main>

<?php get_footer(); ?>
