<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    
    <!-- Meta information -->
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="site-container">
        <header id="site-header" role="banner">
            <div class="site-branding">
                <?php if ( has_custom_logo() ): ?>
                    <?php the_custom_logo(); ?>
                <?php else: ?>
                    <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
                <?php endif; ?>            
            </div>
            <div>
                <?php require_once('inc/primary-menu.php'); ?>
            </div>
        </header>
        <div id="site-content">
            <?php if ( is_active_sidebar( 'left-sidebar' ) ) : ?>
                <aside id="left-sidebar" class="sidebar">
                    <?php dynamic_sidebar( 'left-sidebar' ); ?>
                </aside>
            <?php endif; ?>
